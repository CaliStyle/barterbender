<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<style type="text/css">
    #swfUploadText {
        border: medium none;
        clear: both;
        color: #FFFFFF;
        cursor: pointer;
        font-size: 8pt;
        font-weight: bold;
        margin: 0;
        overflow: visible;
        padding: 4px;
        vertical-align: middle;
    }
</style>
{/literal}


{if $feedback_id >0}
{if $rest_pictures >0}
<div>{_p var='browse_the_pictures_on_your_computer_and_upload_them_to_your_feedback'}</div>
<br>
<div id="js_upload_form">
    <div class="alert alert-success" id="js_feedback_success_message" style="display: none;">{_p var='photo_s_uploaded_successfully'}</div>
    <div id="js_upload_error_message"></div>
    <div id="js_upload_inner_form">
        <div id="js_feedback_form_holder">
            <div class="table form-group">
                <div class="table_right">
                    {module name='core.upload-form' type='feedback' params=$aFeedBack]}
                </div>
            </div>
        </div>
    </div>

    <div id="js_uploaded_images" style="display:none;">

    </div>
</div>
{else}
<div class="extra_info">{_p var='you_have_reach_limit_uploaded_pictures_for_this_feedback'}</div>
{/if}

<div class="ynf_button_back">
        <span>
            <a href="{url link='feedback.detail.'.$feedback_title}" class="btn btn-warning btn-xs no_ajax">
                {_p var='back_to_this_feedback'}
            </a>
        </span>

    <span>
            <a href="{url link = 'feedback.view_my'}" class="btn btn-warning btn-xs">
                {_p var='back_to_your_feedback'}
            </a>
        </span>
</div>
{/if}