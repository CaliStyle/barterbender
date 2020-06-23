<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form onsubmit="$(this).ajaxCall('videochannel.viewUpdate'); return false;" action="#" method="post" class="form_editvideo">
    <div><input type="hidden" value="true" name="val[is_inline]"></div>
    <div id="js_video_edit_form">
        <div><input type="hidden"
            value="{$aForms.video_id}"
        name="val[video_id]"></div>
        <div>
            <input type="hidden" value="{$aForms.user_name}" name="val[user_name]"></div>
            <div><input type="hidden" value="1" name="val[is_instant_edit]"></div>

            <div class="table form-group">
                <div class="table_left">
                    <label for="">
                        {required}{phrase var='videochannel.video_title'}:
                    </label>
                </div>
                <div class="table_right">
                    <input type="text" name="val[title]" value="{value type='input' id='title'}" class="form-control" id="js_video_title" maxlength="200" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="">
                        {phrase var='videochannel.description'}:
                    </label>
                </div>
                <div class="table_right">
                    <textarea cols="40"
                    rows="10"
                    name="val[text]"
                    class="form-control js_edit_video_form"
                    >{value id='text' type='textarea'}</textarea>
                </div>
            </div>
            <div id="js_custom_privacy_input_holder">
            </div>
        </div>
        <div class="">
                <input type="submit" class="btn btn-sm btn-success" value="{phrase var='videochannel.update'}">
                &nbsp;
                <a class="btn btn-sm btn-info" onclick="$('.js_box_close a').trigger('click');" id="js_video_go_advanced" href="{$edit_url}">{phrase var='videochannel.go_advanced_uppercase'}</a>
                &nbsp;
                <a class="btn btn-sm btn-danger" onclick="$('.js_box_close a').trigger('click'); return false;" href="#">{phrase var='videochannel.cancel_uppercase'}</a>
            <div class="clear"></div>
        </div>
    </form>