<?php 
;

if (Phpfox::isModule('directory') && $aPoll['module_id'] == 'directory' && (int)$aPoll['item_id'] > 0)
{
    if ($aCallback = Phpfox::callback($aPoll['module_id'] . '.getPollDetails', $aPoll)){
        $this->template()->clearBreadCrumb();
        $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
        $this->template()->setBreadcrumb(Phpfox::getLib('parse.output')->shorten($aCallback['title'],50, '...'), $aCallback['url_home']);
        $this->template()
            ->setBreadCrumb(_p('poll.polls'), $aCallback['url_home_photo'])
            ->setBreadcrumb($aPoll['question'], $this->url()->permalink('poll', $aPoll['poll_id'], $aPoll['question']), true)
            ;
    }
}

;
?>