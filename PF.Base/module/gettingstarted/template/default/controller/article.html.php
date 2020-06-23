<?php

/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

?>

{literal}
<style type="text/css">
	#main_content_padding
	{
		padding-bottom: 30px;
	}
	.report_this_item
	{
		display:none;
	}
    .item_view_content ul li
    {
        list-style: square inside none;
    }
    .item_view_content ol li
    {
        list-style: decimal inside none;
    }
    .item_view_content li
    {
        list-style: disc inside none;
    }
</style>
{/literal}

<div class="item_view">
	<div class="item_content item_view_content">
        {$dsarticle.description_parsed|parse}
    </div>

    {module name='gettingstarted.articledetail'}

    <!-- By Hoang Trung -->

	<div class="video_rate_body">
		<div class="video_rate_display">{module name='rate.display'}</div>
	</div>
    {if Phpfox::getUserParam('gettingstarted.can_post_comment_on_article')}
    <div>
        {module name = 'feed.comment'}
    </div>
    {/if}

    <div>
        {module name = 'gettingstarted.relatedentries'}
    </div>
</div>
