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

	/** @var string optional section name (without the prepending # pound sign) that the documentation uses for configuring the external service */
	protected $documentation_url_section = '';

	/** @var bool whether the plugin is connected to an external service */
	protected $is_connected;


	/**
	 * Sets up the Connection Handler.
	 *
	 * @since 5.3.0-dev
	 *
	 * @param Framework\SV_WC_Plugin $plugin main plugin class
	 */
	public function __construct( Framework\SV_WC_Plugin $plugin ) {

		// parent plugin
		$this->plugin = $plugin;
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
	 * Makes and returns an ID for the service the plugin connects to.
	 *
	 * @since 5.3.0-dev.1
	 *
	 * @return string e.g. "Google" becomes 'wc_<plugin_id>_google', "MailChimp" becomes 'wc_<plugin_id>_mailchimp"
	 */
	public function get_service_id() {

		$plugin_id    = $this->get_plugin()->get_id();
		$service_name = $this->get_service_name();
		$service_id   = "wc_{$plugin_id}_{$service_name}";

		return str_replace( '-', '_', sanitize_title( $service_id ) );
	}


	/**
	 * Connects the plugin to an external service.
	 *
	 * Child classes should implement the necessary logic (e.g. connect to an API) before returning the parent method.
	 *
	 * @since 5.3.0-dev
	 *
	 * @param null|mixed|array $args optional arguments that implementations could use to pass crendentials for connecting
	 * @return bool
	 */
	public function connect( $args = null ) {

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
	 * @param null|mixed|array $args optional arguments that may be required when disconnecting
	 * @return bool
	 */
	public function disconnect( $args = null ) {

		$this->is_connected = false;

		return $this->is_connected;
	}


	/**
	 * Determines whether the plugin is connected to an external service.
	 *
	 * @since 5.3.0-dev
	 *
	 * @param null|mixed|array $args optional argument that could be used in implementations when a plugin may connect to multiple services
	 * @return bool
	 */
	public function is_connected( $args = null ) {

		return true === $this->is_connected;
	}


	/**
	 * Determines whether the plugin is disconnected from an external service.
	 *
	 * @since 5.3.0-dev
	 *
	 *
	 * @param null|mixed|array $args optional argument that could be used in implementations when a plugin may connect to multiple services
	 * @return bool
	 */
	public function is_disconnected( $args = null ) {

		return ! $this->is_connected();
	}


	/**
	 * Gets a connection error message.
	 *
	 * The error should point out why the connection failed.
	 *
	 * @since 1.1.0-dev.1
	 *
	 * @param null|mixed $args optional arguments
	 * @return null|string
	 */
	public function get_service_error( $args = null ) {

		$status = $this->get_service_status( $args );

		return $this->has_service_error( $status, $args ) ? $status->message : null;
	}


	/**
	 * Determines whether the service has a connection issue.
	 *
	 * @since 5.3.0-dev
	 *
	 * @param \stdClass $status response object (optional, will get the service status if unspecified)
	 * @param mixed|null optional arguments to get the service status
	 * @return bool
	 */
	public function has_service_error( $status = null, $args = null ) {

		$status = null === $status ? $this->get_service_status( $args ) : $status;

		return (int) $status->code >= 300;
	}


	/**
	 * Gets a service status response.
	 *
	 * @param null|mixed $args optional arguments
	 * @return \stdClass the returned object should have 2 mandatory properties `code` and `message`
	 */
	abstract public function get_service_status( $args = null );


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


	/**
	 * Gets the documentation URL to help setting a connection with the service.
	 *
	 * The abstract method returns the plugin's documentation URL by default, but this could be a subsection of the general documentation, or a different page.
	 *
	 * @since 5.3.0-dev
	 *
	 * @return string URL
	 */
	public function get_documentation_url() {

		$section = ! empty( $this->documentation_url_section ) ? "#{$this->documentation_url_section}" : '';

		return sprintf( '%s' . $section, $this->get_plugin()->get_documentation_url() );
	}


}

endif;