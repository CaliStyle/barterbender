function log(obj) {
	if(console)console.log(obj);
}
$Core.advancedmarketplace =
{
	sUrl: '',

	url: function(sUrl)
	{
		this.sUrl = sUrl;
	},

	action: function(oObj, sAction, id)
	{
		$('.link_menu').hide();
		tb_show(oTranslations['advancedmarketplace.admin_menu_manage_custom_fields'], $.ajaxBox('advancedmarketplace.showmanagecustomfieldpopup', 'height=230&width=800&lid=' + id));
		return false;
	}
}

$(function()
{
	$('.sortable ul').sortable({
			axis: 'y',
			update: function(element, ui)
			{
				var iCnt = 0;
				$('.js_mp_order').each(function()
				{
					iCnt++;
					this.value = iCnt;
				});
			},
			opacity: 0.4
		}
	);

	$('.js_drop_down').click(function()
	{
		eleOffset = $(this).offset();

		aParams = $.getParams(this.href);

		$('#js_cache_menu').remove();

		$('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100;">' + $('#js_menu_drop_down').html() + '</div>');

		$('#js_cache_menu .link_menu li a').each(function()
		{
			this.href = '#?id=' + aParams['id'];
		});

		$('.dropContent').show();

		$('.dropContent').mouseover(function()
		{
			$('.dropContent').show();

			return false;
		});

		$('.dropContent').mouseout(function()
		{
			$('.dropContent').hide();
			$('.sJsDropMenu').removeClass('is_already_open');
		});

		return false;
	});

});

var _bh = {
	ii : [],
	cf : function() {
	},
	push : function(a, b, ck) {
		_bh.ii.push( {
			id : a,
			is_active : b,
            check_btn : ck

		});
	},
	pop : function() {
		var o = _bh.ii.pop();
		if (o != null)
			updateFeature(o.id, o.is_active, o.check_btn);
	}
};

var _ch = {
	ii : [],
	cf : function() {
	},
	push : function(a, b) {
		_bh.ii.push( {
			id : a,
			is_active : b
		});
	},
	pop : function() {
		var o = _bh.ii.pop();
		if (o != null)
			updateSponsor(o.id, o.is_active);
	}
};
function selectAll() {
	var check = document.getElementsByName('is_selected');
	var is_select = document.getElementById('checkAll');
	var count = check.length;
	for ( var i = 0; i < count; i++) {
		check[i].checked = is_select.checked;
	}
}

function featureSelected(ck)
{
	var selected = new Array();
	var check = document.getElementsByName('is_selected');
	var count = check.length;
	var is_selected = false;
	for(var i = count - 1; i >= 0; i--)
	{
		if(check[i].checked == true)
		{
			var is_active = document.getElementById('is_selected_active_' + check[i].value).value;
            if(is_active == 0 && ck == 0) {
                _bh.push(check[i].value, is_active, ck);
                is_selected = true;
            }
            if(is_active == 1 && ck == 1) {
                _bh.push(check[i].value, is_active, ck);
                is_selected = true;
            }

		}
	}
	if(is_selected == true)
	{
		_bh.pop();
	}

}
function updateFeature(listing_id, is_active, check_btn)
{
	if(is_active == 0 && check_btn == 0)
	{
		$('#js_listing_is_feature_' + listing_id).hide();
        $.ajaxCall('advancedmarketplace.featureSelected', 'listing_id='+listing_id+'&type='+is_active);
	}
    if(is_active == 1 && check_btn == 1)
    {
		$('#js_listing_is_un_feature_' + listing_id).hide();
        $.ajaxCall('advancedmarketplace.unfeatureSelected', 'listing_id='+listing_id+'&type='+is_active);
	}

}



