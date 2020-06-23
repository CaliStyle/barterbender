<?php
namespace Apps\YNC_Comment;

use Core\App;

/**
 * Class Install
 * @author  YNC
 * @version 4.6.0
 * @package Apps\YNC_Comment
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YNC_Comment';
    }

    protected function setAlias()
    {
        $this->alias = 'ynccomment';
    }

    protected function setName()
    {
        $this->name = _p('Advanced Comment');
    }

    protected function setVersion()
    {
        $this->version = '4.01p3';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
    }

    protected function setSettings()
    {
        $iIndex = 1;
        $this->settings = [
            'ynccomment_comment_show_on_activity_feeds' => [
                'var_name' => 'ynccomment_comment_show_on_activity_feeds',
                'info' => 'Number of comment will be shown on activity feeds',
                'description' => 'Define how many comments should be displayed on each activity feed. 0 mean Unlimited <br> View previous comments and View more comments options is available.',
                'type' => 'integer',
                'value' => '4',
                'ordering' => $iIndex++
            ],
            'ynccomment_comments_show_on_item_details' => [
                'var_name' => 'ynccomment_comments_show_on_item_details',
                'info' => 'Number of comment will be shown on item details',
                'description' => 'Define how many comments should be displayed on each item detail. 0 mean Unlimited <br> View previous comments and View more comments options is available.',
                'type' => 'integer',
                'value' => '4',
                'ordering' => $iIndex++
            ],
            'ynccomment_show_replies_on_comment' => [
                'var_name' => 'ynccomment_show_replies_within_comment',
                'info' => 'Show replies on comment',
                'description' => 'If yes, replies will be shown with comment when your user browse activity feed. No means replies will be hidden as a link',
                'type' => 'boolean',
                'value' => '1',
                'ordering' => $iIndex++
            ],
            'ynccomment_replies_show_on_activity_feeds' => [
                'var_name' => 'ynccomment_replies_show_on_activity_feeds',
                'info' => 'Number of replies will be shown on each comment on activity feeds',
                'description' => 'Define how many replies should be displayed on each comment on activity feed. 0 means Unlimited. <br> View previous replies and View more replies options is available. <br> Note: This is only used if <b>Show replies on comment</b> are enabled. ',
                'type' => 'integer',
                'value' => '1',
                'ordering' => $iIndex++
            ],
            'ynccomment_replies_show_on_item_details' => [
                'var_name' => 'ynccomment_replies_show_on_item_details',
                'info' => 'Number of replies will be shown on each comment on item details',
                'description' => 'Define how many replies should be displayed within each comment on item details. 0 means Unlimited. <br> View previous replies and View more replies options is available. <br> Note: This is only used if <b>Show replies on comment</b> are enabled. ',
                'type' => 'integer',
                'value' => '1',
                'ordering' => $iIndex++
            ],
        ];
        unset($iIndex);
    }

    protected function setUserGroupSettings()
    {
    }

    protected function setComponent()
    {
    }

    protected function setComponentBlock()
    {
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->admincp_route = '/ynccomment/admincp';

        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
        $this->admincp_menu = [
            _p('pending_comments') => 'ynccomment.pending-comments',
            _p('manage_stickers') => '#'
        ];
        $this->_apps_dir = 'ync-comment';
        $this->_admin_cp_menu_ajax = false;
        $this->database = [
            'Ynccomment_Emoticon',
            'Ynccomment_Hide',
            'Ynccomment_Sticker_Set',
            'Ynccomment_User_Sticker_Set',
            'Ynccomment_Stickers',
            'Ynccomment_Comment_Extra',
            'Ynccomment_Comment_Track',
            'Ynccomment_Previous_Versions'
        ];
        $this->_writable_dirs = [
            'PF.Base/file/pic/ynccomment/'
        ];
    }
}