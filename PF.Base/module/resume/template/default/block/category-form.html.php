<label for="js_category{$aItem.category_id}" id="js_category_label{$aItem.category_id}">
	<input value="{$aItem.category_id}" {if in_array($aItem.category_id,$aItemData)}checked= true{/if} type="checkbox" name="val[category][]" id="js_category{$aItem.category_id}" class="checkbox" /> {softPhrase var=$aItem.name}
</label>
