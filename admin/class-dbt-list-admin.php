<?php

/**
 * Il controller amministrativo specifico per le liste (page=dbt_list)
 * @internal
 */
namespace DatabaseTables;

class DBT_list_admin 
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
	 * Viene caricato alla visualizzazione della pagina
     */

	/**
	 * Viene caricato alla visualizzazione della pagina
     */
    function controller() {
		wp_enqueue_style( 'database-table-css' , plugin_dir_url( __FILE__ ) . 'css/database-table.css',[],rand());
		wp_enqueue_script( 'database-table-all-js', plugin_dir_url( __FILE__ ) . 'js/database-table-all.js',[],rand());

		// $dtf = new Dbt_fn();
		Dbt_fn::require_init();
		$temporaly_files = new Dbt_temporaly_files();
	    /**
		 * @var $section Definisce il tab che sta visualizzando
		 */
        $section =  Dbt_fn::get_request('section', 'home');
         /**
		 * @var $action Definisce l'azione
		 */
       	$action = Dbt_fn::get_request('action', '', 'string');
		//print $section." ".$action;	
		$msg =  $msg_error = '';
		if (isset($_COOKIE['dbt_msg'])) {
			$msg = $_COOKIE['dbt_msg'];
		}
		if (isset($_COOKIE['dbt_error'])) {
			$msg_error = $_COOKIE['dbt_error'];
		}	
		switch ($section) {
			case 'list-sql-edit' :
				$this->list_sql_edit();
				break;
			case 'list-browse' :
				$this->list_browse();
				break;
			case 'list-structure' :
				$this->list_structure();
				break;
			case 'list-setting' :
				$this->list_setting();
				break;
			case 'list-form' :
				$this->list_form();
				break;
			default :
				$this->list_all();
				break;
		}
		//print "OK DBT LIST ADMIN";
	}


    private function list_all() {
		global $wpdb;
		wp_register_script( 'dbt-new-list', plugin_dir_url( __FILE__ ) . 'js/dbt-new-list.js',false, rand());
		wp_add_inline_script( 'dbt-new-list', 'dbt_admin_post = "'.esc_url( admin_url("admin-post.php")).'";', 'before' );
		wp_enqueue_script( 'dbt-new-list' );
		
        // $dtf = new Dbt_fn();
        $section =  Dbt_fn::get_request('section', 'list-all');
		$action = Dbt_fn::get_request('action', '', 'string');
		$msg = $msg_error = "";
		
		if ($action == "publish-list" ) {
			$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
			if ($id > 0) {
				wp_publish_post($id);
				$msg = __('List published','database_tables');
			}
		}
		if ($action == "remove-list" ) {
			$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
			if ($id > 0) {
				wp_delete_post($id, true);
				$msg = __('List removed','database_tables');
			}
			$action = "show-trashed";
		}
		if ($action == "trash-list" ) {
			$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
			if ($id > 0) {
				wp_trash_post($id);
				$msg = __('List trashed','database_tables');
			}
		}
		if ($action == "show-trashed" ) {
			$args = array(
				'post_status' => 'trash',
				'numberposts' => -1,
				'post_type'   => 'dbt_list'
			);
		} else {
			$args = array(
				'post_status' => 'publish',
				'numberposts' => -1,
				'post_type'   => 'dbt_list'
			);
		}
		$post_count_sql = $wpdb->get_results("SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = 'dbt_list' GROUP BY post_status");
		$post_count = ['publish'=>0,'trash'=>0];
		if (count($post_count_sql) > 0) {
			foreach ($post_count_sql as $p) {
				$post_count[$p->post_status] = $p->num_posts;
			}
		}
		$list_page = get_posts( $args );
		foreach ($list_page as $key=>$post) {
			$post_content = Dbt_functions_list::convert_post_content_to_list_params($post->post_content);
			if (isset($post_content['sql_filter']) && is_countable($post_content['sql_filter'])) {
				$shortcode_param = [];
				foreach ($post_content['sql_filter'] as $filter) {
					if (isset($filter['value'])) {
						$shortcode_param = array_merge($shortcode_param, Dbt_functions_list::get_pinacode_params($filter['value']));
						
					}
				}
				if (count($shortcode_param) > 0) {
					$list_page[$key]->shortcode_param = " ".implode(", ", $shortcode_param)."";
				} else {
					$list_page[$key]->shortcode_param = "";
				}
			}
			$list_page[$key]->post_content = $post_content;
		}
        $render_content = "/dbt-content-list-all.php";
        require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
    }

	/**
	 * Modifica la query di una lista
	 */
	private function list_sql_edit() {
		global $wp_roles;
		wp_enqueue_script( 'database-sql-editor-js', plugin_dir_url( __FILE__ ) . 'js/database-sql-editor.js',[],rand());
		wp_enqueue_script( 'database-list-sql-js', plugin_dir_url( __FILE__ ) . 'js/database-list-sql.js',[],rand());
		wp_enqueue_script( 'jquery-ui-sortable');
        // $dtf = new Dbt_fn();
        $section =  Dbt_fn::get_request('section', 'list-all');
       	$action = Dbt_fn::get_request('action', '', 'string');
		$msg_error = "";
		$msg = "";
		$ris_list_saved = NULL;
		$show_query = false;
		if ($action == 'list-sql-save') {
			list($ris, $show_query) = $this->list_sql_save();
			if ($ris === true) {
				$ris_list_saved  = true;
				$msg = 'saved';
			} else {
				$ris_list_saved  = false;
				$msg_error = $ris;
			}
		}
		$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
		$render_content = "/dbt-content-list-sql-edit.php";
		$sql = "";
		$info_rows = [];
		$sql_order = ['field'=>'', 'sort'=>''];
		$sql_filter = [];
		$post_allow_delete = [];
		$sql_limit = 100;
		if ($id > 0) {
			$post = Dbt_functions_list::get_post_dbt($id);
			$list_title = $post->post_title;
			$post_excerpt = $post->post_excerpt;
			$sql = @$post->post_content['sql'];
			if (isset($post->post_content['sql_limit'])) {
				$sql_limit = (int)$post->post_content['sql_limit'];
			}
			if (isset($post->post_content['sql_order']) && is_array($post->post_content['sql_order'])) {
				$sql_order = $post->post_content['sql_order'];
			} 
			//var_dump ($post->post_content['sql_filter']);
			if (isset($post->post_content['sql_filter']) && is_array($post->post_content['sql_filter'])) {
				$sql_filter  = $post->post_content['sql_filter'];
			}
			
			if (isset($post->post_content['delete_params']) && is_a($post->post_content['delete_params'], 'DatabaseTables\DbtDs_list_delete_params')) {
				$post_allow_delete = $post->post_content['delete_params']->remove_tables_alias;
			}
		} else {
			$msg_error = __('You have not selected any list', 'database_tables');
		}
		if ($sql_limit < 1) {
			$sql_limit = 100;
		}
		$table_model = new Dbt_model();
		$table_model->prepare($sql);
		if ($sql != "") {
			if ($table_model->sql_type() != "select") {
				$show_query = true;
				$msg_error = __('Only a single select query is allowed in the lists', 'database_tables');
			} else {
				$table_model->list_add_limit(0, 1);
				$items = $table_model->get_list();
				if ($table_model->last_error != "") {
					$show_query = false;
					$msg_error = __('<h2>Query error:</h2>'.$table_model->last_error, 'database_tables');
				} else {
					if ($msg != "") {
						$msg .= "<br>";
					}
					
					$info_rows = $table_model->get_all_fields_from_query();
					$info_rows = array_merge([''=>'Select column'], $info_rows);
					//var_dump ($info_rows);
					$info_ops =  [ '=' => __('= (Equals)',  'database_tables'),
					'!=' => __('!= (Does Not Equal)',  'database_tables'),
					'>'  => __('> (Greater Than)',  'database_tables'),
					'>='  => __('>= (Greater or Equal To)',  'database_tables'),
					'<'  => __('< (Less Than)',  'database_tables'),
					'<='  => __('<= (Less or Equal To)',  'database_tables'),
					'LIKE'  => __('%LIKE% (Search Text)',  'database_tables'),
					'LIKE%'  => __('LIKE% (Text start with)',  'database_tables'),
					'NOT LIKE'  => __('NOT LIKE (Exclude Text)',  'database_tables'),
					'IN'  => __('IN (Match in array)',  'database_tables'),
					'NOT IN'  => __('NOT IN (Not found in array)',  'database_tables')
					];

					$pinacode_fields = ['data'=>[], 'params'=>['example'], 'request'=>['example'], 'total_row'];
					$items =  Dbt_functions_list::get_list_structure_config($table_model->items, $post->post_content['list_setting']);
					foreach ($items as $key=>$item) {
						$pinacode_fields['data'][] = $item->name;
					}
					Dbt_fn::echo_pinacode_variables_script($pinacode_fields);
				}
			}
		} else {
			$show_query = false;
			$msg_error = __('First you need to write a query to extract the list data. ', 'database_tables');
		}

		$dbt_admin_show = get_post_meta($id,'_dbt_admin_show', true);
		if(!is_countable($dbt_admin_show)) {
			$dbt_admin_show = [];
		}
		if (!isset($dbt_admin_show['menu_icon'])) {
			$dbt_admin_show['menu_icon'] = 'dashicons-database-view';
		}
		if (!isset($dbt_admin_show['menu_position'])) {
			$dbt_admin_show['menu_position'] = 101;
		}
        require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
    }

	/**
	 * Salva la query di una lista
	 */
	private function list_sql_save() {
		global $wp_roles;
		// $dtf = new Dbt_fn();
		$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
		$return = true;
		$show_query = false;
		if ($id > 0) {
			$post = Dbt_functions_list::get_post_dbt($id);
			if (isset($_REQUEST['custom_query']) && $_REQUEST['custom_query'] !== '') {
				// aggiungo tutti i primary id e li salvo a parte 
				$table_model = new Dbt_model();
           		$table_model->prepare($_REQUEST['custom_query']);
				
				if ($table_model->sql_type() != "select") {
					return [ __('Only a single select query is allowed in the lists', 'database_tables'), true];
					$show_query = true;
				} else {
					$table_model->add_primary_ids();
					// TODO se aggiungo qualche valore dovrei metterlo hidden in list view formatting!
					$table_model->list_add_limit(0, 1);
					$items = $table_model->get_list();
					if ($table_model->last_error == "") {
						$post->post_content['sql'] = html_entity_decode($table_model->get_current_query());
					} else {
						return [sprintf(__("I didn't save the query because it was wrong!.<br><h3>Error:</h3>%s<h3>Query:</h3>%s",'database_tables'), $table_model->last_error, $post->post_content['sql']), true];
					}
				}
			} else {
				return [__('The query is required', 'database_tables'), true];
			}

			$post->post_content['sql_limit'] = sanitize_text_field(stripslashes($_REQUEST['sql_limit']));
			if ($_REQUEST['sql_order']['field'] != "") {
				$post->post_content['sql_order'] = ['field'=>sanitize_text_field($_REQUEST['sql_order']['field']),'sort'=>sanitize_text_field($_REQUEST['sql_order']['sort'])] ;
			} else {
				if (isset($post->post_content['sql_order'])) {
					unset($post->post_content['sql_order']);
				}
			}
			$post->post_content['sql_filter'] = [];
		
			if (isset($_REQUEST['sql_filter_field']) && is_array($_REQUEST['sql_filter_field'])) {
				foreach ($_REQUEST['sql_filter_field'] as $key=>$field) {
					if ($field != "" && $_REQUEST['sql_filter_val'][$key] != "") {
						$post->post_content['sql_filter'][] = ['column' => sanitize_text_field($field), 'op' => sanitize_text_field($_REQUEST['sql_filter_op'][$key]), 'value' => stripslashes($_REQUEST['sql_filter_val'][$key]), 'required' => ($_REQUEST['sql_filter_required'][$key])];
					}
				}
			} 

			$post->post_content['delete_params'] = ['remove_tables_alias'=>[]];
			foreach ($_REQUEST['remove_tables_alias'] as $remove_tables_alias=>$allow) {
				$post->post_content['delete_params']['remove_tables_alias'][sanitize_text_field($remove_tables_alias)] = absint($allow);
			}


			// Verifico che nella query non vengano cambiati gli alias delle tabelle
			$from_query = $table_model->get_partial_query_from(true);
			$change_from_alias = false;
			$table_example1 = "wp_post";
			$table_example2 = "p";
			if (isset($post->post_content['sql_from'])) {
				foreach ($post->post_content['sql_from'] as $table_alias => $table) {
					$find = false;
					foreach ($from_query as $f) {
						// Ho invertito $f[1] con $f[0] così funziona, da verificare.
						if ($f[1] == $table_alias && $f[0] == $table) {
							$find = true;
							break;
						}
					}
					if (!$find) {
						$table_example1 = $table;
						$table_example2 = $table_alias;
						$change_from_alias = true;
						break;
					}
				}
			} 
			if ($change_from_alias)	{
				if ($table_example1 == $table_example2) {
					return [__('I\'m sorry, but In a list it is not possible to add an alias to a table. " FROM `'.$table_example1.'`" You cannot change in "FROM `'.$table_example1.'` AS `xxx`"', 'database_tables'), true];
				} else {
					return [__('Sorry but table aliases cannot be changed in a list. " FROM `'.$table_example1.'` AS `'.$table_example2.'`". You cannot change "`'.$table_example2.'`"', 'database_tables'), true];
				}
			}
			$from = [];
			foreach ($from_query as $f) {
				$from[$f[1]] = $f[0]; 
			}
			$post->post_content['sql_from'] = $from;

		
		
			$show_query = false;
			/**
			 * @var DbtDs_list_setting[] $setting_custom_list
			 */
			$setting_custom_list =  Dbt_functions_list::get_list_structure_config($items, $post->post_content['list_setting']);
			foreach ($setting_custom_list as $key_list=>$list) {
				$post->post_content['list_setting'][$key_list] = $list->get_for_saving_in_the_db();
			}
			$post_title = Dbt_fn::get_request('post_title', '');
			
			if ($post_title != "") {
				wp_update_post(array(
					'ID'           => $id,
					'post_title' 	=> sanitize_text_field($post_title),
					'post_excerpt' 	=> sanitize_textarea_field(Dbt_fn::get_request('post_excerpt')),
					'post_content' => addslashes(maybe_serialize($post->post_content)),
				));
			} else {
				$return = __('The title is required', 'database_tables');
			}
			

			// permessi e menu admin
			$post_title = Dbt_fn::get_request('post_title', '');
			$old = get_post_meta($id,'_dbt_admin_show', true);
			$title =  (@$post_title != "") ? $post_title : $_REQUEST['menu_title'];

			$dbt_admin_show  = ['page_title'=>sanitize_text_field($_REQUEST['menu_title']), 'menu_title'=>sanitize_text_field($_REQUEST['menu_title']), 'menu_icon'=>sanitize_text_field(trim($_REQUEST['menu_icon'])), 'menu_position'=>absint($_REQUEST['menu_position']), 'capability'=>'dbt_manage_'.$id, 'slug'=>'dbt_'.$id,'show'=>(isset($_REQUEST['show_admin_menu']) && $_REQUEST['show_admin_menu'] == 1) ? 1 : 0];
		
			
			if ($old != false) {
				update_post_meta($id, '_dbt_admin_show', $dbt_admin_show);
			} else {
				add_post_meta($id,'_dbt_admin_show', $dbt_admin_show, false);
			}
			if (isset($_REQUEST['show_admin_menu']) && $_REQUEST['show_admin_menu']) {
				foreach ($wp_roles->get_names() as $role_key => $_role_label) { 
					$role = get_role( $role_key );
					
					if (isset( $_REQUEST['add_role_cap']) && in_array ($role_key, $_REQUEST['add_role_cap'])) {
						$role->add_cap( 'dbt_manage_'.$id, true );
					} else {
						$role->remove_cap('dbt_manage_'.$id);
					}
				}
			} else {
				//TODO tolgo il post_meta?
			}
			
			
			
		} else {
			$return = __('You have not selected any list', 'database_tables');
		}
		return [$return, $show_query];
	}

	/**
	 * L'elenco dei dati estratti da una lista
	 */
	private function list_browse() {
		wp_add_inline_script( 'database-table-js', 'dbt_admin_post = "'.esc_url( admin_url("admin-post.php")).'";', 'before' );
		wp_enqueue_script( 'database-table-js', plugin_dir_url( __FILE__ ) . 'js/database-table.js',[],rand());

		wp_enqueue_script( 'database-form2-js', plugin_dir_url( __FILE__ ) . 'js/database-form2.js',[],rand());
		$file = plugin_dir_path( __FILE__  );
		$dbt_css_ver = date("ymdGi", filemtime( plugin_dir_path($file) . 'frontend/database-table.css' ));
		$dbt_js_ver = date("ymdGi", filemtime( plugin_dir_path($file) . 'frontend/database-table.js' ));
		//wp_register_style( 'dbt_frontend_css',  plugins_url( 'frontend/database-table.css',  $file), false,   $dbt_css_ver );
		//wp_enqueue_style( 'dbt_frontend_css' );
		// lo mette nel footer
		//wp_register_script( 'dbt_frontend_js',  plugins_url( 'frontend/database-table.js',  $file), false,   $dbt_js_ver, true );

		wp_add_inline_script( 'dbt_frontend_js', 'dbt_post = "'.esc_url( admin_url('admin-ajax.php')).'";', 'before' );
		wp_enqueue_script( 'dbt_frontend_js' );

		// $dtf = new Dbt_fn();
		$action = Dbt_fn::get_request('action_query', '', 'string');
		$msg_error = "";
		
		$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
		$render_content = "/dbt-content-list-browse.php";
		$html_content = "";
		if ($id > 0) {
			$post = Dbt_functions_list::get_post_dbt($id);
			if ($post == false) {
				?><script>window.location.href = '<?php echo admin_url("admin.php?page=dbt_list"); ?>';</script><?php
				die;
			}
			$list_title = $post->post_title;
			$description = $post->post_excerpt;
			$sql = @$post->post_content['sql'];
			if ($sql == "") {
				$link = admin_url("admin.php?page=dbt_list&section=list-sql-edit&dbt_id=".$id);
				$msg_error = '<a href="' . $link . '">'.__('You have to config the query first!', 'database_tables')."</a>";
			}
			// TODO se c'è un limit nella query dovrebbe settare la paginazione?!
			$table_model 				= new Dbt_model();

			$list_of_columns 				= Dbt_fn::get_all_columns();

			$table_model->prepare($sql);
			if ($table_model->sql_type() == "multiqueries") {
				//  NON GESTISCO MULTIQUERY NELLE LISTE
				$msg_error = __('No Multiquery permitted in list', 'database_tables');
			} else if ($table_model->sql_type() == "select") {
				// se sto renderizzando questa tabella una form è stata già aperta
				Dbt_fn::set_open_form(); 
				// cancello le righe selezionate!
				if ($action == "delete_rows" && isset($_REQUEST["remove_ids"]) && is_array($_REQUEST["remove_ids"])) {
					$result_delete = Dbt_fn::delete_rows($_REQUEST["remove_ids"], '', $id);
					if ($result_delete['error'] != "") {
						$msg_error = $result_delete;
					} else {
						$msg = sprintf(__('The data has been removed. <br> %s', 'database_tables'), $result_delete['sql']);
					}
				}

				if ($action == "delete_from_sql") {
					$result_delete = Dbt_fn::dbt_delete_from_sql(Dbt_fn::get_request('sql_query_executed'), Dbt_fn::get_request('remove_table_query'));
					if ($result_delete != "") {
						$msg_error = $result_delete;
					} else {
						$msg = __('The data has been removed', 'database_tables');
					}
				}

				if ( Dbt_fn::get_request('filter.limit', 0) == 0) {
					if (isset($post->post_content['sql_limit']) &&  (int)$post->post_content['sql_limit'] > 0) {
						$sql_limit  = (int)$post->post_content['sql_limit'];
					} else {
						$sql_limit  = 100;
					}
					$_REQUEST['filter']['limit'] = $sql_limit ;
					$table_model->list_add_limit(0, $sql_limit);
				}
				if ( Dbt_fn::get_request('filter.sort.field', '') == '') {
					if (isset($post->post_content['sql_order']['sort']) &&  isset($post->post_content['sql_order']['field'])) {
						$_REQUEST['sort']['field'] = $post->post_content['sql_order']['field'] ;
						$table_model->list_add_order($post->post_content['sql_order']['field'], $post->post_content['sql_order']['sort']);
					}
				}

				// SEARCH in all columns
				$search = stripslashes(Dbt_fn::get_request('search', false)); 
				if ($search && $search != "" &&  in_array($action, ['search','order','limit_start','change_limit'])) {
					// TODO se è search deve rimuovere prima tutti i where!!!!
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
				} else {
					$_REQUEST['search'] = '';
				}
				Dbt_fn::add_request_filter_to_model($table_model, $this->max_show_items);
				$table_items = $table_model->get_list();
				$table_model->update_items_with_setting($post);
				Dbt_fn::items_add_action($table_model, $post->post_content);
				$table_model->check_for_filter();
				Dbt_fn::remove_hide_columns($table_model);
				$html_table   = new Dbt_html_table();
				//var_dump($table_model->items);
				$html_content = $html_table->template_render($table_model); // lo uso nel template
				//print (get_class($table_model) );	
				Dbt_fn::set_close_form(); 
			} else {
				$msg_error = __('You need to create a select query for the lists', 'database_tables');
			}
		}  else {
			$msg_error = __('You have not selected any list', 'database_tables');
		}
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
	}

	/**
	 * La struttura prevede di gestire quali campi visualizzare e come
	 */
	private function list_structure() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'database-list-structure-js', plugin_dir_url( __FILE__ ) . 'js/database-list-structure.js',[],rand());
		wp_enqueue_script( 'database-sql-editor-js', plugin_dir_url( __FILE__ ) . 'js/database-sql-editor.js',[],rand());

		// $dtf = new Dbt_fn();
		$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
		$action = Dbt_fn::get_request('action', '', 'string');
		$msg = "";
		$msg_error = "";
		if ($action == 'list-structure-save') {
			$this->list_structure_save();
			$msg = __("Saved", 'database_tables');
		}
		$table_model = new Dbt_model();
		/**
		 * @var DbtDs_list_setting[] $items
		 */
		$items = []; 
		if ($id > 0) {
			$post = Dbt_functions_list::get_post_dbt($id);
			$total_row = Dbt::get_total($id);
			$select_array_test = [];
			for($xtr = 1; ($xtr <= $total_row && $xtr < 100); $xtr++) {
				$select_array_test[$xtr] =  $xtr;
			}

			$list_title = $post->post_title;
			if (array_key_exists('sql', $post->post_content)) {
				$sql = $post->post_content['sql'];
				$table_model->prepare($sql);
				$list = $table_model->get_list();
			
				$items = Dbt_functions_list::get_list_structure_config($table_model->items, $post->post_content['list_setting']);
			} else {
				$link = admin_url("admin.php?page=dbt_list&section=list-sql-edit&dbt_id=".$id);
				$msg_error = '<a href="' . $link . '">'.__('You have to config the query first!', 'database_tables')."</a>";
			}
			$pinacode_fields = [];
			if (is_countable($items)) {
				foreach ($items as $key=>$item) {
					$pinacode_fields[] = $item->name;
				}
				Dbt_fn::echo_pinacode_variables_script(['data'=>$pinacode_fields]);
			}
			//
			$render_content = "/dbt-content-list-structure.php";
		} else {
			$msg_error = __('You have not selected any list', 'database_tables');
		}
		if ($items === false) {
			$msg_error =  __('Something is wrong, check the query', 'database_tables');
		}
		
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
	}

	/**
	 * Salva la struttura di una lista
	 * @return String error message
	 */
	private function list_structure_save() {
		// $dtf = new Dbt_fn();
		$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
		if ($id > 0) {
			$post = Dbt_functions_list::get_post_dbt($id);
			$model = new Dbt_model();
			$model->prepare($post->post_content['sql']);
			$model->list_add_limit(0,1);
			$model_items = $model->get_list();
			unset($post->post_content['list_setting']);
			$count = 0;
			$list_setting = [];
			foreach ($_REQUEST['fields_toggle'] as $key=>$ft) {
				if ($key === 0 ) continue;
				$count++;
				$column_key = $key;
				if ($_REQUEST['fields_origin'][$key] == 'CUSTOM' && is_numeric($key)) {
					$title = sanitize_text_field(@$_REQUEST['fields_title'][$key]);
					$column_key = Dbt_fn::clean_string($title);
				} 
				$list_setting[$column_key] = (new DbtDs_list_setting())->set_from_array(
					['toggle'=>$ft,
					'title'=>sanitize_text_field(@$_REQUEST['fields_title'][$key]), 
					'view'=>sanitize_text_field(@$_REQUEST['fields_custom_view'][$key]),
					'custom_code'=> stripslashes(@$_REQUEST['fields_custom_code'][$key]),
					'order'=>sanitize_text_field(@$_REQUEST['fields_order'][$key]),
					'type' => sanitize_text_field(@$_REQUEST['fields_origin'][$key]),
					'width' => sanitize_text_field(@$_REQUEST['fields_width'][$key]),
					'align' => sanitize_text_field(@$_REQUEST['fields_align'][$key]),
					'mysql_name' => sanitize_text_field(@$_REQUEST['fields_mysql_name'][$key]),
					'mysql_table' => sanitize_text_field(@$_REQUEST['fields_mysql_table'][$key]),
					'name_request' => sanitize_text_field(@$_REQUEST['fields_name_request'][$key]),
					'searchable' => sanitize_text_field(@$_REQUEST['fields_searchable'][$key]),
					'custom_param' => stripslashes(@$_REQUEST['fields_custom_param'][$key]),
					'format_values' => stripslashes(@$_REQUEST['fields_format_values'][$key]),
					'format_styles' => stripslashes(@$_REQUEST['fields_format_styles'][$key]),
					'lookup_id' => stripslashes(@$_REQUEST['fields_lookup_id'][$key]),
					'lookup_sel_val' => stripslashes(@$_REQUEST['fields_lookup_sel_val'][$key]),
					'lookup_sel_txt' => stripslashes(@$_REQUEST['fields_lookup_sel_txt'][$key])
					]
				);
			}
			// aggiungo i metadati di schema estratti dalla query
			$list_setting = Dbt_functions_list::get_list_structure_config($model_items, $list_setting);
		
			foreach ($list_setting as $key=>$single) {
				$post->post_content['list_setting'][$key] = $single->get_for_saving_in_the_db();
			}
			if (isset($_REQUEST['custom_query']) && $_REQUEST['custom_query'] !== '') {
				// aggiungo tutti i primary id e li salvo a parte 
				$table_model = new Dbt_model();
           		$table_model->prepare($_REQUEST['custom_query']);
				
				if ($table_model->sql_type() != "select") {
					return [ __('Only a single select query is allowed in the lists', 'database_tables'), true];
				} else {
					$table_model->get_list();
					if ($table_model->last_error == "") {
						$post->post_content['sql'] = html_entity_decode($table_model->get_current_query());
					} else {
						return [sprintf(__("I didn't save the query because it was wrong!.<br><h3>Error:</h3>%s<h3>Query:</h3>%s",'database_tables'), $table_model->last_error, $post->post_content['sql']), true];
					}
				}
			}
			foreach ($_REQUEST['list_general_setting'] as $lgs_key => $list_general_setting) {
				$post->post_content['list_general_setting'][$lgs_key] = sanitize_text_field($list_general_setting);
			}

			wp_update_post(array(
				'ID'           => $id,
				'post_content' => addslashes(maybe_serialize($post->post_content)),
			));
		}
		return '';
	}
	/**
	 * I setting di una lista definiscono i parametri quali titolo, descrizione stato ecc.. 
	 */
	private function list_setting() {
		$file = plugin_dir_path( __FILE__  );
		wp_register_style( 'dbt_frontend_css',  plugins_url( 'frontend/database-table.css',  $file), false, rand());
		wp_enqueue_style( 'dbt_frontend_css' );
		wp_enqueue_script( 'database-list-setting-js', plugin_dir_url( __FILE__ ) . 'js/database-list-setting.js',[],rand());
		
		$pages  = get_pages(['sort_column' => 'post_title']); 
		
		// $dtf = new Dbt_fn();
		$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
		$action = Dbt_fn::get_request('action', '', 'string');
		$render_content = "/dbt-content-list-setting.php";
		$msg = $msg_error = "";
		if ($id > 0) {
			
			if ($action == 'list-setting-save') {
				if ($this->list_setting_save($id)) {
					$msg = __("Saved", 'database_tables');
				} else {
					$msg_error = __("There was a problem saving the data", 'database_tables');
				}
			}
			$post = Dbt_functions_list::get_post_dbt($id);
			//var_dump ($post->post_content['frontend_view']);
			$few = $post->post_content['frontend_view'];
			$errors_if_textarea  = "";
			if (@$few['checkif'] == 1) {
				$few['if_textarea'];
				$ris = PinaCode::math_and_logic($few['if_textarea']);
				$pc_errors = PcErrors::get('error');
				if (count($pc_errors) == 0) {
					if ( (is_numeric($ris) && $ris != 0 && $ris != 1) || (!is_numeric($ris)  && !is_bool($ris) &&  (is_string($ris) && !in_array(strtolower($ris), ["true",'t','false','f'])))) {
						$errors_if_textarea = __('The expression must return boolean, or a number or one of the following texts: true, t, false, f', 'database_tables');
					}
				} else {
					$errors_if_textarea = array_shift($pc_errors);
					if (is_array($errors_if_textarea)) {
						$errors_if_textarea = array_shift($errors_if_textarea);
					}
				}
			}
			$list_title = $post->post_title;
			$post_excerpt = $post->post_excerpt;



			$sql = @$post->post_content['sql'];
			
			if ($sql != "") {
				$table_model = new Dbt_model();
				$table_model->prepare($sql);
				if ($table_model->sql_type() != "select") {
					$msg_error = __('Only a single select query is allowed in the lists', 'database_tables');
				} else {
					$table_model->list_add_limit(0, 1);
					$items = $table_model->get_list();
					if ($table_model->last_error != "") {
						$msg_error = __('<h2>Query error:</h2>'.$table_model->last_error, 'database_tables');
					} else {
						$pinacode_fields = ['data'=>[], 'params'=>['example'], 'request'=>['example'], 'total_row'];
						$items =  Dbt_functions_list::get_list_structure_config($table_model->items, $post->post_content['list_setting']);
						foreach ($items as $key=>$item) {
							$pinacode_fields['data'][] = $item->name;
						}
						Dbt_fn::echo_pinacode_variables_script($pinacode_fields);
	
					}
				}
			}

		}
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
	}

	/**
	 * Salvo i setting
	 */
	private function list_setting_save($id) {
		// $dtf = new Dbt_fn();
		$frontend_view = Dbt_fn::get_request('frontend_view', []);
		$frontend_view['content'] = stripslashes($frontend_view['content']);
		$frontend_view['no_result_custom_text'] = stripslashes($frontend_view['no_result_custom_text']);
		$frontend_view['detail_template'] = stripslashes($frontend_view['detail_template']);
		$frontend_view['content_header'] = stripslashes($frontend_view['content_header']);
		$frontend_view['content_footer'] = stripslashes($frontend_view['content_footer']);
		$frontend_view['detail_type'] = stripslashes($frontend_view['detail_type']);
		if (@$frontend_view['checkif'] == 1 && $frontend_view['if_textarea'] != "") {
			$frontend_view['content_else'] = stripslashes($frontend_view['content_else']);
			$frontend_view['if_textarea'] = stripslashes($frontend_view['if_textarea']);
		} else {
			$frontend_view['checkif'] = 0;
			$frontend_view['content_else'] = '';
		}
		if ($frontend_view['type'] == "EDITOR") {
			$frontend_view['table_update'] = Dbt_fn::get_request('editor_table_update');
			$frontend_view['table_pagination_style'] = Dbt_fn::get_request('editor_table_pagination_style');
		} 
		$post = Dbt_functions_list::get_post_dbt($id);
		$post->post_content['frontend_view'] = $frontend_view;

		
		wp_update_post(array(
			'ID'           	=> $id,
			'post_content'  => addslashes(maybe_serialize($post->post_content))
		));
		return true;
	
	}

	/**
	 * Gestisco la form 
	 */
	private function list_form() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'database-list-form-js', plugin_dir_url( __FILE__ ) . 'js/database-list-form.js',[], rand());
		wp_enqueue_script( 'database-form2-js', plugin_dir_url( __FILE__ ) . 'js/database-form2.js',[], rand());
		// $dtf = new Dbt_fn();
		$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
		$action = Dbt_fn::get_request('action', '', 'string');
		$msg = "";
		$msg_error = "";
		if ($action == 'list-form-save') {
			$this->list_form_save();
			$msg = __("Saved", 'database_tables');
		}
		$tables = []; 
		if ($id > 0) {
			
			$post = Dbt_functions_list::get_post_dbt($id);
			$total_row = Dbt::get_total($id);
			$select_array_test = [];
			for($xtr = 1; ($xtr <= $total_row && $xtr < 100); $xtr++) {
				$select_array_test[$xtr] =  $xtr;
			}

			$list_title = "FORM ".$post->post_title;
			if (array_key_exists('sql', $post->post_content)) {
			
				
				$form = new Dbt_class_form($id);
				list($settings, $table_options) = $form->get_form(false);
				$table_options = array_shift($table_options);
				$tables = [];
				foreach ($settings as $k=>$sett) {
					$temp_tables = ['table_name'=>$table_options[$k]->orgtable, 'fields'=>[], 'table_options' => $table_options[$k]];
					foreach ($sett as $field) {
						$temp_tables['fields'][] = $field;
					}
					$tables[$table_options[$k]->table] = $temp_tables;
				}
				
			} else {
				$link = admin_url("admin.php?page=dbt_list&section=list-sql-edit&dbt_id=".$id);
				$msg_error = '<a href="' . $link . '">'.__('You have to config the query first!', 'database_tables')."</a>";
			}
			

			$post_types = get_post_types();
		
			// i campi template engine da inserire nella documentazione
			$pinacode_fields = [];
			
			foreach ($tables as $key=>$table) {
				foreach ($table['fields'] as $item) {
					if ($item->js_rif != "") {
						$pinacode_fields[] =  $item->js_rif;
					}
				}
			}

			Dbt_fn::echo_pinacode_variables_script($pinacode_fields);
			
			//
			$render_content = "/dbt-content-list-form.php";
		} else {
			$msg_error = __('You have not selected any list', 'database_tables');
		}
		if ($tables === false) {
			$msg_error =  __('Something is wrong, check the query', 'database_tables');
		}
		require(dirname( __FILE__ ) . "/partials/dbt-page-base.php");
	}

	/**
	 * Salva la form
	 *
	 * @return void
	 */
	private function list_form_save() {
		// $dtf = new Dbt_fn();
		$id = Dbt_fn::get_request('dbt_id', 0, 'absint');
		if ($id > 0) {
			$post = Dbt_functions_list::get_post_dbt($id);
			unset($post->post_content['form']);
			$count = 0;

			// i campi
			foreach ($_REQUEST['fields_name'] as $key=>$ft) {
				if ($key === 0 ) continue;
				$count++;

				if (isset($_REQUEST['fields_delete_column'][$key]) && $_REQUEST['fields_delete_column'][$key] == 1) {
					// elimino il campo
					$model_structure = new Dbt_model_structure($_REQUEST['fields_orgtable'][$key]);
					$model_structure->delete_column($ft);
	
				} else if (isset($_REQUEST['fields_edit_new'][$key])) {
					if ($_REQUEST['fields_edit_new'][$key] == "") {
						$_REQUEST['fields_edit_new'][$key] = 'fl_'.$key;
					}
					// creo il campo
					$model_structure = new Dbt_model_structure($_REQUEST['fields_orgtable'][$key]);

					$array_convert_type_to_field = [
						'VARCHAR'=>['VARCHAR',255],
						'TEXT'=>['TEXT',''],
						'DATE'=>['DATE',''],
						'DATETIME'=>['DATETIME',''],
						'NUMERIC'=>['INT',''],
						'DECIMAL'=>['DECIMAL','9,2'],
						'SELECT'=>['VARCHAR',255],
						'RADIO'=>['VARCHAR',255],
						'CHECKBOX'=>['VARCHAR',255],
						'CHECKBOXES'=>['TINYTEXT',''],
						'READ_ONLY'=>['VARCHAR',255],
						'EDITOR_CODE'=>['TEXT',''],
						'EDITOR_TINYMCE'=>['TEXT',''],
						'CREATION_DATE'=>['DATE',''],
						'LAST_UPDATE_DATE'=>['DATE',''],
						'RECORD_OWNER'=>['BIGINT',''],
						'UPLOAD_FIELD'=>['VARCHAR',255],
						'POST'=>['BIGINT',''],
						'USER'=>['BIGINT',''],
						'MEDIA_GALLERY'=>['BIGINT','']
					];
					
					$config_new_column = $array_convert_type_to_field[$_REQUEST['fields_form_type'][$key]];
					// fields_name
					$ft = $model_structure->insert_new_column($_REQUEST['fields_edit_new'][$key], $config_new_column[0], $config_new_column[1]);

					if ($ft == false) continue;
				}

				$custom_value = '';
				if (@$_REQUEST['fields_form_type'][$key] == 'CHECKBOX') {
					$custom_value =  stripslashes(@$_REQUEST['fields_custom_value_checkbox'][$key]);
				} else if (@$_REQUEST['fields_form_type'][$key] == 'CALCULATED_FIELD') {
					$custom_value =  stripslashes(Dbt_fn::get_request('fields_custom_value_calc.'.$key));
				}
				$array_form =  [ 'name'=>sanitize_text_field($ft), 'order'=>sanitize_text_field(@$_REQUEST['fields_order'][$key]), 'table' => sanitize_text_field(@$_REQUEST['fields_table'][$key]), 'orgtable' => sanitize_text_field(@$_REQUEST['fields_orgtable'][$key]), 'edit_view' => sanitize_text_field(@$_REQUEST['fields_edit_view'][$key]), 'label'=> sanitize_text_field( stripslashes(@$_REQUEST['fields_label'][$key])), 'form_type'=> sanitize_text_field(@$_REQUEST['fields_form_type'][$key]), 'note'=> stripslashes(Dbt_fn::get_request('fields_note.'.$key)), 'options'=> stripslashes(Dbt_fn::get_request('fields_options.'.$key)), 'required'=> sanitize_text_field(@$_REQUEST['fields_required'][$key]), 'custom_css_class'=> stripslashes(Dbt_fn::get_request('fields_custom_css_class.'.$key)), 'default_value'=> stripslashes(Dbt_fn::get_request('fields_default_value.'.$key)), 'js_script'=> stripslashes(Dbt_fn::get_request('fields_js_script.'.$key)), 'custom_value'=> $custom_value];


				//var_dump ($array_form);

				if (@$_REQUEST['fields_form_type'][$key] == 'CALCULATED_FIELD' && isset($_REQUEST['where_precompiled'][$key]) && $_REQUEST['where_precompiled'][$key] == 1) {
					$array_form['where_precompiled'] =  1;
				}
				if (@$_REQUEST['fields_form_type'][$key] == 'POST') {
					$array_form['post_types'] = sanitize_text_field(@$_REQUEST['fields_post_types'][$key]);
					if (isset($_REQUEST['fields_post_cats'][$key]) && is_countable($_REQUEST['fields_post_cats'][$key])) {
						$array_form['post_cats'] = @$_REQUEST['fields_post_cats'][$key];
					}
				}
				if (@$_REQUEST['fields_form_type'][$key] == 'USER') {
					if (isset($_REQUEST['fields_user_roles'][$key]) && is_countable($_REQUEST['fields_user_roles'][$key])) {
						$array_form['user_roles'] = @$_REQUEST['fields_user_roles'][$key];
					}
				}
				if (@$_REQUEST['fields_form_type'][$key] == 'LOOKUP') {
					if (isset($_REQUEST['fields_lookup_id'][$key])) {
						$array_form['lookup_id'] = @$_REQUEST['fields_lookup_id'][$key];
						$array_form['lookup_sel_val'] = Dbt_fn::sanitaze_request('fields_lookup_sel_val.'.$key);
						$array_form['lookup_sel_txt'] = Dbt_fn::sanitaze_request('fields_lookup_sel_txt.'.$key);
					}
				}
				$post->post_content['form'][] = $array_form;
			}

			// la configurazione delle tabelle
			$post->post_content['form_table'] = [];
			if (isset($_REQUEST['fields_table'])) {
				foreach ($_REQUEST['fields_table'] as $field_table) {
					$post->post_content[
						'form_table'][$field_table] = ['allow_create' => $_REQUEST['table_allow_create'][$field_table],
						
						'show_title' => sanitize_text_field($_REQUEST['table_show_title'][$field_table]),
						'frame_style' => sanitize_text_field($_REQUEST['table_frame_style'][$field_table]),
						'title' => stripslashes($_REQUEST['table_title'][$field_table]),
						'description' => stripslashes($_REQUEST['table_description'][$field_table]), 'module_type' => sanitize_text_field($_REQUEST['table_module_type'][$field_table])
					];
				}
			}
			
			if (isset($_REQUEST['link_table']) && isset($_REQUEST['link_list_column']) && isset($_REQUEST['link_table_column'])) {
				
				$alias = Dbt_fn::get_table_alias($_REQUEST['link_table'], $post->post_content['sql'], $_REQUEST['link_table_column']);
				$new_list = ['table'=>sanitize_text_field($_REQUEST['link_table']), 'list'=>sanitize_text_field($_REQUEST['link_list_column']), 'column'=>sanitize_text_field($_REQUEST['link_table_column']), 'alias'=>$alias];
				
				if (!isset($post->post_content['link_form_table'])) {
					$post->post_content['link_form_table'] = [];
				}
				$post->post_content['link_form_table'][] = $new_list;
				
				
			}

			wp_update_post(array(
				'ID'           => $id,
				'post_content' => addslashes(maybe_serialize($post->post_content)),
			));
		}
	}
}