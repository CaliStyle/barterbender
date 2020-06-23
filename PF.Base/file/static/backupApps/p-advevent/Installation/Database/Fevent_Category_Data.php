<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Category_Data
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Category_Data extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_category_data';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'event_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                ),
            'category_id' =>
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