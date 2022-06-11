<?php 
/**
 * Tutte le funzioni che servono per gestire le liste
 *
 * @package    ListTable
 * @subpackage Database table/includes
 */
namespace DatabaseTables;

class Dbt_functions_list {
    /**
     * Elabora le impostazioni di una lista (la prima riga) e ne ritorna l'array della configurazione 
     * Sostituendolo allo schema 
     * @param Array $model_items i risultati di una query get_list() $table_model->items;
     * @param DbtDs_list_setting[] $list_setting 
     * @return DbtDs_list_setting[]
     */
    static function get_list_structure_config($model_items, $list_setting) {
        if (!is_array($model_items) || count($model_items) == 0) return false;

        //$columns = array_column($list_setting, 'order');
        //array_multisort($columns, SORT_ASC, $list_setting);
        
        uasort($list_setting, function($a,$b) {
            if (isset($a->order) && isset($b->order)) {
                if ($a->order == $b->order) {
                    return 0;
                }
                return ($a->order < $b->order) ? -1 : 1;
            } else {
                return 0;
            }
        });
        
        // Imposta rispetto alla list_setting l'elenco delle colonne
        $temp_items_2 = array_shift($model_items);
        if (!is_array($temp_items_2) || count($temp_items_2) == 0) return false;
        $count = 899;
        /**
         * @var DbtDs_list_setting[] $temp_items
         */
        $temp_items = [];
        Dbt_functions_list::private_add_name_request($temp_items_2);
        if (is_array($temp_items_2)) {
            foreach ($temp_items_2 as $key=>$item) {
                $temp_list_structue = new DbtDs_list_setting();
                foreach ($item['schema'] as $ki =>$vi) {
                    $temp_list_structue->$ki = $vi;
                }
                $count++;
                $temp_list_structue->type = Dbt_fn::h_type2txt($item['schema']->type); 
                $temp_list_structue->title = $item['schema']->name;
                $temp_list_structue->toggle = 'SHOW';
                $temp_list_structue->view = Dbt_fn::h_type2txt($item['schema']->type);
                $temp_list_structue->custom_code = '';
                $temp_list_structue->order = $count;
                $temp_list_structue->origin = 'FIELD';
                if ($temp_list_structue->view ==' NUMERIC') {
                    $temp_list_structue->searchable = '=';
                } else {
                    $temp_list_structue->searchable = 'LIKE';
                }
                
                $temp_list_structue->mysql_name = Dbt_functions_list::private_get_mysql_name($item['schema']);
                if (isset($item['schema']->orgtable)) {
                    $temp_list_structue->mysql_table = $item['schema']->orgtable;
                }
                if ($temp_list_structue->mysql_name == "") {
                    $temp_list_structue->searchable = 'no';
                }
           
                $temp_list_structue->width = '';
                $temp_list_structue->custom_param = '';
                $temp_list_structue->format_values = '';
                $temp_list_structue->format_styles = '';
                $temp_items[$key] = $temp_list_structue;
            }
        }
        
        /**
         * @var DbtDs_list_setting[] $temp_items
         */
        $items = [];
        foreach ($list_setting as $key => $column_setting) {
            if (array_key_exists($key, $temp_items)) {
                $items[$key] = $temp_items[$key];
                unset($temp_items[$key]);
            } else if ($column_setting->type == "CUSTOM") {
                $items[$key] = (new DbtDs_list_setting())->set_from_array(['name'=>$key,'type'=>'CUSTOM', 'origin'=>'CUSTOM', 'mysql_name'=>'', 'mysql_table'=>'', 'name_request'=>'']);
                $column_setting->view = 'CUSTOM';
            } else {
                continue;
            }
            $column_setting_array = $column_setting->get_array();
            foreach ($column_setting_array as $ks=>$vs) {
                if ($vs != "") {
                    $items[$key]->$ks = $vs;
                }
            }
        }
        return array_merge($items, $temp_items);
    }
    

