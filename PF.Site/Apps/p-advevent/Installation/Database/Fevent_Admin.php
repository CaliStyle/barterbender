<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Admin
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Admin extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_admin';
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
                ),
            'user_id' =>
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
        $this->_key = [];
    }
}