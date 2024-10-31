<?php

if (!class_exists('Eniture_Quantity_Calculator_Settings')) {
	/**
	 * Add array for Settings.
	 */
	class Eniture_Quantity_Calculator_Settings
	{
		/**
		 * Settings template.
		 * @return array
		 */
		public static function eniture_quantity_calculator_load_settings()
		{
			$start_settings = [
				'eniture_quantity_calculator_settings' => [
					'name' => '',
					'type' => 'title',
					'id' => 'eniture_quantity_calculator_settings',
				],
			];

			// Settings Detail
			$eniture_settings = self::eniture_quantity_calculator_set_settings_detail();

			$end_settings = [
				'eniture_quantity_calculator_settings_end' => [
					'type' => 'sectionend',
					'id' => 'eniture_quantity_calculator_settings_end'
				]
			];

			$settings = array_merge($start_settings, $eniture_settings, $end_settings);

			return $settings;
		}

		
		/**
		 * Connection Settings Detail Set
		 * @return array
		 */
		public static function eniture_quantity_calculator_set_settings_detail()
		{
			$app_block_settings_option = get_option('eniture_quantity_calculator_settings_app_block_options');
			if (empty($app_block_settings_option)) {
				update_option('eniture_quantity_calculator_settings_app_block_options', 'before_add_to_cart_quantity');
			}

			echo '<div class="eniture_quantity_calculator_connection_settings">';

			return
				[
					'eniture_quantity_calculator_settings_app_block_title' => [
						'name' => esc_html__('Settings', 'woocommerce-settings-eniture-quantity-calculator'),
						'type' => 'title',
						'id' => 'eniture_quantity_calculator_settings_app_block_title'
					],
					'eniture_quantity_calculator_settings_license_key' => [
						'name' => esc_html__('Eniture API Key', 'woocommerce-settings-eniture-quantity-calculator'),
						'type' => 'text',
						'desc' => __('Obtain a Eniture API Key from <a href="https://eniture.com/products/" target="_blank">eniture.com</a>', 'woocommerce-settings-eniture-quantity-calculator'),
						'id' => 'eniture_quantity_calculator_settings_license_key'
					],
					'section_end_license_key' => array(
						'type' => 'sectionend',
						'id' => 'eniture_quantity_calculator_license_key_section_end'
					),
					'eniture_quantity_calculator_settings_app_block_description' => [
						'name' => '',
						'type' => 'title',
						'desc' => esc_html__("Where do you want to	position the project requirements input field?", 'woocommerce-settings-eniture-quantity-calculator'),
						'id' => 'eniture_quantity_calculator_settings_app_block_description'
					],
					'eniture_quantity_calculator_settings_app_block_options' => [
						'name' => '',
						'type' => 'radio',
						'default' => 'before_add_to_cart_quantity',
						'options' => [
							'before_add_to_cart_form' => __('Before add to cart form ', 'woocommerce-settings-eniture-quantity-calculator'),
							'before_add_to_cart_quantity' => __('Before add to cart quantity ', 'woocommerce-settings-eniture-quantity-calculator'),
							'after_add_to_cart_quantity' => __('After add to cart quantity ', 'woocommerce-settings-eniture-quantity-calculator'),
						],
						'id' => 'eniture_quantity_calculator_settings_app_block_options'
					],
					'section_end_app_block' => array(
						'type' => 'sectionend',
						'id' => 'eniture_quantity_calculator_section_end'
					),
					'eniture_quantity_calculator_settings_input_size_label' => [
						'name' => '',
						'type' => 'title',
						'desc' => esc_html__("Increase the project requirements field width by the following percentage:", 'woocommerce-settings-eniture-quantity-calculator'),
						'id' => 'eniture_quantity_calculator_settings_input_size_label'
					],
					'eniture_quantity_calculator_coverage_input_size' => [
						'name' => '',
						'type' => 'text',
						'id' => 'eniture_quantity_calculator_coverage_input_size',
					],
					'section_end_coverage_field_block' => array(
						'type' => 'sectionend',
						'id' => 'eniture_quantity_calculator_section_end'
					),
					'eniture_quantity_calculator_settings_cart_input_size_label' => [
						'name' => '',
						'type' => 'title',
						'desc' => __("Increase the <b>Add to cart</b> field width by the following percentage:", 'woocommerce-settings-eniture-quantity-calculator'),
						'id' => 'eniture_quantity_calculator_settings_cart_input_size_label'
					],
					'eniture_quantity_calculator_settings_cart_input_size' => [
						'name' => '',
						'type' => 'text',
						'id' => 'eniture_quantity_calculator_settings_cart_input_size',
					],
				];
		}

	}

}