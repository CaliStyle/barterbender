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

class yncaffiliate_rulemaps extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncaffiliate_rulemaps';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'rulemap_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'rule_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'user_group_id' => [
                'type' => 'tinyint',
                'type_value' => 3,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'is_active' => [
                'type' => 'tinyint',
                'type_value' => 1,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'1\'',
            ],
        ];
    }
}