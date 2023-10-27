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

namespace MageINIC\Translation\Block\Adminhtml\Translation\Edit\Tab;

use MageINIC\Translation\Helper\Data;
use MageINIC\Translation\Model\TranslationFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Page;
use Magento\Config\Model\Config\Source\Locale;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store;

/**
 * MageINIC_Translation class main
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var Store
     */
    protected Store $_systemStore;

    /**
     * @var Locale
     */
    protected Locale $locale;

    /**
     * @var TranslationFactory
     */
    protected TranslationFactory $_translation;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * Main constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param Locale $locale
     * @param TranslationFactory $_translation
     * @param RequestInterface $request
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Locale $locale,
        TranslationFactory $_translation,
        RequestInterface $request,
        Data $helper,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->locale = $locale;
        $this->_translation = $_translation;
        $this->request = $request;
        $this->helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare Form
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareForm(): void
    {
        /* @var $model Page */
        $model = $this->_translation->create()->load($this->request->getParam('key_id'));
        if ($this->_isAllowedAction('MageINIC_Translation::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('translation_main_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Translation Information')]);
        if ($model->getId()) {
            $fieldset->addField('key_id', 'hidden', ['name' => 'key_id']);
        }

        $fieldset->addField(
            'string',
            'textarea',
            [
                'name' => 'string',
                'label' => __('Original Text'),
                'title' => __('Original Text'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'translate',
            'textarea',
            [
                'name' => 'translate',
                'label' => __(' Translate Text'),
                'title' => __(' Translate Text'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'store_id',
            'select',
            [
                'name' => 'store_id',
                'label' => __(' Store View'),
                'title' => __(' Store View'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'values' => $this->helper->getStore()
            ]
        );

        $fieldset->addField(
            'localeui',
            'select',
            [
                'name' => 'localeui',
                'label' => __(' Locale'),
                'title' => __(' Locale'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'values' => $this->locale->toOptionArray()
            ]
        );

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }
        $model->setData('localeui', $model->getData('locale'));
        $this->setForm($form);
        $form->setValues($model->getData());
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel(): string
    {
        return __('Translation Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle(): string
    {
        return __('Translation Information');
    }

    /**
     * @inheritdoc
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden(): bool
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction(string $resourceId): bool
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
