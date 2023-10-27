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

namespace MageINIC\Translation\Model\ResourceModel\Translation\CSV;

use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnNotFoundException;

/**
 * MageINIC_Translation Class ColumnResolver
 */
class ColumnResolver
{
    public const COLUMN_STRING  = 'string';
    public const COLUMN_STORE   = 'store_id';
    public const COLUMN_TRANSLATE = 'translate';
    public const COLUMN_LOCALE      = 'locale';

    /**
     * @var array
     */
    private array $nameToPositionIdMap = [
        self::COLUMN_STRING => 1,
        self::COLUMN_TRANSLATE  => 2,
        self::COLUMN_STORE       => 3,
        self::COLUMN_LOCALE     => 4,
    ];

    /**
     * @var array
     */
    private array $headers;

    /**
     * ColumnResolver constructor.
     *
     * @param array $headers
     * @param array $columns
     */
    public function __construct(
        array $headers,
        array $columns = []
    ) {
        $this->nameToPositionIdMap = array_merge($this->nameToPositionIdMap, $columns);
        $this->headers = array_map('trim', $headers);
    }

    /**
     * Get ColumnValue
     *
     * @param string $column
     * @param array $values
     * @return string
     * @throws ColumnNotFoundException
     */
    public function getColumnValue(string $column, array $values): string
    {
        $column = (string) $column;
        $columnIndex = array_search($column, $this->headers, true);
        if (false === $columnIndex) {
            if (array_key_exists($column, $this->nameToPositionIdMap)) {
                $columnIndex = $this->nameToPositionIdMap[$column];
            } else {
                throw new ColumnNotFoundException(__('Requested column "%1" cannot be resolved', $column));
            }
        }
        if (!array_key_exists($columnIndex, $values)) {
            throw new ColumnNotFoundException(__('Column "%1" not found', $column));
        }
        return  trim($values[$columnIndex]);
    }
}
