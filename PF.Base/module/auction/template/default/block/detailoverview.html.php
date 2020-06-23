<div class="ynauction-detail-overview">
	{if count($aCustomFields) || count($aAuction.additioninfo)}

		<div class="ynauction_trix_header">
			<span class="section_title"> <i class="fa fa-bookmark"></i> {phrase var='auction_specifications'}</span>
	        <span class="section_toggle">
	            <i class="fa fa-chevron-down"></i>
	        </span>
		</div>
	    <div class="content">
	        {template file='auction.block.custom.view'}
		    {if count($aAuction.additioninfo)}
		    <div class="ynauction-detail-overview-additional">
		    	{if count($aCustomFields)}
			        <div class="subsection_header">
			            {phrase var='additional_information'}
			        </div>
		        {/if}
		        {foreach from=$aAuction.additioninfo key=iAdditionalInfo item=aAdditionalInfo }
		        <div class="ynauction-detail-overview-additional-item">
		            <div class="item_label">
		                <i class="fa fa-stop"></i>
		                <span>{$aAdditionalInfo.usercustomfield_title}</span>
		            </div>
		            <div class="item_value">
		                {$aAdditionalInfo.usercustomfield_content}
		            </div>
		        </div>
		        {/foreach}
		    </div>
		    {/if}
	    </div>     
	{/if}
	
    {if $aAuction.description != ''}
	<div class="ynauction_trix_header">
		<span class="section_title"> <i class="fa fa-list"></i> {phrase var='auction_description'}</span>
        <span class="section_toggle">
            <i class="fa fa-chevron-down"></i>
        </span>
	</div>
    <div class="content">
        <div class="ynauction-detail-overview-item">
    		<div class="ynauction-description item_view_content">
    			{$aAuction.description|parse}
    		</div>
    	</div>
    </div> 	
	{/if}
</div>

{literal}
<script type="text/javascript">
$Behavior.countryIsoChangeAddNewAddress = function()
{
	$(".section_toggle").click(function(e) {
		    var parent = $(this).parents('div.ynauction_trix_header');
	        var content = parent.next( ); 
			var icon = $(this).children().first();
			 if ( icon.hasClass('fa-chevron-down') ) {
                icon.removeClass('fa-chevron-down');
                icon.addClass('fa-chevron-up');
            } else {
                icon.removeClass('fa-chevron-up');
                icon.addClass('fa-chevron-down');
            }
	        content.slideToggle();
	});
};
</script>
{/literal}