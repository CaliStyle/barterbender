
$Behavior.ynpetitionfixsearch = function(){

	//console.log("test channel.js");
	if (window.jQuery)
	{
		if ($('#page_petition_index').length)
		{
			$('body').addClass("ynPetition");

			$( ".action_drop a" ).each(function( index ) {
				//console.log( index + ": " + $( this ).text() );
				var href =  $( this ).attr("href");
				//console.log(href);

				href = href.replace("view=/?s","view=&s");
				href = href.replace("view=my/?","view=my&");
				href = href.replace("view=friend/?","view=friend&");
				href = href.replace("view=favorite/?","view=favorite&");
				href = href.replace("view=featured/?","view=featured&");
				href = href.replace("view=pending/?","view=pending&");

				href = href.replace("sort=most-talked/?","sort=most-talked&");
				href = href.replace("sort=most-viewed/?","sort=most-viewed&");

				href = href.replace("sort=most-signed/?","sort=most-signed&");
				href = href.replace("sort=most-liked/?","sort=most-liked&");
				href = href.replace("sort=most-popular/?","sort=most-popular&");
				
				href = href.replace("listing/?s","listing&s"); 

				

				href = href.replace("view=all_channels/?","view=all_channels&");
				href = href.replace("view=channels/?","view=channels&");

				href = href.replace("3/?s","3&");
				href = href.replace("9/?s","9&");
				href = href.replace("12/?s","12&");
				href = href.replace("15/?s","15&");
				href = href.replace("1/?s","1&");

				$( this ).attr("href",href);
			});
		}
	}
}
function ynpetition_submit()
{
//	alert("hello");
	$('#js_petition_block_letter').trigger("submit");
}
function ynpetition_selectall_friend() {
$('input.checkbox').attr('checked', 'checked');
$('.friend_search_holder').each(function(  ) {

	if ($(this).hasClass("friend_search_active"))
	{
		//$(this).trigger("not click");

	}
	else
	{
		$(".checkbox",$(this)).trigger("click");
		$(this).trigger("click");

	}
});
}


function ynpetition_unselectall_friend() {
	$('input.checkbox').attr('checked', 'checked');


	$('input.checkbox').each(function(i,e) {
		if($(e).attr('checked') == 'checked') {
			$(e).attr('checked', false);
			$('.friend_search_holder').removeClass('friend_search_active');
			addFriendToSelectList(e, $(e).val(), false);
		}
	});



	$('.friend_search_holder .friend_search_active').each(function(  ) {

		if ($(this).hasClass("friend_search_active"))
		{
			//$(this).trigger("not click");

		}
		else
		{
			var e = $(".checkbox",$(this));
			if($(e).attr('checked') == 'checked') {
				$(e).attr('checked', false);
				$('.friend_search_holder').removeClass('friend_search_active');

			}



		}
	});
}

