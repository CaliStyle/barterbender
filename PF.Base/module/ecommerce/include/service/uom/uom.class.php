<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Uom_Uom extends Phpfox_Service {
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ecommerce_uom');
    }

    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('*')->from($this->_sTable)->where('uom_id = ' . (int)$iId)->execute('getRow');

        if (!isset($aRow['uom_id']))
        {
            return false;
        }

        //Support legacy phrases
        if (substr($aRow['title'], 0, 7) == '{phrase' && substr($aRow['title'], -1) == '}') {
            $aRow['title'] = preg_replace('/\s+/', ' ', $aRow['title']);
            $aRow['title'] = str_replace([
                "{phrase var='",
                "{phrase var=\"",
                "'}",
                "\"}"
            ], "", $aRow['title']);
        }//End support legacy
        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage){
            $sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['title'])) ? _p($aRow['title'], [], $aLanguage['language_id']) : $aRow['title'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }

        return $aRow;
    }
    
    public function get()
    {
        $sCacheId = $this->cache()->set('ecommerce_uom_display_admin_' . Phpfox::getLib('locale')->getLangId());

        if (!($sOutput = $this->cache()->get($sCacheId)))
        {
            $aUoms = $this->database()->select('*')->from($this->_sTable)->order('ordering ASC')->execute('getRows');
            
            if (count($aUoms))
            {
                $aCache = array();

                $sOutput = '<ul>';

                foreach ($aUoms as $aUom)
                {
                    $aCache[] = $aUom['uom_id'];

                    $sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aUom['uom_id'] . ']" value="' . $aUom['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aUom['uom_id'] . '" class="js_drop_down">' . Phpfox::getLib('locale')->convert($aUom['title']) . '</a></li>' . "\n";
                }

                $sOutput .= '</ul>';
            }
            
            $this->cache()->save($sCacheId, $sOutput);
        }

        return $sOutput;
    }

    public function getAll()
    {
        $uoms = $this->database()->select('*')->from($this->_sTable)->order('ordering')->execute('getRows');
        $aUoms = array();
        foreach ($uoms as $uom) {
            $aUoms[] = array_merge($uom, array(
                'uom_id' => $uom['uom_id'],
                'title' => Phpfox::getLib('locale')->convert($uom['title']),

            ));
        }
        return $aUoms;
    }

}
