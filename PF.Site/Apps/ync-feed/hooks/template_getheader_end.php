<?php
$sData .= '<script>
sGoogleKey = "' . Phpfox::getParam('core.google_api_key') . '";
bAutoloadFeed = "' . setting('ynfeed_enable_auto_loading_by_scrolling_down') . '";
sHomeUrl = "' . Phpfox_Url::instance()->makeUrl('') . '";
sDefaultFeelingImg = "' . Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-feed/assets/images/feelings/263a.svg' . '";
oTranslations["people"] = "'. html_entity_decode(_p("People")) .'";
oTranslations["select_feeling_icon"] = "'. html_entity_decode(_p("select_feeling_icon")) .'";
oTranslations["at_location"] = "'. html_entity_decode(_p("at_location")) .'";
oTranslations["feeling_something"] = "'. html_entity_decode(_p("feeling_something")) .'";
oTranslations["with_somebody"] = "'. html_entity_decode(_p("with_somebody")) .'";
oTranslations["with_somebody_and_somebody"] = "'. html_entity_decode(_p("with_somebody_and_somebody")) .'";
oTranslations["with_somebody_and_number_others"] = "'. html_entity_decode(_p("with_somebody_and_number_others")) .'";
oTranslations["number_others"] = "'. html_entity_decode(_p("number_others")) .'";
oTranslations["business_name"] = "'. html_entity_decode(_p("Business name")) .'";
oTranslations["what_do_you_feel_right_now"] = "'. html_entity_decode(_p("What do you feel right now?")) .'";
oTranslations["who_is_with_you"] = "'. html_entity_decode(_p("Who is with you?")) .'";
oTranslations["unhide_number_items"] = "'. html_entity_decode(_p("unhide_number_items")) .'";
oTranslations["unhide_one_item"] = "'. html_entity_decode(_p("unhide_one_item")) .'";
oTranslations["you_wont_see_this_post_in_news_feed_undo"] = "'. html_entity_decode(_p("you_wont_see_this_post_in_news_feed_undo")) .'";
oTranslations["undo"] = "'. html_entity_decode(_p("Undo")) .'";
oTranslations["hide_all_from_somebody"] = "'. html_entity_decode(_p("hide_all_from_somebody")) .'";
oTranslations["you_wont_see_posts_from_somebody_undo"] = "'. html_entity_decode(_p("you_wont_see_posts_from_somebody_undo")) .'";
oTranslations["remove_tag_confirmation"] = "'. html_entity_decode(_p("remove_tag_confirmation")) .'";
oTranslations["one_item_selected"] = "'. html_entity_decode(_p("one_item_selected")) .'";
oTranslations["number_items_selected"] = "'. html_entity_decode(_p("number_items_selected")) .'";
//Disable core feed js
$Behavior.activityFeedProcess = function() {};
$Core.forceLoadOnFeed = function() {};
$Core.loadMoreFeed = function() {};
$Behavior.checkForNewFeed = function() {};
</script>';
