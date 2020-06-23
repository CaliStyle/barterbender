<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Business_You_May_Like extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $aBusinessYouMayLike = Phpfox::getService('directory')->getBusinessYouMayLike($iLimit);

        if (!count($aBusinessYouMayLike)) {
            return false;
        }

        foreach ($aBusinessYouMayLike as $iKey => $aItem) {
            if(empty($aBusiness['logo_path'])){
                $aBusinessYouMayLike[$iKey]['default_logo_path'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aBusiness['server_id'],
                    'path' => '',
                    'file' => Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png',
                    'suffix' => '_200_square',
                    'return_url' => true
                ));
            }
        }

        $this->template()->assign(array(
                'aBusinessYouMayLike' => $aBusinessYouMayLike,
                'sHeader' => _p('directory.business_you_may_like'),
                'sCustomClassName' => 'ync-block'
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
                'info' => _p('You May Like Businesses Limit'),
                'description' => _p('Define the limit of how many you may like businesses can be displayed when viewing the directory section. Set 0 will hide this block.'),
                'value' => 3,
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
                'title' => '"You May Like Businesses Limit" must be greater than or equal to 0'
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
                'aBusinessYouMayLike',
                'sHeader',
                'limit',
            )
        );

        (($sPlugin = Phpfox_Plugin::get('directory.component_block_you_may_like_clean')) ? eval($sPlugin) : false);
    }

}
