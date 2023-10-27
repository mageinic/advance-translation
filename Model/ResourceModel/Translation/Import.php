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

namespace MageINIC\Translation\Model\ResourceModel\Translation;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\ReadInterface;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowException;
use Magento\Store\Model\StoreManagerInterface;
use MageINIC\Translation\Model\ResourceModel\Translation\CSV\ColumnResolver;
use MageINIC\Translation\Model\ResourceModel\Translation\CSV\ColumnResolverFactory;
use MageINIC\Translation\Model\ResourceModel\Translation\CSV\RowParser;

/**
 * MageINIC_Translation Class Import
 */
class Import
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ScopeConfigInterface
     */
    private $coreConfig;

    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @var CSV\RowParser
     */
    private RowParser $rowParser;

    /**
     * @var ColumnResolverFactory
     */
    private ColumnResolverFactory $columnResolverFactory;

    /**
     * Import constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param ScopeConfigInterface $coreConfig
     * @param RowParser $rowParser
     * @param ColumnResolverFactory $columnResolverFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        ScopeConfigInterface $coreConfig,
        RowParser $rowParser,
        ColumnResolverFactory $columnResolverFactory
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->coreConfig = $coreConfig;
        $this->rowParser = $rowParser;
        $this->columnResolverFactory = $columnResolverFactory;
    }

    /**
     * Check if there are errors.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return (bool)count($this->getErrors());
    }

    /**
     * Get errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retrieve columns.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->rowParser->getColumns();
    }

    /**
     * Get data from file.
     *
     * @param ReadInterface $file
     * @param int $bunchSize
     * @return \Generator
     * @throws LocalizedException
     */
    public function getData(ReadInterface $file, int $bunchSize = 5000): \Generator
    {
        $this->errors = [];
        $headers = $this->getHeaders($file);
        /** @var ColumnResolver $columnResolver */
        $columnResolver = $this->columnResolverFactory->create(['headers' => $headers]);
        $rowNumber = 1;
        $items = [];
        while (false !== ($csvLine = $file->readCsv())) {
            try {
                $rowNumber++;
                if (empty($csvLine)) {
                    continue;
                }
                $rowsData = $this->rowParser->parse(
                    $csvLine,
                    $rowNumber,
                    $columnResolver
                );

                foreach ($rowsData as $rowData) {
                    $items[] = $rowData;
                }
                if (count($rowsData) > 1) {
                    $bunchSize += count($rowsData) - 1;
                }
                if (count($items) === $bunchSize) {
                    yield $items;
                    $items = [];
                }
            } catch (RowException $e) {
                $this->errors[] = $e->getMessage();
            }
        }
        if (count($items)) {
            yield $items;
        }
    }

    /**
     * Retrieve column headers.
     *
     * @param ReadInterface $file
     * @return array|bool
     * @throws LocalizedException
     */
    private function getHeaders(ReadInterface $file): bool|array
    {
        $headers = $file->readCsv();
        if ($headers === false || count($headers) < 4) {
            throw new LocalizedException(
                __('The Translation File Format is incorrect. Verify the format and try again.')
            );
        }
        return $headers;
    }
}
