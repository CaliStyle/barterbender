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
class Document_Component_Block_Topusers  extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE'))
        {
            return false;
        }
        if ($this->request()->get('req2') == 'tag')
        {
            return false;
        }
        $sView = $this->getParam('sView');
        $is_search = $this->getParam('is_document_search');
        if ($is_search)
        {
            return false;
        }
        $sCondition = " m.in_process = 0 ";
        if (!isset($sView)) { $sView = "";}
        if ($sView == 'pending' || $sView == 'my')
        {
            return false;
        }
        if (!Phpfox::isUser())
        {
            return false;
        }
        $sQuery = Phpfox::getLib('database');
        $sQuery->select('u.*');
        
        $sQuery->from(PHpfox::getT('document'),'m');
        $sQuery->join(phpfox::getT('user'),'u', 'm.user_id = u.user_id');
        $sHeader = _p('top_activity_members');
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
                $sHeader = _p('top_active_friends');
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
                        $sCondition .= ' AND m.view_id = 0 AND m.module_id = \'document\' AND m.item_id = 0 AND m.privacy IN(%PRIVACY%)'; 
                        //$this->search()->setCondition('AND m.in_process = 0 AND m.view_id = 0 AND m.item_id = 0 AND m.privacy IN(%PRIVACY%)');
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

        $users_limit = $this->getParam('limit', 5);
        if ($users_limit <= 0)
        {
            return false;
        }else
        {
            $sQuery->limit(0,$users_limit); 
        }
        $sQuery->group(' m.user_id ');  
        $aRows = $sQuery->where($sCondition)
                       ->execute('getRows');
         
        $aDocuments = array(); 
        $count = 0;            
        if (count($aRows))
        {
        }else
        {
            //no data, disable this block
            return false;
        }
        $this->template()->assign(array(
                'sHeader' => $sHeader,
                'aUsers' => $aRows
            )
        );
        
        return 'block';        
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Top Active Members Limit'),
                'description' => _p('Define the limit of how many top active members can be displayed when viewing the document section. Set 0 will hide this block.'),
                'value' => 5,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Top Active Members Limit" must be greater than or equal to 0'
            ]
        ];
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        // Lets clear it from memory
        $this->template()->clean(array(
                'limit',
            )
        );

        (($sPlugin = Phpfox_Plugin::get('document.component_block_filter_clean')) ? eval($sPlugin) : false);
    }
}

?>