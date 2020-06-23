<?php
$aValidation = [
    'fevent_max_instance_repeat_event' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Maximum instances of each repeat events" must be greater than 0'),
    ],
    'subscribe_within_day' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Send email to subscribers information of new events which happen within days" must be greater than 0'),
    ],
    'fevent_time_to_show_countdown' => [
        'def' => 'int:required',
        'min' => '0',
        'title' => _p('"Time to show count down section in event details" must be number and greater than or equal to 0'),
    ]
];

if(!empty($_POST['val']['value'])) {
    $isValid = true;
    $postValues = $_POST['val']['value'];
    if($custom_url = trim($postValues['fevent_custom_url'])) {
        $oParse = Phpfox::getLib('parse.input');
        $custom_url = $oParse->clean($custom_url);
        $url = "http://test.com/" . $custom_url;
        if (strlen($custom_url) > 20) {
            $isValid = false;
            $aValidation['fevent_custom_url'] = [
                'title' => _p('url_name_exceeds_twenty_characters'),
            ];
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $isValid = false;
            $aValidation['fevent_custom_url'] = [
                'title' => _p('invalid_url_name'),
            ];
        }
    } else {
        $custom_url = 'fevent';
    }

    $check = db()
        ->select('rewrite_id')
        ->from(Phpfox::getT('rewrite'))
        ->where('url = \'fevent\'')
        ->execute('getSlaveField');

    if (empty($check)) {
        db()->insert(Phpfox::getT('rewrite'), [
            'url' => 'fevent',
            'replacement' => $custom_url,
        ]);
    } else {
        db()->update(Phpfox::getT('rewrite'), [
            'replacement' => $custom_url
        ], 'url = \'fevent\'');
    }

    $oCache = Phpfox::getLib('cache')->remove('rewrite');
}
