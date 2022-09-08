<?php

/**
 * Il controller amministrativo specifico per le liste
 * @internal
 */
namespace DatabasePress;

class  Dbp_admin_list_menu
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
    function controller() {
		wp_enqueue_style( 'database-press-css' , plugin_dir_url( __FILE__ ) . 'css/database-press.css',[],rand());
		wp_enqueue_script( 'database-press-all-js', plugin_dir_url( __FILE__ ) . 'js/database-press-all.js',[],rand());
		wp_enqueue_script( 'database-form2-js', plugin_dir_url( __FILE__ ) . 'js/database-form2.js',[],rand());
		$id = absint(str_replace("dbp_", "" , $_REQUEST['page']));

		wp_register_script( 'database-press-js', plugin_dir_url( __FILE__ ) . 'js/database-press.js',[],rand());
		wp_add_inline_script( 'database-press-js', 'dbp_admin_post = "'.esc_url( admin_url("admin-post.php")).'";'."\n  var dbp_global_list_id =".$id , 'before' );
		wp_enqueue_script( 'database-press-js' );
		//TODO da verificare!
		//wp_enqueue_script( 'database-sql-editor-js', plugin_dir_url( __FILE__ ) . 'js/database-sql-editor.js',[],rand());

		$file = plugin_dir_path( __FILE__  );
		$dbp_css_ver = date("ymdGi", filemtime( plugin_dir_path($file) . 'frontend/database-press.css' ));
		//wp_register_style( 'dbp_frontend_css',  plugins_url( 'frontend/database-press.css',  $file), false,   $dbp_css_ver );
		//wp_enqueue_style( 'dbp_frontend_css' );

		//wp_register_script( 'dbp_frontend_js',  plugins_url( 'frontend/database-press.js',  $file), false,   $dbp_js_ver, true );
		wp_enqueue_script( 'dbp_frontend_js' );
		$action = dbp_fn::get_request('action_query', '', 'string');
		dbp_fn::require_init();
	
		$msg_error = "";
		
		$render_content = "/dbp-content-list-browse.php";
		$html_content = "";
		if ($id > 0) {
			$post = dbp_functions_list::get_post_dbp($id);
			if ($post == false) {
				 _e('Something is wrong, call the site administrator', 'database_press');
				 return;
			} else {
				$list_title = $post->post_title;
				$description = $post->post_excerpt;
				$sql = @$post->post_content['sql'];
				if ($sql == "") {
					$link = admin_url("admin.php?page=dbp_list&section=list-sql-edit&dbp_id=".$id);
					$msg_error = '<a href="' . $link . '">'.__('Something is wrong, call the site administrator', 'database_press')."</a>";
				}
				// questo aggiunge i filtri del setting
				$table_model = dbp_functions_list::get_model_from_list_params($post->post_content);
				$list_of_columns 				= dbp_fn::get_all_columns();
				
				//print $table_model->get_current_query();
				//	$_REQUEST['table'] = $table_model->get_table();
				if ($table_model->sql_type() == "multiqueries") {
					//  NON GESTISCO MULTIQUERY NELLE LISTE
					$msg_error = __('No Multiquery permitted in list', 'database_press');
				} else if ($table_model->sql_type() == "select") {

					// SEARCH in all columns
					//print "action: ".$action ;
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
						//print ("<p>".$table_model->get_current_query()."</p>");
					} else {
						$_REQUEST['search'] = '';
					}

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

					$extra_params = dbp_functions_list::get_extra_params_from_list_params(@$post->post_content['sql_filter']);
					//var_dump ($extra_params);
					//die;
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

					dbp_fn::add_request_filter_to_model($table_model, $this->max_show_items);

					//PinaCode::set_var('global.dbp_filter_path', "dbp".$id);
					//dbp_functions_list::add_frontend_request_filter_to_model($table_model, $post->post_content , $id);
					
					$table_items = $table_model->get_list();
					//print "<p>".$table_model->get_current_query()."</p>";
					
					$table_model->update_items_with_setting($post);
					dbp_fn::items_add_action($table_model, $id);
					$table_model->check_for_filter();
					dbp_fn::remove_hide_columns($table_model);
					$html_table   = new Dbp_html_table();
					$html_table->add_table_class('dbp-table-admin-menu');
					// TODO Devo rimettere i dati sulla tabella per paginazione ecc...
					 $html_table->add_extra_params($extra_params);
					//var_dump($table_model->items);
					$html_content = $html_table->template_render($table_model); // lo uso nel template
					//print (get_class($table_model) );	
					dbp_fn::set_close_form(); 
				
				} else {
					$msg_error = __('Something is wrong, call the site administrator', 'database_press');
				}
			}
		}  else {
			$msg_error = __('Something is wrong, call the site administrator', 'database_press');
		}
		require(dirname( __FILE__ ) . "/partials/dbp-page-admin-menu.php");
		//print "OK dbp LIST ADMIN";
	}


    

}
