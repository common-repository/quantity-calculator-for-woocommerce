(function ($) {
	'use strict';

	$(function () {
		let isVariantSelected = false;
		const isVariableProduct = $('.eniture_quantity_calculator_is_variable_product').val(),
			coverage_input_size = $('#eniture_quantity_calculator_coverage_input_size').val(),
			cart_input_size = $('#eniture_quantity_calculator_cart_input_size').val();

		// cart quantity input field width
		const defaultWidth = parseFloat($('.qty').css('width'));
		let newCartWidth = defaultWidth,
			newCoverageWidth = defaultWidth;
		if (coverage_input_size?.trim()) {
			newCoverageWidth += (newCoverageWidth * parseFloat(coverage_input_size)) / 100;
		}

		if (cart_input_size?.trim()) {
			newCartWidth += (newCartWidth * parseFloat(cart_input_size)) / 100;
		}

		$('.eniture-quantity-calculator-square-feet-input').css('width', newCoverageWidth);
		$('.qty').css('width', newCartWidth);

		// if variable product, then hide error message on page load
		if (isVariableProduct) {
			$('#eniture_quantity_calculator_error_message').hide();
		}

		// disable square feet input field on page load if no variant/s is selected
		if (isVariableProduct && !isVariantSelected) {
			$('.eniture-quantity-calculator-square-feet-input').prop('disabled', true);
		} else {
			$('.eniture-quantity-calculator-square-feet-input').prop('disabled', false);
		}

		// Set cart quantity value on page load
		const min_order_quantity =
			$('#eniture_quantity_calculator_min_sqaure_feet_value').val() || 1;
		if (min_order_quantity) {
			const coverage_per_unit = $('#eniture_quantity_calculator_square_feet').val(),
				measurement_unit = $('#eniture_quantity_calculator_unit_measurement_value').val();
			let qty = min_order_quantity;

			if (measurement_unit == 'coverage_value') {
				qty = coverage_per_unit;
			}

			if ($('#eniture_quantity_calculator_square_feet').length) {
				$('.qty').val(+qty);
			}

			// validate values to show/hide error message on page load
			if (
				!isVariableProduct &&
				eniture_quantity_calculator_validate_square_feet_value(+qty)
			) {
				$('#eniture_quantity_calculator_error_message').hide();
			}
		}

		// When cart quantity changes
		$('.qty').bind('keyup change', function (e) {
			eniture_quantity_calculator_qc_update_qty(this);
		});

		// When quantity calculator field value changes
		$('.eniture-quantity-calculator-square-feet-input').bind('keyup change', function (e) {
			eniture_quantity_calculator_update_sqr_feet(this);
		});

		// validate on product variant/s selection
		$('.single_variation_wrap').on('show_variation', function (event, variation) {
			isVariantSelected = true;
			const product_id = $('.eniture_quantity_calculator_product_id').val();
			const postForm = {
				action: 'eniture_quantity_calculator_get_variation_data',
				variation_id: variation.variation_id,
				product_id: product_id,
				nonce: eniture_quantity_calculator_obj.eniture_quantity_calculator_public_nonce,
			};

			const status = $('#eniture_quantity_calculator_status').val();
			if (status && status == 'yes') {
				jQuery.ajax({
					type: 'POST',
					url: eniture_quantity_calculator_obj.ajax_url,
					data: postForm,
					beforeSend: function () {
						$('.eniture-quantity-calculator-square-feet-input').prop('disabled', true);
						$('.eniture_quantity_calculator_loading_text').text(
							'Loading...please wait'
						);
						$('#eniture_quantity_calculator_error_message').hide();
						$('#eniture-quantity-calculator-user-message').hide();
					},
					success: function (data_response) {
						data_response = JSON.parse(data_response);

						$('.eniture_quantity_calculator_loading_text').text('');
						$('.eniture-quantity-calculator-square-feet-input').prop('disabled', false);

						let qtyPerUnit =
							data_response.eniture_quantity_calculator_qty_per_unit ||
							$('#eniture_quantity_calculator_qty_per_unit').val() ||
							'';
						const min_square_feet_value =
								data_response.eniture_quantity_calculator_min_square_feet_value ||
								1,
							max_square_feet_value =
								data_response.eniture_quantity_calculator_max_square_feet_value ||
								'',
							is_qc_enabled = data_response.eniture_quantity_calculator_enabled,
							user_msg = data_response.eniture_quantity_calculator_user_message || '',
							coverage_input_label =
								data_response.eniture_quantity_calculator_coverage_input_label ||
								'',
							qc_unit_measurement =
								data_response.eniture_quantity_calculator_unit_measurement ||
								'containers';

						if (is_qc_enabled != 'yes' || !qtyPerUnit) {
							$('#eniture-quantity-calculator-user-message').hide();
							$('.eniture-quantity-calculator-square-feet-input').val('');
							$('.eniture-quantity-calculator-square-feet-input').prop(
								'disabled',
								true
							);
							$('#eniture_quantity_calculator_square_feet_label').hide();
							$('#eniture_quantity_calculator_square_feet').hide();
							$('.single_add_to_cart_button').prop('disabled', false);

							return false;
						} else {
							$('#eniture-quantity-calculator-user-message').show();
							$('#eniture-quantity-calculator-user-message').text(user_msg);
							$('#eniture_quantity_calculator_square_feet_label').show();
							$('#eniture_quantity_calculator_square_feet_label').text(
								coverage_input_label
							);
							$('#eniture_quantity_calculator_square_feet').show();

							$('#eniture_quantity_calculator_qty_per_unit').val(qtyPerUnit);
							$('#eniture_quantity_calculator_min_sqaure_feet_value').val(
								min_square_feet_value
							);
							$('#eniture_quantity_calculator_max_sqaure_feet_value').val(
								max_square_feet_value
							);
							$('#eniture_quantity_calculator_unit_measurement_value').val(
								qc_unit_measurement
							);

							let qty_val = min_square_feet_value,
								coverage_val = min_square_feet_value * qtyPerUnit;
							if (qc_unit_measurement == 'coverage_value') qty_val = coverage_val;
							$('.qty').val(qty_val);
							$('.eniture-quantity-calculator-square-feet-input').val(coverage_val);
						}

						if (qtyPerUnit !== '') {
							$('#eniture_quantity_calculator_qty_per_unit').val(qtyPerUnit);
							eniture_quantity_calculator_qc_update_qty('.qty');
						} else {
							$('.eniture-quantity-calculator-square-feet-input').prop(
								'disabled',
								true
							);
							$('.eniture-quantity-calculator-square-feet-input').val('');
							$('#eniture_quantity_calculator_square_feet_label').hide();
							$('#eniture_quantity_calculator_square_feet').hide();
							$('#eniture-quantity-calculator-user-message').hide();

							return false;
						}
					},
					error: function (err) {
						$('.eniture-quantity-calculator-square-feet-input').prop('disabled', false);
						$('.eniture_quantity_calculator_loading_text').text('');
					},
				});
			}
		});

		// Reset the product variants
		$('.reset_variations').on('click', function (e) {
			eniture_quantity_calculator_fields_reset();
			$('.qty').val('1');
		});
	});

	function eniture_quantity_calculator_update_sqr_feet(ref) {
		const sqr_feet = $(ref).val(),
			quantity_per_unit = $('#eniture_quantity_calculator_qty_per_unit').val(),
			measurement_unit =
				$('#eniture_quantity_calculator_unit_measurement_value').val() || 'containers';

		if (isNaN(sqr_feet) || isNaN(quantity_per_unit)) return false;

		// calculations according to the measurement unit
		let qty = Math.ceil(sqr_feet / quantity_per_unit);
		if (measurement_unit === 'coverage_value') qty = qty * quantity_per_unit;

		eniture_quantity_calculator_validate_square_feet_value(qty);

		const min_square_feet_value = $('#eniture_quantity_calculator_min_sqaure_feet_value').val();
		if (qty >= min_square_feet_value) {
			$('.qty').val(Math.ceil(qty));
		}
	}

	function eniture_quantity_calculator_qc_update_qty(ref) {
		const qty = $(ref).val();
		const quantity_per_unit = $('#eniture_quantity_calculator_qty_per_unit').val();
		const min_order_quantity = $('#eniture_quantity_calculator_min_sqaure_feet_value').val();

		if (qty && +qty < +min_order_quantity) $('.qty').val(min_order_quantity);
		if (isNaN(qty) || isNaN(quantity_per_unit) || +qty < min_order_quantity) return false;

		const isDisabled = $('.eniture-quantity-calculator-square-feet-input').is(':disabled');
		if (!isDisabled) {
			const measurement_unit =
				$('#eniture_quantity_calculator_unit_measurement_value').val() || 'containers';
			if (measurement_unit != 'coverage_value') {
				const coverage_value = quantity_per_unit * qty;
				$('.eniture-quantity-calculator-square-feet-input').val(coverage_value);
			}

			eniture_quantity_calculator_validate_square_feet_value(qty);
		}
	}

	function eniture_quantity_calculator_validate_square_feet_value(sqr_feet) {
		const min_square_feet_value = $('#eniture_quantity_calculator_min_sqaure_feet_value').val(),
			max_square_feet_value = $('#eniture_quantity_calculator_max_sqaure_feet_value').val(),
			order_qty = sqr_feet;
		let alertMsg = '';

		if (
			!isNaN(min_square_feet_value) &&
			min_square_feet_value != '' &&
			order_qty < +min_square_feet_value
		) {
			return false;
		}

		if (
			!isNaN(max_square_feet_value) &&
			max_square_feet_value != '' &&
			order_qty > +max_square_feet_value
		) {
			alertMsg =
				'A maximum order quantity of ' +
				max_square_feet_value +
				' is required for this product';
		}

		if (alertMsg != '') {
			const closeBtn = document.createElement('a');
			closeBtn.innerText = 'X';
			closeBtn.href = '#!';
			closeBtn.className = 'button wc-forward wp-element-button';
			closeBtn.tabIndex = 2;
			closeBtn.addEventListener('click', function () {
				$('#eniture_quantity_calculator_error_message').hide();
			});

			const span = document.createElement('span');
			span.innerText = alertMsg;
			const div = document.createElement('div');
			div.append(closeBtn, span);

			$('#eniture_quantity_calculator_error_message').show();
			$('#eniture_quantity_calculator_error_message').html(div.children);
			$('.single_add_to_cart_button').prop('disabled', true);

			return false;
		}

		$('#eniture_quantity_calculator_error_message').hide();
		$('.single_add_to_cart_button').prop('disabled', false);

		return true;
	}

	function eniture_quantity_calculator_fields_reset() {
		$('#eniture-quantity-calculator-user-message').hide();
		$('.eniture-quantity-calculator-square-feet-input').prop('disabled', true);
		$('.eniture-quantity-calculator-square-feet-input').val('');
		$('#eniture_quantity_calculator_square_feet_label').hide();
		$('#eniture_quantity_calculator_square_feet').hide();
	}
})(jQuery);
