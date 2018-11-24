<?php

require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

require_once(plugin_dir_path(__FILE__) . '../classes/class-wp-list-table-extended.php');

function wp_list_table_layout() {

	$wp_list_table = new WPListTableExtended();

	$wp_list_table->prepare_items();

	echo ("<h3>List of WordPress Users using WP_List_Table</h3>");

	$search_str = '';
	if(isset( $_GET['s'] )) {
		$search_str = $_GET['s'];
	}

	$action_str =  $_SERVER['PHP_SELF'] . '?page=wp-list-table';

	echo ("<div>");
	echo ("<form method='get' action='$action_str'>");
	echo ('Search users:<input type="text" name="s" value="' . $search_str . '"/> ');
	echo ('<input type="hidden" name="page" value="wp-list-table">');
	echo ('<input type="submit" value="Submit"> ');
	echo ("</form>");
	echo ("</div>");

	$wp_list_table->display();
}

