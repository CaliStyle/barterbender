<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/14/16
 * Time: 18:54
 */
class Ynsocialstore_Component_Controller_Store_Embed extends Phpfox_Component
{

    public function process(){
        $iStoreId = $this->request()->get('req4');
        if(!$iStoreId)
        {
            exit(_p('invalid_param'));
        }
        $aStore = Phpfox::getService('ynsocialstore')->getStoreForDetailById($iStoreId);
        if(!$aStore)
        {
            exit(_p('unable_to_find_the_store_you_are_looking_for'));
        }
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        $aStaticFiles = $this->template()->getHeader(true);
        foreach($aStaticFiles as $key => $sFile)
        {
            if(!preg_match('/jscript\/jquery\/jquery.js/i',$sFile)){
                unset($aStaticFiles[$key]);
            }
        }
        $sJs = $this->template()->getHeader();
        $this -> template() -> assign(array(
                                  'aStore'	=> $aStore,
                                  'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
                                  'sJs' => $sJs,
                                  'aFiles' => $aStaticFiles,

                                      ));
        Phpfox_Module::instance()->getControllerTemplate();
        die;
    }
}