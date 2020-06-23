<?php
namespace Apps\YNC_WebPush\Installation\Database;


use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Yncwebpush_User_Notification
 * @package Apps\YNC_WebPush\Installation\Database
 */
class Yncwebpush_User_Notification extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncwebpush_user_notification';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'user_notification' => [
                'type' => 'varchar',
                'type_value' => '225',
                'other' => 'NOT NULL'
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'NOT NULL'
            ],

        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'user_id' => ['user_id'],
            'user_id_1' => ['user_id', 'user_notification'],
        ];
    }
}