<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>
<form method="post" action="{if empty($sCategoryUrl)}{url link=$sParentLink}{else}{url link=''$sParentLink'.'$sCategoryUrl''}{/if}">
	{_p var='fevent.keywords'}:
	<div class="p_4">
		{filter key='keyword'}
	</div>
	
	<div class="p_top_4">
		{_p var='fevent.location'}:
		<div class="p_4">
			{filter key='country'}
			{module name='core.country-child' country_child_filter=true country_child_type='browse'}
		</div>	
	</div>		
	
	<div class="p_top_4">
		{_p var='fevent.city'}:
		<div class="p_4">
			{filter key='city'}
		</div>	
	</div>

	<div class="p_top_4">
		{_p var='fevent.zip_postal_code'}:
		<div class="p_4">
			{filter key='zip'}
		</div>	
	</div>		
	
	<div class="p_top_4">
		{_p var='fevent.sort'}:
		<div class="p_4">
			{filter key='sort'} in {filter key='sort_by'}
		</div>	
	</div>	
	
	<div class="p_top_8">
		<input name="search[submit]" value="{_p var='fevent.submit'}" class="button" type="submit" />
		<input name="search[reset]" value="{_p var='fevent.reset'}" class="button" type="submit" />
	</div>	
</form>