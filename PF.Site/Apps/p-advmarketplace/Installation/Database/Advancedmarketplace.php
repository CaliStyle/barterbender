<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'listing_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'view_id' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '3',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'privacy' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'privacy_comment' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'group_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'user_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'is_featured' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'is_sponsor' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'title' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'NOT NULL',
                ),
            'currency_id' =>
                array(
                    'type' => 'char',
                    'type_value' => '3',
                    'other' => 'NOT NULL DEFAULT \'USD\'',
                ),
            'price' =>
                array(
                    'type' => 'decimal',
                    'type_value' => '14,2',
                    'other' => 'NOT NULL DEFAULT \'0.00\'',
                ),
            'country_iso' =>
                array(
                    'type' => 'char',
                    'type_value' => '2',
                    'other' => 'DEFAULT NULL',
                ),
            'country_child_id' =>
                array(
                    'type' => 'mediumint',
                    'type_value' => '8',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'postal_code' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '20',
                    'other' => 'DEFAULT NULL',
                ),
            'city' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'time_stamp' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'image_path' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '75',
                    'other' => 'DEFAULT NULL',
                ),
            'server_id' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'total_comment' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'total_like' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'total_dislike' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'is_sell' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'is_closed' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'auto_sell' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'has_expiry' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'is_notified' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'total_rate' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'privacy_rating' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '3',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'post_status' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '3',
                    'other' => 'DEFAULT NULL',
                ),
            'tag' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '100',
                    'other' => 'DEFAULT NULL',
                ),
            'update_timestamp' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'DEFAULT NULL',
                ),
            'total_view' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'DEFAULT 0',
                ),
            'total_score' =>
                array(
                    'type' => 'decimal',
                    'type_value' => '4,2',
                    'other' => 'DEFAULT \'0.00\'',
                ),
            'gmap' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'address' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'lat' =>
                array(
                    'type' => 'double',
                    'other' => 'NOT NULL',
                ),
            'lng' =>
                array(
                    'type' => 'double',
                    'other' => 'NOT NULL',
                ),
            'gmap_address' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'location' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '255',
                    'other' => 'DEFAULT NULL',
                ),
            'module_id' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '75',
                    'other' => 'NOT NULL DEFAULT \'advancedmarketplace\'',
                ),
            'item_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '11',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'payment_methods' =>
                array(
                    'type' => 'text',
                    'other' => 'NULL',
                ),
            'expiry_date' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ]
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