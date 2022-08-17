<?php
/**
 * Gestisco il filtri e hook (prevalentemente le chiamate ajax amministrative)
 *
 * @package    DATABASE TABLE
 * @subpackage DATABASE TABLE/INCLUDES
 * @internal
 */
namespace DatabaseTables;

class Dbt_loader {
	/**
	 * @var Object $saved_queries le ultime query salvate per tipo
	 */
	public static $saved_queries;

	public function __construct() {
		self::$saved_queries = (object)[];
		add_action( 'admin_menu', [$this, 'add_menu_page'] );
		// aggiungo eventuali pagine di menu
		add_action('admin_menu',  [$this, 'init_add_menu_page'] );
		add_action('admin_enqueue_scripts', [$this, 'codemirror_enqueue_scripts']);
		// L'ajax per la richiesta dell'elenco dei valori unici per mostrarli nei filtri di ricerca
		add_action( 'wp_ajax_dbt_distinct_values', [$this, 'dbt_distinct_values']);
		add_action( 'wp_ajax_dbt_autocomplete_values', [$this, 'dbt_autocomplete_values']);
		// L'ajax per l'esecuzione delle multiqueries quando sono troppe
		add_action( 'wp_ajax_dbt_multiqueries_ajax', [$this, 'dbt_multiqueries_ajax']);
		// l'ajax per vedere il dettaglio di una sola riga estratta da una query
		add_action( 'wp_ajax_dbt_view_details', [$this, 'dbt_view_details']);
		// l'ajax per confermare l'eliminazione di uno o più record 
		add_action( 'wp_ajax_dbt_delete_confirm', [$this, 'dbt_delete_confirm']);
		/**
		 * l'ajax per l'edit record
		 * @since v0.5
		 */ 
		add_action( 'wp_ajax_dbt_edit_details_v2', [$this, 'dbt_edit_details_v2']);
		// l'ajax per il salvataggio di un record
		add_action( 'wp_ajax_dbt_save_details', [$this, 'dbt_save_details']);
		// l'ajax per generare le query per eliminare tutti i record di una query
		add_action( 'wp_ajax_dbt_check_delete_from_sql', [$this, 'dbt_check_delete_from_sql']);
		// l'ajax preparare gli id da rimuovere successiva a dbt_check_delete_from_sql
		add_action( 'wp_ajax_dbt_prepare_query_delete', [$this, 'prepare_query_delete']);
		// Dopo aver preparato i dati da rimuovere, li rimuovo tutti.
		add_action( 'wp_ajax_dbt_sql_query_delete', [$this, 'sql_query_delete']);

		
		// l'ajax per generare il csv che salva sui file temporanei e poi li puoi scaricare
		add_action( 'wp_ajax_dbt_download_csv', [$this, 'dbt_download_csv']);
		// l'ajax mostra l'elenco delle colonne di una query pee permettere di scegliere quali visualizzare
		add_action( 'wp_ajax_dbt_columns_sql_query_edit', [$this, 'columns_sql_query_edit']);
		// l'ajax Una volta scelto l'elenco delle colonne da visualizzare modifica la query con il nuovo select
		add_action( 'wp_ajax_dbt_edit_sql_query_select', [$this, 'edit_sql_query_select']);
		// l'ajax viasualizzare la form per fare un marge con un'altra tabella
		add_action( 'wp_ajax_dbt_merge_sql_query_edit', [$this, 'merge_sql_query_edit']);
		// trova le colonne di una tabella
		add_action('wp_ajax_dbt_merge_sql_query_get_fields', [$this, 'merge_sql_query_get_fields']);
		// Genera la query con il nuovo left join
		add_action('wp_ajax_dbt_edit_sql_query_merge', [$this, 'edit_sql_query_merge']);
		// Apre la sidebar per aggiungere i metadata e seleziona la tabella
		add_action('wp_ajax_dbt_metadata_sql_query_edit', [$this, 'metadata_sql_query_edit']);
		// Sempre per i metadata trova i campi da visualizzare
		add_action('wp_ajax_dbt_metadata_sql_query_edit_step2', [$this, 'metadata_sql_query_edit_step2']);
		// Genera la query con l'aggiunta dei metadata
		add_action('wp_ajax_dbt_edit_sql_addmeta', [$this, 'edit_sql_addmeta']);
		
		// Verifico una query mentre la si sta scrivendo
		add_action('wp_ajax_dbt_check_query', [$this, 'check_query']);

		// Questa una chiamata che deve rispondere un csv
		add_action( 'admin_post_dbt_download_multiquery_report', [$this, 'dbt_download_multiquery_report']);
		// Nell'init (backend gestisco eventuali redirect)
		add_action('init',  [$this, 'template_redirect']);
		add_action('init',  [$this, 'init_get_msg_cookie'] );
	
		// Aggiungo css e js nel frontend
		add_action( 'wp_enqueue_scripts', [$this, 'frontend_enqueue_scripts'] );

		// carico una lista frontend in ajax
		add_action ('wp_ajax_nopriv_dbt_get_list', [$this,'get_list']); 
        add_action ('wp_ajax_dbt_get_list', [$this,'get_list']);

		// carico i dati del dettaglio di un record in ajax
		add_action ('wp_ajax_nopriv_dbt_get_detail', [$this,'get_detail']); 
        add_action ('wp_ajax_dbt_get_detail', [$this,'get_detail']);
       
		//add_filter('query_vars', [$this, 'add_rewrite_rule']);
		add_action ('wp_ajax_dbt_sql_test_replace', [$this,'sql_test_replace']);
		
		add_action ('wp_ajax_dbt_sql_search_replace', [$this,'sql_search_replace']);
		
		add_action('in_admin_header', function () {
			
			if (is_admin() && isset($_REQUEST['page'])  && 
				in_array($_REQUEST['page'],['database_tables', 'dbt_list']) ) { 
				remove_all_actions('admin_notices');
				remove_all_actions('all_admin_notices');
			}
		}, 1000);

		
		if (is_admin())  {
			// Memorizzo le ultime query eseguite per tipo (insert, delete) altrimenti quando provo a mostrarle usando last_query mi capita di vedere una query al posto di un'altra.
			add_filter ( 'query', [$this, 'store_query'] );
			require_once(DBT_DIR . "includes/dbt-loader-documentation.php");
			$Dbt_loader_documentation = new Dbt_loader_documentation();
		}
		// Carico eventuali altri loader
		if ((isset($_REQUEST['section']) && substr($_REQUEST['section'],0,4) == "list") || @$_REQUEST['page'] == 'dbt_list') {
			require_once(DBT_DIR . "includes/dbt-list-loader.php");
			
		} else if (isset($_REQUEST['section'])) {
			switch($_REQUEST['section']) {
				case 'information-schema':
					require_once(DBT_DIR . "includes/dbt-loader-information-schema.php");
					$database_tables_loader_structure = new Dbt_loader_information_schema();
					break;
				case 'table-structure':
					require_once(DBT_DIR . "includes/dbt-loader-structure.php");
					$database_tables_loader_structure = new Dbt_loader_structure();
					break;
				case 'table-import':
					require_once(DBT_DIR . "includes/dbt-loader-import.php");
					$database_tables_loader_import = new Dbt_loader_import();
					break;
			}
			
		}

		add_filter('dbt_table_status', [$this, 'publish_wp_tables'], 2, 2);

	}

	/**
	 * Aggiunge la voce di menu e carica la classe che gestisce la pagina amministrativa
	 */
	public function add_menu_page() {
		require_once(DBT_DIR . "admin/class-dbt-table-admin.php");
		require_once(DBT_DIR . "admin/class-dbt-list-admin.php");
		require_once(DBT_DIR . "admin/class-dbt-docs-admin.php");
		$db_admin = new database_tables_admin();
		add_menu_page( '', 'Database tables', 'manage_options', 'database_tables', [$db_admin, 'controller'], 'dashicons-database-view');

		$db_admin = new DBT_list_admin();
		add_submenu_page(  'database_tables', 'List', 'List', 'manage_options', 'dbt_list', [$db_admin, 'controller']);

		$db_docs = new DBT_docs_admin();
		add_submenu_page(  'database_tables', 'Documentation', 'Documentation', 'manage_options', 'dbt_docs', [$db_docs, 'controller']);

	}
	/**
	 * Gli script per far funzionare l'editor
	 */
	public function codemirror_enqueue_scripts() {
		if ( ! class_exists( '_WP_Editors', false ) ) {
			require( ABSPATH . WPINC . '/class-wp-editor.php' );
		}
		wp_enqueue_editor();

		$settings = wp_get_code_editor_settings([]);
		// copio wp_enqueue_code_editor per escludere 'false' === wp_get_current_user()->syntax_highlighting
		
		if ( empty( $settings ) || empty( $settings['codemirror'] ) ) {
			return false;
		}

		wp_enqueue_script( 'code-editor' );
		wp_enqueue_style( 'code-editor' );

		wp_enqueue_script( 'csslint' );
		wp_enqueue_script( 'htmlhint' );
		wp_enqueue_script( 'jshint' );
		wp_add_inline_script( 'code-editor', sprintf( 'jQuery.extend( wp.codeEditor.defaultSettings, %s );', wp_json_encode( $settings ) ) );
		
	}

