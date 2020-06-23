{if $aDetailHeaderInfoImages}
<!-- masterslider -->
<div class="master-slider ms-skin-default" id="masterslider">
    {foreach from=$aDetailHeaderInfoImages item=aDetailHeaderInfoImage}
    <div class="ms-slide">
        <img src="masterslider/blank.gif" data-src="{img server_id=$aDetailHeaderInfoImage.server_id path='core.url_pic' file=$aDetailHeaderInfoImage.image_path suffix=''  return_url=true}" alt="lorem ipsum dolor sit"/>

        <img src="{img server_id=$aDetailHeaderInfoImage.server_id path='core.url_pic' file=$aDetailHeaderInfoImage.image_path suffix='_100' class='ms-thumb' return_url=true}" alt="lorem ipsum dolor sit" />

    </div>
    {/foreach}
</div>
<!-- end of masterslider -->
{/if}