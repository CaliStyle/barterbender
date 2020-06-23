<?php
$aUserYnm = $_SESSION['aUserYnmember'];
$iUserId = $aUserYnm['user_id'];
list($aStudyPlaces, $aWorkPlaces, $aLivingPlaces, $aLivedPlaces) = Phpfox::getService('ynmember.place.browse')->getPlacesOfUser($iUserId);


if (!empty($aStudyPlaces)) {
    $aStudyPlacesTitle = _p('Study at');
    ?>
    <div class="item">
        <div class="item-label">
            <?php echo $aStudyPlacesTitle ?>:
        </div>
        <div class="item-value">
            <?php foreach ($aStudyPlaces as $aStudyPlace) { ?>
                <div>
                    <a href="<?php echo ynmember_place($aStudyPlace) ?>"><?php echo $aStudyPlace['location_title'] ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php
}

if (!empty($aWorkPlaces)) {
    $aWorkPlacesTitle = _p('Work at');
    ?>
    <div class="item">
        <div class="item-label">
            <?php echo $aWorkPlacesTitle ?>:
        </div>
        <div class="item-value">
            <?php foreach ($aWorkPlaces as $aWorkPlace) { ?>
                <div>
                    <a href="<?php echo ynmember_place($aWorkPlace) ?>"><?php echo $aWorkPlace['location_title'] ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php
}

if (!empty($aLivingPlaces)) {
    $aLivingPlacesTitle = _p('Living in');
    ?>
    <div class="item">
        <div class="item-label">
            <?php echo $aLivingPlacesTitle ?>:
        </div>
        <div class="item-value">
            <?php foreach ($aLivingPlaces as $aLivingPlace) { ?>
                <div>
                    <a href="<?php echo ynmember_place($aLivingPlace) ?>"><?php echo $aLivingPlace['location_title'] ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php
}

if (!empty($aLivedPlaces)) {
    $aLivedPlacesTitle = _p('Lived in');
    ?>
    <div class="item">
        <div class="item-label">
            <?php echo $aLivedPlacesTitle ?>:
        </div>
        <div class="item-value">
            <?php foreach ($aLivedPlaces as $aLivedPlace) { ?>
                <div>
                    <a href="<?php echo ynmember_place($aLivedPlace) ?>"><?php echo $aLivedPlace['location_title'] ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php
}
?>

