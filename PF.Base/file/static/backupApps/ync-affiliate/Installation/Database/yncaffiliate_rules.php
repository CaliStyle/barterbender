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

class yncaffiliate_rules extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncaffiliate_rules';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'rule_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'module_id' => [
                'type' => 'varchar',
                'type_value' => 150,
                'other' => 'NOT NULL',
            ],
            'rule_title' => [
                'type' => 'varchar',
                'type_value' => 255,
                'other' => 'NOT NULL',
            ],
            'rule_name' => [
                'type' => 'varchar',
                'type_value' => 255,
                'other' => 'NOT NULL',
            ],
        ];
    }
}