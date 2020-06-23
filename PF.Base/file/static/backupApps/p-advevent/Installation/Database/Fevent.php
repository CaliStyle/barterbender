<?php

namespace Apps\P_AdvEvent\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Fevent
 * @package Apps\P_AdvEvent\Installation\Database
 */
class Fevent extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'fevent';
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
                    'auto_increment' => true,
                ),
            'org_event_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL',
                ),
            'view_id' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'is_featured' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'is_sponsor' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'privacy' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'privacy_comment' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'module_id' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '75',
                    'other' => 'NOT NULL DEFAULT \'event\'',
                ),
            'item_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'user_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'title' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'NOT NULL',
                ),
            'location' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'country_iso' =>
                array(
                    'type' => 'char',
                    'type_value' => '2',
                    'other' => 'DEFAULT NULL',
                ),
            'country_child_id' =>
                array(
                    'type' => 'mediumint',
                    'type_value' => '8',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'postal_code' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '20',
                    'other' => 'DEFAULT NULL',
                ),
            'city' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'time_stamp' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'start_time' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'org_start_time' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL',
                ),
            'end_time' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'org_end_time' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL',
                ),
            'image_path' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '75',
                    'other' => 'DEFAULT NULL',
                ),
            'server_id' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
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
            'total_view' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'NOT NULL',
                ),
            'total_attachment' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'NOT NULL',
                ),
            'mass_email' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'start_gmt_offset' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '15',
                    'other' => 'DEFAULT NULL',
                ),
            'end_gmt_offset' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '15',
                    'other' => 'DEFAULT NULL',
                ),
            'gmap' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'address' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'lat' =>
                array(
                    'type' => 'double',
                    'other' => 'NOT NULL',
                ),
            'lng' =>
                array(
                    'type' => 'double',
                    'other' => 'NOT NULL',
                ),
            'gmap_address' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'isrepeat' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'DEFAULT \'-1\'',
                ),
            'timerepeat' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'DEFAULT NULL',
                ),
            'after_number_event' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL',
                ),
            'range_value' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'range_type' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'range_value_real' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'duration_days' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL',
                ),
            'duration_hours' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL',
                ),
            'is_update_warning' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'has_notification' =>
                array (
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'notification_type' =>
                array (
                    'type' => 'varchar',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL'
                ),
            'notification_value' =>
                array (
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL'
                ),
            'notification_time' =>
                array (
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL'
                ),
            'is_notified' =>
                array (
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'has_ticket' =>
                array (
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'ticket_type' =>
                array (
                    'type' => 'varchar',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL'
                ),
            'ticket_price' =>
                array (
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL'
                ),
            'ticket_url' =>
                array (
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL'
                ),
        );
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = array(
            'module_id' =>
                array(
                    0 => 'module_id',
                    1 => 'item_id',
                ),
            'user_id' =>
                array(
                    0 => 'user_id',
                ),
            'view_id' =>
                array(
                    0 => 'view_id',
                    1 => 'privacy',
                    2 => 'item_id',
                    3 => 'start_time',
                ),
            'view_id_2' =>
                array(
                    0 => 'view_id',
                    1 => 'privacy',
                    2 => 'item_id',
                    3 => 'user_id',
                    4 => 'start_time',
                ),
            'view_id_3' =>
                array(
                    0 => 'view_id',
                    1 => 'privacy',
                    2 => 'user_id',
                ),
            'view_id_4' =>
                array(
                    0 => 'view_id',
                    1 => 'privacy',
                    2 => 'item_id',
                    3 => 'title',
                ),
            'view_id_5' =>
                array(
                    0 => 'view_id',
                    1 => 'privacy',
                    2 => 'module_id',
                    3 => 'item_id',
                    4 => 'start_time',
                ),
        );
    }
}