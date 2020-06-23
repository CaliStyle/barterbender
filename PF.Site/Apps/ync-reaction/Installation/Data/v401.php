<?php
namespace Apps\YNC_Reaction\Installation\Data;


use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class v401
 * @package Apps\YNC_Reaction\Installation\Data
 */
class v401
{
    private $_aDefaultReactions;
    private $_aDefaultColors;

    public function __construct()
    {
        $this->_aDefaultReactions = ['like__u', 'love__u', 'haha__u', 'wow__u', 'sad__u', 'angry__u'];
        $this->_aDefaultColors = ['009fe2', 'ff314c', 'ffc84d', 'ffc84d', 'ffc84d', 'e95921'];
    }

    public function process()
    {
        $iCnt = db()->select('COUNT(*)')
            ->from(':yncreaction_reactions')
            ->execute('getField');
        if (!$iCnt) {
            //Insert default reactions
            $i = 1;
            foreach ($this->_aDefaultReactions as $iKey => $sReaction) {
                //view_id | 0: reaction add by admin | 1: reaction default, can delete, de-active | 2: reaction default, cannot delete, de-active
                $aInsert = [
                    'id' => $i,
                    'title' => $sReaction,
                    'is_active' => 1,
                    'is_deleted' => 0,
                    'view_id' => 1,
                    'icon_path' => str_replace('__u', '', $sReaction) . '.svg',
                    'server_id' => 0,
                    'ordering' => $i,
                    'color' => $this->_aDefaultColors[$iKey]
                ];
                $i++;
                //Like is default, cannot delete, de-active
                if (preg_match('/like/', $sReaction)) {
                    $aInsert['view_id'] = 2;
                }
                db()->insert(':yncreaction_reactions', $aInsert);
            }
        }
        if (!db()->isField(':like', 'ync_react_id')) {
            db()->addField([
                'table' => Phpfox::getT('like'),
                'field' => 'ync_react_id',
                'type' => 'INT:10',
                'null' => true,
                'default' => '1', // 1 is id of Like action
            ]);
        }

        $iTotalPlugin = db()
            ->select('COUNT(plugin_id)')
            ->from(Phpfox::getT('plugin'))
            ->where(array(
                'module_id' => 'core',
                'product_id' => 'phpfox',
                'call_name' => 'admincp.service_module_process_updateactivity',
                'title' => 'YNC Reaction Hook Update App'
            ))
            ->execute('getField');

        if ($iTotalPlugin == 0) {
            // add plugin to support update display block when enable/disable app
            db()->insert(':plugin', array(
                'module_id' => 'core',
                'product_id' => 'phpfox',
                'call_name' => 'admincp.service_module_process_updateactivity',
                'title' => 'YNC Reaction Hook Update App',
                'php_code' => '<?php defined(\'PHPFOX\') or exit(\'NO DICE!\');
                    $module_id = $this->database()->escape($iId);
                    $is_active = (int)($iType == \'1\' ? 1 : 0);
                    if($module_id == \'like\')
                    {
                        if($is_active == 0) {
                            db()->update(Phpfox::getT(\'apps\'), [\'is_active\' => 0], \'apps_id="YNC_Reaction"\');
                        }
                    }
                    if($module_id == \'YNC_Reaction\')
                    {
                        if($is_active != 0) {
                            db()->update(Phpfox::getT(\'module\'), [\'is_active\' => 1], \'module_id="like"\');
                        }
                    }',
                'is_active' => 1
            ));
        }
    }
}
