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

class yncaffiliate_requests extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncaffiliate_requests';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'request_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'request_points' => [
                'type' => 'decimal',
                'type_value' => '14,2',
                'other' => 'NOT NULL DEFAULT \'0.00\'',
            ],
            'request_amount' => [
                'type' => 'decimal',
                'type_value' => '14,2',
                'other' => 'NOT NULL DEFAULT \'0.00\'',
            ],
            'request_status' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'NOT NULL DEFAULT \'waiting\'',
            ],
            'request_reason' => [
                'type' => 'text',
                'other' => 'DEFAULT NULL',
            ],
            'request_response' => [
                'type' => 'text',
                'other' => 'DEFAULT NULL',
            ],
            'request_currency' => [
                'type' => 'char',
                'type_value' => 5,
                'other' => 'NOT NULL DEFAULT \'USD\'',
            ],
            'modify_time' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
        ];
    }
}