function sponsorSelected()
{
	
	var selected = new Array();
	var check = document.getElementsByName('is_selected');
	var count = check.length;
	var is_selected = false;
	for(var i = count - 1; i >= 0; i--)
	{
		if(check[i].checked == true)
		{
			var is_active = document.getElementById('is_sponsor_selected_active_' + check[i].value).value;
			if(is_active == 0)
			{
				_ch.push(check[i].value, is_active);
				is_selected = true;
			}
			
		}
	}
	if(is_selected == true)
	{
		_ch.pop();
	}
}

function updateSponsor(listing_id, is_active)
{
	if(is_active == 0)
	{
		$('#js_listing_is_sponsor_' + listing_id).hide();
	}
	else
	{
		$('#js_listing_is_un_sponsor_' + listing_id).hide();
	}
	$.ajaxCall('advancedmarketplace.sponsorSelected', 'listing_id='+listing_id+'&type='+is_active);
}
/* nhanlt */
function processCustomGroupSample(sample){
	var $sample = $(sample);

	setGroupInterfaceActions($sample);

	$("#yn_jh_anchor_addcf").before(
		$sample
	);
}

/* nhanlt */

function setGroupInterfaceActions(obj) {
	obj.find(".yn_jh_cusgroup_save").click(function(evt){
		evt.preventDefault();
		var $this = $(this);
		var $input = $this.parent().parent().find(".value");

		$.ajaxCall("advancedmarketplace.editCustomFieldGroup", "cusfgroupid=" + $this.attr("ref") + "&value=" + $input.val());

		obj.find(".ajxloader").show();
		obj.find(".yn_jh_cusgroup_save").hide();
		obj.find(".yn_jh_cusgroup_edit").show();

		return false;
	});

	obj.find(".yn_jh_cusgroup_edit").click(function(evt){
		evt.preventDefault();

		/* obj.find("input.value").removeAttr("disabled"); */
		obj.find("input.value").removeClass("changed");
		obj.find(".yn_jh_cusgroup_save").css({
			"display": "inline-block"
		});
		$(this).hide();

		return false;
	});

	obj.find(".yn_jh_cusgroup_delete").click(function(evt){
		evt.preventDefault();
        var $this = $(this);
		$Core.jsConfirm({}, function() {
		  obj.find(".ajxloader").show();
		  $.ajaxCall("advancedmarketplace.deleteCustomFieldGroup", "cusfgroupid=" + $this.attr("ref"));

		  $cowner = $("#yn_jh_groupcustomfields").find(".customfield-owner");
		  if($cowner.size() > 0) {
			if($cowner.val() == $this.attr("ref")){
			  $cowner.parent().empty();
			}
		  }
    }, function() {});
		
        return false;
	});

	obj.find("input.value").change(function(evt){
		var $this = $(this);
		obj.find(".yn_jh_cusgroup_edit").hide();
		obj.find(".yn_jh_cusgroup_save").css({
			"display": "inline-block"
		});
		$this.addClass("changed");
	}).click(function(evt){
		var $this = $(this);
		obj.find(".ajxloader").show();
		$.ajaxCall("advancedmarketplace.loadCustomFields", "cusfgroupid=" + $this.attr("ref"));
	}).keydown(function(){
		$(this).change();
	});

	obj.find(".onoffswitch").click(function(evt){
		var $this = $(this);
		obj.find(".ajxloader").show();
		$.ajaxCall("advancedmarketplace.setSwitchOnOffCustomFieldGroup", "cusfgroupid=" + $this.attr("ref"));
	});

	obj.find(".up").click(function(evt){
		evt.preventDefault();

		var $this = $(this);
		var $parent = $this.parents("li.customrow").first();
		var $form = $("#jh_yn_cusfield_submitform");

		$prev = $parent.prev("li.customrow");
		if($prev.size() > 0) {
			$prev.before($parent);
		}
		$form.ajaxCall("advancedmarketplace.updateCustomGroupOrder");

		return false;
	});

	obj.find(".down").click(function(evt){
		evt.preventDefault();

		var $this = $(this);
		var $parent = $this.parents("li.customrow").first();
		var $form = $("#jh_yn_cusfield_submitform");

		$next = $parent.next("li.customrow");
		if($next.size() > 0) {
			$next.after($parent);
		}
		$form.ajaxCall("advancedmarketplace.updateCustomGroupOrder");

		return false;
	});
}

