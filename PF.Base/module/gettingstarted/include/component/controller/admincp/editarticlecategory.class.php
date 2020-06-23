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

class Gettingstarted_component_controller_admincp_editarticlecategory extends Phpfox_Component{

	public function process()
	{
		$id=$this->request()->get("id");

		if(isset($_POST['submit_editarticlecategory'])==true)
		{

			$aVals=$this->request()->get('val');
			if($aVals['title']== "")
			{
				Phpfox_Error::set(_p('gettingstarted.knowledge_base_category_is_not_allowed_empty'));
				$bool_test = 1;
			}
			if($bool_test==0)
			{
				$link="admincp/gettingstarted/editarticlecategory/id_".$aVals["article_category_id"];
				Phpfox::getService("gettingstarted.articlecategory")->UpdateArticleCategoryById($aVals);
				$this->url()->send('admincp.gettingstarted.managearticlecategory', null, _p('gettingstarted.edit_knowledge_base_category_successfully'));
			}
		}

		$category_mail=phpfox::getService('gettingstarted.articlecategory')->getArticleCategoryById($id);

		$boolean_id=0;
		if(count($category_mail)==0)
		{
			Phpfox_Error::set(_p('gettingstarted.id_not_found'));
			$boolean_id=1;
		}

		$this->template()->assign(array(

               'boolean_id' => $boolean_id,
               'scheduled_mail' => $category_mail,
		));

	}
}
?>

