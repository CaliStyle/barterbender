<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Text
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Text extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_text';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'listing_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'description' =>
                array(
                    'type' => 'mediumtext',
                    'other' => 'NULL',
                ),
            'description_parsed' =>
                array(
                    'type' => 'mediumtext',
                    'other' => 'NULL',
                ),
            'short_description' =>
                array(
                    'type' => 'mediumtext',
                    'other' => 'NULL',
                ),
            'short_description_parsed' =>
                array(
                    'type' => 'mediumtext',
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