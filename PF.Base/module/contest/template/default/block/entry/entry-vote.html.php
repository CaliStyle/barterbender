{*
<div>
    <span id="entry_total_votes">{$aEntryViewTemplateParam.aEntry.total_vote} {phrase var='contest.votes'}</span> 
    <span id="block_entry_voted" {if $aEntryViewTemplateParam.aEntry.is_voted}style="display:none"{/if}><a href="#" onclick="$.ajaxCall('contest.addVote','entry_id={$aEntryViewTemplateParam.aEntry.entry_id}&is_voted=0');return false;"><button type="button" class='button btn btn-success btn-sm'>{phrase var='contest.vote'}</button></a></span>
    <span id="block_entry_unvoted" {if !$aEntryViewTemplateParam.aEntry.is_voted}style="display:none"{/if}><a href="#" onclick="$.ajaxCall('contest.addVote','entry_id={$aEntryViewTemplateParam.aEntry.entry_id}&is_voted=1');return false;"><button type="button" class='button btn btn-success btn-sm'>{phrase var='contest.un_vote'}</button></a></span>
</div>
*}
{if $hide_vote==0}
{literal}
<style>
	.yc_button_vote_button{
		background:#3B5998;
		background-image: -ms-linear-gradient(top, #6179AA 0%, #3B5998 100%);
		background-image: -moz-linear-gradient(top, #6179AA 0%, #3B5998 100%);
		background-image: -o-linear-gradient(top, #6179AA 0%, #3B5998 100%);
		background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #6179AA), color-stop(1, #3B5998));
		background-image: -webkit-linear-gradient(top, #6179AA 0%, #3B5998 100%);
		background-image: linear-gradient(to bottom, #6179AA 0%, #3B5998 100%);
		filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#6179AA',endColorstr='#3B5998',GradientType=0);
	    border-radius: 4px 4px 4px 4px;
	    border-top: 1px solid #B0BCD5;
	    display: block;
	    float: left;
	    margin-left: 84px;
	    margin-top: 10px;
	    padding: 5px 15px;
	    color: white;
	    cursor: pointer;
	    
	}
</style>
{/literal}


<div class="yc_vote_entry">
	<p class="yc_number_vote"><span id="entry_total_votes">{$aEntryViewTemplateParam.aEntry.total_vote} {phrase var='contest.votes'}</span></p>
	{if $aEntryViewTemplateParam.aEntry.can_vote_entry}
		<span id="block_entry_voted" {if $aEntryViewTemplateParam.aEntry.is_voted}style="display:none"{/if}>
			<a href="#" onclick="$.ajaxCall('contest.addVote','entry_id={$aEntryViewTemplateParam.aEntry.entry_id}&is_voted=0');return false;">
				<button type="button" class='btn btn-success btn-sm '>{phrase var='contest.vote'}</button>
			</a>
		</span>
	    <span id="block_entry_unvoted" {if !$aEntryViewTemplateParam.aEntry.is_voted}style="display:none"{/if}><a href="#" onclick="$.ajaxCall('contest.addVote','entry_id={$aEntryViewTemplateParam.aEntry.entry_id}&is_voted=1');return false;"><button type="button" class='btn btn-success btn-sm'>{phrase var='contest.un_vote'}</button></a></span>
    {/if}
	<div class="clear"></div>
</div>
{/if}
