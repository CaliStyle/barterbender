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

class Gettingstarted_component_controller_admincp_editmanagecategory extends Phpfox_Component{

	public function process()
	{
            $id=$this->request()->get("id");

            if(isset($_POST['submit_editscheduledmail'])==true)
            {

                $aVals=$this->request()->get('val');
                $link="admincp/gettingstarted/editmanagecategory/id_".$aVals["scheduledmail_id"];

                Phpfox::getService("gettingstarted")->updateCategoryMail($aVals);
                $this->url()->send($link,null,"Edit Category Mail successfully");
            }

            $category_mail=phpfox::getService('gettingstarted')->getAllCategoryMailId($id);
            
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
