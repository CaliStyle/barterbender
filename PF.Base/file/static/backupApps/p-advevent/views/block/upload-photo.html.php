<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="js_event_form_holder">
    {if $iTotalImage < $iTotalImageLimit}
        {module name='core.upload-form' type='fevent' params=$aParamsUpload}
        {if !$isCreating}
            <div class="cancel-upload">
                <a href="{permalink module='fevent' id=$iEventId title=$aForms.title}" style="float:right;" id="js_fevent_done_upload" class="btn btn-primary">
                    <i class="ico ico-check"></i>&nbsp;{_p var='finish_upload'}
                </a>
            </div>
        {/if}
    {else}
        <p>{_p var='you_cannot_add_more_image_to_your_event'}</p>
    {/if}
</div>

<input type="hidden" id="js_p_fevent_total_photos" value="{$iTotalImage}">