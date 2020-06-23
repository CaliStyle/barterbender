<!--Begin Block letter-->
		<div id="js_petition_block_letter" class="js_petition_block page_section_menu_holder" style="display:none;">
			<div class="table form-group">
				<div class="table_left">
					{phrase var='petition.subject'}:
				</div>
				<div class="table_right label_hover">
					<input class="form-control" type="text" name="val[letter_subject]" value="{value type='input' id='letter_subject'}" id="letter_subject" size="60"  />
				</div>
			</div>
			<div class="table form-group">
				<div class="table_left">
					{phrase var='petition.message'}:
				</div>
				<div class="table_right label_hover">
					{editor id="letter"}
				</div>
			</div>
			{if !empty($aForms.target_email)}
			<div class="table form-group">
				<div class="table_left">
					{phrase var='petition.send_petition_letter_online'}
				</div>
				<div class="table_right">
					<div class="item_is_active_holder">
						<span class="js_item_active item_is_not_active"><input type="radio" name="val[is_send_online]" value="0" class="checkbox" style="vertical-align:middle;"{value type='checkbox' id='is_send_online' default='0' selected=true}/> {phrase var='petition.no'}</span>
						<span class="js_item_active item_is_active"><input type="radio" name="val[is_send_online]" value="1" class="checkbox" style="vertical-align:middle;"{value type='checkbox' id='is_send_online' default='1'}/> {phrase var='petition.yes'}</span>
					</div>
				</div>
			</div>
			{/if}
			<div class="table_clear">
				<ul class="table_clear_button">
					<li><input type="submit"
							   onclick="ynpetition_submit();"
							   name="val[submit_letter]"
							   value="{phrase var='petition.save'}"
							   class="btn btn-primary btn-sm"/></li>
				</ul>
				<div class="clear"></div>
			</div>
		</div>
		<!--End Block letter-->