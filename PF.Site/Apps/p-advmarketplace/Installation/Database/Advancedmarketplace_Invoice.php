<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Invoice
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Invoice extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_invoice';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'invoice_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'listing_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'user_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'currency_id' =>
                array(
                    'type' => 'char',
                    'type_value' => '3',
                    'other' => 'NOT NULL',
                ),
            'price' =>
                array(
                    'type' => 'decimal',
                    'type_value' => '14,2',
                    'other' => 'NOT NULL',
                ),
            'status' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '20',
                    'other' => 'DEFAULT NULL',
                ),
            'time_stamp' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'time_stamp_paid' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
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