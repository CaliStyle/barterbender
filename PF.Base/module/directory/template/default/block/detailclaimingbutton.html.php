{if $aBusiness.type != 'claiming' && (int)$aBusiness.business_status == 10}
	<div class="yndirectory_claimed">
		{if $iUserID == $aBusiness.user_id}
			{phrase var='you_have_just_claimed_this_business_successfully_please_wait_for_approval_from_administrator'}
		{else}
			{phrase var='you_cannot_claim_this_business_because_it_is_being_claimed_by'}{$aBusiness|user}
		{/if}
	</div>
{elseif $aBusiness.type == 'claiming' && (int)$aBusiness.business_status == 1 }
<div id="yndirectory_business_detail_detailclaimingbutton" class="yndirectory_business_detail_detailclaimingbutton">
    <span id="yndirectory_loading" style="display: none;">{img theme='ajax/add.gif'}</span>
	<button 
		data-detailclaimingbuttonbusinessid="{$aBusiness.business_id}"
		id="yndirectory_business_detail_detailclaimingbutton_button" class="btn btn-success"><i class="fa fa-check-circle"></i>&nbsp;&nbsp;{phrase var='claim_this_business'}</button>
</div>

{literal}
<script type="text/javascript">
    $Behavior.yndirectory_business_detail_detailclaimingbutton_init = function() {
    	if($('#yndirectory_business_detail_detailclaimingbutton').length > 0){
            $('#yndirectory_business_detail_detailclaimingbutton_button').click(function(){
            	yndirectory.clickClaimBusinessButton(this);
       	    }); 
    	} 
    };        
</script>
{/literal}
{/if}