    /**
     * Trova il post e converte il content con i default
     * [list_setting] la struttura dei singoli campi della lista sia frontend che backend
     * [list_general_setting] le impostazioni della lista (sia frontend che backend)
     * [frontend_view]
     * @return array;
     */
    static function get_post_dbt($post_id) {
        $post = get_post($post_id);
        if (!$post) return false;
        $post->post_content = self::convert_post_content_to_list_params($post->post_content);
        return $post;
    }

    /**
     * Converte il post_content nella struttura della lista
     *
     * @param string $post_content
     * @return array
     */
    static function convert_post_content_to_list_params($post_content) {
        $content = maybe_unserialize($post_content);
        if (is_object($content)) {
            $content = (array)$content;
        }
        if (!is_array($content)) {
            $content = [];
        } 
     
        if (!array_key_exists('list_setting', $content)) {
            $content['list_setting'] = [];
        } else {
            $new_list_setting = [];
            foreach ($content['list_setting'] as $key=>$list ) {
                $new_list_setting[$key] = (new DbtDs_list_setting())->set_from_array($list);
            }
            $content['list_setting'] = $new_list_setting;
        }
        if (!array_key_exists('frontend_view', $content)) {
            $content['frontend_view'] = [];
        }
        if (!array_key_exists('list_general_setting', $content)) {
            $content['list_general_setting'] = ['text_length' => 80, 'obj_depth'=>3];
        }
        if (!array_key_exists('form', $content)) {
            $content['form'] = [];
        } else {
            // TODO converto $content['form'] in DBT_form_setting
        }
        if (!array_key_exists('sql', $content)) {
            $content['sql'] = stripslashes($content['sql']);
        }

        if (array_key_exists('delete_params', $content)) {
            if (!is_a($content['delete_params'], 'DatabaseTables\DbtDs_list_delete_params') ) {
                $content['delete_params'] = new DbtDs_list_delete_params($content['delete_params']);
            }
        } else {
            $content['delete_params'] =  new DbtDs_list_delete_params();
        }
        return $content;
    }

    /**
     * torna tutte le colonne di tutte le tabelle interessate in una query
     * @param Array $item La prima riga dei risultati di una query di database_tables_model_base NON Convertiti in update_items_with_setting
     */
    static function get_all_fields_from_query($item) {
        global $wpdb;
       
        $tables =  $fields = [];
        foreach ($item as $e) {
            if (!array_key_exists($e['schema']->orgtable, $tables)) {
                $tables[$e['schema']->orgtable] = $wpdb->get_results('SHOW COLUMNS FROM `'.esc_sql($e["schema"]->orgtable).'`');
            }
            if (!array_key_exists($e['schema']->table, $fields)) {
                $temp_list =[];
                foreach ($tables[$e['schema']->orgtable] as $tso) {
                    $temp_list[] = '`' . $e['schema']->table . '`.`' . $tso->Field . '`';
                }
                $fields[$e['schema']->table] = $temp_list;
            }
        }

    }

