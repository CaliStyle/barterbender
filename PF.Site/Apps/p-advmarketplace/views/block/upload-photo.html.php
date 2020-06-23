<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="js_advancedmarketplace_form_holder">
    {if $iTotalImage < $iTotalImageLimit}
        {module name='core.upload-form' type='advancedmarketplace' params=$aParamsUpload}
        {if !$isCreating}
            <div class="advancedmarketplace-module cancel-upload">
                <a href="{permalink module='advancedmarketplace/detail' id=$iListingId title=$aForms.title}" style="float:right;" id="js_listing_done_upload" class="btn btn-primary">
                    <i class="ico ico-check"></i>&nbsp;{_p var='finish_upload'}
                </a>
            </div>
        {/if}
    {else}
        <p>{_p var='you_cannot_add_more_image_to_your_listing'}</p>
    {/if}
</div>

<input type="hidden" id="js_p_advmarketplace_total_photos" value="{$iTotalImage}">