<?php
$iUserId = $this->_aVars['aUser']['user_id'];
$oReviewService = Phpfox::getService('ynmember.review');
if ($oReviewService->isWrittenReviewFor($iUserId)) {
    $aReview = $oReviewService->getMyReviewFor($iUserId);
    $sUrl = Phpfox::getLib('url')->makeUrl('ynmember.writereview', ['user_id' => $iUserId, 'review_id' => $aReview['review_id']]);
} else {
    $sUrl = Phpfox::getLib('url')->makeUrl('ynmember.writereview', ['user_id' => $iUserId]);
}

if (Phpfox::getService('ynmember.review')->canWriteReview($iUserId)) {
    if (Phpfox::getUserId() == $iUserId) {
        ?>
        <li>
            <a href="<?php echo $sUrl ?>" class="popup"><?php echo '<span class="ico ico-star-circle-o"></span>' . _p('Review & Rate yourself') ?></a>
        </li>
        <?php
    } else {
        ?>
        <li>
            <a href="<?php echo $sUrl ?>" class="popup"><?php echo '<span class="ico ico-star-circle-o"></span>' . _p('Review & Rate this User') ?></a>
        </li>
        <?php
    }
}

if (Phpfox::getUserParam('ynmember_follow_member') && Phpfox::getService('user.privacy')->hasAccess($iUserId, 'ynmember.follow')) {
    if (Phpfox::getService('ynmember.member')->isFollowingMember(Phpfox::getUserId(), $iUserId)) {
        $sFollowStr = '<span class="ico ico-minus"></span>' . _p('Stop getting notification');
    } else {
        $sFollowStr = '<span class="ico ico-plus"></span>' . _p('Get notification');
    }
    ?>
    <li>
        <a href="javascript:void(0)" onclick="ynmember.followMemberOnProfile(<?php echo $iUserId ?>)">
            <?php echo $sFollowStr ?>
        </a>
    </li>
    <?php
}
?>