	/**
	 * L'ajax per la richiesta dell'elenco dei valori unici per mostrarli nei filtri di ricerca 
	 *  $_REQUEST = array(5) {
	 *	     ["sql"]=> string(26) "SELECT * FROM  `wpx_posts`"["rif"]=> string(21) "wpx_posts_post_author", ["column"]=> string(25) "`wpx_posts`.`post_author`", ["action"]=> string(19) "dbt_distinct_values", ["table"]=> string(9) "wpx_posts"
	 *  }
	 *  [{c=>il testo del campo distinct, p=>l'id se serve di filtrare per id oppure -1 n il numero di volte che compare},{}] | false if is not a select query 
	 *
	 */
	public function dbt_distinct_values() {
		global $wpdb;
		Dbt_fn::require_init();
		if (!isset($_REQUEST['column'])) {
			wp_send_json(['error' => 'no_column_selected']);
			die();
		}
		
		$model = new Dbt_model(@$_REQUEST['table']); // è la tabella a cui appartiene il singolo campo!
		if (isset($_REQUEST['sql'])) {
			$req_sql = html_entity_decode($_REQUEST['sql']);
			$model->prepare($req_sql);
			if (!isset($_REQUEST['dbt_id'])) {
				$model->removes_column_from_where_sql($_REQUEST['column']);
			}
		}
		$result = $model->distinct($_REQUEST['column'], @$_REQUEST['filter_distinct']);
		
		$error = "";
		$count = 0;
		if ($model->last_error != "" || !is_countable($result)) {
			$error = __('Option not available for this query '.$model->last_error." ".$model->get_current_query(),'database_tables');
			$result = [];
		} else if ( count($result) >= 1000) {
			//$error = __('The column has too many values to show.<br><i style="font-size:.8em">You can use the field above to filter the results</i>','database_tables');
			$count = count ($result);
			if (count($result) >= 5000) {
				$count = "5000+";
			} 
			$result = [];
			
		} else {
			$count = count ($result);
		}
		
		wp_send_json(['error' => $error, 'result' => $result, 'rif' => $_REQUEST['rif'], 'count'=>$count, 'filter_distinct'=>@$_REQUEST['filter_distinct'] ]);
		die();
	}
	
	/**
	 * L'ajax per la richiesta dell'elenco dei valori unici per mostrarli nei filtri di ricerca 
	 *  $_REQUEST = array(5) {
	 *	     ["sql"]=> string(26) "SELECT * FROM  `wpx_posts`"["rif"]=> string(21) "wpx_posts_post_author", ["column"]=> string(25) "`wpx_posts`.`post_author`", ["action"]=> string(19) "dbt_distinct_values", ["table"]=> string(9) "wpx_posts"
	 *  }
	 *  [{c=>il testo del campo distinct, p=>l'id se serve di filtrare per id oppure -1 n il numero di volte che compare},{}] | false if is not a select query 
	 */
	public function dbt_autocomplete_values() {
		global $wpdb;
		Dbt_fn::require_init();
		if (!isset($_REQUEST['params'])) {
			wp_send_json(['error' => 'no_params_selected']);
			die();
		}
		$params = $_REQUEST['params'];
		$result = [];
		$error = "";
		// TODO REQUEST TABLE E COLUMN devono trasformarsi in un generico attributes... perché esistono più tipi di distinct values (e in alcuni casi potrebbero non essere distinct (?!))
		if (isset($params['type']) && $params['type'] == "post") {
			$count = 2000;
			$array_params = array(
				'post_limits'      => 100,
				'orderby'          => 'post_title',
				'order'            => 'ASC',
				'post_type'        => $params['post_types'],
				'post_status'      => 'publish'
			);
			if (isset($params['cats']) && is_countable(($params['cats']))) {
				$array_params['category__in'] = $params['cats'];
			}
			if (isset($_REQUEST['filter_distinct'])) {
				$array_params['search'] = $_REQUEST['filter_distinct']."*";
			}
			$posts = query_posts($array_params);
			foreach($posts as $rl) {
				$result[] = ['c' => wp_trim_words(strip_tags($rl->post_title), 10), 'p'=>$rl->ID];
			}
			if (count($result) < 100) {
				$count = count($result);
			}
	

		} else if (isset($params['type']) && $params['type'] == "user") {
			$count = 2000;
			$array_params = array('number'=>100);
			if (isset($params['roles']) && is_countable(($params['roles']))) {
				$array_params['role__in'] = $params['roles'];
			}
			if (isset($_REQUEST['filter_distinct'])) {
				$array_params['search'] = $_REQUEST['filter_distinct']."*";
			}
			$users = get_users( $array_params );
			foreach($users as $rl) {
				$result[] = ['c' => wp_trim_words(strip_tags($rl->user_login), 10), 'p'=>$rl->ID];
			}
			if (count($result) < 100) {
				$count = count($result);
			}
		} else if (isset($params['type']) && $params['type'] == "lookup") {
			
			$count = -1;
			$post       = Dbt_functions_list::get_post_dbt(@$params['lookup_id']);
			$sql =  @$post->post_content['sql'];
			if ( $sql != "") {
				$result = [];
				$table_model = new Dbt_model();
				$table_model->prepare($sql);
				$table_model->list_change_select('`'.esc_sql($params['lookup_sel_val']).'` AS val, `'.esc_sql($params['lookup_sel_txt']).'` AS txt');
				if (isset($_REQUEST['filter_distinct'])) {
					$table_model->list_add_where([['op'=>'LIKE%','column'=>$params['lookup_sel_txt'], 'value'=>$_REQUEST['filter_distinct']]]);
					//$array_params['search'] = $_REQUEST['filter_distinct']."*";
				}
				$table_model->list_add_limit(0, 100);
				$table_model->list_add_order('`'.$params['lookup_sel_txt'].'`', 'ASC');
				//$error = $sql." | ".$table_model->get_current_query();
				$items = $table_model->get_list();
				if ($table_model->last_error != "" || !is_countable($result)) {
					$error = __('Query Error '.$table_model->last_error, 'database_tables');
				} else {
					array_shift($items);
					foreach($items as $rl) {
						$result[] = ['c' => wp_trim_words(strip_tags($rl->txt), 10), 'p'=>$rl->val];
					}
					$count = $table_model->get_count();
				}
			} else {
				$error = _e("List is corrupted", 'database_tables');
			}
		} else {
			$model = new Dbt_model($params['table']); // è la tabella a cui appartiene il singolo campo!
			//TODO questo lo prendo da dbt_id se è presente altrimenti dalla query
			if (isset($_REQUEST['sql'])) {
				$model->prepare(html_entity_decode($_REQUEST['sql']));
			}
			$result = $model->distinct($params['column'], $_REQUEST['filter_distinct']);
			
			$error = "";
			$count = 0;
			if ($model->last_error != "" || !is_countable($result)) {
				$error = __('Option not available for this query','database_tables');
				$result = [];
			} else if ( count($result) >= 1000) {
				//$error = __('The column has too many values to show.<br><i style="font-size:.8em">You can use the field above to filter the results</i>','database_tables');
				$count = count ($result);
				if (count($result) >= 5000) {
					$count = "5000+";
				} 
				$result = [];
				
			} else {
				$count = count ($result);
			}
		}
		wp_send_json(['error' => $error, 'result' => $result, 'rif' => $_REQUEST['rif'], 'count'=>$count, 'filter_distinct'=>@$_REQUEST['filter_distinct'] ]);
		die();
	}
	
	/**
	 * L'ajax per la eseguire altre multiqueries. Output Json
	 * I dati vengono ricevuti dal $_REQUEST { ["action"]=> string(21) "dbt_multiqueries_ajax" ["filename"]=> string(13) "612245f28a809"} 
	 */
	public function dbt_multiqueries_ajax() {
		Dbt_fn::require_init();
		$filename = Dbt_fn::sanitaze_request('filename','');
		$ignore_errors = Dbt_fn::get_request('ignore_errors', false, 'boolean');
		$ris = Dbt_fn::execute_multiqueries($filename, $ignore_errors);
		unset($ris['model']);
		unset($ris['items']);
		wp_send_json($ris);
	}

	/**
	 * Scarica il report delle multiqueri
	 */
	public function dbt_download_multiquery_report() {
		Dbt_fn::require_init();
		$temporaly = new Dbt_temporaly_files();
		$fnid = Dbt_fn::sanitaze_request('fnid','');
		$data = $temporaly->read($fnid);
	//	print $temporaly->get_dir();
		$query_to_execute = $temporaly->read($data['queries_filename']);
		foreach ($data['report_queries'] as $key=>$query) {
			$data['report_queries'][$key] = [@$query[0], @$query[1][1]["effected_row"], @$query[2] , @$query[3] ];
		}
	
		if ($temporaly->last_error == "" && $query_to_execute != false && (is_array($query_to_execute) || is_object($query_to_execute))) {
			foreach ($query_to_execute as $query) {
				$data['report_queries'][] =  [$query[0], 0, 0, __('Query not executed', 'database_tables')];
			}
		}

		Dbt_fn::export_data_to_csv($data['report_queries'], $_REQUEST['fnid'],  ';', '"',  false);
	}

