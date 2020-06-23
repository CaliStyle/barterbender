<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
namespace Apps\Advancedfooter\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Advancedfooter_Menu extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'advancedfooter_menu';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'category_id' => [
                'type' => 'mediumint',
                'type_value' => '8',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'text',
                'other' => 'NULL',
            ],
            'time_stamp' => [
                'type' => 'mediumint',
                'type_value' => '8',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'ordering' => [
                'type' => 'mediumint',
                'type_value' => '8',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'is_active' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'link' => [
                'type' => 'text',
                'other' => 'NULL',
            ],
            'direct_link' => [
                'type' => 'text',
                'other' => 'NULL',
            ],
            'parent_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
        ];
    }
}
