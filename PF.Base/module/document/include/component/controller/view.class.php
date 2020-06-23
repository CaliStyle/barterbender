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
class Document_Component_Controller_View extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getUserParam('document.can_view_documents',true);
        $aCallback = $this->getParam('aCallback', false);
        $iDocument = $this->request()->getInt(($aCallback !== false ? $aCallback['request'] : 'req2'));

        if (isset($iDocument) && $iDocument != "")
        {
            $aDocument = Phpfox::getService('document.process')->getDocument($iDocument);
            if (!isset($aDocument['document_id']))
            {            
                return Phpfox_Error::display(_p('document_not_found'));
            }
            // create a shorten copy of description
            $aDocument["text_shorten"] = Phpfox::getLib('parse.output')->shorten(Phpfox::getParam('core.allow_html') ? $aDocument["text_parsed"] : $aDocument['text'], 100, "...");
            $aMissingTags = array();
            preg_match_all('/<(\w+)[^\/]*?>/i', $aDocument["text_shorten"], $aTemp);
            if(!empty($aTemp[1]))
            {
                $aMissingTags = $aTemp[1];
                preg_match_all('/<\/(\w+)>/i', $aDocument["text_shorten"], $aTemp2);
                if(!empty($aTemp2[1]))
                foreach($aMissingTags as $iKey => $sOpenTag)
                {
                    $sOpenTag = trim($sOpenTag);
					$t2 = $aTemp2[1]; 
                    foreach($t2 as $iKey2 => $sCloseTag)
                    {
                        $sCloseTag = trim($sCloseTag);
                        if($sOpenTag == $sCloseTag)
                        {
                            $aMissingTags[$iKey] = "";
                            $aTemp2[$iKey2] = "";
                        }
                    }
                }
            }
            $sSubfix = "";
            foreach($aMissingTags as $sTag)
            {
                if(in_array($sTag, array("br")))
                    continue;
                if(!empty($sTag))
                {
                    $sSubfix .= "</$sTag>";
                }
            }
            $aDocument["text_shorten"] .= $sSubfix;
            // end shorten
            $aDocument['edit_link'] = $this->url()->makeUrl('document.add', array('id_' . $aDocument['document_id']));
        }
        
        if ($aDocument === false)
        {
            return Phpfox_Error::display(_p('the_document_you_are_looking_for_can_not_be_found'));
        }
        if ($aDocument['is_approved'] != 1 && !Phpfox::getUserParam('document.can_approve_documents') && $aDocument['user_id'] != Phpfox::getUserId())
        {
            return Phpfox_Error::display(_p('this_document_is_pending_for_approved'));
        }
        if (Phpfox::isModule('track') && Phpfox::isUser() && Phpfox::getUserId() != $aDocument['user_id'] && !$aDocument['document_is_viewed'])
        {
            Phpfox::getService('track.process')->add('document', $aDocument['document_id']);
        }

        if (isset($aDocument['module_id']) && Phpfox::isModule($aDocument['module_id']) && Phpfox::hasCallback($aDocument['module_id'],
                'checkPermission')) {
            if (!Phpfox::callback($aDocument['module_id'] . '.checkPermission', $aDocument['item_id'],
                'document.view_browse_documents')) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
        }

        Phpfox::getService('privacy')->check('document', $aDocument['document_id'], $aDocument['user_id'], $aDocument['privacy'], $aDocument['is_friend']);

        $aDocument['total_view'] = Phpfox::getService('document.process')->numberAbbreviation($aDocument['total_view']);
        $aDocument['total_comment'] = Phpfox::getService('document.process')->numberAbbreviation($aDocument['total_comment']);
        $aDocument['total_like'] = Phpfox::getService('document.process')->numberAbbreviation($aDocument['total_like']);
        $this->setParam('aDocument', $aDocument);
		
        $this->setParam('sGroup', ($this->request()->get('req1') == 'group') ? $this->request()->get('req2') : '');
        $this->setParam('aRatingCallback', array(
                'type' => 'document',
                'total_rating' => _p('total_rating_ratings', array('total_rating' => $aDocument['total_rating'])),//$aVideo['total_rating'] . ' Ratings',
                'default_rating' => $aDocument['total_score'],
                'item_id' => $aDocument['document_id'],
                'stars' => array(
                    '2' => _p('poor'),
                    '4' => _p('nothing_special'),
                    '6' => _p('worth_watching'),
                    '8' => _p('pretty_cool'),
                    '10' => _p('awesome')
                )
            )
        ); 
        $this->setParam('aFeed', array(                
                'comment_type_id' => 'document',
                'privacy' => $aDocument['privacy'],
                'comment_privacy' => $aDocument['privacy_comment'],
                'like_type_id' => 'document',
                'feed_is_liked' => $aDocument['is_liked'],
                'feed_is_friend' => $aDocument['is_friend'],
                'item_id' => $aDocument['document_id'],
                'user_id' => $aDocument['user_id'],
                'total_comment' => $aDocument['total_comment'],
                'total_like' => $aDocument['total_like'],
                'feed_link' => Phpfox::permalink('document', $aDocument['document_id'], $aDocument['title']),
                'feed_title' => $aDocument['title'],
                'feed_display' => 'view',
                'feed_total_like' => $aDocument['total_like'],
                'report_module' => 'document',
                'report_phrase' => _p('report_this_document'),
                'time_stamp' => $aDocument['time_stamp']
            )
        );

        //display for pages
        if ($aDocument['module_id'] != 'document' && Phpfox::isModule($aDocument['module_id'])) {
            if ($aCallback = Phpfox::callback('document.getDocumentDetails', $aDocument)) {
                $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);

                if ($aDocument['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aCallback['item_id'],
                        'document.view_browse_documents')) {
                    return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                }
            }
        }
                
        $this->template()->setTitle($aDocument['title'])
                         ->setTitle(_p('documents'))
                         ->setBreadcrumb(_p('documents'),($aCallback === false ? $this->url()->makeUrl('document') : $aCallback['url_home_photo']))
                         ->setBreadcrumb($aDocument['title'], $this->url()->permalink('document', $aDocument['document_id'], $aDocument['title']), true)
                         ->setPhrase(array(
                                    'rate.thanks_for_rating'            
                                )
                            )
                         ->setHeader(array(
                                    'scribd_api.js' => 'module_document',
                                    'document.js' => 'module_document',
                                    'jquery.rating.css' => 'style_css',
                                    'jquery/plugin/star/jquery.rating.js' => 'static_script',
                                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                                    'rate.js' => 'module_rate',
                                    'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                                    'quick_edit.js' => 'static_script',
                                    'pager.css' => 'style_css',
                                    'feed.js' => 'module_feed',
                                    'switch_legend.js' => 'static_script',
                                    'switch_menu.js' => 'static_script',
                                    '<script type="text/javascript">
                                       var document_height = ' . PHpfox::getParam('document.document_height', 800) .';
                                       var document_width = ' . Phpfox::getParam('document.document_width', 600) . '; 
                                     </script>',
                                    ))
                         ->setEditor(array(
                            'load' => 'simple'
                                )
                            )
                         ->assign([
                                'current_url' =>  $this->url()->permalink('document', $aDocument['document_id'], ""),
                                 'aDocument' => $aDocument,
                                 'document_height' => Phpfox::getParam('document.document_height', 800),
                                 'document_width' => Phpfox::getParam('document.document_width', 600),
                                 'download_icon_url' => Phpfox::getParam('core.path_file') . 'module/document/static/image/download.png']);

        if (Phpfox::isModule('rate'))
        {
            if ($aDocument['allow_rating'])
            {
                 $this->template()->setHeader(array(
                     '<script type="text/javascript">$Behavior.rateDocument = function() { $Core.rate.init({module: \'document\', display: ' . ($aDocument['has_rated'] ? 'false' : ($aDocument['user_id'] == Phpfox::getUserId() ? 'false' : 'true')) . ', error_message: \'' . ($aDocument['has_rated'] ? _p('you_have_already_voted', array('phpfox_squote' => true)) : _p('you_cannot_rate_your_own_document', array('phpfox_squote' => true))) . '\'}); }</script>',
                      )
                    );               
            }else
            {
                 $this->template()->setHeader(array(  
                        '<script type="text/javascript">$Behavior.rateDocument = function() { $Core.rate.init({module: \'document\', display: ' . ($aDocument['has_rated'] ? 'false' : ($aDocument['user_id'] == Phpfox::getUserId() ? 'false' : 'true')) . ', error_message: \'' .  _p('could_not_rate_on_this_document', array('phpfox_squote' => true)) . '\'}); }</script>',
                        )
                    );               
            }
           
        }
        $aFilterMenu = array();
        $iMyDocumentTotal = Phpfox::getService('document')->getMyDocumentsTotal();
        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW'))
        {
            $aFilterMenu = array(_p('all_documents_link_title') => '', _p('my_documents_link_title').'<span class="my count-item">' . ($iMyDocumentTotal > 99 ? '99+' : $iMyDocumentTotal) . '</span>' => 'my');

            if (!Phpfox::getParam('core.friends_only_community'))
            {
                $aFilterMenu[_p('friend_documents')] = 'friend';
            }

            if (Phpfox::getUserParam('document.can_approve_documents'))
            {
                $iPendingTotal = Phpfox::getService('document')->getPendingTotal();

                if ($iPendingTotal)
                {
                    $aFilterMenu[_p('pending') .
                    (Phpfox::getUserParam('document.can_approve_documents') ? ' <span class="pending count-item"> ' . $iPendingTotal . ' </span>' : 0)] = 'pending';
                }
            }

            if ($aDocument['is_approved'] != 1) {
                $aTitleLabel['label']['pending'] = [
                    'title' => '',
                    'title_class' => 'flag-style-arrow',
                    'icon_class' => 'clock-o'

                ];
                $aPendingItem = [
                    'message' => _p('document_is_pending_approval'),
                    'actions' => []
                ];
                if (Phpfox::getUserParam('document.can_approve_documents')) {
                    $aPendingItem['actions']['approve'] = [
                        'is_ajax' => true,
                        'label' => _p('approve'),
                        'action' => '$.ajaxCall(\'document.approve\', \'inline=true&amp;document_id='.$aDocument['document_id'].'\', \'POST\')'
                    ];
                }
                if ((Phpfox::getUserParam('document.can_edit_own_document') && $aDocument['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('document.can_edit_other_document')) {
                    $aPendingItem['actions']['edit'] = [
                        'label' => _p('edit'),
                        'action' => $this->url()->makeUrl('document.add',['id' => $aDocument['document_id']]),
                    ];
                }
                if ((Phpfox::getUserParam('document.can_delete_own_document') && $aDocument['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('document.can_delete_other_document')) {
                    $aPendingItem['actions']['delete'] = [
                        'is_ajax' => true,
                        'is_confirm' => true,
                        'confirm_message' => _p('are_you_sure_you_want_to_delete_this_document_permanently'),
                        'label' => _p('delete'),
                        'action' => '$Core.jsConfirm({message: \''._p('are_you_sure_you_want_to_delete_this_document_permanently').'\'}, function(){window.location.href = \''.$this->url()->makeUrl('document.delete',['id' => $aDocument['document_id']]).'\';}, function(){}); return false;'
                    ];
                }

                $this->template()->assign([
                    'aPendingItem' => $aPendingItem
                ]);
            }
        }
        Phpfox_Error::reset();
        $this->template()->buildSectionMenu('document', $aFilterMenu);
        $this->template()->assign('bNotShowActionButton', true);
        (($sPlugin = Phpfox_Plugin::get('document.component_controller_view_process_end')) ? eval($sPlugin) : false);
    }
}
?>
