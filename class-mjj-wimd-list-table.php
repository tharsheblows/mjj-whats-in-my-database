<?php

// This makes the list tables LET'S SEE IF IT WORKS CORRECTLY

require_once( 'class-mjj-wp-list-table-copy.php' );

class MJJ_WIMD_List_Table extends MJJ_WP_List_Table_Copy {

	public function __construct( $table_name ) {

		$table_name = wp_kses( $table_name, array() );//not allowed nothing

		parent::__construct( [
			'singular' => sprintf( __( '%s column', 'mjj-whats-in-my-database' ), $table_name ),
			'plural'   => sprintf( __( '%s columns', 'mjj-whats-in-my-database' ), $table_name ),
		] );
	}

	public function get_columns() {
		return array(
			'Field' => 'Field',
			'Type' => 'Type',
			'Null' => 'Null',
			'Key' => 'Key',
			'Default' => 'Default',
			'Extra' => 'Extra',
		);
	}

	function column_default( $item, $column_name = '' ) {

		switch ( $column_name ) {
			case 'Field':
			case 'Type':
			case 'Null':
			case 'Key':
			case 'Default':
			case 'Extra':
				return $item[ $column_name ];
			default:
				return '';
		}
	}

	public function prepare_items( $table_name ) {

		global $wpdb;

		$columns  = $this->get_columns();
		$this->_column_headers = array( $columns, array(), array() );

		$table_name = esc_attr( $table_name );

		$columns = $wpdb->get_results(
			"
			SHOW COLUMNS IN `$table_name`
			",
			ARRAY_A
		);

		$this->items = $columns;
	}
}
