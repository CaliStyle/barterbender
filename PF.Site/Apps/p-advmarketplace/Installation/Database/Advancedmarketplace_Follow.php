<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Follow
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Follow extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_follow';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'user_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                ),
            'user_follow_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
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