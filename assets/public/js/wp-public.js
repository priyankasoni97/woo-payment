/**
 * jQuery public custom script file.
 */
jQuery( document ).ready( function( $ ) {
	'use strict';

	// event for accept only numbers and format card number.
	$('#wp_card_number').keypress( function (e) {    
    
		var charCode = (e.which) ? e.which : e.keyCode    

		if (String.fromCharCode( charCode ).match(/[^0-9]/g) ) {

			return false;   
		}
		$( this ).val( $(this).val().toCardFormat() );
			                    
	} );  

	// event for accept only numbers in expiry month  & year and card CVV.
	$( document ).on( 'keypress','#wp_expiry_month, #wp_expiry_year, #wp_cvv', function (e) {
		var charCode = (e.which) ? e.which : e.keyCode    

		if (String.fromCharCode( charCode ).match(/[^0-9]/g) ) {

			return false;   
		}
	} );

	// Function for format card number.
	String.prototype.toCardFormat = function() {
		return this.replace( /[^0-9]/g, "" ).substring( 0, 16 ).split('').reduce( cardFormat, '' );
		function cardFormat( str, l, i ) {
			return str + ( (!i || ( i % 4 ) ) ? '' : ' ' ) + l;
		}
	};
} );