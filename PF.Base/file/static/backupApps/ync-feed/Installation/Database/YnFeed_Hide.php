<?php
namespace Apps\YNC_Feed\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;

class YnFeed_Hide extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'ynfeed_hide';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'hide_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true,
            ],
            'user_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
            ],
            'hide_resource_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
            ],
            'hide_resource_type' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 75,
                Field::FIELD_PARAM_OTHER => 'NOT NULL',
            ]
        ];
    }
}