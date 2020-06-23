<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Coupon_Component_Controller_List extends Phpfox_Component
{

        /**
         * Class process method which is used to execute this component.
         */
        public function process()
        {
                $iCouponId = $this->request()->getInt('req3');
                $sType = $this->request()->get('sType');

                $iPage = $this->request()->getInt('page');
                $iLimit = 5;
                $iTotal = 0;
                $aFilters = array(
                        'keyword_claimer' => array(
                                'type' => 'input:text',
                                'search' => ' AND (u.full_name LIKE "%[VALUE]%") ',
                                'size' => 45,
                        ),
                        'keyword_couponcode' => array(
                                'type' => 'input:text',
                                'search' => ' AND (cpc.code LIKE "%[VALUE]%") ',
                                'size' => 45,
                        )
                );

                $oSearch = Phpfox::getLib('search')->set(array(
                        'type' => 'coupon',
                        'filters' => $aFilters,
                        'search' => 'search'
                        )
                );

                $oSearch->setCondition('AND cpc.coupon_id = ' . $iCouponId);

                $sKeywordClaimer = $oSearch->get('keyword_claimer');
                $sKeywordCouponCode = $oSearch->get('keyword_couponcode');
                
                $formatDatePicker = str_split(Phpfox::getParam('core.date_field_order'));

                $aFormatIntial = array();
                foreach ($formatDatePicker as $key => $value) {
               
                    if($formatDatePicker[$key] != 'Y'){
                        $formatIntial = strtolower($formatDatePicker[$key]);
                    }
                    else{
                        $formatIntial = $formatDatePicker[$key];
                    }
               
                    $aFormatIntial[] = $formatIntial;

                    $formatDatePicker[$key] .= $formatDatePicker[$key];
                    $formatDatePicker[$key] = strtolower($formatDatePicker[$key]);                
                }

                $sFromDate = strtotime(str_replace('/', '-', $oSearch->get('fromdate')));
                $sToDate = strtotime(str_replace('/', '-', $oSearch->get('todate')));

                $sSearchFromDate  = $oSearch->get('fromdate');
                $aSearchFromDate = explode("/", $sSearchFromDate);
                
                $sSearchToDate  = $oSearch->get('todate');
                $aSearchToDate = explode("/", $sSearchToDate);

                $aFromDate = array();
                $aToDate = array();
				
                if(!empty($aFormatIntial)){
                    foreach ($aFormatIntial as $key => $aItem) {
                        $aFromDate[$aItem] = (!empty($aSearchFromDate[$key])) ? $aSearchFromDate[$key] : date($aItem);
                        $aToDate[$aItem] = (!empty($aSearchToDate[$key])) ? $aSearchToDate[$key] : date($aItem);
                    }
                }

                $sFormatDatePicker = implode("/", $formatDatePicker);
                $sFormatIntial = implode("/", $aFormatIntial);


                
                $sSubmit = $oSearch->get('submit');
                if($sSubmit == _p("reset"))
                {
                    $this->url()->send('coupon.list.'.$iCouponId,array());
                }
                
                if (count($aFromDate) && count($aToDate))
                {
                        $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $aFromDate['m'], $aFromDate['d'], $aFromDate['Y']);
                        $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aToDate['m'], $aToDate['d'], $aToDate['Y']);
                        if ($iStartTime > $iEndTime)
                        {
                                $iTemp = $iStartTime;
                                $iStartTime = $iEndTime;
                                $iEndTime = $iTemp;
                        }

                        $oSearch->setCondition('AND cpc.time_stamp > ' . $iStartTime . ' AND cpc.time_stamp < ' . $iEndTime);
                }

                $aCoupon = Phpfox::getService('coupon')->getCouponById($iCouponId);

                list($iTotal, $aTransactions) = Phpfox::getService('coupon')->getClaimByCouponId($oSearch->getConditions(), $iPage, $iLimit);

                if ($aCoupon['module_id'] != 'coupon')
                {
                    switch ($aCoupon['module_id']) {
                         case 'pages':
                             $aCallback = Phpfox::callback('coupon.getCouponsDetails', array('item_id' => $aCoupon['item_id']));                    
                             break;
                         case 'groups':
                             $aCallback = Phpfox::callback('coupon.getCouponsGroupDetails', array('item_id' => $aCoupon['item_id']));
                             break;

                         default:
                             $aCallback = Phpfox::callback($aCoupon['module_id'] . '.getCouponsDetails', array('item_id' => $aCoupon['item_id']));                     
                             break;
                    }
                    $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                    $this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
                }

                $sFromDate = $iStartTime ? Phpfox::getTime($sFormatIntial, $iStartTime, false) : Phpfox::getTime($sFormatIntial,PHPFOX_TIME,false);

                $sToDate = $iEndTime ? Phpfox::getTime($sFormatIntial, $iEndTime, false) : Phpfox::getTime($sFormatIntial,PHPFOX_TIME,false);

                Phpfox::getLib('pager')->set(array(
                    'page' => $iPage, 
                    'size' => $iLimit, 
                    'count' => $iTotal
                ));

                if ($aTransactions && isset($aTransactions) && count($aTransactions))
                {
                        $sUrl = 'coupon.list.' . $this->request()->getInt('req4');
                } else
                {
                        $sUrl = 'current';
                }
                
                // Claims Remain
                $sRemain = _p("unlimited");
                if($aCoupon['quantity'] > 0)
                {
                    $sRemain = $aCoupon['quantity'] - $aCoupon['total_claim'];
                    if($sRemain < 0)
                    {
                        $sRemain = 0;
                    }   
                }

                $this -> template()
                      -> setBreadCrumb(_p('coupon'), $aCoupon['module_id'] == 'coupon' ? $this->url()->makeUrl('coupon') : $this->url()->permalink('pages', $aCoupon['item_id'], 'coupon') )
                      -> setBreadCrumb($aCoupon['title'], $this->url()->permalink('coupon.detail', $aCoupon['coupon_id'], $aCoupon['title']))
                      -> setBreadCrumb(_p('coupon_statistics'), $this->url()->permalink('coupon.list', $aCoupon['coupon_id']), TRUE);
                        
                $this -> template()->setMeta('description', $aCoupon['title'] . '.')
                      -> setMeta('keywords', $this->template()->getKeywords($aCoupon['title']));
                        
                $this -> template()->setHeader('cache', array(
                                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                                'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                                'pager.css' => 'style_css',
                                'feed.js' => 'module_feed',
                        ));
                        $this->template() ->assign(array(
                                'aTransactions' => $aTransactions,
                                'aCoupon'       => $aCoupon,
                                'sType'         => $sType,
                                'sFromDate'     => $sFromDate,
                                'sToDate'       => $sToDate,
                                'sRemain'       => $sRemain,
                                'sFormatDatePicker' => $sFormatDatePicker,
                                'iPage' => $iPage
                        ));


                $this->template()->setHeader(
                        array(
                                'global.css' => 'module_coupon'
                        )
                );
                
        }

        /**
         * Garbage collector. Is executed after this class has completed
         * its job and the template has also been displayed.
         */
        public function clean()
        {
                (($sPlugin = Phpfox_Plugin::get('coupon.component_controller_list_clean')) ? eval($sPlugin) : false);
        }

}