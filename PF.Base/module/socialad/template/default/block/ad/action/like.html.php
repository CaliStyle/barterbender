{if isset($aSaData.bIsLiked)}
{if $aSaData.bIsLiked}
	 <a href="#" onclick="$.ajaxCall('socialad.unlikeItem', 
	 'item_id={$aSaData.iItemId}&item_type_id={$aSaData.iItemTypeId}&action_type_id={$aSaData.sActionTypeId}'); return false;" 
	 >
	{phrase var='unlike'}
	</a>

{else}
	 <a href="#" onclick="$.ajaxCall('socialad.likeItem', 
	 'item_id={$aSaData.iItemId}&item_type_id={$aSaData.iItemTypeId}&action_type_id={$aSaData.sActionTypeId}'); return false;" 
	 >
	<i class="fa fa-thumbs-o-up"></i>&nbsp;
	{phrase var='like'}
	</a>
{/if}
{/if}

{if isset($aSaData.sPhrase) && $aSaData.sPhrase}
 &middot {$aSaData.sPhrase}
{/if}

