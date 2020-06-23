<?php
$sFullControllerName = Phpfox::getLib('template')->getVar('sFullControllerName');
if(isset($sFullControllerName) == true && strpos($sFullControllerName, "ynsocialstore") !== false)
{
    $aSearchTool = Phpfox::getLib('template')->getVar('aSearchTool');
    if(isset($aSearchTool)
        && is_array($aSearchTool)
        && isset($aSearchTool['filters'])
        && isset($aSearchTool['search'])
        && isset($aSearchTool['filters']['Sort'])
        && isset($aSearchTool['filters']['Sort']['data'])
    )
    {
        $sort = Phpfox::getLib('request')->get('sort');
        $when = Phpfox::getLib('request')->get('when');
        $show = Phpfox::getLib('request')->get('show');
        $keyword = Phpfox::getLib('request')->get('keywords');

        if ($sort) {
            $sort = str_replace('-', '_', $sort);
            $aSearchTool['filters']['Sort']['active_phrase'] = _p(''.$sort);
        }
        if ($show && isset($aSearchTool['filters']['Show'])) {
            $aSearchTool['filters']['Show']['active_phrase'] = _p('core.per_page', array('total' => $show));
        }
        if ($when && isset($aSearchTool['filters']['When'])) {
            $when = str_replace('-', '_', $when);
            $aSearchTool['filters']['When']['active_phrase'] = _p('core.'.$when);
        }
        if ($keyword != '') {
            $aSearchTool['search']['actual_value'] = $keyword;
        }
        Phpfox::getLib('template')->assign('aSearchTool', $aSearchTool);
    }
}