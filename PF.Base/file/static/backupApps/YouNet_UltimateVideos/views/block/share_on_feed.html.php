{if user('ynuv_can_upload_video', '1') == '0'}
    <div class="error_message">
        {_p('your_membership_group_does_not_have_access_to_share_a_video')}
    </div>
{else}
    <div class="uv_process_form hide">
        <span></span>
        <div class="pf_process_bar"></div>
        <div class="extra_info">{_p('pf_video_uploading_message')}</div>
    </div>
    <div class="uv_video_message" style="display:none;">
        <div class="valid_message">{_p('your_video_has_successfully_been_uploaded_we_are_processing_it_and_you_will_be_notified_when_its_ready')}</div>
    </div>
    <div class="ynuv_upload_form" id="ynuv_add_video_form">
        {if Phpfox::getParam('ultimatevideo.ynuv_enable_uploading_of_videos')}
        <div class="pf_select_video">
            {module name='core.upload-form' type='ultimatevideo_video'}
        </div>
        <span class="extra_info hide_it">
            <a href="#" class="pf_v_upload_cancel button btn-sm">{_p('Cancel')}</a>
        </span>
        {/if}
        {if !empty($sModule)}
            <div>
                <input type="hidden" name="val[callback_module]" value="">
                <input type="hidden" name="val[callback_item_id]" value="">
            </div>
        {/if}
        {if Phpfox::getParam('ultimatevideo.ynuv_enable_uploading_of_videos')}
            <div class="pf_v_title feed-attach-form-label">
                {_p('or_insert_a_video_link')}
            </div>
        {else}
            <div class="pf_v_title feed-attach-form-label">
                {_p('insert_a_video_link')}
            </div>
        {/if}
        <br/>
        <div class="table_right form-group">
            <input type="text" name="val[video_link]" class="form-control"
                   value="{value type='input' id='video_link'}" id="ynuv_add_video_input_link"/>
            <input type="hidden" name="val[video_code]" id="ynuv_add_video_code" value=""/>
            <input type="hidden" name="val[video_source]" id="ynuv_add_video_source" value=""/>
        </div>
        <div class="pf_v_video_url">
			<span class="extra_info hide_it">
				<a href="#" class="pf_v_url_cancel">{_p('Cancel')}</a>
			</span>
        </div>
    </div>
{/if}