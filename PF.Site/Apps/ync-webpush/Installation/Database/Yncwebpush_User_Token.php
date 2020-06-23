<?php
namespace Apps\YNC_WebPush\Installation\Database;


use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Yncwebpush_User_Token
 * @package Apps\YNC_WebPush\Installation\Database
 */
class Yncwebpush_User_Token extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncwebpush_user_token';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'id' => [
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
            'token' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'browser' => [
                'type' => 'enum',
                'other' => '(\'Chrome\',\'Firefox\') DEFAULT \'Chrome\''
            ],
            'is_active' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'1\''
            ]

        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'user_id' => ['user_id'],
            'user_id_1' => ['user_id', 'token'],
            'token' => ['user_id', 'token', 'is_active'],
        ];
    }
}