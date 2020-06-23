
<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

// Add and edit request both go here 
class Socialad_Component_Controller_Package_Choose extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if(!Phpfox::getService('socialad.permission')->canCreateAd($bRedirect = true)) { // redirect and permission checking function
		}
        $sMessage = '';
        $iSimilar = 0;
		if($iSimilar = $this->request()->getInt('createsimilar'))
        {
            $sMessage = _p('you_have_to_choose_other_package_cause_package_of_ad_you_clone_has_been_deleted');
        }

		$aPackages = Phpfox::getService('socialad.package')->getAllActivePackages();
		$this->template()->assign(array( 
			'aSaPackages' => $aPackages,
            'sMessage'  => $sMessage,
            'iSimilar' => $iSimilar
		));

		Phpfox::getService('socialad.helper')->loadSocialadJsCss();
		$this->template()->setTitle(_p('choose_your_package'))
			->setBreadcrumb(_p('ad'), $this->url()->makeUrl('socialad.ad'))
			->setBreadcrumb( _p('choose_your_package'), $this->url()->makeUrl('socialad.package.choose'), true);

	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

