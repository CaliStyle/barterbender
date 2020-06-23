<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function setDefault()
    {
        if (Phpfox::getService('petition.process')->setDefault($this->get('id'))) {
            Phpfox::getLib('cache')->remove('petition_featured_0');
        }
    }

    public function sentToTarget()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('petition')->sendToTarget((int)$this->get('id'), 0)) {
            $this->alert(Phpfox::getPhrase('petition.petition_letter_was_successfully_sent_to_target'));
        } else {
            $this->alert('<div class="error_message">' . Phpfox::getPhrase('petition.an_error_occurred_and_petition_letter_could_not_be_sent_to_target_please_try_again') . '</div>');
        }
    }

    public function deleteNews()
    {
        if (Phpfox::getService('petition.process')->deleteNews($this->get('news_id'))) {
            $this->call("$('#news_" . $this->get('news_id') . "').hide('slow');");
            $this->alert(Phpfox::getPhrase('petition.news_successfully_deleted'), Phpfox::getPhrase('petition.delete'), 300, 100, true);
        }
    }

    public function postNews()
    {
        if ($aVals = $this->get('val')) {
            if (isset($aVals['post_news']) || isset($aVals['update_news'])) {
                if ($iId = Phpfox::getService('petition.process')->postNews($aVals['petition_id'], $aVals)) {
                    Phpfox::getBlock('petition.detail', array('sType' => 'news', 'id' => $aVals['petition_id']));
                    $this->call('$("#petition_comment_block").hide();');
                    $stringAjax = $this->getContent();
                    $stringAjax = str_replace("ajax/php/", "ajax.php", $stringAjax);

                    $this->call('$("#js_details_container").html("' . $stringAjax . '");')->call('$Core.loadInit();');
                    if (isset($aVals['news_id']) && (int)$aVals['news_id'] > 0) {
                        $this->alert(Phpfox::getPhrase('petition.news_successfully_updated'), Phpfox::getPhrase('petition.update'), 300, 100, true);
                        $this->alert('News updated');
                    } else {
                        $this->alert(Phpfox::getPhrase('petition.news_successfully_added'), Phpfox::getPhrase('petition.add'), 300, 100, true);
                    }
                };
            }
        }
    }

    public function signPetition()
    {
        if ($aVals = $this->get('val')) {
            if (isset($aVals['sign'])) //Sign petition
            {
                if (list($iTotal, $bIsSendThank) = Phpfox::getService('petition.process')->sign($aVals['petition_id'], $aVals)) {
                    if ($bIsSendThank) {
                        $this->hide('#js_form_sign');
                        $this->show('#js_thank_message');
                        $this->hide('.sign_now');
                        $this->show('#signed_' . $aVals['petition_id']);
                        $this->call('$(".total_sign").html(parseInt($(".total_sign").html()) + 1)');

                    } else {
                        $this->call('tb_remove();');
                    }
                } else {
                    $this->alert('<div class="error_message">' . Phpfox::getPhrase('petition.you_are_not_allowed_to_sign_this_petition') . '</div>', Phpfox::getPhrase('petition.notice'), 300, 100, true);
                };
            }
        }
    }

    public function sign()
    {
        Phpfox::isUser(true);
        Phpfox::getBlock('petition.sign',
            array('id' => $this->get('id'))
        );
        $this->setTitle(Phpfox::getPhrase('petition.sign_this_petition'));
        $this->call('<script type="text/javascript">$Core.loadInit();</script>');
    }

    public function inviteBlock()
    {
        Phpfox::getBlock('petition.invite',
            array('id' => $this->get('id'))
        );
        $this->setTitle(Phpfox::getPhrase('petition.sign_this_petition'));
        $this->call('<script type="text/javascript">$Core.loadInit();</script>');
    }

    public function inviteFriends()
    {
        if ($aVals = $this->get('val')) {
            if (isset($aVals['invite'])) {
                Phpfox::getService('petition.process')->sentInvite($aVals['petition_id'], $aVals);
                $this->alert(Phpfox::getPhrase('petition.successfully_invited_users'), Phpfox::getPhrase('petition.invite_friends'), 300, 100, true);
            }
        } else {
            $this->alert(Phpfox::getPhrase('petition.an_error_occurred_and_invited_message_could_not_be_sent'), Phpfox::getPhrase('petition.invite_friends'), 300, 100, false);
        }
    }

    public function deleteImage()
    {
        Phpfox::isUser(true);

        if ($iNewId = Phpfox::getService('petition.process')->deleteImage($this->get('id'))) {
            Phpfox::getLib('cache')->remove('petition_featured_0');
            $this->call('$("#js_photo_holder_' . $iNewId . '").addClass("row_focus");');
        }
    }

    public function updatePetition()
    {
        $sPetition = Phpfox::getService('petition.process')->updateTitle($this->get('petition_id'), $this->get('user_id'), $this->get('quick_edit_input'));
        if ($sPetition) {
            $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('admincp.petition') . '\'');
        }
    }

    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'approve':
                Phpfox::getUserParam('petition.can_approve_petitions', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('petition.process')->approve($iId);
                    $this->remove('#js_petition_entry' . $iId . ' .row_pending_link');
                }
                $this->updateCount();
                $sMessage = Phpfox::getPhrase('petition.petition_s_successfully_approved');
                break;
            case 'approvePendingPage':
                Phpfox::getUserParam('petition.can_approve_petitions', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('petition.process')->approve($iId);
                    $this->remove('#js_petition_entry' . $iId);
                }
                $this->updateCount();
                $sMessage = Phpfox::getPhrase('petition.petition_s_successfully_approved');
                break;
            case 'delete':

                Phpfox::getUserParam('petition.delete_user_petition', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('petition.process')->delete($iId);
                    $this->slideUp('#js_petition_entry' . $iId);
                }
                $sMessage = Phpfox::getPhrase('petition.petition_s_successfully_deleted');
                break;
        }

        $this->alert($sMessage, 'Moderation', 300, 150, true);
        $this->hide('.moderation_process');
    }

    public function inlineDelete()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('petition.process')->deleteInline($this->get('item_id'))) {
            $this->call("$('#js_petition_entry" . $this->get('item_id') . "').hide('slow'); $('#core_js_messages').message('" . Phpfox::getPhrase('petition.petition_deleted', array('phpfox_squote' => true)) . "', 'valid').fadeOut(5000);");
        }
    }


    public function displayDetail()
    {
        $sType = $this->get('sType');
        Phpfox::getBlock('petition.detail', array('sType' => $sType, 'id' => $this->get('id'), 'page' => $this->get('page')));
        if ($sType == 'description') {
            $this->call('$("#petition_comment_block").show();');
        } else {
            $this->call('$("#petition_comment_block").hide();');
        }
        //$this->call('alert("hello")');

        $stringAjax = $this->getContent();

        $stringAjax = str_replace("ajax/php/", "ajax.php", $stringAjax);
        $stringAjax = str_replace("petitiondisplayDetail", "petition.displayDetail", $stringAjax);

        if ($this->get('page') < 2) {

            $this->call('$("#js_details_container").html("' . $stringAjax . '");')->call('$Core.loadInit();');

        } else {
            // problem in page 2 javascript not execute
            //$this->hide('#js_details_container');
            //$this->html('#js_details_container','<script>alert("hello");</script>');
            //$this->call('$Core.loadInit();');
        }


    }

    public function approve()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('petition.process')->approve($this->get('id'))) {
            if ($this->get('inline')) {
                $this->alert(Phpfox::getPhrase('petition.petition_has_been_approved'), Phpfox::getPhrase('petition.petition_approved'), 300, 100, true);
                $this->hide('#js_item_bar_approve_image');
                $this->hide('.js_moderation_off');
                $this->show('.js_moderation_on');
            }
        }
    }

    //P_Check
    public function feature()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('petition.can_feature_petition', true);
        $isAdmin = $this->get('admin');
        if ($isAdmin) {
            if (Phpfox::getService('petition.process')->feature($this->get('petition_id'), $this->get('active'))) {
                return true;
            }
        } else {
            if (Phpfox::getService('petition.process')->feature($this->get('petition_id'), $this->get('type'))) {
                if ($this->get('type') == '1') {
                    $sHtml = '<a href="#" title="' . Phpfox::getPhrase('petition.un_feature_this_petition') . '" onclick="$.ajaxCall(\'petition.feature\', \'petition_id=' . $this->get('petition_id') . '&amp;type=0\'); return false;">' . Phpfox::getPhrase('petition.un_feature') . '</a>';
                } else {
                    $sHtml = '<a href="#" title="' . Phpfox::getPhrase('petition.feature_this_petition') . '" onclick="$.ajaxCall(\'petition.feature\', \'petition_id=' . $this->get('petition_id') . '&amp;type=1\'); return false;">' . Phpfox::getPhrase('petition.feature') . '</a>';
                }

                $this->html('#js_petition_feature_' . $this->get('petition_id'), $sHtml)->alert(($this->get('type') == '1' ? Phpfox::getPhrase('petition.petition_successfully_featured') : Phpfox::getPhrase('petition.petition_successfully_un_featured')));
                if ($this->get('type') == '1') {
                    $this->addClass('#js_petition_entry' . $this->get('petition_id'), 'row_featured_image');
                    $this->call('$(\'#js_petition_entry' . $this->get('petition_id') . '\').find(\'.js_featured_petition:first\').show();');
                } else {
                    $this->removeClass('#js_petition_entry' . $this->get('petition_id'), 'row_featured_image');
                    $this->call('$(\'#js_petition_entry' . $this->get('petition_id') . '\').find(\'.js_featured_petition:first\').hide();');
                }
                return true;
            }
        }
        return false;
    }

    public function directsign()
    {
        Phpfox::isAdmin(true);
        $bIsInline = $this->get('inline');
        $iActive = $this->get('active');
        $iId = $this->get('petition_id');
        if (Phpfox::getService('petition.process')->directsign($iId, $iActive)) {
            if ($bIsInline) {
                if ($iActive) {
                    //Set link to unactive
                    $sHtml = '<a href="#" title="' . Phpfox::getPhrase('petition.unset_direct_sign') . '" onclick="$.ajaxCall(\'petition.directsign\', \'petition_id=' . $iId . '&amp;active=0&amp;inline=true\', \'GET\'); return false;">' . Phpfox::getPhrase('petition.unset_direct_sign') . '</a>';

                } else {
                    //Set link to active
                    $sHtml = '<a href="#" title="' . Phpfox::getPhrase('petition.set_direct_sign') . '" onclick="$.ajaxCall(\'petition.directsign\', \'petition_id=' . $iId . '&amp;active=1&amp;inline=true\', \'GET\'); return false;">' . Phpfox::getPhrase('petition.set_direct_sign') . '</a>';
                }
                $this->html('#js_petition_directsign_' . $iId, $sHtml)->alert(($iActive == '1' ? Phpfox::getPhrase('petition.set_direct_sign_successfully') : Phpfox::getPhrase('petition.unset_direct_sign_successfully')));
            }
            return true;
        }
        return false;
    }

    public function getNew()
    {
        Phpfox::getBlock('petition.new');

        $this->html('#' . $this->get('id'), $this->getContent(false));
        $this->call('$(\'#' . $this->get('id') . '\').parents(\'.block:first\').find(\'.bottom li a\').attr(\'href\', \'' . Phpfox::getLib('url')->makeUrl('petition') . '\');');
    }

    public function helpOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');

        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'petition_help',
                'key' => 'help_id',
                'values' => $aVals['ordering']
            )
        );
    }
}

?>