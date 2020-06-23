<?php
namespace Apps\YNC_WebPush\Installation\Database;


use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Yncwebpush_Notification
 * @package Apps\YNC_WebPush\Installation\Database
 */
class Yncwebpush_Notification extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncwebpush_notification';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'notification_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'template_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'title' => [
                'type' => 'varchar',
                'type_value' => '225',
                'other' => 'DEFAULT NULL'
            ],
            'message' => [
                'type' => 'varchar',
                'type_value' => '225',
                'other' => 'DEFAULT NULL'
            ],
            'icon_path' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL'
            ],
            'icon_server_id' => [
                'type' => 'tinyint',
                'type_value' => '3',
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'photo_path' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL'
            ],
            'photo_server_id' => [
                'type' => 'tinyint',
                'type_value' => '3',
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'redirect_url' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL'
            ],
            'schedule_time' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'status' => [
                'type' => 'enum',
                'other' => '(\'scheduled\',\'sent\',\'sending\') DEFAULT \'scheduled\''
            ],
            'audience_type' => [
                'type' => 'enum',
                'other' => '(\'all\',\'group\',\'browser\',\'subscriber\') DEFAULT \'all\''
            ],
            'cron_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED DEFAULT \'0\'',
            ],
            'total_send' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED DEFAULT \'0\'',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'notification_id' => ['notification_id'],
            'notification_id_1' => ['notification_id', 'status'],
            'notification_id_2' => ['notification_id', 'status', 'audience_type'],
            'schedule_time' => ['notification_id', 'schedule_time']
        ];
    }
}