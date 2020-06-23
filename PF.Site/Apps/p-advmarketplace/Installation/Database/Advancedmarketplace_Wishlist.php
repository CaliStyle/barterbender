<?php

namespace Apps\P_AdvMarketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Advancedmarketplace_Track
 * @package Apps\P_AdvMarketplace\Installation\Database
 */
class Advancedmarketplace_Wishlist extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'advancedmarketplace_wishlist';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = array(
            'listing_id' => [
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                ],
            'user_id' => [
                    'type' => 'int',
                    'type_value' => '10',
                    'other' => 'UNSIGNED NOT NULL',
                    'primary_key' => true,
                ],
            'is_wishlist' => [
                'type' => 'int',
                'type_value' => '1',
                'other' => 'UNSIGNED DEFAULT 0',
            ],
        );
    }
}