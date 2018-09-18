<?php
/**
 * WooCommerce Plugin Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\PluginFramework\v5_2_2\Plugin;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_2_2 as Framework;

if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\v5_2_2\\Plugin\\Connection_Handler' ) ) :

/**
 * The Connection Handler.
 *
 * This class is responsible to provide a common standard for plugins that need to connect to an external service (typically an API) to function.
 * Child implementations should use this class to provide common methods to connect to the external service and retrieve the connection state.
 *
 * @since 5.3.0-dev
 */
abstract class Connection_Handler {


	/** @var Framework\SV_WC_Plugin main plugin class */
	private $plugin;

	/** @var string the plugin ID */
	private $connection_key;

	/** @var bool whether the plugin is connected to an external service */
	protected $is_connected;


	/**
	 * Initializes the Connection Handler and sets the default connection state.
	 *
	 * @since 5.3.0-dev
	 *
	 * @param Framework\SV_WC_Plugin $plugin main plugin class
	 */
	public function __construct( Framework\SV_WC_Plugin $plugin ) {

		// parent plugin
		$this->plugin = $plugin;

		// option key name for storing connection status
		$this->connection_key = $plugin->get_id() . '_connected';

		// default state
		$this->is_connected = $this->is_connected();
	}


	/**
	 * Gets the name of the service the plugin connects to.
	 *
	 * @since 5.3.0-dev.
	 *
	 * @return string e.g. "Google", "MailChimp", etc.
	 */
	abstract public function get_service_name();


	/**
	 * Connects the plugin to an external service.
	 *
	 * Child classes should implement the necessary logic (e.g. connect to an API) before returning the parent method.
	 *
	 * @since 5.3.0-dev
	 *
	 * @return bool
	 */
	public function connect() {

		update_option( $this->connection_key, 'yes' );

		$this->is_connected = true;

		return $this->is_connected;
	}


	/**
	 * Disconnects the plugin from an external service.
	 *
	 * Child classes should implement the necessary logic (e.g. disconnect from an API) before returning the parent method.
	 *
	 * @since 5.3.0-dev
	 *
	 * @return bool
	 */
	public function disconnect() {

		update_option( $this->connection_key, 'no' );

		$this->is_connected = false;

		return $this->is_connected;
	}


	/**
	 * Determines whether the plugin is connected to an external service.
	 *
	 * @since 5.3.0-dev
	 *
	 * @return bool
	 */
	public function is_connected() {

		return 'yes' === get_option( $this->connection_key );
	}


	/**
	 * Determines whether the plugin is disconnected from an external service.
	 *
	 * @since 5.3.0-dev
	 *
	 * @return bool
	 */
	public function is_disconnected() {

		return ! $this->is_connected();
	}


	/**
	 * Determines whether the connection has some errors to display.
	 *
	 * @since 5.3.0-dev
	 *
	 * @return bool
	 */
	public function has_errors() {

		$errors = $this->get_errors();

		return ! empty( $errors );
	}


	/**
	 * Returns errors that may be shown when connection was unsuccessful.
	 *
	 * @since 5.3.0-dev
	 *
	 * @return array|string[] associative array of error codes and messages or array of string messages
	 */
	public function get_errors() {

		return array();
	}


	/**
	 * Gets the plugin main instance.
	 *
	 * @since 5.3.0-dev
	 *
	 * @return Framework\SV_WC_Plugin
	 */
	protected function get_plugin() {

		return $this->plugin;
	}


}

endif;