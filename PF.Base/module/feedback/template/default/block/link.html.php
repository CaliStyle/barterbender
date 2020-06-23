<?php
?>
{if $aFeedBack.canAddPicture}
<li>
	<a  class="buttonlink icon_feedback_image_new" href="{url link='feedback.up' feedback =$aFeedBack.feedback_id}">{_p var='add_picture'}</a>
</li>
{/if}

{if $aFeedBack.canEdit}
<li>
	<a class="buttonlink inlinePopup" href="#?call=feedback.editFeedBack&amp;height=400&amp;width=500&amp;feedback_id={$aFeedBack.feedback_id}" title="{_p var='edit_feedback'}">{_p var='edit'}</a>
</li>
{/if}

{if $aFeedBack.canDelete}
	{if $bFeedbackView}
		<li class="item_delete">
			<a href="{url link="feedback.delete" id=""$aFeedBack.feedback_id""}" class="no_ajax_link sJsConfirm">{_p var='delete'}</a>
		</li>
	{else}
		<li class="item_delete">
			<a class="buttonlink icon_feedback_delete" href="javascript:void(0);" onclick="deleteFeedBack({$aFeedBack.feedback_id}); return false;">{_p var='delete'}</a>
		</li>
	{/if}
{/if}

{if $aFeedBack.canApprove}
    <li class="item_delete">
    <a  class="buttonlink icon_feedback_image_new" href="javascript:void(0);" onclick="javascript:approve({$aFeedBack.feedback_id});return false;">{_p var='approve'}</a>
</li>
{/if}
