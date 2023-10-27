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

namespace MageINIC\Translation\Model;

use MageINIC\Translation\Api\Data\TranslationInterface;
use Magento\Framework\Model\AbstractModel;

/**
 *  MageINIC Translation Class
 */
class Translation extends AbstractModel implements TranslationInterface
{
    /**
     * @inheritdoc
     */
    public function getId(): mixed
    {
        return $this->getData(self::COLUMN_ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($key_id): TranslationInterface
    {
        return $this->setData(self::COLUMN_ID, $key_id);
    }

    /**
     * @inheritdoc
     */
    public function getString(): mixed
    {
        return $this->getData(self::COLUMN_STRING);
    }

    /**
     * @inheritdoc
     */
    public function setString(mixed $string): TranslationInterface
    {
        return $this->setData(self::COLUMN_STRING, $string);
    }

    /**
     * @inheritdoc
     */
    public function getTranslate(): mixed
    {
        return $this->getData(self::COLUMN_TRANSLATE);
    }

    /**
     * @inheritdoc
     */
    public function setTranslate(mixed $translate): TranslationInterface
    {
        return $this->setData(self::COLUMN_TRANSLATE, $translate);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId(): mixed
    {
        return $this->getData(self::COLUMN_STORE);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId(string $store_id): TranslationInterface
    {
        return $this->setData(self::COLUMN_STORE, $store_id);
    }

    /**
     * @inheritdoc
     */
    public function getLocale(): mixed
    {
        return $this->getData(self::COLUMN_LOCALE);
    }

    /**
     * @inheritdoc
     */
    public function setLocale(mixed $locale): TranslationInterface
    {
        return $this->setData(self::COLUMN_LOCALE, $locale);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Translation::class);
    }
}
