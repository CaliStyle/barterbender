<?php

namespace Apps\YNC_Blogs\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;

class YnBlog_Blogs extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'ynblog_blogs';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'blog_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true
            ],
            'user_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
            ],
            'title' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
                Field::FIELD_PARAM_OTHER => 'NOT NULL',
            ],
            'text' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_MEDIUMTEXT,
            ],
            'image_path' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            'server_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_TINYINT,
                Field::FIELD_PARAM_TYPE_VALUE => 1,
                Field::FIELD_PARAM_OTHER => 'NOT NULL DEFAULT \'0\'',
            ],
            'time_stamp' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
            ],
            'time_update' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
            ],
            'latest_comment' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL',
            ],
            'is_approved' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_TINYINT,
                Field::FIELD_PARAM_TYPE_VALUE => 1,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'is_featured' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_TINYINT,
                Field::FIELD_PARAM_TYPE_VALUE => 1,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'privacy' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_TINYINT,
                Field::FIELD_PARAM_TYPE_VALUE => 1,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'privacy_comment' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_TINYINT,
                Field::FIELD_PARAM_TYPE_VALUE => 1,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'post_status' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 32,
                Field::FIELD_PARAM_OTHER => ' DEFAULT \'draft\'',
            ],
            'total_attachment' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_comment' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_view' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_like' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_share' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_favorite' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'module_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 75,
            ],
            'item_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
        ];
    }
}
