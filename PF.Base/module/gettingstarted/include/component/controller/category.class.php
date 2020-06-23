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

class Gettingstarted_component_controller_categories extends Phpfox_Component{
	public function process()
	{
            
		$settings = phpfox::getService('gettingstarted.settings')->getSettings(0);
		if (isset($settings['active_knowledge_base']))
		{
			if($settings['active_knowledge_base'] == false)
			{
				Phpfox::getLib('database')->update(phpfox::getT('menu'),array('is_active'=>0),'m_connection="'.'main'.'" and module_id="'."gettingstarted".'"');
				return Phpfox::getLib('module')->setController('error.404');
			}
			else 
			{
				Phpfox::getLib('database')->update(phpfox::getT('menu'),array('is_active'=>1),'m_connection="'.'main'.'" and module_id="'."gettingstarted".'"');
			}
		}
		$iId = $this->request()->get('category');
                
                
		$iLimit=10;
		$iPage = $this->request()->get("page");
		if(!$iPage)
		{
			$iPage = 1;
		}
		$iCnt=Phpfox::getService("gettingstarted.articlecategory")->getCountArticlePaginationId($iId);
		$aArticle=Phpfox::getService("gettingstarted.articlecategory")->getArticlePaginationId($iId,$iPage,$iLimit,$iCnt);
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
		$ArticleCategory = Phpfox::getService('gettingstarted.articlecategory')->getArticleCategoryById($iId);

		$this->template()->assign(array(
               'aArticle' => $aArticle,
               'ArticleCategory' => $ArticleCategory,
		))->setHeader(array(
                    'pager.css' => 'style_css',
		));
	}
}
?>