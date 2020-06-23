<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class FeedBack_Component_Block_EditCategory extends Phpfox_Component
{
    public function process()
    {
        $category_id = $this->getParam('category_id');
        $page = $this->getParam('page');
        $aCat = Phpfox::getService('feedback')->getFeedBackCatForEdit($category_id);
        $aLanguages = Phpfox::getService('language')->getAll();
        $this->template()->assign(array(
            'sHeader' => _p('Edit Category'),
            'aForms' => $aCat,
        	'page'=>$page,
            'aLanguages' => $aLanguages,
        ));

        return 'block';

    }
}
?>