<?php

function ynccomment_parse($sTxt) {
    $aEmojis = Phpfox::getService('ynccomment.emoticon')->getAll();
    $corePath = Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-comment';
    $sTxt = ynccomment_output_parse($sTxt);
    // Parse groups/pages mentions
    if (Phpfox::isModule('groups')) {
        $sTxt = preg_replace_callback('/\[group=(\d+)\].+?\[\/group\]/u', function ($matches) {
            return ynccomment_parsePageTagged($matches[1], 1);
        }, $sTxt);
    }
    if (Phpfox::isModule('pages')) {
        $sTxt = preg_replace_callback('/\[page=(\d+)\].+?\[\/page\]/u', function ($matches) {
            return ynccomment_parsePageTagged($matches[1], 0);
        }, $sTxt);
    }
    //Parse emoticon
    foreach($aEmojis as $aEmoji) {
        $sTxt = str_replace($aEmoji['code'], '<span class="item-tag-emoji"><img class="yncomment_content_emoji" title="'. $aEmoji['title'] .'" src="' . $corePath .'/assets/images/emoticons/'. $aEmoji['image'] . '"  alt="'. $aEmoji['image'] .'"/></span>', $sTxt);
    }
    return $sTxt;
}
function ynccomment_parsePageTagged($iPageId,$iType)
{
    $oService = $iType ? Phpfox::getService('groups') : Phpfox::getService('pages');
    $aPage = $oService->getPage($iPageId);
    $sUrl = $oService->getUrl($aPage['page_id'], $aPage['title'],
        $aPage['vanity_url']);
    $sOut = '';
    if (isset($aPage['title']))
    {
        $sOut = '<a href="' . $sUrl .'">' . $aPage['title'] .'</a>';
    }
    return $sOut;
}

function ynccomment_parse_emojis($sTxt) {
    $aEmojis = Phpfox::getService('ynccomment.emoticon')->getAll();
    $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-comment';
    /*parse emojis*/
    foreach ($aEmojis as $aEmoji) {
        $sTxt = str_replace($aEmoji['code'], '<span class="item-tag-emoji yncomment-item-tag-emoji"><img data-code="'. $aEmoji['code'] . '" class="yncomment_content_emoji" title="' . $aEmoji['title'] . '" src="' . $corePath . '/assets/images/emoticons/' . $aEmoji['image'] . '"  alt="' . $aEmoji['image'] . '"/></span>', $sTxt);
    }
    return $sTxt;
}

/**
 * Use this function instead of parse output lib.
 * Because the lib function always strip \n chars although $bParseNewLine is true
 * @param $sTxt
 * @return mixed|string
 */
function ynccomment_output_parse($sTxt)
{
    $oParseLib = Phpfox::getLib('parse.output');
    if (empty($sTxt))
    {
        return $sTxt;
    }

    $sTxt = ' ' . $sTxt;

    (($sPlugin = Phpfox_Plugin::get('parse_output_parse')) ? eval($sPlugin) : null);

    if (isset($override) && is_callable($override)) {
        $sTxt = call_user_func($override, $sTxt);
    }
    elseif (!Phpfox::getParam('core.allow_html')) {
        $sTxt = $oParseLib->htmlspecialchars($sTxt);
    }
    else {
        $sTxt = $oParseLib->cleanScriptTag($sTxt);
    }

    $sTxt = Phpfox::getService('ban.word')->clean($sTxt);

    if (!Phpfox::getParam('core.disable_all_external_urls')) {
        $sTxt = $oParseLib->parseUrls($sTxt);
    }

    $sTxt = preg_replace_callback('/\[PHPFOX_PHRASE\](.*?)\[\/PHPFOX_PHRASE\]/i', function($aMatches) { return (isset($aMatches[1]) ? _p($aMatches[1]) : $aMatches[0]);}, $sTxt);

    $sTxt = ' ' . $sTxt;
    $sTxt = Phpfox::getLib('parse.bbcode')->parse($sTxt);

    if (Phpfox::getParam('tag.enable_hashtag_support'))
    {
        $sTxt = $oParseLib->replaceHashTags($sTxt);
    }

    //support responsive table
    $sTxt = preg_replace("/<table([^\>]*)>/uim", "<div class=\"table-wrapper table-responsive\"><table $1>", $sTxt);
    $sTxt = preg_replace("/<\/table>/uim", "</table></div>", $sTxt);

    $sTxt = str_replace("\n", "</br>", $sTxt);
    $sTxt = $oParseLib->replaceUserTag($sTxt);
    $sTxt = trim($sTxt);
    return $sTxt;
}
$module = Phpfox_Module::instance();

