
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
class Socialad_Component_Controller_Faq extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);

        $aFAQ = Phpfox::getService('socialad.faq')->get_frontend();

        $result = array();
        foreach($aFAQ as $key => $val){
        	if(!empty($val['answer'])){
        		$result[] = $val;
        	}
        }

        $this->template()->assign(array(
                'aFAQ' => $result,
                'corePath' => Phpfox::getParam('core.path'),
            )
        );

		$this->template()	
			->setBreadcrumb(_p('ad'), $this->url()->makeUrl('socialad.ad'))
			->setBreadcrumb(_p('faqs'), $this->url()->makeUrl('socialad.faq'), true);
		Phpfox::getService('socialad.helper')->loadSocialAdJsCss();
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

