<?php

namespace Apps\P_AdvEventAPI\Installation\Version;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class v453
 * @package Apps\Posts\Installation\Version
 */
class v401
{
    public function __construct()
    {
    }

    public function process()
    {
        // Mobile API Installing Support
        if (db()->tableExists(Phpfox::getT('mobile_api_menu_item'))) {
            $menuExist = db()->select("item_id")
                ->from(Phpfox::getT('mobile_api_menu_item'))
                ->where('path = "fevent"')
                ->execute('getField');

            if (!$menuExist) {
                db()->insert(Phpfox::getT("mobile_api_menu_item"), [
                    "section_id" => 2,
                    "name" => "Advanced Event",
                    "item_type" => 1,
                    "is_active" => 1,
                    "icon_name" => 'calendar',
                    "icon_family" => 'Lineficon',
                    "icon_color" => '#ff564a',
                    "path" => 'fevent',
                    "is_system" => 0,
                    "module_id" => 'fevent',
                    "ordering" =>  21
                ]);
            }
            else {
                db()->update(Phpfox::getT("mobile_api_menu_item"), [
                    "module_id" => 'fevent'
                ], 'path = "fevent"');
            }
        }

    }

}
