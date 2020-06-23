<?php

namespace Apps\Core_Photos\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Photo_Feed extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'photo_feed';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'feed_id'    => [
                'type'        => 'int',
                'type_value'  => '10',
                'other'       => 'UNSIGNED NOT NULL',
                'primary_key' => true,
            ],
            'photo_id'   => [
                'type'        => 'int',
                'type_value'  => '10',
                'other'       => 'UNSIGNED NOT NULL',
                'primary_key' => true,
            ],
            'feed_table' => [
                'type'       => 'varchar',
                'type_value' => '255',
                'other'      => 'NOT NULL DEFAULT \'feed\'',
            ],
        ];
    }

    protected function setKeys()
    {
        $this->_key = [
            'feed_id'  => ['feed_id'],
            'photo_id' => ['photo_id']
        ];
    }
}