<?php

/**
 * The core plugin class.
 */
class Eniture_Quantity_Calculator {

	/**
	 * The unique identifier of this plugin.
	 */
	public $plugin_name;
	/**
	 * The unique identifier of this plugin.
	 */
	public $hooks_obj;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		$this->plugin_name = 'eniture_quantity_calculator';
		$this->eniture_quantity_calculator_load_dependencies_and_enqueue_scripts();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function eniture_quantity_calculator_load_dependencies_and_enqueue_scripts() {
		add_action('admin_enqueue_scripts', array($this, 'eniture_quantity_calculator_enqueue_admin_scripts'));
		add_action('wp_enqueue_scripts', array($this, 'eniture_quantity_calculator_enqueue_public_scripts'));
		require_once plugin_dir_path( __FILE__ ) . 'class-en-quantity-calculator-register-hooks.php';
		$this->hooks_obj = new Eniture_Quantity_Calculator_Register_Hooks();
	}
	/**
	 * Enqueue scripts admin
	 */
	public function eniture_quantity_calculator_enqueue_admin_scripts()
	{
		wp_enqueue_style( $this->plugin_name.'-admin', plugin_dir_url( plugin_dir_path( __FILE__ ) ) . 'admin/css/en-quantity-calculator-admin.css', array(), ENITURE_QUANTITY_CALCULATOR_VERSION, 'all' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( plugin_dir_path( __FILE__ ) ) . 'admin/js/en-quantity-calculator-admin.js', array( 'jquery' ), ENITURE_QUANTITY_CALCULATOR_VERSION, false );
		wp_localize_script($this->plugin_name, 'eniture_quantity_calculator_obj', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'eniture_quantity_calculator_admin_nonce' => wp_create_nonce( 'eniture-qc-ajax-nonce-admin' ),
		));
	}
	/**
	 * Enqueue scripts public
	 */
	public function eniture_quantity_calculator_enqueue_public_scripts()
	{
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( plugin_dir_path( __FILE__ ) ) . 'public/css/en-quantity-calculator-public.css', array(), ENITURE_QUANTITY_CALCULATOR_VERSION, 'all' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( plugin_dir_path( __FILE__ ) ) . 'public/js/en-quantity-calculator-public.js', array( 'jquery' ), ENITURE_QUANTITY_CALCULATOR_VERSION, false );
		wp_localize_script($this->plugin_name, 'eniture_quantity_calculator_obj', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
						'eniture_quantity_calculator_public_nonce' => wp_create_nonce( 'eniture-qc-ajax-nonce-public' ),
        ));
	}

	public function eniture_quantity_calculator_load(){
		
		if (is_admin()) {
			// register admin hooks
			$this->hooks_obj->eniture_quantity_calculator_register_admin_hooks();

			// create admin page and product detail options
			add_filter('woocommerce_get_settings_pages', array($this, 'eniture_quantity_calculator_admin_section'), 10, 1);
			require_once plugin_dir_path( __FILE__ ) . 'class-en-quantity-calculator-product-detail-options.php';
			new Eniture_Quantity_Calculator_Product_Detail();
		}

		// register public hooks
		$this->hooks_obj->eniture_quantity_calculator_register_public_hooks();
	}

	public function eniture_quantity_calculator_admin_section($settings){
		include plugin_dir_path( __FILE__ ) . 'class-en-quantity-calculator-tabs.php';
		return $settings;
	}

}