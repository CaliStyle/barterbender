<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
class Document_Component_Block_Statistic extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {

        $sView = $this->getParam('sView');
        if ($this->request()->get('req2') == 'tag')
        {
            return false;
        }
        $is_search = $this->getParam('is_document_search');
        if ($is_search)
        {
            return false;
        }
        $sCondition = " m.in_process = 0 AND m.is_approved = 1 ";
        if (!isset($sView)) { $sView = "";}
        if ($sView == 'pending')
        {
            return false;
        }
        $sQuery = Phpfox::getLib('database');
        $sQuery->select(' count(*) as total_documents, sum(m.total_view) as total_views, sum(m.total_like) as total_likes');

        $sQuery->from(PHpfox::getT('document'),'m');
        $bIsProfile = $this->getParam('bIsProfile');
        $aUser = $this->getParam('aUser');
        switch ($sView)
        {
            case 'pending':
                    //in Pending view, disable this block
                   return false;
            case 'my':
                Phpfox::isUser(true);
                $sCondition .= " AND m.user_id = " . Phpfox::getUserId() . ' AND m.privacy IN(%PRIVACY%)';
                break;
            case 'friend':
                $sQuery->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = m.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
                $sCondition .= ' AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) ';
            default:
                if ($bIsProfile)
                {
                    $sCondition .= ' AND m.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND m.item_id = 0 AND m.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('privacy')->getForBrowse($aUser)) . ') AND m.user_id = ' . (int) $aUser['user_id'];
                }else
                {
                    if (defined('PHPFOX_IS_PAGES_VIEW'))
                    {
                        $aParentModule = $this->getParam('aParentModule');
                        $sCondition .= ' AND m.view_id = 0 AND m.module_id = \'' . Phpfox::getLib('database')->escape($aParentModule['module_id']) . '\' AND m.item_id = ' . (int) $aParentModule['item_id'] . ' AND m.privacy IN(%PRIVACY%)';
                       // $this->search()->setCondition('AND m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'' . Phpfox::getLib('database')->escape($aParentModule['module_id']) . '\' AND m.item_id = ' . (int) $aParentModule['item_id'] . ' AND m.privacy IN(%PRIVACY%)');
                    }
                    else
                    {
                        $sCondition .= '  AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) ' . Phpfox::getService('document')->getConditionsForSettingPageGroup();
                    }
                }
                break;
        }

       switch ($sView)
        {
            case 'friend':
                $sCondition = str_replace('%PRIVACY%', '0,1,2', $sCondition);
                break;
            case 'my':
                $sCondition = str_replace('%PRIVACY%', '0,1,2,3,4', $sCondition);
                break;
            default:
                $sCondition = str_replace('%PRIVACY%', '0', $sCondition);
                break;
        }

        $sCategory = $this->getParam('sCategory');
        if (isset($sCategory) && $sCategory)
        {
            $sQuery->join(phpfox::getT('document_category_data'),'dcd', 'm.document_id = dcd.document_id');
            $sCondition .= " AND dcd.category_id = " . $sCategory;
        }
        $aRow = $sQuery->where($sCondition)
                       ->execute('getRow');

        if ($aRow['total_documents'] == 0)
        {
            //no data, disable this block
            return false;
        }
        $this->template()->assign(array(
                'sHeader' => _p('document_statistic'),
                'aRow' => $aRow
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
        (($sPlugin = Phpfox_Plugin::get('document.component_block_filter_clean')) ? eval($sPlugin) : false);
    }
}

?>