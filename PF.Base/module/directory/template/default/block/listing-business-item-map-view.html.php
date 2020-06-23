<?php

defined('PHPFOX') or exit('NO DICE!');

 ?>
{foreach from=$aBusinessMap item=aBusiness name=business key=iKey}

<input type="hidden" name="mapview_title[{$iKey}][]" value="{$aBusiness.name|clean|shorten:75:'...'|split:10}" >
<input type="hidden" name="mapview_location[{$iKey}][]" value="{$aBusiness.location_title}">
<input type="hidden" name="mapview_rating[{$iKey}][]" value="{$aBusiness.total_rating}">
<input type="hidden" name="mapview_reviews[{$iKey}][]" value="{$aBusiness.total_reviews}">
<input type="hidden" name="mapview_featured[{$iKey}][]" value="{$aBusiness.featured}">
<input type="hidden" name="mapview_lat[{$iKey}]" value="{$aBusiness.location_latitude}">
<input type="hidden" name="mapview_long[{$iKey}]" value="{$aBusiness.location_longitude}">

	{*if many business belongs to 1 location*}
	{if isset($aBusiness.same) }
		{foreach from=$aBusiness.same item=aBusinesssame key=iKeySame}
		<input type="hidden" name="mapview_title[{$iKey}][]" value="{$aBusinesssame.name|clean|shorten:75:'...'|split:10}" >
		<input type="hidden" name="mapview_location[{$iKey}][]" value="{$aBusinesssame.location_title}">
		<input type="hidden" name="mapview_rating[{$iKey}][]" value="{$aBusinesssame.total_rating}">
		<input type="hidden" name="mapview_reviews[{$iKey}][]" value="{$aBusinesssame.total_reviews}">
		<input type="hidden" name="mapview_featured[{$iKey}][]" value="{$aBusinesssame.featured}">
		{/foreach}
	{/if}
{/foreach}
