jQuery( document ).ready( function( $ ){

	$( '.mjj-wimd-table button.open-show-columns' ).on( 'click', function(){
		var table = $( this ).parent( '.mjj-wimd-table' );
		$( table ).find( '.show-columns' ).slideToggle( '200' );
		
		if( $( this ).hasClass( 'closed' ) ){
			$( this ).html( 'Hide columns' ).addClass( 'open' ).removeClass( 'closed' );
		}
		else{
			$( this ).html( 'Show columns' ).addClass( 'closed' ).removeClass( 'open' );
		}
		console.log( $( table ).find( '.show-columns' ) );
	});
});