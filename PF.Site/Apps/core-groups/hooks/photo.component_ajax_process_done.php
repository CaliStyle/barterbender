<?php

if (isset($aCallback['module']) && $aCallback['module'] == 'groups') {
    // temporary save content, because function send of mail clean all => cause issue when use plugin in ajax
    $content = ob_get_contents();
    ob_clean();

    // validate whom to send notification
    $aGroup = Phpfox::getService('groups')->getPage($aPhoto['group_id']);
    if ($aGroup) {
        $sLink = Phpfox::getService('groups')->getUrl($aGroup['page_id'], $aGroup['title'], $aGroup['vanity_url']);
        $postedUserFullName = Phpfox::getUserBy('full_name');

        $varPhraseTitle = 'full_name_post_some_images_on_group_title';
        $varPhraseLink = 'full_name_post_some_images_on_group_title_link';

        // get all admins (include owner) and send notification
        $aAdmins = Phpfox::getService('groups')->getPageAdmins($aGroup['page_id']);
        foreach ($aAdmins as $aAdmin) {
            if ($aPhoto['user_id'] == $aAdmin['user_id']) { // is owner of photo
                continue;
            }

            if ($aGroup['user_id'] == $aAdmin['user_id']) { // is owner of group
                $varPhraseTitle = 'full_name_post_some_images_on_your_group_title';
                $varPhraseLink = 'full_name_post_some_images_on_your_group_title_link';
            }

            Phpfox::getLib('mail')->to($aAdmin['user_id'])
                ->subject([$varPhraseTitle, [
                    'full_name' => $postedUserFullName,
                    'title' => $aGroup['title']
                ]])
                ->message([$varPhraseLink, [
                    'full_name' => $postedUserFullName,
                    'link' => $sLink,
                    'title' => $aGroup['title']
                ]])
                ->notification('comment.add_new_comment')
                ->send();

            if (Phpfox::isModule('notification')) {
                Phpfox::getService('notification.process')->add('groups_post_image', $aPhoto['photo_id'], $aAdmin['user_id']);
            }
        }
    }

    // return content
    echo $content;
}

