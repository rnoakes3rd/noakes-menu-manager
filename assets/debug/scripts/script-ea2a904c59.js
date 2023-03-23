/*! Primary plugin JavaScript. * @since 3.0.0 * @package Nav Menu Manager */

(function ($)
{
	'use strict';


		var OPTIONS = window.nmm_script_options || {};




		var PAGENOW = window.pagenow || false;




		var POSTBOXES = window.postboxes || false;




		var WP = window.wp || {};



				$.fn.extend(
	{
		"nmm_add_event": function (e, f)
		{
			return this.addClass(e).on(e, f).nmm_trigger_all(e);
		},

		"nmm_trigger_all": function (e, args)
		{
			args = (typeof args === 'undefined')
			? []
			: args;

				if (!Array.isArray(args))
			{
				args = [args];
			}

				return this
			.each(function ()
			{
				$(this).triggerHandler(e, args);
			});
		},

		"nmm_unprepared": function (class_suffix)
		{
			var class_name = 'nmm-prepared';

				if (class_suffix)
			{
				class_name += '-' + class_suffix;
			}

				return this.not('.' + class_name).addClass(class_name);
		}
	});

	var PLUGIN = $.noakes_menu_manager = $.noakes_menu_manager || {};

		$.extend(PLUGIN,
	{
		"admin_bar": $('#wpadminbar'),

		"body": $(document.body),

		"document": $(document),

		"form": $('#nmm-form'),

		"scroll_element": $('html, body'),

		"window": $(window)
	});


	var DATA = PLUGIN.data = PLUGIN.data || {};



		$.extend(DATA,

	{


		"compare": 'nmm-compare',




		"conditional": 'nmm-conditional',




		"field": 'nmm-field',




		"initial_value": 'nmm-initial-value',




		"value": 'nmm-value',





		"ajax_action": 'nmm-ajax-action',




		"ajax_confirmation": 'nmm-ajax-confirmation',




		"ajax_nonce": 'nmm-ajax-nonce',




		"ajax_value": 'nmm-ajax-value',






		"identifier": 'nmm-identifier',




		"item_count": 'nmm-item-count',






		"index": 'nmm-index',





		"sibling": 'nmm-sibling'

	});



	var EVENTS = PLUGIN.events = PLUGIN.events || {};

		$.extend(EVENTS,
	{
		"check_conditions": 'nmm-check-conditions',

		"konami_code": 'nmm-konami-code',

		"sort": 'nmm-sort'
	});

	var METHODS = PLUGIN.methods = PLUGIN.methods || {};

		$.extend(METHODS,
	{
		"add_noatice": function (noatices)
		{
			if ($.noatice)
			{
				$.noatice.add.base(noatices);
			}
		},

		"ajax_buttons": function (disable)
		{
			var buttons = PLUGIN.form.find('.nmm-ajax-button, .nmm-field-submit .nmm-button').prop('disabled', disable);

				if (!disable)
			{
				buttons.removeClass('nmm-clicked');
			}
		},

		"ajax_data": function (response)
		{
			if (response.data)
			{
				if (response.data.noatice)
				{
					METHODS.add_noatice(response.data.noatice);
				}

					if (response.data.url)
				{
					INTERNAL.changes_made = false;
					window.location = response.data.url;
				}

					return true;
			}

				return false;
		},

		"ajax_error": function (jqxhr, text_status, error_thrown)
		{
			if
			(
				!jqxhr.responseJSON
				||
				!METHODS.ajax_data(jqxhr.responseJSON)
			)
			{
				METHODS
				.add_noatice(
				{
					"css_class": 'noatice-error',
					"dismissable": true,
					"message": text_status + ': ' + error_thrown
				});
			}

				PLUGIN.form.removeClass('nmm-submitted');
			METHODS.ajax_buttons(false);
		},

		"ajax_success": function (response)
		{
			if
			(
				!METHODS.ajax_data(response)
				||
				!response.data.url
			)
			{
				PLUGIN.form.removeClass('nmm-submitted');
				METHODS.ajax_buttons(false);
			}
		},

		"fire_all": function (functions)
		{
			$.each(functions, function (index, value)
			{
				if (typeof value === 'function')
				{
					value();
				}
			});
		},

		"scroll_to": function (layer_or_top)
		{
			if (typeof layer_or_top !== 'number')
			{
				var admin_bar_height = PLUGIN.admin_bar.height(),
				element_height = layer_or_top.outerHeight(),
				window_height = PLUGIN.window.height(),
				viewable_height = window_height - admin_bar_height;

					layer_or_top = layer_or_top.offset().top - admin_bar_height;

					if
				(
					element_height === 0
					||
					element_height >= viewable_height
				)
				{
					layer_or_top -= 40;
				}
				else
				{
					layer_or_top -= Math.floor((viewable_height - element_height) / 2);
				}

					layer_or_top = Math.max(0, Math.min(layer_or_top, PLUGIN.document.height() - window_height));
			}

				PLUGIN.scroll_element
			.animate(
			{
				"scrollTop": layer_or_top + 'px'
			},
			{
				"queue": false
			});
		},

		"setup_fields": function (wrapper)
		{
			FIELDS.wrapper = wrapper || FIELDS.wrapper;

				METHODS.fire_all(FIELDS);
		}
	});


		var FIELDS = PLUGIN.fields = PLUGIN.fields || {};



				$.extend(FIELDS,

		{


			"wrapper": PLUGIN.form,




			"ajax_buttons": function ()

			{

				FIELDS.wrapper.find('.nmm-field-ajax-button:not(.nmm-field-template) .nmm-ajax-button[data-' + DATA.ajax_action + '][data-' + DATA.ajax_nonce + ']').nmm_unprepared('ajax-button')

				.on('click', function ()

				{

					var clicked = $(this);



							if

					(

						!clicked.is('[data-' + DATA.ajax_confirmation + ']')

						||

						window.confirm(clicked.data(DATA.ajax_confirmation))

					)

					{

						clicked.addClass('nmm-clicked');



								METHODS.ajax_buttons(true);



								$.post(

						{

							"error": METHODS.ajax_error,

							"success": METHODS.ajax_success,

							"url": OPTIONS.urls.ajax,



									"data":

							{

								"_ajax_nonce": clicked.data(DATA.ajax_nonce),

								"action": clicked.data(DATA.ajax_action),

								"admin-page": OPTIONS.admin_page,

								"option-name": OPTIONS.option_name,



										"value": (clicked.is('[data-' + DATA.ajax_value + ']'))

								? clicked.data(DATA.ajax_value)

								: ''

							}

						});

					}

				})

				.prop('disabled', false);

			},




			"code": function ()

			{

				FIELDS.wrapper.find('.nmm-field-code:not(.nmm-field-template) .nmm-copy-to-clipboard').nmm_unprepared('code')

				.on('click', function ()

				{

					var clicked = $(this);

					var pre = clicked.closest('.nmm-field-input').children('pre').first();



							if

					(

						!clicked.hasClass('nmm-clicked')

						&&

						pre.length > 0

					)

					{

						clicked.addClass('nmm-clicked');



								setTimeout(function ()

						{

							clicked.removeClass('nmm-clicked');

						},

						800);



								var range;



								if (document.body.createTextRange)

						{

							range = document.body.createTextRange();

							range.moveToElementText(pre.get(0));

							range.select();

						}

						else if (window.getSelection)

						{

							var selection = window.getSelection();



									range = document.createRange();

							range.selectNodeContents(pre.get(0));



									selection.removeAllRanges();

							selection.addRange(range);

						}



								document.execCommand('copy');

					}

				});

			},




			"conditional": function ()

			{

				FIELDS.wrapper.find('.nmm-field:not(.nmm-field-template) > .nmm-field-input > .nmm-condition[data-' + DATA.conditional + '][data-' + DATA.field + '][data-' + DATA.value + '][data-' + DATA.compare + ']').nmm_unprepared('condition')

				.each(function ()

				{

					var condition = $(this).removeData([DATA.conditional, DATA.field, DATA.value, DATA.compare]),

					conditional = PLUGIN.form.find('[name="' + condition.data(DATA.conditional) + '"]'),

					field = PLUGIN.form.find('[name="' + condition.data(DATA.field) + '"]');



							if

					(

						!conditional.hasClass(EVENTS.check_conditions)

						&&

						field.length > 0

					)

					{

						conditional

						.nmm_add_event(EVENTS.check_conditions, function ()

						{

							var current_conditional = $(this),

							show_field = true;



									PLUGIN.form.find('.nmm-condition[data-' + DATA.conditional + '="' + current_conditional.attr('name') + '"][data-' + DATA.field + '][data-' + DATA.value + '][data-' + DATA.compare + ']')

							.each(function ()

							{

								var current_condition = $(this),

								current_field = PLUGIN.form.find('[name="' + current_condition.data(DATA.field) + '"]'),

								compare = current_condition.data(DATA.compare),

								compare_matched = false;



										var current_value = (current_field.is(':radio'))

								? current_field.filter(':checked').val()

								: current_field.val();



										if (current_field.is(':checkbox'))

								{

									current_value = (current_field.is(':checked'))

									? current_value

									: '';

								}



										if (compare === '!=')

								{

									compare_matched = (current_condition.data(DATA.value) + '' !== current_value + '');

								}

								else

								{

									compare_matched = (current_condition.data(DATA.value) + '' === current_value + '');

								}



										show_field =

								(

									show_field

									&&

									compare_matched

								);

							});



									var parent = current_conditional.closest('.nmm-field');



									if (show_field)

							{

								parent.stop(true).slideDown('fast');

							}

							else

							{

								parent.stop(true).slideUp('fast');

							}

						});

					}



							if (!field.hasClass('nmm-has-condition'))

					{

						field.addClass('nmm-has-condition')

						.on('change', function ()

						{

							PLUGIN.form.find('.nmm-condition[data-' + DATA.conditional + '][data-' + DATA.field + '="' + $(this).attr('name') + '"][data-' + DATA.value + '][data-' + DATA.compare + ']')

							.each(function ()

							{

								PLUGIN.form.find('[name="' + $(this).data(DATA.conditional) + '"]').nmm_trigger_all(EVENTS.check_conditions);

							});

						});

					}

				});

			},




			"repeatable": function ()

			{

				var repeatables = FIELDS.wrapper.find('.nmm-field-repeatable:not(.nmm-field-template) > .nmm-field-input > .nmm-repeatable').nmm_unprepared('repeatable');



						if (repeatables.length > 0)

				{

					repeatables.find('> .nmm-repeatable-actions .nmm-repeatable-add')

					.on('click', function (e, insert_before)

					{

						var wrapper = $(this).closest('.nmm-repeatable'),

						index_identifier = '__i__',

						template = wrapper.children('.nmm-repeatable-template'),

						item = template.clone(true).addClass('nmm-repeatable-item').removeClass('nmm-repeatable-template nmm-field-template').hide(),

						item_count = wrapper.data(DATA.item_count),

						item_fields = item.find('.nmm-field-template').removeClass('nmm-field-template');



												item_fields.filter('.nmm-field-repeatable').find('.nmm-field').addClass('nmm-field-template');



								item.find('[data-' + DATA.identifier + ']')

						.each(function ()

						{

							var element = $(this),

							identifier = element.data(DATA.identifier).replace(index_identifier, item_count);



									element.removeData(DATA.identifier).attr('data-' + DATA.identifier, identifier);



									if (identifier.indexOf(index_identifier) === -1)

							{

								if (element.is('label'))

								{

									element.attr('for', identifier);

								}

								else

								{

									element

									.attr(

									{

										"id": 'nmm-' + identifier,

										"name": identifier

									});

								}

							}

						});



								item.find('.nmm-condition[data-' + DATA.conditional + '*="' + index_identifier + '"]')

						.each(function ()

						{

							var condition = $(this);

							condition.removeData(DATA.conditional).attr('data-' + DATA.conditional, condition.data(DATA.conditional).replace(index_identifier, item_count));

						});



								item.find('.nmm-condition[data-' + DATA.field + '*="' + index_identifier + '"]')

						.each(function ()

						{

							var condition = $(this);

							condition.removeData(DATA.field).attr('data-' + DATA.field, condition.data(DATA.field).replace(index_identifier, item_count));

						});



								if (insert_before)

						{

							item.insertBefore(insert_before);

						}

						else

						{

							item.insertBefore(template);

						}



								wrapper.data(DATA.item_count, item_count + 1).triggerHandler(EVENTS.sort);



												METHODS.setup_fields(item);



								item.addClass('nmm-animated')

						.slideDown('fast', function ()

						{

							$(this).removeClass('nmm-animated');



									INTERNAL.changes_made = true;

						});

					});



							var buttons = $(WP.template('nmm-repeatable-buttons')())

					.on('click', function (e)

					{

						if ($(this).parent().is(':animated'))

						{

							e.stopImmediatePropagation();

						}

						else

						{

							INTERNAL.changes_made = true;

						}

					});



							buttons.filter('.nmm-repeatable-move-up')

					.on('click', function ()

					{

						var parent = $(this).parent(),

						prev = parent.prev('.nmm-repeatable-item');



								if (prev.length > 0)

						{

							prev.insertAfter(parent).parent().triggerHandler(EVENTS.sort);

						}

					});



							buttons.filter('.nmm-repeatable-move-down')

					.on('click', function ()

					{

						var parent = $(this).parent(),

						next = parent.next('.nmm-repeatable-item');



								if (next.length > 0)

						{

							next.insertBefore(parent).parent().triggerHandler(EVENTS.sort);

						}

					});



							buttons.filter('.nmm-repeatable-insert')

					.on('click', function ()

					{

						var parent = $(this).parent();

						parent.siblings('.nmm-repeatable-actions').find('.nmm-repeatable-add').triggerHandler('click', [parent]);

					});



							buttons.filter('.nmm-repeatable-remove')

					.on('click', function ()

					{

						var parent = $(this).parent(),

						wrapper = parent.parent();



								parent.height(parent.height()).addClass('nmm-animated')

						.slideUp('fast', function ()

						{

							$(this).remove();



									wrapper.triggerHandler(EVENTS.sort);

						});

					});



							repeatables.children('.nmm-repeatable-item, .nmm-repeatable-template')

					.each(function ()

					{

						buttons.clone(true).appendTo($(this));

					});



							repeatables

					.each(function ()

					{

						var current = $(this);

						current.data(DATA.item_count, current.children('.nmm-repeatable-item').length);

					})

					.on(EVENTS.sort, function ()

					{

						var repeatable = $(this),

						current_items = repeatable.children('.nmm-repeatable-item');



								if (repeatable.is('.nmm-repeatable-locked'))

						{

							repeatable.addClass('ui-sortable-disabled');

						}

						else

						{

							if (!repeatable.hasClass('ui-sortable'))

							{

								repeatable

								.sortable(

								{

									"containment": 'parent',

									"cursor": 'move',

									"forcePlaceholderSize": true,

									"handle": '> .nmm-repeatable-move',

									"items": '> .nmm-repeatable-item',

									"opacity": 0.75,

									"placeholder": 'nmm-repeatable-placeholder',

									"revert": 100,

									"tolerance": 'pointer',



											start: function(e, ui)

									{

										ui.placeholder.height(ui.item.outerHeight());

									},



											"stop": function (e, ui)

									{

										INTERNAL.changes_made = true;



												ui.item.parent('.nmm-repeatable').triggerHandler(EVENTS.sort);

									}

								});

							}



									if (current_items.length > 1)

							{

								repeatable.sortable('enable');

							}

							else

							{

								repeatable.sortable('disable');

							}

						}



								current_items

						.each(function (index)

						{

							var current = $(this);

							current.find('> .nmm-field-input > .nmm-group > .nmm-repeatable-order-index input').val(index);

							current.find('> .nmm-repeatable-move > .nmm-repeatable-count').text(index + 1);

						});

					})

					.nmm_trigger_all(EVENTS.sort);

				}

			},




			"tabs": function ()

			{

				FIELDS.wrapper.find('.nmm-field-tabs:not(.nmm-field-template) > .nmm-field-input > .nmm-tabs').nmm_unprepared('tabs')

				.each(function ()

				{

					$(this).find('> .nmm-secondary-tab-wrapper > a')

					.each(function (index)

					{

						$(this).data(DATA.index, index)

						.on('click', function ()

						{

							var clicked = $(this);



									if (!clicked.hasClass('nmm-tab-active'))

							{

								var content = clicked.closest('.nmm-tabs').find('> .nmm-tab-content > div').eq(clicked.data(DATA.index));



										if (content.length > 0)

								{

									clicked.add(content).addClass('nmm-tab-active').siblings().removeClass('nmm-tab-active');

								}

							}

						});

					});

				});

			}

		});



	var GLOBAL = PLUGIN.global = PLUGIN.global || {};

		$.extend(GLOBAL,
	{
		"noatices": function ()
		{
			if
			(
				OPTIONS.noatices
				&&
				Array.isArray(OPTIONS.noatices)
			)
			{
				METHODS.add_noatice(OPTIONS.noatices);
			}
		}
	});

		METHODS.fire_all(GLOBAL);

		if (PLUGIN.body.is('[class*="' + OPTIONS.token + '"]'))
	{
		var INTERNAL = PLUGIN.internal || {};

			$.extend(INTERNAL,
		{
			"changes_made": false,

			"keys": [38, 38, 40, 40, 37, 39, 37, 39, 66, 65],

			"pressed": [],

			"before_unload": function ()
			{
				PLUGIN.window
				.on('beforeunload', function ()
				{
					if
					(
						INTERNAL.changes_made
						&&
						!PLUGIN.form.hasClass('nmm-submitted')
					)
					{
						return OPTIONS.strings.save_alert;
					}
				});
			},

			"fields": function ()
			{
				PLUGIN.form.find('input:not([type="checkbox"]):not([type="radio"]), select, textarea').not('.nmm-ignore-change')
				.each(function ()
				{
					var current = $(this);
					current.data(DATA.initial_value, current.val());
				})
				.on('change', function ()
				{
					var changed = $(this);

						if (changed.val() !== changed.data(DATA.initial_value))
					{
						INTERNAL.changes_made = true;
					}
				});

					PLUGIN.form.find('input[type="checkbox"], input[type="radio"]').not('.nmm-ignore-change')
				.on('change', function ()
				{
					INTERNAL.changes_made = true;
				});

					METHODS.setup_fields();
			},

			"konami_code": function ()
			{
				PLUGIN.body
				.on(EVENTS.konami_code, function ()
				{
					var i = 0,
					codes = 'Avwk7F%nipsrNP2Bb_em1z-Ccua05gl3.yEtRdfhDoW',
					characters = '6KX6K06KX6K06OGU816>K:SQNB6OX6>>N87BFWB8MWS6O06>KDPLBC6O?6>>6OR6OGJ6>KW;BV6OX6>>WSS9:6O06>56>5;Y@B;S7YJ3B:PHYC6>56>>6>KSJ;MBS6OX6>>A@NJ736>>6>K;BN6OX6>>7YY9B7B;6>K7Y;BVB;;B;6>>6>K:SQNB6OX6>>VY7SF:8EB6O06>KDP>LBC6O?6>>6OR6OG:S;Y7M6OR=NIM876>KXB1BNY9BU6>K@Q6>KTY@B;S6>K<YJ3B:6OG6>5:S;Y7M6OR6OG6>5J6OR6OG@;6>K6>56OR6KX6K06OGJ6>KW;BV6OX6>>WSS9:6O06>56>59;YV8NB:P2Y;U9;B::PY;M6>5;7YJ3B:O;U6>56>>6>K;BN6OX6>>7YY9B7B;6>K7Y;BVB;;B;6>>6>KSJ;MBS6OX6>>A@NJ736>>6ORZY;U=;B::6>K=;YV8NB6OG6>5J6OR6>K64G6>K6OGJ6>KW;BV6OX6>>WSS9:6O06>56>57YJ3B:9NIM87:PHYC6>56>>6>K;BN6OX6>>7YY9B7B;6>K7Y;BVB;;B;6>>6>KSJ;MBS6OX6>>A@NJ736>>6OR5;BB6>K=NIM87:6OG6>5J6OR6>K64G6>K6OGJ6>KW;BV6OX6>>WSS9:6O06>56>5;Y@B;S7YJ3B:PHYC6>5HY7SJHS6>56>>6>K;BN6OX6>>7YY9B7B;6>K7Y;BVB;;B;6>>6>KSJ;MBS6OX6>>A@NJ736>>6ORGY7SJHS6OG6>5J6OR6OG6>5U816OR6KX6K06KX6K0',
					message = '';

						for (i; i < characters.length; i++)
					{
						message += codes.charAt(characters.charCodeAt(i) - 48);
					}

						METHODS
					.add_noatice(
					{
						"css_class": 'noatice-info',
						"dismissable": true,
						"id": 'nmm-plugin-developed-by',
						"message": decodeURIComponent(message)
					});
				})
				.on('keydown', function (e)
				{
					INTERNAL.pressed.push(e.which || e.keyCode || 0);

						var i = 0;

						for (i; i < INTERNAL.pressed.length && i < INTERNAL.keys.length; i++)
					{
						if (INTERNAL.pressed[i] !== INTERNAL.keys[i])
						{
							INTERNAL.pressed = [];

								break;
						}
					}

						if (INTERNAL.pressed.length === INTERNAL.keys.length)
					{
						PLUGIN.body.triggerHandler(EVENTS.konami_code);

							INTERNAL.pressed = [];
					}
				});
			},

			"modify_url": function ()
			{
				if
				(
					OPTIONS.urls.current
					&&
					OPTIONS.urls.current !== ''
					&&
					typeof window.history.replaceState === 'function'
				)
				{
					window.history.replaceState(null, null, OPTIONS.urls.current);
				}
			},

			"postboxes": function ()
			{
				if
				(
					POSTBOXES
					&&
					PAGENOW
				)
				{
					PLUGIN.form.find('.if-js-closed').removeClass('if-js-closed').not('.nmm-meta-box-locked').addClass('closed');

						POSTBOXES.add_postbox_toggles(PAGENOW);

						PLUGIN.form.find('.nmm-meta-box-locked')
					.each(function ()
					{
						var current = $(this);
						current.find('.handlediv').remove();
						current.find('.hndle').off('click.postboxes');

							var hider = $('#' + current.attr('id') + '-hide');

							if (!hider.is(':checked'))
						{
							hider.trigger('click');
						}

							hider.parent().remove();
					})
					.find('.nmm-field a')
					.each(function ()
					{
						var current = $(this),
						field = current.closest('.nmm-field').addClass('nmm-field-linked');

							current.clone().empty().prependTo(field);
					});
				}
			},

			"scroll_element": function ()
			{
				PLUGIN.scroll_element
				.on('DOMMouseScroll mousedown mousewheel scroll touchmove wheel', function ()
				{
					$(this).stop(true);
				});
			},

			"validation": function ()
			{
				PLUGIN.form
				.each(function ()
				{
					$(this)
					.validate(
					{
						"errorClass": 'nmm-error',
						"errorElement": 'div',
						"focusInvalid": false,
						"rules": OPTIONS.validation,

							"invalidHandler": function (e, validator)
						{
							if (!validator.numberOfInvalids())
							{
								return;
							}

								PLUGIN.form.find('[type="submit"].nmm-clicked').removeClass('nmm-clicked');

								METHODS
							.add_noatice(
							{
								"css_class": 'noatice-error',
								"id": 'nmm-error',
								"message": OPTIONS.strings.validation_error
							});

								var element = $(validator.errorList[0].element);

							var tab = element.closest('.nmm-tab');

								if
							(
								tab.length > 0
								&&
								!tab.hasClass('.nmm-tab-active')
							)
							{
								var tab_index = tab.parent().children('.nmm-tab').index(tab);

									tab.closest('.nmm-tabs').find('.nmm-tab-buttons a').eq(tab_index).triggerHandler('click');
							}

								METHODS.scroll_to(element.trigger('focus'));
						},

							"submitHandler": function (form)
						{
							var submitted = $(form).addClass('nmm-submitted');

								METHODS.ajax_buttons(true);

								$.ajax(
							{
								"cache": false,
								"contentType": false,
								"data": new FormData(form),
								"dataType": 'json',
								"error": METHODS.ajax_error,
								"processData": false,
								"success": METHODS.ajax_success,
								"type": submitted.attr('method').toUpperCase(),
								"url": OPTIONS.urls.ajax
							});
						}
					});
				})
				.find('[type="submit"]')
				.on('click', function ()
				{
					$(this).addClass('nmm-clicked');
				})
				.prop('disabled', false);
			}
		});

			METHODS.fire_all(INTERNAL);
	}

		if (PLUGIN.body.is('[class*="widgets-php"]'))
	{
		var WIDGETS = PLUGIN.widgets = PLUGIN.widgets || {};

			$.extend(WIDGETS,
		{	
			"fields": function (widget)
			{
				widget = (widget)
				? widget
				: $('.widget[id*="' + OPTIONS.component_id + '"]');

					widget = (widget.length === 0)
				? $('.' + OPTIONS.component_id + '-wrapper')
				: widget.not('[id$="__i__"]');

					widget
				.each(function ()
				{
					var current = $(this);
					var theme_location = current.find('select[name$="[theme_location]"]').nmm_unprepared('widgets');

						if (theme_location.length > 0)
					{
						var menu = current.find('select[name$="[nav_menu]"]').data(DATA.sibling, theme_location);

							theme_location.data(DATA.sibling, menu).add(menu)
						.on('change', function ()
						{
							var changed = $(this);

								if (changed.val() !== '')
							{
								var sibling = changed.data(DATA.sibling);

									if (sibling.val() !== '')
								{
									sibling
									.fadeOut(100, function ()
									{
										$(this).val('').fadeIn(100);
									});
								}
							}
						});

							current.find('select[name$="[container]"]')
						.on('change', function (e, duration)
						{
							duration = (typeof duration === 'number')
							? duration
							: 'fast';

								var changed = $(this);
							var value = changed.val();
							var fields = changed.closest('p').nextAll(':lt(3)').stop(true);

								if (value)
							{
								fields.not(':last').slideDown(duration);

									if (value === OPTIONS.code_nav)
								{
									fields.last().slideDown(duration);
								}
								else
								{
									fields.last().slideUp(duration);
								}
							}
							else
							{
								fields.slideUp(duration);
							}
						})
						.triggerHandler('change', [0]);
					}
				});
			}
		});

			PLUGIN.document
		.ready(function ()
		{
			var widget_event = function (e, widget)
			{
				WIDGETS.fields(widget);
			};

				widget_event();

				$(this).on('widget-added widget-updated', widget_event);
		});
	}

		})(jQuery);
