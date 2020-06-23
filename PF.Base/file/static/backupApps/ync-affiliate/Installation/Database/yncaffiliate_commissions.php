<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/6/17
 * Time: 17:00
 */

namespace Apps\YNC_Affiliate\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;

class yncaffiliate_commissions extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncaffiliate_commissions';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'commission_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true
            ],
            'rule_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'rulemap_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'rulemapdetail_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'module_id' => [
                'type' => 'varchar',
                'type_value' => 128,
                'other' => 'NOT NULL',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'from_user_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'purchase_currency' => [
                'type' => 'char',
                'type_value' => 5,
                'other' => 'NOT NULL DEFAULT \'USD\'',
            ],
            'purchase_amount' => [
                'type' => 'decimal',
                'type_value' => '14,2',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0.00\'',
            ],
            'purchase_type' => [
                'type' => 'int',
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'commission_amount' => [
                'type' => 'decimal',
                'type_value' => '14,2',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0.00\'',
            ],
            'commission_rate' => [
                'type' => 'decimal',
                'type_value' => '10,2',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0.00\'',
            ],
            'commission_points' => [
                'type' => 'decimal',
                'type_value' => '14,2',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0.00\'',
            ],
            'transaction_id' => [
                'type' => 'int',
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'status' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'NOT NULL DEFAULT \'waiting\'',
            ],
            'time_update' => [
                'type' => 'int',
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'reason' => [
                'type' => 'text',
                'other' => 'DEFAULT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
            ],
        ];
    }
}