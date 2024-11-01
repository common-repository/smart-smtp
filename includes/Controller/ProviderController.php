<?php
/**
 * ProviderController class.
 *
 * @since 1.0.0
 * @package  namespace SmartSMTP\Controller
 */

namespace SmartSMTP\Controller;

use SmartSMTP\Helper;
use SmartSMTP\Model\Provider;
use SmartSMTP\Model\MailLogs;
use SmartSMTP\Model\MailProviderModel;
use SmartSMTP\Services\Providers\Brevo\Mailer as BrevoMailer;
use SmartSMTP\Services\Providers\DefaultSmtp\Mailer as DefaultMailer;
use SmartSMTP\Services\Providers\Other\Mailer as OtherMailer;


/**
 * ProviderController.
 *
 * @since 1.0.0
 */
class ProviderController {
	/**
	 * Provider object.
	 *
	 * @since 1.0.0
	 */
	protected $provider;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->provider = new Provider();
	}

	/**
	 * Get the selected provider.
	 *
	 * @since 1.0.0
	 */
	public function get_current_provider_type() {

		$provider_type = $this->provider->get_current_provider_type();

		return new \WP_REST_Response(
			$provider_type,
			200
		);
	}

	/**
	 * Get provider type.
	 *
	 * @since 1.0.0
	 */
	public function get_provider_type() {
		$provider_type = $this->provider->get_provider_type();
		$provider_type = '' !== $provider_type ? $provider_type : 'default';

		return new \WP_REST_Response(
			$provider_type,
			200
		);
	}

	/**
	 * Function to save the config data.
	 *
	 * @since 1.0.0
	 * @param object|array $request The form data.
	 */
	public function save_mail_config( $request ) {
		if ( ! isset( $request['form_data'] ) || empty( $request['form_data'] ) ) {

			return new \WP_REST_Response(
				array(
					'message' => esc_html__( 'Form data not found.', 'smart-smtp' ),
				),
				400
			);
		}

		$form_data = $request['form_data'];

		if ( ! isset( $form_data['mail_config'] ) || empty( $form_data['mail_config'] ) ) {

			return new \WP_REST_Response(
				array(
					'message' => esc_html__( 'Mail Configuration data is empty.', 'smart-smtp' ),
				),
				400
			);
		}

		if ( ! isset( $form_data['mail_config']['providerType'] ) || empty( isset( $form_data['mail_config']['providerType'] ) ) ) {

			return new \WP_REST_Response(
				array(
					'message' => esc_html__( 'Missing provider type!!', 'smart-smtp' ),
				),
				400
			);
		}

		if ( isset( $form_data['mail_config']['smtp_user_password'] ) ) {
			$form_data['mail_config']['smtp_user_password'] = Helper::crypt_the_string( $form_data['mail_config']['smtp_user_password'] );
		}

		$sanitized_form_data = Helper::sanitize_input_fields( $form_data ['mail_config'] );
		$is_validate         = true;

		if ( isset( $sanitized_form_data['providerType'] ) && 'brevo' === $sanitized_form_data['providerType'] ) {
			if ( isset( $sanitized_form_data['smtp_api_key'] ) && ! empty( $sanitized_form_data['smtp_api_key'] ) ) {
				$res         = BrevoMailer::check_brevo_api_key( ( $sanitized_form_data['smtp_api_key'] ) );
				$is_validate = 200 === $res['code'];
			}
		}

		if ( ! $is_validate ) {
			return new \WP_REST_Response(
				array(
					'message' => esc_html__( 'Api key authentication failed.', 'smart-smtp' ),
				),
				$res['code']
			);
		}

		if ( isset( $sanitized_form_data['smtp_is_active'] ) ) {
			$is_checked = $sanitized_form_data['smtp_is_active'];

			$provider_type = $sanitized_form_data['providerType'];
			$res           = $this->provider->set_active_provider( $provider_type, $is_checked );

			unset( $sanitized_form_data['smtp_is_active'] );
		}

		$res = $this->provider->update_mail_config( $sanitized_form_data );

		if ( $res ) {

			return new \WP_REST_Response(
				array(
					'message' => esc_html__( 'Mail provider setting saved successfully.', 'smart-smtp' ),
				),
				200
			);
		} else {

			return new \WP_REST_Response(
				array(
					'message' => esc_html__( 'Mail configuration is up to date.', 'smart-smtp' ),
				),
				200
			);
		}
	}

	/**
	 * Function to get the config data.
	 *
	 * @since 1.0.0
	 * @param array $params  The extra params.
	 */
	public function get_mail_config( $params = array() ) {
		$provider_type = isset( $params['providerType'] ) ? sanitize_text_field( $params['providerType'] ) : '';

		$res = $this->provider->get_mail_config( $provider_type );

		$is_configured = array(
			'default' => $this->is_mailer_complete( 'default' ),
			'brevo'   => $this->is_mailer_complete( 'brevo' ),
			'other'   => $this->is_mailer_complete( 'other' ),
		);

		$res = array_merge( $res, array( 'is_configured' => $is_configured ) );

		$res['smtp_is_active'] = $provider_type === $res['smtp_active_provider_type'];

		if ( isset( $res['smtp_user_password'] ) ) {
			$res['smtp_user_password'] = Helper::crypt_the_string( $res['smtp_user_password'], 'd' );
		}

		return new \WP_REST_Response(
			$res,
			200
		);
	}

	/**
	 * Func to check the mail is complete or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 * @param string $provider_type The provider type.
	 */
	public function is_mailer_complete( $provider_type = '' ) {
		$res = false;

		if ( '' === $provider_type ) {
			$provider_type = $this->provider->get_provider_type();
			if ( '' === $provider_type || empty( $provider_type ) ) {
				return $res;
			}
		}

		switch ( $provider_type ) {
			case 'brevo':
				$res = BrevoMailer::is_mailer_complete();
				break;
			case 'other':
				$res = OtherMailer::is_mailer_complete();
				break;
			case 'default':
				$res = DefaultMailer::is_mailer_complete();
		}

		return $res;
	}
}
