<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Custom_Field_Data
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Custom_Field_Data extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_custom_field_data';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'custom_field_data_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'data' =>
                array(
                    'type' => 'text',
                    'other' => 'NOT NULL',
                ),
            'custom_field_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'field_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'listing_id' =>
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