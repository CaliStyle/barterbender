<?php
\Phpfox_Module::instance()
    ->addServiceNames([
        'ynfeed' => \Apps\YNC_Feed\Service\Feed::class,
        'ynfeed.api' => \Apps\YNC_Feed\Service\Api::class,
        'ynfeed.callback' => \Apps\YNC_Feed\Service\Callback::class,
        'ynfeed.process' => \Apps\YNC_Feed\Service\Process::class,
        'ynfeed.block' => \Apps\YNC_Feed\Service\Block::class,
        'ynfeed.filter' => \Apps\YNC_Feed\Service\Filter::class,
        'ynfeed.emoticon' => \Apps\YNC_Feed\Service\Emoticon::class,
        'ynfeed.feeling' => \Apps\YNC_Feed\Service\Feeling::class,
        'ynfeed.user.process' => \Apps\YNC_Feed\Service\User\Process::class,
        'ynfeed.photo.process' => \Apps\YNC_Feed\Service\Photo\Process::class,
        'ynfeed.directory' => \Apps\YNC_Feed\Service\Directory\Directory::class,
        'ynfeed.save' => \Apps\YNC_Feed\Service\Save::class,
        'ynfeed.hide' => \Apps\YNC_Feed\Service\Hide::class,
    ])
    ->addComponentNames('controller', [
        'ynfeed.admincp.add-filter' => \Apps\YNC_Feed\Controller\Admin\AddFilterController::class,
        'ynfeed.admincp.manage-filter' => \Apps\YNC_Feed\Controller\Admin\ManageFilterController::class,
        'ynfeed.photo.frame' => \Apps\YNC_Feed\Controller\Photo\Frame::class,
        'ynfeed.advancedphoto.frame' => \Apps\YNC_Feed\Controller\Advancedphoto\Frame::class,
    ])
    ->addComponentNames('block', [
        'ynfeed.checknew' => \Apps\YNC_Feed\Block\Checknew::class,
        'ynfeed.display' => \Apps\YNC_Feed\Block\Display::class,
        'ynfeed.edit-user-status' => \Apps\YNC_Feed\Block\EditUserStatus::class,
        'ynfeed.form' => \Apps\YNC_Feed\Block\Form::class,
        'ynfeed.form2' => \Apps\YNC_Feed\Block\Form2::class,
        'ynfeed.load_dates' => \Apps\YNC_Feed\Block\LoadDates::class,
        'ynfeed.mini' => \Apps\YNC_Feed\Block\Mini::class,
        'ynfeed.rating' => \Apps\YNC_Feed\Block\Rating::class,
        'ynfeed.share.share' => \Apps\YNC_Feed\Block\Share\Share::class,
        'ynfeed.share.link' => \Apps\YNC_Feed\Block\Share\Link::class,
        'ynfeed.share.frame' => \Apps\YNC_Feed\Block\Share\Frame::class,
        'ynfeed.manage-hidden' => \Apps\YNC_Feed\Block\ManageHidden::class,
        'ynfeed.share.preview' => \Apps\YNC_Feed\Block\Share\Preview::class,
    ])
    ->addComponentNames('ajax', [
        'ynfeed.ajax' => \Apps\YNC_Feed\Ajax\Ajax::class,
    ])
    ->addTemplateDirs([
        'ynfeed' => PHPFOX_DIR_SITE_APPS . 'ync-feed' . PHPFOX_DS . 'views',
    ])
    ->addAliasNames('ynfeed', 'YNC_Feed');

group('/ynfeed/admincp', function () {
    route('/manage-filter/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'ynfeed_filter',
                'key' => 'filter_id',
                'values' => $values,
            ]
        );
        return true;
    });
});

route('/ynfeed/photo/frame', function (){
    Phpfox_Module::instance()->dispatch('ynfeed.photo.frame');
    return 'controller';
});

route('/ynfeed/advancedphoto/frame', function (){
    Phpfox_Module::instance()->dispatch('ynfeed.advancedphoto.frame');
    return 'controller';
});

route('/ynfeed/strip', function() {
    $text = request()->get('text');
    $text = ynfeed_strip_item_content($text);
    echo $text;
});

function parsePageTagged($iUserId, $iType = 0)
{
    $aPage = db()->select('p.*, ' . Phpfox::getUserField())
        ->from(Phpfox::getT('pages'), 'p')
        ->join(Phpfox::getT('user'), 'u', 'p.page_id = u.profile_page_id')
        ->where('u.user_id = ' . (int) $iUserId . ' AND p.item_type = ' . $iType)
        ->execute('getSlaveRow');

    $sOut = '';
    if (isset($aPage['title'])) {
        $sOut = '<a href="' . ($iType ? Phpfox::getService('groups')->getUrl($aPage['page_id'], '') : Phpfox::getService('pages')->getUrl($aPage['page_id'], '')) . '">' . $aPage['title'] . '</a>';
    }
    return $sOut;
}

function parseBusinessTagged($iBusinessId)
{
    $aBusiness = db()->select('*')
        ->from(Phpfox::getT('ynclistingcar_business'))
        ->where('business_id = ' . (int)$iBusinessId)
        ->execute('getSlaveRow');

    $sOut = '';
    if (isset($aBusiness['name'])) {
        $sOut = '<a href="' . Phpfox::permalink('car.detail', $aBusiness['business_id'], $aBusiness['name']) . '">' . $aBusiness['name'] . '</a>';
    }
    return $sOut;
}

