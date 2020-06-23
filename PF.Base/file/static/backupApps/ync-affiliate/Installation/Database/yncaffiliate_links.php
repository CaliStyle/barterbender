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

class yncaffiliate_links extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncaffiliate_links';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'link_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true
            ],
            'link_title' => [
                'type' => 'varchar',
                'type_value' => 255,
                'other' => 'DEFAULT NULL',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'is_dynamic' => [
                'type' => 'tinyint',
                'type_value' => 1,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'target_url' => [
                'type' => Field::TYPE_MEDIUMTEXT,
                'other' => 'NOT NULL',
            ],
            'affiliate_url' => [
                'type' => Field::TYPE_MEDIUMTEXT,
                'other' => 'NOT NULL',
            ],
            'total_click' => [
                'type' => 'int',
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'total_success' => [
                'type' => 'int',
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL',
            ],
            'last_user_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'last_registered' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'last_click' => [
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