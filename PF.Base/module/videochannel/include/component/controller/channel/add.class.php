<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Channel_Add extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);

        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);

        $aInPage = Phpfox::getService('videochannel')->getIsInPageModule($sModule, $iItem, $this->request()->get('val'));
        $bCanAddChannelInPage = false;
        if (isset($aInPage['module_id']))
        {
            $sModule = $aInPage['module_id'];
            $iItem = $aInPage['item_id'];
            if (((Phpfox::getService('videochannel')->isPageOwner($aInPage['item_id']) || (Phpfox::getUserBy('profile_page_id') == $aInPage['item_id'])) && Phpfox::getUserParam('videochannel.can_add_channel_on_page', true)) || Phpfox::isAdmin())
            {
                $bCanAddChannelInPage = true;
            }
            else
            {
                $this->url()->send('videochannel');
            }
        }
        else
        {
            Phpfox::getUserParam('videochannel.can_add_channels', true);
        }

        $aCallback = false;
        if ($sModule !== false && $iItem !== false && Phpfox::hasCallback($sModule, 'getVideoDetails'))
        {
            if (($aCallback = Phpfox::callback($sModule . '.getVideoDetails', array('item_id' => $iItem))))
            {
                $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
            }
        }

        $iChannelCount = Phpfox::getService('videochannel.channel.process')->channelsCount(Phpfox::getUserId());

        if ($iChannelCount >= Phpfox::getUserParam('videochannel.channels_limit'))
        {
            Phpfox_Error::set(_p('videochannel.added_channels_already_reached_the_limit'));
            $this->template()->assign('bIsLimited', true);
            return;
        }
        //Added channels already reached the limit.
        //set url when currently in page
        if (($sModule == 'pages') && $iItem != false)
        {

            $sSubmitUrl = Phpfox::getLib('url')->makeUrl('videochannel.channel.add', array('module' => 'pages', 'item' => $iItem));
        }
        else
        {
            //if not in page
            $sSubmitUrl = $this->url()->makeUrl('videochannel.channel.add');
        }
        $sKeyword = "";
        if (isset($_SESSION['keyword']))
            $sKeyword = $_SESSION['keyword'];

        //Search channels
        if (isset($_POST['find_channels']) || isset($_POST['next_channels']) || isset($_POST['prev_channels']))
        {
            $aVals = $this->request()->getArray('val');

            if (isset($aVals['keyword']) && $aVals['keyword'] != "")
            {
                $sKeyword = Phpfox::getLib('parse.input')->clean(preg_replace('/\'/', "", $aVals['keyword']));
                $_SESSION['keyword'] = $sKeyword;
            }

            $sTitle = $sKeyword; //Phpfox::getLib('parse.input')->clean(preg_replace('/\'/',"",$sKeyword));		   	

            if ($sTitle != "")
            {
                $sQuery = urlencode($sTitle); //Set search query

                $iMaxResult = Phpfox::getUserParam('videochannel.channel_search_results'); //Set max result per page
                if ($iMaxResult > 50)
                    $iMaxResult = 50;
                //Set start index
                if (isset($_POST['find_channels']))
                    $sPageToken = '';
                else
                    $sPageToken = isset($aVals['sPageToken']) ? $aVals['sPageToken'] : '';

                if (isset($_POST['next_channels']))
                {
                    $sPageToken = $_POST['next_channels'];
                }
                else if (isset($_POST['prev_channels']))
                {
                    $sPageToken = $_POST['prev_channels'];
                }

                //Set key
                $api_key = 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M';
				
               //Generate feed URL
                $sFeedUrl = 'https://www.googleapis.com/youtube/v3/search?order=relevance&part=snippet&type=channel&q='.$sQuery.'&key=' . $api_key . '&pageToken='
                        . $sPageToken . '&maxResults=' . $iMaxResult;

                //Find channels  search channels
                $aChannels = Phpfox::getService('videochannel.channel.process')->getChannels($sFeedUrl, $sPageTokenPrev, $sPageTokenNext, $sModule, $iItem);

                $aChannels = array_reverse($aChannels);
                $this->template()->assign(
                    array(
                        'aChannels' => $aChannels,
                        'sPageToken' => $sPageToken,
                        'sPageTokenPrev' => $sPageTokenPrev,
                        'sPageTokenNext' => $sPageTokenNext));
            }
        }
        else
        {
            $sKeyword = "";
        }

        //End Search channels
        $this->template()->assign(array(
            'sSubmitUrl' => $sSubmitUrl,
            'sKeyword' => $sKeyword,
            'sModule' => ($sModule) ? ($sModule) : 'videochannel',
            'iItem' => ($iItem) ? ($iItem) : 0,
            'bCanAddChannelInPage' => $bCanAddChannelInPage
        ));

        $this->template()->setTitle(_p('videochannel.add_a_channel'))
                ->setBreadcrumb(_p('videochannel.videochannel'), ($aCallback === false ? $this->url()->makeUrl('videochannel') : $aCallback['url_home'] . "videochannel/"))
                ->setBreadcrumb(_p('videochannel.add_a_channel'), ($aCallback === false ? $this->url()->makeUrl('videochannel.channel.add') : $this->url()->makeUrl('videochannel.channel.add', array('module' => $sModule, 'item' => $iItem))), true)
                ->setHeader('cache', array(
                    'channel.js' => 'module_videochannel',
                    'videochannel.js' => 'module_videochannel'
                        )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_channel_add_clean')) ? eval($sPlugin) : false);
    }

}

?>
