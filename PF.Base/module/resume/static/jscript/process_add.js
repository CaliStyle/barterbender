$Behavior.check_experience = function() {
	$('input[id="check_experience"]').unbind().click(function(){
		if(this.checked==true)
			$('.end_experience').hide();
		else
			$('.end_experience').show();
	});
}

function removeElement($this)
{
	$this.parent().parent().remove();
	var listOfElement = '';
	length = $('.chzn-choices').length;
	for(var i=0;i<length;i++)
	{
		row = $('.chzn-choices')[i];
		listOfElement = listOfElement + $(row).find('span').html();
		if(i<length-1)
			listOfElement += ",";
	}
	$('#element_list').val(listOfElement);
}

$Behavior.addMoreElement = function(){
	$('#add_more_element').bind('click',function(){
	var element_name = $('#element_name').val().trim();
	if(element_name!="")
	{
		var current_text = $('#element_list').val();
		var comma = ',';
		if(current_text=="")
			comma = '';
		$('#element_list').val($.trim(current_text) + comma + $.trim(element_name));
		$('#element_name').val('');
		
		var new_element = '<ul class="chzn-choices"><li id="selEEW_chzn_c_1" class="search-choice"><span>'+element_name+'</span><a rel="1" class="search-choice-close closeskill" href="javascript:void(0)" onclick="removeElement($(this));return false;" ></a></li></ul>';
		$('.textareaselect').find('.tablecontent').append(new_element);
	}});
}

$Behavior.publicationOtherType = function()
{
	$('#publication_type').bind('change', function()
	{
		var iValue = parseInt($(this).val());
		if(iValue == 0)
		{
			$('#other_type').show();
		}
		else
		{
			$('#other_type').val("");
			$('#other_type').hide();
		}
	});	
}