	/**
	 * I casi del template redirect
	 */
	public function template_redirect() {
		if (is_admin() && isset($_REQUEST['page'])  && $_REQUEST['page'] == 'database_tables') {
			if (isset($_REQUEST['section']) && ($_REQUEST['section'] == 'table-structure') ) {
				if (!isset($_REQUEST['action']) && !isset($_REQUEST['table'])) {
					wp_redirect(admin_url('?page=database_tables&section=information-schema') );
					die();
				}
			}
			if (isset($_REQUEST['section']) && ($_REQUEST['section'] == 'table-browse') ) {
				if (!isset($_REQUEST['table']) || $_REQUEST['table'] == "") {
					if (!$_REQUEST['custom_query']) {
						wp_redirect(admin_url('?page=database_tables&section=information-schema') );
						die();
					}
				}
			}

		}
	}

	/**
	 * Aggiungo le voci di menu delle liste
	 * postmeta _dbt_admin_show 
	 * ```json
	 * {"page_title":"dbt_174","menu_title":"connection","capability":"manage_options","slug":"dbt_174"}
	 * ```
	 */
	public function init_add_menu_page() {
		global $wpdb;
		$pages = $wpdb->get_results("SELECT * FROM ".$wpdb->postmeta ." WHERE meta_key = '_dbt_admin_show'");
		if (is_countable($pages)) {
			require_once(DBT_DIR . "admin/class-dbt-admin-menu.php");
			$db_admin = new DBT_admin_list_menu();
			foreach ($pages as $page) {
				$page_data = maybe_unserialize(@$page->meta_value);
				if (is_countable($page_data) && $page_data['show'] == 1) {
					add_menu_page($page_data['page_title'], $page_data['menu_title'], $page_data['capability'], $page_data['slug'], [$db_admin, 'controller'], $page_data['menu_icon'], $page_data['menu_position']);
				}
			}
			
		}
	}

	/**
	 * Aggiungo gli script frontend
	 */
	public function frontend_enqueue_scripts() {
		$file = plugin_dir_path( __FILE__  );
		$dbt_css_ver = date("ymdGi", filemtime( plugin_dir_path($file) . 'frontend/database-table.css' ));
		$dbt_js_ver = date("ymdGi", filemtime( plugin_dir_path($file) . 'frontend/database-table.js' ));
		wp_register_style( 'dbt_frontend_css',  plugins_url( 'frontend/database-table.css',  $file), false,   $dbt_css_ver );
		wp_enqueue_style( 'dbt_frontend_css' );
		wp_register_script( 'dbt_frontend_js',  plugins_url( 'frontend/database-table.js',  $file), false,   $dbt_js_ver, true);
		//	wp_add_inline_script( 'mytheme-typekit', 'try{Typekit.load({ async: true });}catch(e){}' );
		wp_add_inline_script( 'dbt_frontend_js', 'dbt_post = "'.esc_url( admin_url('admin-ajax.php')).'";', 'before' );
		wp_enqueue_script( 'dbt_frontend_js' );
	}

    /**
     * Restituisce una lista chiamata in ajax (per il frontend - da verificare che sia nel singolo giorno)
     */
    public function get_list() {
	    Dbt_fn::require_init();
	    $result['div'] = $_REQUEST['dbt_div_id'];
		if (isset($_REQUEST['dbt_prefix'])) {
			$prefix = $_REQUEST['dbt_prefix'];
		} else {
			$prefix = "";
		}
	    if (isset($_REQUEST['dbt_extra_attr'])) {
		   $extra_attr = json_decode(base64_decode($_REQUEST['dbt_extra_attr']), true);
		    if (json_last_error() == JSON_ERROR_NONE) {
			    if (isset($extra_attr['request'])) {
				    foreach ($extra_attr['request'] as $key=>$val) {
					   $_REQUEST[$key] = $val;
				    }
					pinacode::set_var('request', $extra_attr['request']);
			    }
			    //if (isset($extra_attr['params'])) {
				//	pinacode::set_var('params', $extra_attr['params']);
				//}
				if (isset($extra_attr['data'])) {
					pinacode::set_var('data', $extra_attr['data']);
				}
				$result['html'] = Dbt::get_list($_REQUEST['dbt_list_id'], true, $extra_attr['params'], $prefix);
			} else {
				$result['html'] = 'OPS an error occurred';
			}
		} else {
			$result['html'] = Dbt::get_list($_REQUEST['dbt_list_id'], true, [], $prefix);
		}
		wp_send_json($result);
	    die();
    }

	/**
	 * Carico i dettagli di una lista in detail
	 *
	 * @return void
	 */
	public function get_detail() {	
        Dbt_fn::require_init();
		$dbt_id = absint($_REQUEST['dbt_id']);
		$result = Dbt::get_data_by_id($dbt_id, $_REQUEST['dbt_ids']);
		if ($result !== false ) {
			$dbt_post = Dbt_functions_list::get_post_dbt($dbt_id);
			if (isset($dbt_post->post_content['frontend_view']['detail_type']) && $dbt_post->post_content['frontend_view']['detail_type'] != "no") {
				$detail_template = $dbt_post->post_content['frontend_view']['detail_template'];
				PinaCode::set_var('data', $result) ;
				echo PinaCode::execute_shortcode($detail_template);
			}
		}
		die();
	}

	/**
	 * Restituisce il risultato di una query per una riga
	 */
	public function dbt_view_details() {
		Dbt_fn::require_init();
		$result = $_REQUEST;
		$json_send = ['error' => '', 'items' => ''];
		
		if (!isset($_REQUEST['ids']) || !is_countable($_REQUEST['ids'])) {
			$json_send['error'] = __('I have not found any results. Verify that the primary key of each selected table is always displayed in the MySQL SELECT statement.', 'database_tables');
			wp_send_json($json_send);
			die();
		}
		$table_model = $this->get_table_model_for_sidebar();
		$table_model->remove_limit();
		$table_items = $table_model->get_list();
		//var_dump ($table_items);
		if (is_countable($table_items) && count($table_items) == 2) {
			$item = array_pop($table_items);
			foreach ($item as &$val) {
				$val = Dbt_fn::format_single_detail_value($val);
			}
			$json_send['items'] = [$item]; 	
		} else if (is_countable($table_items) && count($table_items) > 2 && count($table_items) < 200) {
			// Sono più risultati quindi raggruppo i risultati per tabella e mostro solo i gruppi differenti. 
			$items = Dbt_fn::convert_table_items_to_group($table_items);
			if (count($items) > 1) {
				$json_send['error'] = __('The query responded with multiple lines. Verify that the primary key of each selected table is always displayed in the MySQL SELECT statement', 'database_tables');
			}
		
			$json_send['items'] = $items;
		} else if (is_countable($table_items) && count($table_items) > 2 && count($table_items) >= 200) {
			$json_send['error'] = __('I am sorry but I cannot show the requested details because I have found more than 200 results!. Verify that the primary key of each selected table is always displayed in the MySQL SELECT statement. Check that the tables have a unique auto increment primary key.', 'database_tables');
		}else {
			$json_send['error'] = __('Strange, I have not found any results. Verify that the primary key of each selected table is always displayed in the MySQL SELECT statement. Check that the tables have a unique auto increment primary key.', 'database_tables');
		}
		wp_send_json($json_send);
		die();
	}

	/**
	 * Genera i parametri per la creazione della form (add o edit) nella sidebar
	 * @since v0.5 
	 */
	public function dbt_edit_details_v2() {
		Dbt_fn::require_init();
		$json_send = ['error' => '', 'items' => ''];
		if (isset($_REQUEST['div_id'])) {
			$json_send['div_id'] = $_REQUEST['div_id'];
		}
		
		if (!isset($_REQUEST['dbt_id']) && !isset($_REQUEST['sql'])) {
			$json_send['error'] = __('There was an unexpected problem, please try reloading the page.', 'database_tables');
			wp_send_json($json_send);
			die();
		}
		
		$json_send['edit_ids'] = Dbt_fn::get_request('ids', 0);
		
		if (isset($_REQUEST['sql']) && $_REQUEST['sql'] != "") {
            $form = new Dbt_class_form($_REQUEST['sql']);
			$json_send['sql'] = $_REQUEST['sql'];
        } else {
            $form = new Dbt_class_form($_REQUEST['dbt_id']);
			$json_send['dbt_id'] = $_REQUEST['dbt_id'];
        }
		if (isset($_REQUEST['ids']) && is_countable($_REQUEST['ids'])) {
			$items = $form->get_data($_REQUEST['ids']);
		} else {
			$items = [];
		}
		//var_dump ($_REQUEST['ids']);
	
        list($settings, $table_options) = $form->get_form();
        $json_send['items'] = $form->convert_items_to_groups($items, $settings, $table_options);
		$json_send['params'] = $form->data_structures_to_array($settings);
		$json_send['table_options'] = $form->data_structures_to_array($table_options);	
		
		wp_send_json($json_send);
		die();
	}

