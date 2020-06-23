<?php

if (Phpfox::isModule('ynmember'))
{
    $aProcessedUser = $aUser;
    Phpfox::getService('ynmember.member')->processReview($aProcessedUser);

    $htmlContent = ynmember_rating($aProcessedUser['rating']);

    $sUrl = Phpfox::getLib('url')->makeUrl('ynmember.review', ['user_id' => $aProcessedUser['user_id']]);
    $sReviewCountStr = ($aProcessedUser['total_review']) == 1 ? _p('1_review') : _p('more_reviews', ['number' => $aProcessedUser['total_review']]);
    $sUrlContent = '<a href="' . $sUrl . '"><span> (' . $sReviewCountStr . ')</span></a>';

    if(!empty($aProcessedUser['rating']) && $aProcessedUser['rating'] > 0)
    {
        ?>

        <script type="text/javascript">
            $Behavior.ynmemberLoadProfilePic = function(){
                var profiles_info = $('.profiles_info');
                var ynmember_rating_block = $('.ynmember_rating_block');
                if (profiles_info && ynmember_rating_block.length == 0) {
                    var rating_str = '<div class="ynmember_rating_block">' + '<?php echo $htmlContent ?>' + '<?php echo $sUrlContent ?>' + '</div>';
                    profiles_info.append(rating_str);
                }
            }
        </script>

        <?php
    }
}