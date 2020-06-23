<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>

<?php
$iPageId = Phpfox::getService('donation')->getPageIdFromUrl();
if($iPageId > 0)
{
    (($sPlugin = Phpfox_Plugin::get('pages.component_controller_index_clean')) ? eval($sPlugin) : false);
}
?>