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

namespace  MageINIC\Translation\Model\ResourceModel\Translation\CSV;

use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnNotFoundException;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowException;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\LocationDirectory;

/**
 * MageINIC_Translation Class RowParser
 */
class RowParser
{
    /**
     * @var LocationDirectory
     */
    private $locationDirectory;

    /**
     * RowParser constructor.
     *
     * @param LocationDirectory $locationDirectory
     */
    public function __construct(
        LocationDirectory $locationDirectory
    ) {
        $this->locationDirectory = $locationDirectory;
    }

    /**
     * Retrieve columns.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            'string',
            'store_id',
            'translate',
            'locale'
        ];
    }

    /**
     * Parse
     *
     * @param array $rowData
     * @param int $rowNumber
     * @param ColumnResolver $columnResolver
     * @return array
     * @throws ColumnNotFoundException
     * @throws RowException
     */
    public function parse(array $rowData, int $rowNumber, ColumnResolver $columnResolver): array
    {
        if (count($rowData) < 4) {
            throw new RowException(
                __(
                    'The Translation  File Format is incorrect in row number "%1".
                     Verify the format and try again.',
                    $rowNumber
                )
            );
        }
        $string = $this->getString($rowData, $columnResolver);
        $storeId = $this->getStoreId($rowData, $columnResolver);
        $translate      = $this->getTranslate($rowData, $columnResolver);
        $locale    = $this->getLocale($rowData, $columnResolver);
        $rates[] = [
            'string' => $string,
            'store_id' => $storeId,
            'translate' => $translate,
            'locale' => $locale,
        ];

        return $rates;
    }

    /**
     * Get String
     *
     * @param array $rowData
     * @param ColumnResolver $columnResolver
     * @return string
     * @throws ColumnNotFoundException
     */
    private function getString(array $rowData, ColumnResolver $columnResolver): string
    {
        return $columnResolver->getColumnValue(ColumnResolver::COLUMN_STRING, $rowData);
    }

    /**
     * Get StoreID
     *
     * @param array $rowData
     * @param ColumnResolver $columnResolver
     * @return string
     * @throws ColumnNotFoundException
     */
    private function getStoreId(array $rowData, ColumnResolver $columnResolver): string
    {
        return $columnResolver->getColumnValue(ColumnResolver::COLUMN_STORE, $rowData);
    }

    /**
     * Get Translate
     *
     * @param array $rowData
     * @param ColumnResolver $columnResolver
     * @return string
     * @throws ColumnNotFoundException
     */
    private function getTranslate(array $rowData, ColumnResolver $columnResolver): string
    {
        return $columnResolver->getColumnValue(ColumnResolver::COLUMN_TRANSLATE, $rowData);
    }

    /**
     * Get Locale
     *
     * @param array $rowData
     * @param ColumnResolver $columnResolver
     * @return string
     * @throws ColumnNotFoundException
     */
    private function getLocale(array $rowData, ColumnResolver $columnResolver): string
    {
        return $columnResolver->getColumnValue(ColumnResolver::COLUMN_LOCALE, $rowData);
    }
}
