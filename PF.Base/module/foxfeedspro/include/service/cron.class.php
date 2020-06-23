<?php

/*
 * @developer       [NTMD]
 * @copyright       [YouNet Copyright]
 * @author          [YouNet Company]
 * @package         [Module Name]
 * @version         [1.0]
 */

class NewsFeed_Service_Cron extends Phpfox_Service
{
    public function approved($aNotification)
    {
        /*
         * Approved
         */
        /*
        $feeds = phpfox::getService('newsfeed.newsfeed')->getFeedsUnapproved();
        $count_feeds = phpfox::getService('newsfeed.newsfeed')->countFeedsUnapproved();
        for ($i = 0; $i < $count_feeds; $i++)
        {
            phpfox::getService('newsfeed.newsfeed')->approvedFeeds($feeds[$i]['feed_id']);
            Phpfox::getService('notification.process')->add('feed_approved', $feeds[$i]['feed_id'], $feeds[$i]['user_id']);
        }
        $articles = phpfox::getService('newsfeed.newsfeed')->getArticlesUnapproved();
        $count_articles = phpfox::getService('newsfeed.newsfeed')->countArticlesUnapproved();
        for ($i = 0; $i < $count_articles; $i++)
        {
            phpfox::getService('newsfeed.newsfeed')->approvedArticles($articles[$i]['item_id']);
            Phpfox::getService('notification.process')->add('item_approved', $articles[$i]['item_id'], $articles[$i]['owner_id']);
        }
         */
        /*
         * Send Mail
         */

        /*
         * Notification
         */
    }
}

?>
