<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>

{if count($aImages) > 1}
<div class="js_box_thumbs_holder2">
{/if}
    <div class="fevent_image_holder">
        <div class="fevent_image">
            {if $aEvent.image_path != ''}
                {img server_id=$aEvent.server_id title=$aEvent.title path='event.url_image' file=$aEvent.image_path}
            {else}
                <img src="{$sDefaultPhoto}" />
            {/if}
        </div>

        {if count($aImages) >= 1 && $aEvent.image_path != ''}
        <div class="fevent_image_extra js_box_image_holder_thumbs">
            <ul>
                {foreach from=$aImages name=images key=iKey item=aImage}
                <li class="{if $iKey > 7}fevent_image_hide hide{/if}" ><a href="{img server_id=$aImage.server_id title=$aEvent.title path='event.url_image' file=$aImage.image_path return_url = true}" class="thickbox">
                    {img server_id=$aImage.server_id title=$aEvent.title path='event.url_image' file=$aImage.image_path suffix='' width='50' height='50'}
                </a></li>
                {/foreach}
            </ul>
            <div class="clear"></div>
        </div>
            {if count($aImages) > 6}
            <div class="fevent_image_extra_button">
                <div class="t_center">
                    <button id="fevent_more" class="btn btn-sm btn-info">{_p('View more')}</button>
                    <button id="fevent_less" class="btn btn-sm btn-info hide">{_p('View less')}</button>
                </div>
            </div>
            {/if}
        {/if}
    </div>
{if count($aImages) > 1}
</div>
{/if}
{literal}
<script type="text/javascript">
    $Behavior.onFEOnLoadImages = function(){
        $('#fevent_more').on('click',function(){
           $(this).addClass('hide');
           $('#fevent_less').removeClass('hide');
           $('.fevent_image_extra').find('.fevent_image_hide').removeClass('hide');
        });
        $('#fevent_less').on('click',function(){
           $(this).addClass('hide');
           $('#fevent_more').removeClass('hide');
           $('.fevent_image_extra').find('.fevent_image_hide').addClass('hide');
        });
    }
</script>
{/literal}