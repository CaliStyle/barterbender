<?php

namespace Apps\YNC_Blogs\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;

class YnBlog_Category extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'ynblog_category';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'category_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true
            ],
            'parent_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'name' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
                Field::FIELD_PARAM_OTHER => 'NOT NULL',
            ],
            'time_stamp' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
            ],
            'used' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
            ],
            'is_active' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 1,
            ],
            'ordering' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
            ]
        ];
    }
}
