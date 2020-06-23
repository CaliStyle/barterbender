<?php

defined('PHPFOX') or exit('NO DICE!');

class contactimporter_Component_Controller_InviteUser extends Phpfox_Component
{

    public function process()
    {
        $iID = $this->request()->get('id');

        if ($iID > 0) {
            $sEmail = phpfox::getLib('database')->select('email')->from(phpfox::getT('user'), 'u')->where('u.user_id = ' . (int)phpfox::getLib('database')->escape($iID))->execute('getSlaveField');
            if (empty($sEmail)) {
                $this->url()->send('');
            } else {
                $iInvite = Phpfox::getService('invite.process')->addInvite(_p('anonymous_user'), $iID);
                Phpfox::getService('contactimporter')->updateStatistic($iID, 1, 0, 'manual');
                $sLink = Phpfox::getLib('url')->makeUrl('invite', array('id' => $iInvite));
                $this->url()->send($sLink);
            }
        } else {
            $this->url()->send('');
        }
        $this->template()->setPhrase(array(
            'contactimporter.are_you_sure_you_want_to_delete',
            'contactimporter.are_you_sure_you_want_to_delete_this_action_will_delete_all_feeds_belong_to',
            'contactimporter.you_can_send',
            'contactimporter.invitations_per_time',
            'contactimporter.you_have_selected',
            'contactimporter.contacts',
            'contactimporter.select_current_page',
            'contactimporter.unselect_current_page',
            'contactimporter.your_email_is_empty',
            'contactimporter.this_mail_domain_is_not_supported',
            'contactimporter.email_should_not_be_left_blank',
            'contactimporter.no_contacts_were_selected',
            'contactimporter.updating'
        ));
    }

}

?>