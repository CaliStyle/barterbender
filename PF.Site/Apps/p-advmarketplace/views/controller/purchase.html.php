<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="main_break"></div>
{if $bInvoice}

<h3>{phrase var='advancedmarketplace.payment_methods'}</h3>
{module name='api.gateway.form'}

{else}
<div class="info">
	<div class="info_left">
		{phrase var='advancedmarketplace.item_you_re_buying'}:
	</div>
	<div class="info_right">
		{$aListing.title}
	</div>		
</div>
<div class="info">
	<div class="info_left">
		{phrase var='advancedmarketplace.price'}:
	</div>
	<div class="info_right">
		{$aListing.price}
	</div>		
</div>
	
<!-- <div class="separate"></div> -->

<div class="p_4">
	{phrase var='advancedmarketplace.by_clicking_on_the_button_below_you_commit_to_buy_this_item_from_the_seller'}
	<br>
	<div class="" style="margin-top: 10px">
		<form method="post" action="{url link='advancedmarketplace.purchase'}">
			<div><input type="hidden" name="id" value="{$aListing.listing_id}" /></div>
			<div><input type="hidden" name="process" value="1" /></div>			
			<input type="submit" value="{phrase var='advancedmarketplace.commit_to_buy'}" class="button btn-primary btn-sm" />
		</form>
	</div>
</div>
{/if}