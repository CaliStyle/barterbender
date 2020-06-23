<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Detail extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iLimit = 1000;
        $iTotal = 0;
        $iId = $this->getParam('id');
        $iPage = $this->getParam('page') ? $this->getParam('page') : 1;

        $aPetition = Phpfox::getService('petition')->getPetition($iId);
        $sType = $this->getParam('sType');
        if ($sType == 'signatures') {

            list($iTotal, $aSignatures) = Phpfox::getService('petition')->getSignatures($aPetition['petition_id'], $iPage * $iLimit);
            $aPetition['signatures'] = $aSignatures;
        } else if ($sType == 'news') {
            $aValidation = array(
                'news_headline' => array(
                    'def' => 'required',
                    'title' => 'Please fill in news headline'
                ),
                'news_content' => array(
                    'def' => 'required',
                    'title' => 'Please fill in news content'
                )
            );

            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'js_form_news',
                    'aParams' => $aValidation
                )
            );

            list($iTotal, $aNews) = Phpfox::getService('petition')->getNews($iId, $iPage * $iLimit);
            $aPetition['news'] = $aNews;

            $sCheckFormNewsLink = "<script type=\"text/javascript\">
						   function checkFormNewsLink()
                                       {
                                          var sLink = $('#news_link').val();
                                          if($.trim(sLink).length == 0)
                                             return true;
                                          if ($.trim(sLink).search(/(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/) == -1)
                                          {
                                             $('#js_form_news_msg').message('" . Phpfox::getPhrase('petition.please_provide_a_valid_url') . "', 'error');
                                             $('#news_link').addClass('alert_input');
                                             return false;
                                          }
                                          return true;
                                       }
                                       </script>";

            $this->template()->assign(array(
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'sCheckFormNewsLink' => $sCheckFormNewsLink
            ));
        }

        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iTotal, 'ajax' => 'petition.displayDetail', 'aParams' => array('id' => $iId, 'sType' => $sType)));

        $this->template()->assign(array(
            'aPetition' => $aPetition,
            'sType' => $sType,
            'iPage' => $iPage
        ));
        if (empty($aPetition['letter'])) {
            $aMenus = array(
                Phpfox::getPhrase('petition.description') => '#petition.displayDetail?sType=description&id=' . $aPetition['petition_id'],
                Phpfox::getPhrase('petition.signatures') => '#petition.displayDetail?sType=signatures&id=' . $aPetition['petition_id'],
                Phpfox::getPhrase('petition.news') => '#petition.displayDetail?sType=news&id=' . $aPetition['petition_id']
            );
        } else {
            $aMenus = array(
                Phpfox::getPhrase('petition.description') => '#petition.displayDetail?sType=description&id=' . $aPetition['petition_id'],
                Phpfox::getPhrase('petition.petition_letter') => '#petition.displayDetail?sType=letter&id=' . $aPetition['petition_id'],
                Phpfox::getPhrase('petition.signatures') => '#petition.displayDetail?sType=signatures&id=' . $aPetition['petition_id'],
                Phpfox::getPhrase('petition.news') => '#petition.displayDetail?sType=news&id=' . $aPetition['petition_id']
            );
        }


        $this->template()->assign(array(
            'corepath' => phpfox::getParam('core.path'),
            'sHeader' => '',
            'aMenus' => $aMenus
        ));
        return 'block';
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('petition.component_block_detail_clean')) ? eval($sPlugin) : false);
    }
}

?>