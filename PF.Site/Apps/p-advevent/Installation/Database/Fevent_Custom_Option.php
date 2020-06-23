<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Custom_Option
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Custom_Option extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_custom_option';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'option_id' =>
                array(
                    'type' => 'smallint',
                    'type_value' => '4',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'field_id' =>
                array(
                    'type' => 'smallint',
                    'type_value' => '4',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'phrase_var_name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '250',
                    'other' => 'NOT NULL',
                ),
        );
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = array(
            'field_id' =>
                array(
                    0 => 'field_id',
                ),
        );
    }
}