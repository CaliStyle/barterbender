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

class yncaffiliate_materials extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncaffiliate_materials';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'material_id' => [
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
            'time_stamp' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'material_name' => [
                'type' => 'varchar',
                'type_value' => 255,
                'other' => 'NOT NULL',
            ],
            'material_width' => [
                'type' => 'int',
                'type_value' => 10,
                'other' => 'NOT NULL',
            ],
            'material_height' => [
                'type' => 'int',
                'type_value' => 10,
                'other' => 'NOT NULL',
            ],
            'link' => [
                'type' => Field::TYPE_MEDIUMTEXT,
                'other' => 'NOT NULL',
            ],
            'image_path' => [
                'type' => 'varchar',
                'type_value' => 255,
                'other' => 'DEFAULT NULL',
            ],
            'server_id' => [
                'type' => 'tinyint',
                'type_value' => 1,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'ordering' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
        ];
    }
}