<?php

class MJJ_Whats_In_My_Database {

	protected static $instance = null;

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	} // end get_instance

	private function __construct() {

		// this will make the page in tools
		add_action( 'admin_menu', array( 'MJJ_Whats_In_My_Database', 'add_wimd_page_to_tools' ) );
		add_action( 'admin_head', array( 'MJJ_Whats_In_My_Database', 'add_styles' ) );
		add_action( 'admin_enqueue_scripts', array( 'MJJ_Whats_In_My_Database', 'add_scripts' ) );

	}

	// Styles for the page
	public static function add_styles() {
		$current_screen = get_current_screen();
		if ( 'tools_page_mjj-whats-in-my-database' === $current_screen->id ) {
			echo '<style>
					.mjj-wimd{
						padding: 1%;
					}
					.mjj-wimd-table{
    					padding: 1%;
    				}
    				.mjj-wimd-table.odd{
    					background-color: #E2E2E2;
    				}
    				.mjj-wimd-table.even{
    					background-color: #FFFFFF;
    				}
    				.mjj-wimd-table h2{
    					margin-top: 0;
    				}
    				.mjj-wimd-table .tablenav{
    					margin: 0;
    					height: auto;
    				}
    				.mjj-wimd-table .show-columns{
    					display: none;
    				}
  				</style>';
		}
	}

	// JS to handle the "show columns" / "hide columns" button (and show and hide the columns :) )
	public static function add_scripts() {
		$current_screen = get_current_screen();
		if ( 'tools_page_mjj-whats-in-my-database' === $current_screen->id ) {
			wp_enqueue_script( 'mjj-wimd-scripts', plugin_dir_url( __FILE__ ) . 'js/mjj-wimd.js', array( 'jquery' ), '0.0.1', true );
		}
	}

	// This page is in Tools
	public static function add_wimd_page_to_tools() {
		add_management_page( 'What&rsquo;s in my database?',  'What&rsquo;s in my database?', 'list_users', 'mjj-whats-in-my-database', array( 'MJJ_Whats_In_My_Database', 'make_tools_page' ) );
	}

	public static function make_tools_page() {

		require_once( 'class-mjj-wimd-list-table.php' );

		global $wpdb;

		// get all the tables in the database
		$tables = $wpdb->get_results(
			$wpdb->prepare(
				'
				SELECT TABLE_NAME as table_name, CREATE_TIME as create_time
				FROM INFORMATION_SCHEMA.TABLES 
				WHERE TABLE_SCHEMA = %s
				',
				DB_NAME
			)
		);

?>

		<div class="mjj-wimd">
				<h1><?php _e( 'What&rsquo;s in my database?', 'mjj-wimd' ); ?></h1>
				<p><?php _e( 'Below is a list of tables in your database and their columns.', 'mjj-wimd' ); ?></p>
<?php
		$iteration = 0;

		// Do each table separately
		foreach ( $tables as $table ) {

			$iteration += 1;
			$oddness = ( $iteration % 2 === 0 ) ? 'even' : 'odd';

			printf( '<div class="mjj-wimd-table %s">', $oddness );

			$table_name = esc_attr( $table->table_name );
			
			// not in previous query because that gives an ever changing estimate sometimes and I find that distressing
			$table_rows = MJJ_Whats_In_My_Database::count_rows_in_table( $table_name );
			// the columns will be in a List Table
			$list_table = new MJJ_WIMD_List_Table( $table_name );
?>


			<h2><?php printf( __( 'Table: %s', 'mjj-wimd' ), esc_html( $table->table_name ) ); ?></h2>
			<p><?php printf( __( 'Number of rows: %d, Create time: %s ', 'mjj-wimd' ),
				$table_rows->count,
				esc_attr( $table->create_time )
			); ?></p>
				
			<button class="open-show-columns closed"><?php _e( 'Show columns', 'mjj-wimd' ); ?></button>
<?php
			$list_table->prepare_items( $table_name );

			echo( '<div class="show-columns">' );
			$list_table->display();
			echo( '</div>' );

			echo( '</div>' );
		}

		echo( '</div>' );
	}

	// Count rows in a table
	public static function count_rows_in_table( $table_name ) {
		global $wpdb;

		$table_name = esc_attr( $table_name );
		$count = $wpdb->get_row(
			"
			SELECT COUNT(*) as count
			FROM `$table_name`
			"
		);

		return $count;
	}
}
