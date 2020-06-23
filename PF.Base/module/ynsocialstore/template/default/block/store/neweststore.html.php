<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if count($aItems)}

<div class="ynstore-view-modes-block yn-viewmode-grid">
	<div class="yn-view-modes">
		<span data-mode="grid" class="yn-view-mode"><i class="ico ico-th"></i></span>
		<span data-mode="list" class="yn-view-mode"><i class="ico ico-list"></i></span>
		<span data-mode="map" class="yn-view-mode"><i class="ico ico-map"></i></span>
	</div>

	<ul class="ynstore-items ynstore-store-listing-block">
		{foreach from=$aItems name=store item=aItem}
			{template file='ynsocialstore.block.store.entry'}
		{/foreach}
	</ul>
	<div id="ynstore_new_store_map" style="width: 100%;height:400px;" ></div>
</div>

{else}
<div class="extra_info">
    {_p var='ynsocialstore.no_stores_found'}
</div>
{/if}

{literal}
<script type="text/javascript">
	$Behavior.initViewMode = function(){
		ynsocialstore.initViewMode('js_block_border_ynsocialstore_store_neweststore');
	}
</script>
{/literal}