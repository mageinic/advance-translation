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

namespace MageINIC\Translation\Api\Data;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface TranslationInterface
 */
interface TranslationInterface
{
    public const COLUMN_ID = 'key_id';
    public const COLUMN_STRING = 'string';
    public const COLUMN_TRANSLATE = 'translate';
    public const COLUMN_STORE = 'store_id';
    public const COLUMN_LOCALE = 'locale';

    /**
     * Get ID
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getId(): mixed;

    /**
     * Get String
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getString(): mixed;

    /**
     * Get Translate
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getTranslate(): mixed;

    /**
     * Get StoreId
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getStoreId(): mixed;

    /**
     * Get Locale
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getLocale(): mixed;

    /**
     * Set Key
     *
     * @param int $key_id
     * @return TranslationInterface
     */
    public function setId(int $key_id): TranslationInterface;

    /**
     * Set String
     *
     * @param mixed $string
     * @return TranslationInterface
     */
    public function setString(mixed $string): TranslationInterface;

    /**
     * Set Translate
     *
     * @param mixed $translate
     * @return TranslationInterface
     */
    public function setTranslate(mixed $translate): TranslationInterface;

    /**
     * Set Store I'd
     *
     * @param string $store_id
     * @return TranslationInterface
     */
    public function setStoreId(string $store_id): TranslationInterface;

    /**
     * Set Locale
     *
     * @param mixed $locale
     * @return TranslationInterface
     */
    public function setLocale(mixed $locale): TranslationInterface;
}