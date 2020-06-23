<?php
namespace Apps\YNC_StatusBg\Installation\Data;


defined('PHPFOX') or exit('NO DICE!');

/**
 * Class v401
 * @package Apps\YNC_StatusBg\Installation\Data
 */
class v401
{

    public function __construct()
    {

    }

    public function process()
    {
        $iTotalCollection = db()->select('COUNT(*)')
            ->from(':yncstatusbg_collections')
            ->execute('getField');
        if (!$iTotalCollection) {
            //Insert default collection
            $aInsert = [
                'title' => 'younetco_status_theme',
                'view_id' => 1,
                'is_default' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
                'time_stamp' => PHPFOX_TIME,
                'total_background' => 30
            ];
            $iId = db()->insert(':yncstatusbg_collections', $aInsert);
            if ($iId) {
                for ($i = 1; $i <= 30; $i++) {
                    $aData = [
                        'collection_id' => $iId,
                        'image_path' => 'bg' . ($i < 10 ? '0' . $i : $i) . '-min.png',
                        'server_id' => 0,
                        'ordering' => $i,
                        'time_stamp' => PHPFOX_TIME,
                        'view_id' => 1
                    ];
                    $iBgId = db()->insert(':yncstatusbg_backgrounds', $aData);
                    if ($iBgId && $i == 1) {
                        db()->update(':yncstatusbg_collections', ['main_image_id' => $iBgId], 'collection_id =' . $iId);
                    }
                }
            }
        }
    }
}
