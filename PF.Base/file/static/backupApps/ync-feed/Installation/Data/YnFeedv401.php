<?php

namespace Apps\YNC_Feed\Installation\Data;

use Phpfox;

class YnFeedv401
{
    private $_aDefaultFilters;
    private $_sEmoticons;
    private $_sFeelings;

    public function __construct()
    {
        $this->_aDefaultFilters = [
            [
                'module_id' => 'feed',
                'type' => 'all',
                'title' => 'ynfeed_filter_all',
                'ordering' => 1,
                'is_default' => 1,
                'is_show' => 1,
            ],
            [
                'module_id' => 'user',
                'type' => 'membership',
                'title' => 'ynfeed_filter_membership',
                'ordering' => 2,
                'is_default' => 1,
                'is_show' => 1,
            ],
            [
                'module_id' => 'user',
                'type' => 'status',
                'title' => 'ynfeed_filter_status',
                'ordering' => 3,
                'is_default' => 1,
                'is_show' => 1,
            ],
            [
                'module_id' => 'photo',
                'type' => 'photo',
                'title' => 'ynfeed_filter_photo',
                'ordering' => 4,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'blog',
                'type' => 'blog',
                'title' => 'ynfeed_filter_blog',
                'ordering' => 5,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'yn_blog',
                'type' => 'yn_blog',
                'title' => 'ynfeed_filter_yn_blog',
                'ordering' => 6,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'v',
                'type' => 'video',
                'title' => 'ynfeed_filter_video',
                'ordering' => 7,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'ultimatevideo',
                'type' => 'ultimatevideo',
                'title' => 'ynfeed_filter_ultimatevideo',
                'ordering' => 8,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'event',
                'type' => 'event',
                'title' => 'ynfeed_filter_event',
                'ordering' => 9,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'fevent',
                'type' => 'fevent',
                'title' => 'ynfeed_filter_fevent',
                'ordering' => 10,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'groups',
                'type' => 'group',
                'title' => 'ynfeed_filter_group',
                'ordering' => 11,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'pages',
                'type' => 'page',
                'title' => 'ynfeed_filter_page',
                'ordering' => 12,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'music',
                'type' => 'music',
                'title' => 'ynfeed_filter_music',
                'ordering' => 13,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'musicsharing',
                'type' => 'musicsharing',
                'title' => 'ynfeed_filter_musicsharing',
                'ordering' => 14,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'forum',
                'type' => 'forum',
                'title' => 'ynfeed_filter_forum',
                'ordering' => 15,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'ynfeed',
                'type' => 'user_saved',
                'title' => 'ynfeed_filter_user_saved',
                'ordering' => 16,
                'is_default' => 1,
                'is_show' => 1,
            ],
            [
                'module_id' => 'videochannel',
                'type' => 'videochannel',
                'title' => 'ynfeed_filter_videochannel',
                'ordering' => 17,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'ynsocialstore',
                'type' => 'ynsocialstore',
                'title' => 'ynfeed_filter_ynsocialstore',
                'ordering' => 18,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'directory',
                'type' => 'directory',
                'title' => 'ynfeed_filter_directory',
                'ordering' => 19,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'jobposting',
                'type' => 'jobposting',
                'title' => 'ynfeed_filter_jobposting',
                'ordering' => 20,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'poll',
                'type' => 'poll',
                'title' => 'ynfeed_filter_poll',
                'ordering' => 21,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'fundraising',
                'type' => 'fundraising',
                'title' => 'ynfeed_filter_fundraising',
                'ordering' => 22,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'foxfeedspro',
                'type' => 'news',
                'title' => 'ynfeed_filter_news',
                'ordering' => 23,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'contest',
                'type' => 'contest',
                'title' => 'ynfeed_filter_contest',
                'ordering' => 24,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'document',
                'type' => 'document',
                'title' => 'ynfeed_filter_document',
                'ordering' => 25,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'donation',
                'type' => 'donation',
                'title' => 'ynfeed_filter_donation',
                'ordering' => 26,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'marketplace',
                'type' => 'marketplace',
                'title' => 'ynfeed_filter_marketplace',
                'ordering' => 27,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'feedback',
                'type' => 'feedback',
                'title' => 'ynfeed_filter_feedback',
                'ordering' => 28,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'auction',
                'type' => 'auction',
                'title' => 'ynfeed_filter_auction',
                'ordering' => 29,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'petition',
                'type' => 'petition',
                'title' => 'ynfeed_filter_petition',
                'ordering' => 30,
                'is_default' => 0,
                'is_show' => 1,
            ],
            [
                'module_id' => 'resume',
                'type' => 'resume',
                'title' => 'ynfeed_filter_resume',
                'ordering' => 31,
                'is_default' => 0,
                'is_show' => 1,
            ]
        ];
        $this->_sEmoticons = "INSERT IGNORE INTO `" . Phpfox::getT('ynfeed_emoticon') . "` (`emoticon_id`, `title`, `code`, `image`, `ordering`) VALUES
            (1, 'angel', '(angel)', 'angel.gif', 0),
            (2, 'angry', ':@', 'angry.gif', 0),
            (3, 'bearhug', '(hug)', 'bearhug.gif', 0),
            (4, 'beer', '(beer)', 'beer.gif', 0),
            (5, 'blush', '(blush)', 'blush.gif', 0),
            (6, 'bow', '(bow)', 'bow.gif', 0),
            (7, 'boxing', '(punch)', 'boxing.gif', 0),
            (8, 'brokenheart', '(u)', 'brokenheart.gif', 0),
            (9, 'cake', '(^)', 'cake.gif', 0),
            (10, 'callme', '(call)', 'callme.gif', 0),
            (11, 'cash', '(cash)', 'cash.gif', 0),
            (12, 'cellphone', '(mp)', 'cellphone.gif', 0),
            (13, 'clapping', '(clap)', 'clapping.gif', 0),
            (14, 'coffee', '(coffee)', 'coffee.gif', 0),
            (15, 'cool', '8-)', 'cool.gif', 0),
            (16, 'crying', ';(', 'crying.gif', 0),
            (17, 'dance', '(dance)', 'dance.gif', 0),
            (18, 'devil', '(devil)', 'devil.gif', 0),
            (19, 'doh', '(doh)', 'doh.gif', 0),
            (20, 'drink', '(d)', 'drink.gif', 0),
            (21, 'dull', '|-(', 'dull.gif', 0),
            (22, 'emo', '(emo)', 'emo.gif', 0),
            (23, 'evilgrin', ']:)', 'evilgrin.gif', 0),
            (24, 'flex', '(flex)', 'flex.gif', 0),
            (25, 'flower', '(F)', 'flower.gif', 0),
            (26, 'giggle', '(chuckle)', 'giggle.gif', 0),
            (27, 'handshake', '(handshake)', 'handshake.gif', 0),
            (28, 'happy', '(happy)', 'happy.gif', 0),
            (29, 'heart', '(h)', 'heart.gif', 0),
            (30, 'hi', '(wave)', 'hi.gif', 0),
            (31, 'inlove', '(inlove)', 'inlove.gif', 0),
            (32, 'itwasntme', '(wasntme)', 'itwasntme.gif', 0),
            (33, 'jealous', '(envy)', 'jealous.gif', 0),
            (34, 'kiss', ':*', 'kiss.gif', 0),
            (35, 'laughing', ':D', 'laughing.gif', 0),
            (36, 'mail', '(e)', 'mail.gif', 0),
            (37, 'makeup', '(makeup)', 'makeup.gif', 0),
            (38, 'mmm', '(mm)', 'mmm.gif', 0),
            (39, 'music', '(music)', 'music.gif', 0),
            (40, 'nerd', '8-|', 'nerd.gif', 0),
            (41, 'no', '(n)', 'no.gif', 0),
            (42, 'nod', '(nod)', 'nod.gif', 0),
            (43, 'nospeak', ':x', 'nospeak.gif', 0),
            (44, 'party', '(party)', 'party.gif', 0),
            (45, 'puke', '(puke)', 'puke.gif', 0),
            (46, 'rofl', '(rofl)', 'rofl.gif', 0),
            (47, 'sad', ':(', 'sad.gif', 0),
            (48, 'shakeno', '(shake)', 'shakeno.gif', 0),
            (49, 'smile', ':)', 'smile.gif', 0),
            (50, 'speechless', ':-|', 'speechless.gif', 0),
            (51, 'sweating', '(sweat)', 'sweating.gif', 0),
            (52, 'thinking', '(think)', 'thinking.gif', 0),
            (53, 'tongue out', ':p', 'tongueout.gif', 0),
            (54, 'wait', '(wait)', 'wait.gif', 0),
            (55, 'whew', '(whew)', 'whew.gif', 0),
            (56, 'wink', ';)', 'wink.gif', 0),
            (57, 'worried', ':S', 'worried.gif', 0),
            (58, 'yes', '(y)', 'yes.gif', 0);";

        $this->_sFeelings = "INSERT IGNORE INTO `" . Phpfox::getT('ynfeed_feeling') . "` (`feeling_id`, `title`, `code`, `image`, `ordering`) VALUES
            (1, 'thankful', ':grinning:', '1f600.svg', 0),
            (2, 'awesome', ':grinning:', '1f600.svg', 0),
            (3, 'hopeful', ':grinning:', '1f600.svg', 0),
            (4, 'great', ':grinning:', '1f600.svg', 0),
            (5, 'entertained', ':grinning:', '1f600.svg', 0),
            (6, 'fantastic', ':grin:', '1f601.svg', 0),
            (7, 'energized', ':grin:', '1f601.svg', 0),
            (8, 'amazed', ':grin:', '1f601.svg', 0),
            (9, 'delighted', ':grin:', '1f601.svg', 0),
            (10, 'exhausted', ':sweat:', '1f613.svg', 0),
            (11, 'anxious', ':sweat:', '1f613.svg', 0),
            (12, 'excited', ':heart_eyes:', '1f60d.svg', 0),
            (13, 'special', ':heart_eyes:', '1f60d.svg', 0),
            (14, 'amazing', ':heart_eyes:', '1f60d.svg', 0),
            (15, 'perfect', ':heart_eyes:', '1f60d.svg', 0),
            (16, 'ectatic', ':heart_eyes:', '1f60d.svg', 0),
            (17, 'love', ':kissing_heart:', '1f618.svg', 0),
            (18, 'lovely', ':kissing_heart:', '1f618.svg', 0),
            (19, 'in love', ':kissing_heart:', '1f618.svg', 0),
            (20, 'happy', ':relaxed:', '263a.svg', 0),
            (21, 'wonderful', ':relaxed:', '263a.svg', 0),
            (22, 'refreshed', ':relaxed:', '263a.svg', 0),
            (23, 'satisfied', ':relaxed:', '263a.svg', 0),
            (24, 'good', ':relaxed:', '263a.svg', 0),
            (25, 'amused', ':relaxed:', '263a.svg', 0),
            (26, 'full', ':relaxed:', '263a.svg', 0),
            (27, 'content', ':relaxed:', '263a.svg', 0),
            (28, 'wanted', ':relaxed:', '263a.svg', 0),
            (29, 'crazy', ':stuck_out_tongue_winking_eye:', '1f61c.svg', 0),
            (30, 'awake', ':stuck_out_tongue_winking_eye:', '1f61c.svg', 0),
            (31, 'sick', ':thermometer_face:', '1f912.svg', 0),
            (32, 'ill', ':thermometer_face:', '1f912.svg', 0),
            (33, 'lazy', ':relieved:', '1f60c.svg', 0),
            (34, 'positive', ':relieved:', '1f60c.svg', 0),
            (35, 'chill', ':relieved:', '1f60c.svg', 0),
            (36, 'relaxed', ':relieved:', '1f60c.svg', 0),
            (37, 'peaceful', ':relieved:', '1f60c.svg', 0),
            (38, 'relieved', ':relieved:', '1f60c.svg', 0),
            (39, 'homeless', ':worried:', '1f61f.svg', 0),
            (40, 'homesick', ':worried:', '1f61f.svg', 0),
            (41, 'sad', ':worried:', '1f61f.svg', 0),
            (42, 'upset', ':worried:', '1f61f.svg', 0),
            (43, 'nervous', ':worried:', '1f61f.svg', 0),
            (44, 'frustrated', ':worried:', '1f61f.svg', 0),
            (45, 'drained', ':pensive:', '1f614.svg', 0),
            (46, 'angry', ':rage:', '1f621.svg', 0),
            (47, 'pissed off', ':rage:', '1f621.svg', 0),
            (48, 'annoyed', ':rage:', '1f621.svg', 0),
            (49, 'pissed', ':rage:', '1f621.svg', 0),
            (50, 'aggravated', ':rage:', '1f621.svg', 0),
            (51, 'bored', ':rolling_eyes:', '1f644.svg', 0),
            (52, 'impatient', ':rolling_eyes:', '1f644.svg', 0),
            (53, 'tired', ':sleeping:', '1f634.svg', 0),
            (54, 'sleepy', ':sleeping:', '1f634.svg', 0),
            (55, 'lonely', ':sleeping:', '1f634.svg', 0),
            (56, 'alone', ':sleeping:', '1f634.svg', 0),
            (57, 'hopeless', ':sleeping:', '1f634.svg', 0),
            (58, 'bless', ':innocent:', '1f607.svg', 0),
            (59, 'cool', ':sunglasses:', '1f60e.svg', 0),
            (60, 'fabulous', ':sunglasses:', '1f60e.svg', 0),
            (61, 'cute', ':blush:', '1f60a.svg', 0),
            (62, 'pretty', ':blush:', '1f60a.svg', 0),
            (63, 'blissful', ':blush:', '1f60a.svg', 0),
            (64, 'joyful', ':blush:', '1f60a.svg', 0),
            (65, 'disappointed', ':disappointed:', '1f61e.svg', 0),
            (66, 'worried', ':disappointed:', '1f61e.svg', 0),
            (67, 'down', ':disappointed:', '1f61e.svg', 0),
            (68, 'depressed', ':disappointed:', '1f61e.svg', 0),
            (69, 'bummed', ':disappointed:', '1f61e.svg', 0);";
    }

