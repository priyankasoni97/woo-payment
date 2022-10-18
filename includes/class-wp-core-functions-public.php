<?php
/**
 * The file that defines the core plugin class.
 *
 * A class definition that holds all the hooks regarding all the custom functionalities.
 *
 * @link       https://github.com/priyankasoni97/
 * @since      1.0.0
 *
 * @package    Woo_Payment_Public
 * @subpackage Woo_Payment_Public/includes
 */

/**
 * The core plugin class.
 *
 * A class definition that holds all the hooks regarding all the custom functionalities.
 *
 * @since      1.0.0
 * @package    Woo_Payment
 * @author     Priyanka Soni <priyanka.soni@cmsminds.com>
 */
class WP_Core_Functions_Public {
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load all the hooks here.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_callback' ) );
		add_action( 'woocommerce_edit_account_form', array( $this, 'woocommerce_edit_account_form_callback' ) );
		add_action( 'woocommerce_save_account_details_errors', array( $this, 'woocommerce_save_account_details_errors_callback' ), 10, 1 );
		add_action( 'woocommerce_save_account_details', array( $this, 'woocommerce_save_account_details_callback' ), 10, 1 );
	}

	/**
	 * Enqueue scripts for public end.
	 */
	public function wp_enqueue_scripts_callback() {
		// Custom public script.
		wp_enqueue_script(
			'wp-public-script',
			PLUGIN_URL . 'assets/public/js/wp-public.js',
			array( 'jquery' ),
			filemtime( PLUGIN_PATH . 'assets/public/js/wp-public.js' ),
			true
		);
	}

	/**
	 * Function for add custom fields on woocommerce register form.
	 */
	public function woocommerce_edit_account_form_callback() {
		$user_id      = get_current_user_id();
		$card_number  = chunk_split( get_user_meta( $user_id, 'wp_card_number', true ), 4, ' ' );
		$expiry_month = get_user_meta( $user_id, 'wp_card_expiry_month', true );
		$expiry_year  = get_user_meta( $user_id, 'wp_card_expiry_year', true );
		$cvv          = get_user_meta( $user_id, 'wp_cvv', true );
		?>
		<fieldset>
			<legend>Add Payment Method</legend>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="wp_card_number"><?php esc_html_e( 'Card Number*', 'woo-payment' ); ?></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" maxlength="19" name="wp_card_number" id="wp_card_number" value="<?php echo esc_attr( $card_number ); ?>" placeholder="1234 1234 1234 1234"/>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
				<label for="wp_expiry_month"><?php esc_html_e( 'Expiry month*', 'woo-payment' ); ?></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" maxlength="2" name="wp_expiry_month" id="wp_expiry_month" value="<?php echo esc_attr( $expiry_month ); ?>" placeholder="MM"/>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
				<label for="wp_expiry_year"><?php esc_html_e( 'Expiry year*', 'woo-payment' ); ?></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" maxlength="2" name="wp_expiry_year" id="wp_expiry_year" value="<?php echo esc_attr( $expiry_year ); ?>" placeholder="YY"/>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="wp_cvv"><?php esc_html_e( 'CVV*', 'woo-payment' ); ?></label>
				<input type="text" class="woocommer-ce-Input woocommerce-Input--text input-text" maxlength="4" inputmode="numeric" name="wp_cvv" id="wp_cvv" value="<?php echo esc_attr( $cvv ); ?>" placeholder="CVV"/>
			</p>
		</fieldset>	
		<?php
	}

	/**
	 * Function for validating user profile fields.
	 *
	 * @param object $args Holds validation argument.
	 */
	public function woocommerce_save_account_details_errors_callback( $args ) {
		// Return error if card number is empty.
		$card_number  = filter_input( INPUT_POST, 'wp_card_number', FILTER_SANITIZE_NUMBER_INT );
		$expiry_month = filter_input( INPUT_POST, 'wp_expiry_month', FILTER_SANITIZE_NUMBER_INT );
		$expiry_year  = filter_input( INPUT_POST, 'wp_expiry_year', FILTER_SANITIZE_NUMBER_INT );
		$cvv          = filter_input( INPUT_POST, 'wp_cvv', FILTER_SANITIZE_NUMBER_INT );
		$expires      = DateTime::createFromFormat( 'my', $expiry_month . $expiry_year );
		$now          = new DateTime();
		$valid_cvv    = '/^[0-9]{3,4}$/';

		// Check if card number is empty.
		if ( empty( $card_number ) ) {
			$args->add( 'error', __( '<strong>Card Number</strong> is required field.', 'woo-payment' ), '' );
		}

		// Return error if expiry month is empty.
		if ( empty( $expiry_month ) ) {
			$args->add( 'error', __( '<strong>Expiry Month</strong> is required field.', 'woo-payment' ), '' );
		}

		// Return error if expiry year is empty.
		if ( empty( $expiry_year ) ) {
			$args->add( 'error', __( '<strong>Expiry Year</strong> is required field.', 'woo-payment' ), '' );
		}

		// Return error if expiry CVV is empty.
		if ( empty( $cvv ) ) {
			$args->add( 'error', __( '<strong>CVV</strong></strong> is required field.', 'woo-payment' ), '' );
		}

		// Check if card number is valid or not.
		if ( ! empty( $card_number ) && ! wp_validate_card_number( $card_number ) ) {
			$args->add( 'error', __( '<strong>Card Number</strong> is not valid.', 'woo-payment' ), '' );
		}

		// check if card expriry date is valid.
		if ( ! empty( $expiry_month ) && ! empty( $expiry_year ) && $expires < $now ) {
			$args->add( 'error', __( 'Your card is expired.', 'woo-payment' ), '' );
		}

		// Check if cvv is valid or not.
		if ( ! empty( $cvv ) && ! preg_match( $valid_cvv, $cvv ) ) {
			$args->add( 'error', __( 'CVV is not valid.', 'woo-payment' ), '' );
		}
	}

	/**
	 * Function for save custom feilds of woocommerce register form.
	 *
	 * @param int $user_id Holds user's id.
	 */
	public function woocommerce_save_account_details_callback( $user_id ) {
		$card_number  = filter_input( INPUT_POST, 'wp_card_number', FILTER_SANITIZE_NUMBER_INT );
		$expiry_month = filter_input( INPUT_POST, 'wp_expiry_month', FILTER_SANITIZE_NUMBER_INT );
		$expiry_year  = filter_input( INPUT_POST, 'wp_expiry_year', FILTER_SANITIZE_NUMBER_INT );
		$cvv          = filter_input( INPUT_POST, 'wp_cvv', FILTER_SANITIZE_NUMBER_INT );

		// Add card number value to users profile.
		if ( isset( $card_number ) ) {
			update_user_meta( $user_id, 'wp_card_number', $card_number );
		}

		// Add card expiry month to user profile.
		if ( isset( $expiry_month ) ) {
			update_user_meta( $user_id, 'wp_card_expiry_month', $expiry_month );
		}

		// Add card expiry year to user profile.
		if ( isset( $expiry_year ) ) {
			update_user_meta( $user_id, 'wp_card_expiry_year', $expiry_year );
		}

		// Add card CVV to user profile.
		if ( isset( $cvv ) ) {
			update_user_meta( $user_id, 'wp_cvv', $cvv );
		}
	}
}
