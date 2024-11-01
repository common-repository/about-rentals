<?php
define( 'PW_SELECT2_URL', plugin_dir_url( __FILE__ ) );
define( 'PW_SELECT2_VERSION', '2.0.4' );
function pw_select2_enqueue() {
	wp_enqueue_script( 'pw-select2-field-js', PW_SELECT2_URL . 'js/select2/select2.min.js', array( 'jquery-ui-sortable' ), '3.5.1' );
	wp_enqueue_script( 'pw-select2-field-init', PW_SELECT2_URL . 'js/select2-init.js', array( 'pw-select2-field-js' ), PW_SELECT2_VERSION );
	wp_enqueue_style( 'pw-select2-field-css', PW_SELECT2_URL . 'js/select2/select2.css', array(), '3.5.1' );
	wp_enqueue_style( 'pw-select2-field-mods', PW_SELECT2_URL . 'css/select2.css', array(), PW_SELECT2_VERSION );
}
function pw_select2_render( $field, $value, $object_id, $object_type, $field_type_object ) {
	pw_select2_enqueue();

	echo $field_type_object->select( array(
		'class'   => 'cmb2_select select2',
		'options' => '<option></option>' . $field_type_object->concat_items(),
		'desc'    => $field->args( 'desc' ) && ! empty( $value ) ? $field_type_object->_desc( true ) : '',
		'data-placeholder' => $field->args( 'desc' ),
	) );
}
add_filter( 'cmb2_render_pw_select', 'pw_select2_render', 10, 5 );
function pw_multiselect_render( $field, $value, $object_id, $object_type, $field_type_object ) {
	pw_select2_enqueue();
	$options = array();
	foreach ( (array) $field->options() as $opt_value => $opt_label ) {
		$options[] = array(
			'id' => $opt_value,
			'text' => $opt_label
		);
	}
	wp_localize_script( 'pw-select2-field-init', $field_type_object->_id() . '_data', $options );
	echo $field_type_object->input( array(
		'type'  => 'hidden',
		'class' => 'select2',
		'desc'  => $field->args( 'desc' ) && ! empty( $value ) ? $field_type_object->_desc( true ) : '',
		'data-placeholder' => esc_attr( $field->args( 'description' ) ),
	) );
}
add_filter( 'cmb2_render_pw_multiselect', 'pw_multiselect_render', 10, 5 );
function pw_multiselect_escape( $check, $meta_value ) {
	return ! empty( $meta_value ) ? implode( ',', $meta_value ) : $check;
}
add_filter( 'cmb2_types_esc_pw_multiselect', 'pw_multiselect_escape', 10, 2 );
function pw_multiselect_sanitize( $check, $meta_value ) {
	if ( ! empty( $meta_value ) ) {
		return explode( ',', $meta_value );
	}
	return $check;
}
add_filter( 'cmb2_sanitize_pw_multiselect', 'pw_multiselect_sanitize', 10, 2 );