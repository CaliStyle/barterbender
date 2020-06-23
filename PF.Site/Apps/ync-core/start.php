<?php

$module = Phpfox_Module::instance();

$module->addAliasNames('ynccore', 'YNC_Core')
    ->addComponentNames('controller', [])
    ->addComponentNames('block', [
        'ynccore.mode_view' => Apps\YNC_Core\Block\ModeView::class
    ]);

// Register template directory
$module->addTemplateDirs([
    'ynccore' => PHPFOX_DIR_SITE_APPS . 'ync-core' . PHPFOX_DS . 'views',
]);

group('/ynccore', function () {
    route('/', 'ynccore.index');
});

function ync_n($number, $single, $plural, $translate = 1)
{
    if ($number == 1) {
        return $translate ? _p($single) : $single;
    } else {
        return $translate ? _p($plural) : $plural;
    }
}

function ync_ne($number, $single, $plural, $translate = 1)
{
    echo ync_n($number, $single, $plural, $translate);
}