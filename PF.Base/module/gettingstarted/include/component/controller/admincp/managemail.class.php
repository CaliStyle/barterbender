<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_component_controller_admincp_managemail extends Phpfox_Component{
	public function process()
	{
		if($_POST && $this->request()->get('testAddtoQueue'))
		{
			phpfox::getService('gettingstarted.process')->InsertMailToQueue(true);
		}
		if($_POST && $this->request()->get('testSendMail'))
		{
			phpfox::getService('gettingstarted.process')->sendMail(true);
		}
		if ($aDeleteIds = $this->request()->getArray('id'))
		{
			if (Phpfox::getService('gettingstarted.process')->deleteMultiple($aDeleteIds))
			{
				$this->url()->send('admincp.gettingstarted.managemail', null, _p('gettingstarted.scheduled_mail_successfully_deleted'));
			}
		}
		$scheduled_categories = phpfox::getService('gettingstarted')->getAllCategoryMail();
		$oFilter = Phpfox::getLib('parse.input');
		$aTypes = array();
		$aTypes[0] = _p('gettingstarted.any');
		foreach($scheduled_categories as $iKey=> $scheduled_category)
		{
			$aTypes[$scheduled_category['scheduledmail_id']] = $scheduled_category['scheduledmail_name'];
		}
		$aFilters = array(
                'title' => array(
                    'type' => 'input:text',
                    'search' => "[VALUE]"
                    ),
                'type' => array(
                    'type' => 'select',
                    'options' => $aTypes,
                    'default' => '1',
                    'search' =>"type_[VALUE]"
                    )
                    );
                    $oSearch = Phpfox::getLib('search')->set(array(
                    'type' => '',
                    'filters' => $aFilters,
                    'search' => 'search'
                    )
                    );
                     
                    $arrSearch = $oSearch->getConditions();
                    $title_search = "";
                    $category_search = 0;
                    if(count($arrSearch) > 2)
                    {
                    	$title_search=$arrSearch[0];
                    	$arrtemp_search = explode("type_", $arrSearch[1]);
                    	$category_search = $arrtemp_search[1];
                    }
                    else if(count($arrSearch == 2))
                    {
                    	$arrtemp_search = explode("type_", $arrSearch[0]);
                    	if(is_numeric($arrtemp_search[1]) == true)
                    	{
                    		$category_search = $arrtemp_search[1];
                    	}
                    }
                    $aSettings=phpfox::getService("gettingstarted.settings")->getSettings(0);

                    $bIsSearch = false;
                    $sSearch = $this->request()->get('search-id');
                    if($sSearch != '')
                    {
                    	$title_search = $oFilter->clean($title_search);
                    	$bIsSearch = true;
                    }
                    $iLimit=10;
                    $iPage = $this->request()->get("page");
                    if(!$iPage)
                    $iPage = 1;
                    $iCnt=Phpfox::getService("gettingstarted")->getCountMails($title_search, $category_search);
                    $aCategories = Phpfox::getService('gettingstarted')->getMails($title_search, $category_search, $iPage,$iLimit,$iCnt);
                    Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
					$this->template()->setBreadCrumb(_p('gettingstarted.manage_mails'), $this->url()->makeUrl('admincp.gettingstarted.managemail'));
                    $this->template()
                    ->assign(array(
                        'aCategories' => $aCategories,
                        'corepath' => phpfox::getParam("core.path"),
						'bIsSearch' => $bIsSearch
                    )
                    )
                    ->setHeader('cache', array(
                    'quick_edit.js' => 'static_script'
                    )
                    );
	}
}
?>
