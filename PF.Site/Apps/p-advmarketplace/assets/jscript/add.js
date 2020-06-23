
$Behavior.advancedmarketplaceAdd = function()
{
    var $categorySelector = $('#p-advmarketplace-categories');
    if (!$categorySelector.length) {
        return;
    }
    $categorySelector.change(function()
	{
		var $this = $(this);
		var iCatId = $this.val();
		$.ajaxCall('advancedmarketplace.frontend_loadCustomFields', 'catid='+iCatId+'&lid='+$("#ilistingid").val());
	});
    $categorySelector.change();
};

$Core.advancedmarketplace =
    {
        sUrl: '',

        url: function (sUrl) {
            this.sUrl = sUrl;
        },

        action: function (oObj, sAction) {
            aParams = $.getParams(oObj.href);

            $('.dropContent').hide();

            switch (sAction) {
                case 'edit':
                    window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
                    break;
                case 'delete':
                    var url = this.sUrl;
                    $Core.jsConfirm({}, function () {
                        window.location.href = url + 'delete_' + aParams['id'] + '/';
                    }, function () {
                    });
                    break;
                default:

                    break;
            }

            return false;
        },

        dropzoneOnSending: function (data, xhr, formData) {
            $('#js_advancedmarketplace_form').find('input[type="hidden"]').each(function () {
                formData.append($(this).prop('name'), $(this).val());
            });
        },

        dropzoneOnSuccess: function (ele, file, response) {
            $Core.advancedmarketplace.processResponse(ele, file, response);
        },

        dropzoneOnError: function (ele, file) {

        },
        dropzoneQueueComplete: function () {
            $('#js_listing_done_upload').show();
        },
        processResponse: function (t, file, response) {
            // response = JSON.parse(response);
            if (typeof response.id !== 'undefined') {
                file.item_id = response.id;
                if (typeof t.data('submit-button') !== 'undefined') {
                    var ids = '';
                    if (typeof $(t.data('submit-button')).data('ids') !== 'undefined') {
                        ids = $(t.data('submit-button')).data('ids');
                    }
                    $(t.data('submit-button')).data('ids', ids + ',' + response.id);
                }
            }
            // show error message
            if (typeof response.errors != 'undefined') {
                for (var i in response.errors) {
                    if (response.errors[i]) {
                        $Core.dropzone.setFileError('advancedmarketplace', file, response.errors[i]);
                        return;
                    }
                }
            }
            return file.previewElement.classList.add('dz-success');
        },
        toggleCreatingUploadMorePhotos: function() {
            $('#p_advmarketplace_back_to_manage_container').show();
            $('#p_advmarketplace_confirm_photo').html(oTranslations['finish_photo_uploading']);
        },
        toggleCreatingBackToManagePhotos: function() {
            $Core.dropzone.instance['advancedmarketplace'].files = [];
            $('#p_advmarketplace_back_to_manage_container').hide();
            $('#p_advmarketplace_confirm_photo').html(oTranslations['next']);
        },
        toggleUploadSection: function (id, show_upload, is_creating) {
            var parent = $('#js-p-advmarketplace-photos-container');
            parent.html('<div class="js_loading_form text-center "><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>');
            $.ajaxCall('advancedmarketplace.toggleUploadSection', 'show_upload=' + show_upload + '&id=' + id + '&is_creating=' + is_creating);
        },
        deleteImage: function(image_id, listing_id) {
            $Core.jsConfirm({message:oTranslations['are_you_sure']},
                function(){
                    $('#js_photo_holder_' + image_id).remove();
                    $.ajaxCall('advancedmarketplace.deleteImage', 'id=' + image_id + '&listing_id=' + listing_id);
                    $('#js_mp_image_' + image_id).remove(); },
                function(){});

            return false;
        }
    };