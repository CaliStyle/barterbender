<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent_Invite
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent_Invite extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent_invite';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'invite_id' =>
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
            'type_id' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'rsvp_id' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'user_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'invited_user_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'invited_email' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '100',
                    'other' => 'DEFAULT NULL',
                ),
            'time_stamp' =>
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
        $this->_key = array(
            'event_id' =>
                array(
                    0 => 'event_id',
                ),
            'event_id_2' =>
                array(
                    0 => 'event_id',
                    1 => 'invited_user_id',
                ),
            'invited_user_id' =>
                array(
                    0 => 'invited_user_id',
                ),
            'event_id_3' =>
                array(
                    0 => 'event_id',
                    1 => 'rsvp_id',
                    2 => 'invited_user_id',
                ),
            'rsvp_id' =>
                array(
                    0 => 'rsvp_id',
                    1 => 'invited_user_id',
                ),
        );
    }
}