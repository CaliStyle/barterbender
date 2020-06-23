<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function addToFavourite()
    {
        Phpfox::isUser(true);
        $iVideoId = $this->get('video_id');
        $sType = $this->get('type');
        $sCorePath = Phpfox::getParam('core.path');
        $aCallback = null;
        $aVideo = Phpfox::getService('videochannel')->getVideoSimple($iVideoId);
        if($aVideo['module_id'] == 'pages')
        {
            if ( Phpfox::hasCallback($aVideo['module_id'], 'convertVideo'))
            {
                $aCallback = Phpfox::callback($aVideo['module_id'] . '.convertVideo', array('item_id' => $iVideoId));
            }
        }
        if($sType == 'favourite')
        {
             $sUnfavouritePhrase = _p('videochannel.unfavourite');
            if(Phpfox::getService('videochannel')->addToFavorite('videochannel', $iVideoId))
            {
                $this->html('#yn_videochannel_favourite', "<a href='#' class='btn btn-sm btn-danger' onclick=\"addToFavourite({$iVideoId}, 'unfavourite');\">
                                <i class=\"fa fa-fw\">&#xf005</i>
                                <span id='yn_favourite_text'>{$sUnfavouritePhrase}</span>
                                </a>");
                //$this->setMessage(_p('videochannel.video_successfully_added'));

                if (Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY'))
                {
                        Phpfox::getService('feed.process')->callback($aCallback)->add('videochannel_favourite', $iVideoId, $aVideo['privacy'], $aVideo['privacy_comment'], ($aCallback === null ? 0 : $aVideo['item_id']));
                }

                // Update user activity
                Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'videochannel');
                Phpfox::getService('notification.process')->add('videochannel_favourite', $aVideo['video_id'], $aVideo['user_id']);

                (($sPlugin = Phpfox_Plugin::get('videochannel.component_ajax_favorite_end')) ? eval($sPlugin) : false);

                $this->alert(_p('videochannel.favourite_succeed'));
                $this->call('setTimeout(function() {window.$Core.reloadPage();}, 2000);');
            }
            else {
                $this->alert(_p('videochannel.favourite_unsucceed'));
            }
        }
        else if($sType =='unfavourite')
        {
            $sFavouritePhrase = _p('videochannel.favourite');
             if(Phpfox::getService('videochannel.process')->unfavouriteVideo( $iVideoId))
             {
                $this->html('#yn_videochannel_favourite', "<a href='#' class='btn btn-sm btn-primary' onclick=\"addToFavourite({$iVideoId}, 'favourite');\">
                                 <i class=\"fa fa-fw\">&#xf006</i>
                                <span id='yn_favourite_text'>{$sFavouritePhrase}</span>
                                </a>");

                if (Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY'))
                {
                        Phpfox::getService('feed.process')->callback($aCallback)->add('videochannel_unfavourite', $iVideoId, $aVideo['privacy'], $aVideo['privacy_comment'], ($aCallback === null ? 0 : $aVideo['item_id']));
                }

                // Update user activity
                Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'videochannel');

                (($sPlugin = Phpfox_Plugin::get('videochannel.component_ajax_unfavorite_end')) ? eval($sPlugin) : false);

                $this->alert(_p('videochannel.unfavourite_succeed'));
                $this->call('setTimeout(function() {window.$Core.reloadPage();}, 2000);');
             }
             else
             {
                $this->alert(_p('videochannel.unfavourite_unsucceed'));
             }
        }

    }



	public function addShare()
	{
		$this->errorSet('#js_video_error');

		if (($aVals = $this->get('val')))
		{
			if (Phpfox::getService('videochannel.grab')->get($aVals['url']))
			{
				if ($iId = Phpfox::getService('videochannel.process')->addShareVideo($aVals, true))
				{
					$this->call('Editor.insert({type: \'video\', id: \'' . (int) $iId . '\', editor_id: \'' . $this->get('editor_id') . '\'});');
					$this->setMessage(_p('videochannel.video_successfully_added'));
					return;
				}
			}
		}
	}

	public function deleteImage()
	{
		Phpfox::isUser(true);

		if (Phpfox::getService('videochannel.process')->deleteImage($this->get('id')))
		{

		}
	}

	public function process()
	{
		exit();

		$this->errorSet('#js_video_upload_message');
		$sModule = $this->get('module', null);
		$iItem = $this->get('item', null);

		$sMethod = false && ($this->get('method','default') == 'massuploader');

		if ($iId = Phpfox::getService('videochannel.process')->process($this->get('video_id'), $sModule, $iItem))
		{
			$aVideo = Phpfox::getService('videochannel')->getVideo($this->get('video_id'), true);

			$this->call('window.location.href = \'' . Phpfox::permalink('videochannel', $aVideo['video_id'], $aVideo['title']) . '\';');
		}
		else
		{
			$this->show('#js_video_upload_error');
		}
	}

	public function update()
	{
		$aVals = $this->get('val');

		if (!isset($aVals['video_id']))
		{
			return Phpfox_Error::set(_p('videochannel.unable_to_edit_this_video_as_there_is_no_video_id'));
		}

		Phpfox::getService('ban')->checkAutomaticBan($aVals['title'] . ' ' . $aVals['text'] . ' ' . $aVals['tag_list']);
		if ($mReturn = Phpfox::getService('videochannel.process')->update($aVals['video_id'], $aVals))
		{
			if (!is_bool($mReturn))
			{
				$aVideo = Phpfox::getService('videochannel')->getVideo($aVals['video_id'], true);

				$this->attr('#js_view_video_link', 'href', ($aVideo['module_id'] != 'videochannel' ? Phpfox::getLib('url')->makeUrl('videochannel', array('redirect' => $aVideo['video_id'])) : Phpfox::getService('videochannel')->makeUrl(Phpfox::getUserBy('user_name'), $mReturn)));
			}

			$this->show('#js_save_video')->html('#js_save_video', '<span class="valid_message">'._p('videochannel.done').'</span>', '.fadeOut(5000)');
		}
		else
		{
			$this->html('#js_save_video', '');
		}

		$this->attr('#js_save_button', 'disabled', false)
			->removeClass('#js_save_button', 'disabled');
	}

	public function edit()
	{
		Phpfox::isUser(true);

		if (Phpfox::getService('user.auth')->hasAccess('channel_video', 'video_id', $this->get('video_id'), 'videochannel.can_edit_own_video', 'videochannel.can_edit_other_video'))
		{
			$aVideo = Phpfox::getService('videochannel')->getForEdit($this->get('video_id'));


			echo '<div><input type="hidden" name="val[video_id]" value="' . $aVideo['video_id'] . '" /></div>';
			echo '<div><input type="hidden" name="val[user_name]" value="' . $aVideo['user_name'] . '" /></div>';
			echo '<div><input type="hidden" name="val[is_instant_edit]" value="1" /></div>';

			$this->template()->assign(array(
						'aForms' => $aVideo,
						'edit_url'=>Phpfox::getLib('url')->makeUrl('videochannel.edit', array('id' => $aVideo['video_id'])),
						'sCategories' => Phpfox::getService('videochannel.category')->get(),
						'sModule' => isset($aVideo['module_id']) ? $aVideo['module_id'] : ''
					)
				)
			//->getTemplate('videochannel.block.form');
			->getTemplate('videochannel.block.formajax');
			/*
			$this->html('#js_video_edit_form', $this->getContent(false))
				->show('#js_video_edit_form_outer')
				->attr('#js_video_go_advanced', 'href', Phpfox::getLib('url')->makeUrl('videochannel.edit', array('id' => $aVideo['video_id'])))
				->hide('#js_video_outer_body')
				->hide('#yn_slide_show_block')
				->call('$Core.loadInit();')
				->call('var aCategories = explode(\',\', \'' . $aVideo['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); }');
			*/

		}
	}

    /**
     * @see Videochannel_Service_Videochannel
     *
     * @see Videochannel_Service_Process
     *
     * @return type
     */
	public function viewUpdate()
	{
		$aVals = $this->get('val');

		if (!isset($aVals['video_id']))
		{
			return Phpfox_Error::set(_p('videochannel.unable_to_edit_this_video_as_there_is_no_video_id'));
		}

		if ($mReturn = Phpfox::getService('videochannel.process')->update($aVals['video_id'], $aVals))
		{
			$oParseInput = Phpfox::getLib('parse.input');
			$oParseOutput = Phpfox::getLib('parse.output');

			if (isset($aVals['is_inline']))
			{
				$aVideo = Phpfox::getService('videochannel')->getForEdit($aVals['video_id']);

				$this->call('window.location.href = \'' . $aVideo['video_url'] . '\';');

				return;
			}

			if (!is_bool($mReturn))
			{
				$this->attr('.js_video_title_' . $aVals['video_id'], 'href', Phpfox::getService('videochannel')->makeUrl($aVals['user_name'], $mReturn));
			}

			$this->hide('#js_video_edit_form_outer')
				->html('#js_video_title_' . $aVals['video_id'],$oParseOutput->shorten($oParseOutput->clean($oParseInput->clean($aVals['title'])), 30, '...',false))
				->html('#js_video_text_' . $aVals['video_id'], (empty($aVals['text']) ? '' : $oParseOutput->parse($oParseInput->prepare($aVals['text']))))
				->show('#js_video_outer_body');
		}
	}

    /**
     * @see Phpfox_Url
     */
	public function delete()
	{
		if (Phpfox::getService('videochannel.process')->delete($this->get('video_id')))
		{
			$this->remove('#js_video_id_' . $this->get('video_id'));
		}

        $this->call("window.location = window.location;");
	}

	public function add()
	{
		$aParam = array();
		if ($this->get('bIsGroup'))
		{
			$aParam['bIsGroup'] = true;
		}
		if (!false || !Phpfox::getUserParam('videochannel.can_upload_videos'))
		{
			Phpfox::getComponent('videochannel.share', $aParam, 'controller');
		}
		else
		{
			Phpfox::getComponent('videochannel.upload', $aParam, 'controller');
		}

		echo $this->template()->getHeader();

		echo '<script type="text/javascript">$Core.loadInit();</script>';
	}

	public function upload()
	{
		Phpfox::getComponent('videochannel.upload', array('bHideSwitchMenu' => true), 'controller');

		echo $this->template()->getHeader();

		echo '<script type="text/javascript">$Core.loadInit();</script>';

		$this->html('#js_video_content', $this->getContent(false));
	}

	public function share()
	{
		Phpfox::getComponent('videochannel.share', array(), 'controller');

		echo $this->template()->getHeader();

		echo '<script type="text/javascript">$Core.loadInit();</script>';

		$this->html('#js_video_content', $this->getContent(false));
	}

	public function getNew()
	{
		Phpfox::getBlock('videochannel.new');

		$this->html('#' . $this->get('id'), $this->getContent(false));
		$this->call('$(\'#' . $this->get('id') . '\').parents(\'.block:first\').find(\'.bottom li a\').attr(\'href\', \'' . Phpfox::getLib('url')->makeUrl('videochannel') . '\');');
	}

	public function feature2()
	{
		if (Phpfox::getService('videochannel.process')->feature($this->get('video_id'), $this->get('type')))
		{
			if($this->get('type') == 1)
				$this->alert(_p('videochannel.featured_this_video_successfully'),'Moderation',300,100,true);
		      else
				$this->alert(_p('videochannel.un_featured_this_video_successfully'),'Moderation',300,100,true);
		}
	}

	public function feature()
	{
		if (Phpfox::getService('videochannel.process')->feature($this->get('video_id'), $this->get('type')))
		{

		}
	}

	public function spotlight()
	{
		if (Phpfox::getService('videochannel.process')->spotlight($this->get('video_id'), $this->get('type')))
		{

		}
	}

	public function sponsor()
	{
	    return false;
	  /*
	    Phpfox::getUserParam('videochannel.can_sponsor_videochannel', true);
	    if (Phpfox::getService('videochannel.process')->sponsor($this->get('video_id'), $this->get('type')))
	    {
		if ($this->get('type') == '1')
		{
		    Phpfox::getService('ad.process')->addSponsor(array('module' => 'videochannel', 'section' => '', 'item_id' => $this->get('video_id')));
		    // image was sponsored
		    $sHtml = '<a href="#" title="' . _p('videochannel.unsponsor_this_video') . '" onclick="$.ajaxCall(\'videochannel.sponsor\', \'video_id=' . $this->get('video_id') . '&amp;type=0\'); return false;">' . _p('videochannel.un_sponsor') . '</a>';
		}
		else
		{
		    Phpfox::getService('ad.process')->deleteAdminSponsor('videochannel', $this->get('video_id'));
		    $sHtml = '<a href="#" title="' . _p('videochannel.sponsor_this_video') . '" onclick="$.ajaxCall(\'videochannel.sponsor\', \'video_id=' . $this->get('video_id') . '&amp;type=1\'); return false;">' . _p('videochannel.sponsor') . '</a>';
		}
		$this->html('#js_video_sponsor_' . $this->get('video_id'), $sHtml)
			->alert($this->get('type') == '1' ? _p('videochannel.video_successfully_sponsored') : _p('videochannel.video_successfully_un_sponsored'));
		if($this->get('type') == '1')
		{
		    $this->addClass('#js_video_id_' . $this->get('video_id'), 'row_sponsored_image');
			$this->call("$('#js_video_id_" . $this->get('video_id') . "').find('.row_sponsored_link:first').show();");
		}
		else
		{
			$this->call("$('#js_video_id_" . $this->get('video_id') . "').find('.row_sponsored_link:first').hide();");
		    $this->removeClass('#js_video_id_' . $this->get('video_id'), 'row_sponsored_image');
		}
	    }
	  */
	}

	public function approve()
	{
		if (Phpfox::getService('videochannel.process')->approve($this->get('video_id')))
		{
			if ($this->get('inline'))
			{
				$this->alert(_p('videochannel.video_has_been_approved'), _p('videochannel.video_approved'), 300, 100, true);
				$this->hide('.item-pending');
				$this->hide('.js_moderation_off');
				$this->show('.js_moderation_on');
			}

		}
	}

	public function convert()
	{
		Phpfox::isUser(true);

		if (Phpfox::getService('videochannel.convert')->process($this->get('attachment_id'), (($this->get('full') || $this->get('inline'))? false : true)))
		{
			if ($this->get('full'))
			{
				$aVideo = Phpfox::getService('videochannel')->getVideo($this->get('attachment_id'), true);

				$this->call('window.location.href = \'' . Phpfox::permalink('videochannel', $aVideo['video_id'], $aVideo['title']) . '\';');
			}
			elseif ($this->get('inline'))
			{
				$iFeedId = Phpfox::getService('feed.process')->getLastId();

				(($sPlugin = Phpfox_Plugin::get('videochannel.component_ajax_convert_feed')) ? eval($sPlugin) : false);

		    	$this->call('window.parent.$.ajaxCall(\'videochannel.displayFeed\', \'id=' . $iFeedId . '&video_id=' . $this->get('attachment_id') . '&custom_pages_post_as_page=' . $this->get('custom_pages_post_as_page') . '\', \'GET\');');
			}
			else
			{
				$aVideo = Phpfox::getService('videochannel.convert')->getDetails();
				Phpfox::getService('attachment.process')->update(array(
						'destination' => $aVideo['destination'],
						'extension' => $aVideo['extension'],
						'is_video' => '1',
						'video_duration' => $aVideo['duration']
					), $this->get('attachment_id')
				);
				/*
				$aVideo = Phpfox::getService('videochannel.convert')->getDetails();
				Phpfox::getService('attachment.process')->update(array(
						'destination' => $aVideo['destination'],
						'extension' => $aVideo['extension'],
						'is_video' => '1',
						'video_duration' => $aVideo['duration']
					), $this->get('attachment_id')
				);

				Phpfox::getBlock('attachment.list', array('sIds' => $this->get('attachment_id'), 'bCanUseInline' => true, 'attachment_no_header' => true, 'attachment_edit' => true));

				$this->call('var $oParent = window.parent.$(\'#' . $this->get('attachment_obj_id') . '\');')
					->call('$oParent.find(\'.js_attachment:first\').val($oParent.find(\'.js_attachment:first\').val() + \'' . $this->get('attachment_id') . ',\');')
					->call('$oParent.find(\'.js_attachment_list:first\').show();')
					->call('$oParent.find(\'.js_attachment_list_holder:first\').prepend(\'' . $this->getContent() . '\');')
					->call('$Core.loadInit();');

				// $this->call('Editor.insert({is_image: true, name: \'\', id: \'' . $aVideo['video_id'] . '\', type: \'video\'});');
				*/
				$this->call('Editor.insert({id: \'' . $aVideo['video_id'] . '\', type: \'attachment\', name: \'\'});');

				if ($this->get('attachment_inline'))
				{
					$this->call('$Core.clearInlineBox();');
				}
				else
				{
					$this->call('tb_remove();');
				}
			}
		}
		else
		{
			$this->alert(implode('<br />', Phpfox_Error::get()));
		}
	}

	public function play()
	{
		$aVideo = Phpfox::getService('videochannel')->getVideo($this->get('id'));
		if ($aVideo['is_stream'])
		{
			$sEmbedCode = $aVideo['embed_code'];
			if( isset($_SERVER['HTTPS'] ) ) {
				$sEmbedCode = str_replace("http","https",$sEmbedCode);
			}
			if ($this->get('popup'))
			{
				$this->setTitle($aVideo['title']);
				echo '<div class="t_center">';
				echo $sEmbedCode;
				echo '</div>';
			}
			elseif ($this->get('feed_id'))
			{
				$this->call('$(\'#js_item_feed_' . $this->get('feed_id') . '\').find(\'.activity_feed_content_link:first\').html(\'' . str_replace("'", "\\'", $sEmbedCode) . '\');');
			}
			else
			{
				$this->html('#js_global_link_id_' . $this->get('id'), str_replace("'", "\\'", $sEmbedCode));
			}
		}
		else
		{
			$sVideoPath = (preg_match("/\{file\/videos\/(.*)\/(.*)\.flv\}/i", $aVideo['destination'], $aMatches) ? Phpfox::getParam('core.path') . str_replace(array('{', '}'), '', $aMatches[0]) : Phpfox::getParam('video.url') . $aVideo['destination']);
			if (!empty($aVideo['server_id']))
			{
				$sVideoPath = Phpfox::getLib('cdn')->getUrl($sVideoPath, $aVideo['server_id']);
			}
			$sDivId = 'js_tmp_video_player_' . $aVideo['video_id'];
			if ($this->get('popup'))
			{
				$this->setTitle($aVideo['title']);
				$this->call('<script type="text/javascript">$Core.loadStaticFile(\'' . $this->template()->getStyle('static_script', 'player/' . Phpfox::getParam('core.default_music_player') . '/core.js') . '\');</script>');
				echo '<div class="t_center">';
				echo '<div id="' . $sDivId . '" style="width:640px; height:390px; margin:auto;"></div>';
				echo '</div>';
				$this->call('<script type="text/javascript">$Core.player.load({id: \'' . $sDivId . '\', auto: true, type: \'video\', play: \'' . $sVideoPath . '\'});</script>');
			}
			else
			{
				$this->call('$Core.loadStaticFile(\'' . $this->template()->getStyle('static_script', 'player/' . Phpfox::getParam('core.default_music_player') . '/core.js') . '\');');
				$this->call('$(\'#js_item_feed_' . $this->get('feed_id') . '\').find(\'.activity_feed_content_link:first\').html(\'<div id="' . $sDivId . '" style="width:425px; height:349px;"></div>\');');
				$this->call('$Core.player.load({id: \'' . $sDivId . '\', auto: true, type: \'video\', play: \'' . $sVideoPath . '\'});');
			}
		}
	}

	public function getUserVideos()
	{
		Phpfox::getBlock('videochannel.user');

		$this->html('.video_user_bar', $this->getContent(false));
		$this->call('$Core.loadInit();');
	}

	public function getMoreRelated()
	{
		$bReturn = Phpfox::getBlock('videochannel.related');

		$sContent = $this->getContent(false);

		if (Phpfox::getLib('parse.format')->isEmpty($sContent))
		{
			$this->remove('#js_block_bottom_link_1')->html('#js_block_bottom_1', _p('videochannel.span_no_more_suggestions_found_span'));
		}
		else
		{
			$this->val('#js_video_related_page_number', ((int) $this->get('page_number') + 1));
			$this->append('#js_video_related_load_more', $sContent);
		}

		$this->call('$(\'#js_block_bottom_1\').find(\'.ajax_image\').hide();');
	}

	public function moderation()
	{
		Phpfox::isUser(true);

		switch ($this->get('action'))
		{
			case 'approve':
				Phpfox::getUserParam('videochannel.can_approve_videos', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('videochannel.process')->approve($iId);
				//  $this->call('$(\'#js_video_id_' . $iId . '\').remove();');
					$this->call('$(\'#js_video_id_' . $iId . ' .row_pending_link\').remove();');
				}
				$this->updateCount();
				$sMessage = _p('videochannel.video_s_successfully_approved');
				break;
			case 'delete':
				Phpfox::getUserParam('videochannel.can_delete_other_video', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('videochannel.process')->delete($iId);
					$this->slideUp('#js_video_id_' . $iId);
				}
				$sMessage = _p('videochannel.video_s_successfully_deleted');
				break;
		}

		$this->alert($sMessage, 'Moderation', 300, 150, true);
		$this->hide('.moderation_process');
	}

	public function displayFeed()
	{
 		$aVideo = Phpfox::getService('videochannel')->getForEdit($this->get('video_id'), true);

		$aCallback = null;
		if ($aVideo['module_id'] != 'videochannel' && Phpfox::hasCallback($aVideo['module_id'], 'convertVideo'))
		{
			$aCallback = Phpfox::callback($aVideo['module_id'] . '.convertVideo', $aVideo);
		}

		Phpfox::getService('feed')->callback($aCallback)->processAjax($this->get('id'));
	}

	public function supportedSites()
	{
		$this->setTitle(_p('videochannel.supported_sites'));
		Phpfox::getBlock('videochannel.supported');
	}

    public function updateActivity()
    {
        Phpfox::getService('videochannel.category.process')->updateActivity($this->get('id'), $this->get('active'));
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'channel_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('videochannel_category_browse');
        $this->alert('Ordering category success', 'Ordering', 300, 150, true);
    }
}

?>