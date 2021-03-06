<?php
/**
 * @author  ThimPress
 * @package LearnPress/Admin/Classes
 * @version 1.0
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'LP_Admin' ) ) {
	/**
	 * Class LP_Admin
	 */
	class LP_Admin {
		/**
		 *  Constructor
		 */
		public function __construct() {
			$this->includes();
			add_action( 'admin_print_scripts', array( $this, 'plugin_js_settings' ) );
			//add_action( 'init', array( $this, 'include_update' ) );
			add_action( 'template_redirect', array( $this, '_redirect' ) );
			add_action( 'delete_user', array( $this, 'delete_user_data' ) );
			add_action( 'delete_user_form', array( $this, 'delete_user_form' ) );
		}

		function delete_user_form() {
			// What should be displayed here?
		}

		/**
		 * Delete records related user being deleted in other tables
		 *
		 * @param int $user_id
		 */
		function delete_user_data( $user_id ) {
			learn_press_delete_user_data( $user_id );
		}

		/**
		 * Output common js settings in admin
		 *
		 * @since 0.9.4
		 */
		public function plugin_js_settings() {
			static $did = false;
			if ( $did || !is_admin() ) return;
			$js = array(
				'ajax'       => admin_url( 'admin-ajax.php' ),
				'plugin_url' => learn_press_plugin_url(),
				'siteurl'    => home_url(),
				'localize'   => array(
					'button_ok'     => __( 'OK', 'learnpress' ),
					'button_cancel' => __( 'Cancel', 'learnpress' ),
					'button_yes'    => __( 'Yes', 'learnpress' ),
					'button_no'     => __( 'No', 'learnpress' )
				)
			);
			LP_Assets::add_param( $js, false, 'learn-press-global', 'LP_Settings' );
			if ( LP_Settings::instance()->get( 'debug' ) == 'yes' ) {
				LP_Assets::add_var( 'LEARN_PRESS_DEBUG', 'true', '__all' );
			}
			$did = true;
		}

		/**
		 * Redirect to admin settings page
		 */
		public function _redirect() {
			$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
			if ( 'learn_press_settings' == $page ) {
				$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';
				$tabs        = learn_press_settings_tabs_array();

				if ( !$current_tab || ( $tabs && empty( $tabs[$current_tab] ) ) ) {
					if ( $tabs ) {
						$tab_keys    = array_keys( $tabs );
						$current_tab = reset( $tab_keys );
						wp_redirect( admin_url( 'options-general.php?page=learn_press_settings&tab=' . $current_tab ) );
						exit();
					}
				}
			}
		}

		/**
		 * Include all classes and functions used for admin
		 */
		public function includes() {
			// Common function used in admin
			include_once( 'lp-admin-functions.php' );
			// Admin actions
			include_once( 'lp-admin-actions.php' );
			//Ajax Class
			include_once( 'class-lp-admin-ajax.php' );
			// Admin menu
			include_once( 'class-lp-admin-menu.php' );


		}
	}

	new LP_Admin();
}