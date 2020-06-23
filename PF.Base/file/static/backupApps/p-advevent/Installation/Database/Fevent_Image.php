<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Image
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Image extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_image';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'image_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'event_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'image_path' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '50',
                    'other' => 'NOT NULL',
                ),
            'server_id' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL',
                ),
            'ordering' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '3',
                    'other' => 'NOT NULL DEFAULT \'0\'',
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