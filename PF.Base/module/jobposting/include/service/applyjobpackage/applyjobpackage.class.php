<?php

defined('PHPFOX') or exit('NO DICE!');

class JobPosting_Service_Applyjobpackage_Applyjobpackage extends Phpfox_service {
	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('jobposting_applyjobpackage');
        $this->_sTableData = Phpfox::getT('jobposting_applyjobpackage_data');
	}
	
	public function getById($package_id)
	{
		$aRow = $this->database()->select('*')
			->from($this->_sTable)
			->where('package_id = '.(int)$package_id)
			->execute('getRow');
			
		return $aRow;
	}
    
    public function getByDataId($iDataId, $bValid = false)
    {
        $sCond = 'pd.data_id = '.(int)$iDataId;
        
        if ($bValid)
        {
            $sCond .= ' AND pd.status = 3 AND (p.apply_number <= 0 OR (p.apply_number > 0 AND pd.remaining_apply > 0)) AND (p.expire_type <= 0 OR (p.expire_type > 0 AND pd.expire_time > '.PHPFOX_TIME.'))';
        }
        
        $aRow = $this->database()->select('pd.*, p.*')
            ->from($this->_sTableData, 'pd')
            ->join($this->_sTable, 'p', 'p.package_id = pd.package_id')
            ->where($sCond)
            ->execute('getSlaveRow');
        
        return $aRow;
    }
	
	public function getPackages($iPage = 0, $iLimit = 0, $iCount = 0)
	{						
		$oSelect = $this -> database() 
						 -> select('*')
						 -> from($this->_sTable, 'pk');
						 
		$oSelect->limit($iPage, $iLimit, $iCount);

		$aPackages = $oSelect->execute('getRows');
		
	 	return $aPackages;
	}
	
	public function getItemCount()
	{			
		$oQuery = $this -> database()
						-> select('count(*)')
						-> from($this->_sTable,'pk');
						
		return $oQuery->execute('getSlaveField');
	}
    
    public function getPackageByDataId($iDataId)
    {
        $aPackage = $this->database()->select('p.*')
            ->from($this->_sTable, 'p')
            ->where('p.package_id = (SELECT pd.package_id FROM '.$this->_sTableData.' pd WHERE pd.data_id = '.$iDataId.')')
            ->execute('getRow');
        
        return $aPackage;
    }
    
    public function getBoughtPackages($iUserId, $bValid = false)
    {
        if ($bValid)
        {
            $aPackages = $this->database()->select('pd.*, p.*')
                ->from($this->_sTableData, 'pd')
                ->join($this->_sTable, 'p', 'p.package_id = pd.package_id')
                ->where('pd.user_id = '.$iUserId.' AND pd.status = 3 AND (p.apply_number <= 0 OR (p.apply_number > 0 AND pd.remaining_apply > 0)) AND (p.expire_type <= 0 OR (p.expire_type > 0 AND pd.expire_time > '.PHPFOX_TIME.'))')
                ->order('pd.data_id ASC')
                ->execute('getSlaveRows');
        }
        else
        {
            $aPackages = $this->database()->select('pd.*, p.*')
                ->from($this->_sTableData, 'pd')
                ->join($this->_sTable, 'p', 'p.package_id = pd.package_id')
                ->where('pd.user_id = '.(int)$iUserId. " and pd.status = 3")
                ->order('pd.data_id ASC')
                ->execute('getSlaveRows');
        }
      
        if(count($aPackages))
        {
            foreach($aPackages as $k => $aPackage)
            {
            	$aPackages[$k]['status_text'] = _p(''.Phpfox::getService('jobposting.transaction')->getStatusNameById($aPackage['status']));
                $aPackages[$k]['fee_text'] = PHpfox::getService('jobposting.helper')->getTextParseCurrency($aPackage['fee']);
				
                if($aPackage['status']!=3)
				{
					$aPackages[$k]['expire_text'] = _p('n_a');
					$aPackages[$k]['expire_text_2'] = _p('n_a');
				}
				else
				{
					if($aPackage['expire_type'] == 0)
	                {
	                    $aPackages[$k]['expire_text'] = _p('never_expired');
						$aPackages[$k]['expire_text_2'] = _p('never_expired');
	                }
	                else
	                {
	                    $aPackages[$k]['expire_text'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aPackage['valid_time'], false).' - '.Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aPackage['expire_time'], false);
						$aPackages[$k]['expire_text_2'] = _p('from').' '.Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aPackage['valid_time'], false).' '._p('to').' '.Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aPackage['expire_time'], false);
	                }
				}
            }
        }
        
        return $aPackages;
    }
    
    public function getToBuyPackages($iUserId)
    {
        $aPackages = $this->database()->select('p.*')
            ->from($this->_sTable, 'p')
            ->where('p.package_id NOT IN (SELECT pd.package_id FROM '.$this->_sTableData.' pd LEFT JOIN '.$this->_sTable.' p1 ON pd.package_id=p1.package_id WHERE pd.user_id = '.$iUserId.' AND pd.status = 3 AND (p1.apply_number <= 0 OR (p1.apply_number > 0 AND pd.remaining_apply > 0)) AND (p1.expire_type <= 0 OR (p1.expire_type > 0 AND pd.expire_time > '.PHPFOX_TIME.'))) AND p.active = 1')
            ->order('p.package_id ASC')
            ->execute('getSlaveRows');
        
        if(count($aPackages))
        {
            foreach($aPackages as $k => $aPackage)
            {
            	
            	$aPackages[$k]['fee_text'] = PHpfox::getService('jobposting.helper')->getTextParseCurrency($aPackage['fee']);
				
                if($aPackage['expire_type'] == 0)
                {
                    $aPackages[$k]['expire_text'] = _p('never_expired');
                }
                else
                {
                    $aPackages[$k]['expire_text'] = _p('period').' '.$aPackage['expire_number'].' ';
                    switch($aPackage['expire_type'])
                    {
                        case 1:
                            $aPackages[$k]['expire_text'] .= ($aPackage['expire_number'] > 1) ? _p('day_plural') : _p('day_singular');
                            break;
                        case 2:
                            $aPackages[$k]['expire_text'] .= ($aPackage['expire_number'] > 1) ? _p('week_plural') : _p('week_singular');
                            break;
                        case 3:
                            $aPackages[$k]['expire_text'] .= ($aPackage['expire_number'] > 1) ? _p('month_plural') : _p('month_singular');
                    }
                }
            }
        }
        
        return $aPackages;
    }
    
    public function buildHtmlBoughtPackages($iUserId)
    {
        $sHtml = '<tr>
                        <th align="left">'._p('package_name').'</th>
                        <th>'._p('fee').'</th>
                        <th>'._p('remaining_job_posts').'</th>
                        <th>'._p('valid_time').'</th>		
                        <th>'._p('payment_status').'</th>
                    </tr>';
        
        $aPackages = $this->getBoughtPackages($iUserId);
        if(is_array($aPackages) && count($aPackages))
        {
            foreach($aPackages as $k => $aPackage)
            {
                $sHtml .= '<tr'.(($k%2 != 0) ? ' class="on"' : '').'>
                        <td>'.$aPackage['name'].'</td>
                        <td class="t_center">$'.$aPackage['fee'].'</td>
                        <td class="t_center">'.(($aPackage['apply_number'] == 0) ? _p('unlimited') : $aPackage['remaining_apply']).'</td>
                        <td class="t_center">'.$aPackage['expire_text'].'</td>
                        <td class="t_center">'.$aPackage['status_text'].'</td>		
                    </tr>';
            }
        }
        else
        {
            $sHtml .= '<tr><td colspan="5"><div class="extra_info">'._p('no_package_found').'</div></td></tr>';
        }
        
        return $sHtml;
    }
    
    public function buildHtmlToBuyPackages($iUserId)
    {
        $sHtml = '';
        
        $aPackages = $this->getToBuyPackages($iUserId);
        if(is_array($aPackages) && count($aPackages))
        {
            foreach($aPackages as $k => $aPackage)
            {
                $sHtml .= '<li><label><input type="checkbox" name="val[packages][]" value="'.$aPackage['package_id'].'" id="js_jc_package_'.$aPackage['package_id'].'" class="js_jc_package" fee_value="'.$aPackage['fee'].'" />';
                $sHtml .= $aPackage['name'];
                $sHtml .= ' - $'.$aPackage['fee'];
                $sHtml .= ' - '.(($aPackage['apply_number']==0) ? _p('unlimited') : _p('remaining').$aPackage['apply_number'].' '._p('job_posts'));
                $sHtml .= ' - '.$aPackage['expire_text'].'</label></li>';
            }
        }
        else
        {
            $sHtml .= '<li><div class="extra_info">'._p('no_package_found').'</div></li>';
        }
        
        return $sHtml;
    }
}

?>