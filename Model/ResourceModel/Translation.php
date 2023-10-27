<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_Translation
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\Translation\Model\ResourceModel;

use MageINIC\Translation\Model\ResourceModel\Translation\Import;
use Magento\Config\Model\Config\Source\Locale;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\ReadInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;

/**
 * MageINIC_Translation Class Translation
 */
class Translation extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @var int
     */
    protected int $_importedRows = 0;

    /**
     * @var ScopeConfigInterface
     * @since 100.1.0
     */
    protected ScopeConfigInterface $coreConfig;

    /**
     * @var LoggerInterface
     * @since 100.1.0
     */
    protected LoggerInterface $logger;

    /**
     * @var StoreManagerInterface
     * @since 100.1.0
     */
    protected StoreManagerInterface $storeManager;

    /**
     * Filesystem instance
     *
     * @var Filesystem
     * @since 100.1.0
     */
    protected Filesystem $filesystem;

    /**
     * @var Import
     */
    private Import $import;

    /**
     * @var Locale
     */
    protected Locale $locale;

    /**
     * @var File
     */
    private File $file;

    /**
     * @var Request
     */
    private Request $request;

    /**
     * Translation Constructor
     *
     * @param Context $context
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $coreConfig
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param Import $import
     * @param Locale $locale
     * @param File $file
     * @param Request $request
     * @param $connectionName
     */
    public function __construct(
        Context               $context,
        LoggerInterface       $logger,
        ScopeConfigInterface  $coreConfig,
        StoreManagerInterface $storeManager,
        Filesystem            $filesystem,
        Import                $import,
        Locale                $locale,
        File                  $file,
        Request $request,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->coreConfig = $coreConfig;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->import = $import;
        $this->locale = $locale;
        $this->file = $file;
        $this->request = $request;
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('translation', 'key_id');
    }

    /**
     * Import Data
     *
     * @param array $fields
     * @param array $values
     * @throws LocalizedException
     */
    private function importData(array $fields, array $values): void
    {
        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            if (count($fields) && count($values)) {
                $this->getConnection()->insertArray($this->getMainTable(), $fields, $values);
                $this->_importedRows += count($values);
            }
        } catch (LocalizedException $e) {
            $connection->rollBack();
            throw new LocalizedException(__('Unable to import data'), $e);
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->critical($e);
            throw new LocalizedException(
                __('Something went wrong while importing translation.')
            );
        }
        $connection->commit();
    }

    /**
     * Delete By Condition
     *
     * @return void
     * @throws LocalizedException
     */
    private function deleteByCondition(): void
    {
        $connection = $this->getConnection();
        $connection->beginTransaction();
        $connection->delete($this->getMainTable());
        $connection->commit();
    }

    /**
     * Upload Translation file and import data from it
     *
     * @param DataObject $object
     * @return Translation
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function uploadAndImport(DataObject $object): static
    {
        $files = $this->request->getFiles()->toArray();
        if (empty($files['groups']['view']['fields']['import']['value'])) {
            return $this;
        }
        $filePath = $files['groups']['view']['fields']['import']['value']['tmp_name'];
        $file = $this->getCsvFile($filePath);
        try {
            $this->deleteByCondition();
            $columns = $this->import->getColumns();
            foreach ($this->import->getData($file) as $bunch) {
                foreach ($bunch as $values) {
                    $getLocale = $this->getLocale($values['locale']);
                    $getStore = $this->getStoreId($values['store_id']);
                    $all_data[] = [
                        'string' => $values['string'],
                        'store_id' => $getStore,
                        'translate' => $values['translate'],
                        'locale' => $getLocale
                    ];
                }
                $this->importData($columns, $all_data);
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            throw new LocalizedException(
                __($e->getMessage())
            );
        } finally {
            $file->close();
        }
        return $this;
    }

    /**
     * Get Locale
     *
     * @param mixed $locale_label
     * @return mixed
     */
    public function getLocale(mixed $locale_label): mixed
    {
        $locale = $this->locale->toOptionArray();
        $local_value = [];
        foreach ($locale as $value) {
            if ($locale_label == $value['label']) {
                $local_value = $value['value'];
            }
        }
        return $local_value;
    }

    /**
     * Get Store ID
     *
     * @param mixed $store_name
     * @return mixed
     */
    public function getStoreId(mixed $store_name): mixed
    {
        $storeManagerDataList = $this->storeManager->getStores();
        $store = [];
        foreach ($storeManagerDataList as $key => $value) {
            $store[] = ['label' => $value['name'], 'value' => $key];
        }
        $default_stores[] = ['label' => 'All Store Views', 'value' => '0'];
        $stores = array_merge($default_stores, $store);
        $store_value = [];
        foreach ($stores as $key => $_locale) {
            if ($store_name == $_locale['label']) {
                $store_value = $_locale['value'];
            }
        }
        return $store_value;
    }

    /**
     * Get Csv File
     *
     * @param string $filePath
     * @return ReadInterface
     * @throws FileSystemException
     */
    private function getCsvFile(string $filePath): ReadInterface
    {
        $pathInfo = $this->file->getPathInfo($filePath);
        $dirName = $pathInfo['dirname'] ?? '';
        $fileName = $pathInfo['basename'] ?? '';
        $directoryRead = $this->filesystem->getDirectoryReadByPath($dirName);
        return $directoryRead->openFile($fileName);
    }
}
