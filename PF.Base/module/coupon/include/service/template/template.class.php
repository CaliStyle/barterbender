<?php

defined('PHPFOX') or exit('NO DICE!');

class Coupon_Service_Template_Template extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('coupon_print_template');
    }
    
    public function get($iId)
    {
        $aRow = $this->database()->select('t.*, '.Phpfox::getUserField())
        ->from($this->_sTable, 't')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = t.user_id')
        ->where('template_id = '.(int)$iId)
        ->execute('getSlaveRow');
        
        if (!empty($aRow))
        {
            $aParams = unserialize($aRow['params']);
            
            foreach($aParams as $sKey => $aParam)
            {
                $aRow[$sKey] = $aParam;
            }
        }
        
        return $aRow;
    }
    
    public function getForManage()
    {
        $aRows = $this->database()->select('t.*, '.Phpfox::getUserField())
        ->from($this->_sTable, 't')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = t.user_id')
        ->order('template_id ASC')
        ->execute('getSlaveRows');
        
        return $aRows;
    }
    
    public function convertToPosition($aParams)
    {
        $aPosition = array(1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null, 8 => null);
        
        foreach ($aParams as $sKey => $aParam)
        {
            if (!empty($aParam['position']))
            {
                if ($sKey=='other_info')
                {
                    foreach ($aParams as $sOther => $aOther)
                    {
                        if (!empty($aOther['order']))
                        {
                            $aPosition[$aParam['position']][$aOther['order']] = $sOther;
                        }
                    }
                    if (is_array($aPosition[$aParam['position']]))
                    {
                        ksort($aPosition[$aParam['position']]);
                    }
                }
                else
                {
                    $aPosition[$aParam['position']] = $sKey;
                }
            }
        }
        
        return $aPosition;
    }
    
    public function buildHtml($aParams, $aCoupon, $bPrint = false, $bAddCoupon = false)
    {
        $aCoupon['coupon_name'] = $aCoupon['title'];
        $aCoupon['coupon_code'] = $aCoupon['code'];
        $aCoupon['expired_date'] = Phpfox::getTime(Phpfox::getParam('coupon.coupon_view_time_stamp'), $aCoupon['expire_time'],false);
		$aCoupon['location'] = $aCoupon['location_venue'];
		if (!empty($aCoupon['city']))
		{
			$aCoupon['location'] .= ', '.$aCoupon['city'];
		}
		if (!empty($aCoupon['country_iso']))
		{
			$aCoupon['location'] .= ', '.Phpfox::getService('core.country')->getCountry($aCoupon['country_iso']);
		}
        if (!$bPrint)
        {
            $aCoupon['coupon_photo'] = '<img src="'.$aCoupon['image_url'].'" style="max-width: 100px; max-height: 100px;" />';
        }
        else
        {
            $aCoupon['coupon_photo'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aCoupon['server_id'],
				'path' => 'core.url_pic',
				'file' => $aCoupon['image_path'],
				'suffix' => '_400',
				'max_width' => 140,
				'max_height' => 140
            ));
        }
        
        $sHtml = '<div class="ync-style style-custom">';
        $sHtml .= '<div class="ync-custom-top">';
        $sHtml .= $this->buildHtmlPosition($aParams, $aCoupon, 1, $bPrint, $bAddCoupon);
        $sHtml .= '</div>';
        
        list($sLeftStyle, $sRightStyle) = $this->_getLRStyle($aParams['coupon_photo']['position']);
        
        $sHtml .= '<div class="ync-custom-left" style="'.$sLeftStyle.'">';
        $sHtml .= $this->buildHtmlPosition($aParams, $aCoupon, 2, $bPrint, $bAddCoupon);
        $sHtml .= $this->buildHtmlPosition($aParams, $aCoupon, 4, $bPrint, $bAddCoupon);
        $sHtml .= $this->buildHtmlPosition($aParams, $aCoupon, 6, $bPrint, $bAddCoupon);
    	$sHtml .= '</div>';
        
    	$sHtml .= '<div class="ync-custom-right" style="'.$sRightStyle.'">';
        $sHtml .= $this->buildHtmlPosition($aParams, $aCoupon, 3, $bPrint, $bAddCoupon);
        $sHtml .= $this->buildHtmlPosition($aParams, $aCoupon, 5, $bPrint, $bAddCoupon);
        $sHtml .= $this->buildHtmlPosition($aParams, $aCoupon, 7, $bPrint, $bAddCoupon);
    	$sHtml .= '</div>';
        
        $sHtml .= '<div class="ync-custom-bottom">';
        $sHtml .= $this->buildHtmlPosition($aParams, $aCoupon, 8, $bPrint, $bAddCoupon);
        $sHtml .= '</div>';
    	$sHtml .= '<div class="clear"></div>';
        $sHtml .= '</div>';
        
        return $sHtml;
    }
    
    private function _getLRStyle($photoPos)
    {
        $sLeftStyle = 'width: 50%';
        $sRightStyle = 'width: 50%';
        
        if ($photoPos==2 || $photoPos==4 || $photoPos==6)
        {
            $sLeftStyle = 'width: 35%;';
            $sRightStyle = 'width: 65%;';
        }
        
        if ($photoPos==3 || $photoPos==5 || $photoPos==7)
        {
            $sLeftStyle = 'width: 65%;';
            $sRightStyle = 'width: 35%;';
        }
        
        return array($sLeftStyle, $sRightStyle);
    }
    
    public function buildHtmlPosition($aParams, $aCoupon, $iPos, $bPrint = false, $bAddCoupon = false)
    {
        $aPosition = $this->convertToPosition($aParams);
        $sHtml = '';
        
        if (!empty($aPosition[$iPos]))
        {
            if (is_array($aPosition[$iPos]))
            {
                foreach ($aPosition[$iPos] as $iOther => $sOther)
                {
                    $sHtml .= $this->buildHtmlItem($aParams, $aCoupon, $sOther, $bPrint, $bAddCoupon);
                }
            }
            else
            {
                $sHtml .= $this->buildHtmlItem($aParams, $aCoupon, $aPosition[$iPos], $bPrint, $bAddCoupon);
            }
        }
        
        return $sHtml;
    }
    
    public function buildHtmlItem($aParams, $aCoupon, $sItem, $bPrint = false, $bAddCoupon = false)
    {
        if (empty($aCoupon[$sItem]))
        {
            return '';
        }
        
        if (!$bAddCoupon)
        {
            $sStyle = 'padding: 4px 0px;';
        }
        else
        {
            $sStyle = 'padding: 3px 0px;';
        }
        
        if ($sItem!='coupon_photo')
        {
            if ($sItem=='coupon_name' || $sItem=='coupon_code' || $sItem=='discount_value')
            {
                $sStyle .= ' font-weight: bold;';
            }
            if (!$bAddCoupon)
            {
                $sStyle .= ' font-size: '.$aParams[$sItem]['size'].'px;';
            }
            else
            {
                $sStyle .= ' font-size: '.(round($aParams[$sItem]['size']*0.75)).'px;';
            }
            $sStyle .= ' color: '.$aParams[$sItem]['color'].';';
        }
        
        switch($sItem)
        {
            case 'discount_value':
                if($aCoupon['discount_type'] == 'percentage')
            	{
                    $aCoupon['discount'] = $aCoupon['discount_value'] . $aCoupon['discount_symbol'];
            	}
            	else
            	{
                    $aCoupon['discount'] = $aCoupon['discount_symbol'] . $aCoupon['discount_value'];
            	}
                $sHtml = '<p style="'.$sStyle.'">';
                $sHtml .= '<span class="number">'.$aCoupon['discount'].'</span> <span class="text-off">'._p('off').'</span>';
                break;
            case 'expired_date':
                $sHtml = '<p style="'.$sStyle.'">';
                $sHtml .= 'Expired date: '.$aCoupon['expired_date'];
                break;
			case 'location':
                if ($bPrint && isset($aCoupon['print_option']) && $aCoupon['print_option']['location']=='0')
                {
                    $sStyle .= ' display: none;';
                }
				$sHtml = '<p class="print_option_location" style="'.$sStyle.'">';
                $sHtml .= 'Location: '.$aCoupon['location'];
				break;
            case 'category':
                if ($bPrint && isset($aCoupon['print_option']) && $aCoupon['print_option']['category']=='0')
                {
                    $sStyle .= ' display: none;';
                }
                $sHtml = '<p class="print_option_category" style="'.$sStyle.'">';
                $sHtml .= 'Category: '.$aCoupon['category'];
                break;
            case 'site_url':
                if ($bPrint && isset($aCoupon['print_option']) && $aCoupon['print_option']['site_url']=='0')
                {
                    $sStyle .= ' display: none;';
                }
                $sHtml = '<p class="print_option_site_url" style="'.$sStyle.'">';
                $sHtml .= $aCoupon[$sItem];
                break;
            case 'coupon_photo':
                if ($bPrint && isset($aCoupon['print_option']) && $aCoupon['print_option']['photo']=='0')
                {
                    $sStyle .= ' display: none;';
                }
                $sHtml = '<p class="ync-image print_option_photo" style="'.$sStyle.'">';
                $sHtml .= $aCoupon[$sItem];
                break;
            default:
                $sHtml = '<p style="'.$sStyle.'">';
                $sHtml .= $aCoupon[$sItem];
        }
        $sHtml .= '</p>';
        
        return $sHtml;
    }

    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('coupon.service_template_template__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method '.__class__.'::'.$sMethod.'()', E_USER_ERROR);
    }
}

?>