<?php
namespace Apps\YNC_Member\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;

class YnMember_Custom_Field extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'ynmember_custom_field';
    }
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'field_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true
            ],
            'var_type' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            'is_required' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_BOOLEAN,
                Field::FIELD_PARAM_OTHER => 'NOT NULL DEFAULT \'0\'',
            ],
            'field_name' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
                Field::FIELD_PARAM_OTHER => 'NOT NULL DEFAULT \'\'',
            ],
            'type_name' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            'ordering' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_TINYINT,
                Field::FIELD_PARAM_TYPE_VALUE => 3,
            ],
            'phrase_var_name' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            'is_active' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_BOOLEAN,
                Field::FIELD_PARAM_OTHER => 'NOT NULL DEFAULT \'0\'',
            ],
            'group_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'NOT NULL',
            ],
            'field_info' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_TEXT,
            ],
        ];
    }
}