/* nhanlt */
function processSwitchFieldGroupStatus(ref, state) {

	var $obj = $("#yn_jh_customgroup_listing").find("a.onoffswitch[ref=" + ref + "]");
	$obj.removeClass("on");
	$obj.removeClass("off");
	$obj.addClass(state=="1"?"on":"off");
}

/* nhanlt */
function processCustomGroupFieldSample(sample){
	var $sample = $(sample);

	setCustomGroupFieldInterfaceActions($sample);

	$("#yn_jh_groupcustomfields").html(
		$sample
	);

	if($sample.find(".anoption").size() > 0){
		$sample.find(".sub_tags").show();
	}
}

/* nhanlt */
function setCustomGroupFieldInterfaceActions(obj){
	obj.find(".yn_jh_addcustomfield").click(function(evt){
		evt.preventDefault();

		var $this = $(this);
		$.ajaxCall("advancedmarketplace.addCustomField", "cusfgroupid=" + $this.attr("ref"));
		$this.parent().find(".ajxloader").show();
		$(".yn_jh_saveall").show();

		return false;
	});
	obj.find(".yn_jh_saveall").click(function(evt){
		evt.preventDefault();

		var $this = $(this);
		var $form = $("#jh_yn_cusfield_submitform");
		$form.ajaxCall("advancedmarketplace.saveAllCustomField");
		$this.parent().find(".ajxloader").show();

		return false;
	});
}

/* nhanlt */
function processCustomFieldSample(sample){
	var $sample = $(sample);

	setCustomFieldInterfaceActions($sample);
	$("#yn_jh_customfieldrow_anchor").before(
		$sample
	);
}

/* nhanlt */
function setCustomFieldInterfaceActions(obj){
	obj.find(".field_type").change(function(){
		var $this = $(this);
		processCustomFieldSubtags(obj, $this);
	});
	obj.find(".field_type").change();
	obj.find(".add_option").click(function(evt){
		evt.preventDefault();

		var $this = $(this);
		$.ajaxCall("advancedmarketplace.addOption", "cusfieldid=" + $this.attr("ref") + "&field_type=" + obj.find(".field_type").val());
		$this.parent().find(".ajxloader").show();

		return false;
	});
	obj.find(".onoffswitch").click(function(evt){
		evt.preventDefault();

		var $this = $(this);
		obj.find(".fieldactive").val((obj.find(".fieldactive").val() == 1) ? 0 : 1)
		if($this.hasClass("on")) {
			$this.removeClass("on").addClass("off");
		} else {
			$this.removeClass("off").addClass("on");
		}

		return false;
	});
	obj.find(".moveup").click(function(evt){
		evt.preventDefault();

		var $this = $(this);
		$parent = $this.parents(".yn_jh_customfield").first();

		$prev = $parent.prev(".yn_jh_customfield");
		if($prev.size() > 0) {
			$prev.before($parent);
		}

		return false;
	});
	obj.find(".movedown").click(function(evt){
		evt.preventDefault();

		var $this = $(this);
		$parent = $this.parents(".yn_jh_customfield").first();

		$next = $parent.next(".yn_jh_customfield");
		if($next.size() > 0) {
			$next.after($parent);
		}

		return false;
	});
	obj.find(".btndelete").click(function(evt){
        evt.preventDefault();
        var $this = $(this);
        $Core.jsConfirm({}, function () {
            $this.parents(".yn_jh_customfield").remove();
            $.ajaxCall("advancedmarketplace.removeCustomField", "cusfieldalias=" + $this.data("custom-id"));
            if ($("#yn_jh_groupcustomfields").find(".yn_jh_customfield").size() <= 0) {
                $(".yn_jh_saveall").hide();
      		}
    }, function() {});

		return false;
	});
}

