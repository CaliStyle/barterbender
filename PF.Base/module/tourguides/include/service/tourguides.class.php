<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class TourGuides_Service_TourGuides extends Phpfox_Service 
{
	private $_sCurrentLangId = 'en';
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('tourguides');
		$this->_sCurrentLangId = Phpfox::getService('language')->getDefaultLanguage();
	}	
    public function init()
    {
        
    }
    public function getRealURL($sCurrentUrl)
    {		
		$bIsRewrite = phpfox::getParam('core.url_rewrite');		
        $sCoreUrl = phpfox::getParam('core.path');
		if($bIsRewrite == 2 && strpos($sCurrentUrl,"index.php?do=/") != false)
			$sCoreUrl .= "index.php?do=/";
        if(strpos($sCurrentUrl,"#!") !== false)
        {
            $aParts = explode("#!/",$sCurrentUrl);			
            if(count($aParts)>0)
            {
                $sUrl = $sCoreUrl.$aParts[1];
                $sCurrentUrl = $sUrl;
            }
        }
        if (!empty($sCurrentUrl))
        {
            $sCurrentUrl = rtrim($sCurrentUrl, '/') . '/';
        }
				
        return $sCurrentUrl;
    }
    public function getCurrentUrl()
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
        if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;

    }
    public function userlogout()
    {
        
    }
	
	public function showTour($iUserId = 0, $aParams = array())
    {	
        $aSession = phpfox::getLib('session')->get('yntour_current_selected');		
        if(!is_array($aSession))
        {
            $aSession = array();            
        }
        $bIsMember = phpfox::getUserId();
        if($bIsMember >=1)
        {
            $bIsMember = 1;
        }
        $aSession = json_encode($aSession); 

        // get guides by controller and is_use_controller
        $aTourController = null;
        if(isset($aParams['sControllerName']) && empty($aParams['sControllerName']) == false){
            $aTourWithController = $this->getToursByUseController($aParams['sControllerName'], 1);
            if(is_array($aTourWithController) && count($aTourWithController) >=1)
            {
                foreach($aTourWithController as $iKey=>$aT)
                {
                   if(($aT['is_member'] == $bIsMember || $aT['is_member'] == 2 )  && $aT['is_complete'] == 1)
                   {
                       $aTourController = $aT;
                       break;
                   }
                }
            }    
        }
        
        $aTour = array();
        if(null == $aTourController){
            $aTours = $this->getToursByURL($aParams['sCurrentUrl']);
            if(count($aTours) >=1 && is_array($aTours))
            {
                foreach($aTours as $iKey=>$aT)
                {
                   if($aT['url'] == $aParams['sCurrentUrl'] && ($aT['is_member'] == $bIsMember || $aT['is_member'] == 2 )  && $aT['is_complete'] == 1)
                   {
                       $aTour = $aT;break;
                   }
                }
            }
        } else {
            $aTour = $aTourController;
        }

        $aSteps = array();
        $aStepResults  = array();
        if(isset($aTour['id']))
        {
            $aSteps = phpfox::getService('tourguides.steps')->getSteps($aTour['id'],true);			
            $aStepResults = array();
            if(count($aSteps) >0)
            {
                foreach($aSteps as $iKey=>$aStep)
                {
					$sText = "";
					if(is_array($aStep['description']))
					{
						
						$sText = ($aStep['single_lang'] == "") ? $aStep['description'][$this->_sCurrentLangId] : $aStep['description'][$aStep['single_lang']];
					}
					else
					{				
						$sText = $aStep['description'];
					}
					
                    $aStepResults[] = array(
                        'name' => $aStep['step_element'],
                        'text' => htmlspecialchars_decode($sText),
                        'bgcolor' =>  $aStep['bgcolor'], 
                        'color' =>  $aStep['fcolor'], 
                        'position' =>  $aStep['position'], 
                        'time' =>  $aStep['delay']
                    );
					
                }
            }
            
        }
		
		$aLanguages = Phpfox::getService('language')->getAll();		
		
		$aLangId = "";
		$aLangTitle = "";
		if(count($aLanguages) == 1)
		{
			$aLangId = "['".$aLanguages[0]['language_id']."']";
			$aLangTitle =  "['". $aLanguages[0]['title']."']";
		}
		else
		{
			foreach($aLanguages as $iKey => $aLanguage)
			{
				if($iKey == 0)
				{
					$aLangId = "['".$aLanguage['language_id'];
					$aLangTitle =  "['". $aLanguage['title'];
				}
				else if($iKey == count($aLanguages)-1)
				{
					$aLangId .= "','".$aLanguage['language_id']."']";
					$aLangTitle .= "','".$aLanguage['title']."']";
				}
				else
				{
					$aLangId .= "','".$aLanguage['language_id'];
					$aLangTitle .= "','".$aLanguage['title'];
				}
			}	
		}		
		
		$aCurrentLanguages = Phpfox::getService('language')->getLanguage($this->_sCurrentLangId);
		$sCorePath = Phpfox::getParam('core.path') ;
        $sCorePath = str_replace("index.php".PHPFOX_DS,"",$sCorePath);
        $sCorePath .= 'PF.Base'.PHPFOX_DS;
		$sJs = "<script>var jsCurrentLangId = '".$this->_sCurrentLangId."'; var jsCurrentLangTitle = '". $aCurrentLanguages['title'] ."'; var jsLangId = ".$aLangId."; var jsLangTitle = ".$aLangTitle.";</script>";
        $aSteps = json_encode($aStepResults);
        $sHTML = phpfox::getLib('template')->assign( array(
                'iUserId' =>$iUserId,
                'aParamsTour' => $aParams,
                'aTourSteps' =>$aSteps,
                'bCanCreate' => (int)Phpfox::getUserParam('tourguides.can_create_tour_guide'),
                'sCoreUrl' => $sCorePath,
                'aYnTourSession'=>$aSession,
                'aYnTour' =>json_encode($aTour),
				'sJsLanguage' => $sJs
                )
            )
            ->getTemplate('tourguides.block.view');
            ;

         echo $sHTML;
    }
    public function showTourAjaxMode($iUserId = 0, $aParams = array())
    {
        $aSession = phpfox::getLib('session')->get('yntour_current_selected');
        if(!is_array($aSession))
        {
            $aSession = array();
            
        }
        $bIsMember = phpfox::getUserId();
        if($bIsMember >=1)
        {
            $bIsMember = 1;
        }
        $aSession = json_encode($aSession); 

        // get guides by controller and is_use_controller
        $aTourController = null;
        if(isset($aParams['sControllerName']) && empty($aParams['sControllerName']) == false){
            $aTourWithController = $this->getToursByUseController($aParams['sControllerName'], 1);
            if(is_array($aTourWithController) && count($aTourWithController) >=1)
            {
                foreach($aTourWithController as $iKey=>$aT)
                {
                   if(($aT['is_member'] == $bIsMember || $aT['is_member'] == 2 )  && $aT['is_complete'] == 1)
                   {
                       $aTourController = $aT;
                       break;
                   }
                }
            }    
        }

        $aTour = array();
        if(null == $aTourController){
            $aTours = $this->getToursByURL($aParams['sCurrentUrl']);
            if(count($aTours) >=1 && is_array($aTours))
            {
                foreach($aTours as $iKey=>$aT)
                {
                   if($aT['url'] == $aParams['sCurrentUrl'] && ($aT['is_member'] == $bIsMember || $aT['is_member'] == 2 ) && $aT['is_complete'] == 1)
                    {
                        $aTour = $aT;break;
                    }
                }
            }
        } else {
            $aTour = $aTourController;
        }
     
        $aSteps = array();
        $aStepResults  = array();
        $bNoAsk = false;
        if(isset($aTour['id']))
        {
            if(phpfox::getUserId()>0)
            {
                $aUserSetting = phpfox::getService('tourguides.user')->getUserSetting(phpfox::getUserId(),$aTour['id']);
                if(isset($aUserSetting['user_id']) && $aUserSetting['user_id'] > 0 && $aUserSetting['no_ask'] == 1)
                {
                    $aStepResults = array();
                    $bNoAsk = true;
                }
            }
            if(!$bNoAsk)
            {
                $aSteps = phpfox::getService('tourguides.steps')->getSteps($aTour['id'],true);
           
                $aStepResults = array();
                if(count($aSteps) >0)
                {
                    foreach($aSteps as $iKey=>$aStep)
                    {
						$sText = "";
						if(is_array($aStep['description']))
						{
							
							$sText = ($aStep['single_lang'] == "") ? $aStep['description'][$this->_sCurrentLangId] : $aStep['description'][$aStep['single_lang']];
						}
						else
						{				
							$sText = $aStep['description'];
						}
						
                        $aStepResults[] = array(
                            'name' => $aStep['step_element'],
                            'text' => htmlspecialchars_decode($sText),
                            'bgcolor' =>  $aStep['bgcolor'], 
                            'color' =>  $aStep['fcolor'], 
                            'position' =>  $aStep['position'], 
                            'time' =>  $aStep['delay']
                        );
                    }
                }
                
            }
            
            
        }
        $aLanguages = Phpfox::getService('language')->getAll();
		
		$aLangId = "";
		$aLangTitle = ""; 
		foreach($aLanguages as $iKey => $aLanguage)
		{
			if($iKey == 0)
			{
				$aLangId = "['".$aLanguage['language_id'];
				$aLangTitle =  "['". $aLanguage['title'];
			}
			else if($iKey == count($aLanguages)-1)
			{
				$aLangId .= "','".$aLanguage['language_id']."']";
				$aLangTitle .= "','".$aLanguage['title']."']";
			}
			else
			{
				$aLangId .= "','".$aLanguage['language_id'];
				$aLangTitle .= "','".$aLanguage['title'];
			}
		}
		
		$sJs = "<script>var jsCurrentLangId = '".$this->_sCurrentLangId."'; var jsLangId = ".$aLangId."; var jsLangTitle = ".$aLangTitle.";</script>";
		$sCorePath = Phpfox::getParam('core.path') ;
        $sCorePath = str_replace("index.php".PHPFOX_DS,"",$sCorePath);
        $sCorePath .= 'PF.Base'.PHPFOX_DS;
        $aSteps = json_encode($aStepResults);
        $sHTML = array(
                'iUserId' =>$iUserId,
                'aParamsTour' => $aParams,
                'aTourSteps' =>$aSteps,
                'bCanCreate' => (int)Phpfox::getUserParam('tourguides.can_create_tour_guide'),
                'sCoreUrl' => $sCorePath,
                'aYnTourSession'=>$aSession,
                'aYnTour' =>json_encode($aTour),
                'bNoAsk' =>$bNoAsk,
                'sJsLanguage' => $sJs
                )
            ;
            
         return $sHTML;
    }
	public function addTour($aInsertTour = array())
    {
        $aInsertTour['name'] = isset($aInsertTour['name'])?phpfox::getLib('parse.input')->clean($aInsertTour['name']):"no_name";
        $aInsertTour['url'] = $this->getRealURL($aInsertTour['url']);
        $aInsertTour['controller'] = '';
        return $this->database()->insert($this->_sTable,$aInsertTour);
    }
    public function removeTour($iTourGuideId = 0)
    {
        return $this->database()->delete($this->_sTable,'id = '.(int)$iTourGuideId);
    }
    public function updateTour($iTourId, $aParams)
    {
        if(isset($aParams['url']))
        {
            $aParams['url'] = $this->getRealURL($aParams['url']);    
        }
        return $this->database()->update($this->_sTable,$aParams,'id = '.(int)$iTourId);
    }
    public function getToursByController($sController)
    {
        $aTours = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('controller = "'.$sController.'" AND is_active = 1 AND is_complete = 1')
                    ->execute('getRows');
        return $aTours;
    }  
    public function getToursByURL($sController)
    {
        $aTours = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('url = "'.$this->database()->escape($sController).'" AND is_active = 1 AND is_complete = 1')
                    ->execute('getRows');
        return $aTours;
    }
    public function getTourById($iTourGuideId = 0)
    {
        $aTour = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('id = '.(int)$iTourGuideId)
                    ->execute('getRow');
        return $aTour;
    }
    public function getTours()
    {    
        $aTours = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('is_complete = 1')
                    ->execute('getRows');
        return $aTours;
        
    }
    public function resetTour($iTourId = 0)
    {
        $this->database()->delete(phpfox::getT('tourguides_usersetting'),'tour_id = '.(int)$iTourId);
        return true;
    }
    public function get($aConds, $sSort = 't.name ASC', $iPage = '', $iLimit = '')
    {        
        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 't')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = t.user_id')
            ->where($aConds)
            ->order($sSort)
            ->execute('getSlaveField');    
        $aItems = array();
        if ($iCnt)
        {        
            $aItems = $this->database()->select('t.*, ' . Phpfox::getUserField())
                ->from($this->_sTable, 't')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = t.user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getSlaveRows');
            if(count($aItems))
            {
                foreach($aItems as $iKey=>$aItem)
                {
                    $aSteps = phpfox::getService('tourguides.steps')->getSteps($aItem['id']);
                    $aItems[$iKey]['total_steps'] = count($aSteps);
                    $aItems[$iKey]['aSteps'] = $aSteps;
                    $aItems[$iKey]['step_view'] = $iKey+1;
                }
            }
           
        }
        return array($iCnt, $aItems);
    }
	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('tourguides.service_process__call'))
		{
			return eval($sPlugin);
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	

    public function getToursByUseController($sController = '', $isUseController = 1)
    {
        $aTours = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('controller = "'.$sController.'" AND is_active = 1 AND is_complete = 1 AND is_use_controller = ' . (int)$isUseController)
                    ->execute('getRows');
        return $aTours;
    }  

}

?>
