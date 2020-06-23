<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Invite
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Invite extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_invite';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'invite_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                    'auto_increment' => true,
                ),
            'listing_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                ),
            'type_id' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'visited_id' =>
                array(
                    'type' => 'tinyint',
                    'type_value' => '1',
                    'other' => 'NOT NULL DEFAULT \'0\'',
                ),
            'user_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'invited_user_id' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
                ),
            'invited_email' =>
                array(
                    'type' => 'varchar',
                    'type_value' => '100',
                    'other' => 'DEFAULT NULL',
                ),
            'time_stamp' =>
                array(
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
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