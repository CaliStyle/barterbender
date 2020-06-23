<?php
namespace Apps\YNC_Feed\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;

class YnFeed_Turnoff_Notification extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'ynfeed_turnoff_notification';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'turnoff_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true,
            ],
            'item_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
            ],
            'type_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 75,
                Field::FIELD_PARAM_OTHER => 'NOT NULL'
            ],
            'user_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
            ],
        ];
    }
}