<?php
class Gettingstarted_Component_Block_Article_Feed extends Phpfox_Component {
    public function process()
    {
        if (($feedId = $this->getParam('this_feed_id')) && $param = $this->getParam('custom_param_gettingstarted_article_' . $feedId)) {
            $article = $param['article'];
            $this->template()->assign([
                'article' => $article,
                'link' => Phpfox::getLib('url')->makeUrl('gettingstarted.article', ['article' => $article['article_id']]),
            ]);
        }
        return 'block';
    }
}
