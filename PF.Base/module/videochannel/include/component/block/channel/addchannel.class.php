<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Channel_Addchannel extends Phpfox_Component
{

    /**
     *
     * @var Videochannel_Service_Channel_Process
     */
    public $oSerVideoChannelChannelProcess;

    /**
     *
     * @var Videochannel_Service_Videochannel
     */
    public $oSerVideoChannel;

    /**
     *
     * @var Phpfox_Parse_Input
     */
    public $oLibParseInput;

    /**
     *
     * @var Phpfox_Parse_Output
     */
    public $oLibParseOutput;

    public function __construct($aParams)
    {
        parent::__construct($aParams);

        $this->oSerVideoChannelChannelProcess = Phpfox::getService('videochannel.channel.process');
        $this->oSerVideoChannel = Phpfox::getService('videochannel');
        $this->oLibParseInput = Phpfox::getLib('parse.input');
        $this->oLibParseOutput = Phpfox::getLib('parse.output');
    }


    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);
        $sModule = $this->request()->get('module');
        $iItem = $this->request()->getInt('item');
        $iChannelId = (int)$this->getParam('iChannelId');
        $iIndex = (int)$this->request()->getInt('id');

        $bCanAddChannelInPage = $this->oSerVideoChannel->getCanAddChannel($sModule, $iItem);

        // Set the default category.
        $iCategoryId = 0;

        if ($iChannelId > 0) {
            // Get the categories.
            $aCategoryChannel = $this->oSerVideoChannelChannelProcess->getCategory($iChannelId);
            // A channel will has a parent category and maybe have a child category.
            // If it only has a parent, just get parent, otherwise get child.
            if (count($aCategoryChannel) == 1) {
                // Get parent category id.
                $iCategoryId = isset($aCategoryChannel[0]['category_id']) ? $aCategoryChannel[0]['category_id'] : 0;
            } else {
                // Get child category id.
                foreach ($aCategoryChannel as $aCategory) {
                    // Is a child.
                    if ($aCategory['parent_id'] != 0) {
                        // Get the child category id. Because max count is 2.
                        $iCategoryId = isset($aCategory['category_id']) ? $aCategory['category_id'] : 0;
                    }
                }

            }

        }

        // Get categories in HTML.
        $sCategoriesInHTML = $this->oSerVideoChannelChannelProcess->getCategoriesInHtml($iCategoryId);

        $act = $this->getParam('act');

        if (!empty($iChannelId) && !empty($act)) //Edit channel
        {
            $aChannel = $this->oSerVideoChannelChannelProcess->getChannel($iChannelId);
            $aChannel['url_encode'] = base64_encode($aChannel['url']);
            if (($aChannel['user_id'] != Phpfox::getUserId()) && !Phpfox::isAdmin()) {
                return Phpfox_Error::display(_p('videochannel.invalid_permissions'));
            }

            $aChannel['aCategories'] = Phpfox::getService('videochannel.category')->getSelectBox(0,1);
            $aChannel['img'] = base64_decode($this->getParam('img'));

            if ($act == 'no') {
                $aVideos = $aChannel['videos'];

                foreach ($aVideos as $iKey => $video) {
                    if (!empty($video['image_path'])) {
                        $aVideos[$iKey]['image_path'] = Phpfox::getParam('core.url_pic') . str_replace('\\', '/', str_replace('%s', '_120', $video['image_path']));
                    } else {
                        $aVideos[$iKey]['image_path'] = Phpfox::getParam('core.path_file') . 'module/videochannel/static/image/no_item.jpg';
                    }

                    $aVideos[$iKey]['url'] = Phpfox::permalink('videochannel', $aVideos[$iKey]['video_id'], $aVideos[$iKey]['title']);
                }

                //Limit videos per page
                $iLimit = 6;
                $this->template()->assign('aVideos', $aVideos);
                $this->template()->assign('iVideoCount', count($aVideos));
                $this->template()->assign('iLimit', $iLimit);
            }
        } else {
            $aChannel = array(
                'channel_id' => $iChannelId,
                'site_id' => $this->getParam('site_id'),
                'url' => base64_decode($this->getParam('url')),
                'url_encode' => $this->getParam('url'),
                'title' => (str_replace('number_sign', '#', base64_decode($this->getParam('title')))),

                'summary' => (str_replace('number_sign', '#', base64_decode($this->getParam('description')))),
                'img' => base64_decode($this->getParam('img')),
                'aCategories' => Phpfox::getService('videochannel.category')->getSelectBox(0,1)
            );

        }

        $this->template()->assign(array(
                'aForms' => $aChannel,
                'act' => $act,
                'sModule' => ($sModule) ? $sModule : 'videochannel',
                'iItem' => ($iItem) ? $iItem : 0,
                'iIndex' => $iIndex,
                'no_image' => Phpfox::getParam('core.path_file') . 'module/videochannel/static/image/no_item.jpg',
                'sCategories' => ($iCategoryId ? implode(',',array_reverse(explode(',',Phpfox::getService('videochannel.category')->getParentCategories($iCategoryId)))) : '0')
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_block_channel_addchannel_clean')) ? eval($sPlugin) : false);
    }

}

?>