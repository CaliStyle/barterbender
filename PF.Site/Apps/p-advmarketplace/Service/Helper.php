<?php

namespace Apps\P_AdvMarketplace\Service;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;

class Helper extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('advancedmarketplace');
    }

    public function buildActivityPointPaymentMethod($gatewayData)
    {
        $gateway = [];
        if (Phpfox::isAppActive('Core_Activity_Points') && Phpfox::getUserParam('activitypoint.can_purchase_with_activity_points')) {
            $totalPoints = (int)$this->database()
                ->select('activity_points')
                ->from(Phpfox::getT('user_activity'))
                ->where('user_id = ' . (int)Phpfox::getUserId())
                ->execute('getSlaveField');

            $sCurreny = $gatewayData['currency_code'];
            $pointConversionSetting = Phpfox::getParam('activitypoint.activity_points_conversion_rate');
            if (isset($pointConversionSetting[$sCurreny]) && is_numeric($pointConversionSetting[$sCurreny])) {
                // Avoid division by zero
                $conversion = ($pointConversionSetting[$sCurreny] != 0 ? ($gatewayData['amount'] / $pointConversionSetting[$sCurreny]) : 0);
                if ($totalPoints >= $conversion) {
                    if (isset($gatewayData['setting']) && is_array($gatewayData['setting'])) {
                        $params = serialize($gatewayData['setting']);
                        unset($gatewayData['setting']);
                    }

                    $gateway = [
                        'yourpoints' => $totalPoints,
                        'yourcost' => $conversion,
                        'gateway_id' => 'activitypoints',
                        'title' => _p('activity_points'),
                        'description' => _p('you_can_purchase_this_with_your_activity_points'),
                        'is_active' => '1',
                        'form' => [
                            'url' => '#',
                            'param' => $gatewayData
                        ]
                    ];
                    if (isset($params) && !empty($params)) {
                        $gateway['setting'] = $params;
                    }
                }
            }
        }
        return $gateway;
    }

    public function _isTagSearching()
    {
        $isSearching = false;
        $searchText = '';
        $isUserProfile = defined('PHPFOX_IS_AJAX_CONTROLLER') || defined('PHPFOX_IS_USER_PROFILE') ? true : false;
        $isParentModuleView = defined('PHPFOX_IS_PAGES_VIEW') ? true : false;
        $request = $this->request();
        if ($isParentModuleView) {
            if ($request->get('req4') == 'tag') {
                $searchText = $request->get('req5');
            } elseif ($request->get('req4') == 'search' && $request->get('req5') == 'tag') {
                $searchText = $request->get('req6');
            }
            $isSearching = true;
        } elseif ($isUserProfile) {
            if ($request->get('req3') == 'tag') {
                $searchText = $request->get('req4');
            } elseif (($request->get('req3') == 'search' && $request->get('req4') == 'tag')) {
                $searchText = $request->get('req5');
            }
            $isSearching = true;
        } else {
            if ($request->get('req2') == 'tag') {
                $searchText = $request->get('req3');
            } elseif ($request->get('req2') == 'search' && $request->get('req3') == 'tag') {
                $searchText = $request->get('req4');
            } else {
                $searchText = $request->get('tag');
            }
            $isSearching = true;
        }
        return [$isSearching, $searchText];
    }

    public function display($aParam = array())
    {
        /*
            'server_id' => $aRow['server_id'],
            'source' => 'xxx',
            'max_width' => 120,
            'max_height' => 120
        */
        return sprintf("<img server_id=\"%s\" src=\"%s\" max-width=\"%d\" max-height=\"%d\" />",
            $aParam["server_id"],
            $aParam["source"],
            $aParam["max_width"],
            $aParam["max_height"]
        );
    }

    public function getFooterLink($dataSource, $listing)
    {
        $url = Phpfox::getLib('url');
        switch ($dataSource) {
            case 'featured':
                $footerLink = array(
                    _p('view_more') => $url->makeUrl('advancedmarketplace', array('view' => 'all', 'sort' => 'featured'))
                );
                break;
            case 'sponsored':
                $footerLink = array(
                    _p('encourage_sponsor_listing') => $url->makeUrl('advancedmarketplace',
                        array('view' => 'my', 'sponsor' => 'help'))
                );
                break;
            case 'today':
                $footerLink = array(
                    _p('view_more') => $url->makeUrl('advancedmarketplace',
                        array('view' => 'all', 'when' => 'today'))
                );
                break;
            case 'most_viewed':
                $footerLink = array(
                    _p('view_more') => $url->makeUrl('advancedmarketplace', array('sort' => 'most-viewed'))
                );
                break;
            case 'most_liked':
                $footerLink = array(
                    _p('view_more') => $url->makeUrl('advancedmarketplace', array('sort' => 'most-liked'))
                );
                break;
            case 'most_commented':
                $footerLink = array(
                    _p('view_more') => $url->makeUrl('advancedmarketplace', array('sort' => 'most-talked'))
                );
                break;
            case 'most_reviewed':
                $footerLink = array(
                    _p('view_more') => $url->makeUrl('advancedmarketplace', array('sort' => 'most-reviewed'))
                );
                break;
            case 'latest':
                $footerLink = array(
                    _p('view_more') => $url->makeUrl('advancedmarketplace', array('sort' => 'latest'))
                );
                break;
            case 'recent_viewed':
                $footerLink = array(
                    _p('view_more') => $url->makeUrl('advancedmarketplace', array('sort' => 'recent-viewed'))
                );
                break;
            case 'interested':
                $footerLink = array();
                break;
            case 'same_tag':
                $footerLink = array();
                break;
            case 'more_from_seller':
                $aUser = $this->database()->select('user_name')
                    ->from(Phpfox::getT('user'))
                    ->where('user_id = ' . (int)$listing['user_id'])
                    ->execute('getSlaveRow');

                $footerLink = array(
                    _p('view_more') => $url->makeUrl($aUser['user_name']) . '/advancedmarketplace'
                );
                break;
        }

        return $footerLink;
    }

    public function bIsSideLocation($location = 2)
    {
        return in_array($location, array(1, 9, 3, 10));
    }

    public function isContactingSeller()
    {
        $aVals = request()->getArray('val');
        $module_id = request()->get('module_id');
        $listing_id = request()->get('listing_id');

        return (($module_id == 'advancedmarketplace') && $listing_id) || (!empty($aVals['advancedmarketplace_contacting_seller']));
    }

    public function getPaymentGateways()
    {
        $gateways = Phpfox::getService('api.gateway')->getActive();
        if (Phpfox::isAppActive('Core_Activity_Points') && Phpfox::getUserParam('activitypoint.can_purchase_with_activity_points')) {
            $aPointsGateway = [
                'gateway_id' => 'activitypoints',
                'title' => _p('activity_points'),
                'description' => _p('you_can_purchase_this_with_your_activity_points'),
                'is_active' => '1',
            ];
            $gateways[] = $aPointsGateway;
        }

        return $gateways;
    }


    /**
     * Must is owner or have permission on all listing
     * @return bool
     */
    public function canEdit($aListing)
    {
        if ($aListing['listing_id'] && (Phpfox::getUserParam('advancedmarketplace.can_edit_other_listing') || Phpfox::getUserParam('advancedmarketplace.can_edit_own_listing') && Phpfox::getUserId() == $aListing['user_id'])) {
            return true;
        }

        return false;
    }
}
