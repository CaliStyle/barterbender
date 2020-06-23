<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="global_attachment_holder_section" id="global_attachment_videochannel">	
	<div><input type="hidden" name="val[video_inline]" value="1" /></div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='videochannel.title'}:</label>
                <input type="text" name="val[video_title]" style="width:90%;" id="js_form_videochannel_title" class="form-control"/>
            </div>

            <div class="form-group">
                <label>{phrase var='videochannel.video'}:</label>
                <div><input type="file" name="video" id="global_attachment_videochannel_file_input" value="" onchange="$bButtonSubmitActive = true; $('.activity_feed_form_button .button').removeClass('button_not_active'); $Core.resetActivityFeedErrorMessage();" /></div>
                <div class="extra_info">
                    {phrase var='videochannel.select_a_video_to_attach'}
                </div>
            </div>
        </div>
    </div>
</div>
{literal}
<script type="text/javascript">
$ActivityFeedCompleted.resetVideoForm = function()
{
	$('#js_form_videochannel_title').val('');
	$('#global_attachment_videochannel_file_input').val('');
}
</script>
{/literal}
