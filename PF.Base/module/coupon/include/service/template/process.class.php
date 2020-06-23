<?php

defined('PHPFOX') or exit('NO DICE!');

class Coupon_Service_Template_Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('coupon_print_template');
    }
    
    public function add($aVals)
    {
        if (($sMsg = $this->verify($aVals)) !== true)
        {
            return Phpfox_Error::set($sMsg);
        }
        
        $aParams = $aVals;
        unset($aParams['name']);
        
        $aSql = array(
            'user_id' => Phpfox::getUserId(),
            'name' => $aVals['name'],
            'params' => serialize($aParams),
            'is_active' => 1 //use in later version
        );
        
        $iId = $this->database()->insert($this->_sTable, $aSql);
        
        return $iId;
    }
    
    public function update($iId, $aVals)
    {
        if (($sMsg = $this->verify($aVals)) !== true)
        {
            return Phpfox_Error::set($sMsg);
        }
        
        $aParams = $aVals;
        unset($aParams['name']);
        
        $aSql = array(
            'user_id' => Phpfox::getUserId(),
            'name' => $aVals['name'],
            'params' => serialize($aParams),
            'is_active' => 1 //use in later version
        );
        
        $this->database()->update($this->_sTable, $aSql, 'template_id = '.(int)$iId);
        
        return true;
    }
    
    public function delete($iId)
    {
        $this->database()->delete($this->_sTable, 'template_id = '.(int)$iId);
        
        return true;
    }

    public function verify($aVals)
    {
        if (empty($aVals['name']))
        {
            return _p('template_name_is_required');
        }
        
        $aPosition = array();
        $aOrder = array();
        
        foreach ($aVals as $k => $aVal)
        {
            if ($k!='name' && $k!='coupon_photo' && $k!='other_info')
            {
                if (empty($aVal['size']))
                {
                    return _p('font_size_is_required');
                }
                if (!is_numeric($aVal['size']) || $aVal['size']<1 || $aVal['size']!=round($aVal['size']))
                {
                    return _p('invalid_font_size');
                }
                if (!preg_match('/^#[a-f0-9]{6}$/i', $aVal['color']))
                {
                    return _p('invalid_color_code');
                }
            }
            
            if (is_array($aVal) && isset($aVal['position']))
            {
                $aPosition[] = $aVal['position'];
            }
            
            if (is_array($aVal) && isset($aVal['order']))
            {
                $aOrder[] = $aVal['order'];
            }
        }
        
        if ($this->_isDuplicate($aPosition))
        {
            return _p('block_position_can_not_duplicate');
        }
        
        if ($this->_isDuplicate($aOrder))
        {
            return _p('order_in_group_can_not_duplicate');
        }
        
        return true;
    }
    
    private function _isDuplicate($arr)
    {
        $iCnt = count($arr);
        for($i=0; $i<$iCnt; $i++)
        {
            for($j=$i+1; $j<$iCnt; $j++)
            {
                if ($arr[$i]!=0 && $arr[$j]!=0 && $arr[$j]==$arr[$i])
                {
                    return true;
                }
            }
        }
        
        return false;
    }

    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('coupon.service_template_process__call'))
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