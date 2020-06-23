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

class yncaffiliate_accounts extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncaffiliate_accounts';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'account_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'status' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'NOT NULL DEFAULT \'pending\'',
            ],
            'contact_email' => [
                'type' => 'varchar',
                'type_value' => 128,
                'other' => 'NOT NULL',
            ],
            'contact_name' => [
                'type' => 'varchar',
                'type_value' => 128,
                'other' => 'DEFAULT NULL',
            ],
            'contact_address' => [
                'type' => 'varchar',
                'type_value' => 255,
                'other' => 'DEFAULT NULL',
            ],
            'contact_phone' => [
                'type' => 'varchar',
                'type_value' => 128,
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