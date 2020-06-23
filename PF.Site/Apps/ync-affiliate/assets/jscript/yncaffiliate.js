/**
 * Created by minhhai on 2/15/17.
 */
var yncaffiliate = {
    initRegister : function(){
        if($('#yncaffiliate_register_affiliate_form').length == 0) return;
        yncaffiliate.initValidator($('#yncaffiliate_register_affiliate_form'));
        jQuery.validator.addMethod('checkTerms', function () {
            if($('#yncaffiliate_register_affiliate_form #terms_and_service').is(':checked'))
            {
                return true;
            }
            return false;
        }, oTranslations['you_have_to_agree_with_our_terms_of_service']);
        $('#yncaffiliate_register_affiliate_form #name').rules('add', {
            required: true,
            maxlength: 255
        });
        $('#yncaffiliate_register_affiliate_form #email').rules('add', {
            required: true,
            email: true,
            maxlength: 100
        });
        $('#yncaffiliate_register_affiliate_form #phone').rules('add', {
            required: true,
            maxlength: 100
        });
        $('#yncaffiliate_register_affiliate_form #address').rules('add', {
            required: true,
            maxlength: 200
        });
        $('#yncaffiliate_register_affiliate_form #terms_and_service').rules('add', {
            checkTerms: true,
        });
    },
    initValidator : function(element){
        jQuery.validator.messages.required = oTranslations['this_field_is_required'];
        $.data(element[0], 'validator', null);
        element.validate({
            errorPlacement: function (error, element) {
                if (element.is(":radio") || element.is(":checkbox") || element.is("textarea") || element.is('#ynstore_product_discount_value')) {
                    error.appendTo($(element).closest('.form-group'));
                } else {
                    error.appendTo(element.parent());
                }
            },
            errorClass: 'text-danger',
            errorElement: 'span',
            debug: false
        });
    },
    showTerm : function(){
        tb_show(oTranslations['terms_of_service'],$.ajaxBox('yncaffiliate.getTermAndService'));
        return false;
    }
};

yncaffiliate.initRegister();