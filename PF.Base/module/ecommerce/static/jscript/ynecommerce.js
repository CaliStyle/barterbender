;

var ynecommerce = {
	pt : []
    , params : false
	, setParams : function(params) {
		ynecommerce.params = JSON.parse(params);
	}
	, init: function()
	{
	}
    , alertMessage: function(message, width){
		if(undefined == width || null == width){
			width = '300px';
		}
		$.magnificPopup.open({
		  items: {
		    src: '<div class="white-popup-block" style="width: ' + width + ';">' + message + '</div>',
		    type: 'inline'
		  }
		});
	}
    , confirmCancelRequest: function(iRequestId){
		$Core.jsConfirm({message: oTranslations['ecommerce.are_you_sure_you_want_to_cancel_this_request']}, function(){
            ynecommerce.cancelRequest(iRequestId);
		});
	}
    , cancelRequest: function(iRequestId){
        $.ajaxCall('ecommerce.cancelRequest', 'id=' + iRequestId);
    }
    , advSearchDisplay: function(title_search)
    {
		var $form = $('#ynecommerce_adv_search');
		var $flag = $('#form_flag');
        
		if($flag.val() == 1)
		{
			$form.hide();
			$flag.val(0); 
		}
		else
		{
			$form.show();
			$flag.val(1);
		}        

		return false; 
    }
    , initAdvancedSearch: function(){
        $('.js_mp_category_list').change(function()
        {
            var iParentId = parseInt(this.id.replace('js_mp_id_', ''));

            $('.js_mp_category_list').each(function()
            {
                if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
                {
                    $('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();				

                    this.value = '';
                }
            });

            $('#js_mp_holder_' + $(this).val()).show();
        });	
    }, initYnEcommerceMyCartUpdateQuantity: function()
    {
    	var total_money = 0;
	  	  $('.mycart_quantity_product').each(function(idx){
				total_money += parseFloat($(this).val()) * parseFloat($(this).parent().find('.mycart_price_product').val());
	  			
				/*update total money on seller*/
		  		total_seller_money = 0;
		  		seller_id = $(this).parent().find('.mycart_seller').val();
		  		$('.mycart_seller_'+seller_id).each(function(idx){
					total_seller_money += parseFloat($(this).parent().find('.mycart_price_product').val()) * parseFloat($(this).parent().find('.mycart_quantity_product').val());
		  		});
		  		$('#ynecommerce_mycart #mycart_total_seller_'+seller_id).text(total_seller_money.toFixed(2));

	  		});
	  	  /*update total money*/
	  	  $('#ynecommerce_mycart #mycart_total').text(total_money.toFixed(2));

		  $('.mycart_quantity_product').change(function(){
		  		if($(this).val() <= 0 || $(this).val() == '' ){
		  			$(this).val('1');
		  		}
			  	productID = $(this).parent().find('.mycart_productid').val();
			  	symbol = $(this).parent().find('.mycart_symbol').val();
			  	$('.item_price_' + productID).text(symbol + (parseFloat($(this).val()) * parseFloat($(this).parent().find('.mycart_price_product').val())).toFixed(2));
		  		/*update total money*/
				total_money = 0;
		  		$('.mycart_quantity_product').each(function(idx){
					total_money += parseFloat($(this).val()) * parseFloat($(this).parent().find('.mycart_price_product').val());
		  		});
		  		$('#ynecommerce_mycart #mycart_total').text(total_money.toFixed(2));

				/*update total money on seller*/
		  		total_seller_money = 0;
		  		seller_id = $(this).parent().find('.mycart_seller').val();
		  		$('.mycart_seller_'+seller_id).each(function(idx){
					total_seller_money += parseFloat($(this).parent().find('.mycart_price_product').val()) * parseFloat($(this).parent().find('.mycart_quantity_product').val());
		  		});
		  		$('#ynecommerce_mycart #mycart_total_seller_'+seller_id).text(total_seller_money.toFixed(2));

		  		/*send ajax to update db*/
		  		iCartId = $('#ynecommerce_cartid').val();
		  		iProductId = parseFloat($(this).parent().find('.mycart_productid').val());
		  		iQuantity = parseFloat($(this).parent().find('.mycart_quantity_product').val());
		  		$.ajaxCall('ecommerce.updateQuantityProduct', 'iCartId=' + iCartId +'&iProductId=' + iProductId+'&iQuantity=' + iQuantity);

		  });  
    }, initYnEcommerceCheckoutUpdateQuantity: function()
    {
    	$('#ynecommerce_checkout_form .contact_item').first().addClass('selected');
         $('#ynecommerce_checkout_form .contact_list input:radio[name="val[selected_address]"]').bind('click',function(){
                    $('#ynecommerce_checkout_form .contact_item').removeClass('selected');
                    $(this).parent().parent().parent().addClass('selected');
         });
		  total_money = 0;
	  	  $('.checkout_quantity_product').each(function(idx){
				total_money += parseFloat($(this).val()) * parseFloat($(this).parent().find('.checkout_price_product').val());
	  			
				/*update total money on seller*/
		  		total_seller_money = 0;
		  		seller_id = $(this).parent().find('.checkout_seller').val();
		  		$('.checkout_seller_'+seller_id).each(function(idx){
					total_seller_money += parseFloat($(this).parent().find('.checkout_price_product').val()) * parseFloat($(this).parent().find('.checkout_quantity_product').val());
		  		});
		  		$('#ynecommerce_checkout #checkout_total_seller_'+seller_id).text(total_seller_money.toFixed(2));

	  		});
	  	  /*update total money*/
	  	  $('#ynecommerce_checkout #checkout_total').text(total_money.toFixed(2));

		  $('.checkout_quantity_product').change(function(){
		  		if($(this).val() <= 0 || $(this).val() == '' ){
		  			$(this).val('1');
		  		}
			    productID = $(this).parent().find('.checkout_product_id').val();
			    symbol = $(this).parent().find('.checkout_symbol').val();
			    $('.item_price_' + productID).text(symbol + (parseFloat($(this).val()) * parseFloat($(this).parent().find('.checkout_price_product').val())).toFixed(2));
		  		/*update total money*/
				total_money = 0;
		  		$('.checkout_quantity_product').each(function(idx){
					total_money += parseFloat($(this).val()) * parseFloat($(this).parent().find('.checkout_price_product').val());
		  		});
		  		$('#ynecommerce_checkout #checkout_total').text(total_money.toFixed(2));

				/*update total money on seller*/
		  		total_seller_money = 0;
		  		seller_id = $(this).parent().find('.checkout_seller').val();
		  		$('.checkout_seller_'+seller_id).each(function(idx){
					total_seller_money += parseFloat($(this).parent().find('.checkout_price_product').val()) * parseFloat($(this).parent().find('.checkout_quantity_product').val());
		  		});
		  		$('#ynecommerce_checkout #checkout_total_seller_'+seller_id).text(total_seller_money.toFixed(2));

		  		/*send ajax to update db*/
		  		iCartId = $('#ynecommerce_cartid').val();
		  		iProductId = parseFloat($(this).parent().find('.checkout_product_id').val());
		  		iQuantity = parseFloat($(this).parent().find('.checkout_quantity_product').val());
		  		$.ajaxCall('ecommerce.updateQuantityProduct', 'iCartId=' + iCartId +'&iProductId=' + iProductId+'&iQuantity=' + iQuantity);
		  		
		  });   
    }
};