function ynfeed_parse_emojis($sTxt) {
    $aEmojis = Phpfox::getService('ynfeed.emoticon')->getAll();
    $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-feed';
    /*parse emojis*/
    // why evilgrin? evilgrin code is " ]:) " when status is parsed using updateFormValue when editing post
    //  data-code is parsed again with " :) " and become data-code="]<img data-code=":)"
    foreach ($aEmojis as $aEmoji) {
        $sTxt = str_replace($aEmoji['code'], '<img data-code="'. ($aEmoji['code'] == ']:)' ? '(evilgrin)' : $aEmoji['code']) . '" class="ynfeed_content_emoji" title="' . $aEmoji['title'] . '" src="' . $corePath . '/assets/images/emoticons/' . $aEmoji['image'] . '"  alt="' . $aEmoji['image'] . '"/>', $sTxt);
    }
    return $sTxt;
}

function ynfeed_strip($sTxt)
{
    $aEmojis = Phpfox::getService('ynfeed.emoticon')->getAll();
    $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-feed';
    // convert to html
    $sTxt = html_entity_decode($sTxt);
    $sTxt = trim(preg_replace('/<[^>]*>/', " \n ", $sTxt));

    /*parse emojis*/
    foreach ($aEmojis as $aEmoji) {
        $sTxt = str_replace($aEmoji['code'], '<img class="ynfeed_content_emoji" title="' . $aEmoji['title'] . '" src="' . $corePath . '/assets/images/emoticons/' . $aEmoji['image'] . '"  alt="' . $aEmoji['image'] . '"/>', $sTxt);
    }

    $sTxt = ynfeed_output_parse($sTxt);

    /* Parse groups/pages mentions */
    $sTxt = preg_replace_callback('/\[group=(\d+)\].+?\[\/group\]/u', function ($matches) {
        return parsePageTagged($matches[1], 1);
    }, $sTxt);
    $sTxt = preg_replace_callback('/\[page=(\d+)\].+?\[\/page\]/u', function ($matches) {
        return parsePageTagged($matches[1], 0);
    }, $sTxt);
    $sTxt = preg_replace_callback('/\[car=(\d+)\].+?\[\/car\]/u', function ($matches) {
        return parseBusinessTagged($matches[1]);
    }, $sTxt);

    return $sTxt;
}

function ynfeed_strip_item_content($sTxt)
{
    $aEmojis = Phpfox::getService('ynfeed.emoticon')->getAll();
    $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-feed';

    /* Parse groups/pages mentions */
    $sTxt = preg_replace_callback('/\[group=(\d+)\].+?\[\/group\]/u', function ($matches) {
        return parsePageTagged($matches[1], 1);
    }, $sTxt);
    $sTxt = preg_replace_callback('/\[page=(\d+)\].+?\[\/page\]/u', function ($matches) {
        return parsePageTagged($matches[1], 0);
    }, $sTxt);

    /*parse emojis*/
    foreach ($aEmojis as $aEmoji) {
        $sTxt = str_replace($aEmoji['code'], '<img class="ynfeed_content_emoji" title="' . $aEmoji['title'] . '" src="' . $corePath . '/assets/images/emoticons/' . $aEmoji['image'] . '"  alt="' . $aEmoji['image'] . '"/>', $sTxt);
    }
    $sTxt = html_entity_decode($sTxt);
    return $sTxt;
}

function removeMentionInText($iId, $sTxt)
{
    return preg_replace('/(?:\[(?:user|group|page)=' . $iId . '\])([a-zA-Z0-9\s]+)(?:\[\/(?:user|group|page)\])/u', '$1', $sTxt);
}

/**
 * Use this function instead of parse output lib.
 * Because the lib function always strip \n chars although $bParseNewLine is true
 * @param $sTxt
 * @return mixed|string
 */
function ynfeed_output_parse($sTxt)
{
    $oParseLib = Phpfox::getLib('parse.output');
    if (empty($sTxt)) {
        return $sTxt;
    }

    $sTxt = ' ' . $sTxt;

    (($sPlugin = Phpfox_Plugin::get('parse_output_parse')) ? eval($sPlugin) : null);

    if (isset($override) && is_callable($override)) {
        $sTxt = call_user_func($override, $sTxt);
    } elseif (!Phpfox::getParam('core.allow_html')) {
        $sTxt = $oParseLib->htmlspecialchars($sTxt);
    } else {
        $sTxt = $oParseLib->cleanScriptTag($sTxt);
    }

    $sTxt = Phpfox::getService('ban.word')->clean($sTxt);

    $sTxt = $oParseLib->parseUrls($sTxt);

    $sTxt = preg_replace_callback('/\[PHPFOX_PHRASE\](.*?)\[\/PHPFOX_PHRASE\]/i', function ($aMatches) {
        return (isset($aMatches[1]) ? _p($aMatches[1]) : $aMatches[0]);
    }, $sTxt);

    $sTxt = ' ' . $sTxt;
    $sTxt = Phpfox::getLib('parse.bbcode')->parse($sTxt);

    if (Phpfox::getParam('tag.enable_hashtag_support')) {
        $sTxt = $oParseLib->replaceHashTags($sTxt);
    }

    //support responsive table
    $sTxt = preg_replace("/<table([^\>]*)>/uim", "<div class=\"table-wrapper table-responsive\"><table $1>", $sTxt);
    $sTxt = preg_replace("/<\/table>/uim", "</table></div>", $sTxt);

    $sTxt = str_replace("\n", "<div class=\"newline\"></div>", $sTxt);
    $sTxt = $oParseLib->replaceUserTag($sTxt);
    $sTxt = trim($sTxt);
    return $sTxt;
}

//((new Apps\YNC_Feed\Install())->processInstall());
