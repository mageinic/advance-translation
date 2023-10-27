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

namespace MageINIC\Translation\Block\Adminhtml\Translation;

use Exception;
use MageINIC\Translation\Helper\Data;
use MageINIC\Translation\Model\ResourceModel\Translation\Collection;
use MageINIC\Translation\Model\ResourceModel\Translation\CollectionFactory;
use MageINIC\Translation\Model\Translation;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Config\Model\Config\Source\Locale;
use Magento\Framework\Exception\FileSystemException;
use Magento\Store\Model\System\Store;

/**
 * MageINIC_Translation Class Grid
 */
class Grid extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var Translation
     */
    protected Translation $translation;

    /**
     * @var Locale
     */
    protected Locale $locale;

    /**
     * @var Store
     */
    protected Store $systemStore;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * Grid constructor.
     *
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param Translation $translation
     * @param CollectionFactory $collectionFactory
     * @param Store $systemStore
     * @param Locale $locale
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context           $context,
        BackendHelper     $backendHelper,
        Translation       $translation,
        CollectionFactory $collectionFactory,
        Store             $systemStore,
        Locale            $locale,
        Data              $helper,
        array             $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->translation = $translation;
        $this->locale = $locale;
        $this->systemStore = $systemStore;
        $this->helper = $helper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Row click url
     *
     * @param Object $row
     * @return string
     */
    public function getRowUrl($row): string
    {
        return $this->getUrl('*/*/edit', ['key_id' => $row->getId()]);
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl(): string
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * Constructor
     *
     * @return void
     * @throws FileSystemException
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->setId('translationGrid');
        $this->setDefaultSort('key_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('grid_record');
    }

    /**
     * Prepare collection
     *
     * @return Grid
     */
    protected function _prepareCollection(): Grid
    {
        $collection = $this->collectionFactory->create();
        /* @var $collection Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Extended
     * @throws Exception
     */
    protected function _prepareColumns(): Extended
    {
        $this->addColumn(
            'key_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'key_id',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display'
            ]
        );

        $this->addColumn(
            'string',
            [
                'header' => __('Original Text'),
                'index' => 'string',
            ]
        );

        $this->addColumn(
            'translate',
            [
                'header' => __(' Translate Text'),
                'index' => 'translate',
            ]
        );

        $this->addColumn(
            'store_id',
            [
                'header' => __('Store View'),
                'index' => 'store_id',
                'type' => 'options',
                'options' => $this->helper->getStore(),
            ]
        );

        $this->addColumn(
            'locale',
            [
                'header' => __('Locale'),
                'index' => 'locale',
                'type' => 'options',
                'options' => $this->getLocaleForOptions()
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'key_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get Locale For Options
     *
     * @return array
     */
    private function getLocaleForOptions(): array
    {
        $locale = $this->locale->toOptionArray();

        $options = [];
        foreach ($locale as $key => $_locale) {
            $options[$_locale['value']] = $_locale['label'];
        }
        return $options;
    }

    /**
     * Prepare Mass Action
     *
     * @return Grid|$this
     */
    protected function _prepareMassaction(): Grid|static
    {
        $this->setMassactionIdField('key_id');
        $this->getMassactionBlock()->setFormFieldName('key_id');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/index/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

        return $this;
    }
}
