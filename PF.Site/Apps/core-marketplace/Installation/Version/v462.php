<?php
namespace Apps\Core_Marketplace\Installation\Version;

/**
 * Class v462
 * @package Apps\Core_Marketplace\Installation\Version
 */
class v462
{
    public function process()
    {
        $this->importToRssFeed();
    }
    public function importToRssFeed()
    {
        $aRssGroup = [
            'module_id' => 'marketplace',
            'product_id' => 'phpfox',
            'name_var' => 'marketplace',
            'is_active' => 1,
            'ordering' => 0,
        ];

        $aRssData = [
            'module_id' => 'marketplace',
            'product_id' => 'phpfox',
            'feed_link' => 'marketplace',
            'php_view_code' => '$aRows = Phpfox::getService(\'marketplace\')->getForRssFeed();',
            'is_site_wide' => 1,
            'is_active' => 1
        ];

        $iCnt = db()->select('COUNT(*)')
            ->from(':rss_group')
            ->where('module_id = \'marketplace\'')
            ->execute('getSlaveField');

        if(!$iCnt)
        {
            $iGroupId = db()->insert(':rss_group', $aRssGroup);
            \Core\Lib::phrase()->addPhrase('rss_group_name_' . $iGroupId, 'Marketplace');
            \Core\Lib::phrase()->addPhrase('rss_title_' . $iGroupId, 'Latest Listings');
            \Core\Lib::phrase()->addPhrase('rss_description_' . $iGroupId,
                'List of the latest listings.');
            db()->update(':rss_group', ['name_var' => 'marketplace.rss_group_name_' . $iGroupId],
                'group_id =' . $iGroupId);
            $aRssData['title_var'] = 'forum.rss_title_' . $iGroupId;
            $aRssData['description_var'] = 'forum.rss_description_' . $iGroupId;
            $aRssData['group_id'] = $iGroupId;
            db()->insert(':rss', $aRssData);
        }
    }
}