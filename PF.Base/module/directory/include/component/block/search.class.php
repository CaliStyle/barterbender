<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Search extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iCategory = $this->getParam('category');
        $sCategories = Phpfox::getService('directory.category')->display('searchblock')->get($iCategory);
        $sSearch = $this->request()->get('search');
                
        $this->template()->assign(array(
            'sSearch' => isset($sSearch['search']) ? urldecode($sSearch['search']) : urldecode($sSearch),
            'sHeader' => _p('directory.search'),
            'sCategories' => $sCategories,
            'sCustomClassName' => 'ync-block'
        ));

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
    }
}
