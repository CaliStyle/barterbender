<?php

defined('PHPFOX') or exit('NO DICE!');

class JobPosting_Component_Block_Category extends Phpfox_Component {

    public function process() {
        $bIsProfile = false;
        if ($this->getParam('bIsProfile') === true && ($aUser = $this->getParam('aUser'))) {
            $bIsProfile = true;
        }

        $aCategories = Phpfox::getService('jobposting.category')->getForBrowse();
        if (!is_array($aCategories)) {
            return false;
        }

        if (!$aCategories) {
            return false;
        }
		if($this->request()->get('req2')=="company")
		{
			foreach ($aCategories as $iKey => $aCategory) {
	            $aCategories[$iKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.jobposting.company.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']) : $this->url()->permalink(array('jobposting.company.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']));
	            if (isset($aCategory['sub'])) {
	                foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory) {
	                    $aCategories[$iKey]['sub'][$iSubKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.jobposting.company.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']) : $this->url()->permalink(array('jobposting.company.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']));
	                }
	            }
        	}	
		}
		else {
			foreach ($aCategories as $iKey => $aCategory) {
	            $aCategories[$iKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.jobposting.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']) : $this->url()->permalink(array('jobposting.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']));
	            if (isset($aCategory['sub'])) {
	                foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory) {
	                    $aCategories[$iKey]['sub'][$iSubKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.jobposting.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']) : $this->url()->permalink(array('jobposting.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']));
	                }
	            }
	        }	
		}
        

     
        $this->template()->assign(array(
            'sHeader' => _p('industry'),
            'aCategories' => $aCategories,
            'iCategoryFundraisingView' => $this->request()->getInt('req3')
                )
        );

        (($sPlugin = Phpfox_Plugin::get('jobposting.component_block_categories_process')) ? eval($sPlugin) : false);

        return 'block';
    }

    public function clean() {
        $this->template()->clean(array(
            'aCategories'
                )
        );

        (($sPlugin = Phpfox_Plugin::get('jobposting.component_block_categories_clean')) ? eval($sPlugin) : false);
    }

}