	/**
	 * Salva un record
	 */
	public function dbt_save_details() {
		global $wpdb;
		Dbt_fn::require_init();
		$json_result = ['reload'=>0,'msg'=>'','error'=>''];
		if (isset($_REQUEST['div_id'])) {
			$json_result['div_id'] = $_REQUEST['div_id'];
		}
		$queries_executed = [];
		$query_to_execute = [];
		$dbt_id = 0;
		$form_dbt_id = false;
		$request_edit_table = $_REQUEST['edit_table'];

		// se è una lista ok, altrimenti solo gli amministratori possono salvare dati
		if (isset($_REQUEST['dbt_global_list_id']) && absint($_REQUEST['dbt_global_list_id']) > 0) {
			$dbt_id = absint($_REQUEST['dbt_global_list_id']);
			$form_dbt_id = new Dbt_class_form($dbt_id);
			list($request_edit_table, $error) 
			=Dbt_functions_list::process_saving_data_using_form_list($request_edit_table, $_REQUEST['dbt_global_list_id']);
			if ($error != "") {
				$json_result['result'] = 'nook';
				$json_result['error'] = __($error, 'database_tables');
				wp_send_json($json_result);
				die();
			}
			
		} else {
			if( !current_user_can('administrator') ) {
				$json_result['result'] = 'nook';
				$json_result['error'] = __('You do not have permission to access this content!', 'database_tables');
				wp_send_json($json_result);
				die();
			}
		}
		
		foreach ($request_edit_table as $form_value) {
			$alias_table = "";
			foreach ($form_value as $table=>$rows) {
				//print $table;
				$primary_key = Dbt_fn::get_primary_key($table);
				$primary_field = $fields_names = [];
				
				foreach ($rows as $key=>$row) {
					if ($key == $primary_key) {
						$primary_field = $row;
					} else {
						$fields_names[$key] = $row;
					}
				}
				// ciclo per più query.
				$exists = 0;
				$primary_value = "";
				// ciclo quante volte si ripete la chiave primaria per la tabella (ogni volta è una nuova riga)
				foreach ($primary_field as $key => $pri) {
					$sql = [];
					$exists = 0;
					$primary_value = $pri;
					// l'alias della tabella sta in un campo nascosto e serve per definire i pinacode
					if (isset($fields_names["_dbt_alias_table_"][$key]))  {
						$alias_table = $fields_names["_dbt_alias_table_"][$key];
					}
					if ($alias_table == "") {
						$alias_table = $table;
					}
					//print "ALIAS TABLE:" . $alias_table;
					$pri_name = Dbt_fn::clean_string($alias_table).'.'.Dbt_fn::clean_string($primary_key);
					PinaCode::set_var($pri_name, $primary_value);
					// preparo i campi da salvare 
					// Setto le variabili per i campi calcolati // DA TESTARE
					foreach ($fields_names as $kn=>$fn) {
						if ($kn != "_dbt_alias_table_") {
							// ?
							if (is_countable($fn[$key])) {
								$fn[$key] = maybe_serialize($fn[$key]);
							}
							$fn[$key] = stripslashes( $fn[$key] );
							//$sql[$kn] = $fn[$key];
							PinaCode::set_var(Dbt_fn::clean_string($alias_table).".".Dbt_fn::clean_string($kn), $fn[$key]);
						}
					}
				
					foreach ($fields_names as $kn=>$fn) {
						if ($kn != "_dbt_alias_table_") {
							$sql[$kn] = stripslashes( $fn[$key] );
						}
					}
					
					// se primary key è un valore 
					if ($primary_value != "") {
						$exists = $wpdb->get_var('SELECT count(*) as tot FROM `'.$table.'` WHERE `'.esc_sql($primary_key).'` = \''.esc_sql($primary_value).'\'');
						if ($exists == 0) {
							$sql[$primary_key] = $primary_value;
						}
					} else {
						$sql[$primary_key] = $primary_value;
					}
					$setting = false;
					if (is_a($form_dbt_id, 'DatabaseTables\Dbt_class_form')) {
						$setting = $form_dbt_id->find_setting_from_table_field($alias_table);
					}
					if ($exists == 1) {
						if (count($sql) > 0) {
							$query_to_execute[] = ['action'=>'update', 'table'=>$table, 'sql_to_save'=>$sql, 'id'=> [$primary_key=>$primary_value], 'table_alias'=>$alias_table, 'pri_val'=>$primary_value, 'pri_name'=>$primary_key, 'setting' => $setting];
							
						}
					} else if ($exists == 0) {
						$json_result['reload'] = 1;
						if (isset($sql[$primary_key])) {
							unset($sql[$primary_key]);
						}
						
						if (count($sql) > 0 &&  !(isset($sql['_dbt_leave_empty_']) && $sql['_dbt_leave_empty_'] == 1 )) {
							$query_to_execute[] = ['action'=>'insert', 'table'=>$table, 'sql_to_save'=>$sql, 'table_alias'=>$alias_table, 'pri_val'=>$primary_value, 'pri_name'=>$primary_key, 'setting' => $setting];
						}
					} else {
						// ha trovaro risultati doppi?
					}
				}
				//die($pri);
			}
		}
		//var_dump ($query_to_execute);
		$ris =  Dbt_functions_list::execute_query_savedata($query_to_execute, $dbt_id, 'admin-form');
		

		foreach ($ris as $r) {
			if (!($r['result'] == true || ($r['result'] == false && $r['error'] == "" && $r['action']=="update"))) {	
				$json_result['error'] = ($r['error'] != "") ? $r['error'] : 'the data could not be saved';
				Dbt_fn::set_cookie('error', $json_result['error']);
				if (is_countable($queries_executed) && count($queries_executed) > 0) {
					$json_result['msg'] =sprintf( __('%s queries were executed successfully:','database_tables'), count($queries_executed))."<br>".implode("<br>", $queries_executed);
					Dbt_fn::set_cookie('msg', $json_result['msg']);
				}
				wp_send_json($json_result);
				die();
			} else {
				$queries_executed[] =  $r['query'];
			} 
		}

		if (is_countable($queries_executed) && count($queries_executed) > 0) {
			$json_result['msg'] = sprintf(__('%s queries were executed successfully:','database_tables'), count($queries_executed))."<br>".implode("<br>", $queries_executed);
			Dbt_fn::set_cookie('msg', $json_result['msg']);
		}
		// preparo i dati da inviare per aggiornare la tabella nel frontend!
		$dbt_global_list_id = Dbt_fn::get_request('dbt_global_list_id', 0, 'absint');
		$table_model = $this->get_table_model_for_sidebar($dbt_global_list_id);
		if ($table_model != false) {
			$table_model->get_list();
			if (isset($_REQUEST['dbt_global_list_id'])) {
				$post = Dbt_functions_list::get_post_dbt($dbt_global_list_id);
				if (isset($post->post_content)) {
					$table_model->update_items_with_setting($post);
				}
			}
			Dbt_fn::remove_hide_columns($table_model);
			$table_items = $table_model->items;
			if (count($table_model->items) == 2) {
				$json_result['table_item_row'] = array_pop($table_items);
			} else {
				$json_result['reload'] = 1;
			}
		}
		//$json_result['error'] = 'OPS ERRORE!!!';
		wp_send_json($json_result);
		die();
	}
	
	/**
	 * Calcola quali record sta per eliminare a seconda della query e delle primary ID
	 */
	public function dbt_delete_confirm() {
		Dbt_fn::require_init();
		$json_send = [];
		//$json_send = ['error' => '', 'items' => '', 'checkboxes'];
		if (!isset($_REQUEST['ids']) || !is_countable($_REQUEST['ids'])) {
			$json_send['error'] = __('I have not found any results. Verify that the primary key of each selected table is always displayed in the MySQL SELECT statement.', 'database_tables');
			wp_send_json($json_send);
			die();
		}
		if (isset($_REQUEST['dbt_id']) && $_REQUEST['dbt_id']  > 0) {
			$json_send = Dbt_fn::prepare_delete_rows($_REQUEST['ids'],'', $_REQUEST['dbt_id']);
        } else if ($_REQUEST['sql'] != "") {
			$json_send = Dbt_fn::prepare_delete_rows($_REQUEST['ids'], $_REQUEST['sql'] );
        } else {
			$json_send['error'] = __('Something wrong', 'database_tables');
			wp_send_json($json_send);
			die();
		}
		unset($json_send['sql']);
		wp_send_json($json_send);
		die();
		
	}


	/**
	 * bulk delete on sql: 
	 * Scelgo da quali tabelle rimuovere i dati
	 */
	function dbt_check_delete_from_sql() {
		Dbt_fn::require_init();
		$errors = [];
		$table_model = new Dbt_model();
		$table_model->prepare(Dbt_fn::get_request('sql', ''));
		$table_items = $table_model->get_list();
		if ($table_model->last_error ) {
            $error =  $table_model->last_error."<br >".$table_model->get_current_query();
			wp_send_json(['items'=>[],'error'=>$error]);
			die();
        }
        if (count($table_items) < 2) {
			wp_send_json(['items'=>[],'error'=>__("There are no records to delete", 'database_tables')]);
			die();
        }
		
		$header = array_shift($table_items);
		// trovo le tabelle interessate
		$temp_groups = [];
		foreach ($header as $key=>$th) {
			if ($th['schema']->table == '' OR $th['schema']->orgtable == '') continue;
			$id = Dbt_fn::get_primary_key($th['schema']->orgtable);
			$option = Dbt_fn::get_dbt_option_table($th['schema']->orgtable);
			if ($option['status'] != "CLOSE" && $id != "") {
				if ($th['schema']->table == $th['schema']->orgtable) {
					$temp_groups[$th['schema']->table] =  $th['schema']->table;
				} else {
					$temp_groups[$th['schema']->table] = $th['schema']->orgtable." AS ". $th['schema']->table;
				}
			}
		}

		wp_send_json(['items'=>$temp_groups, 'error'=>implode("<br>", array_unique($errors))]);
		die();
	}

