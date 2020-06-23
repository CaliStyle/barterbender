<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Channel_Index extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);
        // Get the params.
        $oServiceVideoBrowse = Phpfox::getService('videochannel.browse');
        $aParentModule = $this->getParam('aParentModule');
        $iChannelId = $this->request()->getInt('req3');
        // Check the channel.
        $aChannel = Phpfox::getService('videochannel')->getChannelInfo($iChannelId);
        if (!isset($aChannel['channel_id']))
        {
            return Phpfox_Error::display(_p('videochannel.no_channels_found'));
        }
        // Check privacy.
        if (Phpfox::isModule('privacy'))
        {
            Phpfox::getService('privacy')->check('channel_channel', $aChannel['channel_id'], $aChannel['user_id'], $aChannel['privacy']);
        }
        // Get the callback.
        $sModule = $aChannel['module_id'];
        $sItem = $aChannel['item_id'];
        $aCallback = null;
        if ($sModule == 'pages')
        {
            $aCallback = Phpfox::callback($sModule . '.getVideoDetails', array('item_id' => $sItem));
        }
        // Get the channel.
        $aChannel = Phpfox::getService('videochannel.channel.process')->getChannel($iChannelId);
        $sChannelTitle = Phpfox::getLib('parse.output')->shorten(isset($aChannel['title']) ? $aChannel['title'] : _p('videochannel.videochannel'), 100, "...");
        // Set config for search.
        $this->search()->set(array(
            'type' => 'channel_video',
            'field' => 'm.video_id',
            'search_tool' => array(
                'table_alias' => 'm',
                'search' => array(
                    'action' => (defined('PHPFOX_IS_PAGES_VIEW') ? $aParentModule['url'] . 'videochannel/channel/' . $iChannelId : $this->url()->makeUrl('videochannel.channel', $iChannelId)),
                    'default_value' => _p('videochannel.search_videos'),
                    'name' => 'search',
                    'field' => 'm.title'
                ),
                'sort' => array(
                    'latest' => array('m.time_stamp', _p('videochannel.latest')),
                    'popular' => array('m.total_score', _p('videochannel.popular')),
                    'featured' => array('is_featured', _p('videochannel.featured')),
                    'most-viewed' => array('m.total_view', _p('videochannel.most_viewed')),
                    'most-liked' => array('m.total_like', _p('videochannel.most_liked')),
                    'most-talked' => array('m.total_comment', _p('videochannel.most_discussed'))
                ),
                'show' => array(12, 15, 18, 21)
            )
                )
        );
        $aBrowseParams = array(
            'module_id' => 'videochannel',
            'alias' => 'm',
            'field' => 'video_id',
            'table' => Phpfox::getT('channel_video'),
            'hide_view' => array('pending', 'my')
        );
        $oServiceVideoBrowse->channel($iChannelId);
        // Excute the search.
        $this->search()->browse()->params($aBrowseParams)->execute();
        // Is profile, set the description.
        if (defined('PHPFOX_IS_USER_PROFILE'))
        {
            $this->template()->setMeta('description', _p('videochannel.full_name_s_videos_full_name_has_total_video_s', array('full_name' => $aUser['full_name'], 'total' => $this->search()->browse()->getCount())));
        }
        // Set the javascript.
        $sJs = '';
        $bCanUploadVideo = (int) Phpfox::getUserParam('videochannel.can_upload_videos');
        if (!$bCanUploadVideo)
        {
            $sJs .= "<script type='text/javascript'>
                    \$Behavior.VideoChannelRemoveMenuSection1 = function() {
                        \$('#section_menu ul li').first().remove();
                    }
                </script>";
        }
        $bCanAddChannel = (int) Phpfox::getUserParam('videochannel.can_add_channels');
        if (!$bCanAddChannel)
        {
            $sJs .= "<script type='text/javascript'>
                    \$Behavior.VideoChannelRemoveMenuSection2 = function() {
                        \$('#section_menu ul li').last().remove();
                    }
                </script>";
        }
        $this->template()
                ->assign('sJs', $sJs)
                ->assign('aVideos', $this->search()->browse()->getRows())
                ->assign('iPage', $this->search()->getPage())
                ->setTitle(_p('videochannel.videochannel'))
                ->setMeta('keywords', Phpfox::getParam('videochannel.video_meta_keywords'))
                ->setMeta('description', Phpfox::getParam('videochannel.video_meta_description'))
                ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'videochannel.js' => 'module_videochannel',
                    'channel.js' => 'module_videochannel',
                        )
        );
        // Set the breadcrumb.
        if ($aCallback != null)
        {
            $this->template()
                    ->setBreadcrumb($aCallback['breadcrumb_title'],
                        $aCallback['breadcrumb_home'])
                    ->setBreadcrumb($aCallback['title'], $aCallback['url_home'])
                    ->setBreadcrumb(_p('videochannel.channels'),
                        $aCallback['url_home'] . 'videochannel/view_channels');
        }
        else
        {
           /*
            $this->template()->setBreadcrumb(_p('videochannel.all_channels'),
                Phpfox::permalink('videochannel', 'view_all_channels'));
           */
            $this->template()->setBreadcrumb(_p('videochannel.all_channels'),
                Phpfox::permalink('videochannel', 'view_all_channels'));
        }
        // Get the keywords.
        foreach ((array) $this->search()->browse()->getRows() as $aVideo)
        {
            $this->template()->setMeta('keywords', $this->template()->getKeywords($aVideo['title']));
        }

        // Set the breakcrumb.
        $this->template()->setBreadcrumb($sChannelTitle,
            Phpfox::permalink('videochannel.channel', $iChannelId));

        // Set the pagination.
        Phpfox::getLib('pager')->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()));
        // Set the gldbal moderation.
        $this->setParam('global_moderation', array(
            'name' => 'videochannel',
            'ajax' => 'videochannel.moderation',
            'menu' => array(
                array(
                    'phrase' => _p('videochannel.delete'),
                    'action' => 'delete'
                ),
                array(
                    'phrase' => _p('videochannel.approve'),
                    'action' => 'approve'
                )
            )
                )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_channel_index_clean')) ? eval($sPlugin) : false);
    }

}

?>
