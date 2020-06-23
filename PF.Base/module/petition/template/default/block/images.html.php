<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<script type="text/javascript">
   $Behavior.marketplaceShowImage = function(){
         $('.js_petition_click_image').click(function(){
               var oNewImage = new Image();
               oNewImage.onload = function(){
                     $('#js_marketplace_click_image_viewer').show();
                     $('#js_marketplace_click_image_viewer_inner').html('<img src="' + this.src + '" alt="" />');
                     $('#js_marketplace_click_image_viewer_close').show();
               };
               oNewImage.src = $(this).attr('href');

               return false;
         });

         $('#js_marketplace_click_image_viewer_close a').click(function(){
               $('#js_marketplace_click_image_viewer').hide();
               return false;
         });
   }
</script>
{/literal}
<div id="js_marketplace_click_image_viewer" style="">
	<div id="js_marketplace_click_image_viewer_inner">
		{phrase var='petition.loading'}
	</div>
	<div id="js_marketplace_click_image_viewer_close">
		<a href="#">{phrase var='petition.close'}</a>
	</div>
</div>
{if $aItem.petition_status != 2}
<div class="petition_large_image">
    {if $aItem.petition_status == 3}
        <div class="petition_victory">{phrase var='petition.victory'}</div>
    {else if $aItem.petition_status == 1}
        <div class="petition_closed">{phrase var='petition.closed'}</div>
    {/if}
{else}
<div class="petition_large_image"> 
	      
{/if} 
 
{if $aItem.image_path}
<a class="js_petition_click_image no_ajax_link"
   href='{img return_url=true server_id=$aItem.server_id title=$aItem.title path='core.url_pic' file=$aItem.image_path suffix=''}'>
    {img server_id=$aItem.server_id title=$aItem.title path='core.url_pic'
    file=$aItem.image_path suffix='_500'}  
</a> 
{else}
    <img src="{$corepath}module/petition/static/image/no_photo.png" />

{/if}



</div>
{if count($aImages) > 1}
<div class="petition_small_image">
    <ul>
        {foreach from=$aImages name=images item=aImage}
            <li><a class="js_petition_click_image no_ajax_link"
                   href='{img return_url=true
                   server_id=$aItem.server_id title=$aItem.title path='core.url_pic' file=$aImage.image_path suffix=''}'>

                    {img server_id=$aImage.server_id title=$aItem.title path='core.url_pic' file=$aImage.image_path suffix='_300' max_width='50' max_height='50'}</a>
            </li>
        {/foreach}
    </ul>
    <div class="clear"></div>
</div>
{/if}

