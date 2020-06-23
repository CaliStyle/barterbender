<?php

defined('PHPFOX') or exit('NO DICE!');

?>

{if $bInHomepage}
	{if !$aContests && $iPage <= 1}
 	<div>
        {phrase var='contest.no_contest_found'}
    </div>
    {/if}
	{module name='contest.homepage.featured-slideshow'}
	{module name='contest.homepage.ending-soon-contest'}
	{module name='contest.homepage.recent-contest'}
    {module name='contest.homepage.entries'}
{else}

    <div class="wrap_list_items">
        {foreach from=$aContests  name=contest item=aContest}
            {template file='contest.block.contest.listing-item'}
        {foreachelse}
        <div>
            {if $iPage <=1}
                {phrase var='contest.no_contest_found'}
            {/if}
        </div>
        {/foreach}
    </div>
    
    {if $iPage == 0}    
        {if $sView == 'closed' && Phpfox::isAdmin()}
            {moderation}
        {/if}
    {/if}
    {if count($aContests)}
        {pager}
    {/if}
{/if}