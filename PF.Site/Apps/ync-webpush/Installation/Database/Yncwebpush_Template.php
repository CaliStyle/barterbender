<?php
namespace Apps\YNC_WebPush\Installation\Database;


use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Yncwebpush_Notification
 * @package Apps\YNC_WebPush\Installation\Database
 */
class Yncwebpush_Template extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncwebpush_template';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'template_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'template_name' => [
                'type' => 'varchar',
                'type_value' => '225',
                'other' => 'NOT NULL'
            ],
            'title' => [
                'type' => 'varchar',
                'type_value' => '225',
                'other' => 'NOT NULL'
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
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'used' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'template_id' => ['template_id'],
        ];
    }
}