    public function process()
    {
        $iTotalFilter = db()
            ->select('COUNT(filter_id)')
            ->from(Phpfox::getT('ynfeed_filter'))
            ->execute('getField');

        if ($iTotalFilter == 0) {
            foreach ($this->_aDefaultFilters as $aFilter) {
                db()->insert(Phpfox::getT('ynfeed_filter'), $aFilter);
            }
        }

        $iTotalEmoticon = db()
            ->select('COUNT(emoticon_id)')
            ->from(Phpfox::getT('ynfeed_emoticon'))
            ->execute('getField');

        if ($iTotalEmoticon == 0) {
            db()->query($this->_sEmoticons);
        }

        $iTotalFeeling = db()
            ->select('COUNT(feeling_id)')
            ->from(Phpfox::getT('ynfeed_feeling'))
            ->execute('getField');

        if ($iTotalFeeling == 0) {
            db()->query($this->_sFeelings);
        }

        // disable all feed display blocks
        db()->update(':block', array('is_active' => 0), "component = 'display' AND (module_id = 'feed' OR module_id = 'fevent')");

        // enable all adv. feed display blocks
        db()->update(':block', array('is_active' => 1), "component = 'display' AND module_id = 'ynfeed'");

        $iTotalPlugin = db()
            ->select('COUNT(plugin_id)')
            ->from(Phpfox::getT('plugin'))
            ->where(array(
                'module_id' => 'core',
                'product_id' => 'phpfox',
                'call_name' => 'admincp.service_module_process_updateactivity',
                'title' => 'Advanced Feed Hook Update App'
            ))
            ->execute('getField');
        if ($iTotalPlugin == 0) {
            // add plugin to support update display block when enable/disable app
            db()->insert(':plugin', array(
                'module_id' => 'core',
                'product_id' => 'phpfox',
                'call_name' => 'admincp.service_module_process_updateactivity',
                'title' => 'Advanced Feed Hook Update App',
                'php_code' => '<?php
        defined(\'PHPFOX\') or exit(\'NO DICE!\');
        $module_id = $this->database()->escape($iId);
        $is_active = (int)($iType == \'1\' ? 1 : 0);
        if($module_id == \'feed\')
        {
            if($is_active != 1) {
                db()->update(Phpfox::getT(\'apps\'), [\'is_active\' => 0], \'apps_id="YNC_Feed"\');
                db()->update(\':block\', array(\'is_active\' => 0), "component = \'display\' AND module_id = \'ynfeed\'");
                db()->update(\':block\', array(\'is_active\' => 0), "component = \'display\' AND (module_id = \'feed\' OR module_id = \'fevent\')");
            }
            else {
                db()->update(\':block\', array(\'is_active\' => 1), "component = \'display\' AND (module_id = \'feed\' OR module_id = \'fevent\')");
            }
        }
        if($module_id == \'YNC_Feed\')
        {
            if($is_active == 0) {
                db()->update(\':block\', array(\'is_active\' => 1), "component = \'display\' AND (module_id = \'feed\' OR module_id = \'fevent\')");
                db()->update(\':block\', array(\'is_active\' => 0), "component = \'display\' AND module_id = \'ynfeed\'");
            }
            else {
                db()->update(Phpfox::getT(\'module\'), [\'is_active\' => 1], \'module_id="feed"\');
                db()->update(\':block\', array(\'is_active\' => 0), "component = \'display\' AND (module_id = \'feed\' OR module_id = \'fevent\')");
                db()->update(\':block\', array(\'is_active\' => 1), "component = \'display\' AND module_id = \'ynfeed\'");
            }
        }',
                'is_active' => 1
            ));
        }
    }
}