$module->addAliasNames('ynccomment', 'YNC_Comment');

$module->addComponentNames('controller', [
    'ynccomment.comments' => Apps\YNC_Comment\Controller\CommentsController::class,
    'ynccomment.replies' => Apps\YNC_Comment\Controller\RepliesController::class,
    'ynccomment.admincp.manage-stickers' => Apps\YNC_Comment\Controller\Admin\ManageStickersController::class,
    'ynccomment.admincp.pending-comments' => Apps\YNC_Comment\Controller\Admin\PendingCommentsController::class,
    'ynccomment.admincp.add-sticker-set' => Apps\YNC_Comment\Controller\Admin\AddStickerSetController::class,
    'ynccomment.admincp.frame-upload' => Apps\YNC_Comment\Controller\Admin\FrameUploadController::class,
    'ynccomment.admincp.index' => Apps\YNC_Comment\Controller\Admin\IndexController::class
])->addComponentNames('block',[
    'ynccomment.comment' => Apps\YNC_Comment\Block\CommentBlock::class,
    'ynccomment.mini' => Apps\YNC_Comment\Block\MiniBlock::class,
    'ynccomment.attach-sticker' => Apps\YNC_Comment\Block\AttachStickerBlock::class,
    'ynccomment.emoticon' => Apps\YNC_Comment\Block\EmoticonBlock::class,
    'ynccomment.sticker-collection' => Apps\YNC_Comment\Block\StickerCollectionBlock::class,
    'ynccomment.edit-history' => Apps\YNC_Comment\Block\EditHistory::class,
    'ynccomment.more-replies' => Apps\YNC_Comment\Block\MoreReplies::class
])->addComponentNames('ajax', [
    'ynccomment.ajax' => Apps\YNC_Comment\Ajax\Ajax::class
])->addServiceNames([
    'ynccomment.stickers' => Apps\YNC_Comment\Service\Stickers\Stickers::class,
    'ynccomment.tracking' => Apps\YNC_Comment\Service\Tracking::class,
    'ynccomment.stickers.process' => Apps\YNC_Comment\Service\Stickers\Process::class,
    'ynccomment' => Apps\YNC_Comment\Service\Ynccomment::class,
    'ynccomment.process' => Apps\YNC_Comment\Service\Process::class,
    'ynccomment.callback' => Apps\YNC_Comment\Service\Callback::class,
    'ynccomment.emoticon' => Apps\YNC_Comment\Service\Emoticon::class,
    'ynccomment.history' => Apps\YNC_Comment\Service\History::class
]);
// Register template directory
$module->addTemplateDirs([
    'ynccomment' => PHPFOX_DIR_SITE_APPS . 'ync-comment' . PHPFOX_DS . 'views',
]);

group('/ynccomment', function () {
    // BackEnd routes
    route('/comments','ynccomment.comments');
    route('/replies','ynccomment.replies');
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('ynccomment.admincp.manage-stickers');
        return 'controller';
    });
    route('/admincp/stickers-set/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'ynccomment_sticker_set',
                'key' => 'set_id',
                'values' => $values,
            ]
        );
        Phpfox::getLib('cache')->remove();
        return true;
    });
});


Phpfox::getLib('setting')->setParam('ynccomment.thumbnail_sizes', array(150, 200));
Phpfox::getLib('setting')->setParam('ynccomment.attach_sizes', array(150, 200, 500, 1024));