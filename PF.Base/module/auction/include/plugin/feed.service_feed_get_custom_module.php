<?php
if (Phpfox_Request::instance()->get('req1') == 'auction' || (!empty($this->_aCallback['module']) && in_array($this->_aCallback['module'], ['auction', 'ecommerce']))) {
    unset($aRow['parent_user_id']);
}
