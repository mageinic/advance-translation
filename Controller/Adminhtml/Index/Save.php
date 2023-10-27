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

use Exception;
use MageINIC\Translation\Api\TranslationRepositoryInterface;
use MageINIC\Translation\Model\TranslationFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use RuntimeException;

/**
 * MageINIC_Translation Class Save
 */
class Save extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var TranslationFactory
     */
    private TranslationFactory $translation;

    /**
     * @var TranslationRepositoryInterface
     */
    private TranslationRepositoryInterface $translationRepositoryInterface;

    /**
     * Save constructor.
     *
     * @param Action\Context $context
     * @param Session $session
     * @param TranslationFactory $translation
     * @param TranslationRepositoryInterface $translationRepositoryInterface
     */
    public function __construct(
        Action\Context                 $context,
        Session                        $session,
        TranslationFactory             $translation,
        TranslationRepositoryInterface $translationRepositoryInterface
    ) {
        $this->translation = $translation;
        $this->session = $session;
        $this->translationRepositoryInterface = $translationRepositoryInterface;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute(): Redirect|ResultInterface|ResponseInterface
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->translation->create();
            $id = $this->getRequest()->getParam('key_id');
            if ($id) {
                $model->load($id);
                $model->setCreatedAt(date('Y-m-d H:i:s'));
            }
            $data['locale'] = $this->getRequest()->getParam('localeui');
            $model->setData($data);
            $model->addData($data);

            try {
                $this->translationRepositoryInterface->save($model);
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/', ['key_id' => $model->getId(), '_current' => true]);
                }
                $this->_redirect('*/*/');
            } catch (LocalizedException|RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Translation.')
                );
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/', ['key_id' => $this->getRequest()->getParam('key_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Is Allowed
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('MageINIC_Translation::save');
    }
}
