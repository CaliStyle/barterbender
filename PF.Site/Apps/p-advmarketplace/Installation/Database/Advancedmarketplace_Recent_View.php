<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Recent_View
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Recent_View extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_recent_view';
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
                    'type_value' => '11',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                    'primary_key' => true,
                ),
            'listing_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                    'primary_key' => true,
                ),
            'timestamp' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'DEFAULT NULL',
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