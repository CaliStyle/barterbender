<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Category
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Category extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_category';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'category_id' =>
                array(
                    'type' => 'mediumint',
                    'type_value' => '8',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'parent_id' =>
                array(
                    'type' => 'mediumint',
                    'type_value' => '8',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'is_active' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'NOT NULL',
                ),
            'name_url' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'time_stamp' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'used' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'ordering' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
        );
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = array(
            'parent_id' =>
                array(
                    0 => 'parent_id',
                    1 => 'is_active',
                ),
            'is_active' =>
                array(
                    0 => 'is_active',
                    1 => 'name_url',
                ),
        );
    }
}