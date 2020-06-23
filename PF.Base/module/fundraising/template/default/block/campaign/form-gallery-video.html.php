<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{if Phpfox::getUserParam('fundraising.can_upload_video')}
<h3>{phrase var='videos'}</h3>
{if $aVideo}
<div class="form-group-follow mb-2">
    {$aVideo.embed_code}
</div>
{/if}
<div class="form-group">
    <label for="fundraising_video_url">{_p var='video_url'}:</label>
    <input id="fundraising_video_url" type="url" class="form-control" name="val[video_url]" size="100" >
    <div class="extra_info">
        {phrase var='please_enter_a_youtube_video_url'}
    </div>
</div>
{/if}