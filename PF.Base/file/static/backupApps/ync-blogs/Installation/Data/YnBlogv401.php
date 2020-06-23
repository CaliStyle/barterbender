<?php

namespace Apps\YNC_Blogs\Installation\Data;

use Phpfox;

class YnBlogv401
{
    private $_aDefaultCategories;

    public function __construct()
    {
        $this->_aDefaultCategories = [
            'Business',
            'Education',
            'Entertainment',
            'Family & Home',
            'Health',
            'Recreation',
            'Shopping',
            'Society',
            'Sports',
            'Technology',
        ];
    }

    private function database()
    {
        return Phpfox::getLib('phpfox.database');
    }

    public function process()
    {
        $aRewrite = $this->database()
            ->select('*')
            ->from(Phpfox::getT('rewrite'))
            ->where('url = \'ynblog\' AND replacement = \'advanced-blog\'')
            ->execute('getSlaveRow');

        if (empty($aRewrite)) {
            $this->database()->insert(Phpfox::getT('rewrite'), [
                'url' => 'ynblog',
                'replacement' => 'advanced-blog',
            ]);
        }

        if (!$this->database()->isField(Phpfox::getT('user_activity'), 'activity_ynblog')) {
            $this->database()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD  `activity_ynblog` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
        }
        if (!$this->database()->isField(Phpfox::getT('user_field'), 'total_ynblog')) {
            $this->database()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD  `total_ynblog` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'");
        }

        $iTotalCategory = $this->database()
            ->select('COUNT(category_id)')
            ->from(Phpfox::getT('ynblog_category'))
            ->execute('getField');

        if ($iTotalCategory == 0) {
            foreach ($this->_aDefaultCategories as $sName) {
                $this->database()->insert(Phpfox::getT('ynblog_category'), array(
                        'parent_id' => 0,
                        'name' => $sName,
                        'time_stamp' => PHPFOX_TIME,
                        'used' => 0,
                        'is_active' => 1,
                        'ordering' => 0
                    )
                );
            }
        }
    }
}