	/**
	 * Preparo gli id da rimuovere in delete from query
	 */
	function prepare_query_delete() {
		Dbt_fn::require_init();
		$errors = [];
		$table_model = new Dbt_model();
		$tables = Dbt_fn::get_request('tables', 0);
		$limit_start = Dbt_fn::get_request('limit_start', 0);
		$limit = 1000;
		$total = Dbt_fn::get_request('total', 0);
		$filename = Dbt_fn::get_request('dbt_filename', '');
		$table_model->prepare(Dbt_fn::get_request('sql', ''));
		$table_model->add_primary_ids();
		$table_model->list_add_limit($limit_start, $limit);
		$table_model->get_list();
		$table_model->update_items_with_setting();

		if ($total == 0) {
			$total = $table_model->get_count();
		}
		$data_to_delete = [];
		$table_items = $table_model->items;
		$temporaly_file = new Dbt_temporaly_files();
		if ($filename != "") {
			$data_to_delete = $temporaly_file->read($filename);
		} else {
			$temporaly_file->read($filename);
		}
		if (count($table_items) > 1) {
			$header = array_shift($table_items);
			$header_pris = [];
			foreach ($header as $key=>$th) {
				if ($th->pri && in_array($th->table, $tables)) {
					if (!isset($data_to_delete[$th->original_table."|".$th->original_name])) {
						$data_to_delete[$th->original_table."|".$th->original_name] = [];
					}
					$header_pris[$key] = $th;
				}
			}
			//var_dump ($table_items);
			foreach($table_items as $item) {
				foreach ($header_pris as $key => $hpri) {
					if (!in_array( $item->$key, $data_to_delete[$hpri->original_table."|".$hpri->original_name])) {
						$data_to_delete[$hpri->original_table."|".$hpri->original_name][] = $item->$key;
					}
				}
			}
			$filename = $temporaly_file->store($data_to_delete, $filename);
		}
		wp_send_json(['executed'=>$limit_start+$limit, 'total'=>$total, 'filename'=>$filename]);
		die();
	}

	function sql_query_delete() {
		global $wpdb;
		Dbt_fn::require_init();
		$filename = Dbt_fn::get_request('dbt_filename', '');
		$temporaly_file = new Dbt_temporaly_files();
		$data_to_delete = $temporaly_file->read($filename);
		$total = Dbt_fn::get_request('total', 0);
		$base_executed = $executed = Dbt_fn::get_request('executed', 0);
		$limit = 1000;
		//$data_to_delete[$th->original_table."|".$th->original_name] 
		if ($total == 0) {
			
			foreach ($data_to_delete as $dtd) {
				foreach ($dtd as $id) {
					$total++;
				}
			}
		}
		//ob_start();
		$count = 0;
		foreach ($data_to_delete as $key => $dtd) {
			list($table,$field) = explode("|", $key);
			$query = "DELETE FROM `".esc_sql($table)."` WHERE `".esc_sql($field)."` = '%s';";
			foreach ($dtd as $id) {
				$count++;
				if ($count <= $base_executed) continue;
				if ($count > $base_executed + $limit) break;
				$executed++;
				//print sprintf($query, absint($id));
				if (absint($id) > 0) {
					$wpdb->query(sprintf($query, absint($id)));
				}
			}
		}
		//$html = ob_get_clean();
		wp_send_json(['executed'=>$executed, 'total'=>$total, 'filename'=>$filename]);
		die();
	}
	
	/**
	 * Prepara il csv 
	 */
	function dbt_download_csv() {
		Dbt_fn::require_init();
		$temporaly_files = new Dbt_temporaly_files();
		$csv_filename = Dbt_fn::get_request('csv_filename', '');
		$request_ids = Dbt_fn::get_request('ids', false);
		$limit_start = Dbt_fn::get_request('limit_start', 0);
		$dbt_id		 = Dbt_fn::get_request('dbt_id', 0);
		if ($dbt_id > 0) {
			$post = Dbt_functions_list::get_post_dbt($dbt_id);
		}
		if ($limit_start == 0) {
			$temporaly_files->clear_old();
		}
		if ($request_ids == false || !is_countable($request_ids)) {
			// estraggo i dati dalla query
			$line = 2000;
			$next_limit_start = $limit_start + $line;
			$table_model = new Dbt_model();
			$table_model->prepare(Dbt_fn::get_request('sql', ''));
			$table_model->list_add_limit($limit_start, $line);
			if ($dbt_id > 0) {
				if (isset($post->post_content['sql_order']['sort']) &&  isset($post->post_content['sql_order']['field'])) {
					$_REQUEST['sort']['field'] = $post->post_content['sql_order']['field'] ;
					$table_model->list_add_order($post->post_content['sql_order']['field'], $post->post_content['sql_order']['sort']);
				}
			}
			$table_items = $table_model->get_list();
			$count = $table_model->get_count();
			if ($dbt_id > 0) {
				$table_model->update_items_with_setting($post);
				Dbt_fn::remove_hide_columns($table_model);
				$table_items = [];
				/*
				// TODO come lo gestisco?
				// se modifichi una tabella e non risalvi list_setting questa non sarà allineata con i risultati!!
				foreach ($table_model->items as $key => $column) {
					$temp_item = [];
					// rimuovo i campi delle chiavi primarie aggiunte di nascosto nelle query e cambio il nome delle colonne
					foreach ($post->post_content['list_setting'] as $name_key => $header_col) {
						$temp_item[$header_col->title] = $column->$name_key;
					}
					$table_items[] = $temp_item;
				}
				*/
				$table_items = $table_model->items;
			}
			// verifico che la query non abbia dato errore
			if ($table_model->last_error ) {
				$error =  $table_model->last_error."<br >".$table_model->get_current_query();
				wp_send_json(['error'=>$error]);
				die();
			}
			if (count($table_items) < 2 && $limit_start+2 < $count) {
				wp_send_json(['error'=>__("There was an unexpected problem", 'database_tables')]);
				die();
			}
			 array_shift($table_items);
		} else {
			$line = 200;
			// estraggo i dati dalla query solo per i checkbox selezionati
			$table_items = [];
			$next_limit_start = $limit_start + $line;
			$count = count($request_ids);
			$foreach_count = 0;
			foreach ($request_ids as $ids) {
				$foreach_count++;
				if ($foreach_count <= $limit_start) continue;
				if ($foreach_count > $next_limit_start) break;
				$filter = [];
				$table_model = new Dbt_model();
				$table_model->prepare(Dbt_fn::get_request('sql', ''));
				foreach ($ids as $column=>$id) {
					$temp_col = explode(".",$column);
					$table = '`'.esc_sql(array_shift($temp_col)).'`';
					$field = '`'.esc_sql(implode(".", $temp_col)).'`';
					$filter[] = ['op'=>"=", 'value'=>$id, 'column'=>$table.'.'.$field];
				}
				$table_model->list_add_where($filter);
				$table_items_temp = $table_model->get_list();
				if (count($table_items_temp) == 2) {
					$table_items[] = array_pop($table_items_temp);
				}

			}
		}
		// rimuovo la prima riga che è l'header con lo schema della tabella.
		$csv_filename = $temporaly_files->store_csv($table_items, $csv_filename, ";", true);
		//
		$link = add_query_arg(['section'=>'table-import', 'action'=>'dbt_download_csv_report','filename'=>$csv_filename],  admin_url("admin-post.php"));
		wp_send_json(['link' => $link,  'msg' => '', 'error' => '', 'count' => $count, 'next_limit_start' => $next_limit_start, 'filename' => $csv_filename]);
		die();
	}

