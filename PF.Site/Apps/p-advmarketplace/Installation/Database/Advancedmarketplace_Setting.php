<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Setting
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Setting extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_setting';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'var_name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'NOT NULL',
                ),
            'value' =>
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
        $this->_key = array();
    }
}