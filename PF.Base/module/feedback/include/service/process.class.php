<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          3.01p6
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php

class FeedBack_Service_Process extends Phpfox_Service {

    /**
     * Class constructor
     */
    public function __construct() {
        $this->_sTable = Phpfox::getT('feedback');
        $this->_aLanguages = Phpfox::getService('language')->getAll();
    }

    public function add($aVals) {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__start')) ? eval($sPlugin) : false);
        if (phpfox::isUser()) {
            $aUser = phpfox::getLib('database')->select('u.email, u.full_name')
                    ->from(phpfox::getT('user'), 'u')
                    ->where('u.user_id = ' . phpfox::getUserId())
                    ->execute('getRow');
            if (!empty($aUser)) {
                $aVals['email'] = $aUser['email'];
                $aVals['full_name'] = $aUser['full_name'];
            }
        }
        $oFilter = Phpfox::getLib('parse.input');
        $sTitle = $oFilter->clean(strip_tags($aVals['title']), 255);
        $sUserId = Phpfox::getUserId();
        $privacy = (isset($aVals['privacy']) &&  $aVals['privacy'] == 2 ? 3 : 0);
        $aInsert = array(
            'user_id' => (isset($sUserId) ? $sUserId : '0'),
            'feedback_category_id' => (isset($aVals['category_id']) ? $aVals['category_id'] : 0),
            'feedback_serverity_id' => (isset($aVals['serverity_id']) ? $aVals['serverity_id'] : '0'),
            'feedback_status_id' => 0,
            'title' => $sTitle,
            'title_url' => Phpfox::getService('feedback')->prepareTitle1($aVals['title']),
            'feedback_description' => $oFilter->clean(strip_tags($aVals['description'])),
            'time_stamp' => PHPFOX_TIME,
            'privacy' => isset($aVals['privacy']) ? $aVals['privacy'] : 1,
            'full_name' => (isset($aVals['full_name']) ? $aVals['full_name'] : ''),
            'email' => (isset($aVals['email']) ? $aVals['email'] : ''),
            'date_modify' => PHPFOX_TIME,
            'is_approved' => (int)Phpfox::getUserParam('feedback.approve_feedbacks'),
        );

        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_add_start')) ? eval($sPlugin) : false);
        $iId = $this->database()->insert(Phpfox::getT('feedback'), $aInsert);
        if ($aVals['privacy'] == 1 && Phpfox::getUserId() && $aInsert['is_approved']) {
            Phpfox::getService('feed.process')->add('feedback', $iId, $privacy);
        }
        if (phpfox::getUserId() && $aInsert['is_approved']) {
            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'feedback');
        }

        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description'])) {
            Phpfox::getService('tag.process')->update('feedback', $iId, Phpfox::getUserId(), $aVals['description'], true);
        }

        //support tag
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_tag_support') && isset($aVals['tag_list']) && !empty($aVals['tag_list'])) {
            Phpfox::getService('tag.process')->add('feedback', $iId, Phpfox::getUserId(), $aVals['tag_list']);
        }

        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__end')) ? eval($sPlugin) : false);

        return array($iId, $sTitle);
    }

    public function addCategory($aVals) {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__start')) ? eval($sPlugin) : false);
        $oFilter = Phpfox::getLib('parse.input');
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_'.$aLanguages[0]['language_id']];
        $phrase_var_name = 'feedback_category_' . md5('Feedback Category'. $name . PHPFOX_TIME);

        $aText = [];

        foreach ($aLanguages as $aLanguage){
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
            }
            else {
                return Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
            }
            if (strlen($aVals['name_' . $aLanguage['language_id']]) > 40) {
                return Phpfox_Error::set(_p('category_language_name_name_must_be_less_than_limit', ['limit' => 40, 'language_name' => $aLanguage['title']]));
            }
        }
        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];
        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_addCategory_start')) ? eval($sPlugin) : false);
        $iId = $this->database()->insert(Phpfox::getT('feedback_category'), array(
                                              'name' => $finalPhrase,
                                              'name_url' => $finalPhrase,
                                              'description' => $oFilter->clean($aVals['description']),
                                              'time_stamp' => PHPFOX_TIME
                                                                          )
        );
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__end')) ? eval($sPlugin) : false);
        return $iId;
    }

    public function addServerity($aVals, $sName = 'name') {
        $finalPhrase = $this->addPhrase($aVals, $sName);
        $oFilter = Phpfox::getLib('parse.input');
        $aInsert = array(
            'name' => $finalPhrase,
            'name_url' => Phpfox::getService('feedback')->prepareTitle($finalPhrase),
            'description' => (isset($aVals['description']) ? $oFilter->clean(strip_tags($aVals['description'])) : ''),
            'time_stamp' => PHPFOX_TIME,
            'colour' => (isset($aVals['colour']) ? $aVals['colour'] : '195B85')
        );
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_addserverity_start')) ? eval($sPlugin) : false);
        $iId = $this->database()->insert(Phpfox::getT('feedback_serverity'), $aInsert);
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__end')) ? eval($sPlugin) : false);
        $this->cache()->remove();
        return $iId;
    }

    protected function addPhrase($aVals, $sName = 'name', $bVerify = true)
    {
        $langId =  current($this->_aLanguages)['language_id'];
        $aFirstLang = end($this->_aLanguages);

        //Add phrases
        $aText = [];
        //Verify name

        foreach ($this->_aLanguages as $aLanguage) {
            if (isset($aVals[$sName . '_' . $aLanguage['language_id']]) && !empty($aVals[$sName . '_' . $aLanguage['language_id']])) {
                $aText[$aLanguage['language_id']] = $aVals[$sName . '_' . $aLanguage['language_id']];
            }elseif(isset($aVals[$sName . '_' . $langId]) && !empty($aVals[$sName . '_' . $langId])){
                $aText[$aLanguage['language_id']] = $aVals[$sName . '_' . $langId];
            } elseif ($bVerify) {
                return Phpfox_Error::set(_p('provide_a_language_name_label',
                    ['language_name' => $aLanguage['title'],'label' => $sName]));
            } else {
                $bReturnNull = true;
            }
        }
        if (isset($bReturnNull) && $bReturnNull) {
            //If we don't verify value, phrase can't be empty. Return null for this case.
            return null;
        }
        $name = $aVals[$sName . '_' . $aFirstLang['language_id']];
        $phrase_var_name = 'Feedback Serverity' . md5($name . PHPFOX_TIME);

        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        return $finalPhrase;
    }

    public function addStatus($aVals) {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__start')) ? eval($sPlugin) : false);
        $oFilter = Phpfox::getLib('parse.input');

        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_'.$aLanguages[0]['language_id']];
        $phrase_var_name = 'feedback_status_' . md5('Feedback Status'. $name . PHPFOX_TIME);

        $aText = [];

        foreach ($aLanguages as $aLanguage){
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
            }
            else {
                return Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
            }
        }
        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];
        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);

        $aInsert = array(
            'name' => $finalPhrase,
            'description' => (isset($aVals['description']) ? $oFilter->clean($aVals['description']) : ''),
            'time_stamp' => PHPFOX_TIME,
            'colour' => (isset($aVals['colour']) ? $aVals['colour'] : '195B85')
        );
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_addstatus_start')) ? eval($sPlugin) : false);
        $iId = $this->database()->insert(Phpfox::getT('feedback_status'), $aInsert);
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__end')) ? eval($sPlugin) : false);
        return $iId;
    }

    public function uploadPicture($aVals) {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__start')) ? eval($sPlugin) : false);
        $oFilter = Phpfox::getLib('parse.input');
        $sTitle = $oFilter->clean($aVals['file_name'], 255);
        $aInsert = array(
            'file_name' => $sTitle,
            'picture_path' => (isset($aVals['picture_path']) ? $aVals['picture_path'] : ''),
            'thumb_url' => (isset($aVals['thumb_url']) ? $aVals['thumb_url'] : ''),
            'file_size' => (isset($aVals['filesize']) ? $aVals['filesize'] : '0'),
            'feedback_id' => (isset($aVals['feedback_id']) ? $aVals['feedback_id'] : '0'),
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
        );
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_uploadpicture_start')) ? eval($sPlugin) : false);
        $iId = $this->database()->insert(Phpfox::getT('feedback_picture'), $aInsert);
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__end')) ? eval($sPlugin) : false);
        return $iId;
    }

    public function updateCategory($aVals) {
        $aLanguages = Phpfox::getService('language')->getAll();
        if (\Core\Lib::phrase()->isPhrase($aVals['name'])){
            $finalPhrase = $aVals['name'];
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    if (strlen($aVals['name_' . $aLanguage['language_id']]) > 40) {
                        return Phpfox_Error::set(_p('category_language_name_name_must_be_less_than_limit', ['limit' => 40, 'language_name' => $aLanguage['title']]));
                    }
                    $name = $aVals['name_' . $aLanguage['language_id']];
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
                }
                else {
                    return Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
                }
            }
        }
        else {
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'feedback_category_' . md5('Feedback Category' . $name . PHPFOX_TIME);
            //Add phrases
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }

                if (strlen($aVals['name_' . $aLanguage['language_id']]) > 40) {
                    return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => 40, 'language_name' => $aLanguage['title']]));
                }
            }

            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];

            $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);

        }

        $iUpdate = $this->database()->update(Phpfox::getT('feedback_category'), array('name' => $finalPhrase,'name_url' => $finalPhrase, 'description' => Phpfox::getLib('parse.input')->clean($aVals['description']),'time_stamp' => PHPFOX_TIME), 'category_id = ' . (int)$aVals['category_id']);

        return $iUpdate;
    }

    public function updateServerity($aVals) {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__start')) ? eval($sPlugin) : false);
        $oFilter = Phpfox::getLib('parse.input');

        $aLanguages = Phpfox::getService('language')->getAll();
        if (\Core\Lib::phrase()->isPhrase($aVals['name'])){
            $finalPhrase = $aVals['name'];
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    $name = $aVals['name_' . $aLanguage['language_id']];
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
                }
                else {
                    return Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
                }
            }
        }
        else {
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'feedback_serverity_' . md5('Feedback Serverity' . $name . PHPFOX_TIME);
            //Add phrases
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }
            }

            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];

            $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        }

        $aUpdate = array(
            'name' => $finalPhrase,
            'name_url' => Phpfox::getService('feedback')->prepareTitle($aVals['name']),
            'description' => (isset($aVals['description']) ? $oFilter->clean($aVals['description']) : ''),
            'colour' => (isset($aVals['colour']) ? $aVals['colour'] : '195B85'),
            'time_stamp' => PHPFOX_TIME
        );
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_updateserverity_start')) ? eval($sPlugin) : false);
        $isUpdate = $this->database()->update(Phpfox::getT('feedback_serverity'), $aUpdate, 'serverity_id=' . (int) $aVals['serverity_id']);
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__end')) ? eval($sPlugin) : false);
        return $isUpdate;
    }

    public function updateStatusAdmin($aVals) {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__start')) ? eval($sPlugin) : false);
        $oFilter = Phpfox::getLib('parse.input');

        $aLanguages = Phpfox::getService('language')->getAll();
        if (\Core\Lib::phrase()->isPhrase($aVals['name'])){
            $finalPhrase = $aVals['name'];
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    $name = $aVals['name_' . $aLanguage['language_id']];
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
                }
                else {
                    return Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
                }
            }
        }
        else {
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'feedback_status_' . md5('Feedback Status' . $name . PHPFOX_TIME);
            //Add phrases
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }
            }

            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];

            $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        }


        $aUpdate = array(
            'name' => $finalPhrase,
            'description' => (isset($aVals['description']) ? $oFilter->clean(strip_tags($aVals['description'])) : ''),
            'colour' => (isset($aVals['colour']) ? $aVals['colour'] : '195B85'),
            'time_stamp' => PHPFOX_TIME
        );
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_updatestatus_start')) ? eval($sPlugin) : false);
        $isUpdate = $this->database()->update(Phpfox::getT('feedback_status'), $aUpdate, 'status_id=' . (int) $aVals['status_id']);
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process__end')) ? eval($sPlugin) : false);
        return $isUpdate;
    }

    public function update($aVals) {

        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_update__start')) ? eval($sPlugin) : false);
        $oFilter = Phpfox::getLib('parse.input');
        $sTitle = $oFilter->clean(strip_tags($aVals['title']), 255);
        $sUserId = Phpfox::getUserId();

        $privacy = isset($aVals['privacy']) && $aVals['privacy'] == 2 ? 3 : 0;
        $aUpdate = array(
            'feedback_category_id' => (isset($aVals['category_id']) ? $aVals['category_id'] : 0),
            'feedback_serverity_id' => (isset($aVals['serverity_id']) ? $aVals['serverity_id'] : '0'),
            'title' => $sTitle,
            'feedback_description' => (isset($aVals['description']) ? $oFilter->clean(strip_tags($aVals['description'])) : ''),
            'time_stamp' => PHPFOX_TIME,
            'privacy' => isset($aVals['privacy']) ? $aVals['privacy'] : 1,
            'date_modify' => PHPFOX_TIME
        );
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_update')) ? eval($sPlugin) : false);
        $this->database()->update(Phpfox::getT('feedback'), $aUpdate, 'feedback_id = ' . (int) $aVals['feedback_id']);
        $aFeedBack = Phpfox::getService('feedback')->getFeedBackForEdit($aVals['feedback_id']);

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('feedback', $aFeedBack['feedback_id'], $privacy, 0, 0, $aFeedBack['user_id']) : null);

        $isUpdate = Phpfox::getLib('database')->select('fb.title_url')
                ->from(Phpfox::getT('feedback'), 'fb')
                ->where('fb.feedback_id = ' . (int) $aVals['feedback_id'])
                ->execute('getSlaveField');

        $iId = $aVals['feedback_id'];
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description'])) {

            Phpfox::getService('tag.process')->update('feedback', $iId, Phpfox::getUserId(), $aVals['description'], true);
        }
        //update tag
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_tag_support')) {
            Phpfox::getService('tag.process')->update('feedback', $iId, Phpfox::getUserId(), $aVals['tag_list']);
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_update__end')) ? eval($sPlugin) : false);
        return $isUpdate;
    }

    public function updateDateModify($feedback_id) {
        if ($feedback_id == null) {
            return false;
        }
        $aUpdate = array(
            'date_modify' => PHPFOX_TIME
        );
        $isUpdate = $this->database()->update(Phpfox::getT('feedback'), $aUpdate, 'feedback_id = ' . (int) $feedback_id);
        return $isUpdate;
    }

    public function updateStatus($aVals) {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_updatestatus__start')) ? eval($sPlugin) : false);
        $oFilter = Phpfox::getLib('parse.input');
        $aUpdate = array(
            'feedback_status_id' => (isset($aVals['status_id']) ? $aVals['status_id'] : '0'),
            'feedback_status' => (isset($aVals['description']) ? $oFilter->clean($aVals['description']) : '')
        );
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_updatestatus')) ? eval($sPlugin) : false);
        $isUpdate = $this->database()->update(Phpfox::getT('feedback'), $aUpdate, 'feedback_id = ' . (int) $aVals['feedback_id']);
        $this->updateDateModify($aVals['feedback_id']);
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_update__end')) ? eval($sPlugin) : false);
        return $isUpdate;
    }

    public function delete($iId) {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_delete__start')) ? eval($sPlugin) : false);
        $iUserId = Phpfox::getService('feedback')->hasAccess($iId, 'delete_own_feedback', 'delete_user_feedback');

        if ($iUserId !== false) {
            $aFeedBack = Phpfox::getService('feedback')->getFeedBackForEdit($iId);

            $this->database()->delete(Phpfox::getT('feedback'), "feedback_id = " . (int) $iId);
            $this->database()->delete(Phpfox::getT('feedback_vote'), "feedback_id = " . (int) $iId);
            (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem($aFeedBack['user_id'], $iId, 'feedback') : null);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('feedback', (int) $iId) : null);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_feedback', $iId) : null);
            if (phpfox::isModule('notification')) {
                phpfox::getLib('database')->delete(phpfox::getT('notification'), 'type_id = "comment_feedfeedback" and item_id = ' . $iId);
                phpfox::getLib('database')->delete(phpfox::getT('notification'), 'type_id = "comment_feedfeedback_tag" and item_id = ' . $iId);
                phpfox::getLib('database')->delete(phpfox::getT('notification'), 'type_id = "feedback_like" and item_id = ' . $iId);
            }
            Phpfox::getService('user.activity')->update($aFeedBack['user_id'], 'feedback', '-');
            return true;
        }
        return false;
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_delete')) ? eval($sPlugin) : false);
    }

    public function deleteCategory($category_id) {

        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_deletecategory__start')) ? eval($sPlugin) : false);
        $aCat = Phpfox::getService('feedback')->getFeedBackCatForEdit($category_id);
        $aFeedBacks = Phpfox::getService('feedback')->getFeedBackByCategoryId($category_id);
        if (count($aCat) > 0) {
            $this->database()->delete(Phpfox::getT('feedback_category'), "category_id = " . (int) $category_id);
            $this->database()->update(Phpfox::getT('feedback'), array('feedback_category_id' => 0), "feedback_category_id = " . (int) $category_id);
            return _p($aCat['name']);
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_deletecategory')) ? eval($sPlugin) : false);
        return false;
    }

    public function deleteServerity($serverity_id) {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_deleteserverity__start')) ? eval($sPlugin) : false);
        $aSer = phpfox::getLib('phpfox.database')->select('*')
                ->from(Phpfox::getT('feedback_serverity'))
                ->where('serverity_id=' . $serverity_id)
                ->execute('getRow');
        $aFeedBacks = phpfox::getLib('phpfox.database')->select('*')
                ->from(Phpfox::getT('feedback'))
                ->where('feedback_serverity_id=' . $serverity_id)
                ->execute('getRow');
        if (count($aSer) > 0) {
            $this->database()->delete(Phpfox::getT('feedback_serverity'), "serverity_id = " . (int) $serverity_id);
            $this->database()->update(Phpfox::getT('feedback'), array('feedback_serverity_id' => 0), "feedback_serverity_id = " . (int) $serverity_id);
            return _p($aSer['name']);
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_deleteserverity')) ? eval($sPlugin) : false);
        return false;
    }

    public function deleteStatus($status_id) {
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_deletestatus__start')) ? eval($sPlugin) : false);
        $aStatus = phpfox::getLib('phpfox.database')->select('*')
                ->from(Phpfox::getT('feedback_status'))
                ->where('status_id=' . $status_id)
                ->execute('getRow');
        $aFeedBacks = phpfox::getLib('phpfox.database')->select('*')
                ->from(Phpfox::getT('feedback'))
                ->where('feedback_status_id=' . $status_id)
                ->execute('getRows');
        if (count($aStatus) > 0) {
            $this->database()->delete(Phpfox::getT('feedback_status'), "status_id = " . (int) $status_id);
            $this->database()->update(Phpfox::getT('feedback'), array('feedback_status_id' => 0, 'feedback_status' => NULL), "feedback_status_id = " . (int) $status_id);
            return _p($aStatus['name']);
        }
        (($sPlugin = Phpfox_Plugin::get('feedback.service_process_deletestatus')) ? eval($sPlugin) : false);
        return false;
    }

    public function updateView($iId) {
        $this->database()->query("
            UPDATE " . $this->_sTable . "
            SET total_view = total_view + 1
            WHERE feedback_id = " . (int) $iId . "
            ");

        return true;
    }

    public function updateTotalPicture($iId) {
        $this->database()->query("
            UPDATE " . $this->_sTable . "
            SET total_attachment = total_attachment + 1
            WHERE feedback_id = " . (int) $iId . "
            ");

        return true;
    }

    public function updateTotalPictureDel($iId) {
        $this->database()->query("
            UPDATE " . $this->_sTable . "
            SET total_attachment = total_attachment - 1
            WHERE feedback_id = " . (int) $iId . "
            ");
        return true;
    }

    public function updateCounter($iId, $bMinus = false) {
        $this->database()->query("
            UPDATE " . $this->_sTable . "
            SET total_comment = total_comment " . ($bMinus ? "-" : "+") . " 1
            WHERE feedback_id = " . (int) $iId . "
            ");
    }

    public function approve($iId) {
        Phpfox::getUserParam('feedback.can_approve_feedbacks', true);

        $aFeedback = $this->database()->select('fb.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('feedback'), 'fb')
                ->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fb.user_id')
                ->where('fb.feedback_id = ' . (int) $iId)
                ->execute('getRow');

        if (!isset($aFeedback['feedback_id'])) {
            return Phpfox_Error::set(_p('feedback.the_feedback_you_are_trying_to_approve_is_not_valid'));
        }

        if ($aFeedback['is_approved'] == '1') {
            return false;
        }

        $this->database()->update(Phpfox::getT('feedback'), array('is_approved' => '1', 'time_stamp' => PHPFOX_TIME), 'feedback_id = ' . $aFeedback['feedback_id']);

        if (Phpfox::isModule('feed') && $aFeedback['user_id']) {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('feedback', $iId, $aFeedback['privacy'] == 2 ? 3 : 0, 0, 0, $aFeedback['user_id']) : null);
        }

        if (Phpfox::isModule('notification') && $aFeedback['user_id']) {
            Phpfox::getService('notification.process')->add('feedback_approved', $aFeedback['feedback_id'], $aFeedback['user_id']);
        }

        if ($aFeedback['user_id']) {
            Phpfox::getService('user.activity')->update($aFeedback['user_id'], 'feedback');
        }

        // Send the user an email
        if ($aFeedback['user_id']) {
            $sEmail = phpfox::getLib('database')->select('u.email')
                    ->from(phpfox::getT('user'), 'u')
                    ->where('u.user_id = ' . $aFeedback['user_id'])
                    ->execute('getSlaveField');
            $sLink = Phpfox::getLib('url')->permalink('feedback.detail', $aFeedback['title_url']);
            Phpfox::getLib('mail')->to($sEmail)
                    ->subject(array('feedback.your_feedback_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
                    ->message(array('feedback.your_feedback_has_been_approved_on_site_title_message', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
                    //->notification('blog.blog_is_approved')
                    ->send();
        } else if ($aFeedback['user_id'] == 0) {
            $sLink = Phpfox::getLib('url')->permalink('feedback.detail', $aFeedback['title_url']);
            Phpfox::getLib('mail')->to($aFeedback['email'])
                    ->subject(array('feedback.your_feedback_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
                    ->message(array('feedback.your_feedback_has_been_approved_on_site_title_message', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
                    //->notification('blog.blog_is_approved')
                    ->send();
        }

        return true;
    }

    public function __call($sMethod, $aArguments) {
        if ($sPlugin = Phpfox_Plugin::get('feedback.service_process__call')) {
            return eval($sPlugin);
        }

        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    function numberAbbreviation($number) {
        if($number > 0)
        {
            $abbrevs = array(12 => "T", 9 => "B", 6 => "M", 3 => "K", 0 => "");
            foreach($abbrevs as $exponent => $abbrev) {
                if($number >= pow(10, $exponent)) {
                    $display_num = $number / pow(10, $exponent);
                    $decimals = ($exponent >= 3 && round($display_num) < 100) ? 1 : 0;
                    return number_format($display_num,$decimals) . $abbrev;
                }
            }
        }
        else{
            return $number;
        }
    }

}

?>