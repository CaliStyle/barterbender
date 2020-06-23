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

class yncaffiliate_rulemap_details extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncaffiliate_rulemap_details';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'rulemapdetail_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'rulemap_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'rule_level' => [
                'type' => 'tinyint',
                'type_value' => 3,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'rule_value' => [
                'type' => 'decimal',
                'type_value' => '10,2',
                'other' => 'UNSIGNED NOT NULL',
            ],
        ];
    }
}