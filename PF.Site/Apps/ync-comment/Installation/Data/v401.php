<?php
namespace Apps\YNC_Comment\Installation\Data;


use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class v401
 * @package Apps\YNC_Comment\Installation\Data
 */
class v401
{
    private $_sEmoticons;
    private $_aStickerSetDefault;

    public function __construct()
    {
        $this->_sEmoticons = "INSERT IGNORE INTO `" . Phpfox::getT('ynccomment_emoticon') . "` (`title`, `code`, `image`, `ordering`) VALUES
            ('waving', '(waving)', '1f44b.png', 0),
            ('OK', '(OK)' , '1f44c.png', 0),
			('yes', '(y)', '1f44d.png', 0),
			('no', '(n)', '1f44e.png', 0),
			('clap', '(clap)', '1f44f.png', 0),
			('smiling', '(smiling)', '1f60a.png', 0),
			('savoring', '(savoring)', '1f60b.png', 0),
			('relieved', '(relieved)', '1f60c.png', 0),
			('hearteyes', '(hearteyes)', '1f60d.png', 0),
			('cool', '(cool)', '1f60e.png', 0),
			('smirking', '(smirking)', '1f60f.png', 0),
			('kiss', '(kiss)', '1f61a.png', 0),
			('tongue', ':P', '1f61b.png', 0),
			('disappointed', '(disappointed)', '1f60e.png', 0),
			('worried', ':S', '1f60f.png', 0),
			('sleepy', '(sleepy)', '1f62a.png', 0),
			('cryingface', '(cryingface)', '1f622.png', 0),
			('crying', ';(', '1f62d.png', 0),
			('surprise', ':O', '1f62e.png', 0),
			('handshake', '(handshake)', '1f91d.png', 0),
			('rockon', '(rockon)', '1f91f.png', 0),
			('zany', '(zany)', '1f92a.png', 0),
			('shush', '(shush)', '1f92b.png', 0),
			('giggle', '(chuckle)', '1f92d.png', 0),
			('vomiting', '(puke)', '1f92e.png', 0),
			('brokenheart', '(brokenheart)', '1f494.png', 0),
			('grinning', ':D', '1f600.png', 0),
			('beaming', '(beaming)', '1f601.png', 0),
			('tearofjoys', '(tearofjoys)', '1f602.png', 0),
			('smilingeyes', '(smilingeyes)', '1f604.png', 0),
			('sweat', '(sweat)', '1f605.png', 0),
			('squinting', '(squint)', '1f606.png', 0),
			('angel', '(angel)', '1f607.png', 0),
			('devil', '(devil)', '1f608.png', 0),
			('winking', ';)', '1f609.png', 0),
			('neutral', ':|', '1f610.png', 0),
			('expressionless', '(expressionless)', '1f611.png', 0),
			('unamused', '(unamused)', '1f612.png', 0),
			('downcast', '(downcast)', '1f613.png', 0),
			('sad', ':(', '1f614.png', 0),
			('confused', ':-/', '1f615.png', 0),
			('confounded', '(confounded)', '1f616.png', 0),
			('kissingface', '(kissingface)', '1f617.png', 0),
			('blowingkiss', ':-*', '1f618.png', 0),
			('angry', '(angry)', '1f620.png', 0),
			('pouting', '(pouting)', '1f621.png', 0),
			('persevering', '(persevering)', '1f623.png', 0),
			('steamnose', '(steamnose)', '1f624.png', 0),
			('anguished', '(anguished)', '1f627.png', 0),
			('fearful', '(fearful)', '1f628.png', 0),
			('weary', '(weary)', '1f629.png', 0),
			('anxious', '(anxious)', '1f630.png', 0),
			('screaming', '(scream)', '1f631.png', 0),
			('sleeping', '(sleep)', '1f634.png', 0),
			('dizzy', '(dizzy)', '1f635.png', 0),
			('emptymouth', '(emptymouth)', '1f636.png', 0),
			('medicalmask', '(medicalmask)', '1f637.png', 0),
			('frowning', '(frown)', '1f641.png', 0),
			('smile', ':)', '1f642.png', 0),
			('upsidedown', '(upsidedown)', '1f643.png', 0),
			('rollingeyes', '(rollingeyes)', '1f644.png', 0),
			('zipper', '(zipper)', '1f910.png', 0),
			('moneymouth', '(moneymouth)', '1f911.png', 0),
			('sicking', '(sick)', '1f912.png', 0),
			('nerd', '(nerd)', '1f913.png', 0),
			('thinking', '(think)', '1f914.png', 0),
			('injure', '(injure)', '1f915.png', 0),
			('hugging', '(hug)', '1f917.png', 0),
			('nauseated', '(nauseated)', '1f922.png', 0),
			('drooling', '(drooling)', '1f924.png', 0),
			('lying', '(lie)', '1f925.png', 0),
			('sneezing', '(sneeze)', '1f927.png', 0),
			('starstruck', '(starstruck)', '1f929.png', 0),
			('star', '(star)', '2b50.png', 0),
			('victory', '(victory)', '270c.png', 0),
			('heart', '(heart)', '2665.png', 0);
		";
        $this->_aStickerSetDefault = [
            [
                'set_id' => 1,
                'title' => 'Cute Deer & Bear',
                'total_sticker' => 23,
                'is_active' => 1,
                'is_default' => 1,
                'ordering' => 1,
                'view_only' => 1,
            ],
            [
                'set_id' => 2,
                'title' => 'Meme Sticker',
                'total_sticker' => 35,
                'is_active' => 1,
                'is_default' => 1,
                'ordering' => 1,
                'view_only' => 1,
            ],
        ];
    }

