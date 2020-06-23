<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="feedback_younet_entry_js">
{if !count($aFeedBacks)}
<div class="extra_info">
	{_p var='no_feedback_found'}
</div>
{else}
{foreach from=$aFeedBacks item=aFeedBack name=fbl}
    {template file='feedback.block.entry'}
{/foreach}
{if Phpfox::getUserParam('feedback.can_approve_feedbacks') || Phpfox::getUserParam('feedback.delete_user_feedback')}
	{moderation}
{/if}
{unset var=$aFeedbacks}
{pager}
{/if}
</div>

