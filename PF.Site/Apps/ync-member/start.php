<?php

function ynmember_ratingaction($value, $bIsEditable = false)
{
    $value = floor($value + 0.4999);
    $result = [];

    for ($i = 1; $i <= 5; ++$i) {
        $ratingClass = '';
        $edit = '';
        $disabledClass = '';
        if ($bIsEditable) {
            $ratingClass = 'ynmember_rate ';
            $edit = 'data-toggle="ynmember" data-cmd="rate_member" data-value="' . $i . '"';
        }

        if ($value < $i ) {
            $disabledClass = ' disable';
        }

        $item = '<i class="' . $ratingClass . 'fa fa-star' . $disabledClass . '" ' . $edit . '></i>';
        $result[] = $item;
    }
    return implode('', $result);
}

function ynmember_rating($value)
{
    $result = [];

    for ($i = 0; $i < 5; ++$i) {

        if ($i < (int)$value) {
            $item = '<i class="' . 'fa fa-star' . '"'  . '></i>';
        } elseif (((round($value) - $value) > 0) && ($value - $i) > 0) {
            $item = '<i class="' . 'fa fa-star-half-o' . '"'  . '></i>';
        } else {
            $item = '<i class="'  . 'fa fa-star disable' . '"'  . '></i>';
        }

        $result[] = $item;

    }
    return implode('', $result);
}

function ynmember_subtract($value, $subtract = 1){
    return (int) $value - (int) $subtract;
}

function ynmember_round($value, $precision = 1)
{
    return number_format($value, $precision);
}

function ynmember_convert_birthday($value) {
    return date('d F', strtotime(substr($value, 0, 2) . '/' . substr($value, 2, 2) . '/' . substr($value, 4, 4)));
}

function ynmember_gender($value) {

    if (!empty($value['gender'])) {
        if ($value['gender'] == 1)
            return _p('him');
        elseif ($value['gender'] == 2)
            return _p('her');
    }
    return $value['full_name'];
}

function ynmember_place($value) {
    if (!empty($value['company_id']))
        return Phpfox::permalink('jobposting.company', $value['company_id'], $value['name']);
    if (!empty($value['business_id']))
        return Phpfox::permalink('directory.detail', $value['business_id'], $value['name']);
    if (!empty($value['location_latitude']) && !empty($value['location_longitude'])) {
        return 'https://www.google.com/maps/place/' . $value['location_latitude'] . ',' . $value['location_longitude'] . '" target="_blank"';
    }
    if (!empty($value['location_address'])) {
        return 'https://maps.google.com/?q=' . $value['location_address'] . '" target="_blank"';
    }
    return '#';
}

$module = \Phpfox_Module::instance();

$module->addAliasNames('ynmember', 'YNC_Member');

$module->addComponentNames('controller', [
    'ynmember.index' => Apps\YNC_Member\Controller\IndexController::class,
    'ynmember.review' => Apps\YNC_Member\Controller\ReviewController::class,
    'ynmember.writereview' => Apps\YNC_Member\Controller\WriteReviewController::class,
    'ynmember.birthday' => Apps\YNC_Member\Controller\BirthdayController::class,
    'ynmember.birthdaywish' => Apps\YNC_Member\Controller\BirthdayWishController::class,
    'ynmember.admincp.managemembers' => Apps\YNC_Member\Controller\Admin\ManageMembersController::class,
    'ynmember.admincp.managereviews' => Apps\YNC_Member\Controller\Admin\ManageReviewsController::class,
    'ynmember.profile_places' => Apps\YNC_Member\Controller\ProfilePlacesController::class,
    'ynmember.add_place' => Apps\YNC_Member\Controller\AddPlaceController::class,
    'ynmember.admincp.customfield.add' => Apps\YNC_Member\Controller\Admin\Customfield\AddController::class,
    'ynmember.admincp.customfield.index' => Apps\YNC_Member\Controller\Admin\Customfield\IndexController::class,
    'ynmember.admincp.customfield.addfield' => Apps\YNC_Member\Controller\Admin\Customfield\AddFieldController::class,
]);

