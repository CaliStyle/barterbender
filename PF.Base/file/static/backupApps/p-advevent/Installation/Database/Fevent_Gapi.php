<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Gapi
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Gapi extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_gapi';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'id' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'oauth2_client_id' =>
                array(
                    'type' => 'text',
                    'other' => 'NOT NULL',
                ),
            'oauth2_client_secret' =>
                array(
                    'type' => 'text',
                    'other' => 'NOT NULL',
                ),
            'developer_key' =>
                array(
                    'type' => 'text',
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