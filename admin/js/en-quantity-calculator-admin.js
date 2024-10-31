(function ($) {
	'use strict';

	$(document).ready(function () {
		$('#eniture_quantity_calculator_coverage_input_size').attr('maxLength', 5);
		$('#eniture_quantity_calculator_coverage_input_size').on('change keypress', function (e) {
			if (!String.fromCharCode(e.keyCode).match(/^[\d%]+$/i)) return false;
		});

		/*
		 * Add err class on Eniture API Key field
		 */
		if (
			jQuery('#eniture_quantity_calculator_settings_license_key')
				.parent()
				.find('.eniture_quantity_calculator_error').length < 1
		) {
			jQuery('#eniture_quantity_calculator_settings_license_key').after(
				'<span class="eniture_quantity_calculator_error"></span>'
			);
		}

		$('.eniture_quantity_calculator_connection_settings .woocommerce-save-button').before(
			'<button type="button" class="button-primary components-button eniture_quantity_calculator_test_connection">Test connection</button>'
		);
		$('.eniture_quantity_calculator_test_connection').click(function (e) {
			e.preventDefault();
			jQuery('.eniture_quantity_calculator_error').html('');

			if (!jQuery('#eniture_quantity_calculator_settings_license_key').val()) {
				jQuery('.eniture_quantity_calculator_error').html(
					'Eniture API Key is required.'
				);
				return false;
			}

			const postForm = {
				action: 'eniture_quantity_calculator_test_connection',
				eniture_quantity_calculator_licence_key: $(
					'#eniture_quantity_calculator_settings_license_key'
				).val(),
				nonce: eniture_quantity_calculator_obj.eniture_quantity_calculator_admin_nonce,
			};

			$.ajax({
				type: 'POST',
				url: eniture_quantity_calculator_obj.ajax_url,
				data: postForm,
				dataType: 'json',
				beforeSend: function () {
					$('.eniture_quantity_calculator_test_connection')
						.addClass('spinner_disable')
						.text('Loading...');
					$('.eniture-quantity-calculator-success-message').hide();
					$('.eniture-quantity-calculator-error-message').hide();
				},
				success: function (data) {
					$('.eniture_quantity_calculator_test_connection')
						.removeClass('spinner_disable')
						.text('Test connection');
					const severity = data?.severity || '';
					const msg = data?.Message || 'Invalid credentials.';

					if (severity === 'SUCCESS') {
						$('.eniture_quantity_calculator_connection_settings').before(
							`<div class="notice notice-success eniture-quantity-calculator-success-message"><p><b>Success! ${msg}</b></p></div>`
						);
					} else {
						$('.eniture_quantity_calculator_connection_settings').before(
							`<div class="notice notice-error eniture-quantity-calculator-error-message"><p><b>Error! ${msg}</b></p></div>`
						);
					}
				},
				error: function (error) {
					$('.eniture_quantity_calculator_test_connection')
						.removeClass('spinner_disable')
						.text('Test connection');
					$('.eniture_quantity_calculator_connection_settings').before(
						`<div class="notice notice-error eniture-quantity-calculator-error-message"><p><b>Error! Something went wrong.</b></p></div>`
					);
				},
			});
		});

		$('.eniture_quantity_calculator_connection_settings .woocommerce-save-button').on(
			'click',
			function (e) {
				jQuery('.eniture_quantity_calculator_error').html('');

				if (!jQuery('#eniture_quantity_calculator_settings_license_key').val()) {
					jQuery('.eniture_quantity_calculator_error').html(
						'Eniture API Key is required.'
					);
					return false;
				}
			}
		);
	});

	$(function () {
		$(document).bind('DOMNodeInserted', function (e) {
			const inputs = $(
				'.eniture_quantity_per_unit, .eniture_minimum_square_feet_value, .eniture_maximum_square_feet_value'
			);

			if (inputs && inputs.length > 0) {
				$(
					'.eniture_quantity_per_unit, .eniture_minimum_square_feet_value, .eniture_maximum_square_feet_value'
				).keydown(function (e) {
					if ((e.keyCode === 109 || e.keyCode === 189) && $(this).val().length > 0)
						return false;
					if (e.keyCode === 53)
						if (e.shiftKey) if ($(this).val().length == 0) return false;
					if (
						$(this).val().indexOf('.') != -1 &&
						$(this)
							.val()
							.substring(
								$(this).val().indexOf('.'),
								$(this).val().indexOf('.').length
							).length > 2
					) {
						if (e.keyCode !== 8 && e.keyCode !== 46) {
							//exception
							e.preventDefault();
						}
					}

					// Allow: backspace, delete, tab, escape, enter and .
					if (
						$.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
						// Allow: Ctrl+A, Command+A
						(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
						// Allow: home, end, left, right, down, up
						(e.keyCode >= 35 && e.keyCode <= 40)
					) {
						// let it happen, don't do anything
						return;
					}
					// Ensure that it is a number and stop the keypress
					if (
						(e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
						(e.keyCode < 96 || e.keyCode > 105)
					) {
						e.preventDefault();
					}

					if ($(this).val().length > 7) {
						e.preventDefault();
					}
				});
			}
		});

		$(
			'.eniture_quantity_per_unit, .eniture_minimum_square_feet_value, .eniture_maximum_square_feet_value'
		).bind('paste', function (e) {
			e.preventDefault();
		});

		$('.eniture_quantity_per_unit').closest('p').addClass('eniture_quantity_per_unit_tr');
		$('.eniture_minimum_square_feet_value')
			.closest('p')
			.addClass('eniture_minimum_square_feet_value_tr');
		$('.eniture_maximum_square_feet_value')
			.closest('p')
			.addClass('eniture_maximum_square_feet_value_tr');
		$('.eniture_message_for_user').closest('p').addClass('eniture_message_for_user_tr');
		$('.eniture_coverage_input_label').closest('p').addClass('eniture_coverage_input_label_tr');
		$('.eniture_unit_measurement_value')
			.closest('fieldset')
			.addClass('eniture_unit_measurement_value_tr');

		// Check fields on page load
		toggleQCFieldsDisplay();

		// toggle display on enable qc checkbox
		$('.eniture_enable_quantity_calculator').on('click change load', function () {
			toggleQCFieldsDisplay();
		});
		$(document).on('click change load', '.eniture_enable_quantity_calculator', function (e) {
			const name = $(e.target).attr('name');
			const checked = $(e.target).prop('checked');
			const id = name?.split('eniture_enable_quantity_calculator')[1];

			toggleQCVariantFieldsDisplay(id, checked);
		});

		function toggleQCFieldsDisplay() {
			if (!$('.eniture_enable_quantity_calculator').is(':checked')) {
				$('.eniture_quantity_per_unit_tr').hide();
				$('.eniture_minimum_square_feet_value_tr').hide();
				$('.eniture_maximum_square_feet_value_tr').hide();
				$('.eniture_message_for_user_tr').hide();
				$('.eniture_coverage_input_label_tr').hide();
				$('.eniture_unit_measurement_value_tr').hide();
			} else {
				$('.eniture_quantity_per_unit_tr').show();
				$('.eniture_minimum_square_feet_value_tr').show();
				$('.eniture_maximum_square_feet_value_tr').show();
				$('.eniture_message_for_user_tr').show();
				$('.eniture_coverage_input_label_tr').show();
				$('.eniture_unit_measurement_value_tr').show();
			}
		}

		function toggleQCVariantFieldsDisplay(id, checked) {
			$(
				`input[name="eniture_quantity_per_unit${id}"], input[name="eniture_minimum_square_feet_value${id}"], input[name="eniture_maximum_square_feet_value${id}"]`
			).keypress(function (e) {
				if (!String.fromCharCode(e.keyCode).match(/^[0-9.]+$/)) return false;
			});

			const fieldsNames = [
				'eniture_message_for_user',
				'eniture_coverage_input_label',
				'eniture_quantity_per_unit',
				'eniture_minimum_square_feet_value',
				'eniture_maximum_square_feet_value',
				'eniture_unit_measurement_value',
			];

			for (const fn of fieldsNames) {
				const selector =
					fn === 'eniture_message_for_user'
						? `textarea[name="${fn}${id}"`
						: `input[name="${fn}${id}"`;
				const parent = fn === 'eniture_unit_measurement_value' ? 'fieldset' : 'p';

				$(selector)
					.closest(parent)
					.css('display', checked ? '' : 'none');
			}
		}
	});
})(jQuery);
