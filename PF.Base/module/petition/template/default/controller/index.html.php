<?php


defined('PHPFOX') or exit('NO DICE!');

?>
{if $bInHomepage && !empty($aFeatured)}
	{module name='petition.featured'}
{/if}

{if !count($aItems)}
<div class="extra_info">
	{phrase var='petition.no_petitions_found'}
</div>
{else}

{foreach from=$aItems name=petition item=aItem}
	{template file='petition.block.entry'}
{/foreach}

{if Phpfox::getUserParam('petition.can_approve_petitions') || Phpfox::getUserParam('petition.delete_user_petition')}
{moderation}
{/if}

{pager}
{/if}
