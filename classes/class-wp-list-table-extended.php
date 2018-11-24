<?php
/**
 * Created by PhpStorm.
 * User: ljbrown
 * Date: 11/23/18
 * Time: 18:03PST
 */


class WPListTableExtended extends WP_List_Table {

	public function prepare_items() {

		//
		// Handle parameters
		//

		// Get the column we are sorting by
		$orderby = isset($_GET['orderby']) ? htmlentities(trim($_GET['orderby'])) : "";
		$orderby = preg_replace('/\s+/', '', $orderby);

		// Get the direction of sort
		$order = isset($_GET['order']) ? htmlentities(trim($_GET['order'])) : "";
		$order = preg_replace('/\s+/', '', $order);

		// Get the search term, if there is one
		$search_term = isset($_GET['s']) ? htmlentities(trim($_GET['s'])) : "";
		$search_term = esc_sql($search_term);


		//
		// Deal with pagination and obtaining data
		//

		// Determine what page we're on, if any
		$current_page = $this->get_pagenum();

		// Determine the number of rows per page
		// 2 for testing
		$per_page = 2;

		// Determine the offset for the MySQL query
		$offset = ( $current_page - 1 ) * $per_page;

		// Pass all params in to get the data
		$data_set = $this->get_data($offset, $per_page, $orderby, $order, $search_term);
		$this->items = $data_set['data'];
		$total_items = $data_set['total_items'];

		$this->set_pagination_args(
			array(
				"total_items" => $total_items,
				"per_page" => $per_page
			)
		);


		//
		// Handle columns
		//

		// Gets an array of columns with the "[colname]" => "[header name]" parameters
		$columns = $this->get_columns();

		// If we have hidden any columns, they will be obtained here
		$hidden = $this->get_hidden_columns();

		// The sorting defaults will be established here
		$sortable = $this->get_sortable_columns();

		// Set column headers
		$this->_column_headers = array($columns, $hidden, $sortable);
	}

	// This is our own function to get the data based on the search parameters
	public function get_data($offset, $per_page, $orderby = '', $order = '', $search_term = '') {

		// We're going to get data from WordPress, so get ready
		global $wpdb;

		// The "data" query is designed to get all of the parameters and all relevant data
		$query_data = "
			SELECT
				ID as id,
				user_login,
				user_pass,
				user_email
			FROM wp_users
		";

		// The "total_items" query is designed to get the total number of items had we NOT LIMITED the output
		// The rationale here is that we do NOT want to get ALL of the data if we do not need it.
		// We simply need to find out the total number of rows and then drill into where we need the data.
		// Otherwise, we wind up with WAY too much unnecessary data
		$query_total_items = "
			SELECT
				count(ID)
			FROM wp_users
		";

		// If we ARE searching on a term, set up a string for query inclusion to both
		// the "data" and "total_items" query
		if (!empty($search_term)) {
			$phrase_search = "
				WHERE 
					user_login like '%$search_term%' 
					OR user_pass like '%$search_term%' ";
		}

		$query_data .= $phrase_search;
		$query_total_items .= $phrase_search;


		// If we are ordering, we will not need to sort for a total count,
		// so we just apply that to the data query
		if ($orderby != '') {
			$phrase_order = " ORDER BY $orderby ";

			switch ($order) {
				case 'asc':
					$phrase_order .= " ASC ";
					break;

				case 'desc':
					$phrase_order .= " DESC ";
					break;
			}

			// Only apply this to the "data" query
			$query_data .= $phrase_order;
		}

		// Now, apply the limit to the "data" query, since we only need the necessary data here
		$query_data .= "  LIMIT $offset, $per_page";

		// Get the LIMITed data that we need
		$data = $wpdb->get_results(
			$query_data,
			ARRAY_A
		);

		// Get the total number of items, based on the search criterion
		$total_items = $wpdb->get_var(
			$query_total_items
		);

		// Pass back both the limited data and the total_number of searched items
		$results_arr = array("data" => $data, "total_items" => $total_items);

		return $results_arr;
	}

	public function get_hidden_columns() {
		// CSV list of columns not to show
		// Intentionally user_pass as an example of what NOT to show
		return array('user_pass');
	}

	public function get_sortable_columns() {
		return array(
			'user_login' => array("user_login",true),
			'user_email' => array("user_email",false)
		);
	}

	public function column_default($item, $column_name) {

		switch($column_name) {
			case "id":
			case "user_login":
			case "user_email":
			case "user_pass":
				return $item[$column_name];
			default:
				return 'no value';
		}
	}

	public function get_columns() {
		$columns = array(
			"id" => "ID",
			"user_login" => "Login",
			"user_email" => "EMail",
			"user_pass" => "Password"
		);

		return $columns;
	}

	public function default() { }
}
