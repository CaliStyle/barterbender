<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('video_utilities')}
        </div>
    </div>
    {if !$isError}
        <div class="panel-body">
            <div class="form-group">
                <label for="">{_p('ffmpeg_version')}</label>
                <label>{_p('this_will_display_the_current_installed_version_of_ffmpeg')}</label>
                <textarea class="form-control" rows="10" readonly>{$sVersion}</textarea>
            </div>
            <div class="form-group">
                <label for="">{_p('supported_video_formats')}</label>
                <label for="text">{_p('this_will_run_and_show_the_output_of_ffmpeg_formats_please_see_this_page_for_more_info')}</label>
                <textarea class="form-control" rows="10" readonly>{$sFormat}</textarea>
            </div>
        </div>
    {else}
        <div class="panel-body">
            <div class="form-group">
                <label for="">{_p('ffmpeg_is_not_something_is_wrongured_or_ffmpeg_path_is_not_correct_please_try_again')}</label>
            </div>
        </div>
    {/if}
</div>

