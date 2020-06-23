<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form class="form-horizontal" method="post" accept-charset="utf-8"  action="{$sFormUrl}">
<div class="p_bottom_15">
	<div class="p_top_4">
		{_p var='keyword'}:
		<div class="p_4">
			{$aFilters.keyword}
		</div>
	</div>

	<div class="p_top_4">
		{_p var='category'}:
		<div class="p_4">
			{$aFilters.type_cats}
		</div>
	</div>

	<div class="p_top_4">
		{_p var='status'}:
		<div class="p_4">
			{$aFilters.type_status}
		</div>
	</div>

	<div class="p_top_4">
		{_p var='sort_by'}:
		<div class="p_4">
			{$aFilters.sort}
		</div>
	</div>
	<div class="p_top_8 form-group">
        <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-success" />
		<input type="hidden" name="makesearch" value="1"/>
		<input type="submit" name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-success" />	
	</div>
</div>
</form>

