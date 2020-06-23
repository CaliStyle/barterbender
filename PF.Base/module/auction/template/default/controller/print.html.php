{$sJs}
{foreach from=$aFiles item=file}
<script type="text/javascript" src="{$file}"></script>
{/foreach}
{literal}
<script type="text/javascript">
	$(document).ready(function() {
		window.onload = function () {
			window.print();
			setTimeout(function(){window.close();}, 1);
		}
	});
</script>
<style type="text/css">
	.ynauction-masterslider,
	.item_bar_action_holder,
	.ynauction_trix_header .section_toggle
	{
		display: none;
	}
	.ynauction-detail-overview
	{
		padding-top: 10px;
	}
</style>
{/literal}
<div id="ynauction_detail" class="main_break">
	{module name='auction.detail-header-info' aYnAuctionDetail=$aYnAuctionDetail}
	{module name='auction.detailoverview' aYnAuctionDetail=$aYnAuctionDetail}
</div>