/* nhanlt */
function getSubTagHTMLGenCode(array_prefix){
	var row = $("<div>");
	var label = $("<span>");
	var input = $("<input />");

	input.attr({
		"type": "text"
	});

	row.append(label).append(input);

	return row;
}

/* nhanlt */
function processCustomFieldSubtags(obj, owner) {
	if(owner.val() && fieldInfors[owner.val()] && fieldInfors[owner.val()]["sub_tags"] != "") {
		obj.find(".sub_tags").show();
		obj.find(".add_option").show();
	} else {
		obj.find(".sub_tags").hide();
		obj.find(".add_option").hide();
	}
}

/* nhanlt */
function processCustomFieldOptionSample(sample, cid) {
	var $sample = $(sample);
	$(".options_anchor_" + cid).find(".add_option").before($sample);
	$sample.find(".yn_jh_cusfield_delete").click(function(evt){
		evt.preventDefault();

		$Core.jsConfirm({}, function() {
      var $this = $(this);
      $this.parent().remove();
    }, function() {});

		return false;
	});
	$sample.find(".down").click(function(evt){
		evt.preventDefault();

		var $this = $(this);
		var $parent = $this.parent();

		$next = $parent.next(".anoption");
		if($next.size() > 0) {
			$next.after($parent);
		}

		return false;
	});
	$sample.find(".up").click(function(evt){
		evt.preventDefault();

		var $this = $(this);
		var $parent = $this.parent();

		$prev = $parent.prev(".anoption");
		if($prev.size() > 0) {
			$prev.before($parent);
		}

		return false;
	});
}

var is_listing = false;
function setValue() {
	var check = document.getElementsByName('is_selected');
	var count = check.length;
	var arr = "";
	var nopermission = 0;
	var countcheck = 0;
	var is_submit = false;
	for ( var i = count - 1; i >= 0; i--) {
		if (check[i].checked == true) {
			arr += "," + check[i].value;
			countcheck += 1;
		}
	}

	if (countcheck == 1 && check[0].value == 100 && check[0].checked) {
		nopermission = 1;
	}

	document.getElementById('arr_selected').value = arr;
	return is_submit;
}

$(document).on('click', '#today-listing-btnr', function (evt) {
    evt.preventDefault();
    var submit_todaylisting_form = $('#_submit-todaylisting-form');
    var check = false;
    if (!submit_todaylisting_form.find('input[id^="todaylisting-item"]').length) {
        check = false;
    } else {
        check = true;

    }
    if (typeof submit_todaylisting_form === 'undefined') {
        return;
    }

    submit_todaylisting_form.ajaxCall('advancedmarketplace.advMarketTodayListing', 'check=' + check);
    tb_remove();

    return false;
});

$(document).on('click', '.js_advmarketplace_checkbox', function () {
	var check = $('.js_advmarketplace_checkbox');
    var countcheck = check.length;
    var count = check.length;
    for ( var i = count - 1; i >= 0; i--) {
        if (check[i].checked == true) {
            countcheck += 1;
            $('.js_adv_delete_button').removeClass('disabled');
            $('.js_adv_delete_button').removeAttr("disabled");
        }else {
            countcheck -= 1;
		}
    }
    if (countcheck == 0) {
        $('.js_adv_delete_button').addClass('disabled');
        $('.js_adv_delete_button').prop("disabled", true);
    }

});

$(document).on('click', '#checkAll', function () {
    if (this.checked == true) {
        $('.js_adv_delete_button').removeClass('disabled');
        $('.js_adv_delete_button').removeAttr("disabled");
    }else {
        $('.js_adv_delete_button').addClass('disabled');
        $('.js_adv_delete_button').prop("disabled", true);
    }
});

if ($('#js_advmarketplace_checkbox').checked == true) {
    $('.js_adv_delete_button').removeClass('disabled');
    $('.js_adv_delete_button').removeAttr("disabled");
}else {
    $('.js_adv_delete_button').addClass('disabled');
    $('.js_adv_delete_button').prop("disabled", true);
}