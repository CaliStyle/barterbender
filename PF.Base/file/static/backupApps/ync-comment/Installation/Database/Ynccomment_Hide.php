<?php
namespace Apps\YNC_Comment\Installation\Database;


use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ynccomment_Hide
 * @package Apps\YNC_Comment\Installation\Database
 */
class Ynccomment_Hide extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'ynccomment_hide';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'hide_id' => [
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
            'comment_id' => [
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
            'user_id_1' => ['user_id', 'comment_id']
        ];
    }
}