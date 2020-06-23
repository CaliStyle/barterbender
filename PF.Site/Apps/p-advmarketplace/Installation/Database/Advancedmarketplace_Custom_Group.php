<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Custom_Group
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Custom_Group extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_custom_group';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'group_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'phrase_var_name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '250',
                    'other' => 'DEFAULT NULL',
                ),
            'is_active' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'DEFAULT \'1\'',
                ),
            'ordering' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '3',
                    'other' => 'DEFAULT NULL',
                ),
            'category_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
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