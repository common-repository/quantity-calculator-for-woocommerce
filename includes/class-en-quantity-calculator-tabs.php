<?php

if (!class_exists('Eniture_Quantity_Calculator_Tab')) {

    /**
     * Tabs show on admin side.
     */
    class Eniture_Quantity_Calculator_Tab extends WC_Settings_Page
    {

        /**
         * Hook for call.
         */
        public function eniture_quantity_calculator_tab_load()
        {
            $this->id = 'eniture-quantity-calculator';
            add_filter('woocommerce_settings_tabs_array', [$this, 'eniture_quantity_calculator_add_settings_tab'], 50);
            add_action('woocommerce_sections_' . $this->id, [$this, 'output_sections']);
            add_action('woocommerce_settings_' . $this->id, [$this, 'eniture_quantity_calculator_output']);
            add_action('woocommerce_settings_save_' . $this->id, [$this, 'eniture_quantity_calculator_save_settings']);
        }

        /**
         * Setting Tab For Woocommerce
         * @param $settings_tabs
         * @return string
         */
        public function eniture_quantity_calculator_add_settings_tab($settings_tabs)
        {
            $settings_tabs[$this->id] = __('Quantity Calculator', 'woocommerce-settings-eniture-quantity-calculator');
            return $settings_tabs;
        }

        /**
         * Setting Sections
         * @return array
         */
        public function get_sections()
        {
            $sections = array(
                '' => __('Settings', 'woocommerce-settings-eniture-quantity-calculator'),
                'user-guide' => __('User guide', 'woocommerce-settings-eniture-quantity-calculator'),
            );

            return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
        }

        /**
         * Display all pages on wc settings tabs
         * @param $section
         * @return array
         */
        public function eniture_quantity_calculator_get_settings($section = null)
        {
            ob_start();
            switch ($section) {
                case 'user-guide' :
					$this->eniture_quantity_calculator_load_user_guide_template();
					$settings = [];
					break;

				default:
					require_once plugin_dir_path(__FILE__). 'class-en-quantity-calculator-settings.php';
					$settings = Eniture_Quantity_Calculator_Settings::eniture_quantity_calculator_load_settings();
                    break;
            }

            return $settings;
        }

        /**
         * WooCommerce Settings Tabs
         * @global $current_section
         */
        public function eniture_quantity_calculator_output()
        {
            global $current_section;
            $settings = $this->eniture_quantity_calculator_get_settings($current_section);
            WC_Admin_Settings::output_fields($settings);
        }

        /**
         * Woocommerce save Settings
         * @global $current_section
         */
        public function eniture_quantity_calculator_save_settings()
        {
            global $current_section;
            $settings = $this->eniture_quantity_calculator_get_settings($current_section);
            WC_Admin_Settings::save_fields($settings);
        }

		/**
		 * User	 Guide template.
		 */
		public function eniture_quantity_calculator_load_user_guide_template()
		{
			?>
			<div class="eniture_quantity_calculator_user_guide">
			<p>
				The User Guide for this application is maintained on the publisher's website. To view it click
				<a href="<?php echo esc_url(ENITURE_QUANTITY_CALCULATOR_DOCUMENTATION_URL); ?>" target="_blank">
					here
				</a>
				or paste the following link into your browser.
			</p>
			<?php echo esc_url(ENITURE_QUANTITY_CALCULATOR_DOCUMENTATION_URL);
		}

    }

	$eniture_quantity_calculator_settings_tab = new Eniture_Quantity_Calculator_Tab();
    return $eniture_quantity_calculator_settings_tab->eniture_quantity_calculator_tab_load();
}

