<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YNC_Member\Controller\Admin\Customfield;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
defined('PHPFOX') or exit('NO DICE!');

class IndexController extends \Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$bOrderUpdated = false;

		if (($this->request()->get('req6') && $this->request()->get('req5') == 'delete') && Phpfox::getService('ynmember.custom.group')->deleteGroup($this->request()->get('req6')))
		{
			$this->url()->send('admincp.app', [
                    'id' => 'yn_member'
                ],_p('custom_fields_successfully_deleted'));
		}

		if (($aFieldOrders = $this->request()->getArray('field')) && Phpfox::getService('ynmember.custom.process')->updateOrder($aFieldOrders))
		{
			$bOrderUpdated = true;
		}

		if (($aGroupOrders = $this->request()->getArray('group')) && Phpfox::getService('ynmember.custom.group')->updateOrder($aGroupOrders))
		{
			$bOrderUpdated = true;
		}

		if ($bOrderUpdated === true)
		{
			$this->url()->send('admincp.app', [
                    'id' => 'yn_member'
                ],_p('custom_fields_successfully_updated'));
		}

		$aGroups = Phpfox::getService('ynmember.custom.group')->getForListing();
		$corePath = Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-member';
		$this->template()->setTitle(_p('Manage Review Custom Field Groups'))
			->setBreadcrumb(_p('Manage Review Custom Field Groups'))
			->setHeader(array(
					'<script type="text/javascript">$Behavior.custom_set_url = function() { $Core.customgroup.url(\'' . $this->url()->makeUrl('admincp.ynmember.customfield') . '\'); };</script>',
					'jquery/ui.js' => 'static_script',
					'<script type="text/javascript">$Behavior.custom_admin_addSort = function(){$Core.customgroup.addSort();};</script>'
				)
			)
			->assign(array(
					'aGroups' => $aGroups,
					'corePath' => $corePath,
					'sUrl' => $this->url()->makeUrl('admincp.ynmember.customfield')
				)
			);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('custom.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>
