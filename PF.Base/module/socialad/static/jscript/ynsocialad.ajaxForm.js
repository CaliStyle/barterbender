;
(function(window, undefined, $) {
	$.fn.extend({
		ajaxForm: function(options) {
			return this.each( // in case we want to init multiple elements
				function() {
					var $this;
					$this = $(this);
					var settings = $.extend({
						'ajax_action' : $this.data('ajax-action'),
						'result_div_id' : $this.data('result-div-id'),
						'custom_event' : $this.data('custom-event'),
						'is_validate' : $this.data('is-validate') === true,
						'is_prevent_submit' : $this.data('is-prevent-submit') === true,
					}, options);
					$this.data('ajaxForm', new ajaxForm(this, settings));
				}
			);
		},
	});

	var ajaxForm = ( function() {

		var _this, _ele ;

		var $paging = $('<input>').attr({
			type: 'hidden',
			name: 'val[page]',
			value: '1'
		});

		var customList =  {};

		ajaxForm = function(ele, settings) { // constructor
			_this = this;
			_ele = ele;
			_this.settings = settings;

			$(_ele).append($paging); // for paging
			$(document).bind(_this.settings['custom_event'], _this.handleTableDataChanged);
			$(document).bind('changepage', _this.changePage); // to bind paging action
			$(document).bind('changeCustom', _this.changeCustom); // to bind paging action
			$(_ele).on('change', function(e) {
					if(!$(e.target).hasClass('ynsaNoAjax')) {
						if(_this.settings.is_validate) {
							if(!$(_ele).valid() ) { // assume that we use valid as checking function
								return false;
							}
						}
						$paging.val(1);
						_this.submitAjaxForm();
					}
			});

			$(_ele).on('submit', function() {
				if(_this.settings.is_prevent_submit) {
					return false;
				}
			});
		};

		ajaxForm.prototype.changePage = function(evt, page) {
			$paging.val(page);
			_this.submitAjaxForm();
		}

		ajaxForm.prototype.changeCustom = function(evt, name, val) {
			if(typeof customList[name] !== 'undefined') {
				var $custom = customList[name];
			} else {

				var $custom = $('<input>').attr({
					type: 'hidden',
					name:  '',
					value: ''
				});

				customList[name] = $custom;
				$(_ele).append($custom);
			}

			$custom.val(val);
			$custom.attr('name', name);

			_this.submitAjaxForm();
		}

		ajaxForm.prototype.handleTableDataChanged = function(evt, html) {
			$('#' + _this.settings['result_div_id']).html(html);
		}


		ajaxForm.prototype.submitAjaxForm = function() {
            $(_ele).ajaxCall(_this.settings['ajax_action'], 'custom_event=' + _this.settings['custom_event'], false, 'POST', function () {
                $('.ynsaActionList').each(function () {
                    if ($(this).find('.link_menu li').length <= 0) {
                        $(this).hide();
                    }
                });
                $('.js_ynsa_drop_down_link').off('click').click(function () {
                    if ($(this).hasClass('clicked')) {
                        $('#js_drop_down_cache_menu').remove();
                        $(this).removeClass('clicked');
                        return false;
                    }
                    var ele = $(this);
                    $(this).addClass('clicked');
                    eleOffset = $(this).offset();

                    $('#js_drop_down_cache_menu').remove();

                    $('body').prepend('<div id="js_drop_down_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:9999;"><div class="link_menu dropdown open" style="display:block;position:relative;">' + $(this).parent().find('.link_menu:first').html() + '</div></div>');

                    $('#js_drop_down_cache_menu .link_menu').hover(function () {

                        },
                        function () {
                            $('#js_drop_down_cache_menu').remove();
                            ele.removeClass('clicked');
                        });

                    return false;
                });
            });
			$('#' + _this.settings['result_div_id']).ynsaWaiting('prepend');

			return false;

		};
		return ajaxForm;
	})();
	window.ynsocialad.ajaxForm = ajaxForm;
}(window, undefined, jQuery));