	/**
	 *  Estraggo tutte le colonne possibili che si possono visualizzare da una query.
	 *  Chiamato dal bottone ORGANIZE COLUMNS
	 */
	function columns_sql_query_edit() {
		Dbt_fn::require_init();
		$table_model = new Dbt_model();
		$sql = html_entity_decode(Dbt_fn::get_request('sql'));
		$table_model->prepare($sql);
		if ($sql != "" && $table_model->sql_type() == "select") {
			$all_fields = $table_model->get_all_fields_from_query();
			//Todo trovo le colonne originali della query per impostare i checkbox checked.
			$table_model->prepare($sql);
			
			$header = $table_model->get_schema();
			if ($table_model->last_error != "") {
				wp_send_json(['msg' => sprintf(__("Ops Query Error: %s ",'database_tables'), $table_model->last_error)]);
				die;
			}
			// data without as serve per capire se ci sono funzioni nella query
			// trovo un array con le colonne della query che non fanno parte dei campi del db tipo CONCAT()
			$new_fields = $table_model->get_original_column_name();
			//var_dump ($new_fields);
			$sql_fields = [];
			$all_fields2 = [];
			$unique_names = [];
			if (is_countable($header)) {
				foreach ($header as $k=>$h) {
					if (isset($h->table) && isset($h->orgname) && array_key_exists('`'.$h->table.'`.`'.$h->orgname.'`', $all_fields)) {
						$as_name =  $h->name;
						$count = 1;
						while (in_array($as_name, $unique_names) && $as_name != "" && $count < 9999) {
							$as_table =  ($h->table != $h->orgtable) ? $h->table : $h->orgtable;
							$as_name = strtolower(str_replace(" ","", $as_table."_".$h->name ."_".$count));
							$count++;
						}
						$unique_names[] = $as_name;
						$as_name = ($as_name != $h->orgname) ? $as_name : '';
						$sql_fields['`'.$h->table.'`.`'.$h->orgname.'`'] = $as_name;
						$all_fields2['`'.$h->table.'`.`'.$h->orgname.'`'] = $all_fields['`'.$h->table.'`.`'.$h->orgname.'`'];
						unset($all_fields['`'.$h->table.'`.`'.$h->orgname.'`']);
					} else if (isset($new_fields[$h->name])) {
						
						$sql_fields[$new_fields[$h->name]] = $h->name;
						$all_fields2[$new_fields[$h->name]] = $h->name;
					}
				}
			}
			$all_fields2 = array_merge($all_fields2, $all_fields);
			if (is_countable($all_fields2) && count($all_fields2) > 0) {
				wp_send_json(['all_fields' => $all_fields2, 'sql_fields' => $sql_fields ]);
			} else {
				wp_send_json(['msg' => __("I'm sorry, but I can't extract the query columns",'database_tables'),  'html'=>'']);
			}
		}  else {
			wp_send_json(['msg' => __("I'm sorry, but I can't extract the query columns",'database_tables'),  'html'=>'']);
		}
		die();
	}

	/**
	 * Ricevo una query e un elenco di colonne da visualizzare. Ritorna la query con il nuovo select
	 * Chiamato dal bottone ORGANIZE COLUMNS
	 */
	function edit_sql_query_select() {
		Dbt_fn::require_init();
		$table_model = new Dbt_model();
		$table_model->prepare(Dbt_fn::get_request('sql', ''));
		if (Dbt_fn::get_request('sql') != "" && $table_model->sql_type() == "select") {
			// preparo la stringa con il nuovo select
			$choose_columns = Dbt_fn::get_request('choose_columns');
			$columns_as = Dbt_fn::get_request('label');
			$select = [];
			$as_unique = [];
			foreach ($choose_columns as $key => $value) {
				if (isset($columns_as[$key]) && trim($columns_as[$key]) != "" ) {
					$as = str_replace("`","'", stripslashes(trim($columns_as[$key])));
					$count_while = 0;
					while (in_array($as, $as_unique) && $count_while < 999) {
						$as = $columns_as[$key] ."_".$count_while;
						$count_while++;
					}
					$select[] = $value." AS `".$as."`";
					$as_unique[] = $as;
				} else {
					$select[] = $value;
					$val = explode(".", $value);
					$val = array_pop($val);
					$as_unique[] = trim(str_replace('`','',$val));
				}
			}
			$table_model->list_change_select(implode(", ", $select));
			$new_query = $table_model->get_current_query();
			/**
			 * Ricarico l'html del box della query
			 */
			$table_model->remove_limit();
			$html = Dbt_html_sql::get_html_fields($table_model);

			wp_send_json(['sql' => $new_query, 'html'=>$html]);
		} else {
			//TODO ERROR!
			wp_send_json(['msg' => __("I'm sorry, but I can't extract the query columns",'database_tables')]);
		}
		die();
	}

	/**
	 * Estraggo i parametri per preparare la form per aggiungere un join ad una query
	 * Chiamato dal bottone MERGE
	 */
	function merge_sql_query_edit() {
		Dbt_fn::require_init();
		$table_model = new Dbt_model();
		$sql = html_entity_decode(Dbt_fn::get_request('sql'));
		$table_model->prepare($sql);
		if ($sql != "" && $table_model->sql_type() == "select") {
			$all_fields = $table_model->get_all_fields_from_query();
			$all_tables = Dbt_fn::get_table_list();
			if (is_countable($all_fields) && count($all_fields) > 0 && is_countable($all_tables) && count($all_tables) > 0) {
				wp_send_json(['all_fields' => $all_fields, 'all_tables' => $all_tables['tables']]);
			} else {
				wp_send_json(['msg' => __('The current query cannot be joined to other tables','database_tables')]);
			}
		} else {
			wp_send_json(['msg' => __('The current query cannot be joined to other tables','database_tables')]);
		}
		die();
	}

	/**
	 * Estraggo i parametri per preparare la form per aggiungere un join ad una query
	 * Chiamato dal bottone MERGE
	 */
	function merge_sql_query_get_fields() {
		Dbt_fn::require_init();
		$table = esc_sql(Dbt_fn::get_request('table'));
		$all_columns = Dbt_fn::get_table_structure($table, true);
		wp_send_json(['all_columns' => $all_columns]);
		die();
	}

	/**
	 * Genero la query con il  join
	 */
	function edit_sql_query_merge() {
		global $wpdb;
		Dbt_fn::require_init();
		if (!isset($_REQUEST['dbt_merge_table']) || !isset($_REQUEST['dbt_merge_column']) ||  !isset($_REQUEST['dbt_ori_field'])) {
			wp_send_json(['msg' => __('All fields are required','database_tables')]);
			die();
		}
		//var_dump ($_REQUEST);
		$table_model = new Dbt_model();
		$sql = html_entity_decode(Dbt_fn::get_request('sql'));
		$table_model->prepare($sql);
		if ($sql != "" && $table_model->sql_type() == "select") {
			$sql_schema = $table_model->get_schema();
			$temp_curr_query = $table_model->get_current_query();
			// trovo l'alias della tabella di cui si sta facendo il join
			// TODO ho una funzione apposta per questo da sostituire
			$table_alias_temp  = substr(Dbt_fn::clean_string(str_replace($wpdb->prefix, "", $_REQUEST['dbt_merge_table'])),0 ,3);
			if (strlen($table_alias_temp) < 3 ) {
				$table_alias_temp = $table_alias_temp.substr(md5($table_alias_temp),0 , 2);
			}
			$table_alias = $table_alias_temp;
			$count_ta = 1;
			while(stripos($temp_curr_query, $table_alias.'`') !== false || stripos($temp_curr_query, $table_alias.' ') !== false) {
				$table_alias = $table_alias_temp.''.$count_ta;
				$count_ta++;
			}
			// compongo la nuova porzione di query
			$join = $_REQUEST['dbt_merge_join'];
			if (!in_array($join, ['INNER JOIN','LEFT JOIN','RIGHT JOIN'])) {
				$join ='INNER JOIN';
			}
			$join = $join.' `'.$_REQUEST['dbt_merge_table'].'` `'.$table_alias.'`';
			$join .= " ON `" . $table_alias . "`.`" . $_REQUEST['dbt_merge_column'] . '` = '. $_REQUEST['dbt_ori_field'];
			// la unisco alla query originale
			//$table_model->list_add_select(''); // serve per convertire l'* in table.*
			$table_model2 =  new Dbt_model();
			$table_model->list_add_from($join);
			// Modifico il select aggiungo i nuovi campi:
			// duplico il model per avere lo schema dei dati da inserire. Non uso l'* perché 
			// genera colonne duplicate!
			$table_model2->prepare($table_model->get_current_query());
			$table_model2->list_add_select('`' . $table_alias . '`.*');
			$sql_schema2 = $table_model2->get_schema();
			
			$add_select = [];
			$sql_query_temp = $table_model->get_partial_query_select(); // Il select per evitare colonne duplicate
			if (is_countable($sql_schema2)) {
				foreach ($sql_schema2 as $field) {
				
					if (isset($field->orgtable) && $field->orgtable != "" && isset($field->table) && $field->table == $table_alias) {
						
						$new_name = Dbt_fn::get_column_alias(strtolower($table_alias . '_' .substr(str_replace(" ", "_", $field->name), 0, 50)), $sql_query_temp);
						$sql_query_temp .= " ".$new_name;
						
						$add_select[] = '`' . $table_alias . '`.`' .$field->name . '` AS `'.$new_name.'`';
					}
				}
				
				if (count($add_select) > 0) {
					$table_model->list_add_select(implode(", ", $add_select));
				}
			} else {
				// annullo tutto!
				$table_model->remove_limit();
				$html = Dbt_html_sql::get_html_fields($table_model);
				wp_send_json(['sql' => $table_model->get_current_query(), 'error'=>$table_model->last_error, 'html'=>$html]);
				die();
			}
		}
		$table_model->remove_limit();
		$html = Dbt_html_sql::get_html_fields($table_model);

		wp_send_json(['sql' => $table_model->get_current_query(), 'html'=>$html]);
		die();
	}

