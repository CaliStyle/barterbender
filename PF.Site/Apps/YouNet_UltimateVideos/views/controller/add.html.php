<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if isset($sError) && !empty($sError)}
<div>{$sError}</div>
{elseif !isset($bIsSpam) || !$bIsSpam}
{$sCreateJs}

<div id="js_ultimatevideo_block_detail" data-addjs="{$corePath}/assets/jscript/add.js"
     data-validjs="{$corePath}/assets/jscript/jquery.validate.js">
    <form method="post" enctype="multipart/form-data" action="{url link='current'}" id="ynuv_add_video_form"
          class="ultimatevideo-form-add">
        <div id="js_custom_privacy_input_holder">
            {if $bIsEdit && (!isset($sModule) || empty($sModule))}
                {module name='privacy.build' privacy_item_id=$aForms.video_id privacy_module_id='ultimatevideo'}
            {/if}
        </div>
        {if $bIsEdit}<input type="hidden" id="ynuv_videoid" name="val[video_id]" value="{$aForms.video_id}">{/if}
        {if $bIsEdit && $aForms.type == 3 && setting('ynuv_allow_user_upload_video_to_yt')}
            <input type="checkbox" name="val[allow_upload_channel]" id='allow_upload_channel' }
                   {if $aForms.allow_upload_channel}checked{/if}>
            {_p('also_upload_this_video_to_youtube')}
        {/if}
        {if !$bIsEdit}
            {if Phpfox::getParam('ultimatevideo.ynuv_enable_uploading_of_videos')}
                <input type="hidden" name="val[video_type]" id="ynuv_add_video_type"
                       value="{value type='hidden' id='video_type'}"/>
            {else}
                <input type="hidden" name="val[video_type]" id="ynuv_add_video_type"
                       value="url"/>
            {/if}
            <input type="hidden" name="val[video_source]" id="ynuv_add_video_source"
                   value="{value type='hidden' id='video_source'}"/>
            {if Phpfox::getParam('ultimatevideo.ynuv_enable_uploading_of_videos')}
            <div class="p-tab-nav-outer">
                <ul class="p-tab-nav" id="myTab" role="tablist">
                    <li class="p-tab-item active">
                        <a class="p-tab-link " id="upload-tab" data-toggle="tab" href="#upload" role="tab"
                           aria-controls="upload" aria-selected="true">{_p var='upload_a_video'}</a>
                    </li>
                    <li class="p-tab-item">
                        <a class="p-tab-link" id="url-tab" data-toggle="tab" href="#url" role="tab" aria-controls="url"
                           aria-selected="false">{_p var='video_url'}</a>
                    </li>
                </ul>
            </div>
            {/if}
            <div class="p-tab-content tab-content" id="myTabContent">
                {if Phpfox::getParam('ultimatevideo.ynuv_enable_uploading_of_videos')}
                <div class="p-tab-panel tab-pane fade active in" id="upload" role="tabpanel"
                     aria-labelledby="upload-tab">
                    <div class="pf_select_video">
                        {module name='core.upload-form' type='ultimatevideo_video'}
                        <span class="extra_info hide_it">
                        <a href="javascript:void(0);" class="pf_v_upload_cancel button btn-sm">{_p('Cancel')}</a>
                    </span>
                    </div>
                    {if setting('ynuv_allow_user_upload_video_to_yt')}
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="val[allow_upload_channel]"/>
                                {_p('also_upload_this_video_to_youtube')}
                            </label>
                        </div>
                    {/if}
                    <span class="help-block">{_p('please_wait_while_your_video_is_being_uploaded_when_your_upload_finish_your_video_will_be_processed_you_will_be_notified_when_it_is_ready_to_be_viewed')}</span>
                </div>
                {/if}
                <div class="p-tab-panel tab-pane fade{if !Phpfox::getParam('ultimatevideo.ynuv_enable_uploading_of_videos')} active in{/if}" id="url" role="tabpanel" aria-labelledby="url-tab">
                    <input type="hidden" name="val[video_code]" id="ynuv_add_video_code"
                           value="{value type='hidden' id='video_code'}"/>
                    <input type="hidden" name="val[video_url]" id="ynuv_add_video_url"
                           value="{value type='hidden' id='video_url'}"/>
                    <div>
                        <label>{_p('url')} <span class="p-text-danger">{required}</span></label>
                        <input type="text" name="val[video_link]" value="{value type='input' id='video_link'}"
                               id="ynuv_add_video_input_link"/>
                        <span class="help-block" id="ynuv_help_block_link"
                              style="display:none">{_p('paste_the_web_address_of_the_video_here')}</span>
                        <span class="help-block" id="ynuv_help_block_url"
                              style="display:none">{_p('paste_the_web_address_of_the_video_here_only_support_mp4_video_when_uploading_video_via_url')}</span>
                    </div>
                    <div class="ynuv_error error_message" id="ynuv_add_error_link" style="display:none"></div>
                </div>
            </div>
            <div class="js_ultimatevideo_block page_section_menu_holder" id="js_ultimatevideo_block_url"
                 {if !empty($sActiveTab) && $sActiveTab != 'url'}style="display:none;"{/if}>

            </div>
            <div class="js_ultimatevideo_block page_section_menu_holder" id="js_ultimatevideo_block_upload"
                 {if !empty($sActiveTab) && $sActiveTab != 'upload'}style="display:none;"{/if}>

            </div>
            <div class="ynuv_processing message" id="ynuv_add_processing"
                 style="display:none">{_p('checking_url_three_dot')}</div>
            <div class="ynuv_processing message" id="ynuv_add_processing_embed"
                 style="display:none">{_p('checking_embed_code_three_dot')}</div>
            <div class="ynuv_error error_message" id="ynuv_add_error_embed"
                 style="display:none">{_p('we_could_not_find_a_video_there_please_check_the_embed_code_and_try_again')}</div>
        {/if}
        {if $bIsEdit}
            {if !empty($aForms.image_path)}
                {module name='core.upload-form' type='ultimatevideo' current_photo=$aForms.current_image id=$aForms.video_id}
                <input type="hidden" name="val[image_path]" value="{value type='input' id='image_path'}">
                <input type="hidden" name="val[image_server_id]" value="{value type='input' id='image_server_id'}">
            {else}
                {module name='core.upload-form' type='ultimatevideo' current_photo=''}
            {/if}
        {/if}
        <div class="">
            <div class="p-flex-wrapper flex-wrap p-mx--1">
                <div class="form-group col-xs-12 col-sm-6 px-1 p-flex-item">
                    <label for="ynuv_add_video_title">{_p('title')} <span class="p-text-danger">{required}</span></label>
                    <input class="form-control" type="text" name="val[title]" id="ynuv_add_video_title" value="{value type='input' id='title'}" placeholder="{_p var='video_title_l'}">
                </div>
                {if !empty($sCategories)}
                    <div class="form-group js_core_init_selectize_form_group col-xs-12 col-sm-6 px-1 p-flex-item">
                        <label for="category">{_p('Category')}</label>
                        <div>
                            {module name='ultimatevideo.add_category_list'}
                        </div>
                    </div>
                {/if}
            </div>
        </div>
        <div class="form-group">
            <label>{_p('description')}</label>
            {editor id='description'}
        </div>
        <div class="form-group">
            <label>{_p('tags')}</label>
            <input type="text" name="val[tag_list]" value="{value type='input' id='tag_list'}" placeholder="{_p('separate_tags_with_commas')}">
        </div>
        <div class="form-group">
            <div id="ynuv_customfield_category">
            </div>
        </div>
        {if empty($sModule) && Phpfox::isModule('privacy')}
            <div class="form-group">
                <label for="text">{_p var='privacy'}</label>
                {module name='privacy.form' privacy_name='privacy' default_privacy='ultimatevideo.default_privacy_setting'}
            </div>
        {/if}
        <div class="p-form-group-btn-container">
            {if !$bIsEdit}
                {if Phpfox::getParam('ultimatevideo.ynuv_enable_uploading_of_videos')}
                <div class="js_ultimatevideo_btn js_ultimatevideo_btn_upload">
                    <input type="submit" value="{_p('Submit')}" name="val[submit]" id="ynuv_add_submit_upload"
                           class="button btn btn-primary" disabled="disabled"
                           {if !empty($sActiveTab) && $sActiveTab != 'uploaded'}style="display:none;"{/if}>
                </div>
                {/if}
                <div class="js_ultimatevideo_btn js_ultimatevideo_btn_url" {if Phpfox::getParam('ultimatevideo.ynuv_enable_uploading_of_videos')}style="display:none;"{/if}>
                    <input type="submit" value="{_p('Submit')}" name="val[submit]" id="ynuv_add_submit"
                           class="button btn btn-primary" disabled="disabled">
                </div>
            {else}
                <input type="submit" value="{_p('Update')}" name="val[submit]" id="ynuv_add_update"
                       class="button btn btn-primary">
            {/if}
        </div>
    </form>
</div>
{/if}
