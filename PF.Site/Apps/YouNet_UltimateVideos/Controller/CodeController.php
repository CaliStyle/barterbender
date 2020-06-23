<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/24/16
 * Time: 3:33 PM
 */

namespace Apps\YouNet_UltimateVideos\Controller;


class CodeController extends \Phpfox_Component
{

    public function process()
    {
        $id = $this->request()->get('req3');
        $this->template()->setBreadCrumb(_p('html_code'), '')
            ->setTitle(_p('html_code'));


        $this->template()->assign([
            'sUrl' => \Phpfox::permalink('ultimatevideo.embed', $id, ''),
        ]);

    }
}