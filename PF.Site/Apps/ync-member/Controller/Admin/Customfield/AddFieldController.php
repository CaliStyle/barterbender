<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YNC_Member\Controller\Admin\Customfield;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
defined('PHPFOX') or exit('NO DICE!');

class AddFieldController extends \Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {

        $bHideOptions = true;
        $iDefaultSelect = 4;
        $bIsEdit = false;
        $aForms = array('is_required' => '1');
        $iObjType = $this->request()->get('objtype');

        $sAction = $this->request()->get('action');

        $iGroupId = $this->getParam('iGroupId');
        if($sAction == 'add')
        {
            $iType = $this->request()->get('type');
        }
        elseif($sAction == 'edit')
        {
            $iId = $this->request()->get('id');
            $aField = Phpfox::getService('ynmember.custom')->getForCustomEdit($iId);
            if (isset($aField['field_id']))
            {
                $bIsEdit = true;
                $aForms = $aField;

                if (isset($aField['option']) && $aField['var_type'] == 'select')
                {
                    $bHideOptions = false;
                }
            }
        }
		elseif($sAction == 'delete')
		{
			$iId = $this->request()->get('id');
		}
        else
        {
            return \Phpfox_Error::set('Invalid action.');
        }

        $phrase = _p('are_you_sure_want_to_delete_this_custom_option');
		$phrase = str_replace("\r\n", "\\n", $phrase);

        $this->template()
        	->assign(array(
                'aLanguages' => Phpfox::getService('language')->getAll(),
                'urlModule' => Phpfox::getParam('core.url_module'),
                'bHideOptions' => $bHideOptions,
                'iDefaultSelect' => $iDefaultSelect,
                'aForms' => $aForms,
                'bIsEdit' => $bIsEdit,
                'iCompanyId' => 0,
                'iObjType' => $iObjType,
                'iId' => isset($iId) ? $iId : '',
                'iGroupId' => isset($iGroupId) ? $iGroupId : '',
                'phrase' => $phrase,
            ))
			;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

    }

}
