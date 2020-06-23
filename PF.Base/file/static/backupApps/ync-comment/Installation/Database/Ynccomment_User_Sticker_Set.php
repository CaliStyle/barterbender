<?php
namespace Apps\YNC_Comment\Installation\Database;


use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ynccomment_User_Sticker_Set
 * @package Apps\YNC_Comment\Installation\Database
 */
class Ynccomment_User_Sticker_Set extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'ynccomment_user_sticker_set';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'set_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'user_id' => ['user_id', 'set_id'],
        ];
    }
}