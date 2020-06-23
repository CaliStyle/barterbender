<?php
if (isset($this->_aCallback['module']) && $this->_aCallback['module'] == 'pages' && Phpfox::getUserId() != $this->_aCallback['email_user_id']) {
    $sLink = $this->_aCallback['link'] . 'comment-id_' . $iStatusId . '/';

    // get and send email/notification to all admins of page
    $aAdmins = Phpfox::getService('pages')->getPageAdmins($this->_aCallback['item_id']);
    foreach ($aAdmins as $aAdmin) {
        if (Phpfox::getUserId() == $aAdmin['user_id'] || $aAdmin['user_id'] == $this->_aCallback['email_user_id']) {
            continue;
        }
        Phpfox::getLib('mail')->to($aAdmin['user_id'])
            ->subject(['full_name_wrote_a_comment_on_page_title', [
                'full_name' => $aAdmin['full_name'],
                'title' => $this->_aCallback['item_title']
            ]])
            ->message(['full_name_wrote_a_comment_on_page_link', [
                'full_name' => $aAdmin['full_name'],
                'title' => $this->_aCallback['item_title'],
                'link' => $sLink
            ]])
            ->notification('comment.add_new_comment')
            ->send();

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('pages_comment', $iStatusId, $aAdmin['user_id']);
        }
    }
}
