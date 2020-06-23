<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Category_Customgroup_Data
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Category_Customgroup_Data extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_category_customgroup_data';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'category_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'NOT NULL',
                    'primary_key' => true,
                ),
            'group_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'NOT NULL',
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