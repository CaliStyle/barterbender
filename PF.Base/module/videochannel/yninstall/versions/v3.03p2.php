<?php
function videochannel_install303p2()
{
    $oDb = Phpfox::getLib('phpfox.database');
    
    $oDb->query("DELETE FROM `". Phpfox::getT('setting') ."`
        WHERE var_name IN ('video_enable_mass_uploader','allow_videochannel_uploading','mencoder_path','ffmpeg_path') 
        and product_id = 'younet_videochannel';");
}

videochannel_install303p2();

?>