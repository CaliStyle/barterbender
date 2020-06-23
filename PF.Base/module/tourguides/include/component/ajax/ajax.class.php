<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class TourGuides_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function completetour()
    {
        $iUserId = phpfox::getUserId();
        $iTourId = $this->get('id');
        if ($iUserId > 0 && $iTourId > 0)
        {
            $aUserSetting = phpfox::getService('tourguides.user')->getUserSetting($iUserId, $iTourId);
            if (isset($aUserSetting['user_id']) && $aUserSetting['user_id'] > 0)
            {
                $aParams = array('no_ask' => 1);
                phpfox::getService('tourguides.user')->updateUserSetting($iUserId, $iTourId, $aParams);
            }
            else
            {
                $aParams = array(
                    'no_ask' => 1,
                    'user_id' => $iUserId,
                    'tour_id' => $iTourId,
                    );
                phpfox::getService('tourguides.user')->addUserSetting($aParams);
            }
        }
    }

    public function getObject()
    {
        phpfox::getLib('session')->remove('yntour_current_selected');
        $iUserId = phpfox::getUserId();
        $sCurrentUrl = $this->get('url');
        $sTourController = $this->get('sTourController');
        $sCurrentUrl = phpfox::getService('tourguides')->getRealURL($sCurrentUrl);

        $aParams = array(
            'sCurrentUrl' => $sCurrentUrl,
            'sControllerName' => $sTourController,
            );

        $sHTML = phpfox::getService('tourguides')->showTourAjaxMode($iUserId, $aParams);
        $sHTML['success'] = true;
        $sHTML = json_encode($sHTML);
        echo $sHTML;
    }
    
    public function getcontroler()
    {
        $sUrl = $this->get('data');
        if ($sUrl)
        {

            $oOutput = Phpfox::getLib('request')->send($sUrl, array('yntour' => 'getcontroller', Phpfox::getTokenName() . '[security_token]' => Phpfox::getService('log.session')->getToken()), 'POST', $_SERVER['HTTP_USER_AGENT']);
            preg_match("/\[controller\](.*)\[\/controller\]/i", $oOutput, $aMatches);
            if (isset($aMatches[1]) && $aMatches[1] != "")
            {
                $aReturnData = array(
                    'success' => true,
                    'controller' => $aMatches[1],
                    );
            }
            else
            {
                $aReturnData = array(
                    'success' => false,
                    'message' => _p('tourguides.fail_to_get_controller_from_this_url'),
                    );
            }
            echo json_encode($aReturnData);

        }
    }
    
    public function loginbackAdmin()
    {
        $aData = $this->getAll();
        $aSession = phpfox::getLib('session')->get('yntour_current_selected');
        if (isset($aData['sessionid']) && isset($aSession['sessionid']) && $aData['sessionid'] == $aSession['sessionid'])
        {
            phpfox::getService('user.auth')->login($aSession['current_user'], '', false, 'email', true);
            $aResult = array('admincp_url_return' => $aSession['admincp_url_return'] . '/com_1/', 'success' => true);                    
            echo json_encode($aResult);
        }
    }
    
    public function editstep()
    {
        $iStep = (int)$this->get('id');
        $iOrder = (int)$this->get('step');
        if ($iStep > 0)
        {
            phpfox::getBlock('tourguides.editstep', array('id' => $iStep, 'iOrder' => $iOrder));
        }
    }
    
    public function onaddnewstep()
    {
        phpfox::getLib('session')->remove('yntour_current_selected');
        Phpfox::getUserParam('tourguides.can_create_tour_guide', true);
        $iTourId = (int)$this->get('data');
        if ($iTourId > 0)
        {
            $aTour = phpfox::getService('tourguides')->getTourById($iTourId);
            $aTour['sessionid'] = session_id();
            $aTour['current_user'] = phpfox::getUserBy('email');
            $aTour['admincp_url_return'] = phpfox::getLib('url')->makeUrl('admincp.tourguides.index', array('id' => $aTour['id'], 'ss' => session_id()));
            $aTour['call_from'] = 'admincp';
            $aTour['user_id'] = phpfox::getUserId();
            $aSteps = phpfox::getService('tourguides.steps')->getSteps($aTour['id']);
            $aTour['total_steps'] = count($aSteps);
            $aTour['aSteps'] = $aSteps;
            phpfox::getLib('session')->set('yntour_current_selected', $aTour);
            echo json_encode($aTour);
            return;
        }
        echo json_encode(array());
    }
    
    public function saveEditStep()
    {

        Phpfox::getUserParam('tourguides.can_create_tour_guide', true);
        $aData = $this->getAll();
        $oServiceTourGuide = phpfox::getService('tourguides');
        $oServiceSteps = phpfox::getService('tourguides.steps');
        $aStepData = "";
        if (isset($aData['data']))
        {
            parse_str($aData['data'], $aStepData);
        }

        $aResults = array();
        $aResults['tour_tourguide_id'] = isset($aStepData['tour_tourguide_id']) ? $aStepData['tour_tourguide_id'] : 0;
        $aResults['id'] = isset($aStepData['id']) ? $aStepData['id'] : 0;

        if ($aResults['tour_tourguide_id'] > 0 && $aResults['id'] > 0)
        {
            $sStepElement = isset($aStepData['tour_element']) ? $aStepData['tour_element'] : "main_core_body_holder";
            $sStepElement = str_replace('.yntour_mouse_active', '', $sStepElement);
            $sStepElement = str_replace('.yntour_border_active', '', $sStepElement);
            $aStepData['tour_description'] = isset($aStepData['tour_description']) ? $aStepData['tour_description'] : "";
            if (!empty($aStepData['tour_description']))
            {
                foreach ($aStepData['tour_description'] as $iKey => $aDesc)
                {
                    $aStepData['tour_description'][$iKey] = Phpfox::getLib('parse.input')->clean($aDesc);
                }
            }
            $aStepData['delay'] = isset($aStepData['tour_delay']) ? (int)$aStepData['tour_delay'] : 3;
            $aStepData['delay'] = $aStepData['delay'] * 1000;
            //.yntour_mouse_active
            $aInsertStep = array(
                'tourguide_id' => $aResults['tour_tourguide_id'],
                'step_element' => $sStepElement,
                'description' => serialize($aStepData['tour_description']),
                'delay' => $aStepData['delay'],
                'bgcolor' => !empty($aStepData['tour_bgcolor']) ? $aStepData['tour_bgcolor'] : "black",
                'fcolor' => !empty($aStepData['tour_fcolor']) ? $aStepData['tour_fcolor'] : "white",
                'position' => !empty($aStepData['position']) ? $aStepData['position'] : "",
                'single_lang' => isset($aStepData['is_multi_lang']) ? $aStepData['is_multi_lang'] : "",
                );

            $iStepId = $oServiceSteps->updateStep($aResults['id'], $aInsertStep);
            $aInsertStep['id'] = $aResults['id'];
            $aResults['step'] = $aInsertStep;
        }
        echo json_encode($aResults);
    }
    
    public function cancelNewTour()
    {       
        $bAllowEdit = Phpfox::getUserParam('tourguides.can_create_tour_guide');
        $aSession = phpfox::getLib('session')->get('yntour_current_selected');
        if ($aSession)
        {

        }
        if (isset($aSession['sessionid']))
        {
            phpfox::getLib('session')->remove('yntour_current_selected');
        }
        $iTourId = (int)$this->get('id');
        if ($iTourId > 0 && $bAllowEdit)
        {
            phpfox::getService('tourguides')->removeTour($iTourId);
            phpfox::getService('tourguides.steps')->removeSteps($iTourId);
            $this->call("cancelCreateTour();");
        }
    }
    
    public function createNewTour()
    {
        $oServiceTourGuide = phpfox::getService('tourguides');
        $iUserId = phpfox::getUserId();
        $aData = $this->getAll();
        if (isset($aData['data']))
        {
            parse_str($aData['data'], $aStepData);
            $aData['data'] = $aStepData;
        }
        $aInsertTour = array(
            'name' => isset($aData['data']['tour_name']) ? $aData['data']['tour_name'] : $aData['url'],
            'url' => isset($aData['url']) ? $aData['url'] : phpfox::getParam('core.path'),
            'controller' => isset($aData['controller']) ? $aData['controller'] : "core.index-visitor",
            'is_auto' => isset($aData['data']['is_auto']) ? $aData['data']['is_auto'] : 0,
            'params' => "",
            'is_active' => 0,
            'is_complete' => 0,
            'user_id' => $iUserId);
        $aResults = array();
        $aResults['tour_tourguide_id'] = $oServiceTourGuide->addTour($aInsertTour);
        if ($aResults['tour_tourguide_id'] > 0)
        {
            $aResults['success'] = true;
            $aResults['name'] = phpfox::getLib('parse.input')->clean($aInsertTour['name']);
        }
        else
        {
            $aResults['success'] = false;
            $aResults['message'] = _p('tourguides.create_fail_please_try_again');
        }
        echo json_encode($aResults);

    }
    
    public function completeCreateTour()
    {
        $bAllowEdit = Phpfox::getUserParam('tourguides.can_create_tour_guide');
        $aSession = phpfox::getLib('session')->get('yntour_current_selected');
        if ($aSession)
        {

        }
        if (isset($aSession['sessionid']))
        {
            phpfox::getLib('session')->remove('yntour_current_selected');
            $bAllowEdit = true;
        }
        $iTourId = (int)$this->get('id');
        if ($iTourId > 0 && $bAllowEdit)
        {
            phpfox::getService('tourguides')->updateTour($iTourId, array('is_active' => 1, 'is_complete' => 1));
            $aTour = phpfox::getService('tourguides')->getTourById($iTourId);
            echo json_encode($aTour);
        }

    }
    
    public function cancelAddSteps()
    {
        $bAllowEdit = Phpfox::getUserParam('tourguides.can_create_tour_guide');
        $aSession = Phpfox::getLib('session')->get('yntour_current_selected');
        
        if (isset($aSession['sessionid']))
        {
            Phpfox::getLib('session')->remove('yntour_current_selected');
            $bAllowEdit = true;
        }
        
        $iTourId = (int)$this->get('id');
        $iStep = (int)$this->get('step');
        
        if ($iTourId > 0 && $bAllowEdit)
        {
            Phpfox::getService('tourguides.steps')->deleteStepsAfter($iStep, $iTourId);
            $aTour = Phpfox::getService('tourguides')->getTourById($iTourId);
            echo json_encode($aTour);
        }
    }
    
    public function updateActivity()
    {
        $iTourId = (int)$this->get('id');
        $bIsActive = $this->get('active');
        if ($iTourId > 0)
        {
            phpfox::getService('tourguides')->updateTour($iTourId, array('is_active' => $bIsActive));

        }
    }
    
    public function ordering()
    {
        if (Phpfox::getService('tourguides.steps')->updateOrder($this->get('valstep')))
        {
            //do nothing
        }
    }
    
    public function updateActivityStep()
    {
        $iTourId = (int)$this->get('id');
        $bIsActive = $this->get('active');
        if ($iTourId > 0)
        {
            phpfox::getService('tourguides.steps')->updateStep($iTourId, array('is_active' => $bIsActive));

        }
    }

    public function addStep()
    {
        $bAllowEdit = Phpfox::getUserParam('tourguides.can_create_tour_guide');
        $aSession = phpfox::getLib('session')->get('yntour_current_selected');
        if ($aSession)
        {

        }
        if (!isset($aSession['sessionid']) && !$bAllowEdit)
        {
            echo json_encode(array());
            return false;
        }
        $aData = $this->getAll();
        $oServiceTourGuide = phpfox::getService('tourguides');
        $oServiceSteps = phpfox::getService('tourguides.steps');
        $aStepData = "";
        if (isset($aData['data']))
        {
            parse_str($aData['data'], $aStepData);

        }

        $aResults = array();
        $aResults['tour_tourguide_id'] = isset($aStepData['tour_tourguide_id']) ? $aStepData['tour_tourguide_id'] : 0;
        if (!isset($aStepData['tour_tourguide_id']) || $aStepData['tour_tourguide_id'] == 0 || empty($aStepData['tour_tourguide_id']))
        {
            $aInsertTour = array(
                'name' => isset($aData['url']) ? $aData['url'] : phpfox::getParam('core.path'),
                'url' => isset($aData['url']) ? $aData['url'] : phpfox::getParam('core.path'),
                'controller' => isset($aData['controller']) ? $aData['controller'] : "core.index-visitor",
                'params' => "",
                'is_active' => 0);
            $aResults['tour_tourguide_id'] = $oServiceTourGuide->addTour($aInsertTour);

        }
        if ($aResults['tour_tourguide_id'] > 0)
        {
            $sStepElement = isset($aStepData['tour_element']) ? $aStepData['tour_element'] : "main_core_body_holder";
            $sStepElement = str_replace('.yntour_mouse_active', '', $sStepElement);
            $sStepElement = str_replace('.yntour_border_active', '', $sStepElement);
            $sStepElement = preg_replace('/\._custom__.*_\d/i', '', $sStepElement);
            $aStepData['delay'] = isset($aStepData['tour_delay']) ? (int)$aStepData['tour_delay'] : 3;
            $aStepData['delay'] = $aStepData['delay'] * 1000;

            if (isset($aStepData['tour_description']))
            {
                foreach ($aStepData['tour_description'] as $iKey => $aDesc)
                {
                    $aStepData['tour_description'][$iKey] = Phpfox::getLib('parse.input')->clean($aDesc);
                }
            }
            //.yntour_mouse_active
            $aInsertStep = array(
                'tourguide_id' => $aResults['tour_tourguide_id'],
                'step_element' => $sStepElement,
                'description' => isset($aStepData['tour_description']) ? serialize($aStepData['tour_description']) : "",
                'delay' => $aStepData['delay'],
                'bgcolor' => !empty($aStepData['tour_bgcolor']) ? $aStepData['tour_bgcolor'] : "black",
                'fcolor' => !empty($aStepData['tour_fcolor']) ? $aStepData['tour_fcolor'] : "white",
                'position' => !empty($aStepData['position']) ? $aStepData['position'] : "",
                'single_lang' => isset($aStepData['is_multi_lang']) ? $aStepData['is_multi_lang'] : "",
                'orderring' => PHPFOX_TIME,
                );
            $iStepId = $oServiceSteps->addStep($aInsertStep);
            $aInsertStep['id'] = $iStepId;
            $aResults['step'] = $aInsertStep;
        }
        echo json_encode($aResults);
    }
}

?>
