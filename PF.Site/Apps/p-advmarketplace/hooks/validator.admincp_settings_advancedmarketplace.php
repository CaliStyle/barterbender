<?php
$aValidation['how_many_sponsored_listings'] = [
    'def' => 'int',
    'min' => '0',
    'title' => _p('"How Many Sponsored Listings To Show" must be greater than or equal to 0.'),
];
$aValidation['total_listing_more_from'] = [
    'def' => 'int',
    'min' => '0',
    'title' => _p('"Total More From Listings to Display" must be greater than or equal to 0.'),
];
$aValidation['advmarketplace_days_to_notify_expire'] = [
    'def' => 'int',
    'min' => '0',
    'title' => _p('"Days to Notify Expiring Listing" must be greater than or equal to 0.'),
];

$isValid = true;
if (!empty($_POST['val']['value']['advmarketplace_custom_url']) && trim($_POST['val']['value']['advmarketplace_custom_url'])) {
    $oParse = Phpfox::getLib('parse.input');
    $custom_url = trim($_POST['val']['value']['advmarketplace_custom_url']);
    $custom_url = $oParse->clean($custom_url);
    $url = "http://test.com/" . $custom_url;
    if (strlen($custom_url) > 20) {
        $isValid = false;
        $aValidation['advmarketplace_custom_url'] = [
            'title' => _p('url_name_exceeds_twenty_characters'),
        ];
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $isValid = false;
        $aValidation['advmarketplace_custom_url'] = [
            'title' => _p('invalid_url_name'),
        ];
    }
} else {
    $custom_url = 'advancedmarketplace';
}

if ($isValid) {
    $aRewrite = db()
        ->select('*')
        ->from(Phpfox::getT('rewrite'))
        ->where('url = \'advancedmarketplace\'')
        ->execute('getSlaveRow');

    if (empty($aRewrite)) {
        db()->insert(Phpfox::getT('rewrite'), [
            'url' => 'advancedmarketplace',
            'replacement' => $custom_url,
        ]);
    } else {
        db()->update(Phpfox::getT('rewrite'), array(
            'replacement' => $custom_url
        ), 'url = \'advancedmarketplace\'');
    }

//    $oCache = Phpfox::getLib('cache')->remove('rewrite');
    Phpfox::getLib('cache')->remove(); // remove rewrite and menu for every user group
}