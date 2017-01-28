<?php
// Cleaner variable printing
if( ! function_exists( 'view' ) ) {

	function view( $variable ) {

		echo '<pre>';
		print_r( $variable );
		echo '</pre>';
	}
}
// Easy access to widget id
function dev_get_widget_id( $widget_instance ) {

    // Check if the widget is already saved or not. 
    if ( $widget_instance->number == "__i__" ){ 
    	echo '<strong>Widget ID is</strong>: Please save the widget first!';
    } 
    else {
 
		//get the widget ID
		echo '<strong>Widget ID is: </strong>' . $widget_instance->id;       
    }
}
add_action( 'in_widget_form', 'dev_get_widget_id' );