	/**
	 * Ritorna i dati per generare la form per l'aggiunta dei metadati alla query
	 * Chiamato dal bottone Add metadata
	 */
	function metadata_sql_query_edit() {
		Dbt_fn::require_init();
		$table_model = new Dbt_model();
		$sql = html_entity_decode(Dbt_fn::get_request('sql'));
		$table_model->prepare($sql);
		if ($sql != "" && $table_model->sql_type() == "select") {
			$tables = [];
			$sql_schema = $table_model->get_schema();
			$pris = [];
			$already_inserted = [];
			if (is_countable($sql_schema)) {
				foreach ($sql_schema as $field) {
					if (isset($field->orgtable) && $field->orgtable != "" && isset($field->table)) {
						$table = $field->orgtable;
						// devo trovare la primary key
						if (!isset($pris[$field->orgtable])) {
							$pris[$field->orgtable] = Dbt_fn::get_primary_key($field->orgtable);
						}
						// se non è già stata inserita
						if (!in_array($table, $already_inserted) && $pris[$field->orgtable] != "") {
							$already_inserted[] = $table;
							$tables[] = [$table  ."meta", $field->table.".".$pris[$field->orgtable]];
							$tables[] = [$table  ."_meta", $field->table.".".$pris[$field->orgtable]];
							if (substr($table,-1) == "s") {
								if (substr($table,-4) == "ches" || substr($table,-4) == "shes" || substr($table,-3) == "ses" || substr($table,-3) == "xes" || substr($table,-3) == "zes") {
									$singular = substr($table,0, -2);
								} else {
									$singular = substr($table,0, -1);
								}
								$tables[] = [$singular ."meta", $field->table.".".$pris[$field->orgtable]];
								$tables[] = [$singular ."_meta", $field->table.".".$pris[$field->orgtable]];
							}
						}
					}
				}
			} else {
				wp_send_json(['msg' => __("I can't find any linkable metadata",'database_tables')]);
			}
		
			$all_tables = Dbt_fn::get_table_list();
			$return_table = [];
			foreach ($all_tables['tables'] as $sql_table) {
				
				$sql_table_name = '';
				foreach ($tables as $val_tab) {
					if ($sql_table == $val_tab[0]) {
						$sql_table_name =  $val_tab[1]."::".$sql_table;
						break;
					}
				}
				if ($sql_table_name != "") {
					$return_table[$sql_table_name] = $sql_table;
				}
			} 
			if (is_countable($return_table) && count($return_table) > 0) {
				wp_send_json(['all_tables' => $return_table]);
			} else {
				wp_send_json(['msg' => __("I can't find any linkable metadata",'database_tables')]);
			}

		} else {
			wp_send_json(['msg' => __("The current query cannot be linked to metadata",'database_tables')]);
		}
		die();
	}

	/**
	 * Estraggo i meta_key, meta_value dalla tabella meta 
	 * Chiamato dopo il bottone Add metadata dal select della tabella
	 */
	function metadata_sql_query_edit_step2() {
		global $wpdb;
		Dbt_fn::require_init();
		$table2 = Dbt_fn::get_request('table2');
		$sql_table_temp = explode("::", $table2);
		$sql = html_entity_decode($_REQUEST['sql']);
		$sql_table = array_pop($sql_table_temp);
	
		$structure = Dbt_fn::get_table_structure($sql_table);
		$table = substr($sql_table,strlen($wpdb->prefix));
		$table = str_replace(["_meta","meta"],"", $table);
		if (substr($table,-1) == "s") {
			$table = substr($sql_table,0, -2);
		}
	//	print $table." ";
		$columns = ['pri'=>'','parent_id'=>''];
		if (count($structure) > 3) {
			foreach ($structure as $field) {
				//var_dump ($field->Field);
				//var_dump (stripos($field->Field, $table));
				if ($field->Key == "PRI") {
					$columns['pri'] = $field->Field;
				} elseif ($field->Field != "meta_key" && $field->Field != "meta_value" && stripos($field->Field, $table) !== false) {
					$columns['parent_id'] = $field->Field;
				}
			}
		}
		$list = [];
		// Aggiungo all'elenco ($list) i meta_key
		if ($columns['pri'] != "" && $columns['parent_id'] != "") {
			$list_db = $wpdb->get_results('SELECT DISTINCT meta_key FROM `'.$sql_table.'` ORDER BY meta_key ASC');
			if (is_countable($list_db)) {
				foreach ($list_db as $d) {
					$list[] = $d->meta_key;
				} 
			}
		}
		// cerco di capire quali metadata sono stati già aggiunti 
		$table_model = new Dbt_model();
		$table_model->prepare($sql);
		$selected = [];
		$from_sql = $table_model->get_partial_query_from(true);
	
		foreach ($from_sql as $from) {
			// sto in una condizione
			if (stripos($from[2], 'meta_key') !== false && str_replace(["`", ' '], '', $sql_table) == str_replace(["`",' '], '',$from[0])) {
				$from2 = explode("meta_key", $from[2]) ;
				if (count($from2) == 2) {
					$from_selected = array_pop($from2);
					$from_selected_temp = explode(" AND ", str_ireplace(" OR ", " AND ", $from_selected));
					$selected[] = str_replace(["=","`",'"',"'", ' '], '', array_shift($from_selected_temp));
				}
			}
			
		}

		wp_send_json(['distinct' => $list, 'pri'=>$columns['pri'], 'parent_id'=>$columns['parent_id'], 'selected'=>$selected]);
		die;
	}

	/**
	 * Genero la query con l'aggiunta dei metadati
	 */
	function edit_sql_addmeta() {
		Dbt_fn::require_init();
		$choose_meta = @$_REQUEST['choose_meta'];
		$already_checked_meta = @$_REQUEST['altreadychecked_meta'];
		if (is_array($already_checked_meta)) {
			$already_checked_meta = array_filter($already_checked_meta);
		} else {
			$already_checked_meta = [];
		}
		$pri = $_REQUEST['pri_key'];
		$parent_id = $_REQUEST['parent_id'];
		$table2 =  $_REQUEST['dbt_meta_table'];
		$_sql_table_temp = explode("::", $table2);
		$_parent_table_temp = array_shift($_sql_table_temp); // la tabella.primary_id su cui sono collegati i meta 
		$_parent_table_temp =  explode(".", $_parent_table_temp);
		$parent_table_id = array_pop($_parent_table_temp); // l'id della tabella originale
		$parent_table = implode('.', $_parent_table_temp); // la tabella originale
		// manca il primary_id della tabella principale!
		$table = array_shift($_sql_table_temp); // la tabella dei meta
		$sql = $_REQUEST['sql'];
		$table_model = new Dbt_model();
		$table_model->prepare($sql);
		$from_sql = $table_model->get_partial_query_from();
		if ($sql != "" && $table_model->sql_type() == "select") {
			$temp_sql_from = [];
			$temp_sql_select = [];
			foreach ($choose_meta as $meta) {
				// verifico se non è stato già inserito il join
				$check_string = '.`meta_key` = \''.esc_sql($meta).'\'';
				if (in_array($meta, $already_checked_meta)) {
					$key = array_search($meta, $already_checked_meta);
					unset($already_checked_meta[$key]);
				} elseif (stripos($from_sql, $check_string) === false) {
					$alias = Dbt_fn::get_table_alias($table, $sql." ".implode(", ",$temp_sql_from), str_replace("_","",$meta));
					$temp_sql_from[] = ' LEFT JOIN `'.$table.'` `'.$alias.'` ON `'.$alias.'`.`'.$parent_id.'` = `'.$parent_table.'`.`'.$parent_table_id.'` AND `'.$alias.'`.`meta_key` = \''.esc_sql($meta).'\'';
					$temp_sql_select[] = '`'.$alias.'`.`meta_value` AS `'.Dbt_fn::get_column_alias($meta, $sql).'`';
				}
			}

			if (count($temp_sql_select) > 0) {
				$table_model->list_add_select(implode(", ", $temp_sql_select));
			}

			$table_model->list_add_from(implode(" ", $temp_sql_from));
			
			if (count($already_checked_meta) > 0) {
				$select_sql = $table_model->get_partial_query_select(true);
				$from_sql = $table_model->get_partial_query_from(true);
				//var_dump ($from_sql);
				$select_to_remove = [];
				$new_from = [];
				foreach ($from_sql as $from) {
					$add = true;
					foreach ($already_checked_meta as $meta_to_search) {
						if (stripos($from[2], $meta_to_search) !== false && str_replace(["`", ' '], '', $table) == str_replace(["`",' '], '',$from[0])) {
							//print "DEVO RIMUOVERE ".$meta_to_search;
							$select_to_remove[] =  str_replace(["`", ' '], '', $from[1]);
							$add = false;
						} 
					} 
					if ($add) {
						$new_from[] = $from[3];
					}
				}
				// Ricostruisco il select
				$new_select = [];
				foreach ($select_sql as $rebuild_select) {
					if (!in_array($rebuild_select[0],$select_to_remove)) {
						$new_select[] =  $rebuild_select[3];
					}
				}
				$table_model->list_change_select(implode(", ", $new_select));
				$table_model->list_change_from(implode(' ', $new_from));
			}

		}
		$table_model->remove_limit();
		$html = Dbt_html_sql::get_html_fields($table_model);
		wp_send_json(['sql' => $table_model->get_current_query(), 'html'=>$html]);
		die();
	}

