<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynchat_Component_Controller_Admincp_Banword extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aBanFilter = array(
				'title' => Phpfox::getPhrase('ynchat.words'),
				'type' => 'word',
				'url' => 'admincp.ynchat.banword',
				'form' => Phpfox::getPhrase('ynchat.words'),
				'replace' => true
        );
		
		if (($iDeleteId = $this->request()->getInt('delete')))
		{
			if (Phpfox::getService('ynchat')->deleteBan($iDeleteId))
			{
				$this->url()->send($aBanFilter['url'], null, Phpfox::getPhrase('ynchat.filter_successfully_deleted'));
			}
		}
		
		if (($this->request()->get('add_ban')))
		{
            $sBanValue = $this->request()->get('find_value');
			$aBan = $this->request()->getArray('aBan');

			$aVals = array_merge(array(
						'type_id' => $aBanFilter['type'],
						'find_value' => $sBanValue,
						'replacement' => $this->request()->get('replacement', null)
					),$aBan);
			if (Phpfox::getService('ynchat')->addBan($aVals, $aBanFilter))
			{
				$this->url()->send($aBanFilter['url'], null, Phpfox::getPhrase('ynchat.filter_successfully_added'));
			}
		}

		$aFilters = Phpfox::getService('ynchat')->getBanFilters($aBanFilter['type']);
		foreach ($aFilters as $iKey => $aFilter)
		{
			$aFilters[$iKey]['s_user_groups_affected'] = '';
			if (is_array($aFilter['user_groups_affected']))
			{
				foreach ($aFilter['user_groups_affected'] as $aGroup)
				{
					$aFilters[$iKey]['s_user_groups_affected'] .= Phpfox::getLib('locale')->convert($aGroup['title']) . ', ';
				}
				$aFilters[$iKey]['s_user_groups_affected'] = rtrim($aFilters[$iKey]['s_user_groups_affected'], ', ');
			}
		}
		$this->template()->setTitle(Phpfox::getPhrase('ynchat.words'))
			->setBreadcrumb(Phpfox::getPhrase('ynchat.words'))
			->setBreadcrumb(Phpfox::getPhrase('ynchat.words'), null, true)
			->assign(array(
					'aFilters' => $aFilters,
					'aBanFilter' => $aBanFilter
				)
			);			
	}

}

?>