<?php
namespace Apps\YNC_Feed\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;

class YnFeed_Extra_Info extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'ynfeed_extra_info';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'info_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true
            ],
            'module' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 75,
                Field::FIELD_PARAM_OTHER => 'NOT NULL DEFAULT \'\''
            ],
            'table_prefix' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 75,
                Field::FIELD_PARAM_OTHER => 'NOT NULL DEFAULT \'\''
            ],
            'item_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL'
            ],
            'type_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 75,
                Field::FIELD_PARAM_OTHER => 'NOT NULL'
            ],
            'location_latlng' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 100,
                Field::FIELD_PARAM_OTHER => 'NULL'
            ],
            'location_name' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
                Field::FIELD_PARAM_OTHER => 'NULL'
            ],
            'business_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'feeling_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'params' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_TEXT,
                Field::FIELD_PARAM_OTHER => 'NULL'
            ],
        ];
    }
}