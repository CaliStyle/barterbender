
<div class="yc_detail_contest item_view_content">
    {$aContest.description_show|parse|shorten:'350':'comment.view_more':true}
</div>

{if $aContest.total_attachment}
	{module name='attachment.list' sType=contest iItemId=$aContest.contest_id}
{/if}