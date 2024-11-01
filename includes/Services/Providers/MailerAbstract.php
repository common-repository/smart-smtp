<?php
/**
 * SmartSMTP Provider class.
 *
 * @package  namespace SmartSMTP\Services\Providers
 *
 * @since 1.0.0
 */

namespace SmartSMTP\Services\Providers;

use SmartSMTP\SmartSMTP;
use SmartSMTP\Model\Provider;

/**
 * Base Mailer.
 *
 * @since 1.0.0
 */
class MailerAbstract {

	/**
	 * Easymail smtp mailer.
	 *
	 * @since 0
	 * @var [type] $php_mailer base php mailer.
	 */
	protected $php_mailer = null;
	/**
	 * Set the email headers.
	 *
	 * @since 1.0.0
	 *
	 * @param array $headers List of key=>value pairs.
	 */
	public function set_headers( $headers ) {

		foreach ( $headers as $header ) {
			$name  = isset( $header[0] ) ? $header[0] : false;
			$value = isset( $header[1] ) ? $header[1] : false;

			if ( empty( $name ) || empty( $value ) ) {
				continue;
			}

			$this->set_header( $name, $value );
		}
	}

	/**
	 * Set individual header key=>value pair for the email.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The name.
	 * @param string $value The value.
	 */
	public function set_header( $name, $value ) {

		$name = sanitize_text_field( $name );

		$this->headers[ $name ] = $value;
	}
}
