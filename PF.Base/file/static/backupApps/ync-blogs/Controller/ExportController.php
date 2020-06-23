<?php

namespace Apps\YNC_Blogs\Controller;
defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class ExportController extends Phpfox_Component
{
    public function process()
    {
        $aIds = $this->getParam('aIds', $this->request()->getArray('aIds'));
        array_push($aIds, 0);
        $sCond = 'AND blog.user_id = ' . Phpfox::getUserId() . ' AND blog.blog_id IN (' . implode(',', $aIds) . ')';
        $aBlogs = Phpfox::getService('ynblog.blog')->getManageBlog(array($sCond));

        $sExportTo = $this->getParam('sType', $this->request()->get('sType'));

        $sFileName = "";
        switch ($sExportTo) {
            case 'export_wordpress':
                $sFileName = 'wordpress.' . date('Y-m-d', time()) . '.xml';
                break;
            case 'export_tumblr':
                $sFileName = 'tumblr.' . date('Y-m-d', time()) . '.xml';
                break;
            case 'export_blogger':
                $sFileName = 'blogger.' . date('Y-m-d', time()) . '.xml';
                break;
        }

        if ($sFileName == '')
            return false;

        $this->template()->assign(
            array(
                'sLink' => 'https://google.com.vn',
                'aItems' => $aBlogs,
                'sTemplate' => $sExportTo,
                'aUser' => Phpfox::getService('user')->getUserFields(true),
            )
        );

        Phpfox::getLib('module')->getControllerTemplate();

        header("Content-Disposition: attachment; filename=" . urlencode(basename($sFileName)), true);
        header("Content-Transfer-Encoding: Binary", true);
        header("Content-Type: application/force-download", true);
        header("Content-Type: application/octet-stream", true);
        header("Content-Type: application/download", true);
        header("Content-Description: File Transfer", true);
        die;
    }
}
