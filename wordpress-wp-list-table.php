<?php

/*
 Plugin Name: WordPress WP_List_Table
 Description: WordPress WP_List_Table Example from Online Web Tutor
 Plugin URI: https://github.com/ljbrown238/wordpress-wp-list-table
 Version: 1.0
 Author: Loren J. Brown
 Author:  Loren J. Brown <ljbrown@QuantumPeg.com>
 Author URI: http://QuantumPeg.com
 License: GPL2
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 GitHub Plugin URI: https://github.com/ljbrown238/wordpress-wp-list-table
 GitHub Branch: master
*/

add_action("admin_menu", function() {

	add_menu_page(
		"WP_List_Table",
		"WP_List_Table",
		"manage_options",
		"wp-list-table",
		"wpl_wp_list_table",
		"dashicons-id"
	);

});

function wpl_wp_list_table() {

	// Would be displayed on the page
	// echo "This is a sample of WP_List_Table";

	ob_start();

	include_once plugin_dir_path(__FILE__) . 'views/view-wp-list-table.php';

	// Call the function defined in view-wp-list-table.php
	wp_list_table_layout();

	$template = ob_get_contents();

	ob_clean();

	echo $template;
}