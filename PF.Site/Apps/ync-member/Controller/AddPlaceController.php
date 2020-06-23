<?php

/**
 * [PHPFOX_HEADER]
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		PhuongNV
 * @package  		yn_member
 */

namespace Apps\YNC_Member\Controller;

use Phpfox;
use Phpfox_Component;

class AddPlaceController extends Phpfox_Component
{
    private $_aTypes;

    public function __construct(array $aParams)
    {
        $this->_aTypes = [
            'work',
            'study',
            'live',
        ];
    }

    public function process()
    {
        // require user and redirect to permission missing page
        Phpfox::isUser(true);
        $sType = $this->request()->get('type', '');

        $bIsEdit = false;

        if ($iPlaceId = $this->request()->get('id')) {
            $bIsEdit = true;
            $aRow = Phpfox::getService('ynmember.place')->getForEdit($iPlaceId);
            if ($aRow) {
                $this->template()->assign(array(
                        'aForms' => $aRow
                    )
                );
                $sType = $aRow['type'];
            }
        }

        $bRequireTitle = 0;
        $sTitle = $sCurrentlyMsg = '';
        switch ($sType) {
            case 'work':
                $bRequireTitle = 1;
                $sCurrentlyMsg = _p('I\'m currently working here');
                $sTitle = $bIsEdit ?  _p('Edit workplace') : _p('Add a workplace');
                break;
            case 'study':
                $bRequireTitle = 1;
                $sCurrentlyMsg = _p('I\'m currently study here');
                $sTitle = $bIsEdit ? _p('Edit study place') : _p('Add a school');
                break;
            case 'living':
                $sTitle = $bIsEdit ? _p('Edit living place') : _p('Add a living place');
                break;
            case 'lived':
                $sTitle = $bIsEdit ? _p('Edit lived place') : _p('Add a lived place');
                break;
            default:
                break;
        }

        $this->template()->setTitle($sTitle);

        $this->template()->assign([
            'bIsEdit' => $bIsEdit,
            'sType' => $sType,
            'bRequireTitle' => $bRequireTitle,
            'sTitle' => $sTitle,
            'sCurrentMessage' => $sCurrentlyMsg,
            'apiKey' => Phpfox::getParam('core.google_api_key'),
        ]);
        return true;
    }
}