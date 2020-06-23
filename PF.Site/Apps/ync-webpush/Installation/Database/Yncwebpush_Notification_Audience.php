<?php
namespace Apps\YNC_WebPush\Installation\Database;


use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Yncwebpush_Notification_Audience
 * @package Apps\YNC_WebPush\Installation\Database
 */
class Yncwebpush_Notification_Audience extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncwebpush_notification_audience';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'notification_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'audience_type' => [
                'type' => 'enum',
                'other' => '(\'all\',\'subscriber\',\'browser\',\'group\') DEFAULT \'subscriber\'',
            ],
            'audience_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'audience_title' => [
                'type' => 'text',
                'other' => 'DEFAULT NULL'
            ]

        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'notification_id' => ['notification_id'],
            'notification_id_1' => ['notification_id', 'audience_id'],
        ];
    }
}