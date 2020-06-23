<?php
defined('PHPFOX') or exit('NO DICE!');

?>
{if $aItem.type == 1}
    {template file='contest.block.entry.feed.blog'}
{elseif $aItem.type == 2}
    {template file='contest.block.entry.feed.photo'}
{elseif $aItem.type == 3}
    {template file='contest.block.entry.feed.video'}
{elseif $aItem.type == 4}
    {assign var='aSong' value=$aItem}
    {template file='contest.block.entry.feed.music'}
{/if}