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

namespace MageINIC\Translation\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Widget\Button;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

/**
 * MageINIC_Translation Class Export
 */
class Export extends AbstractElement
{
    /**
     * @var UrlInterface
     */
    protected UrlInterface $_backendUrl;

    /**
     * Export Constructor
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param UrlInterface $backendUrl
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        UrlInterface $backendUrl,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->_backendUrl = $backendUrl;
    }

    /**
     * Get Element Html
     *
     * @return string
     */
    public function getElementHtml(): string
    {
        /** @var Button $buttonBlock */
        $buttonBlock = $this->getForm()->getParent()->getLayout()->createBlock(
            Button::class
        );
        $params = ['website' => $buttonBlock->getRequest()->getParam('website')];
        $url = $this->_backendUrl->getUrl("translation/index/exporttransaltion", $params);
        $data = [
            'label'   => __('Export CSV'),
            'onclick' => "setLocation('" . $url . "export/' + '/translation.csv' )",
            'class'   => '',
        ];
        return $buttonBlock->setData($data)->toHtml();
    }
}
