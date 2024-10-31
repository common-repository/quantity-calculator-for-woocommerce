<?php

/**
 * Add quantity calculator options to product detail page
 */
if (!class_exists('Eniture_Quantity_Calculator_Product_Detail')) {

    class Eniture_Quantity_Calculator_Product_Detail
    {

        /**
         * Hook for call.
         */
        public function __construct()
        {
            // Add simple product fields
			add_action('woocommerce_product_options_general_product_data', [$this, 'eniture_quantity_calculator_show_product_fields'], 101, 3);
			add_action('woocommerce_process_product_meta', [$this, 'eniture_quantity_calculator_save_product_fields'], 101, 1);

			// Add variable product fields.
			add_action('woocommerce_product_after_variable_attributes', [$this, 'eniture_quantity_calculator_show_product_fields'], 101, 3);
			add_action('woocommerce_save_product_variation', [$this, 'eniture_quantity_calculator_save_product_fields'], 101, 1);
        }

        /**
         * Show product fields in variation and simple product.
         * @param array $loop
         * @param array $variation_data
         * @param array $variation
         */
        public function eniture_quantity_calculator_show_product_fields($loop, $variation_data = [], $variation = [])
        {
            $postId = (isset($variation->ID)) ? $variation->ID : get_the_ID();
            $this->eniture_quantity_calculator_custom_product_fields($postId);
        }

        /**
         * Save the simple product fields.
         * @param int $postId
         */
        public function eniture_quantity_calculator_save_product_fields($postId)
        {
            if (isset($postId) && $postId > 0) {
                $eniture_product_fields = $this->eniture_quantity_calculator_product_fields_arr();

                foreach ($eniture_product_fields as $key => $custom_field) {
                    $custom_field = (isset($custom_field['id'])) ? $custom_field['id'] : '';
                    $eniture_updated_product = (isset($_POST[$custom_field][$postId])) ? esc_attr( sanitize_text_field( wp_unslash( $_POST[$custom_field][$postId] ) ) ) : '';
                    update_post_meta($postId, $custom_field, $eniture_updated_product);
                }
            }
        }

        /**
         * Product Fields Array
         * @return array
         */
        public function eniture_quantity_calculator_product_fields_arr()
        {
            $eniture_product_fields = [
                [
                    'type' => 'checkbox',
                    'id' => 'eniture_enable_quantity_calculator',
                    'class' => 'eniture_enable_quantity_calculator',
                    'label' => 'Enable Quantity Calculator',
                    'desc_tip' => false
                ],
                [
                    'type' => 'text_area',
                    'id' => 'eniture_message_for_user',
                    'class' => 'eniture_message_for_user short',
                    'label' => 'Message / Instructions',
                    'desc_tip' => true,
                    'description' => __('Enter the message you want displayed to the site visitor.', 'woocommerce')
                ],
                [
                    'type' => 'input_field',
                    'id' => 'eniture_coverage_input_label',
                    'class' => 'eniture_coverage_input_label short',
                    'label' => 'Coverage input label',
                    'desc_tip' => true,
                    'description' => __('Enter the unit of measure that describes what the the field is intended to collect. "Square feet" or "Cubic volume" are common labels, but you can specify any type of unit of measure you wish.', 'woocommerce')
                ],
                [
                    'type' => 'input_field',
                    'id' => 'eniture_quantity_per_unit',
                    'class' => 'eniture_quantity_per_unit short',
                    'label' => 'Coverage per unit',
                    'desc_tip' => true,
                    'description' => __('Coverage per unit factor used to calculate quantity.', 'woocommerce')
                ],
                [
                    'type' => 'input_field',
                    'id' => 'eniture_minimum_square_feet_value',
                    'class' => 'eniture_minimum_square_feet_value short',
                    'label' => 'Minimum order quantity',
                    'desc_tip' => true,
                    'description' => __('Minimum order quantity in a decimal format.', 'woocommerce')
                ],
                [
                    'type' => 'input_field',
                    'id' => 'eniture_maximum_square_feet_value',
                    'class' => 'eniture_maximum_square_feet_value short',
                    'label' => 'Maximum order quantity',
                    'desc_tip' => true,
                    'description' => __('Maximum order quantity in a decimal format.', 'woocommerce')
                ],
                [
                    'type' => 'radio',
                    'id' => 'eniture_unit_measurement_value',
                    'class' => 'eniture_unit_measurement_value short',
                    'label' => 'Put the',
                    'default' => 'containers',
                    'options' => [
                        'containers' => 'containers (e.g. boxes, cans, other) in the cart',
                        'coverage_value' => 'coverage value (e.g. area, volume) into the cart'
                    ]
                ]
            ];

            return $eniture_product_fields;
        }

        /**
         * Show Product Fields
         * @param int $postId
         */
        public function eniture_quantity_calculator_custom_product_fields($postId)
        {
            $eniture_product_fields = $this->eniture_quantity_calculator_product_fields_arr();

            foreach ($eniture_product_fields as $key => $custom_field) {
                $eniture_field_type = (isset($custom_field['type'])) ? $custom_field['type'] : '';
                $eniture_action_function_name = 'eniture_quantity_calculator_product_' . $eniture_field_type;

                if (method_exists($this, $eniture_action_function_name)) {
                    $this->$eniture_action_function_name($custom_field, $postId);
                }
            }
        }

        /**
         * Dynamic checkbox field show on product detail page
         * @param array $custom_field
         * @param int $postId
         */
        public function eniture_quantity_calculator_product_checkbox($custom_field, $postId)
        {
            $custom_checkbox_field = [
                'id' => $custom_field['id'] . '[' . $postId . ']',
                'value' => get_post_meta($postId, $custom_field['id'], true),
                'label' => $custom_field['label'],
                'class' => $custom_field['class'],
            ];

            if (isset($custom_field['description'])) {
                $custom_checkbox_field['description'] = $custom_field['description'];
            }

            echo '<div>';
            woocommerce_wp_checkbox($custom_checkbox_field);
            echo '</div>';
        }

        /**
         * Dynamic dropdown field show on product detail page
         * @param array $custom_field
         * @param int $postId
         */
        public function eniture_quantity_calculator_product_dropdown($custom_field, $postId)
        {
            $custom_dropdown_field = [
                'id' => $custom_field['id'] . '[' . $postId . ']',
                'label' => $custom_field['label'],
                'class' => $custom_field['class'],
                'value' => get_post_meta($postId, $custom_field['id'], true),
                'options' => $custom_field['options']
            ];

            echo '<div>';
            woocommerce_wp_select($custom_dropdown_field);
            echo '</div>';
        }

        /**
         * Dynamic input field show on product detail page
         * @param array $custom_field
         * @param int $postId
         */
        public function eniture_quantity_calculator_product_input_field($custom_field, $postId)
        {
            $custom_input_field = [
                'id' => $custom_field['id'] . '[' . $postId . ']',
                'label' => $custom_field['label'],
                'class' => $custom_field['class'],
                'placeholder' => $custom_field['label'],
                'value' => get_post_meta($postId, $custom_field['id'], true)
            ];

            if (isset($custom_field['description'])) {
                $custom_input_field['desc_tip'] = true;
                $custom_input_field['description'] = $custom_field['description'];
            }

            echo '<div>';
            woocommerce_wp_text_input($custom_input_field);
            echo '</div>';
        }

        /**
         * Dynamic input field show on product detail page
         * @param array $custom_field
         * @param int $postId
         */
        public function eniture_quantity_calculator_product_text_area($custom_field, $postId)
        {
            $value = htmlspecialchars_decode(get_post_meta($postId, $custom_field['id'], true), ENT_QUOTES);
            $custom_input_field = [
                'id' => $custom_field['id'] . '[' . $postId . ']',
                'label' => $custom_field['label'],
                'class' => $custom_field['class'],
                'placeholder' => $custom_field['label'],
                'value' => $value
            ];

            if (isset($custom_field['description'])) {
                $custom_input_field['desc_tip'] = true;
                $custom_input_field['description'] = $custom_field['description'];
            }

            echo '<div>';
            woocommerce_wp_textarea_input($custom_input_field);
            echo '</div>';
        }

        public function eniture_quantity_calculator_product_radio($custom_field, $postId) {
            $get_meta = get_post_meta($postId, $custom_field['id'], true);
            $assigned_option = is_serialized($get_meta) ? maybe_unserialize($get_meta) : $get_meta;
            $assigned_option = empty($assigned_option) ? $custom_field['default'] : $assigned_option;

            $custom_input_field = [
                'id'      => $custom_field['id'] . '[' . $postId . ']',
                'options' => $custom_field['options'],
                'label'   => $custom_field['label'],
                'class'   => $custom_field['class'],
                'value'   => $assigned_option,
            ];

            echo '<div class="options_group">';
            woocommerce_wp_radio($custom_input_field);
            echo '</div>';
        }
    }

}
