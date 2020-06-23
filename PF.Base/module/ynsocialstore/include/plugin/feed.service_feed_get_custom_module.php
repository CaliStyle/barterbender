<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/20/16
 * Time: 4:55 PM
 */
?>
<?php
if (Phpfox_Request::instance()->get('req1') == 'ynsocialstore' || (!empty($this->_aCallback['module']) && $this->_aCallback['module'] == 'ynsocialstore')) {
    unset($aRow['parent_user_id']);
}
?>
