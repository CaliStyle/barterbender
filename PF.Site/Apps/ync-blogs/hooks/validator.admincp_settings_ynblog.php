<?php

$isValid = true;
if (!empty($_POST['val']['value']['custom_url']) && trim($_POST['val']['value']['custom_url'])) {
    $oParse = Phpfox::getLib('parse.input');
    $custom_url = trim($_POST['val']['value']['custom_url']);
    $custom_url = $oParse->clean($custom_url);
    $url = "http://test.com/" . $custom_url;

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $isValid = false;
        $aValidation['custom_url'] = [
            'title' => _p('invalid_url_name'),
        ];
    }
} else {
    $custom_url = Phpfox::getParam('ynblog.custom_url', 'advanced-blog');
}

if ($isValid) {
    $aRewrite = db()
        ->select('*')
        ->from(Phpfox::getT('rewrite'))
        ->where('url = \'ynblog\'')
        ->execute('getSlaveRow');

    if (empty($aRewrite)) {
        db()->insert(Phpfox::getT('rewrite'), [
            'url' => 'ynblog',
            'replacement' => $custom_url,
        ]);
    } else {
        db()->update(Phpfox::getT('rewrite'), array(
            'replacement' => $custom_url
        ), 'url = \'ynblog\'');
    }

//    $oCache = Phpfox::getLib('cache')->remove('rewrite');
    Phpfox::getLib('cache')->remove(); // remove rewrite and menu for every user group
}

