<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 2197 2010-11-22 15:26:08Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<form id="yn_btn_add_to_category" method="post" class="ajax_form">
	<input type="hidden" value="{$iItemId}" name="val[item_id]" />
	<div class="form-group">
        <label for="">{phrase var='foxfeedspro.rss_provider_category'}:</label>
		{$sCategories}
	</div>
	<div class="form-group">
		<button id="btnAddToCategory" onclick="addToCategories(); return false;" class="btn btn-primary form-control">Add</button>
	</div>
</form>
{literal}
<script>
$Behavior.marketplaceAdd = function()
{
	$('.js_mp_parent_holder > select').attr('class','js_mp_category_list form-control');
	$('.js_mp_category_list').change(function()
	{
		var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
		
		$('.js_mp_category_list').each(function()
		{
			if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
			{
				$('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();				
				
				this.value = '';
			}
		});
		$('#js_mp_holder_' + $(this).val()).show();
	});	
}
function addToCategories()
{
	$('#yn_btn_add_to_category').ajaxCall('foxfeedspro.addToCategories');
}
$Core.loadInit();
</script>
{/literal}