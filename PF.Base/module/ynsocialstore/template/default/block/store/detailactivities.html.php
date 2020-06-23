<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/18/16
 * Time: 10:44 AM
 */
?>
<div class="ynsocialstore-store-detail-activities">
    {module name='feed.display'}
</div>

{literal}
<script>
    $Behavior.ynsocialstore_init_feed_activities = function () {
        if ($('#btn_display_with_friend').length) {
            $('#btn_display_with_friend').hide();
        }
    }
</script>
{/literal}