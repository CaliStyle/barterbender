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

class yncaffiliate_suggests extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncaffiliate_suggests';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'suggest_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'is_active' => [
                'type' => 'tinyint',
                'type_value' => 1,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'1\'',
            ],
            'module_id' => [
                'type' => 'varchar',
                'type_value' => 100,
                'other' => 'NOT NULL',
            ],
            'suggest_title' => [
                'type' => 'varchar',
                'type_value' => 255,
                'other' => 'NOT NULL',
            ],
            'href' => [
                'type' => 'text',
                'other' => 'DEFAULT NULL',
            ],
        ];
    }
}