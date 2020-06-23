<?php
namespace Apps\YNC_Feed\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;

class YnFeed_Feeling extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'ynfeed_feeling';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'feeling_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true
            ],
            'title' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 75,
                Field::FIELD_PARAM_OTHER => 'NOT NULL'
            ],
            'code' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 75,
                Field::FIELD_PARAM_OTHER => 'NOT NULL'
            ],
            'image' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 128
            ],
            'ordering' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_SMALLINT,
                Field::FIELD_PARAM_TYPE_VALUE => 6,
                Field::FIELD_PARAM_OTHER => 'NOT NULL DEFAULT \'0\''
            ]
        ];
    }
}