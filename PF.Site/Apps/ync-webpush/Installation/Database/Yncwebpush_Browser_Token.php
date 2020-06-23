<?php
namespace Apps\YNC_WebPush\Installation\Database;


use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Yncwebpush_User_Token
 * @package Apps\YNC_WebPush\Installation\Database
 */
class Yncwebpush_Browser_Token extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncwebpush_browser_token';
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
            'last_update' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'browser' => [
                'type' => 'enum',
                'other' => '(\'Chrome\',\'Firefox\') DEFAULT \'Chrome\''
            ],
            'skip_time' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'token' => ['token'],
            'token_1' => ['token', 'skip_time'],
            'last_update' => ['last_update', 'token'],
        ];
    }
}