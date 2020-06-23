<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FoxFeedsPro
 * @version          3.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class FoxFeedsPro_Component_Block_Edit_Category extends Phpfox_Component
{    
    public function process()
    {
      
        $cat_id =  $this->getParam('cat_id');
        $iPage = $this->getParam('iPage');
        $cats = phpfox::getLib('phpfox.database')->select('*')
                       ->from(Phpfox::getT('ynnews_categories'))
                       ->where('category_id = '.$cat_id)
                       ->execute('getRow'); 
       
        $this->template()->assign(array(
        								'cat_edit'=>$cats,
        								'iPage'=>$iPage
        						));
             
        return 'block';
    }

}

?>