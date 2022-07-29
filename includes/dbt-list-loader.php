<?php
/**
 * Gestisco il filtri e hook per le liste
 *
 * @package    database-table
 * @subpackage database-table/includes
 */
namespace DatabaseTables;

class Dbt_list_loader {

    public function __construct() {
       
        // Questa una chiamata che deve rispondere un csv
        add_action( 'admin_post_dbt_create_list', [$this, 'create_list']);	
        add_action('admin_head', [$this, 'echo_dbt_id_variables_script']);
        add_action( 'wp_ajax_dbt_test_formula', [$this, 'test_formula']);
        add_action( 'wp_ajax_dbt_recalculate_formula', [$this, 'recalculate_formula']);
        add_action( 'wp_ajax_dbt_get_list_columns', [$this, 'get_list_columns']);
    }
    /**
     * Crea una nuova lista
     */
    function create_list() {
        global $wpdb;
        Dbt_fn::require_init();
        // SE c'è una query la scrivo
        if (!isset($_REQUEST['table_choose']) && !isset($_REQUEST['new_sql'])) {
            wp_redirect( admin_url("admin.php?page=dbt_list&section=list-all&msg=create_list_error"));
        }
        $title = wp_strip_all_tags( $_REQUEST['new_title'] );
       // TODO: if (!is_admin()) return;
        $create_list = array(
            'post_title'    => $title,
            'post_content'  => '{}',
            'post_status'   => 'publish',
            'comment_status' =>'closed',
            'post_author'   => get_current_user_id(),
            'post_type' => 'dbt_list'
        );
        $id = wp_insert_post($create_list);
        
        if (is_wp_error($id) || $id == 0) {
            wp_redirect( admin_url("admin.php?page=dbt_list&section=list-all&msg=create_list_error"));
        } else {
            if (isset($_REQUEST['new_sql'])) {
                $sql = html_entity_decode ($_REQUEST['new_sql']);
            } else if (isset($_REQUEST['table_choose'])) {
                if ($_REQUEST['table_choose'] == 'create_new_table') {
                    // il nome della tabella
                    $table_name = str_replace($wpdb->prefix, '', Dbt_fn::clean_string($title));
                    $count = 0;
                    if ($table_name == "") {
                       $table_name = uniqid();
                    }
                    $table_name = $wpdb->prefix."dbt_".$table_name;
                    $table_name_temp = $table_name;
                    
                    while (Dbt_fn::exists_table($table_name)) {
                        $count ++;
                        $table_name = $table_name_temp ."_". $count;
                    }
                    $table_as = substr(str_replace([$wpdb->prefix,"_","-"], '',  $table_name), 0, 3);

                    $charset = $wpdb->get_var('SELECT @@character_set_database as cs');
                    if ($charset == "") {
                        $charset = 'utf8mb4';
                    }
                    $ris = $wpdb->query('CREATE TABLE `'.$table_name.'` ( `dbt_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`dbt_id`))   ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET='.$charset);
                    
                    $sql = 'SELECT `'.$table_as.'`.* FROM `'.$table_name.'` `'.$table_as.'`';
                    // TODO METTO LA TABELLA IN DRAFT MODE!
                    Dbt_fn::update_dbt_option_table_status($table_name, 'DRAFT');

                } else {
                    $table_name = $_REQUEST['mysql_table_name'];
                    // ho già la tabella
                    $table_as = substr(str_replace([$wpdb->prefix,"_","-"], '',  $table_name), 0, 3);

                    $sql = 'SELECT `'.$table_as.'`.* FROM `'.$table_name.'` `'.$table_as.'`';
                }
            }
            $post = Dbt_functions_list::get_post_dbt($id);
            if ($sql != "") {
                $table_model = new Dbt_model();
                $table_model->prepare($sql);
                if ($table_model->sql_type() != "select") {
                    //TODO Al momento il messaggio di errore non è usato da impostare con i cookie !!!!
                    $msg = __('Only a single select query is allowed in the lists', 'database_tables');
                    wp_redirect( admin_url("admin.php?page=dbt_list&section=list-sql-edit&msg=list_created&dbt_id=".$id));
                } else {
                    $limit = $table_model->remove_limit();
                    if ($limit > 0) {
                        $post->post_content['sql_limit'] = $limit;
                    }
                    
                  
                    // Questo pezzo di codice è copiato pari pari da class-database-list-admin.php > list_sql_save()
                    // Rigenero list_structure perché risalvando la query potrebbero essere cambiati i parametri!
                    $table_model->list_add_limit(0, 1);
                    $table_model->add_primary_ids();
                    $items = $table_model->get_list();
                    if (isset($post->post_content['list_setting'])) {
                        $list_setting = $post->post_content['list_setting'];
                    } else {
                        $list_setting = [];
                    }
                    $setting_custom_list =  Dbt_functions_list::get_list_structure_config($items, $list_setting);
                    $table_model->remove_limit();
                    $table_model->remove_order();
                    $post->post_content['sql'] = $table_model->get_current_query();

                    
                    $post->post_content['list_setting'] = [];
                    foreach ($setting_custom_list as $column_key => $list) {
                        $post->post_content['list_setting'][$column_key] =  $list->get_for_saving_in_the_db();
                    }

                    $dbt_admin_show  = ['page_title'=>sanitize_text_field($title), 'menu_title'=>sanitize_text_field($title), 'menu_icon'=>'dashicons-database', 'menu_position' => 120, 'capability'=>'dbt_manage_'.$id, 'slug'=> 'dbt_'.$id, 'show' => 1];
                    add_post_meta($id,'_dbt_admin_show', $dbt_admin_show, false);
                
                    wp_update_post(array(
                        'ID'           => $id,
                        'post_content' => addslashes(maybe_serialize($post->post_content)),
                    ));
                    $role = get_role( 'administrator' );
                    $role->add_cap( 'dbt_manage_'.$id, true );

                }
            }
            // ridirigo alla gestione della form 
            wp_redirect( admin_url("admin.php?page=dbt_list&section=list-form&msg=list_created&dbt_id=".$id));
        }
    }