    /**
     * Inserisce nella query di table_model il limit, l'order ed eventuali filtri 
     * @param Array $post_content
     * @return Model|false; 
     */
    public static function get_model_from_list_params($post_content) {
        $sql =  @$post_content['sql'];
        if ( $sql != "") {
            $table_model = new Dbt_model();
            $table_model->prepare($sql);
            if ($table_model->sql_type() == "select") {
                if (isset($post_content['sql_limit'])) {
                    $table_model->limit = (int)$post_content['sql_limit'];
                }
                if (isset($post_content['sql_order'])) {
                    if (isset($post_content['sql_order']['field']) && isset($post_content['sql_order']['sort'])) {
                    $table_model->list_add_order($post_content['sql_order']['field'], $post_content['sql_order']['sort']);
                    }
                }

                // aggiungo eventuali dbt_extra_attr 
                if (isset($_REQUEST['dbt_extra_attr'])) {
                    $extra_attr = json_decode(base64_decode($_REQUEST['dbt_extra_attr']), true);
                     if (json_last_error() == JSON_ERROR_NONE) {
                         if (isset($extra_attr['request'])) {
                             foreach ($extra_attr['request'] as $key=>$val) {
                                $_REQUEST[$key] = $val;
                             }
                             pinacode::set_var('request', $extra_attr['request']);
                         }
                         if (isset($extra_attr['params'])) {
                             pinacode::set_var('params', $extra_attr['params']);
                         }
                         if (isset($extra_attr['data'])) {
                             pinacode::set_var('data', $extra_attr['data']);
                         }
                     } 
                } 

                if (isset($post_content['sql_filter']) && is_array($post_content['sql_filter'])) {
                    $table_model->list_add_where($post_content['sql_filter']);
                }
                return $table_model;
            }
            return false;
        } else {
            return false;
        }
    }
     /**
     * estraggo le variabili pinacode dei filtri request, params
     * @param Array $post_content
     * @return Model|false; 
     */
    public static function get_extra_params_from_list_params($sql_filter) {
        
        $extra_value_pina = [];
        if (isset($sql_filter) && is_array($sql_filter)) {
            $shortcode_param = [];
            $shortcode_request = [];
            $shortcode_data = [];
            foreach ($sql_filter as $filter) {
                if (isset($filter['value'])) {
                    $shortcode_param = array_merge($shortcode_param, Dbt_functions_list::get_pinacode_params($filter['value']));
                    $shortcode_request = array_merge($shortcode_request, Dbt_functions_list::get_pinacode_params($filter['value'],'[%request'));
                    $shortcode_data = array_merge($shortcode_data, Dbt_functions_list::get_pinacode_params($filter['value'],'[%data'));
                    
                }
            }

            $param = PinaCode::execute_shortcode('[%params]');
            if (is_array($param)) {
                foreach ($shortcode_param as $val) {
                    $temp = $param[$val];
                    if ($temp != '' && !is_countable($temp)) {
                        if (!isset($extra_value_pina['params'])) {
                            $extra_value_pina['params'] = [];
                        }
                        $extra_value_pina['params'][$val] = $temp;
                    }
                }
            }
            $param = PinaCode::execute_shortcode('[%request]');
            if (is_array($param)) {
                foreach ($shortcode_request as $val) {
                    $temp = $param[$val];
                    if ($temp != '' && !is_countable($temp)) {
                        if (!isset($extra_value_pina['request'])) {
                            $extra_value_pina['request'] = [];
                        }
                        $extra_value_pina['request'][$val] = $temp;
                    }
                }
            }
            $param = PinaCode::execute_shortcode('[%data]');
            if (is_array($param)) {
                foreach ($shortcode_data as $val) {
                    $temp = $param[$val];
                    if ($temp != '' && !is_countable($temp)) {
                        if (!isset($extra_value_pina['data'])) {
                            $extra_value_pina['data'] = [];
                        }
                        $extra_value_pina['data'][$val] = $temp;
                    }
                }
            }
            
        }
        return $extra_value_pina;  
    }
     /**
     * Funzione per aggiungere limit (paginazione), order e altri where nel frontend.  Il risultato lo mette dentro il model. 
     * @param Class $table_model
     * @param String $request_path  {$request_path}_page = [] {$request_path}_sort
     */
    static function add_frontend_request_filter_to_model(&$table_model, $post_content, $list_id) {
        $request_path = "dbt".$list_id;
        $list_settings =  $post_content['list_setting'];
        $table_model->get_count();
        $table_limit 			= $table_model->limit;
        $table_limit_start 		= Dbt_fn::get_request_limit_start( $request_path .'_page', 1, ceil($table_model->total_items/$table_limit )) ;
        
        $limit_start = ($table_limit_start -1) * $table_limit;
        $table_model->list_add_limit($limit_start, $table_limit);
       
        // order
        $table_sort = Dbt_fn::get_request($request_path . '_sort', false); 

        if ($table_sort) {
            $sorts = explode(".", $table_sort);
            if (count($sorts) > 1) {
                $table_sort_order = array_pop($sorts);
                $table_sort_field =  Dbt_fn::get_val_from_head_column_name($list_settings, implode(".",$sorts), 'mysql_name' );
                if ($table_sort_field != "") {
                    $table_model->list_add_order($table_sort_field, $table_sort_order);
                }
            }
        }
        // search
       
        $search = stripslashes(Dbt_fn::get_request($request_path . '_search', false)); 
        if ($search) {
            $filter =[] ; //[[op:'', column:'',value:'' ], ... ];
            foreach ($list_settings as $list_setting) {
                if ($list_setting->searchable == "LIKE" && $list_setting->mysql_name != "") {
                    $filter[] = ['op'=>'LIKE', 'column'=> $list_setting->mysql_name, 'value' =>$search];
                }
                if ($list_setting->searchable == "=" && $list_setting->mysql_name != "") {
                    $filter[] = ['op'=>'=', 'column'=> $list_setting->mysql_name, 'value' =>$search];
                }
            }
            if (count($filter) > 0) {
                $table_model->list_add_where($filter, 'OR');
            }
           
        }
        $filter =[] ; //[[op:'', column:'',value:'' ], ... ];
        foreach ($_REQUEST as $req => $req_val) {
            if (substr($req,0, strlen($request_path)) == $request_path && $req != $request_path . '_search') {
                $request_field = substr($req, strlen($request_path)+1);
               // print "<p>request_fiel ".$request_field."</p>";
                $filter_temp =  Dbt_fn::convert_head_column_in_filter_array($list_setting, $request_field, $req_val);
                if ($filter_temp != false) {
                    $filter[] =$filter_temp ;
                }

            }
        }
        //var_dump ($filter);
        if (count($filter) > 0) {
            $table_model->list_add_where($filter, 'AND');
        }

        $search = stripslashes(Dbt_fn::get_request($request_path . '_search', false)); 
    }

