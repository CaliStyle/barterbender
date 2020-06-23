<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class FeedBack_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function uploadProcess()
	{
		$this->call('completeProgress();');

	}
	public function saveValue()
	{
		$_SESSION['data_search'] = $this->getAll();
		$js = "setValue();";
		$this->call($js);
	}

	public function saveAdminValue()
	{
		$_SESSION['data_admin_search'] = $this->getAll();
		$js = "setValue();";
		$this->call($js);
	}
	public function addFeedBack()
	{
            
		if(!Phpfox::isUser()){
			if(!Phpfox::getParam('feedback.is_allowed_creation'))
			{
				Phpfox::isUser(true);

			}
		}                
        $aParams=array();
		Phpfox::getBlock('feedback.add',$aParams); //$aParams=array()
	}

	public function isValid($aVals)
	{
		$errors = "";
		if(empty($aVals['title']))
		    $errors .= _p('feedback.title_cannot_be_empty').'. <br />';
		else {
            if(Phpfox::getService('feedback')->checkFeedBackExistByTitle($aVals['title']))
                $errors .= _p('feedback.the_feedback_title_already_exists', ['title' => $aVals['title']]) .'<br />';
        }
		if(empty($aVals['description']))
		    $errors .= _p('feedback.description_of_feedback_cannot_be_empty').'. <br />';

		if(!Phpfox::isUser())
		{
			if(isset($aVals['full_name']) && empty ($aVals['full_name']))
			{
				$errors .= _p('feedback.your_full_name_cannot_be_empty').'. <br />';
			}
			$email = trim($aVals['email']);
			if(!Phpfox::getService('feedback')->isEmail($email))
			{
				Phpfox::getService('user.validate')->email($aVals['email']);
				$errors .= _p('feedback.invalid_email_address').'. <br />';
			}
		}
		return $errors;
	}

	public function addFeed()
	{
		$aVals = $this->get('val');
		$errors = $this->isValid($aVals);
		if($errors != "")
		{
			$this->html('#errofeedback','<div class="error_message">'.$errors.'</div>');
			$sJsEnable = "$('#js_submit_form_feedback').removeClass('disabled').removeAttr('disabled');";
			$this->call($sJsEnable);
			return false;
		}
		$post_ajax="$('#post_ajax_feedback').val(2);";
		$this->call($post_ajax);
		$js = "$('#js_form_feedback').submit();";
		$this->call($js);
		return true;
	}

	public function updateStatusAdmin()
	{
		$errors = "";
		$aVals = $this->get('val');
		if($aVals['status_id'] == 0)
		{
			$errors .= _p('feedback.please_select_one_of_status_for_your_feedback').'. <br />' ;
		}
		if(empty($aVals['description']))
		{
			$errors .= _p('feedback.description_of_the_status_feedback_cannot_be_empty').'. <br />' ;
		}
		if($errors != "")
		{
			$this->html('#errorstatus','<div class="error_message">'.$errors.'</div>');
			return false;
		}
		$post_ajax="$('#post_ajax_feedback').val(2);";
		$this->call($post_ajax);
		$js = "$('#js_form_statusfeedback').submit();";
		$this->call($js);
		return true;
	}

	public function editFeedBack()
	{
		Phpfox::isUser(true);
		$feedback_id = (int) $this->get('feedback_id');
		$aParams = array('feedback_id'=>$feedback_id);
		Phpfox::getBlock('feedback.editfeedback', $aParams);

	}

	public function callEditCategory()
	{
		$idCat = (int)$this->get('cat_id');
		$page = (int)$this->get('page');
		$aParams=array('category_id'=>$idCat, 'page'=>$page);
		Phpfox::getBlock('feedback.editcategory', $aParams);
	}

	public function callEditServerity()
	{
		$idSer = (int)$this->get('serverity_id');
		$page = (int)$this->get('page');
		$aParams = array('serverity_id'=>$idSer, 'page'=>$page);
		Phpfox::getBlock('feedback.editserverity', $aParams);
	}

	public function callEditStatus()
	{
		$status_id = (int)$this->get('status_id');
		$aParams=array('status_id'=>$status_id);
		Phpfox::getBlock('feedback.editstatus', $aParams);
	}


	public function callEditFeedBack()
	{
		$feedback_id = (int)$this->get('feedback_id');
		$aParams = array('feedback_id'=>$feedback_id);
		Phpfox::getBlock('feedback.editfeedbackforadmin', $aParams);
	}


	public function callDeleteCategory()
	{  
		
		$category_id = (int)$this->get('category_id');
		$isDelete = phpfox::getService('feedback.process')->deleteCategory($category_id);
		if($isDelete)
		{
			Phpfox::addMessage('The Category"'.$isDelete.'" was deleted successfully.');
			$js = "window.location.href='".phpfox::getLib('url')->makeUrl('admincp.feedback.category')."'";
			$this->call($js);
		}
		else
		{
			$js = "window.location.href='".phpfox::getLib('url')->makeUrl('admincp.feedback.category')."'";
			$this->call($js);
			Phpfox::addMessage(_p('feedback.delete_the_category_fail').".");
		}

	}

	public function callDeleteServerity()
	{
		$serverity_id = (int)$this->get('serverity_id');
		$isDelete = phpfox::getService('feedback.process')->deleteServerity($serverity_id);
		if($isDelete)
		{
			Phpfox::addMessage('The Serverity "'.$isDelete.'" was deleted successfully.');
			$js = "window.location.href='".phpfox::getLib('url')->makeUrl('admincp.feedback.serverity')."'";
			$this->call($js);
		}
		else
		{
			$js = "window.location.href='".phpfox::getLib('url')->makeUrl('admincp.feedback.serverity')."'";
			$this->call($js);
			Phpfox::addMessage("Delete the Serverity fail.");
		}

	}

	public function callDeleteSatus()
	{
		$status_id = (int)$this->get('status_id');
		$isDelete = phpfox::getService('feedback.process')->deleteStatus($status_id);
		if($isDelete)
		{
			Phpfox::addMessage('The Status "'.$isDelete.'" was deleted successfully.');
			$js = "window.location.href='".phpfox::getLib('url')->makeUrl('admincp.feedback.status')."'";
			$this->call($js);
		}
		else
		{
			$js = "window.location.href='".phpfox::getLib('url')->makeUrl('admincp.feedback.status')."'";
			$this->call($js);
			Phpfox::addMessage("Delete the Status fail.");
		}

	}

	public function updateVote()
	{
		phpfox::isUser(true);
		$feedback_id = (int)$this->get('feedback_id');
		$total_vote = phpfox::getLib('database')->select('fb.total_vote')
												->from(phpfox::getT('feedback'), 'fb')
												->where('fb.feedback_id = '.$feedback_id)
												->execute('getField');
		$sType = $this->get('sType');
		$user_vote = phpfox::getService('feedback.vote')->getVote($feedback_id,phpfox::getUserId());
		if ($sType=='up')
		{
			$bCheckLimitVote = Phpfox::getService('feedback')->checkVoteOfUser(Phpfox::getUserId());
			if($bCheckLimitVote)
			{
				$sType='down';
				if($user_vote != null)
				{
					$this->call('tb_close();') ;
					$this->alert(_p('feedback.you_have_already_voted_this_feedback'));
					return false;
				}
				
				$total_vote += 1;
				Phpfox::getLib('phpfox.database')
				->update(Phpfox::getT('feedback'),
				array(
                                'total_vote'=>$total_vote,
				)
				,'feedback_id ='.$feedback_id);
				phpfox::getService('feedback.vote')->insertVote($feedback_id,phpfox::getUserId());
			}
			else 
			{
				$js = "alert('You cannot vote more feedbacks.');";
				$this->call($js);
			}
		}
		else
		{
			$sType ='up';
			$total_vote -= 1;
			Phpfox::getLib('phpfox.database')
			->update(Phpfox::getT('feedback'),
			array(
                'total_vote'=>$total_vote,
			)
			,'feedback_id ='.$feedback_id);
			phpfox::getService('feedback.vote')->deleteVote($feedback_id,phpfox::getUserId());
		}
		$core_url = phpfox::getParam('core.path');
		//$str="<img src=".$core_url."module/feedback/static/image/vote_".$sType.".gif alt='' style='vertical-align:middle;'/>";
		$sType = trim($sType);
		if($sType == 'down') $str=_p('feedback.remove_feedback'); else $str=_p('feedback.vote_feedback');
		$v_str = "";
		if($total_vote == 1) $v_str=_p('feedback_vote'); else $v_str=_p('feedback_votes_n');
		$this->html('#feedback_vote_' . $feedback_id, '<button class="btn btn-success vote_button_feedback" onclick="updatevote('.$feedback_id.','.$total_vote.','."'".$sType."'".')">'.$str.'</button>');
		if($total_vote<0)
			$total_vote = 0;
		$this->html('#feedback_voting_'.$feedback_id, '<span>'.$total_vote.'<span>');
		$this->html('#feedback_voting_title_'.$feedback_id, $v_str);
	}

	public function updateVotePopUp()
	{
		$feedback_id = (int)$this->get('feedback_id');
		$f_url = phpfox::getLib('url')->makeUrl('feedback');
		$f_url = str_replace(phpfox::getParam('core.path'),"",$f_url);
		$f_url = str_replace("index.php?do=","",$f_url);
		Phpfox::getLib('session')->set('redirect', $f_url);
		phpfox::isUser(true);
		$total_vote = (int)$this->get('total_vote');
		$sType = $this->get('sType');
		$user_vote = phpfox::getService('feedback.vote')->getVote($feedback_id,phpfox::getUserId());
		if ($sType=='up')
		{
			$bCheckLimitVote = Phpfox::getService('feedback')->checkVoteOfUser(Phpfox::getUserId());
			if($bCheckLimitVote)
			{
				$sType='down';
				if($user_vote != null)
				{
					$this->call("$('#TB_window').hide();");
					$this->alert(_p('feedback.you_have_already_voted_this_feedback'));
					return false;
				}
				$total_vote += 1;
				Phpfox::getLib('phpfox.database')
				->update(Phpfox::getT('feedback'),
				array(
	                'total_vote'=>$total_vote,
				)
				,'feedback_id ='.$feedback_id);
				phpfox::getService('feedback.vote')->insertVote($feedback_id,phpfox::getUserId());
			}
			else 
			{
				$js = "alert('You cannot vote more feedbacks.');";
				$this->call($js);
			}
		}
		else
		{
			$sType ='up';
			$total_vote -= 1;
			Phpfox::getLib('phpfox.database')
			->update(Phpfox::getT('feedback'),
			array(
                'total_vote'=>$total_vote,
			)
			,'feedback_id ='.$feedback_id);
			phpfox::getService('feedback.vote')->deleteVote($feedback_id,phpfox::getUserId());
		}

		$core_url = phpfox::getParam('core.path');
		//$str="<img src=".$core_url."module/feedback/static/image/vote_".$sType.".gif alt='' style='vertical-align:middle;'/>";
		$sType = trim($sType);
		if($sType == 'down') $str=_p('feedback.remove_feedback'); else $str=_p('feedback.vote_feedback');
		$v_str = "";
		if($total_vote == 1) $v_str=_p('feedback_vote'); else $v_str=_p('feedback_votes_n');
		$this->html('div[id="feedback_vote_popup_' . $feedback_id . '"]', '<button class="btn btn-success btn-xs vote_button" onclick="updatevotepopup('.$feedback_id.','.$total_vote.','."'".$sType."'".')">'.$str.'</button>');
		$this->html('p[id="feedback_voting_popup_'.$feedback_id . '"]', '<span>'.$total_vote.'<span>');
		$this->html('#feedback_voting_popup_title_'.$feedback_id, $v_str);
	}

	public function updateStatus()
	{
		$feedback_id = (int)$this->get('feedback_id');
		Phpfox::getBlock('feedback.updatestatus',$aParams=array('feedback_id'=>$feedback_id));
	}

	public function updateFeatured()
	{
		$item_id = (int)$this->get('item_id');
		$is_featured = (int)$this->get('is_featured');
		if ($item_id)
		{
			$is_featured = (int)(!$is_featured);
			Phpfox::getLib('phpfox.database')
			->update(Phpfox::getT('feedback'),
			array(
                'is_featured'=>$is_featured,
			)
			,'feedback_id ="'.$item_id.'"');

			$str =  $is_featured?_p('feedback.yes'): _p('feedback.no');
			if($is_featured)
			{
				$this->html('#item_update_featured_' . $item_id, '<a href="javascript:updatefeatured('.$item_id.','.$is_featured.')" title="'._p('feedback.click_to_clear_featured').'">'.$str.'</a>');
			}
			else {
				$this->html('#item_update_featured_' . $item_id, '<a href="javascript:updatefeatured('.$item_id.','.$is_featured.')" title="'._p('feedback.click_to_set_as_featured').'">'.$str.'</a>');
			}

            if ($is_featured)
                $this->alert(_p('feedback_feature_successfully'),_p('pages.moderation'),300,150,true);
            else
                $this->alert(_p('feedback_unfeature_successfully'),_p('pages.moderation'),300,150,true);

            $js = "window.location.reload()";
            $this->call($js);
		}
	}
        
        public function updateVotable()
	{
		$item_id = (int)$this->get('item_id',0);
		$votable = (int)$this->get('votable');
		if ($item_id)
		{
			$votable = (int)(!$votable);
			Phpfox::getLib('phpfox.database')
			->update(Phpfox::getT('feedback'),
			array(
                'votable'=>$votable,
			)
			,'feedback_id ="'.$item_id.'"');

			$str =  $votable?_p('feedback.enabled'): _p('feedback.disabled');
			if($votable)
			{
				$this->html('#item_update_votable_' . $item_id, '<a href="javascript:updatevotable('.$item_id.','.$votable.')" title="'._p('feedback.click_to_clear_votable').'">'.$str.'</a>');
			}
			else {
				$this->html('#item_update_votable_' . $item_id, '<a href="javascript:updatevotable('.$item_id.','.$votable.')" title="'._p('feedback.click_to_set_as_votable').'">'.$str.'</a>');
			}
			

		}
	}
        
	public function inlineDelete()
	{
		phpfox::isUser(true);
		$feedback_id = $this->get('feedback_id');
		$isDelete = phpfox::getService('feedback')->delete($feedback_id);
		if($isDelete)
		{
			//Phpfox::addMessage(_p('feedback.delete_your_feedback_successfully').".");
                        $this->call("$('#js_feedback_entry" . $feedback_id . "').hide('slow'); $('#core_js_messages').message('" . _p('feedback.feedback_delete', array('phpfox_squote' => true)) . "', 'valid').fadeOut(5000);");
                        //$this->url()->send('feedback',null,_p('feedback.delete_your_feedback_successfully'));
                        
                }
		else
		{
			Phpfox::addMessage(_p('feedback.delete_your_feedback_fail').".");
		}

	}

	public function inlineDeleteFeedBack()
	{
		phpfox::isUser(true);
		$feedback_id = $this->get('feedback_id');
		$isDelete = phpfox::getService('feedback')->delete($feedback_id);
		if($isDelete)
		{
			Phpfox::addMessage(_p('feedback.the_feedback_title_was_deleted_successfully',array('feedback_title'=>$isDelete)).'.');
			$js = "window.location.href='".phpfox::getLib('url')->makeUrl('admincp.feedback.feedbacks')."'";
			$this->call($js);
		}
		else
		{
			Phpfox::addMessage(_p('feedback.the_feedback_title_was_deleted_fail',array('feedback_title'=>$isDelete)).'.');
			$js = "window.location.href='".phpfox::getLib('url')->makeUrl('admincp.feedback.feedbacks')."'";
			$this->call($js);
		}
	}

	public function deletePicture()
	{
		phpfox::isUser(true);
		$feedback_id = $this->get('feedback_id');
		$picture_id = $this->get('picture_id');
		$isDelete = phpfox::getService('feedback')->deletePic($picture_id,$feedback_id);
		if($isDelete)
		{
			Phpfox::addMessage(_p('feedback.delete_the_picture_successfully').".");
			$js = "window.location.href='".phpfox::getLib('url')->makeUrl('feedback.detail').$isDelete."'";
			$this->call($js);
		}
		else
		{
			Phpfox::addMessage(_p('feedback.delete_your_picture_fail').".");
			$js = "window.location.href='".phpfox::getLib('url')->makeUrl('admincp.feedback.feedbacks')."'";
			$this->call($js);
		}
	}

	public function deletePhotoOnUploadForm() {
	    Phpfox::isUser(true);
        $iPictureId = $this->get('id');
        $aPicture = Phpfox::getService('feedback')->getPicture($iPictureId);
        if(empty($aPicture)) {
            return;
        }
        $iFeedbackId = $aPicture['feedback_id'];
        $aFeedBack = Phpfox::getService('feedback')->getFeedBackById($iFeedbackId);
        if(empty($aFeedBack)) {
            return;
        }
	    if((Phpfox::getUserParam('feedback.edit_own_feedback') && (Phpfox::getUserId() == $aFeedBack['user_id'])) || (Phpfox::getUserParam('feedback.edit_user_feedback') && (Phpfox::getUserId() == $aFeedBack['user_id']))) {
            Phpfox::getService('feedback')->deletePic($iPictureId,$iFeedbackId);
        }
    }

	public function getNew()
	{
		Phpfox::getBlock('feedback.new');
		$this->html('#' . $this->get('id'), $this->getContent(false));
		$this->call('$(\'#' . $this->get('id') . '\').parents(\'.block:first\').find(\'.bottom li a\').attr(\'href\', \'' . Phpfox::getLib('url')->makeUrl('feedback') . '\');');
	}

	public function viewFeedback()
	{
		$feedback_id = $this->get('id');
		$html_hide = "$('#col-add').hide(); $('#post_your_feedback').show();";
		$this->call($html_hide);
        $img_loader = Phpfox::getLib('phpfox.image.helper')->display(array(
            'theme'=>'ajax/large.gif',
            'class' =>'ajax_image'
            ));
        $htimg = "<div class=\"loadding\">".$img_loader."</div>";
		$html_show = "$('#show_feedback').show();$('#show_feedback').html('".$htimg."');";
		$this->call($html_show);
		$aParams = array('feedback_id' => $feedback_id);
		phpfox::getBlock('feedback.entry_feedback', $aParams);
		$fb = $this->getContent(false);
		$this->html('#show_feedback', $fb);

	}

	public function showFormPostFeedBack()
	{
		$html_show = "$('#col-add').show(); $('#post_your_feedback').hide();";
		$this->call($html_show);
		$html_hide = "$('#show_feedback').hide();";
		$this->call($html_hide);
	}

	public function viewFeedbackByCategory()
	{
		$category_id = $this->get('id');
		$aParams = array('category_id' => $category_id);
		phpfox::getBlock('feedback.feedback_category', $aParams);
		$feedback_category = $this->getContent(false);
		$this->html('#category_feedback', $feedback_category);
	}
	public function getPictureFeedbackBlock()
	{
		  $link = $this->get('link');
		Phpfox::getBlock('feedback.feedback_image',array(
                    'link' => $link,
                ));
	}


	public function approve()
	{
        Phpfox::isUser(true);
		if (Phpfox::getService('feedback.process')->approve($this->get('id')))
		{
			if ($this->get('inline'))
			{
				$this->alert(_p('feedback.feedback_has_been_approved'), _p('feedback.feedback_approved'), 300, 100, true);
				$this->updateCount();
                $this->call("setTimeout(window.location.reload(), 2000);");
			}
		}
	}

	public function moderation()
	{
		Phpfox::isUser(true);

		switch ($this->get('action'))
		{
			case 'approve':
				Phpfox::getUserParam('feedback.can_approve_feedbacks', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('feedback.process')->approve($iId);
					$this->remove('#js_feedback_entry' . $iId);
				}
				$this->updateCount();
				$sMessage = _p('feedback.feedback_has_been_approved');
				break;
			case 'delete':
				Phpfox::getUserParam('feedback.delete_user_feedback', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('feedback.process')->delete($iId);
					$this->slideUp('#js_feedback_entry' . $iId);
                    
				}
				$sMessage = _p('feedback.feedback_s_successfully_deleted');
				break;
		}

		$this->alert($sMessage, _p('feedback.moderation'), 300, 150, true);
		$this->hide('.moderation_process');
	}
}

?>