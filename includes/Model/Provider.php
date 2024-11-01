<?php
/**
 * Provider Model class.
 *
 * @since 1.0.0
 * @package  namespace SmartSMTP\Model\Provider
 */

namespace SmartSMTP\Model;

/**
 *  Provider class for Wpeverest stmp.
 *
 * @since 1.0.0
 */
class Provider {

	/**
	 * Email logs table name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $table_name;
	/**
	 * The name of the database connection to use.
	 *
	 * @var wpdb
	 */
	protected $con;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;

		$this->con = $wpdb;
	}

	/**
	 * Get the config data type.
	 *
	 * @since 1.0.0
	 */
	public function get_current_provider_type() {
		return get_option( 'smart_smtp_provider_type', 'default' );
	}

	/**
	 * Set the mail provider type.
	 *
	 * @since 1.0.0
	 * @param mixed $data The provider data.
	 */
	public function set_provider_type( $data ) {
		return update_option( 'smart_smtp_provider_type', $data );
	}

	/**
	 * Get the provider type.
	 *
	 * @since 1.0.0
	 */
	public function get_provider_type() {
		$provider_type = get_option( 'smart_smtp_provider_type', '' );
		return $provider_type;
	}

	/**
	 * Get the config params.
	 *
	 * @since 1.0.0
	 * @param string $provider_type The data provider type.
	 */
	public function get_mail_config( $provider_type ) {

		if ( empty( $provider_type ) ) {
			$provider_type = $this->get_provider_type();
		}
		if ( '' === $provider_type ) {
			$provider_type = 'default';
		}
			return array_merge( get_option( 'smart_smtp_' . $provider_type . '_configuration', array( 'providerType' => 'default' ) ), array( 'smtp_active_provider_type' => $this->get_provider_type() ) );
	}

	/**
	 * Update the mail config params.
	 *
	 * @since 1.0.0
	 * @param mixed $params The mail config params.
	 */
	public function update_mail_config( $params = array() ) {
		$config_updator = update_option( 'smart_smtp_' . $params['providerType'] . '_configuration', $params );
		return $config_updator;
	}

	/**
	 * Set the active provider type.
	 *
	 * @since 0
	 *
	 * @param  [type] $provider_type The provider type.
	 * @param  [type] $is_checked set or reset the provider type action.
	 */
	public function set_active_provider( $provider_type, $is_checked ) {
		if ( ! $is_checked ) {
			$provider_type = '';
		}

		return update_option( 'smart_smtp_provider_type', $provider_type );
	}

}
