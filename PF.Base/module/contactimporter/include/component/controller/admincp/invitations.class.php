<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php

class Contactimporter_Component_Controller_Admincp_Invitations extends Phpfox_Component
{

    public function process()
    {
        $aFilters = array(
            'title' => array(
                'type' => 'input:text',
                'search' => " pi.email LIKE '%[VALUE]%' OR pu.user_name  LIKE '%[VALUE]%' OR pu.full_name  LIKE '%[VALUE]%'"
            ),
        );
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'invite_email',
            'filters' => $aFilters,
            'search' => 'search'
                )
        );
        if ($this->request()->get('delete_select'))
        {
            $arr_select = $this->request()->get('arr_selected');
            $arr = explode(",", $arr_select);
            if (is_array($arr) && !empty($arr))
            {
                Phpfox::getService('contactimporter.process')->deleteInvitation($arr);
            }
        }
        if ($this->request()->get('del'))
        {
            $iInvite = $this->request()->get('del');            
            list($iSocial, $iEmail) = Phpfox::getService('contactimporter.process')->deleteInvitation((array) $iInvite);                
            if ($iSocial > 0 || $iEmail > 0)
            {
                $this->url()->send('admincp.contactimporter.invitations', null, _p('invite.invitation_deleted'));
            }
            else{
                $this->url()->send('admincp.contactimporter.invitations', null, _p('invite.invitation_not_found'));
            }
        }
        $iPage = $this->request()->getInt('page');
        $iPageSize = 20;
        list($iCnt, $items) = phpfox::getService('contactimporter')->getAllEmailInvitations($oSearch->getConditions(), "", $oSearch->getPage(), $iPageSize);
        $this->template()->setHeader(array('contactimporter.js' => 'module_contactimporter'));
        $this->template()->assign(array('emails' => isset($emails), 'core_url' => phpfox::getParam('core.path')));

        //echo $iPage;
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $oSearch->getSearchTotal($iCnt)));

        $this->template()->assign(array('items' => $items, 'iCnt' => $iCnt, 'iPage' => $iPage))
                ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                ))
                ->setPhrase(array('contactimporter.are_you_sure_you_want_to_delete', 'contactimporter.are_you_sure_you_want_to_delete_this_action_will_delete_all_feeds_belong_to', 'contactimporter.you_can_send', 'contactimporter.invitations_per_time', 'contactimporter.you_have_selected', 'contactimporter.contacts', 'contactimporter.select_current_page', 'contactimporter.unselect_current_page', 'contactimporter.your_email_is_empty', 'contactimporter.this_mail_domain_is_not_supported', 'contactimporter.email_should_not_be_left_blank', 'contactimporter.no_contacts_were_selected', 'contactimporter.updating'
                ));
        ;
        $this->template()
            ->setBreadCrumb(_p('Apps'),$this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_contactimporter'),$this->url()->makeUrl('admincp.app',['id' => '__module_contactimporter']))
            ->setBreadCrumb(_p('invitations_list'), $this->url()->makeUrl('admincp.contactimporter.invitations'));
    }

}

?>