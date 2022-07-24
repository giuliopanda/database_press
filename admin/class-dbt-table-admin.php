<?php
/**
 * Il controller dell'amministrazione che gestisce tutte le chiamate alle pagine del plugin (quindi quando stai dentro page=database_tables).
 * Le chiamate POST e Ajax sono gestite dal loader.
 * 
 * Le funzioni della classe chiamano direttamente i vari file del template quindi dentro i template vengono chiamate le variabili delle funzioni stesse. 
 * @internal 
 */
namespace DatabaseTables;

class database_tables_admin 
{
	/**
	 * @var Int $max_show_items Numero massimo di elementi da caricare per un select
	 */
	var $max_show_items = 500; 
	/**
	 * @var String $last_error
	 */
	var $last_error = "";
	/**
	 * @var Array $get_table_list L'elenco delle tabelle e delle viste
	 */
	var $table_list = [];
	/**
	 * @var String $msg
	 */
	var $msg = "";
    /**
	 * Viene caricato alla visualizzazione della pagina
     */
    function controller() {
		global $wpdb;
		wp_enqueue_style( 'database-table-css' , plugin_dir_url( __FILE__ ) . 'css/database-table.css',[],rand());
		wp_enqueue_script( 'database-table-all-js', plugin_dir_url( __FILE__ ) . 'js/database-table-all.js',[],rand());

		$dtf = new Dbt_fn();
		Dbt_fn::require_init();
		$temporaly_files = new Dbt_temporaly_files();
	    /**
		 * @var $section Definisce il tab che sta visualizzando
		 */
        $section =  $dtf::get_request('section', 'home');
         /**
		 * @var $action Definisce l'azione
		 */
       	$action = $dtf::get_request('action', '', 'string');
		//print $section." ".$action;	
		$msg =  $msg_error = '';
		if (isset($_COOKIE['dbt_msg'])) {
			$msg = $_COOKIE['dbt_msg'];
		}
		if (isset($_COOKIE['dbt_error'])) {
			$msg_error = $_COOKIE['dbt_error'];
		}	
		switch ($section) {
			case 'information-schema' :
				$this->information_schema();
				break;
			case 'table-structure' :
				$this->table_structure();
				break;
			case 'table-import' :
				$this->table_list = $dtf::get_table_list();
			
				if ($action =='import-sql-file') {
					$this->import_sql_file();
				} else if ($action =='import-csv-file') {
					$this->import_csv_file();
				} else if ($action == 'execute-csv-data' ) {
					$this->execute_csv_data();
				} else if ($action == 'create-table-csv-data') {
					$this->create_table_csv_data();
				} else {
					$max_row_allowed = floor(Dbt_fn::get_max_input_vars()/10);
					$temporaly_files->clear_old();
					$import_table = $dtf::get_request('table', '');
					$render_content = "/dbt-content-table-import.php";
					require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
				}
				break;
			case 'table-sql' :
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'database-sql-editor-js', plugin_dir_url( __FILE__ ) . 'js/database-sql-editor.js',[],rand());
				$this->table_list = $dtf::get_table_list();
				// TODO: $list_of_columns 				= $dtf::get_all_columns();
				add_filter( 'dbt_render_sql_btns', [$this, 'filter_render_sql_btns'] );

				$render_content = "/dbt-content-sql.php";
				$table_model 				= new Dbt_model(@$_REQUEST['table']);
				$table_model->prepare();
				require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
				break;
			case 'table-browse' :
				$this->table_browse();
				break;
			case 'home' :
				//TODO Aggiungere un popup introduttivo
				// https://www.designbombs.com/adding-modal-windows-in-the-wordpress-admin/

				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'database-sql-editor-js', plugin_dir_url( __FILE__ ) . 'js/database-sql-editor.js',[],rand());
				wp_register_script( 'dbt-new-list', plugin_dir_url( __FILE__ ) . 'js/dbt-new-list.js',false, rand());
				wp_add_inline_script( 'dbt-new-list', 'dbt_admin_post = "'.esc_url( admin_url("admin-post.php")).'";', 'before' );
				wp_enqueue_script( 'dbt-new-list' );
				add_filter('dbt_render_sql_height', function () {return 100;} );

