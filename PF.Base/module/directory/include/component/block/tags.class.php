<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Tags extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() 
    {
        $sType = 'directory';
        $aTags = Phpfox::getService('directory')->getTagCloud(10);    
        if(!count($aTags) || !is_array($aTags)){
            return false;
        }
        $aTagsFont = array();
        if(count($aTags) > 0 && is_array($aTags)){
            foreach ($aTags as $aTag) {
                 $aTagsFont[$aTag['key']] = $aTag['value'];
            }
            
            $maxValue = max($aTagsFont);
            $minValue = min($aTagsFont);
            $step = ($maxValue - $minValue) / 4;        
            
            foreach ($aTags as $key =>$aTag) {
                if($aTag['value'] <= $minValue){
                        $aTags[$key]['font'] = 14;
                }
                else
                if($aTag['value'] <= ($minValue + $step) && $aTag['value'] > $minValue){
                        $aTags[$key]['font'] = 16;
                }
                else
                if($aTag['value'] <= ($minValue + $step * 3) && $aTag['value'] > ($minValue + $step * 2) ){
                        $aTags[$key]['font'] = 18;
                }
                else
                if($aTag['value'] < ($maxValue) && $aTag['value'] > ($minValue + $step * 3) ){
                        $aTags[$key]['font'] = 20;
                }
                else
                if($aTag['value'] >= ($maxValue)){
                        $aTags[$key]['font'] = 22;
                }
                else{
                    $aTags[$key]['font'] = 14;
                }
            }

            shuffle($aTags);
        }
        else{
            $aTags = array();
        }

        $this->template()->assign(array(
                'aTags' => $aTags,
                'sHeader' => _p('directory.tags'),
                'sCustomClassName' => 'ync-block'
            )
        );
        return 'block';
    }

}

?>