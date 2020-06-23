<?php
;
if (Phpfox::isModule('yncwebpush')) {
    $sText = _p('push_notification_settings');
    $sLink = Phpfox::getLib('url')->makeUrl('push-notification');
    $sAttach = "<li role='presentation' id='yncwebpush_settings'><a href='" . $sLink . "'><i class='ico ico-bell2-o'></i>" . $sText . "</a></li>";
    ?>

    <script type="text/javascript">
        $Ready(function () {
            if ($("#yncwebpush_settings").length) return false;
            $('#header_menu .feed_form_menu .nav_header ul li:nth-child(4)').after("<?php echo $sAttach; ?>");
            $('#section-header #user_sticky_bar .dropdown-menu-right li:nth-child(4)').after("<?php echo $sAttach; ?>");
        });
    </script>
    <?php
};
?>