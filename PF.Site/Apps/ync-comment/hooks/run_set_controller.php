<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/28/18
 * Time: 01:48
 */

if (Phpfox::getLib('module')->getFullControllerName() == 'core.index-member') {
    Phpfox::getLib('setting')->setParam('comment.comment_page_limit',
        setting('ynccomment.ynccomment_comment_show_on_activity_feeds', 4) == 0 ? null : setting('ynccomment.ynccomment_comment_show_on_activity_feeds', 4));
    if (!setting('ynccomment.ynccomment_show_replies_on_comment')) {
        Phpfox::getLib('setting')->setParam('comment.thread_comment_total_display',0);
    } else {
        Phpfox::getLib('setting')->setParam('comment.thread_comment_total_display',
            setting('ynccomment.ynccomment_replies_show_on_activity_feeds',
                4) == 0 ? null : setting('ynccomment.ynccomment_replies_show_on_activity_feeds', 1));
    }
} else {
    Phpfox::getLib('setting')->setParam('comment.comment_page_limit',
        setting('ynccomment.ynccomment_comments_show_on_item_details',
            4) == 0 ? null : setting('ynccomment.ynccomment_comments_show_on_item_details', 4));
    if (!setting('ynccomment.ynccomment_show_replies_on_comment')) {
        Phpfox::getLib('setting')->setParam('comment.thread_comment_total_display',0);
    } else {
        Phpfox::getLib('setting')->setParam('comment.thread_comment_total_display',
            setting('ynccomment.ynccomment_replies_show_on_item_details',
                4) == 0 ? null : setting('ynccomment.ynccomment_replies_show_on_item_details', 1));
    }
}