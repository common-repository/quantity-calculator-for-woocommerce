<?php

/**
 * Register hooks
 */
if (!class_exists('Eniture_Quantity_Calculator_Register_Hooks')) {

    class Eniture_Quantity_Calculator_Register_Hooks
    {

        public function __construct(){}

        /**
         * Register Admin Hooks
         */
        public function eniture_quantity_calculator_register_admin_hooks()
        {
            add_action('plugin_action_links', [$this, 'eniture_quantity_calculator_plugin_action_links'], 10, 2);
        }

        /**
         * Register Public Hooks
         */
        public function eniture_quantity_calculator_register_public_hooks()
        {
            $app_block_settings_option = get_option('eniture_quantity_calculator_settings_app_block_options');
            $action = 'woocommerce_' . $app_block_settings_option;

            add_action($action, [$this, 'eniture_quantity_calculator_add_fields']);
            add_action('woocommerce_before_single_product_summary', [$this, 'eniture_quantity_calculator_error_msg']);
        
            add_action('wp_ajax_nopriv_eniture_quantity_calculator_get_variation_data', [$this, 'eniture_quantity_calculator_get_variation_data']);
            add_action('wp_ajax_eniture_quantity_calculator_get_variation_data', [$this, 'eniture_quantity_calculator_get_variation_data']);

            add_action('wp_ajax_nopriv_eniture_quantity_calculator_test_connection', [$this, 'eniture_quantity_calculator_test_connection']);
            add_action('wp_ajax_eniture_quantity_calculator_test_connection', [$this, 'eniture_quantity_calculator_test_connection']);
        }

        /**
         * Show action links on plugins page
         * @param $actions
         * @param $plugin_file
         * @return array
         */
        public function eniture_quantity_calculator_plugin_action_links($actions, $plugin_file)
        {
            static $plugin;
            if (!isset($plugin)) {
                $plugin = ENITURE_QUANTITY_CALCULATOR_BASE_NAME;
            }

            if ($plugin == $plugin_file) {
                $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=eniture-quantity-calculator">' . __('Settings', 'General') . '</a>');
                $site_link = array('support' => '<a href="' . ENITURE_QUANTITY_CALCULATOR_SUPPORT_URL . '" target="_blank">Support</a>');
                $actions = array_merge($settings, $actions);
                $actions = array_merge($site_link, $actions);
            }

            return $actions;
        }

        public function eniture_quantity_calculator_add_fields($input_value){
            $status = get_option('eniture_quantity_calculator_status');
            $last_date = get_option('eniture_quantity_calculator_last_date_check');

            if (empty($status) || $status == 'no' || empty($last_date) || $last_date < gmdate('Y-m-d')) {
                $resp = $this->eniture_quantity_calculator_check_status();
                $resp = is_string($resp) && strlen($resp) > 0 ? json_decode($resp, true) : [];
                $status = isset($resp['severity']) && $resp['severity'] == 'SUCCESS' ? 'yes' : 'no';
                $current_date = gmdate('Y-m-d');

                update_option('eniture_quantity_calculator_status', $status);
                update_option('eniture_quantity_calculator_last_date_check', $current_date);
            }

            if ($status != 'yes') return false;

            global $product;
            $eniture_quantity_calculator_variant_enabled = false;

            if($product->is_type('variable')) {
                $product_variants = $product->get_available_variations();

                if(!empty($product_variants)) {
                    foreach ($product_variants as $variant) {
                        $is_enabled = get_post_meta($variant['variation_id'], 'eniture_enable_quantity_calculator', true);

                        if (empty($eniture_quantity_calculator_variant_enabled) && !empty($is_enabled)) {
                            $eniture_quantity_calculator_variant_enabled = true;
                            break;
                        }
                    }
                }
            }
            
            $eniture_quantity_calculator_qty_per_unit = get_post_meta($product->get_id(), 'eniture_quantity_per_unit', true);
            $eniture_quantity_calculator_enabled = get_post_meta($product->get_id(), 'eniture_enable_quantity_calculator', true);
            $eniture_quantity_calculator_min_sqaure_feet_value = get_post_meta($product->get_id(), 'eniture_minimum_square_feet_value', true);
            $eniture_quantity_calculator_max_sqaure_feet_value = get_post_meta($product->get_id(), 'eniture_maximum_square_feet_value', true);
            $eniture_quantity_calculator_unit_measurement = get_post_meta($product->get_id(), 'eniture_unit_measurement_value', true);
            
            if (!$eniture_quantity_calculator_variant_enabled && empty($eniture_quantity_calculator_enabled)) {
                return false;
            }

            $value = $eniture_quantity_calculator_qty_per_unit;
            if (!empty($value) && !empty($eniture_quantity_calculator_min_sqaure_feet_value)) {
                $value = $value * $eniture_quantity_calculator_min_sqaure_feet_value;
            } elseif (!empty($eniture_quantity_calculator_min_sqaure_feet_value)) {
                $value = $eniture_quantity_calculator_min_sqaure_feet_value;
            }

            ?>
            <div class="quantity eniture-quantity-calculator-square-feet-div">
                <!-- user custom message -->
                <p id="eniture-quantity-calculator-user-message">
                    <?php 
                        $user_message = get_post_meta($product->get_id(), 'eniture_message_for_user', true);
                        echo esc_html($user_message);
                    ?>
                </p>

                <!-- square feet lable and input field -->
                <label for="eniture_quantity_calculator_square_feet" id="eniture_quantity_calculator_square_feet_label">
                    <?php 
                        $coverage_input_label = get_post_meta($product->get_id(), 'eniture_coverage_input_label', true);
                        if (empty($coverage_input_label)) {
                            $coverage_input_label = 'Square feet';
                            update_post_meta($product->get_id(), 'eniture_coverage_input_label', $coverage_input_label);
                        }
                        
                        echo esc_html($coverage_input_label); 
                    ?> 
                </label>
                <input type="number" id="eniture_quantity_calculator_square_feet" name="eniture_quantity_calculator_square_feet" class="input-text text eniture-quantity-calculator-square-feet-input" min="0" step="0.1" 
                title="<?php echo esc_attr($coverage_input_label); ?>"
                value="<?php echo esc_attr($value); ?>"/>
                <span class="eniture_quantity_calculator_loading_text"></span>

                <!-- current product -->
                <input type="hidden" id="eniture_quantity_calculator_product_id" name="eniture_quantity_calculator_product_id" class="eniture_quantity_calculator_product_id" value="<?php echo esc_attr($product->get_id()); ?>" >

                <!-- Quantity per unit, min / max order quantity values -->
                <input type="hidden" id="eniture_quantity_calculator_qty_per_unit" name="eniture_quantity_calculator_qty_per_unit" class="eniture_quantity_calculator_qty_per_unit"
                value="<?php echo esc_attr($eniture_quantity_calculator_qty_per_unit); ?>"/>
                <input type="hidden" id="eniture_quantity_calculator_min_sqaure_feet_value" name="eniture_quantity_calculator_min_sqaure_feet_value" class="eniture_quantity_calculator_min_sqaure_feet_value"
                value="<?php echo esc_attr($eniture_quantity_calculator_min_sqaure_feet_value); ?>"/>
                <input type="hidden" id="eniture_quantity_calculator_max_sqaure_feet_value" name="eniture_quantity_calculator_max_sqaure_feet_value" class="eniture_quantity_calculator_max_sqaure_feet_value"
                value="<?php echo esc_attr($eniture_quantity_calculator_max_sqaure_feet_value); ?>"/>
                <input type="hidden" id="eniture_quantity_calculator_unit_measurement_value" name="eniture_quantity_calculator_unit_measurement_value" class="eniture_quantity_calculator_unit_measurement_value"
                value="<?php echo esc_attr($eniture_quantity_calculator_unit_measurement); ?>"/>

                <!-- Product type e.g. simple / variable value -->
                <input type="hidden" id="eniture_quantity_calculator_is_variable_product" name="eniture_quantity_calculator_is_variable_product" class="eniture_quantity_calculator_is_variable_product" value="<?php echo esc_attr($product->is_type('variable')); ?>"/>

                <!-- Cart and coverage input fields size values -->
                <input type="hidden" name="eniture_quantity_calculator_coverage_input_size" id="eniture_quantity_calculator_coverage_input_size" value="<?php echo   esc_attr(get_option('eniture_quantity_calculator_coverage_input_size')); ?>">
                <input type="hidden" name="eniture_quantity_calculator_cart_input_size" id="eniture_quantity_calculator_cart_input_size" value="<?php echo esc_attr(get_option('eniture_quantity_calculator_settings_cart_input_size')); ?>">
                <input type="hidden" name="eniture_quantity_calculator_status" id="eniture_quantity_calculator_status" value="<?php echo esc_attr(get_option('eniture_quantity_calculator_status')); ?>">
            </div>
            <?php
        }
         
        public function eniture_quantity_calculator_error_msg() {
        ?>

        <div class="woocommerce-notices-wrapper">
            <div class="woocommerce-error" role="alert" id="eniture_quantity_calculator_error_message">
            </div>
        </div>

        <?php
        }

         
        public function eniture_quantity_calculator_get_variation_data()
        {
            $nonce = isset($_POST['nonce']) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            $status = get_option('eniture_quantity_calculator_status');
            if (!wp_verify_nonce($nonce, 'eniture-qc-ajax-nonce-public') || $status != 'yes') {
                echo wp_json_encode([]);
                wp_die();
            }

            $variation_id = isset($_POST['variation_id']) ? sanitize_text_field( wp_unslash( $_POST['variation_id'] ) ) : '';
            $product_id = isset($_POST['product_id']) ? sanitize_text_field( wp_unslash( $_POST['product_id'] )) : '';
            if (empty($variation_id)) {
                echo wp_json_encode([]);
                wp_die();
            }
            
            $response = [];
            $is_enabled = get_post_meta($variation_id, 'eniture_enable_quantity_calculator', true);

            if ($is_enabled == 'yes') {
                $response['eniture_quantity_calculator_qty_per_unit'] = get_post_meta($variation_id, 'eniture_quantity_per_unit', true);
                $response['eniture_quantity_calculator_min_square_feet_value'] = get_post_meta($variation_id, 'eniture_minimum_square_feet_value', true);
                $response['eniture_quantity_calculator_max_square_feet_value'] = get_post_meta($variation_id, 'eniture_maximum_square_feet_value', true);
                $response['eniture_quantity_calculator_enabled'] = get_post_meta($variation_id, 'eniture_enable_quantity_calculator', true);
                $response['eniture_quantity_calculator_user_message'] = htmlspecialchars_decode(get_post_meta($variation_id, 'eniture_message_for_user', true), ENT_QUOTES);
                $response['eniture_quantity_calculator_coverage_input_label'] = htmlspecialchars_decode(get_post_meta($variation_id, 'eniture_coverage_input_label', true), ENT_QUOTES);
                $response['eniture_quantity_calculator_unit_measurement'] = get_post_meta($variation_id, 'eniture_unit_measurement_value', true);
            }

            // Parent product enable check
            $is_enabled = get_post_meta($product_id, 'eniture_enable_quantity_calculator', true);
            if ($is_enabled != 'yes') {
                if (empty($response['eniture_quantity_calculator_min_square_feet_value'])) {
                    $response['eniture_quantity_calculator_min_square_feet_value'] = 1;
                }

                echo wp_json_encode($response);
                wp_die();
            }

            if (empty($response['eniture_quantity_calculator_qty_per_unit'])) {
                $response['eniture_quantity_calculator_qty_per_unit'] = get_post_meta($product_id, 'eniture_quantity_per_unit', true);
            }

            if (empty($response['eniture_quantity_calculator_min_square_feet_value'])) {
                $eniture_quantity_calculator_min_sqaure_feet_value = get_post_meta($product_id, 'eniture_minimum_square_feet_value', true);
                if (empty($eniture_quantity_calculator_min_sqaure_feet_value)) {
                    $eniture_quantity_calculator_min_sqaure_feet_value = 1;
                }
                
                $response['eniture_quantity_calculator_min_square_feet_value'] = $eniture_quantity_calculator_min_sqaure_feet_value;
            }

            if (empty($response['eniture_quantity_calculator_max_square_feet_value'])) {
                $response['eniture_quantity_calculator_max_square_feet_value'] = get_post_meta($product_id, 'eniture_maximum_square_feet_value', true);
            }

            if (empty($response['eniture_quantity_calculator_enabled'])) {
                $response['eniture_quantity_calculator_enabled'] = get_post_meta($product_id, 'eniture_enable_quantity_calculator', true);
            }

            if (empty($response['eniture_quantity_calculator_user_message'])) {
                $response['eniture_quantity_calculator_user_message'] = htmlspecialchars_decode(get_post_meta($product_id, 'eniture_message_for_user', true), ENT_QUOTES);
            }

            if (empty($response['eniture_quantity_calculator_coverage_input_label'])) {
                $response['eniture_quantity_calculator_coverage_input_label'] = htmlspecialchars_decode(get_post_meta($product_id, 'eniture_coverage_input_label', true), ENT_QUOTES);
            }

            if (empty($response['eniture_quantity_calculator_unit_measurement'])) {
                $response['eniture_quantity_calculator_unit_measurement'] = get_post_meta($product_id, 'eniture_unit_measurement_value', true);
            }
            
            echo wp_json_encode($response);
            wp_die();
        }

        /**
         * Get Curl Response
         * @return array $resp
        */
        public function eniture_quantity_calculator_test_connection()
        {
            $nonce = isset($_POST['nonce']) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
            if (!wp_verify_nonce($nonce, 'eniture-qc-ajax-nonce-admin')) {
                $resp = [
                    'severity' => 'ERROR',
                    'Message' => 'Invalid request.'
                ];
                
                echo wp_json_encode($resp);
                wp_die();
            }
            
            $license_key = isset($_POST['eniture_quantity_calculator_licence_key']) ? sanitize_text_field( wp_unslash( $_POST['eniture_quantity_calculator_licence_key'] ) ) : '';
            $resp = $this->eniture_quantity_calculator_get_api_response($license_key);   
            $resp = is_string($resp) && strlen($resp) > 0 ? json_decode($resp, true) : [];
            
            echo wp_json_encode($resp);
            wp_die();
        }

        public function eniture_quantity_calculator_check_status()
        {
            $license_key = get_option('eniture_quantity_calculator_settings_license_key');
            $response = $this->eniture_quantity_calculator_get_api_response($license_key);

            return $response;
        }

        public function eniture_quantity_calculator_get_api_response($license_key)
        {
            $url = ENITURE_QUANTITY_CALCULATOR_DOMAIN_URL;
            $postData = [
                'serverName' => $this->eniture_quantity_calculator_get_server_name(),
                'licenseKey' => $license_key,
                'platform' => 'WordPress',
                'carrierType' => 'Small',
                'carrierName' => 'qtyCalc',
                'carrierMode' => 'test',
                'requestVersion' => '2.0'
            ];

            $response = wp_remote_post($url,
                array(
                    'method' => 'POST',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => wp_json_encode($postData),
                )
            );
            $response = wp_remote_retrieve_body($response);

            return $response;
        }

        public function eniture_quantity_calculator_get_host($url)
        {
            $parse_url = wp_parse_url(trim($url));
            if (isset($parse_url['host'])) {
                $host = $parse_url['host'];
            } else {
                $path = explode('/', $parse_url['path']);
                $host = $path[0];
            }

            return trim($host);
        }

        /**
         * Get Domain Name
         */
        public function eniture_quantity_calculator_get_server_name()
        {
            global $wp;
            $wp_request = (isset($wp->request)) ? $wp->request : '';
            $url = home_url($wp_request);
            return $this->eniture_quantity_calculator_get_host($url);
        }
    }
    
}
