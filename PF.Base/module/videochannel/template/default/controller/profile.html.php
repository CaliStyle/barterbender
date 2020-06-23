<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !($bCanUploadVideo)}
    {literal}
        <style>#section_menu ul li:first-child{display:none;}</style>
    {/literal}
{/if}

{if !($bCanAddChannel)}
    {literal}
        <style>#section_menu ul li:last-child{display:none;}</style>
    {/literal}
{/if}

{template file='videochannel.controller.index'}