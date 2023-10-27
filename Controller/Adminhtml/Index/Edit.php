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

use MageINIC\Translation\Model\Translation;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * MageINIC_Translation Class Edit
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * registry
     *
     * @var ?Registry
     */
    protected ?Registry $_coreRegistry = null;

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var Translation
     */
    private Translation $translation;

    /**
     * Edit Consecration
     *
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param Translation $translation
     * @param Session $session
     * @param Registry $registry
     */
    public function __construct(
        Action\Context $context,
        PageFactory    $resultPageFactory,
        Translation    $translation,
        Session        $session,
        Registry       $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->translation = $translation;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return Page|ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface|ResponseInterface|Page|Redirect
    {
        $id = $this->getRequest()->getParam('key_id');
        $model = $this->translation;
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This translation no longer exists.'));
                /** Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('translation', $model);
        /** @var Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Translation') : __('New Translation'),
            $id ? __('Edit Translation') : __('New Translation')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Translation'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Translation'));
        return $resultPage;
    }

    /**
     * Init actions
     *
     * @return Page|Edit
     */
    protected function _initAction(): Page|static
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'MageINIC_Translation::translation_manage'
        )->addBreadcrumb(
            __('Translation'),
            __('Translation')
        )->addBreadcrumb(
            __('Manage Translation'),
            __('Manage Translation')
        );
        return $resultPage;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('MageINIC_Translation::save');
    }
}