    /**
     * Aggiungo l'id della lista se è presente così da poterla richiare negli ajax senza dovermela passare ogni volta
     */
    function echo_dbt_id_variables_script() {
       
		if (isset($_REQUEST['dbt_id'])) {
			 ?>
<script>
	var dbt_global_list_id = <?php echo $_REQUEST['dbt_id']; ?>;
</script>
			 <?php 
		}
	 }

    /**
      * Testo una formula pinacode
      */
    function test_formula() {
        Dbt_fn::require_init();
        $formula = stripslashes(@$_REQUEST['formula']);
        $post_id = stripslashes(@$_REQUEST['dbt_id']);
        $row = stripslashes(@$_REQUEST['row']);
        $json_result = ['formula'=>$formula, 'id'=>$post_id, 'row'=>$row, 'error'=>[], 'warning'=>[],'notice'=>[], 'response'=>'', 'typeof'=>'NULL', 'pinacode_data'=>[] ];
        if ($formula != "" && $post_id > 0 && $row > 0) {
            $post        = Dbt_functions_list::get_post_dbt($post_id);
            $table_model = new Dbt_model();
            if (isset($post->post_content['sql'])) {
                $table_model->prepare($post->post_content['sql']);
            } else {
                $table_model = false;
            }
            if ($table_model != false && $table_model->sql_type() == "select") {
                $table_model->list_add_limit($row -1 ,1);
                $table_model->add_primary_ids();
                $table_items = $table_model->get_list();
                //Dbt_fn::add_primary_ids_to_sql($table_model, $table_items);
                // Preparo i dati da editare a seconda di quanti sono i risultati
                if (is_countable($table_items) && count($table_items) > 1) {
                    $header = reset($table_items);
                    $items = Dbt_fn::convert_table_items_to_group($table_items, false);
                    //var_dump ($items);
                    foreach ($items as $item_key=>$item) {
                        foreach ($item as $key=>$value_item) {
                            //echo " SET PINACODE:".Dbt_fn::clean_string($header[$key]['schema']->table).".".Dbt_fn::clean_string($header[$key]['schema']->name)." = ".$value_item."\n ";
                            PinaCode::set_var(Dbt_fn::clean_string($header[$key]['schema']->table).".".Dbt_fn::clean_string($header[$key]['schema']->name), $value_item);
                            PinaCode::set_var("data.".Dbt_fn::clean_string($header[$key]['schema']->name), $value_item);
                        }
                    }
                }
                $json_result['pinacode_data'] = PinaCode::get_var('*');
                $json_result['response'] =  PinaCode::execute_shortcode( $formula );
                $json_result['typeof'] = gettype( $json_result['response']);
                $json_result['error'] = PcErrors::get('error', true);
                $json_result['warning'] = PcErrors::get('warning', true);
                $json_result['notice'] = PcErrors::get('notice', true);
            }
        }
        wp_send_json($json_result);
		die();
    }
    /**
      * Rieseguo una formula e salvo il risultato nel db
      */
    function recalculate_formula() {
        global $wpdb;
        Dbt_fn::require_init();
        $formula = stripslashes(@$_REQUEST['formula']);
        $el_id = stripslashes(@$_REQUEST['el_id']);
        $post_id = stripslashes(@$_REQUEST['dbt_id']);
        $limit_start = stripslashes(@$_REQUEST['limit_start']);
        $limit = 3;
        $errors = [];
        $json_result = ['formula'=>$formula, 'el_id'=>$el_id, 'total'=>@$_REQUEST['total'], 'limit_start'=>$limit_start+$limit, 'msgs'=>[],  'success_count'=>absint(@$_REQUEST['success_count']), 'error_count' => absint(@$_REQUEST['error_count']) ];
        if ($formula != "" && $post_id > 0) {
            $post        = Dbt_functions_list::get_post_dbt($post_id);
            $table_model = new Dbt_model();
            if (isset($post->post_content['sql'])) {
                $table_model->prepare($post->post_content['sql']);
            } else {
                $table_model = false;
            }
            if ($table_model != false && $table_model->sql_type() == "select") {
                $table_model->list_add_limit($limit_start, $limit);
                $table_model->add_primary_ids();
                $table_items = $table_model->get_list();
                //Dbt_fn::add_primary_ids_to_sql($table_model, $table_items);
                if (!isset($_REQUEST['insert_table']) || !isset($_REQUEST['field_name'])) {
                    $json_result['error'] = __('Ops this looks like a bug, parameters are missing.', 'database_tables');
                    wp_send_json($json_result);
                    die();
                }
                // Preparo i dati da editare a seconda di quanti sono i risultati
                if (is_countable($table_items) && count($table_items) > 1) {
                    $header = reset($table_items);
                    $items = Dbt_fn::convert_table_items_to_group($table_items, false);
                    //var_dump ($items);
                    $row = $limit_start;
                    foreach ($items as $item_key=>$item) {
                        $row++;
                        //PinaCode::clean_var();
                        $primary_value = -1;
                        $primary_key = "";
                        $insert_field = "";
                        $insert_table = "";
                        foreach ($item as $key=>$value_item) {
                            //echo " SET PINACODE:".Dbt_fn::clean_string($header[$key]['schema']->table).".".Dbt_fn::clean_string($header[$key]['schema']->name)." = ".$value_item."\n ";
                            PinaCode::set_var(Dbt_fn::clean_string($header[$key]['schema']->table).".".Dbt_fn::clean_string($header[$key]['schema']->name), $value_item);
                            PinaCode::set_var("data.".Dbt_fn::clean_string($header[$key]['schema']->name), $value_item);

                            $primary_key = Dbt_fn::get_primary_key($header[$key]['schema']->table);
                            if ($_REQUEST['field_name'] == $header[$key]['schema']->orgname) {
                                $insert_field = $header[$key]['schema']->orgname;
                            }
                            if ($_REQUEST['insert_table'] == $header[$key]['schema']->orgtable) {
                                $insert_table = $header[$key]['schema']->orgtable;
                            }
                            if ($header[$key]['schema']->orgname == $primary_key) {
                                $primary_value = $value_item;
                            }
                        }

                        if ($insert_field != "" && $insert_table != "" && $primary_value > 0) {
                            $response =  PinaCode::execute_shortcode( $formula );
                            $pina_error = PcErrors::get('error', true);
                            if (count($pina_error) > 0) {
                                $errors[] = sprintf(__("row ID %s: The template engine gave an error: %s", 'database_tables'), $primary_value, array_shift($pina_error));
                                $json_result['error_count']++;
                            } else {
                                $sql = 'UPDATE `'.$insert_table .'` SET `'.$insert_field.'` = "'.esc_sql($response).'" WHERE `'.$primary_key .'` = "'.absint($primary_value).'" LIMIT 1;   ';
                                if ($wpdb->query($sql) !== false) {
                                    $json_result['success_count']++;
                                } else {
                                    $errors[] = "row ID ".$primary_value.":".$wpdb->last_error;
                                    $json_result['error_count']++;
                                }
                            }
                        } else {
                            $errors[] = sprintf(__("For row %s I can't find the primary key or the field or table in which to insert the data.", 'database_tables'), $row);
                            $json_result['error_count']++;
                        }
                        //$json_result['typeof'] = gettype( $json_result['response']);
                        //$json_result['error'] = PcErrors::get('error', true);

                    }
                }
            }
        }
        $json_result['error'] = implode("<br>", $errors);
        wp_send_json($json_result);
		die();
    }
    
    /**
     * Ritorna l'elenco delle colonne di una tabella
     */
    function get_list_columns() {
        Dbt_fn::require_init();
        $dbt_id = absint($_REQUEST['dbt_id']);
        $list = Dbt::get_list_columns($dbt_id, $_REQUEST['searchable']);
    
        $primaries = Dbt::get_primaries_id($dbt_id);
        //$dbt_id;
        $json_result = ['list' => $list, 'rif' => $_REQUEST['rif'], 'pri'=>array_shift($primaries)];
        wp_send_json($json_result);
		die();
    }

}

$database_tables_loader_import = new Dbt_list_loader();