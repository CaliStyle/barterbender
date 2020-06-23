{literal}
	<style>
		input:focus, select:focus, textarea:focus{
			border: none;
			-moz-box-shadow:none;
			-webkit-box-shadow: none;
			box-shadow: none;
		}
	</style>
{/literal}

<form method="post" action="{url link='admincp.donation.addbuttonimage'}" onsubmit="return startProcess(true, false);" enctype="multipart/form-data">
	<div id="js_donation_block_photo_holder">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label for="">{phrase var='donation.donation_default_image'}:</label>
                    <div>
                        <img src='{$sDefaultImagePath}'/>
                    </div>
                    <div style="height:20px;">
                        <span style="float:right;">
                            <input type="checkbox" name="default_use_as_default" id="default_use_as_default" {if $bIsUsingDefaultButtonImage} checked='true'{/if}/>
                        </span>
                        <span style="float:right;margin-right:4px;line-height: 20px;">{phrase var='donation.use_this_image_as_default'}</span>
                    </div>
                    </br>
                </div>
            </div>
        </div>
	</div>
	<div class="message" style="display:none;"></div>
	<div class="error_message" style="display:none;"></div>
	
	<input type='hidden' name='aVals[bIsAddForm]' value='1'/>
	<div id="js_donation_block_photo_holder">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label for="">{phrase var='donation.donation_image'}:</label>
                    {if $sAdminImagePath}
                    <img src='{$sAdminImagePath}'/>
                    {/if}
			    </div>

                <div class="form-group">
                    <input type="file" name="image" class="js_uploader_files_input form-control" size="30" >
                    <div id="js_video_upload_image">
                        <div id="js_progress_uploader"></div>
                        <div class="extra_info">
                            {phrase var='donation.you_can_upload_a_jpg_gif_or_png_file'}
                            {if $iMaxFileSize !== null}
                            {phrase var='donation.the_file_size_limit_is' iMaxFileSize_filesize=$iMaxFileSize_filesize}
                            {/if}
                            <br/>{phrase var='donation.recommended_image_size_width_x_height_pixels' width=260 height=80}
                        </div>
                    </div>

                    <div style="height:20px;">
                        <span style="float:right;"><input type="checkbox" name="custom_use_as_default" id="custom_use_as_default"
                        {if !$bIsUsingDefaultButtonImage} checked='true'{/if}/></span>
                        <span style="float:right;margin-right:4px;line-height: 20px;">{phrase var='donation.use_this_image_as_default'}</span>
                    </div>
                </div>
            </div>
		
            <div id="js_submit_upload_image" class="panel-footer">
                <input type="submit" value="{phrase var='donation.save'}" class="btn btn-primary" />
            </div>
        </div>
	</div>
	
</form>

{literal}
<script type="text/javascript" language="javascript">
    $Behavior.DonationAddImageButton = function() {
        $('input[name=default_use_as_default]').change(function() {
            var changedValue = !$('input[name=custom_use_as_default]').is(':checked');
            $('input[name=custom_use_as_default]').attr('checked', changedValue );
        });

        $('input[name=custom_use_as_default]').change(function() {
            $('input[name=default_use_as_default]').attr('checked', !$('input[name=default_use_as_default]').is(':checked'));
        });        
    }
</script>
{/literal}
		
		