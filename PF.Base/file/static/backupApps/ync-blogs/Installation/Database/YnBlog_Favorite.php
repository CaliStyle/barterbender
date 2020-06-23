<?php

namespace Apps\YNC_Blogs\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;


class YnBlog_Favorite extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'ynblog_favorite';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'favorite_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true
            ],
            'blog_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
            ],
            'user_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
            ],
            'time_stamp' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
            ]
        ];
    }
}
