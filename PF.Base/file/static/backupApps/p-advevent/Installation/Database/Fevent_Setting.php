<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Setting
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Setting extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_setting';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'setting_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'NOT NULL',
                ),
            'default_value' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'NOT NULL',
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