    public function process()
    {

        $iTotalEmoticon = db()
            ->select('COUNT(emoticon_id)')
            ->from(':ynccomment_emoticon')
            ->execute('getField');

        if ($iTotalEmoticon == 0) {
            db()->query($this->_sEmoticons);
        }
        $iTotalPlugin = db()
            ->select('COUNT(plugin_id)')
            ->from(Phpfox::getT('plugin'))
            ->where(array(
                'module_id' => 'core',
                'product_id' => 'phpfox',
                'call_name' => 'admincp.service_module_process_updateactivity',
                'title' => 'Advanced Comment Hook Update App'
            ))
            ->execute('getField');
        //Hide comment setting
        db()->update(':setting', ['is_hidden' => 1],
            'var_name IN (\'comment_page_limit\',\'total_comments_in_activity_feed\',\'thread_comment_total_display\')');

        if ($iTotalPlugin == 0) {
            // add plugin to support update display block when enable/disable app
            db()->insert(':plugin', array(
                'module_id' => 'core',
                'product_id' => 'phpfox',
                'call_name' => 'admincp.service_module_process_updateactivity',
                'title' => 'Advanced Comment Hook Update App',
                'php_code' => '<?php defined(\'PHPFOX\') or exit(\'NO DICE!\');
                    $module_id = $this->database()->escape($iId);
                    $is_active = (int)($iType == \'1\' ? 1 : 0);
                    if($module_id == \'comment\')
                    {
                        if($is_active == 0) {
                            db()->update(Phpfox::getT(\'apps\'), [\'is_active\' => 0], \'apps_id="YNC_Comment"\');
                        } else {
                            db()->update(\':setting\',[\'is_hidden\' => 0],"var_name IN (\'comment_page_limit\',\'total_comments_in_activity_feed\',\'thread_comment_total_display\')");
                        }
                    }
                    if($module_id == \'YNC_Comment\')
                    {
                        if($is_active == 0) {
                            db()->update(\':setting\',[\'is_hidden\' => 0],"var_name IN (\'comment_page_limit\',\'total_comments_in_activity_feed\',\'thread_comment_total_display\')");
                        }
                        else {
                            db()->update(\':setting\',[\'is_hidden\' => 1],"var_name IN (\'comment_page_limit\',\'total_comments_in_activity_feed\',\'thread_comment_total_display\')");
                            db()->update(Phpfox::getT(\'module\'), [\'is_active\' => 1], \'module_id="comment"\');
                        }
                    }',
                'is_active' => 1
            ));
        }
        $iTotalSticker = db()
            ->select('COUNT(*)')
            ->from(':ynccomment_sticker_set')
            ->execute('getField');
        if (!$iTotalSticker) {
            foreach ($this->_aStickerSetDefault as $aSet) {
                db()->insert(':ynccomment_sticker_set', $aSet);
            }
            $aStickerSet = db()->select('*')
                                ->from(':ynccomment_sticker_set')
                                ->execute('getSlaveRows');
            foreach ($aStickerSet as $aSet) {
                for ($i = 1; $i <= $aSet['total_sticker']; $i++) {
                    $iId = db()->insert(':ynccomment_stickers',[
                        'set_id' => $aSet['set_id'],
                        'image_path' => $i < 10 ? '0'.$i.'.gif' : $i.'.gif',
                        'ordering' => $i,
                        'view_only' => 1,
                        'server_id' => 0
                    ]);
                    if($i == 1) {
                        db()->update(':ynccomment_sticker_set',['thumbnail_id' => $iId],'set_id='.$aSet['set_id']);
                    }
                }
            }
        }
    }
}
