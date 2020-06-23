<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Feed_Comment
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Feed_Comment extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_feed_comment';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'feed_comment_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'user_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'parent_user_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'privacy' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '3',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'privacy_comment' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '3',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'content' =>
                array(
                    'type' => 'mediumtext',
                    'other' => 'NULL',
                ),
            'time_stamp' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'total_comment' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'total_like' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
        );
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = array(
            'parent_user_id' =>
                array(
                    0 => 'parent_user_id',
                ),
        );
    }
}