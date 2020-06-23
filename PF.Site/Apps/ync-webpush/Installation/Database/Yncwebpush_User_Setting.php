<?php
namespace Apps\YNC_WebPush\Installation\Database;


use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Yncwebpush_User_Setting
 * @package Apps\YNC_WebPush\Installation\Database
 */
class Yncwebpush_User_Setting extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncwebpush_user_setting';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
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
        ];
    }
}