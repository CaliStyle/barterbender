<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class FeedBack_Component_Block_EditFeedBack extends Phpfox_Component {

    public function isValid($aVals) {
        $errors = "";
        if (!isset($aVals['title']) || ($aVals['title'] == ''))
            $errors .= _p('Title can\'t be empty. <br />');
        if (empty($aVals['description']))
            $errors .= _p('Description of feedback can\'t be empty.');
        return $errors;
    }

    public function process() {
        $feedback_id = $this->getParam('feedback_id');
        $aFeedBack = Phpfox::getService('feedback')->getFeedBackById($feedback_id);
        $aCats = phpfox::getLib('database')->select('*')->from(phpfox::getT('feedback_category'), 'fc')->where(1)->execute('getRows');
        foreach ($aCats as $k => $aCat) {
            $aCats[$k]['name'] = Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aCat['name']) ? _p($aCat['name']) : $aCat['name']);
        }
        $aSers = phpfox::getLib('database')->select('*')->from(phpfox::getT('feedback_serverity'), 'fs')->where(1)->execute('getRows');
        foreach ($aSers as $key => $aSer) {
            $aSer['name'] = Phpfox::getLib('locale')->convert($aSer['name']);
            $aSers[$key] = $aSer;
        }

        if (Phpfox::isModule('tag')) {
            $aTags = Phpfox::getService('tag')->getTagsById('feedback', $feedback_id);
            if (isset($aTags[$feedback_id])) {
                $aFeedBack['tag_list'] = '';
                foreach ($aTags[$feedback_id] as $aTag) {
                    $aFeedBack['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                }
                $aFeedBack['tag_list'] = trim(trim($aFeedBack['tag_list'], ','));
                $aForms['tag_list'] = $aFeedBack['tag_list'];
                $this->template()->assign(array(
                    'aForms' => $aForms
                ));
            }
        }

        $this->template()->assign(array(
            'aFeedBack' => $aFeedBack,
            'aCats' => $aCats,
            'aSers' => $aSers
        ));
    }

}

?>