$module->addComponentNames('block', [
    'ynmember.featured_members' => Apps\YNC_Member\Block\FeaturedMembers::class,
    'ynmember.most_reviewed' => Apps\YNC_Member\Block\MostReviewed::class,
    'ynmember.top_rated' => Apps\YNC_Member\Block\TopRated::class,
    'ynmember.people_you_may_know' => Apps\YNC_Member\Block\PeopleYouMayKnow::class,
    'ynmember.recommended_friends' => Apps\YNC_Member\Block\RecommendedFriends::class,
    'ynmember.member_of_day' => Apps\YNC_Member\Block\MemberOfDay::class,
    'ynmember.birthday_calendar' => Apps\YNC_Member\Block\BirthdayCalendar::class,
    'ynmember.advanced_search' => Apps\YNC_Member\Block\AdvancedSearch::class,
    'ynmember.entry_link_friendship' => Apps\YNC_Member\Block\EntryLinkFriendship::class,
    'ynmember.entry_link_action' => Apps\YNC_Member\Block\EntryLinkAction::class,
    'ynmember.editcustomfield' => Apps\YNC_Member\Block\EditCustomFieldBlock::class,
    'ynmember.custom.view' => Apps\YNC_Member\Block\Custom\ViewBlock::class,
]);

$module->addComponentNames('ajax', [
    'ynmember.ajax' => Apps\YNC_Member\Ajax\Ajax::class,
]);

$module->addServiceNames([
    'ynmember.browse' => Apps\YNC_Member\Service\Browse::class,
    'ynmember.process' => Apps\YNC_Member\Service\Process::class,
    'ynmember.member' => Apps\YNC_Member\Service\Member::class,
    'ynmember.review' => Apps\YNC_Member\Service\Review\Review::class,
    'ynmember.review.browse' => Apps\YNC_Member\Service\Review\Browse::class,
    'ynmember.review.process' => Apps\YNC_Member\Service\Review\Process::class,
    'ynmember.place' => Apps\YNC_Member\Service\Place\Place::class,
    'ynmember.place.browse' => Apps\YNC_Member\Service\Place\Browse::class,
    'ynmember.place.process' => Apps\YNC_Member\Service\Place\Process::class,
    'ynmember.callback' => Apps\YNC_Member\Service\Callback::class,
    'ynmember.custom' => Apps\YNC_Member\Service\Custom\Custom::class,
    'ynmember.custom.group' => Apps\YNC_Member\Service\Custom\Group::class,
    'ynmember.custom.process' => Apps\YNC_Member\Service\Custom\Process::class,
    'ynmember.feature' => Apps\YNC_Member\Service\Feature::class,
]);

$module->addTemplateDirs([
    'ynmember' => PHPFOX_DIR_SITE_APPS . 'ync-member' . PHPFOX_DS . 'views',
]);
//
group('/ynmember', function (){
//    route('/admincp', function () {
//        auth()->isAdmin(true);
//        \Phpfox_Module::instance()->dispatch('ynmember.admincp.managemembers');
//        return 'controller';
//    });
    route('/', 'ynmember.index');
    route('/review', 'ynmember.review');
    route('/writereview', 'ynmember.writereview');
    route('/add_place', 'ynmember.add_place');
    route('/birthday', 'ynmember.birthday');
    route('/birthdaywish', 'ynmember.birthdaywish');
    route('/review/*', 'ynmember.review');
});
//route('/user/browse', 'ynmember.index');

group('/admincp/ynmember/customfield',function(){
    route('/add', function (){
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('ynmember.admincp.customfield.add');
        return 'controller';
    });
    route('/edit/:id', function (){
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('ynmember.admincp.customfield.add');
        return 'controller';
    });
    route('/index', function (){
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('ynmember.admincp.customfield.index');
        return 'controller';
    });
});

// (new Apps\YNC_Member\Install())->processInstall();