<?php

/**
 * Class LP_Abstract_Assets
 *
 * @author  ThimPress
 * @package LearnPress/Classes
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class LP_Abstract_Assets {

	/**
	 * Styles
	 *
	 * @var array
	 */
	protected static $styles = array();

	/**
	 * Scripts
	 *
	 * @var array
	 */
	protected static $scripts = array();

	/**
	 * Localize scripts
	 *
	 * @var array
	 */
	protected static $wp_localize_scripts = array();

	/**
	 * Localized flag
	 *
	 * @var bool
	 */
	protected static $localized = false;

	/**
	 * @var int
	 */
	protected static $id = 0;

	/**
	 * Constructor
	 */
	function __construct() {

	}

	/**
	 * Init Asset
	 */
	static function init() {
		self::$id = is_admin() ? 0 : 1;
		$class = get_called_class();
		if( self::$id ) {
			add_action( 'wp_enqueue_scripts', array( $class, 'load_scripts' ) );
			add_action( 'wp_print_scripts', array( $class, 'localize_printed_scripts' ), 5 );
			add_action( 'wp_print_footer_scripts', array( $class, 'localize_printed_scripts' ), 5 );
		}else{
			add_action( 'admin_enqueue_scripts', array( $class, 'load_scripts' ) );
			add_action( 'admin_print_scripts', array( $class, 'localize_printed_scripts' ), 5 );
			add_action( 'admin_print_footer_scripts', array( $class, 'localize_printed_scripts' ), 5 );
		}
	}

	/**
	 * register script
	 *
	 * @param string  $handle
	 * @param string  $src
	 * @param array   $deps
	 * @param string  $version
	 * @param boolean $in_footer
	 */
	static function add_script( $handle, $src, $deps = array( 'jquery' ), $version = LEARNPRESS_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $src, $deps, $version, $in_footer );
	}

	/**
	 * register style
	 *
	 * @param string $handle
	 * @param string $src
	 * @param array  $deps
	 * @param string $version
	 * @param string $media
	 */
	static function add_style( $handle, $src, $deps = array(), $version = LEARNPRESS_VERSION, $media = 'all' ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * enqueue script
	 *
	 * @param string  $handle
	 * @param string  $src
	 * @param array   $deps
	 * @param string  $version
	 * @param boolean $in_footer
	 */
	static function enqueue_script( $handle, $src = '', $deps = array( 'jquery' ), $version = LEARNPRESS_VERSION, $in_footer = true ) {
		if ( !in_array( $handle, self::$scripts ) && $src ) {
			self::add_script( $handle, $src, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * enqueue style
	 *
	 * @param string $handle
	 * @param string $src
	 * @param array  $deps
	 * @param string $version
	 * @param string $media
	 */
	static function enqueue_style( $handle, $src = '', $deps = array(), $version = LEARNPRESS_VERSION, $media = 'all' ) {
		if ( !in_array( $handle, self::$styles ) && $src ) {
			self::add_style( $handle, $src, $deps, $version, $media );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * add translate text
	 *
	 * @param array $localize
	 */
	static function add_localize( $key, $localize = null, $handle = 'learn-press-js' ) {
		if ( is_array( $key ) ) {
			foreach ( $key as $k => $v ) {
				self::add_localize( $k, $v, $handle );
			}
		} elseif ( is_string( $key ) && strlen( $key ) ) {
			if ( !$handle ) $handle = 'learn-press-js';
			if ( empty( self::$wp_localize_scripts[$handle] ) ) self::$wp_localize_scripts[$handle] = array();
			self::$wp_localize_scripts[$handle][$key] = $localize;
		}
	}

	/**
	 * Localize script
	 *
	 * @param  mixed $handle
	 */
	private static function localize_script( $handle ) {
		$data = !empty( self::$wp_localize_scripts[$handle] ) ? self::$wp_localize_scripts[$handle] : false;
		if ( wp_script_is( $handle ) && $data ) {
			$name = str_replace( '-', '_', $handle ) . '_localize';
			unset( self::$wp_localize_scripts[$handle] );
			wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
		}
	}

	/**
	 * Load Script
	 */
	static function load_scripts() {
	}

	static function localize_printed_scripts() {
		if ( self::$scripts ) foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}
}