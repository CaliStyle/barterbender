<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Custom_Field
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Custom_Field extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_custom_field';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'field_id' =>
                array(
                    'type' => 'smallint',
                    'type_value' => '4',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'field_name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '150',
                    'other' => 'NOT NULL',
                ),
            'category_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'NOT NULL',
                ),
            'phrase_var_name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '250',
                    'other' => 'NOT NULL',
                ),
            'type_name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '50',
                    'other' => 'NOT NULL',
                ),
            'var_type' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '20',
                    'other' => 'NOT NULL',
                ),
            'is_active' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'1\'',
                ),
            'is_required' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'ordering' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
        );
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [];
    }
}