				add_filter( 'dbt_render_sql_btns', [$this, 'home_render_sql_btns'] );

				$permission_list = ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'CREATE', 'DROP', 'RELOAD', 'INDEX', 'ALTER', 'SHOW DATABASES', 'CREATE TEMPORARY TABLES', 'CREATE VIEW', 'SHOW VIEW'];
				$user_permission = $wpdb->get_results("SHOW GRANTS");
				if (!is_array($user_permission) || count($user_permission) >0) {
					$user_permission = $wpdb->get_results("SHOW GRANTS FOR '".esc_sql(DB_USER)."'@localhost'");
				} 
				if (is_array($user_permission) && count($user_permission) > 0) {
				
					foreach ($user_permission as $up1) {
						foreach ($up1 as $up) {
							foreach ($permission_list as $k=>$pl) {
								if (stripos($up, $pl) !== false) {
									unset($permission_list[$k]);
								}
							}
						}
					}
				} else {
					$permission_list = false;
				}
				$processlist = [];
				$processlist_sql = $wpdb->get_results("SHOW processlist;");
				foreach ($processlist_sql as $pl) {
					if ($pl->Time > 30 && $pl->Command == "Query" && $pl->Info != Null) {
						$processlist[$pl->Id] = $pl->Info;
					}
				}

				$variables = $wpdb->get_row('SHOW VARIABLES WHERE Variable_name = "version_comment";');
				$info_db = "";
				if (stripos($variables->Value, 'MySQL ') !== false) {
					$info_db = "MYSQL ".$wpdb->get_var('SELECT VERSION();');
				} else {
					$vers = $wpdb->get_var('SELECT VERSION();');
					if (stripos($variables->Value, 'MariaDB') > 0) {
						$info_db = $vers; 
					}
				}
				$database_size = 0;
				$database_name = $wpdb->get_var('SELECT DATABASE();');
				if ($database_name != "") {
					$database_size = $wpdb->get_var('SELECT  sys.FORMAT_BYTES(SUM(data_length + index_length)) `size` FROM information_schema.tables WHERE table_schema = "'.$database_name.'" GROUP BY table_schema');
				}
				$temporaly = new Dbt_temporaly_files();
				$dir = $temporaly->get_dir();
				
				$is_writable_dir = false;
				if ($dir != "") {
					$is_writable_dir = wp_is_writable($dir);
				}

				require(dirname( __FILE__ ) . "/partials/dbt-page-home.php");
				break;
		}
    }

	/**
	 * 
	 */
	private function information_schema() {
		global $wpdb;

		$dtf = new Dbt_fn();
		$temporaly_files = new Dbt_temporaly_files();
        $section =  $dtf::get_request('section', 'home');
       	$action = $dtf::get_request('action', '', 'string');
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'database-sql-editor-js', plugin_dir_url( __FILE__ ) . 'js/database-information-schema.js',[],rand());
		
		require(dirname( __FILE__ ) . "/../includes/dbt-model-information-schema.php");
		$temporaly_files->clear_old();
		$table_model 				= new Dbt_model_information_schema();
	
		$table_model->get_list();
		$html_table 				= new dbt_html_simple_table();	
		$html_table->add_table_class('wp-list-table widefat striped dbt-table-view-list');
		$render_content = "/dbt-content-information-schema.php";
		
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
	}
	/**
	 * Il tab structure
	 */
	private function table_structure() {
		$dtf = new Dbt_fn();
        $section =  $dtf::get_request('section', 'home');
       	$action = $dtf::get_request('action', '', 'string');
		$msg =  $msg_error = $table = $table_new_name = '';	
		if ($action == 'edit-index') {
			wp_enqueue_script( 'database-table-import-js', plugin_dir_url( __FILE__ ) . 'js/database-table-structure.js',[],rand());
			wp_enqueue_script( 'jquery-ui-sortable' );
			
			$table = $_REQUEST['table'];
			$id = $_REQUEST['dbt_id'];
			$table_fields = Dbt_fn::get_table_structure($table, true);
			$table_model = new Dbt_model_structure($table);
			$indexes = $table_model->get_index($id);
			$index_table = new dbt_html_simple_table();
			$render_content = "/dbt-content-table-structure-indexes.php";
		} else {
			$render_content = "/dbt-content-table-structure.php";
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'database-table-import-js', plugin_dir_url( __FILE__ ) . 'js/database-table-structure.js',[],rand());
			wp_enqueue_script( 'database-table-alter-table-js', plugin_dir_url( __FILE__ ) . 'js/database-table-alter-table.js',[],rand());
			if (!isset($_REQUEST['table'])) {
				// Nuova tabella
				$tables_list = Dbt_fn::get_table_list();
				$count_table_name = 1;
				$table_new_name = Dbt_fn::get_prefix(). "dbt_table_".str_pad($count_table_name, 3, '0', STR_PAD_LEFT) ;
				while (array_key_exists($table_new_name, $tables_list["tables"]) && $count_table_name < 999) {
					$count_table_name++;
					$table_new_name = Dbt_fn::get_prefix(). "dbt_table_".str_pad($count_table_name, 3, '0', STR_PAD_LEFT) ;
				}
				$table = "";
				$table_model = new Dbt_model_structure();
			} else {
				$table = $_REQUEST['table'];
				$table_model = new Dbt_model_structure($table);
			}
			if ($action == 'save_metadata') {
				//options[status], options[description]
				Dbt_fn::update_dbt_option_table_status($table, $_REQUEST['options']['status'], $_REQUEST['options']['description']);
			}
			if ($action == 'save_index') {
				if (isset($_REQUEST['index']['columns'])) {
					$name =  (isset($_REQUEST['index']['name'])) ?  $_REQUEST['index']['name'] : '';
					if ($table_model->alter_index($_REQUEST['index']['columns'], $_REQUEST['original_name'], $name, $_REQUEST['original_index'], $_REQUEST['index']['type'])) {
						$msg =__('Altered Index success', 'database_tables');
					} else {
						$msg_error = $table_model->last_error;
					}
				} else {
					$msg_error = __('You must select at least one column', 'database_tables');
				}
			}
			if ($action == 'delete-index') {
				if ($table_model->delete_index( $_REQUEST['dbt_id'])) {
					$msg =__('Delete Index success', 'database_tables');
				} else {
					$msg_error = __('Error Delete Index success', 'database_tables');
				}
			}
			// $_REQUEST['table'] c'è di sicuro perché altrimenti si esegue il redirect nel loader ad information-schema
			
			$table_model->get_structure();
			$old_primaries = [];
			$is_old_primaries_type_numeric = false;
			foreach ($table_model->items as $cs) {
				if (is_object($cs)) {
					if ($cs->Key == "PRI" ) {
						$old_primaries[] = '`'.$cs->Field.'`';
						if (substr($cs->Type,0,3) == "int" || substr($cs->Type,0,6) == "bigint") {
							$is_old_primaries_type_numeric = true;
						}
					}
					
				}
			}
			$table_options = Dbt_fn::get_dbt_option_table($table);

			if ($table_model->error_primary) {
				$msg_error = __('This system works better with tables that have only one field set as the autoincrement primary key.','database_tables');
				$msg_error .= '<br><br>';
				if ($table_options['status'] != 'DRAFT') {
					$msg_error .= __('<b>To solve the problem follow the instructions:</b><br>
					<p>1. Click on Edit status and put the table in DRAFT MODE</p><p>2. Copy and run the queries</p>','database_tables');
				} else {
					$msg_error .= __('<b>Copy and run the queries to correct the problem.</b>','database_tables');
				}
				if (count($old_primaries) == 1 &&  $is_old_primaries_type_numeric ) {
					$msg_error .= __(sprintf('<p style="background:#F2F2F2; border:1px solid #EEE; padding:.5rem">ALTER TABLE `%s` MODIFY %s INT NOT NULL AUTO_INCREMENT;<br></p>', $table, implode(", ", $old_primaries)), 'database_tables');
				} else {
					$msg_error .= __(sprintf('<p style="background:#F2F2F2; border:1px solid #EEE; padding:.5rem">ALTER TABLE `%s` drop primary key;<br>
					CREATE UNIQUE INDEX old_primary_key ON `%s` (%s);<br>
					ALTER TABLE `%s` ADD dbt_id BIGINT AUTO_INCREMENT PRIMARY KEY;<br></p>', $table,$table, implode(", ", $old_primaries), $table),'database_tables');
				}
			}

			if ($action == 'show_create_structure') {
				$sql_sctructure = $this->show_create_structure($table);
			}
			
		
			$indexes = $table_model->get_indexes();
		
			$index_table = new dbt_html_simple_table();
			if ($table != "" && count($table_model->items) > 0) {
				$this->last_error = Dbt_fn::get_max_input_vars(count($table_model->items)*15)	;
			}
			$max_row_allowed = floor(Dbt_fn::get_max_input_vars()/15);
		}
		
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
	}
	/**
	 * Mostro il risultato di una query
	 */
	private function table_browse() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'database-form2-js', plugin_dir_url( __FILE__ ) . 'js/database-form2.js',[],rand());
		wp_register_script( 'dbt_database_tables_js',  plugin_dir_url( __FILE__ ) . 'js/database-table.js',false,rand());
		//	wp_add_inline_script( 'mytheme-typekit', 'try{Typekit.load({ async: true });}catch(e){}' );
		wp_add_inline_script( 'dbt_database_tables_js', 'dbt_admin_post = "'.esc_url( admin_url("admin-post.php")).'";', 'before' );
		wp_enqueue_script( 'dbt_database_tables_js' );

		wp_enqueue_script( 'database-sql-editor-js', plugin_dir_url( __FILE__ ) . 'js/database-sql-editor.js',[],rand());
		wp_enqueue_script( 'database-table-js-multiqueries', plugin_dir_url( __FILE__ ) . 'js/database-table-multiqueries.js',[],rand());
		
		$msg =  $msg_error = '';
		if (isset($_COOKIE['dbt_msg'])) {
			$msg = stripslashes($_COOKIE['dbt_msg']);
		}
		if (isset($_COOKIE['dbt_error'])) {
			$msg_error = stripslashes($_COOKIE['dbt_error']);
		}	
		$temporaly_files = new Dbt_temporaly_files();
		$dtf = new Dbt_fn();
        $section =  $dtf::get_request('section', 'home');
       	$action = $dtf::get_request('action_query', '', 'string');

		//	wp_add_inline_script( 'database-table-js', 'dbt_admin_post = "'.esc_url( admin_url("admin-post.php")).'";', 'before' );
		//	wp_enqueue_script( 'database-table-js', plugin_dir_url( __FILE__ ) . 'js/database-table.js',[],rand());

		$table_model 				= new Dbt_model(@$_REQUEST['table']);
		$list_of_columns 				= $dtf::get_all_columns();
		$show_query = false;
		// cancello le righe selezionate!
		if ($action == "delete_rows" && isset($_REQUEST["remove_ids"]) && is_array($_REQUEST["remove_ids"])) {
			$result_delete = $dtf::delete_rows($_REQUEST["remove_ids"], $_REQUEST['custom_query']);
			if ($result_delete['error'] != "") {
				$msg_error = $result_delete;
			} else {
				$msg = sprintf(__('The data has been removed. <br> %s', 'database_tables'), $result_delete['sql']);
			}
		}

		if ($action == "delete_from_sql") {
			$result_delete = $dtf::dbt_delete_from_sql($dtf::get_request('sql_query_executed'), $dtf::get_request('remove_table_query'));
			if ($result_delete != "") {
				$msg_error = $result_delete;
			} else {
				$msg = __('The data has been removed', 'database_tables');
			}
		}
	
		$custom_query = $dtf::get_request('custom_query', '');

		// Dbt_util_marks_parentheses diventa lentissimo con testi troppo lunghi > 1.000.000 chr 
	
		if (strlen($custom_query) > 150000) {
			$mysqli = new \mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die ('DB CONNECTION ERROR?!');
			$mysqli->multi_query($custom_query);
			$render_content = "/dbt-content-table-import.php";

			$msg_error = "";	
			//Make sure this keeps php waiting for queries to be done
			$count_query = 0;
			do {
				/* store the result set in PHP */
				if ($result = $mysqli->store_result()) {
					while ($row = $result->fetch_row()) {
						$msg_error .= '<p>'.$row[0].'</p>' ;
					}
				}
				$count_query++;
			} while ($mysqli->next_result());

			if ($msg_error == "") {
				if (is_countable($mysqli->error_list) && count($mysqli->error_list) > 0) {
					foreach ($mysqli->error_list as $el) {
						$msg_error .= '<p>'.$el['error'].'</p>' ;
					}
				} else {
					$msg = __(sprintf('The queries were performed successfully.', 'database_tables'));
				}
			} 

			$mysqli->close();	
			$dtf::get_table_list(false);
		
			$render_content = "/dbt-content-table-with-filter.php";
		
			add_filter( 'dbt_render_sql_btns', [$this, 'browse_table_filter_render_sql_btns'] );
			require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
			return;
		}

		$table_model->prepare($dtf::get_request('custom_query', ''));
		
		$_REQUEST['table'] = $table_model->get_table();
		if ($table_model->sql_type() == "multiqueries") {
			$queries = $table_model->get_current_query();
			$ajax_continue = $temporaly_files->store(['total_queries' => count($queries), 'queries_filename' => $temporaly_files->store($queries), 'last_error' => '', 'error_count' => 0, 'report_queries' => [] ]); 
			$info = Dbt_fn::execute_multiqueries($ajax_continue);
			$items = $info['items'];
			if ($info['executed_queries'] == $info['total_queries']) {
				$ajax_continue = false;
			}
			$render_content = "/dbt-content-multiquery.php";
		} else {
			$dtf::add_request_filter_to_model($table_model, $this->max_show_items);
			$table_model->add_primary_ids();
			$table_items = $table_model->get_list();
			$table_model->update_items_with_setting();
			$dtf::items_add_action($table_model);
			$table_model->check_for_filter();
			$dtf::remove_hide_columns($table_model);
		
			if ( count($table_model->get_pirmaries()) == 0 && count($table_model->get_query_tables()) > 0 && $msg_error == '' && $table_model->sql_type() == "select") {
				$msg_error = __('This system works better with tables that have only one field set as the autoincrement primary key.','database_tables');
				$msg_error .= '<br>'.__('Most of the features have been disabled.','database_tables').'<br>';
				if ($table_model->table_status() == "DRAFT")  {
					$msg_error .= '<b>'.__('If you can alter the table, go to Structure and follow the instructions.','database_tables').'</b>';
				}
			}
			if ($table_model->table_status() == 'CLOSE' && $msg == '' && $msg_error == '') {
				$msg = __('The table can no longer be modified because it is in the "CLOSE" state.', 'database_tables');
			}
			$html_table   = new Dbt_html_table();
			$html_content = $html_table->template_render($table_model); // lo uso nel template
			//print (get_class($table_model) );
			$render_content = "/dbt-content-table-with-filter.php";
		}
		add_filter( 'dbt_render_sql_btns', [$this, 'browse_table_filter_render_sql_btns'] );
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");

	}

	/**
	 * Importa un file sql
	 */
	function import_sql_file() {
		$dtf = new Dbt_fn();
		$section =  $dtf::get_request('section', 'home');
		$action = $dtf::get_request('action_query', '', 'string');
		$import_table = $dtf::get_request('table', '');
		$this->msg = '';
		$this->last_error = "";
		$render_content = "/dbt-content-table-import.php";
		if (!isset($_FILES['sql_file']['tmp_name']) || $_FILES['sql_file']['tmp_name'] == "") {
			$this->last_error = __('No file uploaded', 'database-table');
			$action = "";
		} else {
			$sql = file_get_contents($_FILES['sql_file']['tmp_name']);
			$mysqli = new \mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die ('DB CONNECTION ERROR?!');
			$mysqli->multi_query($sql);
			//Make sure this keeps php waiting for queries to be done
			$count_query = 0;
			do {
				/* store the result set in PHP */
				if ($result = $mysqli->store_result()) {
					while ($row = $result->fetch_row()) {
						
						$this->last_error .= '<p>'.$row[0].'</p>' ;
					}
				}
				$count_query++;
			} while ($mysqli->next_result());

			if ($this->last_error == "") {
				if (is_countable($mysqli->error_list) && count($mysqli->error_list) > 0) {
					foreach ($mysqli->error_list as $el) {
						$this->last_error .= '<p>'.$el['error'].'</p>' ;
					}
				} else {
					$this->msg = __(sprintf('%s queries executed successfully.', $count_query ));
				}
			} 

			$mysqli->close();	
			$dtf::get_table_list(false);
		}
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");

	}
	/**
	 * Quando apri la pagina e carichi un csv
	 */
	function import_csv_file() {
		wp_enqueue_script( 'database-table-import-js', plugin_dir_url( __FILE__ ) . 'js/database-table-import.js',[],rand());
		wp_enqueue_script( 'jquery-ui-sortable' );
		// carico il csv e ne mostro le opzioni
		if (!isset($_FILES['sql_file'])) {
			wp_redirect( add_query_arg(['section'=>'table-import'], admin_url("admin.php?page=database_tables")));
			die();
		}
		$max_row_allowed = floor(Dbt_fn::get_max_input_vars()/10);
		$dtf = new Dbt_fn();
		Dbt_fn::require_init();
		$section =  $dtf::get_request('section', 'home');
       	$action = $dtf::get_request('action', '', 'string');
		$import_table = $dtf::get_request('table', '');
		if ($import_table != "" && $dtf::exists_table($import_table)) {
			$select_action = "insert_records";
			$current_table = $import_table;
		}
		$temporaly_files = new Dbt_temporaly_files();
		$model_structure = new Dbt_model_structure();
		$name_of_file = $model_structure->change_unique_table_name($_FILES['sql_file']['name']);

		$csv_filename = $temporaly_files->move_uploaded_file('sql_file');
		if ($csv_filename == "") {
			$this->last_error = $temporaly_files->last_error;
			$action = '';
		} else {
			$csv_delimiter = $temporaly_files->find_csv_delimiter($csv_filename);

			$csv_items = $temporaly_files->read_csv($csv_filename, $csv_delimiter, true, 20);
			
			$csv_first_row_as_headers = $allow_use_first_row = $temporaly_files->csv_allow_to_use_first_line(reset($csv_items));
			if (!$allow_use_first_row) {
				$csv_items = $temporaly_files->read_csv($csv_filename, $csv_delimiter, false, 20);
			}
			$csv_structure = $temporaly_files->csv_structure($csv_filename, $csv_delimiter, $csv_first_row_as_headers);

			$csv_structure = self::csv_create_table_add_primary($csv_structure);
			
			if ($csv_structure == false) {
				$this->last_error = __("It doesn't look like a valid csv", 'database_tables');
				$action = '';
			}
		}
		$render_content = "/dbt-content-table-import.php";
	
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
	}

	/**
	 * SU IMPORT CSV quando premo il bottone UPDATE PREVIEW
	 * Aggiorno le opzioni del csv Mostra le impostazioni per l'importazione del csv
	 */
	function execute_csv_data() {
		wp_enqueue_script( 'database-table-import-js', plugin_dir_url( __FILE__ ) . 'js/database-table-import.js',[],rand());
		wp_enqueue_script( 'jquery-ui-sortable' );
		$dtf = new Dbt_fn();
		Dbt_fn::require_init();
		$max_row_allowed = floor(Dbt_fn::get_max_input_vars()/10);
		$section =  $dtf::get_request('section', 'home');
       	$action = $dtf::get_request('action', '', 'string');
		$import_table = $dtf::get_request('table', '');
		$temporaly_files = new Dbt_temporaly_files();
		
		$csv_filename = $dtf::get_request('csv_temporaly_filename');
		$csv_delimiter = $dtf::get_request('csv_delimiter');
		$allow_use_first_row = $dtf::get_request('allow_use_first_row', 1);
		$model_structure = new Dbt_model_structure();
		$name_of_file = $model_structure->change_unique_table_name($dtf::get_request('csv_name_of_file'));

		$csv_first_row_as_headers = $dtf::get_request('csv_first_row_as_headers', false, 'boolean');
		$csv_items = $temporaly_files->read_csv($csv_filename, $csv_delimiter, $csv_first_row_as_headers, 20);
		
		$csv_structure = $temporaly_files->csv_structure($csv_filename, $csv_delimiter, $csv_first_row_as_headers);
		$csv_structure = self::csv_create_table_add_primary($csv_structure);
		$render_content = "/dbt-content-table-import.php";
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
	}

	/**
	 * Verifica se bisogna aggiungere una chiave primaria alla tabella
	 * @param Array $csv_strcture
	 */
	private function csv_create_table_add_primary($csv_structure) {
		$has_primary = false;
		foreach ($csv_structure as $struct) {
			//var_dump ($struct);
			if ($struct->primary == "t") {
				$has_primary = true;
				break;
			}
		}
		if (!$has_primary) {
			array_unshift($csv_structure, json_decode('{"field_name":"dbt_id", "field_type":"INT", "auto_increment":"t", "field_length":"11", "attributes":"UNSIGNED", "null":"f", "default":"", "primary": "t", "ai":"t", "comment":"", "preset":"pri"}'));
		}
		return $csv_structure;
	}

	/**
	 * Crea la tabella dal csv
	 */
	function create_table_csv_data() {
		global $wpdb;
		wp_enqueue_script( 'database-table-import-js', plugin_dir_url( __FILE__ ) . 'js/database-table-import.js',[],rand());
		wp_enqueue_script( 'jquery-ui-sortable' );
		$dtf = new Dbt_fn();
		Dbt_fn::require_init();
		$max_row_allowed = floor(Dbt_fn::get_max_input_vars()/10);
		$section =  $dtf::get_request('section', 'home');
       	$action = $dtf::get_request('action', '', 'string');
		$import_table = $dtf::get_request('table', '');
		$temporaly_files = new Dbt_temporaly_files();
		$csv_filename = $_REQUEST['csv_temporaly_filename'];
		$csv_delimiter = $_REQUEST['csv_delimiter'];
		$csv_first_row_as_headers = $dtf::get_request('csv_first_row_as_headers', false, 'boolean');
		$csv_items = $temporaly_files->read_csv($csv_filename, $csv_delimiter, $csv_first_row_as_headers, 20);
		
		$csv_structure = $temporaly_files->csv_structure($csv_filename, $csv_delimiter, $csv_first_row_as_headers);

		$model_structure = new Dbt_model_structure($_REQUEST['csv_name_of_file']);
		$name_of_file = $model_structure->change_unique_table_name();
		if (isset($_REQUEST['use_prefix']) && $_REQUEST['use_prefix'] == 1) {
			//print "<p>USEPREFIX ".$_REQUEST['use_prefix']."</p>";
			$model_structure->use_prefix = true;
		}

		$execute_query = true;
		$csv_structure_table_created = [];
		foreach ($_REQUEST['form_create']["field_name"] as $key => $column_name) {
			if ($column_name != "") {
				switch ($_REQUEST['form_create']["field_type"][$key]) {
					case 'pri':
						$model_structure->insert_column($column_name, 'BIGINT', '', '', true, 'SIGNED', true );
						break;
					case 'varchar':
						$model_structure->insert_column($column_name, 'VARCHAR', '255');
						break;
					case 'text':
						$model_structure->insert_column($column_name, 'TEXT');
						break;
					case 'int':
						$model_structure->insert_column($column_name, 'INT');
						break;
					case 'double':
						$model_structure->insert_column($column_name, 'DOUBLE','11,2');
						break;
					case 'date':
						$model_structure->insert_column($column_name, 'DATE');
						break;
					case 'datetime':
						$model_structure->insert_column($column_name, 'DATETIME');
						break;
				}
				
				$csv_structure_table_created[] = (object)[
					'name'=>$_REQUEST['form_create']["csv_name"][$key],
					'field_name'=>$column_name,
					'preset'=>$_REQUEST['form_create']["field_type"][$key],
				];
			
				if ($model_structure->last_error != "") {
					$execute_query = false;
					$this->last_error = $model_structure->last_error;
				}
			}
		}
		$sql = "";
		// TODO: Devo centralizzare la creazione delle tabelle
		$select_action = "create_database";
		if ($execute_query) {
			$sql = $model_structure->get_create_sql();
			if ($model_structure->last_error != "") {
				$this->last_error = $model_structure->last_error;
				$csv_structure = $csv_structure_table_created;
				unset($csv_structure_table_created);
			} else {
				$result = $wpdb->query($sql);
				if (is_wp_error($result) || !empty($wpdb->last_error)) {
					$this->last_error = $wpdb->last_error;
					$csv_structure = $csv_structure_table_created;
					unset($csv_structure_table_created);
				} else {
					$import_table = $model_structure->get_table_name();
					$this->msg = __('Table <b>'. $model_structure->get_table_name(). '</b> created','database_tables');
					Dbt_fn::update_dbt_option_table_status($model_structure->get_table_name(), 'DRAFT', 'Table created with the csv import procedure');
				}
				Dbt_fn::$table_list = [];
				$this->table_list = $dtf::get_table_list();
				$select_action = "insert_records";
				$current_table = $model_structure->get_table_name();
				
			}
		}
		
		$render_content = "/dbt-content-table-import.php";
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
	}


	function show_create_structure($table) {
		global $wpdb;
		$sql = 'SHOW CREATE TABLE `'.esc_sql($table).'`';
		$result = $wpdb->get_row($sql, 'ARRAY_A');
		if (isset($result['Create Table'])) {
			return ($result['Create Table']);
		} else {
			return '';
		}
	}

	function filter_render_sql_btns($btns) {
		unset($btns['cancel']);
		$btns = array_merge(['go_custom' =>
		'<div id="dbt-bnt-go-query"  class="dbt-submit dbt-btn-show-sql-edit"  onclick="dtf_submit_custom_query()">'. __('Go','database_tables').'</div>'],$btns) ;
		
		return $btns;
	}
	function home_render_sql_btns($btns) {
		unset($btns['cancel']);	
		$btns = array_merge(['go_custom' =>
		'<div id="dbt-bnt-go-query"  class="dbt-submit dbt-btn-show-sql-edit"  onclick="jQuery(\'#table_sql_home\').submit()">'. __('Go','database_tables').'</div>'],$btns) ;
		return $btns;
	}

	function browse_table_filter_render_sql_btns($btns) {
		$btns = array_merge(['save_query' =>
		'<div class="dbt-right-query-btns">
		<div id="dbt-bnt-save-query" class="button js-show-only-select-query"  onclick="dbt_show_save_sql_query()">'. __('Create list from query','database_tables').'</div></div>'], $btns) ;

		$btns = array_merge(['go_custom' =>
		'<div id="dbt-bnt-go-query" class="dbt-submit" onclick="dtf_submit_table_filter(\'custom_query\')">'. __('Go','database_tables').'</div>'], $btns) ;
		return $btns;
	}

}
