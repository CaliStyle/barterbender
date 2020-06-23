<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Custom_Field
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Custom_Field extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_custom_field';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'field_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'var_type' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '250',
                    'other' => 'DEFAULT NULL',
                ),
            'is_required' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL',
                ),
            'field_name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '75',
                    'other' => 'NOT NULL',
                ),
            'type_name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '75',
                    'other' => 'DEFAULT NULL',
                ),
            'ordering' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '3',
                    'other' => 'DEFAULT NULL',
                ),
            'phrase_var_name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '75',
                    'other' => 'DEFAULT NULL',
                ),
            'is_active' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'DEFAULT NULL',
                ),
            'group_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'NOT NULL',
                ),
            'field_info' =>
                array(
                    'type' => 'text',
                    'other' => 'NULL',
                ),
        );
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = array();
    }
}