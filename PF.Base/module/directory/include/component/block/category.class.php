<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Category extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $sCategory = $this->getParam('sCategory');
        $aCategories = Phpfox::getService('directory.category')->getForBrowse($sCategory);
        if (empty($aCategories)) {
            return false;
        }

        if (!is_array($aCategories)) {
            return false;
        }

        $this->template()->assign(array(
                'aCategories' => $aCategories,
                'sHeader' => _p('directory.categories'),
                'sCustomClassName' => 'ync-block'
            )
        );


        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

    }
}

?>