    /**
     * In una stringa pinacode trova le variabili [%params.xxx] che sono le variabili scelti per gli shortcode
     * 
     * @param string $string
     * @param string $shortcode il parametro da cercare
     * @return array
     */
    public static function get_pinacode_params($string, $shortcode='[%params') {
        $start = 0;
        $shortcode_param = [];
        $length = strlen($shortcode)+1;
        do {
            $find = stripos($string, $shortcode, $start);
            if ($find !== false) {
                $end1 = stripos($string, ' ', $find + $length);
                $end2 = stripos($string,']',  $find + $length);
                if ($end1 !== false && $end2 !== false) {
                    $end = min($end1, $end2);
                } else if ($end1 !== false) {
                    $end = $end1;
                } else {
                    $end = $end2;
                }
                $param = trim(substr($string, $start + $length, $end - ($start + $length)));
                if (strlen($param) > 1 && strpos($param, ".") === false) {
                 
                    $shortcode_param[] = $param;
                }
                $start = $end+1;
            }
        } while ($find !== false);
        return $shortcode_param;
    }

    /**
     * TODO: Lo riscrivo usando dbt_form!!!!
     *
     * @param [type] $request_edit_table
     * @param [type] $dbt_id
     * @return void
     */
    public static function process_saving_data_using_form_list($request_edit_table, $dbt_id) {
        $form_dbt_id = new Dbt_class_form($dbt_id);
        list($form_settings, $__) = $form_dbt_id->get_form();
        foreach ($request_edit_table as $request_key => $form_value) {

            // SETTO le variabili su pinacode!
			foreach ($form_value as $table=>$rows) {
                if (isset($form_value[$table]["_dbt_alias_table_"]))  {
                    foreach ($form_value[$table]["_dbt_alias_table_"] as $key=>$alias_table) {
                       // print " Alias table: ".$alias_table."   \r\n";
                       
                        foreach ($rows as $field=>$value) {
                            if ($field == "_dbt_alias_table_ ") continue;
                            if (is_countable($value[$key])) {
								$value[$key] = maybe_serialize($value[$key]);
							}
                            $value[$key] = stripslashes( $value[$key] );
                            PinaCode::set_var(Dbt_fn::clean_string($alias_table).".".Dbt_fn::clean_string( $field), $value[$key]);
                            //print " SET :".$field." = " .$value[$key].";  \r\n  ";
                        }
                    }
                }
            }

            foreach ($form_value as $table=>$rows) {
                if (isset($form_value[$table]["_dbt_alias_table_"]))  {
                    foreach ($form_value[$table]["_dbt_alias_table_"] as $key=>$alias_table) {
                        foreach ($rows as $field=>$value) {
                            $field_setting = $form_dbt_id->find_setting_row_from_table_field($form_settings, $rows["_dbt_alias_table_"][$key], $field);

                            if ($field_setting != false && $field_setting->form_type == "CALCULATED_FIELD") {
                                $request_edit_table[$request_key][$table][$field][$key] = PinaCode::execute_shortcode($field_setting->custom_value);
                                PinaCode::set_var(Dbt_fn::clean_string($alias_table).".".Dbt_fn::clean_string($field), $request_edit_table[$request_key][$table][$field]);
                            }
                            if ($field_setting != false && $field_setting->form_type == "UPLOAD_FIELD") {

                                $new_file = [
                                    "name" => $_FILES['edit_table']["name"][$request_key][$table][$field][$key]['upload'],
                                    "full_path" => $_FILES['edit_table']["full_path"][$request_key][$table][$field][$key]['upload'],
                                    "tmp_name" =>  $_FILES['edit_table']["tmp_name"][$request_key][$table][$field][$key]['upload'],
                                    "type" =>  $_FILES['edit_table']["type"][$request_key][$table][$field][$key]['upload'],
                                    "error" => $_FILES['edit_table']["error"][$request_key][$table][$field][$key]['upload'],
                                    "size" => $_FILES['edit_table']["size"][$request_key][$table][$field][$key]['upload']
                                ];

                                if ($new_file['name'] != "") {
                                    if ( ! function_exists( 'wp_handle_upload' ) ) {
                                        require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                    }
                                    //  echo  json_encode($new_file)." | ";
                                    $file_return = wp_handle_upload( $new_file, array('test_form' => false ) );
                                    if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) || is_wp_error($file_return) ) {
                                        $error = _e('Failed to upload document to server', 'database_tables');
                                    } else { 
                                        $request_edit_table[$request_key][$table][$field][$key] = $file_return['url'];
                                    }
                                    //TODO add pinacode variables
                                }
                            }
                        }
                    }
                }
            }
        }
        return [$request_edit_table, $error];
    }
    

    /**
     * Stampo il modulo per selezionare le categorie nel campo post
     * @param int $catId
     * @param int $depth
     * @param int $count_field
     * @param array $selected_cats
     * @return string
     */
    static function form_categ_tree($catId, $depth, $count_field, $selected_cats){
        $depth += 1;  
        $output ='';
        $args = 'hierarchical=1&taxonomy=category&hide_empty=0&parent=';    
        $categories = get_categories($args . $catId);
        if(count($categories) > 0) {
            foreach ($categories as $category) {
                if (is_array($selected_cats)) {
                    $checked = (in_array($category->cat_ID, $selected_cats)) ? ' checked="checked"' : '';
                } else {
                    $checked = "";
                }
                $output .=  '<label class="dbt-form-cat dbt-form-cat-' . $depth .'"><input type="checkbox" name="fields_post_cats['.$count_field.'][]" value="'.$category->cat_ID.'"' . $checked . '>'. $category->cat_name . '</label>';
                $output .=  self::form_categ_tree($category->cat_ID, $depth, $count_field, $selected_cats);
            }
        }
        return $output;
    }

    /**
     * Stampo il modulo per selezionare i ruoli nel campo post
     * @param int $count_field
     * @param array $selected_cats
     * @return string
     */
    static function form_user_roles( $count_field, $selected_roles){
        global $wp_roles;
        $roles = $wp_roles->roles;
        $output = "";
        foreach ($roles as $key=>$rl) {
            if (is_array($selected_roles)) {
                $checked = (in_array($key, $selected_roles)) ? ' checked="checked"' : '';
            } else {
                $checked = "";
            }
            $output .=  '<label class="dbt-form-cat "><input type="checkbox" name="fields_user_roles['.$count_field.'][]" value="'.$key.'"' . $checked . '>'. $rl['name'] . '</label>';
        }
        return $output;
    }


    /**
     * il nome del campo così non lo devo ricalcolare ogni volta, ma soprattutto lo salvo sul list_setting per convertire i parametri della ricerca e del search nei request
     */
    private static function private_get_mysql_name($item) {

        if (@$item->orgname != "" && $item->table != "" ) {							
            $original_field_name = '`'.$item->table.'`.`'.$item->orgname.'`';
            
        } else if (@$item->orgname != "" && $item->orgtable ) {							
            $original_field_name = '`'.$item->orgtable.'`.`'.$item->orgname.'`';
        } else if (@$item->orgname != "" ) {							
            $original_field_name = '`'.$item->orgname.'`';
        } else {
            $original_field_name = '';
        }
        return $original_field_name;
    }

    /**
     * Gli passo tutto un array come riferimento l'header di $model->get_list e ci aggiunge la variabile name_request
     * Il namerequest serve perché quando invio una richiesta da un form questo è il nome del campo che invio
     */
    private static function private_add_name_request(&$temp_items_2) {
        $names = array();
        foreach ($temp_items_2 as &$item_o) {
            $item = $item_o['schema'];
            $calculate_name = "";
            if ($item->name != "") {
                $temp_exp = explode("_", str_replace(" ","_", $item->name));
                if (count($temp_exp) == 1) {
                    $name1 = Dbt_functions_list::private_clean(6,  $item->name );
                } else {
                    $first =  array_shift($temp_exp);
                    $name1 = Dbt_functions_list::private_clean(4, implode("", $temp_exp), $first );
                }
                $name2 = Dbt_functions_list::private_clean(15, $item->name);
                $name3 = Dbt_functions_list::private_clean(4,  $item->name, $item->table);
                $name4 = Dbt_functions_list::private_clean(8, $item->name, $item->table);
                if ( !in_array($name1, $names) ) {
                    $calculate_name =  $name1;
                } elseif (!in_array($name2, $names) ) {
                    $calculate_name =  $name2;
                } elseif ($item->table != "" && !in_array( $name3, $names) ) {
                        $calculate_name =  $name3 ;
                }   elseif ($item->table != "" && !in_array( $name4, $names) ) {
                    $calculate_name =  $name4 ;
                }
            } 
        
            if (in_array( $calculate_name, $names) || $calculate_name == "") {
                $calculate_name =  Dbt_fn::clean_string(Dbt_fn::get_uniqid());
            } 
            $item_o['schema']->name_request = $calculate_name;
            $names[] = $calculate_name;
        }
    }

    /**
     * 
     */
    private static function private_clean($substr, $var1, $var2 = "") {
        $var1 = substr(Dbt_fn::clean_string($var1), 0, $substr);
        if ($var2 != "") {
            $var2 = substr(Dbt_fn::clean_string($var2), 0, $substr);
            return $var2."_".$var1;
        }
        return $var1;
    }


}