<?php

namespace Apps\P_AdvMarketplace\Installation\Version;

use Phpfox;

class v403
{
    public function process()
    {
        $this->addFields();
        $this->removeOldControllers();
        $this->replaceOldBlocks();
        $this->updateExpiryDate();
        $this->maybeAddCron();
        $this->addDefaultCategories();
        $this->updateModuleToApp();
        $this->removeSettings();
        $this->addUserCountField();
    }

    private function addFields()
    {
        // add activity advlisting
        if (!db()->isField(':user_activity', 'activity_advancedmarketplace')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD `activity_advancedmarketplace` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add statistics total advlisting
        if (!db()->isField(':user_field', 'total_advlisting')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD `total_advlisting` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add space field
        if (!db()->isField(':user_space', 'space_advancedmarketplace')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_space') . "` ADD `space_advancedmarketplace` INT(10) UNSIGNED DEFAULT '0'");
        }
    }

    private function removeOldControllers() {
        db()->delete(':component', '`module_id` = "advancedmarketplace" AND `is_controller` = 1 AND `component` IN ("profile", "view")');
    }

    private function replaceOldBlocks()
    {
        // replace blocks of previous version
        $aReplacedBlocks = array(
            'search' => array(),
            'filter' => array(),
            'menu' => array(),
            'profile' => array(),
            'info' => array(),
            'rating' => array(),
            'image' => array(),
            'invite' => array(),
            'invitelist' => array(),
            'listingslideshow' => array(),
            'detailslideshow' => array(),
            'my' => array(),
            'featured' => array(
                'new_component' => 'listinglist',
                'old_params' => array(
                    'data_source' => 'featured',
                    'display_view_more' => '1',
                    'limit' => '6',
                    'is_slider' => '1',
                )
            ),
            'interestedlisting' => array(
                'new_component' => 'listinglist',
                'old_params' => array(
                    'data_source' => 'interested',
                    'display_view_more' => '1',
                    'limit' => '3',
                    'is_slider' => '0',
                )
            ),
            'morefromseller' => array(
                'new_component' => 'listinglist',
                'old_params' => array(
                    'data_source' => 'more_from_seller',
                    'display_view_more' => '1',
                    'limit' => '3',
                    'is_slider' => '0',
                )
            ),
            'mostreviewedlisting' => array(
                'new_component' => 'listinglist',
                'old_params' => array(
                    'data_source' => 'most_reviewed',
                    'display_view_more' => '1',
                    'limit' => '3',
                    'is_slider' => '0',
                )
            ),
            'mostviewlisting' => array(
                'new_component' => 'listinglist',
                'old_params' => array(
                    'data_source' => 'most_viewed',
                    'display_view_more' => '1',
                    'limit' => '3',
                    'is_slider' => '0',
                )
            ),
            'recentlisting' => array(
                'new_component' => 'listinglist',
                'old_params' => array(
                    'data_source' => 'latest',
                    'display_view_more' => '1',
                    'limit' => '6',
                    'is_slider' => '0',
                )
            ),
            'recentviewlisting' => array(
                'new_component' => 'listinglist',
                'old_params' => array(
                    'data_source' => 'recent_viewed',
                    'display_view_more' => '0',
                    'limit' => '4',
                    'is_slider' => '0',
                )
            ),
            'sponsored' => array(
                'new_component' => 'listinglist',
                'old_params' => array(
                    'data_source' => 'sponsored',
                    'display_view_more' => '0',
                    'limit' => '4',
                    'is_slider' => '0',
                )
            ),
            'todaylisting' => array(
                'new_component' => 'listinglist',
                'old_params' => array(
                    'data_source' => 'today',
                    'display_view_more' => '0',
                    'limit' => '6',
                    'is_slider' => '0',
                )
            ),
        );

        $aOldBlocks = db()->select('*')
            ->from(':block')
            ->where('module_id = "advancedmarketplace"')
            ->executeRows();

        foreach ($aOldBlocks as $aOldBlock) {
            $sComponent = $aOldBlock['component'];
            if (isset($aReplacedBlocks[$sComponent])) {
                if (empty($aReplacedBlocks[$sComponent])) {
                    db()->delete(':block', '`module_id` = "advancedmarketplace" AND `component` = "' . $sComponent . '"');
                } else {
                    if (!empty($aOldBlock['params'])) {
                        $aOldParams = json_decode($aOldBlock['params'], true);
                        $aParams = array_merge($aReplacedBlocks[$sComponent]['old_params'], $aOldParams);
                    } else {
                        $aParams = array_merge($aReplacedBlocks[$sComponent]['old_params']);
                    }

                    db()->update(':block',
                        array(
                            'component' => $aReplacedBlocks[$sComponent]['new_component'],
                            'params' => json_encode($aParams)
                        ),
                        array(
                            'block_id' => $aOldBlock['block_id']
                        )
                    );
                }
            }
        }

        foreach ($aReplacedBlocks as $key => $aReplacedBlock) {
            db()->delete(':component', '`module_id` = "advancedmarketplace" AND `is_controller` = 0 AND `component` = "' . $key . '"');
        }
    }

    private function updateExpiryDate()
    {
        $iDaysToExpireSinceAdded = Phpfox::getParam('advancedmarketplace.days_to_expire_listing', 0) * 86400;
        if ($iDaysToExpireSinceAdded) {
            $table = Phpfox::getT('advancedmarketplace');
            $query = "UPDATE $table SET has_expiry = 1, expiry_date = time_stamp + " . (int)$iDaysToExpireSinceAdded;
            db()->query($query);
        }
    }

    private function maybeAddCron()
    {
        $iCron = db()->select('COUNT(*)')
            ->from(':cron')
            ->where('module_id = \'advancedmarketplace\'')
            ->execute('getSlaveField');
        if (!$iCron) {
            db()->insert(Phpfox::getT('cron'), [
                'module_id' => 'advancedmarketplace',
                'product_id' => 'phpfox',
                'type_id' => 2,
                'every' => 1,
                'is_active' => 1,
                'php_code' => 'Phpfox::getService(\'advancedmarketplace.process\')->sendExpireNotifications();'
            ]);
        }
    }

    private function addDefaultCategories()
    {
        $iCntCategory = db()->select('COUNT(category_id)')
            ->from(':advancedmarketplace_category')
            ->execute('getField');

        if (!$iCntCategory) {
            db()->query("INSERT IGNORE INTO `" . Phpfox::getT('advancedmarketplace_category') . "` (`category_id`, `parent_id`, `is_active`, `name`, `image_path`, `server_id`, `ordering` ) VALUES
			(1, 0, 1, 'Community', 'community.png', -1, 1),
            (2, 0, 1, 'Houses', 'houses.png', -1, 2),
            (3, 0, 1, 'Jobs', 'jobs.png', -1, 3),
            (4, 0, 1, 'Pets', 'pets.png', -1, 4),
            (5, 0, 1, 'Rentals', 'rentals.png', -1, 5),
            (6, 0, 1, 'Services', 'services.png', -1, 6),
            (7, 0, 1, 'Stuff', 'stuff.png', -1, 7),
            (8, 0, 1, 'Tickets', 'tickets.png', -1, 8),
            (9, 0, 1, 'Vehicle', 'vehicle.png', -1, 9)
			");
        }
    }

    private function updateModuleToApp()
    {
        db()->delete(':product', '`product_id` = "younet_advmarketplace4"');

        // update module is app
        db()->update(':module', ['product_id' => 'phpfox', 'phrase_var_name' => 'module_apps', 'is_active' => 1], ['module_id' => 'advancedmarketplace']);

        //delete menu add new listing
        db()->delete(':menu', '`url_value` = "advancedmarketplace.add"');
    }

    private function removeSettings() {
        db()->delete(Phpfox::getT('user_group_setting'),'module_id = "advancedmarketplace" AND name = "auto_publish_sponsored_listing"');
    }

    private function addUserCountField() {
        if(!db()->isField(PHPFOX::getT("user_count"),'advancedmarketplace_invite'))
        {
            db()->query("ALTER TABLE `" . phpfox::getT("user_count") . "` ADD COLUMN `advancedmarketplace_invite` INTEGER(10) UNSIGNED NOT NULL default '0';");
        }
    }
}