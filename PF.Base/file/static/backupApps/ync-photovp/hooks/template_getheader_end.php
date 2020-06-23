<?php
$aPhrases = [
    'done_editing' => _p('done_editing'),
    'done_tagging' => _p('done_tagging'),
    'click_here_to_tag_as_yourself' => _p('click_here_to_tag_as_yourself'),
    'editing_photo_information' => _p('editing_photo_information'),
    'tagged_in_this_photo' => _p('tagged_in_this_photo'),
];

$sData .= '<script>const ync_photovp_phrases = ' . json_encode($aPhrases) . ';</script>';
