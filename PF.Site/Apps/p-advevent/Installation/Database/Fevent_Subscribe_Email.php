<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;
use Core\App\Install\Database\Field as Field;

/**
 * Class Fevent_Subscribe_Email
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Subscribe_Email extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_subscribe_email';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'subscribe_id' => [
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ],
            'email' => [
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'NOT NULL',
                ],
            'data' => [
                    'type' => 'text',
                    'other' => 'NULL',
                ],
            'code' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_CHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 50,
                Field::FIELD_PARAM_OTHER => 'NOT NULL'
            ]
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