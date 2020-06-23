<?php

namespace Apps\YNC_Comment\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;


class CommentsController extends Phpfox_Component
{
    public function process()
    {
        $feed = Phpfox::getService('feed')->getForItem($this->request()->get('type'), $this->request()->get('id'));
        $aFeed = Phpfox::getService('feed')->get(null, $feed['feed_id']);
        $iTotalComment = $this->request()->get('total-comment');
        $iShownTotal = $this->request()->getInt('shown-total');
        if ($aFeed[0]['total_comment'] > $iTotalComment) {
            $iShownTotal = $iShownTotal + ($aFeed[0]['total_comment'] - $iTotalComment);
        } elseif ($aFeed[0]['total_comment'] < $iTotalComment) {
            $iShownTotal = $iShownTotal - ($iTotalComment - $aFeed[0]['total_comment']);
        }
        $this->setParam('aFeed', array_merge($aFeed[0], ['feed_display' => 'view']));
        $this->setParam([
            'iTimeStamp' => $this->request()->get('time-stamp'),
            'bIsLoadMoreComment' => true,
            'iShownTotal' => !$iShownTotal ? 0 : $iShownTotal
        ]);
        $this->template()->assign([
            'showOnlyComments' => true,
        ]);
        Phpfox::getBlock('ynccomment.comment');

        $out = "var comment = " . json_encode(['html' => ob_get_contents()]) . "; ";
        $out .= "var oCommentContainer = $('#js_feed_comment_pager_{$feed['type_id']}{$feed['item_id']}').parent().find('.ync-comment-container .js_ync_comment_comment_items'); ";
        $out .= "var oOldViewMore = oCommentContainer.closest('.js_feed_comment_view_more_holder').find('.ync-comment-viewmore');";
        $out .= "if (oOldViewMore.length) { oOldViewMore.remove(); }";
        $out .= "oCommentContainer.prepend(comment.html);";
        $out .= "var oViewMore = oCommentContainer.find('.ync_comment_viemore_holder');";
        $out .= "if (oViewMore.length) { oCommentContainer.closest('.js_feed_comment_view_more_holder').prepend(oViewMore.html()); oViewMore.remove();}";
        $out .= "\$Core.loadInit();";
        $out .= "obj.remove();";
        ob_clean();

        header('Content-type: application/json');
        echo json_encode(['run' => $out]);
        exit;
    }
}