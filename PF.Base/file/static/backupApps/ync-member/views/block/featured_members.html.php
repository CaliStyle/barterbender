<link href="{$amCorePath}/assets/css/masterslider.css" rel='stylesheet' type='text/css'>
<link href="{$amCorePath}/assets/css/ms-staff-style.css" rel='stylesheet' type='text/css'>

{if !PHPFOX_IS_AJAX}
<div class="ms-staff-carousel">
    <div class="master-slider dont-unbind-children" id="ynmember_feature_slider" data-js="{$amCorePath}/assets/jscript/masterslider/masterslider.min.js">
        {/if}
            {foreach from=$aUsers name=users item=aUser}
                {template file='ynmember.block.entry_featured'}
            {/foreach}
        {if !PHPFOX_IS_AJAX}
    </div>
</div>
{/if}
