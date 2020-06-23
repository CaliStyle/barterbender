<div class="yndirectory-member-list">
	{if count($aFollowers) < 1}
		<div class="help-block">
		{phrase var='no_users_found'}
		</div>
	{/if}
	<div class="yndirectory-member-list-container">
	{foreach from=$aFollowers name=follower item=aFollower}
		<div class="yndirectory-member-item">
			<div class="yndirectory-member-row-image">
				{img user=$aFollower suffix='_50_square' max_width=50 max_height=50}
			</div>
			<div class="item-user">
				{$aFollower|user}
			</div>
		</div>
	{/foreach}
	</div>

	<div class="clear"></div>
	{module name='directory.paging'}	
</div>
{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}