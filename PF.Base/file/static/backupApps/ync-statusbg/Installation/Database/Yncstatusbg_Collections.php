<?php
namespace Apps\YNC_StatusBg\Installation\Database;


use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Yncstatusbg_Collections
 * @package Apps\YNC_StatusBg\Installation\Database
 */
class Yncstatusbg_Collections extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncstatusbg_collections';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'collection_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL'
            ],
            'is_active' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'1\''
            ],
            'is_default' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'is_deleted' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'main_image_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'view_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'total_background' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'NOT NULL DEFAULT \'0\''
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'id' => ['collection_id', 'is_active'],
            'id_1' => ['collection_id', 'is_active', 'is_default'],
            'id_2' => ['collection_id', 'is_deleted'],
        ];
    }
}