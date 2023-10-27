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

namespace MageINIC\Translation\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * MageINIC_Translation Class Translation
 */
class Translation extends Container
{
    /**
     * Translation Constructor
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_controller = 'adminhtml_translation';
        $this->_blockGroup = 'MageINIC_Translation';
        $this->_headerText = __('Translation');
        $this->_addButtonLabel = __('Add New Translation');
        parent::_construct();
        if ($this->_isAllowedAction('MageINIC_Translation::save')) {
            $this->buttonList->update('add', 'label', __('Add New Translation'));
        } else {
            $this->buttonList->remove('add');
        }
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
