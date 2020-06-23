<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class gettingstarted_component_block_articledetail extends Phpfox_Component{
	public function process()
	{
		$article_id=$this->request()->get('article');
		$dsarticle = Phpfox::getService('gettingstarted.articlecategory')->getArticleById($article_id);
		$time_stamp = Phpfox::getTime(Phpfox::getParam('gettingstarted.display_time_stamp'), $dsarticle ['time_stamp'] );
		if($dsarticle['article_category_id'] == -1)
		{
			$dsarticle['article_category_name'] = _p('gettingstarted.uncategorized');
		}
        $dsarticle['url'] = $this->url()->permalink(array('gettingstarted.categories', 'view' => $this->request()->get('view')), $dsarticle['article_category_id'], $dsarticle['article_category_name']);
		$dsarticle['bookmark'] = $this->url()->permalink('gettingstarted.article', 'article_' . $article_id);
		$user = Phpfox::getLib('database')->select('u.user_name,u.full_name')
											->from(phpfox::getT('user'), 'u')
											->where('u.user_id = '.$dsarticle['user_id'])
											->execute('getSlaveRow');
		$path_user = phpfox::getLib('url')->makeUrl($user['user_name']);
		$total_comments = $dsarticle['total_comment'];
		$this->template()->assign(array(
           'dsarticle' => $dsarticle,
			'time_stamp'=>$time_stamp,
			'path_user'=>$path_user,
			'username'=>$user['full_name'],
			'total_comments' => $total_comments,
			'public_id_addthis'=> setting('core.addthis_pub_id', ''),
            'bShowAddThisSection' => setting('core.show_addthis_section', false)
		));
		return 'block';
	}
}
?>