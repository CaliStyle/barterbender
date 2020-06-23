<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/11/16
 * Time: 4:59 PM
 */
?>
<div id="js_cover_photo_iframe_loader_error"></div>
<div id="js_cover_photo_iframe_loader_upload" style="display:none;">{img theme='ajax/add.gif' class='v_middle'} {_p var='user.uploading_image'}</div>
<form onsubmit="$('#js_cover_photo_iframe_loader_error').hide(); $('#js_cover_photo_iframe_loader_upload').show(); $('#js_activity_feed_form').hide();" enctype="multipart/form-data" action="#" method="post" target="js_cover_photo_iframe_loader">
    <div><input type="hidden" name="val[action]" value="upload_photo_via_share" /></div>
    <div><input type="hidden" name="val[is_cover_photo]" value="1" /></div>
    {if isset($iStoreId) && !empty($iStoreId)}
    <div>
        <input type="hidden" name="val[store_id]" value="{$iStoreId}" />
    </div>
    {/if}
    <div class="table form-group">
        <div class="table_right">
            <div><input class="ajax_upload" data-url="{url link='ynsocialstore.store.photo' store_id=$iStoreId is_cover_photo=true}" type="file" accept="image/*" name="image" id="global_attachment_photo_file_input" value="" /></div>
        </div>
    </div>
    <div class="table_clear" style="display:none;">
        <div><input type="submit" value="{_p var='user.upload'}" class="button btn-primary"/></div>
    </div>
    <iframe id="js_cover_photo_iframe_loader" name="js_cover_photo_iframe_loader" height="200" width="500" frameborder="1" style="display:none;"></iframe>
</form>
<script>
    $Core.loadInit();
</script>
