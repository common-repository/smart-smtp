<?php
/**
 * TestMailController class.
 *
 * @since 1.0.0
 * @package  namespace SmartSMTP\Controller\TestMailController
 */

 namespace SmartSMTP\Controller;

 use SmartSMTP\Model\TestMail as TestMailModel;
 use SmartSMTP\Services\Services;
 use SmartSMTP\Helper;
 use SmartSMTP\Model\Provider;
 use SmartSMTP\Controller\ProviderController;

/**
 * TestMailController.
 *
 * @since 1.0.0
 */
class TestMailController {
	/**
	 * Test mail object.
	 *
	 * @since 1.0.0
	 */
	protected $test_mail;
	/**
	 * Construtor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->test_mail = new TestMailModel();
	}

	/**
	 * Function to save the data.
	 *
	 * @since 1.0.0
	 * @param object|array $request The form data.
	 */
	public function save_test_mail_config( $request ) {
		if ( ! isset( $request['form_data'] ) || empty( $request['form_data'] ) ) {

			return new \WP_REST_Response(
				array(
					'message' => esc_html__( 'Form data not found.', 'smart-smtp' ),
				),
				400
			);
		}

		$form_data = $request['form_data'];
		if ( ! isset( $form_data['mail_test_config'] ) || empty( $form_data['mail_test_config'] ) ) {

			return new \WP_REST_Response(
				array(
					'message' => esc_html__( 'Send Test Mail Configuration data is empty.', 'smart-smtp' ),
				),
				400
			);
		}

		$sanitized_test_data = Helper::sanitize_input_fields( $form_data ['mail_test_config'] );

		$res = $this->test_mail->update_test_mail_config( $sanitized_test_data );

		try {
			$service       = Services::init();
			$test_response = $service->send_test_mail( $sanitized_test_data );
			$response      = Services::$response_message;
			if ( $test_response ) {
				return new \WP_REST_Response(
					array(
						'message' => isset( $response['message'] ) ? $response['message'] : esc_html__( 'Sent Successfully', 'smart-smtp' ),
					),
					isset( $response['code'] ) && ! empty( $response['code'] ) ? $response['code'] : 200
				);
			} else {
				return new \WP_REST_Response(
					array(
						'message' => isset( $response['message'] ) ? $response['message'] : esc_html__( 'Send Failed!!', 'smart-smtp' ),
					),
					isset( $response['code'] ) && ! empty( $response['code'] ) ? ( strlen(
						$response['code']
					) < 3 ? 400 : $response['code']
					) : 400
				);
			}
		} catch ( Exception $e ) {
			return new \WP_REST_Response(
				array(
					'message' => $e->get_error_message(),
				),
				400
			);
		}
	}

	/**
	 * Function to get the config data.
	 *
	 * @since 1.0.0
	 */
	public function get_test_mail_config() {
		$test_mail_config = $this->test_mail->get_test_mail_config();

		$provider           = new ProviderController();
		$is_mailer_complete = $provider->is_mailer_complete();

		$res = array(
			'test_mail_config'   => $test_mail_config,
			'is_mailer_complete' => $is_mailer_complete,
		);

		return new \WP_REST_Response(
			$res,
			200
		);
	}
}
