<?php

/**
 * Il controller amministrativo specifico per le liste (page=dbp_list)
 * @internal
 */
namespace DatabasePress;

class  Dbp_list_admin 
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
		wp_enqueue_style( 'database-press-css' , plugin_dir_url( __FILE__ ) . 'css/database-press.css',[],rand());
		wp_enqueue_script( 'database-press-all-js', plugin_dir_url( __FILE__ ) . 'js/database-press-all.js',[],rand());

		// $dbp = new Dbp_fn();
		dbp_fn::require_init();
		$temporaly_files = new Dbp_temporaly_files();
	    /**
		 * @var $section Definisce il tab che sta visualizzando
		 */
        $section =  dbp_fn::get_request('section', 'home');
         /**
		 * @var $action Definisce l'azione
		 */
       	$action = dbp_fn::get_request('action', '', 'string');
		//print $section." ".$action;	
		$msg =  $msg_error = '';
		if (isset($_COOKIE['dbp_msg'])) {
			$msg = $_COOKIE['dbp_msg'];
		}
		if (isset($_COOKIE['dbp_error'])) {
			$msg_error = $_COOKIE['dbp_error'];
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
		//print "OK dbp LIST ADMIN";
	}


    private function list_all() {
		global $wpdb;
		wp_register_script( 'dbp-new-list', plugin_dir_url( __FILE__ ) . 'js/dbp-new-list.js',false, rand());
		wp_add_inline_script( 'dbp-new-list', 'dbp_admin_post = "'.esc_url( admin_url("admin-post.php")).'";', 'before' );
		wp_enqueue_script( 'dbp-new-list' );
		
        // $dbp = new Dbp_fn();
        $section =  dbp_fn::get_request('section', 'list-all');
		$action = dbp_fn::get_request('action', '', 'string');
		$msg = $msg_error = "";
		
		if ($action == "publish-list" ) {
			$id = dbp_fn::get_request('dbp_id', 0, 'absint');
			if ($id > 0) {
				wp_publish_post($id);
				$msg = __('List published','database_press');
			}
		}
		if ($action == "remove-list" ) {
			$id = dbp_fn::get_request('dbp_id', 0, 'absint');
			if ($id > 0) {
				wp_delete_post($id, true);
				$msg = __('List removed','database_press');
			}
			$action = "show-trashed";
		}
		if ($action == "trash-list" ) {
			$id = dbp_fn::get_request('dbp_id', 0, 'absint');
			if ($id > 0) {
				wp_trash_post($id);
				$msg = __('List trashed','database_press');
			}
		}
		if ($action == "show-trashed" ) {
			$args = array(
				'post_status' => 'trash',
				'numberposts' => -1,
				'post_type'   => 'dbp_list'
			);
		} else {
			$args = array(
				'post_status' => 'publish',
				'numberposts' => -1,
				'post_type'   => 'dbp_list'
			);
		}
		$post_count_sql = $wpdb->get_results("SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = 'dbp_list' GROUP BY post_status");
		$post_count = ['publish'=>0,'trash'=>0];
		if (count($post_count_sql) > 0) {
			foreach ($post_count_sql as $p) {
				$post_count[$p->post_status] = $p->num_posts;
			}
		}
		$list_page = get_posts( $args );
		foreach ($list_page as $key=>$post) {
			$post_content = dbp_functions_list::convert_post_content_to_list_params($post->post_content);
			if (isset($post_content['sql_filter']) && is_countable($post_content['sql_filter'])) {
				$shortcode_param = [];
				foreach ($post_content['sql_filter'] as $filter) {
					if (isset($filter['value'])) {
						$shortcode_param = array_merge($shortcode_param, dbp_functions_list::get_pinacode_params($filter['value']));
						
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
        $render_content = "/dbp-content-list-all.php";
        require(dirname( __FILE__ ) . "/partials/dbp-page-base.php");
    }

	/**
	 * Modifica la query di una lista
	 */
	private function list_sql_edit() {
		global $wp_roles;
		wp_enqueue_script( 'database-sql-editor-js', plugin_dir_url( __FILE__ ) . 'js/database-sql-editor.js',[],rand());
		wp_enqueue_script( 'database-list-sql-js', plugin_dir_url( __FILE__ ) . 'js/database-list-sql.js',[],rand());
		wp_enqueue_script( 'jquery-ui-sortable');
        // $dbp = new Dbp_fn();
        $section =  dbp_fn::get_request('section', 'list-all');
       	$action = dbp_fn::get_request('action', '', 'string');
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
		$id = dbp_fn::get_request('dbp_id', 0, 'absint');
		$render_content = "/dbp-content-list-sql-edit.php";
		$sql = "";
		$info_rows = [];
		$sql_order = ['field'=>'', 'sort'=>''];
		$sql_filter = [];
		$post_allow_delete = [];
		$sql_limit = 100;
		if ($id > 0) {
			$post = dbp_functions_list::get_post_dbp($id);
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
			
			if (isset($post->post_content['delete_params']) && is_a($post->post_content['delete_params'], 'DatabasePress\dbpDs_list_delete_params')) {
				$post_allow_delete = $post->post_content['delete_params']->remove_tables_alias;
			}
		} else {
			$msg_error = __('You have not selected any list', 'database_press');
		}
		if ($sql_limit < 1) {
			$sql_limit = 100;
		}
		$table_model = new Dbp_model();
		$table_model->prepare($sql);
		if ($sql != "") {
			if ($table_model->sql_type() != "select") {
				$show_query = true;
				$msg_error = __('Only a single select query is allowed in the lists', 'database_press');
			} else {
				$table_model->list_add_limit(0, 1);
				$items = $table_model->get_list();
				if ($table_model->last_error != "") {
					$show_query = false;
					$msg_error = __('<h2>Query error:</h2>'.$table_model->last_error, 'database_press');
				} else {
					if ($msg != "") {
						$msg .= "<br>";
					}
					
					$info_rows = $table_model->get_all_fields_from_query();
					$info_rows = array_merge([''=>'Select column'], $info_rows);
					//var_dump ($info_rows);
					$info_ops =  [ '=' => __('= (Equals)',  'database_press'),
					'!=' => __('!= (Does Not Equal)',  'database_press'),
					'>'  => __('> (Greater Than)',  'database_press'),
					'>='  => __('>= (Greater or Equal To)',  'database_press'),
					'<'  => __('< (Less Than)',  'database_press'),
					'<='  => __('<= (Less or Equal To)',  'database_press'),
					'LIKE'  => __('%LIKE% (Search Text)',  'database_press'),
					'LIKE%'  => __('LIKE% (Text start with)',  'database_press'),
					'NOT LIKE'  => __('NOT LIKE (Exclude Text)',  'database_press'),
					'IN'  => __('IN (Match in array)',  'database_press'),
					'NOT IN'  => __('NOT IN (Not found in array)',  'database_press')
					];

					$pinacode_fields = ['data'=>[], 'params'=>['example'], 'request'=>['example'], 'total_row'];
					$items =  dbp_functions_list::get_list_structure_config($table_model->items, $post->post_content['list_setting']);
					foreach ($items as $key=>$item) {
						$pinacode_fields['data'][] = $item->name;
					}
					dbp_fn::echo_pinacode_variables_script($pinacode_fields);
				}
			}
		} else {
			$show_query = false;
			$msg_error = __('First you need to write a query to extract the list data. ', 'database_press');
		}

		$dbp_admin_show = get_post_meta($id,'_dbp_admin_show', true);
		if(!is_countable($dbp_admin_show)) {
			$dbp_admin_show = [];
		}
		if (!isset($dbp_admin_show['menu_icon'])) {
			$dbp_admin_show['menu_icon'] = 'dashicons-database-view';
		}
		if (!isset($dbp_admin_show['menu_position'])) {
			$dbp_admin_show['menu_position'] = 101;
		}
        require(dirname( __FILE__ ) . "/partials/dbp-page-base.php");
    }

	/**
	 * Salva la query di una lista
	 * @return array ([string|true], bool)
	 */
	private function list_sql_save() {
		global $wp_roles;
		// $dbp = new Dbp_fn();
		$id = dbp_fn::get_request('dbp_id', 0, 'absint');
		$return = [];
		$show_query = false;
		$error_query = "";
		if ($id > 0) {
			$post = dbp_functions_list::get_post_dbp($id);
			if (isset($_REQUEST['custom_query']) && $_REQUEST['custom_query'] !== '') {
				// aggiungo tutti i primary id e li salvo a parte 
				$table_model = new Dbp_model();
           		$table_model->prepare($_REQUEST['custom_query']);
				
				if ($table_model->sql_type() != "select") {
					$error_query = __('Only a single select query is allowed in the lists', 'database_press');
					$show_query = true;
				} else {
					$table_model->add_primary_ids();
					// TODO se aggiungo qualche valore dovrei metterlo hidden in list view formatting!
					$table_model->list_add_limit(0, 1);
					$items = $table_model->get_list();
					if ($table_model->last_error == "") {
						$post->post_content['sql'] = html_entity_decode($table_model->get_current_query());
					} else {
						$error_query = sprintf(__("I didn't save the query because it was wrong!.<br><h3>Error:</h3>%s<h3>Query:</h3>%s",'database_press'), $table_model->last_error, stripslashes(nl2br($_REQUEST['custom_query'])));
						
					}
				}
			} else {
				$error_query = __('The query is required', 'database_press');
			}
			// TODO se metto il limit nella query vorrei che passasse qui!
			$post->post_content['sql_limit'] = sanitize_text_field(stripslashes($_REQUEST['sql_limit']));
			if ($_REQUEST['sql_order']['field'] != "") {
				$post->post_content['sql_order'] = ['field'=>sanitize_text_field($_REQUEST['sql_order']['field']),'sort'=>sanitize_text_field($_REQUEST['sql_order']['sort'])] ;
			} else {
				if (isset($post->post_content['sql_order'])) {
					unset($post->post_content['sql_order']);
				}
			}

			// DEVO RICALCOLARE form_table che mi serve per capire se ci sono i bottoni dell'edit e del delete
			// la configurazione delle tabelle
			if (!isset($post->post_content['form_table']) || !is_array($post->post_content['form_table'])) {
				$post->post_content['form_table'] = [];
			}
		
			$fields_from = $table_model->get_partial_query_from(true);
		
			$style_list = ['WHITE','BLUE','GREEN','RED','YELLOW','PURPLE','BROWN'];
			foreach ($fields_from as $single_from) {
				if (!isset($single_from[1]) || array_key_exists($single_from[1], $post->post_content['form_table'])) {
					continue;
				} 
				$post->post_content['form_table'][$single_from[1]] = [
					'allow_create' => 'SHOW',	
					'show_title' => 'SHOW',
					'frame_style' => $style_list[rand(0,6)],
					'title' => '',
					'description' => '', 	
					'module_type' =>'EDIT'
				];
			}
			
			$post->post_content['sql_filter'] = [];
		
			if (isset($_REQUEST['sql_filter_field']) && is_array($_REQUEST['sql_filter_field'])) {
				foreach ($_REQUEST['sql_filter_field'] as $key=>$field) {
					if ($field != "" && $_REQUEST['sql_filter_val'][$key] != "") {
						$post->post_content['sql_filter'][] = ['column' => sanitize_text_field($field), 'op' => sanitize_text_field($_REQUEST['sql_filter_op'][$key]), 'value' => stripslashes($_REQUEST['sql_filter_val'][$key]), 'required' => ($_REQUEST['sql_filter_required'][$key])];
					} else {
						if ($_REQUEST['sql_filter_val'][$key] != "") {
							$return[] = __('a filter could not be saved because a field was not chosen to associate it with', 'database_press');
						} else if ($field != "") {
							$return[] = sprintf(__("I have not saved the filter associated with the <b>%s</b> field because it has no parameters to pass. If you want to filter the list by shortcode attributes use %s.", 'database_press'), $field, '[%params.attr_name]');
						}
					}
				}
			} 

			$post->post_content['delete_params'] = ['remove_tables_alias'=>[]];
			foreach ($_REQUEST['remove_tables_alias'] as $remove_tables_alias=>$allow) {
				$post->post_content['delete_params']['remove_tables_alias'][sanitize_text_field($remove_tables_alias)] = absint($allow);
			}


			// Verifico che nella query non vengano cambiati gli alias delle tabelle
			if ($error_query == "") {
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
							$return[] = sprintf(__('The settings have been saved, but you have changed the name of a query table (%s as %s). <br>This can cause an unexpected operation in the management of the list. <br>In these cases it is preferable to create a new list.', 'database_press'), $table, $table_alias);
						}
					}
				} 
				$from = [];
				foreach ($from_query as $f) {
					$from[$f[1]] = $f[0]; 
				}
				$post->post_content['sql_from'] = $from;
			
				// Salvo le chiavi primarie e lo schema
				$post->post_content['primaries'] = $table_model->get_pirmaries();	
				$post->post_content['schema'] = reset($table_model->items);
			} else {
				if (isset($post->post_content['primaries'])) unset($post->post_content['primaries']);
				if (isset($post->post_content['schema'])) unset($post->post_content['schema']);
				if (isset($post->post_content['sql_from'])) unset($post->post_content['sql_from']);
			}
		
			
			

		
		
			$show_query = false;
			/**
			 * @var dbpDs_list_setting[] $setting_custom_list
			 */
			$setting_custom_list =  dbp_functions_list::get_list_structure_config($items, $post->post_content['list_setting']);
			foreach ($setting_custom_list as $key_list=>$list) {
				$post->post_content['list_setting'][$key_list] = $list->get_for_saving_in_the_db();
			}
			$post_title = dbp_fn::get_request('post_title', '');
			
			if ($post_title != "") {
				wp_update_post(array(
					'ID'           => $id,
					'post_title' 	=> sanitize_text_field($post_title),
					'post_excerpt' 	=> sanitize_textarea_field(dbp_fn::get_request('post_excerpt')),
					'post_content' => addslashes(maybe_serialize($post->post_content)),
				));
			} else {
				$return[] = __('The title is required', 'database_press');
			}
			

			// permessi e menu admin
			$post_title = dbp_fn::get_request('post_title', '');
			$old = get_post_meta($id,'_dbp_admin_show', true);
			$title =  (@$post_title != "") ? $post_title : $_REQUEST['menu_title'];

			$dbp_admin_show  = ['page_title'=>sanitize_text_field($_REQUEST['menu_title']), 'menu_title'=>sanitize_text_field($_REQUEST['menu_title']), 'menu_icon'=>sanitize_text_field(trim($_REQUEST['menu_icon'])), 'menu_position'=>absint($_REQUEST['menu_position']), 'capability'=>'dbp_manage_'.$id, 'slug'=>'dbp_'.$id,'show'=>(isset($_REQUEST['show_admin_menu']) && $_REQUEST['show_admin_menu'] == 1) ? 1 : 0];
		
			if (isset($_REQUEST['show_admin_menu']) && $_REQUEST['show_admin_menu']) {
				if ($old != false) {
					update_post_meta($id, '_dbp_admin_show', $dbp_admin_show);
				} else {
					add_post_meta($id,'_dbp_admin_show', $dbp_admin_show, false);
				}
				foreach ($wp_roles->get_names() as $role_key => $_role_label) { 
					$role = get_role( $role_key );
					
					if (isset( $_REQUEST['add_role_cap']) && in_array ($role_key, $_REQUEST['add_role_cap'])) {
						$role->add_cap( 'dbp_manage_'.$id, true );
					} else {
						$role->remove_cap('dbp_manage_'.$id);
					}
				}
			} else {
				delete_post_meta($id, '_dbp_admin_show');
			}
			
			
		} else {
			$return[] = __('You have not selected any list', 'database_press');
		}
		if ($error_query != "") {
			$return[] = $error_query;
			$show_query = true;
		}
		$return = (count($return) == 0) ? true : implode("<br>", $return);
		
		return [$return, $show_query];
	}

	/**
	 * L'elenco dei dati estratti da una lista
	 */
	private function list_browse() {
		wp_add_inline_script( 'database-press-js', 'dbp_admin_post = "'.esc_url( admin_url("admin-post.php")).'";', 'before' );
		wp_enqueue_script( 'database-press-js', plugin_dir_url( __FILE__ ) . 'js/database-press.js',[],rand());

		wp_enqueue_script( 'database-form2-js', plugin_dir_url( __FILE__ ) . 'js/database-form2.js',[],rand());
		$file = plugin_dir_path( __FILE__  );
		$dbp_css_ver = date("ymdGi", filemtime( plugin_dir_path($file) . 'frontend/database-press.css' ));
		$dbp_js_ver = date("ymdGi", filemtime( plugin_dir_path($file) . 'frontend/database-press.js' ));

		wp_add_inline_script( 'dbp_frontend_js', 'dbp_post = "'.esc_url( admin_url('admin-ajax.php')).'";', 'before' );
		wp_enqueue_script( 'dbp_frontend_js' );

		$action = dbp_fn::get_request('action_query', '', 'string');
		$msg_error = "";
		
		$id = dbp_fn::get_request('dbp_id', 0, 'absint');
		$render_content = "/dbp-content-list-browse.php";
		$html_content = "";
		if ($id > 0) {
			$post = dbp_functions_list::get_post_dbp($id);
			if ($post == false) {
				?><script>window.location.href = '<?php echo admin_url("admin.php?page=dbp_list"); ?>';</script><?php
				die;
			}
			$list_title = $post->post_title;
			$description = $post->post_excerpt;
			$sql = @$post->post_content['sql'];
			if ($sql == "") {
				$link = admin_url("admin.php?page=dbp_list&section=list-sql-edit&dbp_id=".$id);
				$msg_error = '<a href="' . $link . '">'.__('You have to config the query first!', 'database_press')."</a>";
			}
			// TODO se c'è un limit nella query dovrebbe settare la paginazione?!
			$table_model 				= new Dbp_model();

			$list_of_columns 				= dbp_fn::get_all_columns();

			$table_model->prepare($sql);
			if ($table_model->sql_type() == "multiqueries") {
				//  NON GESTISCO MULTIQUERY NELLE LISTE
				$msg_error = __('No Multiquery permitted in list', 'database_press');
			} else if ($table_model->sql_type() == "select") {
				// se sto renderizzando questa tabella una form è stata già aperta
				dbp_fn::set_open_form(); 
				// cancello le righe selezionate!
				if ($action == "delete_rows" && isset($_REQUEST["remove_ids"]) && is_array($_REQUEST["remove_ids"])) {
					$result_delete = dbp_fn::delete_rows($_REQUEST["remove_ids"], '', $id);
					if ($result_delete['error'] != "") {
						$msg_error = $result_delete;
					} else {
						$msg = sprintf(__('The data has been removed. <br> %s', 'database_press'), $result_delete['sql']);
					}
				}

				if ($action == "delete_from_sql") {
					$result_delete = dbp_fn::dbp_delete_from_sql(dbp_fn::get_request('sql_query_executed'), dbp_fn::get_request('remove_table_query'));
					if ($result_delete != "") {
						$msg_error = $result_delete;
					} else {
						$msg = __('The data has been removed', 'database_press');
					}
				}

				if ( dbp_fn::get_request('filter.limit', 0) == 0) {
					if (isset($post->post_content['sql_limit']) &&  (int)$post->post_content['sql_limit'] > 0) {
						$sql_limit  = (int)$post->post_content['sql_limit'];
					} else {
						$sql_limit  = 100;
					}
					$_REQUEST['filter']['limit'] = $sql_limit ;
					$table_model->list_add_limit(0, $sql_limit);
				}
				if ( dbp_fn::get_request('filter.sort.field', '') == '') {
					if (isset($post->post_content['sql_order']['sort']) &&  isset($post->post_content['sql_order']['field'])) {
						$_REQUEST['sort']['field'] = $post->post_content['sql_order']['field'] ;
						$table_model->list_add_order($post->post_content['sql_order']['field'], $post->post_content['sql_order']['sort']);
					}
				}
				Dbp_functions_list::add_lookups_column($table_model, $post);

				// SEARCH in all columns
				$search = stripslashes(dbp_fn::get_request('search', false)); 
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
				dbp_fn::add_request_filter_to_model($table_model, $this->max_show_items);
				
				$table_items = $table_model->get_list();
				$table_model->update_items_with_setting($post);
				dbp_fn::items_add_action($table_model, $id);
				$table_model->check_for_filter();
				dbp_fn::remove_hide_columns($table_model);
				$html_table   = new Dbp_html_table();
				//var_dump($table_model->items);
				$html_content = $html_table->template_render($table_model); // lo uso nel template
				//print (get_class($table_model) );	
				dbp_fn::set_close_form(); 
			} else {
				$msg_error = __('You need to create a select query for the lists', 'database_press');
			}
		}  else {
			$msg_error = __('You have not selected any list', 'database_press');
		}
		require(dirname( __FILE__ ) . "/partials/dbp-page-base.php");
	}

	/**
	 * La struttura prevede di gestire quali campi visualizzare e come
	 */
	private function list_structure() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'database-list-structure-js', plugin_dir_url( __FILE__ ) . 'js/database-list-structure.js',[],rand());
		wp_enqueue_script( 'database-sql-editor-js', plugin_dir_url( __FILE__ ) . 'js/database-sql-editor.js',[],rand());

		// $dbp = new Dbp_fn();
		$id = dbp_fn::get_request('dbp_id', 0, 'absint');
		$action = dbp_fn::get_request('action', '', 'string');
		$msg = "";
		$msg_error = "";
		if ($action == 'list-structure-save') {
			$this->list_structure_save();
			$msg = __("Saved", 'database_press');
		}
		$table_model = new Dbp_model();
		$table_model2 = new Dbp_model();
		/**
		 * @var dbpDs_list_setting[] $items
		 */
		$items = []; 
		if ($id > 0) {
			$post = dbp_functions_list::get_post_dbp($id);
			$total_row = Dbp::get_total($id);
			$select_array_test = [];
			for($xtr = 1; ($xtr <= $total_row && $xtr < 100); $xtr++) {
				$select_array_test[$xtr] =  $xtr;
			}

			$list_title = $post->post_title;
			if (array_key_exists('sql', $post->post_content)) {
				$sql = $post->post_content['sql'];
				$table_model->prepare($sql);
				$list = $table_model->get_list();

				$table_model2->prepare($sql);
				Dbp_functions_list::add_lookups_column($table_model2, $post);
//
				$table_model2->get_list();
			
				$items = dbp_functions_list::get_list_structure_config($table_model2->items, $post->post_content['list_setting']);
				
			} else {
				$link = admin_url("admin.php?page=dbp_list&section=list-sql-edit&dbp_id=".$id);
				$msg_error = '<a href="' . $link . '">'.__('You have to config the query first!', 'database_press')."</a>";
			}
			$pinacode_fields = [];
			
			$primaries = $table_model->get_pirmaries();
			if (is_countable($items)) {
				foreach ($items as $key=>$item) {	
					$pinacode_fields[] = $item->name;
				}
				dbp_fn::echo_pinacode_variables_script(['data'=>$pinacode_fields]);
			}
			//
			$render_content = "/dbp-content-list-structure.php";
		} else {
			$msg_error = __('You have not selected any list', 'database_press');
		}
		if ($items === false) {
			$msg_error =  __('Something is wrong, check the query', 'database_press');
		}
		
		require(dirname( __FILE__ ) . "/partials/dbp-page-base.php");
	}

	/**
	 * Salva la struttura di una lista
	 * @return String error message
	 */
	private function list_structure_save() {
		// $dbp = new Dbp_fn();
		$id = dbp_fn::get_request('dbp_id', 0, 'absint');
		if ($id > 0) {
			$post = dbp_functions_list::get_post_dbp($id);
			
			
			unset($post->post_content['list_setting']);
			$count = 0;
			$list_setting = [];
			foreach ($_REQUEST['fields_toggle'] as $key=>$ft) {
				if ($key === 0 ) continue;
				$count++;
				$column_key = $key;
				if ($_REQUEST['fields_origin'][$key] == 'CUSTOM' && is_numeric($key)) {
					$title = sanitize_text_field(@$_REQUEST['fields_title'][$key]);
					$column_key = dbp_fn::clean_string($title);
				} 
				if (isset($header[$column_key])) {
					unset($header[$column_key]);
				}
				$list_setting[$column_key] = (new DbpDs_list_setting())->set_from_array(
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
					'lookup_id' => sanitize_text_field(@$_REQUEST['fields_lookup_id'][$key]),
					'lookup_sel_val' => sanitize_text_field(@$_REQUEST['fields_lookup_sel_val'][$key]),
					'lookup_sel_txt' => @$_REQUEST['fields_lookup_sel_txt'][$key]
					]
				);
			}
		
			// lo faccio sia prima che dopo per attivare i lookups e quindi salvarli
			
			$post->post_content['list_setting'] = $list_setting;
			
			$model = new Dbp_model();
			$model->prepare($post->post_content['sql']);
			$model->list_add_limit(0,1);
			Dbp_functions_list::add_lookups_column($model, $post);
			$model_items = $model->get_list();
			
			// aggiungo i metadati di schema estratti dalla query
			$list_setting = dbp_functions_list::get_list_structure_config($model_items, $list_setting);

		
			foreach ($list_setting as $key=>$single) {
				$post->post_content['list_setting'][$key] = $single->get_for_saving_in_the_db();
			}
			if (isset($_REQUEST['custom_query']) && $_REQUEST['custom_query'] !== '') {
				// aggiungo tutti i primary id e li salvo a parte 
				$table_model = new Dbp_model();
           		$table_model->prepare($_REQUEST['custom_query']);
				
				if ($table_model->sql_type() != "select") {
					return [ __('Only a single select query is allowed in the lists', 'database_press'), true];
				} else {
					$table_model->get_list();
					if ($table_model->last_error == "") {
						$post->post_content['sql'] = html_entity_decode($table_model->get_current_query());
					} else {
						return [sprintf(__("I didn't save the query because it was wrong!.<br><h3>Error:</h3>%s<h3>Query:</h3>%s",'database_press'), $table_model->last_error, $post->post_content['sql']), true];
					}
				}
			}
			if (isset($_REQUEST['list_general_setting']) && is_countable($_REQUEST['list_general_setting'])) {
				foreach ($_REQUEST['list_general_setting'] as $lgs_key => $list_general_setting) {
					$post->post_content['list_general_setting'][$lgs_key] = sanitize_text_field($list_general_setting);
				}
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
		wp_register_style( 'dbp_frontend_css',  plugins_url( 'frontend/database-press.css',  $file), false, rand());
		wp_enqueue_style( 'dbp_frontend_css' );
		wp_enqueue_script( 'database-list-setting-js', plugin_dir_url( __FILE__ ) . 'js/database-list-setting.js',[],rand());
		
		$pages  = get_pages(['sort_column' => 'post_title']); 
		
		// $dbp = new Dbp_fn();
		$id = dbp_fn::get_request('dbp_id', 0, 'absint');
		$action = dbp_fn::get_request('action', '', 'string');
		$render_content = "/dbp-content-list-setting.php";
		$msg = $msg_error = "";
		if ($id > 0) {
			
			if ($action == 'list-setting-save') {
				if ($this->list_setting_save($id)) {
					$msg = __("Saved", 'database_press');
				} else {
					$msg_error = __("There was a problem saving the data", 'database_press');
				}
			}
			$post = dbp_functions_list::get_post_dbp($id);
			//var_dump ($post->post_content['frontend_view']);
			$few = $post->post_content['frontend_view'];
			$errors_if_textarea  = "";
			if (@$few['checkif'] == 1) {
				$few['if_textarea'];
				$ris = PinaCode::math_and_logic($few['if_textarea']);
				$pc_errors = PcErrors::get('error');
				if (count($pc_errors) == 0) {
					if ( (is_numeric($ris) && $ris != 0 && $ris != 1) || (!is_numeric($ris)  && !is_bool($ris) &&  (is_string($ris) && !in_array(strtolower($ris), ["true",'t','false','f'])))) {
						$errors_if_textarea = __('The expression must return boolean, or a number or one of the following texts: true, t, false, f', 'database_press');
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
				$table_model = new Dbp_model();
				$table_model->prepare($sql);
				if ($table_model->sql_type() != "select") {
					$msg_error = __('Only a single select query is allowed in the lists', 'database_press');
				} else {
					$table_model->list_add_limit(0, 1);
					$items = $table_model->get_list();
					if ($table_model->last_error != "") {
						$msg_error = __('<h2>Query error:</h2>'.$table_model->last_error, 'database_press');
					} else {
						$pinacode_fields = ['data'=>[], 'params'=>['example'], 'request'=>['example'], 'total_row'];
						$items =  dbp_functions_list::get_list_structure_config($table_model->items, $post->post_content['list_setting']);
						foreach ($items as $key=>$item) {
							$pinacode_fields['data'][] = $item->name;
						}
						dbp_fn::echo_pinacode_variables_script($pinacode_fields);
	
					}
				}
			}

		}
		require(dirname( __FILE__ ) . "/partials/dbp-page-base.php");
	}

	/**
	 * Salvo i setting
	 */
	private function list_setting_save($id) {
		// $dbp = new Dbp_fn();
		$frontend_view = dbp_fn::get_request('frontend_view', []);
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
			$frontend_view['table_update'] = dbp_fn::get_request('editor_table_update');
			$frontend_view['table_pagination_style'] = dbp_fn::get_request('editor_table_pagination_style');
		} 
		$post = dbp_functions_list::get_post_dbp($id);
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
		// $dbp = new Dbp_fn();
		$id = dbp_fn::get_request('dbp_id', 0, 'absint');
		$action = dbp_fn::get_request('action', '', 'string');
		$msg = "";
		$msg_error = "";
		if ($action == 'list-form-save') {
			$this->list_form_save();
			$msg = __("Saved", 'database_press');
		}
		$tables = []; 
		if ($id > 0) {
			
			$post = dbp_functions_list::get_post_dbp($id);
			$total_row = Dbp::get_total($id);
			$select_array_test = [];
			for($xtr = 1; ($xtr <= $total_row && $xtr < 100); $xtr++) {
				$select_array_test[$xtr] =  $xtr;
			}

			$list_title = "FORM ".$post->post_title;
			if (array_key_exists('sql', $post->post_content)) {
	
				$form = new Dbp_class_form($id);
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
				$link = admin_url("admin.php?page=dbp_list&section=list-sql-edit&dbp_id=".$id);
				$msg_error = '<a href="' . $link . '">'.__('You have to config the query first!', 'database_press')."</a>";
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

			dbp_fn::echo_pinacode_variables_script($pinacode_fields);
			
			//
			$render_content = "/dbp-content-list-form.php";
		} else {
			$msg_error = __('You have not selected any list', 'database_press');
		}
		if ($tables === false) {
			$msg_error =  __('Something is wrong, check the query', 'database_press');
		}
		require(dirname( __FILE__ ) . "/partials/dbp-page-base.php");
	}

	/**
	 * Salva la form
	 *
	 * @return void
	 */
	private function list_form_save() {
		// $dbp = new Dbp_fn();
		$id = dbp_fn::get_request('dbp_id', 0, 'absint');
		if ($id > 0) {
			$post = dbp_functions_list::get_post_dbp($id);
			unset($post->post_content['form']);
			$count = 0;
			$rec_setting = false;
			// i campi
			foreach ($_REQUEST['fields_name'] as $key=>$ft) {
				if ($key === 0 ) continue;
				$count++;

				if (isset($_REQUEST['fields_delete_column'][$key]) && $_REQUEST['fields_delete_column'][$key] == 1) {
					// elimino il campo
					$model_structure = new Dbp_model_structure($_REQUEST['fields_orgtable'][$key]);
					$model_structure->delete_column($ft);
					$rec_setting = true;
				} else if (isset($_REQUEST['fields_edit_new'][$key])) {
					if ($_REQUEST['fields_edit_new'][$key] == "") {
						$_REQUEST['fields_edit_new'][$key] = 'fl_'.$key;
					}
					// creo il campo
					$model_structure = new Dbp_model_structure($_REQUEST['fields_orgtable'][$key]);

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
						'LOOKUP'=>['BIGINT',''],
						'CALCULATED_FIELD'=>['VARCHAR',255],
						'MEDIA_GALLERY'=>['BIGINT','']
					];
					if (array_key_exists($_REQUEST['fields_form_type'][$key], $array_convert_type_to_field)) {
						$config_new_column = $array_convert_type_to_field[$_REQUEST['fields_form_type'][$key]];
					} else {
						$config_new_column = ['VARCHAR',255];
					}
					// fields_name
					$ft = $model_structure->insert_new_column($_REQUEST['fields_edit_new'][$key], $config_new_column[0], $config_new_column[1]);
					$rec_setting = true;
					if ($ft == false) continue;
				}

				$custom_value = '';
				if (@$_REQUEST['fields_form_type'][$key] == 'CHECKBOX') {
					$custom_value =  stripslashes(@$_REQUEST['fields_custom_value_checkbox'][$key]);
				} else if (@$_REQUEST['fields_form_type'][$key] == 'CALCULATED_FIELD') {
					$custom_value =  stripslashes(dbp_fn::get_request('fields_custom_value_calc.'.$key));
				}
				$array_form =  [ 'name'=>sanitize_text_field($ft), 'order'=>sanitize_text_field(@$_REQUEST['fields_order'][$key]), 'table' => sanitize_text_field(@$_REQUEST['fields_table'][$key]), 'orgtable' => sanitize_text_field(@$_REQUEST['fields_orgtable'][$key]), 'edit_view' => sanitize_text_field(@$_REQUEST['fields_edit_view'][$key]), 'label'=> sanitize_text_field( stripslashes(@$_REQUEST['fields_label'][$key])), 'form_type'=> sanitize_text_field(@$_REQUEST['fields_form_type'][$key]), 'note'=> stripslashes(dbp_fn::get_request('fields_note.'.$key)), 'options'=> stripslashes(dbp_fn::get_request('fields_options.'.$key)), 'required'=> sanitize_text_field(@$_REQUEST['fields_required'][$key]), 'custom_css_class'=> stripslashes(dbp_fn::get_request('fields_custom_css_class.'.$key)), 'default_value'=> stripslashes(dbp_fn::get_request('fields_default_value.'.$key)), 'js_script'=> stripslashes(dbp_fn::get_request('fields_js_script.'.$key)), 'custom_value'=> $custom_value];


				//var_dump ($array_form);

				if (@$_REQUEST['fields_form_type'][$key] == 'CALCULATED_FIELD' && isset($_REQUEST['where_precompiled'][$key]) && $_REQUEST['where_precompiled'][$key] == 1) {
					$array_form['where_precompiled'] =  1;
				}
				if (@$_REQUEST['fields_form_type'][$key] == 'CALCULATED_FIELD') {
					$array_form['custom_value_calc_when'] =   sanitize_text_field($_REQUEST['fields_custom_value_calc_when'][$key]) ;
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
						$array_form['lookup_id'] = sanitize_text_field(@$_REQUEST['fields_lookup_id'][$key]);
						$array_form['lookup_sel_val'] = sanitize_text_field($_REQUEST['fields_lookup_sel_val'][$key]); 
						$array_form['lookup_sel_txt'] = sanitize_text_field($_REQUEST['fields_lookup_sel_txt'][$key]);
						$array_form['lookup_where'] = stripslashes($_REQUEST['fields_lookup_where'][$key]);
					}
				}
				$post->post_content['form'][] = $array_form;
			}

			if ($rec_setting || 1==1) {
		
				$table_model = new Dbp_model();
				$table_model->prepare($post->post_content['sql']);
				$table_model->list_add_limit(0, 1);
				$model_items = $table_model->get_list();
				$setting_custom_list =  dbp_functions_list::get_list_structure_config($model_items,  $post->post_content['list_setting']);
				foreach ($setting_custom_list as $key_list=>$list) {
					$post->post_content['list_setting'][$key_list] = $list->get_for_saving_in_the_db();
				}
				
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
				
				$alias = dbp_fn::get_table_alias($_REQUEST['link_table'], $post->post_content['sql'], $_REQUEST['link_table_column']);
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