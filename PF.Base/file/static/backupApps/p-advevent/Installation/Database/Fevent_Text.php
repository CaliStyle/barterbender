<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Text
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Text extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_text';
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
        );
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = array(
            'event_id' =>
                array(
                    0 => 'event_id',
                ),
        );
    }
}