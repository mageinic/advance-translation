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

namespace MageINIC\Translation\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Config\Controller\Adminhtml\System\AbstractConfig;
use Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * MageINIC_Translation Class ExportTransaltion
 */
class ExportTransaltion extends AbstractConfig
{
    /**
     * @var FileFactory
     */
    protected FileFactory $_fileFactory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * @param Context $context
     * @param Structure $configStructure
     * @param ConfigSectionChecker $sectionChecker
     * @param FileFactory $fileFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Structure $configStructure,
        ConfigSectionChecker $sectionChecker,
        FileFactory $fileFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context, $configStructure, $sectionChecker);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     * @throws \Exception
     */
    public function execute(): ResultInterface|ResponseInterface
    {
        $fileName = 'translation.csv';
        /** @var $gridBlock \MageINIC\Translation\Block\Adminhtml\Translation\Grid */
        $gridBlock = $this->_view->getLayout()->createBlock(
            \MageINIC\Translation\Block\Adminhtml\Translation\Grid::class
        );
        $website = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'));
        $gridBlock->setWebsiteId($website->getId());
        $content = $gridBlock->getCsvFile();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
