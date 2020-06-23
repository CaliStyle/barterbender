<?php

$sFullControllerName = Phpfox::getLib('template')->getVar('sFullControllerName');
if (isset($sFullControllerName) == true && strpos($sFullControllerName, "ynblog") !== false) {
    $sRssTitle = _p('RSS');
    $sClassRSS = '';
    ?>

    <script type="text/javascript">
        $Behavior.ynblogLoadContentIndex = function () {
            if ($('#js_block_border_apps_yn_blog_block_rss').length === 0) {
                var content = '<div class="block" id="js_block_border_apps_yn_blog_block_rss">' +
                    '<a target="_blank" class="btn btn-sm btn-default no_ajax" href="<?php echo Phpfox::getLib('url')->makeUrl('ynblog.rss') ?>"><?php echo $sRssTitle; ?><i class="fa fa-rss-square" aria-hidden="true"></i>' +
                    '</a>' +
                    '</div>';
                $('#page_ynblog_index ._block.location_1').prepend(content);
            }
        }
    </script>
    <?php
}
