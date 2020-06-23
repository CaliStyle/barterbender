<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:49 pm */ ?>
<?php

?>
<div id="feed_check_new_count" class="hide">
    <div class="btn-info">
    <div id="feed_check_new_count_link" onclick="ynfeedloadNewFeeds()"><?php echo _p('you_have_number_updates', array('number' => $this->_aVars['iCnt'])); ?></div></div>
    <script>$Core.ynfeedCheckNewFeedAfter(<?php echo $this->_aVars['aFeedIds']; ?>);</script>
</div>
