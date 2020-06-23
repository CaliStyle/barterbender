<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: sample.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
class Suggestion_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 *
	 */

	public function process()
	{
		$view = $this -> request() -> get('view') . '';
        $countObj = Phpfox::getService('suggestion')->getAllCoutObj();
		$bIsFriends = true;
		if (strpos($view, 'my') !== false)
		{
			$bIsFriends = false;
		}

		if ($view == '')
			$this -> url() -> send('suggestion.view_friends');

		if ($view == 'redirect')
		{
			$iFriendId = (int)$this -> request() -> get('iFriendId');
			$iItemid = (int)$this -> request() -> get('iItemid');
			$sModule = $this -> request() -> get('sModule') . '';
			$sRedirect = $this -> request() -> get('sRedirect') . '';

			Phpfox::getService('suggestion.process') -> approve($iFriendId, $iItemid, $iApprove = 1, $sModule);
			$sLink = '';
			$aRedirect = explode('__', $sRedirect);
			$aRedirect = str_replace(' ','+', $aRedirect);
			$sLink = base64_decode($aRedirect[0]);

			for ($i = 1; $i < count($aRedirect); $i++)
			{
				$sLink .= base64_decode($aRedirect[$i]);
			}
			$this -> url() -> send(urldecode($sLink));
		}

		$this -> template() -> assign(array('bShowPending' => false, 'bShowFilter' => false));
		$sSupportModule = Phpfox::getUserParam('suggestion.support_module');
		$rSort = array();
		if ($sSupportModule != '')
		{
			$sSupportModule = explode(',', $sSupportModule);
			$aSort[] = array('link' => "all", 'phrase' => _p('suggestion.all'));
			foreach ($sSupportModule as $sModule)
			{
				if (Phpfox::isModule($sModule))
				{
					$rSort[] = 'suggestion_' . $sModule;
					$sModuleUpcase = ucfirst($sModule);
					$aSort[] = array('link' => $sModuleUpcase, 'phrase' => $sModuleUpcase);
				}
			}
		} else
		{
			$aSort = array();
		}

		/*
		 * process filter display by module name
		 * change default module to suggestion_<Filter>
		 */

		$sView = $this -> request() -> get('view', 'incoming');

		$sKey = Phpfox::getService('suggestion') -> getSearchKey($sView);

		if ($sKey != '')
		{
			//has key search
			$sKey = Phpfox::getLib('parse.input') -> convert($sKey);
			$sKeySearch = ' AND (user.full_name like "%' . $sKey . '%" OR user0.full_name like "%' . $sKey . '%")';
		} else
		{
			$sKey = _p('suggestion.search_suggestions') . '...';
			$sKeySearch = '';
		}

		$_SESSION['suggestion']['pending'] = 0;
		$aRows = phpfox::getService("suggestion") -> getAllSuggestion(array('sView' => $sView, 'rSort' => $rSort, 'limit' => Phpfox::getParam('suggestion.number_item_on_other_block')));

		$aRow1 = array();
		$aRowAll = array();
        $ModuleName = array();
        $sView = $this->request()->get('view');
        $bAll = false;
        if ($sView == 'my' || $sView == 'friends') {
            $bAll = true;
            foreach ($sSupportModule as $sModule) {
                if (Phpfox::isModule($sModule)) {
                    if ($sView == 'my') {
                        $aRowAll[$sModule]['count'] = $countObj['my'][$sModule];
                        if ($aRowAll[$sModule]['count'] > 0) {
                            $ModuleName[] = substr($aRows['my' . $sModule][0]['module_id'], 11);
                            $moduleId[] = $aRows['my' . $sModule][0]['module_id'];
                        }

                    } else {
                        $aRowAll[$sModule]['count'] = $countObj['friends'][$sModule];
                        if ($aRowAll[$sModule]['count'] > 0) {
                            $ModuleName[] = substr($aRows['friends' . $sModule][0]['module_id'], 11);
                            $moduleId[] = $aRows['friends' . $sModule][0]['module_id'];
                        }
                    }
                }
            }
        }
        else {
            if ($bIsFriends) {
                $sModule = substr($aRows[$sView][0]['module_id'], 11);
                $aRow1['count'] = $countObj['friends'][$sModule];

            } else {
                $sModule = substr($aRows[$sView][0]['module_id'], 11);
                $aRow1['count'] = $countObj['my'][$sModule];
            }
        }

        for ($i = 0; $i < count($moduleId); $i++){
            $aRowAll[$i] = $aRowAll[$ModuleName[$i]]['count'];
        }

		$this -> template() -> setHeader(array('suggestion.js' => 'module_suggestion')) -> assign(array('sFullUrl' => Phpfox::getParam('core.path'), 'aRows' => $aRows, 'sKey' => $sKey, 'sView' => $sView, 'bIsFriends' => $bIsFriends));
		Phpfox::getLib('pager') -> set(array('page' => $this -> search() -> getPage(), 'size' => $this -> search() -> getDisplay(), 'count' => $this -> search() -> browse() -> getCount()));
		$this -> template() -> setBreadcrumb(_p('suggestion.suggestion'), $this -> url() -> makeUrl('suggestion'));
        $this->template()->assign(array(
            'iLimit' => Phpfox::getParam('suggestion.number_item_on_other_block'),
            'bAll' => $bAll,
            'aRow1' => $aRow1,
            'aRowAll' => $aRowAll,
            'ModuleName' => $ModuleName,
            'moduleId' => $moduleId,
            'iModuleActive' => count($moduleId)
        ));
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('suggestion.component_controller_index_clean')) ? eval($sPlugin) : false);
	}

}
?>