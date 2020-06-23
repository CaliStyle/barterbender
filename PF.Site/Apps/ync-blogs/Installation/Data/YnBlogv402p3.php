<?php
namespace Apps\YNC_Blogs\Installation\Data;

use Phpfox;

class YnBlogv402p3
{
    public function process()
    {
        $this->_removeUnusedSettings();
        $this->_updateFeaturedTitleBlock();
    }

    private function _removeUnusedSettings()
    {
        $settingTable = Phpfox::getT('setting');

        $unusedSettings = [
            'yn_advblog_default_viewmode'
        ];

        db()->delete($settingTable, 'var_name IN ("' . implode('","', $unusedSettings) . '") AND module_id = "ynblog"');
    }

    private function _updateFeaturedTitleBlock()
    {
        $blockTable = Phpfox::getT('block');
        $check = db()->select('block_id')
                    ->from($blockTable)
                    ->where('m_connection = \'ynblog.index\' AND component = \'blog_list\' AND params LIKE \'%"data_source":"featured"%\' AND title =\'\'')
                    ->execute('getSlaveField');
        if($check) {
            db()->update($blockTable, ['title' => 'Featured Blogs'], 'block_id = ' . (int)$check);
        }
    }
}