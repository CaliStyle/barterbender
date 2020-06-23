<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;
use Core\App\Install\Database\Field as Field;

/**
 * Class Advancedmarketplace_Category
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Category extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_category';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'category_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'parent_id' =>
                array(
                    'type' => 'mediumint',
                    'type_value' => '8',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'is_active' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'name' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'NOT NULL',
                ),
            'name_url' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'image_path' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 50,
                Field::FIELD_PARAM_OTHER => 'DEFAULT NULL'
            ],
            'server_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_TINYINT,
                Field::FIELD_PARAM_TYPE_VALUE => 1,
                Field::FIELD_PARAM_OTHER => 'DEFAULT 0'
            ],
            'time_stamp' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'used' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'ordering' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
        );
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = array();
    }
}