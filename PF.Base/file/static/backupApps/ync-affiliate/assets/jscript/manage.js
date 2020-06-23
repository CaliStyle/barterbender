/**
 * Created by dai on 20/01/2017.
 */

var yncaffiliate =
{
    deleteAffiliate : function(id)
    {
        $.ajaxCall('yncaffiliate.deleteAffiliate', 'iAffiliateId=' + id);
        return false;
    },

    actionMultiSelect : function(obj)
    {
        $.ajaxCall('yncaffiliate.actionMultiSelectAffiliate',$(obj).serialize(),'post');
        return false;
    },

    checkAllAffiliate : function()
    {
        var checked = document.getElementById('ync_affiliates_list_check_all').checked;
        $('.affiliate_row_checkbox').each(function(index,element)
        {
            element.checked = checked;
            var sIdName = '#ync_affiliates_' + element.value;
            if (element.checked == true) {
                $(sIdName).css({
                    'backgroundColor' : '#FFFF88'
                });
            }
            else {
                if(element.value % 2 == 0)
                {
                    $(sIdName).css({
                        'backgroundColor' : '#F0f0f0'
                    });
                }
                else{
                    $(sIdName).css({
                        'backgroundColor' : '#F9F9F9'
                    });
                }
            }
        });

        yncaffiliate.setButtonStatus(checked);

        return checked;
    },

    setButtonStatus: function(status)
    {
        if (status)
        {
            $('.delete_selected').removeClass('disabled');
            $('.delete_selected').attr('disabled', false);
            $('.approve_selected').removeClass('disabled');
            $('.approve_selected').attr('disabled', false);
            $('.deny_selected').removeClass('disabled');
            $('.deny_selected').attr('disabled', false);
        }
        else
        {
            $('.delete_selected').addClass('disabled');
            $('.delete_selected').attr('disabled', true);
            $('.approve_selected').addClass('disabled');
            $('.approve_selected').attr('disabled', true);
            $('.deny_selected').addClass('disabled');
            $('.deny_selected').attr('disabled', true);
        }
    },

    checkDisableStatus: function()
    {
        var status = false;
        $('.affiliate_row_checkbox').each(function(index, element)
        {
            var sIdName = '#ync_affiliate_' + element.value;
            if (element.checked == true)
            {
                status = true;
                $(sIdName).css({
                    'backgroundColor' : '#FFFF88'
                });
            }
            else {
                if(element.value % 2 == 0)
                {
                    $(sIdName).css({
                        'backgroundColor' : '#F0f0f0'
                    });
                }
                else{
                    $(sIdName).css({
                        'backgroundColor' : '#F9F9F9'
                    });
                }
            }
        });

        yncaffiliate.setButtonStatus(status);

        return status;
    },

    switchAction : function (sType)
    {
        switch(sType){
            case 'delete':
                $("#ynaf_multi_select_action").val('1');
                break;
            case 'approve':
                $("#ynaf_multi_select_action").val('2');
                break;
            case 'deny':
                $("#ynaf_multi_select_action").val('3');
                break;
        }
    },

    getCommissionRuleByUserGroup : function(iUserGroupId)
    {
        $.ajaxCall('yncaffiliate.getCommissionRuleByUserGroup', 'iUserGroupId=' + iUserGroupId);
        return false;
    },
}