	/**
	 * mostra come cambierebbero i dati dopo il replace 
	 */
	function sql_test_replace() {
		global $wpdb;
		Dbt_fn::require_init();
		$table_model = new Dbt_model();
		$sql = Dbt_fn::get_request('sql');
		$table_model->prepare($sql);    
		$search = stripslashes(Dbt_fn::get_request('search', false)); 
		$schemas = $table_model->get_schema();
		$filter =[] ; //[[op:'', column:'',value:'' ], ... ];
		foreach ($schemas as $schema) {
			if ($schema->orgtable != ""  && $schema->table != ""  && $schema->name != "") {
				$filter[] = ['op'=>'LIKE', 'column'=> '`'.esc_attr($schema->table).'`.`'.esc_attr($schema->orgname).'`', 'value' =>$search];
			}
		}
		if (count($filter) > 0) {
			$table_model->list_add_where($filter, 'OR');
		}
		$table_model->list_add_limit(0, 100);
		$items = $table_model->get_list();
		$replace = stripslashes(Dbt_fn::get_request('replace', false)); 
		if (count ($items) > 0) {
			ob_start();
			$first_row = array_shift($items);
			?>
			<h2><?php _e('Text the first 100 records', 'database_tables'); ?></h2>
			<table class="wp-list-table widefat striped dbt-table-view-list">
				<thead>
					<tr>
						<?php foreach ($first_row as $key=>$_) : ?>
							<th><?php echo $key; ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
				<?php 
				if (count($items) > 0) {
					foreach ($items as $item) {
						pinacode::set_var('data', $item);
						$temp_replace = PinaCode::execute_shortcode($replace);
						?><tr><?php
						// sostituisco i dati
						foreach ($item as $value) {
							if (stripos($value, $search) !== false) {
								if (is_serialized($value)) {
									$value_ser = maybe_unserialize($value);
									$value_ser2 = Dbt_fn::search_and_resplace_in_serialize($value_ser, $search, $temp_replace);
									$value_serialized = maybe_serialize($value_ser2);
									if (strlen($value) > 100) {
										$temp_pos = stripos($value, $search);
										if ($temp_pos > 20) {
											$value = "... ". substr($value,$temp_pos - 10);	
											$value_serialized = "... ". substr($value_serialized,$temp_pos - 10);
										}
										if (strlen($value) > 100) {
											$value =  substr($value,0,80)." ...";
											$value_serialized  =  substr($value_serialized,0,80)." ...";
										}
									}

									$value = "<b>Change serialized data (only values):</b><br >".str_ireplace(htmlentities($search),'<span style="text-decoration: line-through; color:red">??'.htmlentities($search)."</span>", $value)."<br>".$value_serialized;
								} else {
									$value = htmlentities($value);
									if (strlen($value) > 100) {
										$temp_pos = stripos($value, $search);
										if ($temp_pos > 20) {
											$value = "... ". substr($value,$temp_pos - 10);	
										}
										if (strlen($value) > 100) {
											$value =  substr($value,0,80)." ...";
										}
									}
									$value = str_ireplace(htmlentities($search),'<span style="text-decoration: line-through; color:red">'.htmlentities($search)."</span>", $value)."<br>".str_ireplace(htmlentities($search), '<b style="color:#259">'.htmlentities($temp_replace).'</b>', $value );
								}
							} else {
								$value = htmlentities($value);
								if (strlen($value) > 100) {
									$value =  substr($value,0,80)." ...";
								}
							}
							?><td><?php echo $value; ?></td><?php
						}
						?></tr><?php
					}
				}
				?>
				</tbody>
			</table>
			<?php
			$html = ob_get_clean();
		} else{
			$html = _e('Non ho trovato nulla da sostituire'); 
		}
		wp_send_json(['html'=>$html]);
		die();
	}


	/**
	 * mostra come cambierebbero i dati dopo il replace 
	 */
	function sql_search_replace() {
		global $wpdb;
		Dbt_fn::require_init();
		$table_model = new Dbt_model();
		$sql = Dbt_fn::get_request('sql');
		$table_model->prepare($sql);
		$table_model->add_primary_ids();
		$search = stripslashes(Dbt_fn::get_request('search', false)); 

		$limit_start = Dbt_fn::get_request('limit_start', 0);
		
		$total = Dbt_fn::get_request('total', 0);
		$replaced = Dbt_fn::get_request('row_replaced', 0);
		if ($total == 0) {
			$total = $table_model->get_count();
		}

		$table_model->list_add_limit($limit_start, 200);
		$items = $table_model->get_list();
		if (count ($items) > 1) {
			$replace = stripslashes(Dbt_fn::get_request('replace', false)); 
			$executed = $limit_start + count($items) - 1;
		
			$first_row = array_shift($items);
	
			foreach ($items as $item) {
				pinacode::set_var('data', $item);
				$temp_replace = PinaCode::execute_shortcode($replace);
				// sostituisco i dati
				$update = false;
				foreach ($item as &$value) {
					if (stripos($value, $search) !== false && $value != "") {
						$update =true;
						$replaced++;
						if (is_serialized($value)) {
							$value_ser = maybe_unserialize($value);
							
							$value_ser2 = Dbt_fn::search_and_resplace_in_serialize($value_ser, $search, $temp_replace);
							$value = maybe_serialize($value_ser2);
						} else {
							$value = str_ireplace($search, $temp_replace, $value );
						}
					}	
				}
				if ($update) {
					// aggiorno i dati!
					//print "\nSAVE\n";
				
					$ris = Dbt::save_data($sql, $item, false);
					//var_dump ($ris);
					//die;
				}
			}
		} else {
			$executed = $total;
		}
		wp_send_json(['total'=>$total, 'executed' => $executed, 'replaced' => $replaced ]);
		die();

		$replace = stripslashes(Dbt_fn::get_request('replace', false)); 

	}

	/**
	 * Testa una query. Verifica se è un select.
	 *
	 * @return void
	 */
	function check_query() {
		global $wpdb;
		Dbt_fn::require_init();
		$response = ['is_select' => 0, 'error' => ''];
		$sql = stripslashes($_REQUEST['sql']);
		$table_model = new Dbt_model();
		$table_model->prepare($sql);
		$type = "select";
		if ($sql != "" && $table_model->sql_type() == "select") {
			$ris = $wpdb->get_var("EXPLAIN ".$sql );
			if (!$ris && $wpdb->last_error != "" ) {
				$response['error'] =  $wpdb->last_error;
			} else {
				$response['is_select'] = 1;
			}
		}
		wp_send_json($response);
		die();
	}
	
	/**
     *  Imposta i cookie in una variabile statica e li rimuove dai cookie
     */
	function init_get_msg_cookie() {
        Dbt_fn::init_get_msg_cookie();
    }

	/**
	 * Raggruppo questo pezzettino di codice solo perché usato di continuo per preparare la query sull'edit, view, ecc..
	 */
	private function get_table_model_for_sidebar($dbt_id = 0) {
		if ($dbt_id > 0) {
			$post       = Dbt_functions_list::get_post_dbt($dbt_id);
			$table_model 				= new Dbt_model();
			$table_model->prepare($post->post_content['sql']);
		} else if (!isset($_REQUEST['ids']) || !isset($_REQUEST['sql'])) {
			return false;
		}
	
		if (isset($_REQUEST['sql'])) {
			$table_model 				= new Dbt_model();
			$table_model->prepare(html_entity_decode (Dbt_fn::get_request('sql', '')));
		}
		$filter = [];
		
		$json_send['edit_ids'] = Dbt_fn::get_request('ids', 0);
		foreach ($_REQUEST['ids'] as $column => $id) {
			$column = str_replace("`", "", $column );
			$column = "`".str_replace(".", "`.`", $column )."`";
			$filter[] = ['op' => "=", 'column' => $column, 'value' => $id];
		}
		$table_model->list_add_where($filter);
		return $table_model;
	}

	/**
	 * Mette tutte le tabelle di wordpress in stato pubblicato
	 */
	function publish_wp_tables($status, $table) {
		global $wpdb; 
		if (in_array($table, [$wpdb->posts, $wpdb->users, $wpdb->prefix.'usermeta', $wpdb->prefix.'terms' , $wpdb->prefix.'termmeta', $wpdb->prefix.'term_taxonomy', $wpdb->prefix.'term_relationships', $wpdb->prefix.'postmeta', $wpdb->prefix.'options', $wpdb->prefix.'links', $wpdb->prefix.'comments', $wpdb->prefix.'commentmeta'])) {
			$status = 'PUBLISH';
		}
		return $status;
	}

	/**
	 * Memorizzo le query di modifica che vengono eseguite dentro change
	 */
	function store_query($query) {
		if (!isset(self::$saved_queries->change) || !is_array(self::$saved_queries->change)) {
			self::$saved_queries->change = [];
		}
		switch (strtolower(substr(trim($query), 0, 6))) {

			case 'update':
				self::$saved_queries->change[] = $query;
				break;
			case 'insert':
				self::$saved_queries->change[] = $query;
				break;
			case 'delete':
				self::$saved_queries->change[] = $query;
				break;
		}
		return $query;
	}
}

$database_tables_loader = new Dbt_loader();