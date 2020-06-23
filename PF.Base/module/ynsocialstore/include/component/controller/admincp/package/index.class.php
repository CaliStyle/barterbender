<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/4/16
 * Time: 10:31 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Admincp_Package_Index extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iPage = $this->request()->getInt('page');
        $iPageSize 	= 10;

        $iCount = Phpfox::getService('ynsocialstore.package')->getItemCount();
        $aPackages = Phpfox::getService('ynsocialstore.package')->getPackages($iPage, $iPageSize, $iCount);

        PhpFox::getLib('pager')->set(array(
            'page'  => $iPage,
            'size'  => $iPageSize,
            'count' => $iCount
        ));


        $this->template()
            ->setTitle(_p('manage_packages'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_ynsocialstore'), $this->url()->makeUrl('admincp.app').'?id=__module_ynsocialstore')
            ->setBreadcrumb(_p('manage_packages'))
            ->assign(array(
                    'aPackages' => $aPackages,
                )
            )->setHeader(array(
                'package_index.js'			 => 'module_ynsocialstore',
            ));
        $this->template()->setPhrase(array(
            'ynsocialstore.are_you_sure',
            'ynsocialstore.yes',
            'ynsocialstore.no',
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