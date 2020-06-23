<?php
/*
* @copyright        [YouNet_COPYRIGHT]
* @author           YouNet Company
* @package          Module_FeedBack
* @version          2.01
*
*/
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynf_desc">{_p var='below_some_valuable_statistics_for_feedbacks_submitted_on_your_site'}</div>

<ul class="ynf_statictis">
	<li>
		<span>{_p var='total_feedbacks'}</span>
		<span>{$total_feedbacks}</span>
	</li>

	<li>
		<span>{_p var='total_public_feedbacks'}</span>
		<span>{$total_public_feedbacks}</span>
	</li>

	<li>
		<span>{_p var='total_private_feedbacks'}</span>
		<span>{$total_private_feedbacks}</span>
	</li>

	<li>
		<span class="pr-1">{_p var='total_anonymous_feedbacks'}</span>
		<span>{$total_anonymous_feedbacks}</span>
	</li>

	<li>
		<span>{phrase var='feedback_total_comments'}</span>
		<span>{$total_comments}</span>
	</li>

	<li>
		<span>{_p var='total_votes'}</span>
		<span>{$total_votes}</span>
	</li>
</ul>