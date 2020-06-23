<?php 
/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Socialad
 * @version        3.01
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_menu_drop_down" style="display:none;">
	<div class="link_menu dropContent" style="display:block;">
		<ul>
			<li><a href="#" onclick="return $Core.socialad.action(this, 'edit');">{_p var='edit'}</a></li>
			<li><a href="#" onclick="return $Core.socialad.action(this, 'delete');">{_p var='delete'}</a></li>
		</ul>
	</div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='faqs'}
        </div>
    </div>
        <form method="post" action="{url link='admincp.socialad.faq'}">
            <div class="panel-body">
                <div class="table">
                    <div class="sortable">
                        {$sFaqs}
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <input type="submit" value="{_p var='update_order'}" class="btn btn-primary" />
            </div>
        </form>
</div>
{literal}
<script type="text/javascript">
    $Behavior.initFaqs = function () {
        ynsocialad_init.init_dropdown();
        ynsocialad_init.init_sortable();
    }
</script>
{/literal}