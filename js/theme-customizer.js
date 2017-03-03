jQuery(document).ready(function($) {

// Update the publication header color in real time...
	wp.customize( 'header_color', function( value ) {
		value.bind( function( newval ) {
			$( '.pubtable-header' ).css('background-color', newval );
		} );
	} );

    // Update the publication header color in real time...
	wp.customize( 'even_color', function( value ) {
		value.bind( function( newval ) {
			$( ' .publication-table tr.pubrow:nth-child(2n+0)' ).css( 'background-color', newval );
		} );
	} );
    
    
     // Update the publication form color in real time...
	wp.customize( 'form_color', function( value ) {
		value.bind( function( newval ) {
			$( 'div.search-group' ).css('background-color', newval );
		} );
	});
    
    // Update the publication header color in real time...
	wp.customize( 'header_text_color', function( value ) {
		value.bind( function( newval ) {
			$( '.pubtable-header > a:link,.pubtable-header > a:visited, .pubtable-header > a:hover' ).css('color', newval );
		} );
	} );
    
    
     })