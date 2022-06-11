<?php

/**
 * Il controller amministrativo specifico per le liste
 * @internal
 */
namespace DatabaseTables;

class DBT_admin_list_menu
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
		wp_enqueue_style( 'database-table-css' , plugin_dir_url( __FILE__ ) . 'css/database-table.css',[],rand());
		wp_enqueue_script( 'database-table-all-js', plugin_dir_url( __FILE__ ) . 'js/database-table-all.js',[],rand());
		wp_enqueue_script( 'database-form2-js', plugin_dir_url( __FILE__ ) . 'js/database-form2.js',[],rand());
		$id = absint(str_replace("dbt_", "" , $_REQUEST['page']));

		wp_register_script( 'database-table-js', plugin_dir_url( __FILE__ ) . 'js/database-table.js',[],rand());
		wp_add_inline_script( 'database-table-js', 'dbt_admin_post = "'.esc_url( admin_url("admin-post.php")).'";'."\n  var dbt_global_list_id =".$id , 'before' );
		wp_enqueue_script( 'database-table-js' );
		//TODO da verificare!
		//wp_enqueue_script( 'database-sql-editor-js', plugin_dir_url( __FILE__ ) . 'js/database-sql-editor.js',[],rand());

		$file = plugin_dir_path( __FILE__  );
		$dbt_css_ver = date("ymdGi", filemtime( plugin_dir_path($file) . 'frontend/database-table.css' ));
	
		wp_register_style( 'dbt_frontend_css',  plugins_url( 'frontend/database-table.css',  $file), false,   $dbt_css_ver );

		$action = Dbt_fn::get_request('action_query', '', 'string');
		Dbt_fn::require_init();
	
		$msg_error = "";
		
		$render_content = "/dbt-content-list-browse.php";
		$html_content = "";
		if ($id > 0) {
			$post = Dbt_functions_list::get_post_dbt($id);
		
			if ($post == false) {
				 _e('Something is wrong, call the site administrator', 'database_tables');
				 return;
			} else {
				$list_title = $post->post_title;
				$description = $post->post_excerpt;
				$sql = @$post->post_content['sql'];
				if ($sql == "") {
					$link = admin_url("admin.php?page=dbt_list&section=list-sql-edit&dbt_id=".$id);
					$msg_error = '<a href="' . $link . '">'.__('Something is wrong, call the site administrator', 'database_tables')."</a>";
				}
				// questo aggiunge i filtri del setting
				$table_model = Dbt_functions_list::get_model_from_list_params($post->post_content);
				$list_of_columns 				= Dbt_fn::get_all_columns();
				

				//	$_REQUEST['table'] = $table_model->get_table();
				if ($table_model->sql_type() == "multiqueries") {
					//  NON GESTISCO MULTIQUERY NELLE LISTE
					$msg_error = __('No Multiquery permitted in list', 'database_tables');
				} else if ($table_model->sql_type() == "select") {

					// cancello le righe selezionate!
					if ($action == "delete_rows" && isset($_REQUEST["remove_ids"]) && is_array($_REQUEST["remove_ids"])) {
						$result_delete = Dbt_fn::delete_rows($_REQUEST["remove_ids"], '', $id);
						if ($result_delete['error'] != "") {
							$msg_error = $result_delete;
						} else {
							$msg = sprintf(__('The data has been removed. <br> %s', 'database_tables'), $result_delete['sql']);
						}
					}

					$extra_params = Dbt_functions_list::get_extra_params_from_list_params(@$post->post_content['sql_filter']);
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

					Dbt_fn::add_request_filter_to_model($table_model, $this->max_show_items);

					//PinaCode::set_var('global.dbt_filter_path', "dbt".$id);
					//Dbt_functions_list::add_frontend_request_filter_to_model($table_model, $post->post_content , $id);
					
					$table_items = $table_model->get_list();
					$table_model->update_items_with_setting($post->post_content);
					Dbt_fn::items_add_action($table_model, $post->post_content);
					$table_model->check_for_filter();
					Dbt_fn::remove_hide_columns($table_model);
					$html_table   = new Dbt_html_table();
					$html_table->add_table_class('dbt-table-admin-menu');
					// TODO Devo rimettere i dati sulla tabella per paginazione ecc...
					 $html_table->add_extra_params($extra_params);
					//var_dump($table_model->items);
					$html_content = $html_table->template_render($table_model); // lo uso nel template
					//print (get_class($table_model) );	
				
				} else {
					$msg_error = __('Something is wrong, call the site administrator', 'database_tables');
				}
			}
		}  else {
			$msg_error = __('Something is wrong, call the site administrator', 'database_tables');
		}
		require(dirname( __FILE__ ) . "/partials/dbt-page-admin-menu.php");
		//print "OK DBT LIST ADMIN";
	}


    

}
