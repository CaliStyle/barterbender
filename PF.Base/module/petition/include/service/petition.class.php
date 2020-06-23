<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Service_Petition extends Phpfox_Service
{	
	private $_aSpecial = array(
		'category',
		'tag'
	);	
    private $_iLimit = 4;
	
	private $_aCallback = null;
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('petition');
		
		(($sPlugin = Phpfox_Plugin::get('petition.service_petition___construct')) ? eval($sPlugin) : false);
	}	
   
      public function callback($aCallback)
	{
		$this->_aCallback = $aCallback;

		return $this;
	}
      
	public function parseVar($sParse,$aPetition)
	{
		$aReplace = array('[title]',
			'[petition_url]',
			'[petition_goal]',
			'[short_description]',
			'[description]',
			'[start_time]',
			'[end_time]',
			'[total_sign]',
			'[signature_goal]',
			'[target]',
			'[full_name]'
                              );

            $oDate = Phpfox::getLib('date');
            $sUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
            $aLink = Phpfox::getLib('url')->permalink('petition',
				$aPetition['petition_id'],
				$aPetition['title']);
		$sLink = '<a href="'. $aLink  . '" title = "' . $aPetition['title'] . '" target="_blank">' . $aLink. '</a>';
		
            $aVar = array($aPetition['title'],$sLink,$aPetition['petition_goal'],$aPetition['short_description'],$aPetition['description'],
                          $oDate->convertTime($aPetition['start_time']),
				$oDate->convertTime($aPetition['end_time']),
				$aPetition['total_sign'],
				$aPetition['signature_goal'],
				$aPetition['target'],
				$sUser['full_name']
                          );
		$sParse = str_replace($aReplace,$aVar,$sParse);
		return $sParse;
	}
      public function parseLetter($mPetition, $bIsId = false)
      {
         $aPetition = array();
         $sParse = '';
         
         if($bIsId == true)
         {
            $aPetition = $this->getPetition($mPetition);
         }
         else
         {
            $aPetition = $mPetition;
         }
                  
         if(isset($aPetition['letter']))
         {
            $sParse = $this->parseVar($aPetition['letter'],$aPetition);
         }         
         return $sParse;
      }

   public function getSignedUsers($iPetitionId)
   {
      $aRows = $this->database() ->select('u.user_id, u.full_name, ps.time_stamp')
                                 ->from(Phpfox::getT('petition_sign'),'ps')
                                 ->join(Phpfox::getT('user'),'u','u.user_id = ps.user_id')
                                 ->where('ps.petition_id = '.$iPetitionId)
                                 ->order('ps.time_stamp DESC')
                                 ->execute('getSlaveRows');
      if(!empty($aRows))
      {
         foreach($aRows as $iKey => $aRow)
         {            
            $aRows[$iKey]['href'] = Phpfox::getService('user')->getLink($aRow['user_id']);
            $aRows[$iKey]['link'] = "<a href='". $aRows[$iKey]['href'] ."' title='". $aRow['full_name'] ."'>". $aRow['full_name'] ."</a> ";
         }
      }
      
      return array(count($aRows),$aRows);
   }
    
    public function sendToTarget($iId,$iUserId = 0)
    {
	
      $oParseInput = Phpfox::getLib('parse.input');
      $aPetition = $this->getPetition($iId);
	
      if(empty($aPetition))
	 {
		 return false; 
	 }
      if(empty($aPetition['letter']))
      {		
         return Phpfox_Error::set(Phpfox::getPhrase('petition.an_error_occurred_and_petition_letter_could_not_be_sent_to_target_petition_letter_may_be_blank'));
      }
      
      if (!empty($aPetition['target_email']) && (Phpfox::isAdmin() || ($aPetition['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin('' . $aPetition['item_id'] . '')) || $aPetition['user_id'] == Phpfox::getUserId()))
      {
        $aEmails = explode(',', $aPetition['target_email']);
        $aCachedEmails = array();
	   $bSentSuccess = 0;
	   
        foreach ($aEmails as $sEmail)
        {
            $sEmail = trim($sEmail);
		  if (!Phpfox::getLib('mail')->checkEmail($sEmail))
		  {
			  continue;
		  }
		  
		  if(isset($aCachedEmails[$sEmail]) && $aCachedEmails[$sEmail] == true)
		  {
			   continue;
		  }
            $sLink = Phpfox::getLib('url')->permalink('petition', $aPetition['petition_id'], $aPetition['title']);

            //Letter
            $sMessage = "<a href='". $sLink ."' title='". $aPetition['title'] ."'><h2>". $aPetition['title'] ."</h2></a><br/>";
		  
            if($iUserId)
            {
               $sUserLink = Phpfox::getService('user')->getLink($iUserId);
               $sUser = Phpfox::getService('user')->getUser($iUserId);
               $sMessage .= "<a href='". $sUserLink ."' title='". $sUser['full_name'] ."'>". $sUser['full_name'] ."</a> ". Phpfox::getPhrase('petition.just_signed_this_petition_and_i_want_to_send_the_letter_again') ."<p/><br/>";
            }
            $sMessage .= $this->parseLetter($aPetition);
            
            //Signed List
            list($iTotal,$aSignedUsers) = $this->getSignedUsers($aPetition['petition_id']);
            
            if($iTotal)
            {
               $sMessage .= "<br/><hr/><strong>".Phpfox::getPhrase('petition.signatures') .":</strong> (" . $iTotal . " " . Phpfox::getPhrase('petition.out_of') . " " . number_format($aPetition['signature_goal'],0,'.',',')  . ")<br/>";               
               
			foreach($aSignedUsers as $iKey => $aSignedUser)
               {
                  $sMessage .= $aSignedUser['link'] . "(". Phpfox::getPhrase('petition.on') . " " . Phpfox::getLib('date')->convertTime($aSignedUser['time_stamp']) .")";
                  if($iKey < $iTotal -1)
                     $sMessage .= ', ';
               }               
            }
                        
            $oMail = Phpfox::getLib('mail');
            
            $oMail->fromEmail(Phpfox::getUserBy('email'))
                            ->fromName(Phpfox::getUserBy('full_name'));
            $quote_style = ENT_COMPAT;
		  $charset = 'UTF-8';		  
			
            $bSent = $oMail->to($sEmail)
                  ->subject($result = html_entity_decode ($aPetition['letter_subject'], $quote_style, $charset))
                  ->message($sMessage)
                  ->send();
			   
			
		  if($bSent)
		  {
			   $aCachedEmails[$sEmail] = true;
			   $bSentSuccess++;
		  }
        }
	   
	   return $bSentSuccess;
      }
	
	return false;
    }
    
  
    
    public function isAlreadyInvited($iItemId, $aFriends)
    {
         if ((int) $iItemId === 0)
         {
              return false;
         }
         
         if (is_array($aFriends))
         {
              if (!count($aFriends))
              {
                   return false;
              }
              
              $sIds = '';
              foreach ($aFriends as $aFriend)
              {
                   if (!isset($aFriend['user_id']))
                   {
                        continue;
                   }
                   
                   $sIds[] = $aFriend['user_id'];
              }			
              
              $aInvites = $this->database()->select('invite_id, signed_id, invited_user_id')
                   ->from(Phpfox::getT('petition_invite'))
                   ->where('petition_id = ' . (int) $iItemId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')
                   ->execute('getSlaveRows');
              
              $aCache = array();
              foreach ($aInvites as $aInvite)
              {
                   $aCache[$aInvite['invited_user_id']] = ($aInvite['signed_id'] > 0 ? Phpfox::getPhrase('petition.signed') : Phpfox::getPhrase('petition.invited'));
              }
              
              if (count($aCache))
              {
                   return $aCache;
              }
         }
         
         return false;
    }
	
     public function canSign($aPetition)
	{      
		$bCanSign = 1;
	 
          if($aPetition['petition_status'] != 2)
            return 0;
		
	     if(Phpfox::isAdmin())
		{
		  $bCanSign = 1;
		}
		else if (isset($aPetition['privacy_sign']))
		{
			switch ($aPetition['privacy_sign'])
			{
				case 1:	//Friends					
					if (!Phpfox::getService('friend')->isFriend(Phpfox::getUserId(),$aPetition['user_id']))
					{
						$bCanSign = 0;	
					}
					break;
				case 2: //Friend of friend
					if (!Phpfox::getService('friend')->isFriendOfFriend($aPetition['user_id']))
					{
						$bCanSign = 0;	
					}
					break;
				case 3: //Only me
					if($aPetition['user_id'] != Phpfox::getUserId())
					{
						$bCanSign = 0;	
					}					
					break;
			}
		}
		
		$aRow = $this->database()->select('*')
				->from(Phpfox::getT('petition_sign'), 'ps')
				->where('ps.petition_id = ' . $aPetition['petition_id'] . ' AND ps.user_id = ' . Phpfox::getUserId())
				->limit(1)
				->execute('getSlaveField');
				
		if(!empty($aRow))
		{
			$bCanSign = 2;
		}
		
		return $bCanSign;
	}

	public function getDirectSign()
	{
		static $aDirect = null;
					
		if ($aDirect !== null)
		{
			return $aDirect;
		}
				
		$sCacheId = $this->cache()->set('petition_directsign');
		
		if (!($aRow = $this->cache()->get($sCacheId)))
		{
			$aRow = $this->database()->select('p.*, pt.short_description, pt.target, pt.petition_goal, ' . Phpfox::getUserField())
				->from(Phpfox::getT('petition'), 'p')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                                ->join(Phpfox::getT('petition_text'), 'pt', 'pt.petition_id= p.petition_id')
				->where('p.petition_status = 2 AND p.is_directsign = 1 AND p.is_approved = 1' . ' AND p.module_id = \'' . ($this->_aCallback != null ? $this->_aCallback['module'] : 'petition') . '\' AND p.item_id = ' . ($this->_aCallback != null ? $this->_aCallback['item'] : 0))
				->limit(1)
				->execute('getSlaveRow');
    
                        if (!empty($aRow))
                        {
                            $aCategory = $this->database()->select('d.category_id, c.name')
                                        ->from(Phpfox::getT('petition_category_data'), 'd')
                                        ->join(Phpfox::getT('petition_category'), 'c', 'd.category_id = c.category_id')
                                        ->where('d.petition_id = ' . $aRow['petition_id'])
                                        ->execute('getSlaveRow');
                            
                            if($aCategory)
                            {
                                    $aCategory['link'] = Phpfox::getLib('url')->permalink('petition.category', $aCategory['category_id'], $aCategory['name']);
                                    $aRow['category'] = $aCategory;
                            }
                            					   
                            $this->cache()->save($sCacheId, $aRow);   
                        }                        
		}

		if (!empty($aRow))
		{		
			$aDirect = $aRow;
		}
		
		return $aDirect;
	}   	
	
    public function getRecent()
	{		
		static $aRecent = null;
		static $iTotal4 = null;
			
		if ($aRecent !== null)
		{
			return array($iTotal4, $aRecent);
		}
		
		$aRecent = array();
		$sCacheId = $this->cache()->set('petition_recent');
		
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			
			$aRows = $this->database()->select('p.*, pt.short_description, ' . Phpfox::getUserField())
				->from(Phpfox::getT('petition'), 'p')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                                ->join(Phpfox::getT('petition_text'), 'pt', 'pt.petition_id= p.petition_id')
				->where('p.privacy IN(' . Phpfox::getService('core')->getForBrowse($aUser) . ') AND p.is_approved = 1' . ' AND p.module_id = \'' . ($this->_aCallback != null ? $this->_aCallback['module'] : 'petition') . '\' AND p.item_id = ' . ($this->_aCallback != null ? $this->_aCallback['item'] : 0))
				->order('p.time_stamp DESC')
				->limit($this->_iLimit + 1)
				->execute('getSlaveRows');
	 		
			$this->cache()->save($sCacheId, $aRows);
		}
		
		$iTotal4 = 0;
		if (is_array($aRows) && count($aRows))
		{
			$iTotal4 = count($aRows);
			foreach($aRows as $iKey => $aRow)
			{
				if($iKey == $this->_iLimit)
					break;
				$aRecent[] = $aRow;				
			}			
		}
		
		return array($iTotal4, $aRecent);
	}        

        
    public function getMostSigned()
	{		
		static $aSigns = null;
		static $iTotal3 = null;
			
		if ($aSigns !== null)
		{
			return array($iTotal3, $aSigns);
		}
		
		$aSigns = array();
		$sCacheId = $this->cache()->set('petition_most_signed');
		
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			
			$aRows = $this->database()->select('p.*, pt.short_description, ' . Phpfox::getUserField())
				->from(Phpfox::getT('petition'), 'p')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                                ->join(Phpfox::getT('petition_text'), 'pt', 'pt.petition_id= p.petition_id')
				->where('p.is_approved = 1' . ' AND p.module_id = \'' . ($this->_aCallback != null ? $this->_aCallback['module'] : 'petition') . '\' AND p.item_id = ' . ($this->_aCallback != null ? $this->_aCallback['item'] : 0))
				->order('p.total_sign DESC')
				->limit($this->_iLimit + 1)
				->execute('getSlaveRows');
			
			$this->cache()->save($sCacheId, $aRows);
		}
		
		$iTotal3 = 0;
		if (is_array($aRows) && count($aRows))
		{
			$iTotal3 = count($aRows);
			foreach($aRows as $iKey => $aRow)
			{
				if($iKey == $this->_iLimit)
					break;
				$aSigns[] = $aRow;				
			}			
		}
		
		return array($iTotal3, $aSigns);
	}
	
	public function getPopular()
	{
		static $aPopular = null;
		static $iTotal2 = null;
			
		if ($aPopular !== null)
		{
			return array($iTotal2, $aPopular);
		}
		
		$aPopular = array();
		$sCacheId = $this->cache()->set('petition_popular');
		
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			
			$aRows = $this->database()->select('p.*, pt.short_description, ' . Phpfox::getUserField())
				->from(Phpfox::getT('petition'), 'p')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                                ->join(Phpfox::getT('petition_text'), 'pt', 'pt.petition_id= p.petition_id')
				->where('p.is_approved = 1'. ' AND p.module_id = \'' . ($this->_aCallback != null ? $this->_aCallback['module'] : 'petition') . '\' AND p.item_id = ' . ($this->_aCallback != null ? $this->_aCallback['item'] : 0))
				->order('p.total_view DESC')
				->limit($this->_iLimit + 1)
				->execute('getSlaveRows');
                                
			$this->cache()->save($sCacheId, $aRows);
		}
		
		$iTotal2 = 0;
		if (is_array($aRows) && count($aRows))
		{
			$iTotal2 = count($aRows);
			foreach($aRows as $iKey => $aRow)
			{
				if($iKey == $this->_iLimit)
					break;
				$aPopular[] = $aRow;				
			}						
		}
		
		return array($iTotal2, $aPopular);
	}
	
	public function getFeatured($iLimit = null)
	{
		static $aFeatured = null;
		static $iTotal = null;
		
		if ($aFeatured !== null)
		{
			return array($iTotal, $aFeatured);
		}
		
		$aFeatured = array();
		$sCacheId = $this->cache()->set('petition_featured');		
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			$aRows = $this->database()->select('p.*, pt.short_description, pc.category_id, pc.name as category_name, ' . Phpfox::getUserField())
				->from(Phpfox::getT('petition'), 'p')                                
				->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                        ->join(Phpfox::getT('petition_text'), 'pt', 'pt.petition_id= p.petition_id')                        
                        ->join(Phpfox::getT('petition_category_data'), 'pd', 'pd.petition_id= p.petition_id')
                        ->join(Phpfox::getT('petition_category'), 'pc', 'pd.category_id = pc.category_id')
						->where('p.is_featured = 1' . ' AND p.module_id = \'petition\' AND p.item_id = 0')
                        ->limit($iLimit)
				->execute('getSlaveRows');
			
                        if (is_array($aRows) && count($aRows))
                        {
                                foreach ($aRows as $iKey => $aRow)
                                {
                                    $aCategory = array('category_id'=>$aRow['category_id'],
                                                      'name'  => $aRow['category_name'],
                                                      'link' => Phpfox::getLib('url')->permalink('petition.category', $aRow['category_id'], $aRow['category_name'])
                                                      );                                       
                                    $aRows[$iKey]['category'] = $aCategory;                                       
                                }
                        }			
			$this->cache()->save($sCacheId, $aRows);
		}
		
		$iTotal = 0;
                
		if (is_array($aRows) && count($aRows))
		{
			$iTotal = count($aRows);
			shuffle($aRows);
			foreach ($aRows as $iKey => $aRow)
			{
				
				$aFeatured[] = $aRow;
			}
		}
		
		return array($iTotal, $aFeatured);
	}
	
	public function getImages($iId, $iLimit = null)
	{
		$aImages =  $this->database()->select('*')
		     ->from(Phpfox::getT('petition_image'))
		     ->where('petition_id = ' . (int) $iId)
		     ->order('ordering ASC')
		     ->limit($iLimit)
		     ->execute('getSlaveRows');
		return $aImages;
	}
	
	public function getSignatures($iId, $iLimit = null)
	{
		$aSignatures = array();
		$iTotal = 0;
		
		$aRows =  $this->database()->select('ps.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('petition_sign'),'ps')
				->join(Phpfox::getT('petition'), 'p', 'p.petition_id= ps.petition_id')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = ps.user_id')
				->where('ps.petition_id = ' . (int) $iId)
				->order('ps.time_stamp DESC')
				->execute('getSlaveRows');
				
		if (is_array($aRows) && count($aRows))
		{
			$iTotal = count($aRows);
			foreach ($aRows as $iKey => $aRow)
			{
				if ($iLimit != null && $iKey === $iLimit)
				{
					break;
				}
				$aSignatures[] = $aRow;
			}
		}
		
		return array($iTotal, $aSignatures);
	}	
		
	public function getNews($iId, $iLimit = null)
	{
		$aNews = array();
		$iTotal = 0;
		
		$aRows =  $this->database()->select('pn.*')
				->from(Phpfox::getT('petition_news'),'pn')
				->where('pn.petition_id = ' . (int) $iId)
				->order('pn.time_stamp DESC')
				->execute('getSlaveRows');
          
		if (is_array($aRows) && count($aRows))
		{
			$iTotal = count($aRows);
			foreach ($aRows as $iKey => $aRow)
			{
				if ($iLimit != null && $iKey === $iLimit)
				{
					break;
				}
				$aNews[] = $aRow;
			}
		}

		return array($iTotal, $aNews);
	}
		
                        
	public function getPetitionForEdit($iId)
	{            
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_petition_getpetitionforedit__start')) ? eval($sPlugin) : false);
		
		$aRow = $this->database()->select("p.*, pt.*, u.user_name,pcd.category_id")
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('petition_text'), 'pt', 'pt.petition_id = p.petition_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                  ->leftjoin(Phpfox::getT('petition_category_data'), 'pcd', 'pcd.petition_id = p.petition_id')			
                  ->where('p.petition_id = ' . (int) $iId)
			->execute('getSlaveRow');
                        
            return $aRow;
	}

	
	public function searchPetition($aConds, $sSort = 'p.title ASC', $iPage = '', $iLimit = '')
	{
		(($sPlugin = Phpfox_Plugin::get('petition.service_petition_searchPetition_start')) ? eval($sPlugin) : false);
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('petition'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where($aConds)
			->order($sSort)
			->execute('getSlaveField');	
		$aStatus = array('0' => Phpfox::getPhrase('petition.all'),
				 '1' => Phpfox::getPhrase('petition.closed'),
				 '2' => Phpfox::getPhrase('petition.on_going'),
				 '3' => Phpfox::getPhrase('petition.victory')
				);	
		$aItems = array();
		if ($iCnt)
		{		
			$aItems = $this->database()->select('p.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('petition'), 'p')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
				->where($aConds)
				->order($sSort)
				->limit($iPage, $iLimit, $iCnt)
				->execute('getSlaveRows');
				
			foreach ($aItems as $iKey => $aItem)
			{
				
				$aItems[$iKey]['petition_status_text'] = $aStatus[$aItem['petition_status']];
				if($aItem['is_approved'] == 0)
				{
					$aItems[$iKey]['petition_status_text']  = Phpfox::getPhrase('petition.pending');
				}				
				$aItems[$iKey]['link'] = ($aItem['user_id'] ? Phpfox::getLib('url')->permalink($aItem['user_name'] . '.petition', $aItem['petition_id'], $aItem['title']) : Phpfox::getLib('url')->permalink('petition', $aItem['petition_id'], $aItem['title']));
			}
		}
			
		(($sPlugin = Phpfox_Plugin::get('petition.service_petition_searchPetition_end')) ? eval($sPlugin) : false);
		
		$this->processRows($aItems);
		
		return array($iCnt, $aItems);
	}	
	
	public function processRows(&$aRows)
	{
		foreach ($aRows as $iKey => $aRow)
		{
			if($aRow['module_id'] === 'pages')
			{
				$aPage = Phpfox::getService('pages')->getPage($aRow['item_id']);
				if($aPage['vanity_url'])
				{
					$aRows[$iKey]['page_link'] = Phpfox::permalink($aPage['vanity_url'], 'petition');
				}
				else
				{
					$aRows[$iKey]['page_link'] = Phpfox::permalink('pages', $aRow['item_id'], 'petition');	
				}
				$aRows[$iKey]['page_name'] = $aPage['title'];
			}			
		}
	}
	
	public function getExtra(&$aItems, $sType = null)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_petition_getextra__start')) ? eval($sPlugin) : false);
		
		if (!is_array($aItems))
		{
			$aItems = array();
		}
		
		$aIds = array();
		foreach ($aItems as $iKey => $aValue)
		{
			$aIds[] = $aValue['petition_id'];
		}			

		$aCategories = Phpfox::getService('petition.category')->getCategoriesById(implode(', ', $aIds));	

		if (Phpfox::isModule('tag'))
		{
			$aTags = Phpfox::getService('tag')->getTagsById('petition', implode(', ', $aIds));	
		}

		$oFilterOutput = Phpfox::getLib('parse.output');
		foreach ($aItems as $iKey => $aValue)
		{
			if (isset($aCategories[$aValue['petition_id']]))
			{
				$sCategories = '';
				$aCacheCategory[$aValue['petition_id']] = array();
				foreach ($aCategories[$aValue['petition_id']] as $aCategory)
				{					
					if (isset($aCacheCategory[$aValue['petition_id']][$aCategory['category_id']]))
					{
						continue;
					}
					
					$aCacheCategory[$aValue['petition_id']][$aCategory['category_id']] = true;						

					if ($aCategory['user_id'] && $sType == 'user_profile')
					{
						$sCategories .= ', <a href="' . Phpfox::getLib('url')->permalink($aValue['user_name'] . '.petition.category',  $aCategory['category_id'], $aCategory['category_name']) . '">' . Phpfox::getLib('locale')->convert($oFilterOutput->clean($aCategory['category_name'])) . '</a>';
					}
					else 
					{
						$sCategories .= ', <a href="' . Phpfox::getLib('url')->permalink('petition.category',  $aCategory['category_id'], $aCategory['category_name']) . '">' . Phpfox::getLib('locale')->convert($oFilterOutput->clean($aCategory['category_name'])) . '</a>';
					}
				}
				$sCategories = trim(ltrim($sCategories, ','));

				$aItems[$iKey]['info'] = Phpfox::getPhrase('petition.posted_x_by_x_in_x', array('date' => Phpfox::getTime(Phpfox::getParam('petition.petition_time_stamp'), $aValue['time_stamp']), 'link' => Phpfox::getLib('url')->makeUrl($aValue['user_name']), 'user' => $aValue, 'categories' => $sCategories));
			}
			else 
			{				
				$aItems[$iKey]['info'] = Phpfox::getPhrase('petition.posted_x_by_x', array('date' => Phpfox::getTime(Phpfox::getParam('petition.petition_time_stamp'), $aValue['time_stamp']), 'link' => Phpfox::getLib('url')->makeUrl($aValue['user_name']), 'user' => $aValue));
			}
			
			if (isset($aTags[$aValue['petition_id']]))
			{
				$aItems[$iKey]['tag_list'] = $aTags[$aValue['petition_id']];
			}
			
			$aItems[$iKey]['bookmark_url'] = Phpfox::permalink('petition', $aValue['petition_id'], $aValue['title']);
			
			$aItems[$iKey]['aFeed'] = array(			
				'feed_display' => 'mini',	
				'comment_type_id' => 'petition',
				'privacy' => $aValue['privacy'],
				'comment_privacy' => $aValue['privacy_comment'],
				'like_type_id' => 'petition',				
				'feed_is_liked' => (isset($aValue['is_liked']) ? $aValue['is_liked'] : false),
				'feed_is_friend' => (isset($aValue['is_friend']) ? $aValue['is_friend'] : false),
				'item_id' => $aValue['petition_id'],
				'user_id' => $aValue['user_id'],
				'total_comment' => $aValue['total_comment'],
				'feed_total_like' => $aValue['total_like'],
				'total_like' => $aValue['total_like'],
				'feed_link' => $aItems[$iKey]['bookmark_url'],
				'feed_title' => $aValue['title'],
				'time_stamp' => $aValue['time_stamp'],
				'report_module' => 'petition'
			);
		}		
		
		unset($aTags, $aCategories);
		
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_petition_getextra__end')) ? eval($sPlugin) : false);
	}
	
	public function getPetition($iPetitionId)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_petition_getpetition__start')) ? eval($sPlugin) : false);
		(($sPlugin = Phpfox_Plugin::get('petition.service_petition_getpetition')) ? eval($sPlugin) : false);
		
		if (Phpfox::isModule('track'))
		{
			$this->database()->select("petition_track.item_id AS is_viewed, ")->leftJoin(Phpfox::getT('petition_track'), 'petition_track', 'petition_track.item_id = petition.petition_id AND petition_track.user_id = ' . Phpfox::getUserBy('user_id'));
		}		
				
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = petition.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}		
		
		if (Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'petition\' AND l.item_id = petition.petition_id AND l.user_id = ' . Phpfox::getUserId());
		}
                $sSelectSql = "";
                if(Phpfox::getParam('core.allow_html'))
                {
                     $sSelectSql  = "  petition_text.description_parsed AS description,
                                    petition_text.short_description_parsed AS short_description,
                                    petition_text.letter_parsed AS letter				   
                                 ";
                }
                else
                {
                    $sSelectSql  = "   petition_text.description AS description,
                                    petition_text.short_description AS short_description,
                                    petition_text.letter AS letter
                                 ";
                }
		$sSelectSql .= ', petition_text.letter_subject, petition_text.target, petition_text.target_email, petition_text.petition_goal, petition_text.signature_goal';
		$aRow = $this->database()->select("petition.*, " . $sSelectSql . " , " . Phpfox::getUserField())
			->from($this->_sTable, 'petition')
			->join(Phpfox::getT('petition_text'), 'petition_text', 'petition_text.petition_id = petition.petition_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = petition.user_id')
			->where('petition.petition_id = ' . (int) $iPetitionId)
			->execute('getSlaveRow');
		
		if(empty($aRow))
		{
			return false;
		}
            //->where('petition.petition_id = ' . (int) $iPetitionId . ' AND petition.module_id = \'' . ($this->_aCallback != null ? $this->_aCallback['module'] : 'petition') . '\' AND petition.item_id = ' . ($this->_aCallback != null ? $this->_aCallback['item'] : 0))			
		$aCategory = $this->database()->select('d.category_id, c.name')
			->from(Phpfox::getT('petition_category_data'), 'd')
			->join(Phpfox::getT('petition_category'), 'c', 'd.category_id = c.category_id')
			->where('d.petition_id = ' . (int) $iPetitionId)
			->execute('getSlaveRow');
		
		
		if($aCategory)
		{
			$aCategory['link'] = Phpfox::getLib('url')->permalink('petition.category', $aCategory['category_id'], $aCategory['name']);
			$aRow['category'] = $aCategory;
		}		
		
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_petition_getpetition__end')) ? eval($sPlugin) : false);
		
		if (!isset($aRow['is_friend']))
		{
			$aRow['is_friend'] = 0;
		}
            
		$aRow['can_sign'] = $this->canSign($aRow);
            if($aRow['end_time'] <= PHPFOX_TIME && $aRow['petition_status'] == 2)
            {
               if($aRow['is_approved'] && Phpfox::getService('petition.process')->close($aRow['petition_id']))
               {
                  $aRow['petition_status'] = 1;
			   if($aRow['is_directsign'] == 1)
			   {
				$this->cache()->remove('petition_directsign');
			   }
               };               
            }
            
		return $aRow;
	}
	
	public function hasAccess($iId, $sUserPerm, $sGlobalPerm)
	{
		(($sPlugin = Phpfox_Plugin::get('petition.service_petition_hasaccess_start')) ? eval($sPlugin) : false);
		
		$aRow = $this->database()->select('u.user_id, petition.module_id, petition.item_id')
			->from($this->_sTable, 'petition')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = petition.user_id')
			->where('petition.petition_id = ' . (int) $iId)
			->execute('getSlaveRow');
		

		(($sPlugin = Phpfox_Plugin::get('petition.service_petition_hasaccess_end')) ? eval($sPlugin) : false);
		
		if (!isset($aRow['user_id']))
		{
			return false;
		}
		
      if ($aRow['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aRow['item_id']))
		{		
			return true;
		}
            
		if ((Phpfox::getUserId() == $aRow['user_id'] && Phpfox::getUserParam('petition.' . $sUserPerm,true)) || Phpfox::getUserParam('petition.' . $sGlobalPerm,true))
		{
			return $aRow['user_id'];
		}
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_petition_getpetition__end')) ? eval($sPlugin) : false);
		return false;
	}	

	public function getPendingTotal()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_service_petition_getpendingtotal')) ? eval($sPlugin) : false);
		
		$iTotal =  (int) $this->database()->select('COUNT(*)')
			->from($this->_sTable,'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('is_approved = 0')
			->execute('getSlaveField');
            //->where('is_approved = 0'. ' AND module_id = \'' . ($this->_aCallback != null ? $this->_aCallback['module'] : 'petition') . '\' AND item_id = ' . ($this->_aCallback != null ? $this->_aCallback['item'] : 0))
            return $iTotal;
	}	

	public function getStats()
	{
		$aStats = array();
	
		$aStats['victories'] = 	(int)  $this->database()->select('COUNT(*)')
								->from($this->_sTable)
								->where('is_approved = 1 AND petition_status = 3 AND module_id = "petition"')
								->execute('getSlaveField');
		$aStats['ongoing'] = 	(int)  $this->database()->select('COUNT(*)')
								->from($this->_sTable)
								->where('is_approved = 1 AND petition_status = 2 AND module_id = "petition"')
								->execute('getSlaveField');
		$aStats['closed'] = 	(int)  $this->database()->select('COUNT(*)')
								->from($this->_sTable)
								->where('is_approved = 1 AND petition_status = 1 AND module_id = "petition"')
								->execute('getSlaveField');
								
		$aStats['victories'] = number_format($aStats['victories'],0,'.',',');
		$aStats['ongoing'] = number_format($aStats['ongoing'],0,'.',',');
		$aStats['closed'] = number_format($aStats['closed'],0,'.',',');
		return $aStats;
	}

    /**
     * get feed for not add feed when edit
     * @param $iListingId
     * @return bool
     */

    public function isPetitionOnFeed($iPetitionId) {
        $aRow = phpfox::getLib('database')->select('*')
            ->from(Phpfox::getT('feed'))
            ->where('type_id = "petition" AND item_id = "' . $iPetitionId  . '"')
            ->execute('getSlaveRow');

        if($aRow) return true;

        return false;
    }

	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('petition.service_petition__call'))
		{
			return eval($sPlugin);
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
	public function setAdvSearchConditions($aVals)
	{
		// Filter keywords
		if(!empty($aVals['keywords']))
		{
			$this->search()->setCondition("AND petition.title LIKE '%" . $aVals['keywords'] . "%'");
		}
		if(!empty($aVals['categories'])){

			$listcategory = trim($aVals['categories'],",");
			$aList = explode(",", $listcategory);
			if(count($aList)>=2){
				if($aList[1]>0)
					$listcategory = $aList[1];
				else {
					$listcategory = $aList[0];
				}
			}
			if ($listcategory > 0 )
				$this->search()->setCondition("AND 0<(select(count(*)) from ".Phpfox::getT('petition_category_data')." data where data.petition_id = petition.petition_id and data.category_id in (".$listcategory."))");
		}
		if(($aVals['status'])>0)
		{
			$this->search()->setCondition("AND `petition_status` = '" . $aVals['status'] . "'");
		}
		/*
		if(isset($aVals['end_day']) && $aVals['end_day']>0
			&& isset($aVals['end_month'])
			&& $aVals['end_month']> 0 && isset($aVals['end_year']) && $aVals['end_year']>0)
		{
			$time_expire = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_month'], $aVals['end_day'], $aVals['end_year']);
			$time_expire = Phpfox::getService('jobposting.helper')->convertToUserTimeZone($time_expire);
			$this->search()->setCondition("AND petition.time_expire <=".$time_expire);
		}
		*/
		if ($aVals['search_by_date'])
		{
			if (isset($aVals['start_time_expire']))
			{
				$this->search()->setCondition("AND petition.end_time >".$aVals['start_time_expire']);
			}
			if (isset($aVals['end_time_expire']))
			{
				$this->search()->setCondition("AND petition.end_time <=".$aVals['end_time_expire']);
			}
		}

	}
	public function getAdvSearchFields()
	{
		$aVals = array();
		//$aVals['keywords'] = $this->search()->get('keywords');
		$keywords = $this->search()->get('keywords');
		if ($keywords==Phpfox::getPhrase("petition.search_petition_dot"))
		{
			$keywords = "";
		}
		$aVals['keywords'] = $keywords;
		$aCategory = $this->search()->get('category');
		$aVals['categories']  = "";
		if(isset($aCategory['search_0']))
		{
			$aValue = str_replace("search_", "", $aCategory['search_0']);

			$aVals['categories'].= $aValue;
			if(isset($aCategory["search_".$aValue]))
			{
				$aValue = str_replace("search_", "", $aCategory["search_".$aValue]);
				$aVals['categories'].= ",".$aValue;
			}
		}
		$aVals['status'] = $this->search()->get('status');

		$search_by_date = $this->search()->get('search_by_date');
		if (!empty($search_by_date))
		{
			$aVals['search_by_date'] = true;

			$post = $this->request()->getArray("val");
			if(isset($post['start_month']))
			{
				$time_expire_start = Phpfox::getLib('date')->mktime(23, 59, 59, $post['start_month'], $post['start_day'], $post['start_year']);
				$aVals['start_time_expire'] = $time_expire_start;
			}
			if(isset($post['end_month']))
			{
				$time_expire_start = Phpfox::getLib('date')->mktime(23, 59, 59, $post['end_month'], $post['end_day'], $post['end_year']);
				$aVals['end_time_expire'] = $time_expire_start;

			}

		}else
		{
			$aVals['search_by_date'] = false;
		}


		//var_dump($this->request()->getArray("val"));
		//var_dump($aVals);
		//die();
		return $aVals;
	}
}

?>

