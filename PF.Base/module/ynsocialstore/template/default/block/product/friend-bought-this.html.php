<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/8/16
 * Time: 9:06 AM
 */
?>

<div class="ynstore-product-friendbought clearfix">
	{foreach from = $aFriends item = aUser}
	<div class="ynstore-product-friendbought-item">
        {img user=$aUser suffix='_120_square'}

	    <?php echo '<span class="" id="js_user_name_link_' . $this->_aVars['aUser']['user_name'] . '"><a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile', array($this->_aVars['aUser']['user_name'], ((empty($this->_aVars['aUser']['user_name']) && isset($this->_aVars['aUser']['profile_page_id'])) ? $this->_aVars['aUser']['profile_page_id'] : null))) . '">' . Phpfox::getLib('phpfox.parse.output')->shorten(Phpfox::getService('user')->getCurrentName($this->_aVars['aUser']['user_id'], $this->_aVars['aUser']['full_name']), Phpfox::getParam('user.maximum_length_for_full_name')) . '</a></span>'; ?>
	</div>
	{/foreach}
</div>
