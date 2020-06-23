<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/6/17
 * Time: 17:00
 */

namespace Apps\YNC_Affiliate\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;

class yncaffiliate_faqs extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'yncaffiliate_faqs';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'faq_id' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true
            ],
            'is_active' => [
                'type' => 'tinyint',
                'type_value' => 1,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'1\'',
            ],
            'ordering' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'question' => [
                'type' => 'varchar',
                'type_value' => 255,
                'other' => 'NOT NULL',
            ],
            'answer' => [
                'type' => 'text',
                'other' => 'DEFAULT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL',
            ],
        ];
    }
}