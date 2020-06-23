<?php
/**
 * [PHPFOX_HEADER]
 *
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if Phpfox::isUser() && $iFeedId > 0}
{module name='ynfeed.share.share' type=$sBookmarkType url=$sBookmarkUrl}
{else}
{module name='share.friend' type=$sBookmarkType url=$sBookmarkUrl title=$sBookmarkTitle}
{/if}
<script type="text/javascript">$Core.loadInit();</script>