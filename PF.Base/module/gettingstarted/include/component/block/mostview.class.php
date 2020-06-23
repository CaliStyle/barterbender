<?php

//define('PHPFOX') or die('NO DICE!');
?>
<?php

class Gettingstarted_Component_Block_MostView extends PhpFox_Component {

    public function process() {
        $iLimit = 10;
		if(PHPFOX::getParam('gettingstarted.number_of_limit_most_view_articles')>0)
        {
           $iLimit = PHPFOX::getParam('gettingstarted.number_of_limit_most_view_articles');
        }
        //get top most view articles by total view DESC
        $article_block = Phpfox::getService("gettingstarted")->getArticleMostView($iLimit);
        if (count($article_block) < 1) {
            return false;
        }

        //get viewing article id to active
        if ($this->request()->get('article')) {
            $art = $this->request()->getInt('article');
        } else {
            $art = -2;
        }
        $this->template()->assign(array(
            'sHeader' => _p('gettingstarted.most_view'),
            'art' => $art,
            'article_block' => $article_block,
        ));


        return 'block';
    }

}
?>

