<?php
function videochannel_install303p3()
{
    $oDb = Phpfox::getLib('phpfox.database');
    
    $oDb->query("DELETE FROM `". Phpfox::getT('setting') ."`
        WHERE var_name IN ('video_enable_mass_uploader','allow_videochannel_uploading','mencoder_path','ffmpeg_path','flvtool2_path','enable_flvtool2','params_for_mencoder','params_for_ffmpeg','params_for_flvtool2','params_for_mencoder_fallback') 
        and product_id = 'younet_videochannel';");
	
        
    $channels = Phpfox::getLib('database')->select('ch.*')
            ->from(Phpfox::getT('channel_channel'), 'ch')
            ->execute('getRows');

    $api_key = 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M';
    $pattern = "/((http|https):\/\/|)(www\.|)youtube\.com\/(channel|user)\/([a-zA-Z0-9-_]{1,})/";
    
    if(count($channels)){
        foreach ($channels as $key => $channel) {
            if(strpos($channel['url'],'http://gdata.youtube.com') !== false){

                    $aUrlChannel =explode("/", $channel['url']);
                    if(isset($aUrlChannel[6])){

                        $sUser = $aUrlChannel[6];
                        $sUrl = 'https://youtube.com/user/'.$sUser; 

                        $aMatch = array();
                        preg_match($pattern, $sUrl . '?', $aMatch);

                        if(!$aMatch)
                        {
                            continue;
                        }

                        $for = $aMatch[4];
                        $id = $aMatch[5];
                        $sChannelFeedUrl = '';
                        $info = array();

                        if($for == 'user'){

                            $url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&forUsername=$id&key=$api_key";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                            $data = curl_exec($ch);
                            curl_close($ch);
                            $data = json_decode($data);
                            $items = $data -> items;
                            if(count($items))
                            {
                                $info = $items[0] -> snippet;
                                $channelId = $items[0] -> id;
                                $sChannelFeedUrl = "https://www.googleapis.com/youtube/v3/search?key=$api_key&channelId=$channelId&part=snippet&order=date";
                            }
                        }
                        if($sChannelFeedUrl == ''){
                                $sChannelFeedUrl = "https://www.googleapis.com/youtube/v3/search?key=$api_key&channelId=$id&part=snippet&order=date";
                                $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=$id&key=$api_key&maxResults=1";
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                                curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                                $data = curl_exec($ch);
                                curl_close($ch);
                                $data = json_decode($data);
                                $items = $data -> items;
                                if(count($items))
                                {
                                    if(!empty($items[0] -> snippet -> channelTitle))
                                    {
                                        $userName = $items[0] -> snippet -> channelTitle;
                                        $url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&forUsername=$userName&key=$api_key";
                                        $ch = curl_init();
                                        curl_setopt($ch, CURLOPT_URL, $url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                                        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                                        $data = curl_exec($ch);
                                        curl_close($ch);
                                        $data = json_decode($data);
                                        $items = $data -> items;
                                    }
                                    if(count($items))
                                    {
                                        $info = $items[0] -> snippet;
                                    }
                                }
                        }
                        if($sChannelFeedUrl != ''){
                             Phpfox::getLib('database')->update(Phpfox::getT('channel_channel'), array('url' => $sChannelFeedUrl), 'channel_id = ' . $channel['channel_id']);
                        }
                   }
            }
        }
    }
}

videochannel_install303p3();

?>