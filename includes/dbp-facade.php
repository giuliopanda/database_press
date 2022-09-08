<?php
/**
* funzioni pubbliche
* Se stai in function.php ricordati di chiamarle attraverso il namespace: DatabasePress\Dbp::get_list(...);
*/
namespace DatabasePress;

class  Dbp
{

    static $frontend_template_engine_data = [];
    /**
     * Carica una lista da un id e ne ritorna l'html. Praticamente la stessa cosa che fa lo shortcode!
     * @param Int $post_id
     * @param Boolean $only_table Se stampare i filtri e la form che la raggruppa oppure no (true)
     * @param Array $params I parametri aggiuntivi che verranno salvati in [%params
     * @param String $prefix Il prefisso delle variabili che verranno passate
     */
    static function get_list($post_id, $only_table = false, $params = [], $prefix="") {
        dbp_fn::require_init();
        
        $custom_data = apply_filters('dbp_frontend_get_list', '', $post_id);
        if ($custom_data != "") {
            return $custom_data;
        }
        $ori_params =  PinaCode::get_var('params');
        $ori_globals = PinaCode::get_var('global');
        PinaCode::set_var('params', $params);
        $show_trappings = true;
        if (dbp_fn::is_form_open()) {
            // la form che uso di solito per gestire le tabelle è stata già aperta e non chiusa
            // da un'altra quindi si sta renderizzando una lista dentro un'altra lista
            // per cui disabilito ordinamento, paginazione e ricerca!!
            $show_trappings = false;
        }
        dbp_fn::set_open_form();
        $list =  new Dbp_render_list($post_id, null, $prefix);
        //print ("REQUEST['dbp_div_id'] ");
        //var_dump($_REQUEST);
        if ($only_table || !$show_trappings) {
            $list->hide_div_container();
            // setto il div
            if (isset($_REQUEST['dbp_div_id'])) {
                $list->set_uniqid($_REQUEST['dbp_div_id']);
            }
        }
        if (!$show_trappings) {
            // Faccio finta che la form è stata già create
            $list->block_opened = true;
            $list->frontend_view_setting['table_sort'] = false;
        }
        if ( $list->get_frontend_view('type', 'TABLE_BASE') == "TABLE_BASE") {
            dbp_fn::remove_hide_columns($list->table_model);
            ob_start();
            $show_table = true;
            if (isset($list->frontend_view_setting['checkif']) && $list->frontend_view_setting['checkif'] == 1 && isset($list->frontend_view_setting['if_textarea']) && $list->frontend_view_setting['if_textarea'] != '') {
                $show_table = (boolean)trim(PinaCode::math_and_logic($list->frontend_view_setting['if_textarea']));
            } 
            if ($show_table) {
                if ($list->get_frontend_view('table_search') == 'simple' && $show_trappings) {
                    $list->search();
                }
                if (in_array($list->get_frontend_view('table_pagination_position'), ["up",'both']) && $show_trappings) {
                    $list->pagination();
                }
                if (($list->no_result == '' || empty($list->no_result)) || count($list->table_model->items) > 1) {
                    $list->table();
                } else {
                    echo $list->no_result; 
                }
                if (in_array($list->get_frontend_view('table_pagination_position'), ["down",'both']) && $show_trappings) {
                    $list->pagination();
                }
                if ($show_trappings) {
                    $list->end();
                } 
            } else {
                if ($list->get_frontend_view('table_update') != "none") {
                    ob_start() ;
                    $list->open_block(false);
                    $result[] = ob_get_clean();
                    ob_start();
                    $list->search();
                    $search = ob_get_clean();
                    PinaCode::set_var('html.search',  $search);
                    ob_start();
                    $list->pagination();
                    $pagination = ob_get_clean();
                    PinaCode::set_var('html.pagination',  $pagination);
                   
                } else {
                    PinaCode::set_var('html.pagination',  '');
                    PinaCode::set_var('html.search',  '');
                }
                ob_start();
                $list->table();
                $table = ob_get_clean();
                PinaCode::set_var('html.table',  $table);
                PinaCode::set_var('html.no_result',  $list->no_result);
                PinaCode::set_var('data',  $list->table_model->items);
                echo PinaCode::execute_shortcode($list->frontend_view_setting['content_else']);
            }
            PinaCode::set_var('global',  $ori_globals);
            PinaCode::set_var('params', $ori_params);
            return ob_get_clean();
        } else {
            // EDITOR
            $result = [];
            $items = $list->table_model->items;
            $text = $list->get_frontend_view('content');
            if ($list->get_frontend_view('table_update') != "none") {
                ob_start() ;
                $list->open_block(false);
                $result[] = ob_get_clean();
                ob_start();
                $list->search();
                $search = ob_get_clean();
                PinaCode::set_var('html.search',  $search);
            } else {
                PinaCode::set_var('html.search',  '');
            }
            if (is_array($items)) {
                $table_header = reset($list->table_model->items);  
                $first_row = array_shift($items);
                $first_row = array_map(function($el) {return $el->name;}, $first_row);
                PinaCode::set_var('total_row', absint($list->table_model->get_count()));
                PinaCode::set_var('key',0);
                $first_row = dbp_fn::remove_hide_columns_in_row($table_header, $first_row);
                PinaCode::set_var('data',  $first_row);
                if ($list->get_frontend_view('table_update') != "none") {
                    ob_start();
                    $list->pagination();
                    $pagination = ob_get_clean();
                    PinaCode::set_var('html.pagination',  $pagination);
                } else {
                    PinaCode::set_var('html.pagination',  '');
                }
            } 
            if (!is_array($items) || !$show_trappings) {
                PinaCode::set_var('total_row', 0);
                PinaCode::set_var('key',0);
                PinaCode::set_var('data',  []);
                PinaCode::set_var('html.pagination',    '');
                PinaCode::set_var('html.search',    '');
            }
            $result[] = PinaCode::execute_shortcode($list->get_frontend_view('content_header'));
            
            //var_dump ($first_row);
            if (is_array($items) && $text != "") {
                foreach ($items as $key=> $item) {
                    //PinaCode::set_var('primaries', dbp_fn::data_primaries_values( $primaries, $table_header, $item));
                    PinaCode::set_var('key', ($key+1));
                    //$item = dbp_fn::remove_hide_columns_in_row($table_header, $item);
                    PinaCode::set_var('data', $item);
                    $temp = PinaCode::execute_shortcode($text);
                    if (is_array($temp)) {
                        $result[] = json_encode($temp); 
                    } else {
                        $result[] = $temp; 
                    }
                }
            }
            PinaCode::set_var('data', []);
            $result[] = PinaCode::execute_shortcode($list->get_frontend_view('content_footer'));
           
            if ($list->get_frontend_view('table_update') != "none") {
                ob_start() ;
                $list->end();
                $result[] = ob_get_clean();
            }
            if (isset($list->frontend_view_setting['popup_type']) && isset($list->frontend_view_setting['popup_type']) != '') {
                foreach ($result as &$res) {
                    $res = str_replace("js-dbp-popup", "js-dbp-popup js-dbp-popup-mode-".$list->frontend_view_setting['popup_type'], $res);
                }
            }
            PinaCode::set_var('global',  $ori_globals);
            PinaCode::set_var('params', $ori_params);
            return implode("",$result);
        }
        if ($show_trappings) {
            dbp_fn::set_close_form();
        }
        PinaCode::set_var('global',  $ori_globals);
        PinaCode::set_var('params', $ori_params);
    }

    /**
     * Ritorna la classe che genera la tabella
     * return \dbp_render_list;
     */
    static function render($dbp_id, $mode = null) {
        dbp_fn::require_init();
        return new Dbp_render_list($dbp_id, $mode);
    }

    /**
     * Calcola il totale dei record dei dati estratti da una lista
     * @param number $post_id l'id della lista
     * @param boolean $filter se aggiungere i filtri oppure no alla query
     * @return int -1 se non riesce a fare il conto
     */
    static function get_total($post_id, $filter = false) {
        $post        = dbp_functions_list::get_post_dbp($post_id);
        if ($filter) {
            $table_model = dbp_functions_list::get_model_from_list_params($post->post_content);
        } else {
            $table_model = new Dbp_model();
            if (isset($post->post_content['sql'])) {
                $table_model->prepare($post->post_content['sql']);
            } else {
                $table_model = false;
            }
        }
        if ($table_model != false && $table_model->sql_type() == "select") {
            return $table_model->get_count();
        }
        return -1;
       
    }

     /**
     * Carica tutte le liste dbp
     * @return array 
     * ```json
     * {'id':'title','id':'...'}
     * ```
     */
    static function get_lists_names() {
        global $wpdb;
        $query_lists = $wpdb->get_results('SELECT ID, post_title FROM '.$wpdb->posts.' WHERE post_type ="dbp_list" AND `post_status` = "publish" ORDER BY post_title');
        $lists = [];
        if (is_countable($query_lists)) {
            foreach ($query_lists as $ql) {
                $lists[$ql->ID] = $ql->post_title;
            }
        }
        return $lists;
    }

    /**
     * Ritorna l'elenco delle colonne di una lista
     * @param int $dbp_id 
     * @param bool searchable 
     * @param bool extend 
     * @return dbpDs_list_setting[]|array
     * SE extend è false
     * ```json
     * {'field_name':'field_title','field_name':'...'} 
     * ```
     * Se extend è true dbpDs_list_setting[]
     */
    static function get_list_columns($dbp_id, $searchable = true, $extend = false) {
        $post  = dbp_functions_list::get_post_dbp($dbp_id);
        /**
         * @var dbpDs_list_setting[] $lists
         */
        $lists = [];
        /**
         * @var dbpDs_list_setting[] $list_setting
         */
        $list_setting = [];
        if (!isset($post->post_content) ) {
            return [];
        }
       
        $sql = @$post->post_content['sql'];
        if ($sql != "") {
            $table_model = new Dbp_model();
            $table_model->prepare($sql);
            $table_model->list_add_limit(0, 1);
            $model_items = $table_model->get_list();
            if ($table_model->sql_type() == "select") {
                $list_setting = dbp_functions_list::get_list_structure_config($model_items, $post->post_content['list_setting']);
      
            } else {
                return [];  
            }
        } else {
            return [];
        }
       
        
        if ($extend) {
            if (!$searchable) {
                $lists = $list_setting;
            } else {
                foreach ($list_setting as $key=>$pcls) {
                    if (!$searchable || ($searchable &&  $pcls->searchable != "no" && $pcls->mysql_table != '')) { 
                        $lists[$pcls->name] = $pcls;
                    }
                }
            }
        } else {
            foreach ($list_setting as $key=>$pcls) {
                if (!$searchable || ($searchable &&  $pcls->searchable != "no" && $pcls->mysql_table != '')) { 
                 
                    $lists[$pcls->name] = ($pcls->isset('title')) ? $pcls->title : $key;
                }
            }
        }
        return $lists;
    }

    /**
     * Ritorna l'elenco delle colonne di una lista con l'url request da un lato, il nome dell'altro
     * @param int $dbp_id 
     * @param bool searchable 
     * @param bool extend 
     * @return dbpDs_list_setting[]|array
     * SE extend è false
     * ```json
     * {'field_name':'field_title','field_name':'...'} 
     * ```
     * Se extend è true dbpDs_list_setting[]
     */
    static function get_ur_list_columns($dbp_id) {
        $list_settings = self::get_list_columns($dbp_id, true, true);
        $lists = [];
        foreach ($list_settings as $list_setting) {
            $lists[$list_setting->name_request] = $list_setting->name;
        }
        return $lists;
    }

    /**
     * Ritorna l'elenco delle chiavi primarie di una lista. I campi estratti sono gli alias!
     * TODO NON funziona, vorrei che fosse salvato nella lista comunque sto lavorando su model->add_primary_ids
     * @param int $dbp_id
     * @todo Ora i parametri sono salvati in post_content['primaries'];
     * @return array [table=>primary_name, ]
     */
    static function get_primaries_id($dbp_id, $name_request = true) {
        $post  = dbp_functions_list::get_post_dbp($dbp_id);
        $sql =  @$post->post_content['sql'];
        $primaries = [];
        if ( $sql != "") {
            $table_model = new Dbp_model();
            $table_model->prepare($sql);
            $primaries = $table_model->get_pirmaries(true);
            if ($name_request) {
                $list_settings = $post->post_content["list_setting"];
                foreach ($primaries as &$n) {
                    if (isset($list_settings[$n])) {
                        $n = $list_settings[$n]->name_request;
                    }
                }
                
            }
        }
        return $primaries;
    }

    /**
     * Ritornano i dati o il model di una lista
     * @todo ADD WHERE deve caricare gli alias dei campi che però dovrei trasformare in table_alias.field da schema! Questo per semplificare la lettura agli utenti!
     * @param [type] $dbp_id
     * @param string $return items|schema|model|schema+items
     * se diverso da null e diverso da '' 
     * ritorna direttamente il campo singolo senza array
     * @param array $add_where  [[op:'', column:'',value:'' ], ... ]
     * @param string $limit
     * @param string $order_field
     * @param string $order ASC|DESC
     * @param bool  $raw_data Se false elabora i dati estratti con list_setting, altrimenti restuisce i dati così come sono stati estratti dalla query
     * @return mixed
     * @todo aggiungere i risultati lavorati dalle impostazioni oppure no.
     */
    static function get_data($dbp_id, $return = "items", $add_where = null, $limit = null, $order_field = null, $order="ASC", $raw_data = false) {
        $return = strtolower($return);
        $post       = dbp_functions_list::get_post_dbp($dbp_id);
        $sql =  @$post->post_content['sql'];
        if ( $sql != "") {
            $table_model = new Dbp_model();
            $table_model->prepare($sql);
            
            if ($add_where != null) {
                $table_model->list_add_where($add_where);
            }
            if ($limit != null) {
                $table_model->list_add_limit(0, $limit);
            }
            if ($order_field != null) {
                $table_model->list_add_order($order_field, $order);
            }
            $table_model->add_primary_ids();
            $table_model->get_list();
            // prevengo l'htmlentities e il substr del testo.
            if (!$raw_data) {
                $table_model->update_items_with_setting($post, false, -1);
            }
            $items = $table_model->items;
            if (is_countable($items) && $table_model->last_error == "") {
                //items|schema|model|schema+items
                if ($return == 'items') {
                    array_shift($items);
                    return $items;
                } else if ($return == 'schema') {
                    $schema = array_shift($items);
                    return $schema;
                } else if ($return == 'model') {
                    return $table_model;
                } else {
                    return $items;
                }
               
            } else{
                return false;
            }
        }

    }
     
    /**
     * Data la lista estrae uno o più record a partire dagli ID e ritorna i dati grezzi!
     * Questi dati possono essere usati come base di partenza per salvare poi i dati.
     *
     * @param int $dbp_id
     * @param array|int $dbp_ids [pri_key=>val, ...] per un singolo ID perché una query può avere più primary Id a causa di left join per cui li accetto tutti. Se un integer invece lo associo al primo pri_id che mi ritorna. pri_key accetta sia il nome della colonna che il name_request
     * @return \stdClass|false se torna false non bisogna esegure il template engine
     */
    static function get_detail($dbp_id, $dbp_ids) {
        if (!class_exists('dbp_functions_list')) {
            dbp_fn::require_init();
        }
        //var_dump($dbp_ids);
        $dbp_post = dbp_functions_list::get_post_dbp($dbp_id);
        // qui ci devono essere tutte le chiavi primarie!
        $name_requests = self::get_primaries_id($dbp_id);
        $where = [];
        $columns = Dbp::get_ur_list_columns($dbp_id);
        if (is_string($dbp_ids)) {
             //provo a vedere se è una stringa
             $temp = Dbp_fn::ids_url_decode($dbp_ids);
             if (is_array($temp)) {
                $dbp_ids = $temp;
             }
        }
        if (is_array($dbp_ids)) {
            foreach ($name_requests as $name_request) {
                $field_setting = $dbp_post->post_content["list_setting"][$columns[$name_request]];
                $query_var = "";
                if (array_key_exists($name_request, $dbp_ids))  {
                    // name_request
                 
                    $query_var = $dbp_ids[$name_request];
                } else if (array_key_exists($columns[$name_request], $dbp_ids))  {
                    // nome della colonna
                 
                    $query_var = $dbp_ids[$columns[$name_request]];
                }
                if (absint($query_var) > 0) {
                    $where[] = ['op' => '=', 'column' => $field_setting->mysql_name, 'value' => esc_sql(absint($query_var)) ];
                }
            }
        } else {
            $name_request = reset($name_requests);
            $field_setting = $dbp_post->post_content["list_setting"][$columns[$name_request]];
            if (absint($dbp_ids) > 0) {
                $where[] = ['op' => '=', 'column' => $field_setting->mysql_name, 'value' => esc_sql(absint($dbp_ids)) ];
            }
        }
        if (count($where) == 0) {
            return false;
        }
        $results = Dbp::get_data($dbp_id, 'items', $where, 1, null, 'ASC', true);
        self::$frontend_template_engine_data[$dbp_id] = $results;
        if (is_array($results) && count($results) == 1) {
          
            return reset($results);
        } else {
            return false;
        }
        return false;
    }



    /**
     * ritorna la struttura della classe get_form per il salvataggio dei dati
     * @return array
     */
    static function get_save_data_structure($dbp_id) {
        if (!class_exists('dbp_class_form')) {
            dbp_fn::require_init();
        }
    
        $form = new Dbp_class_form($dbp_id);
    
        list($settings, $_) = $form->get_form();
        // TODO è un misto tra setting e schema della query perché poi il salvataggio è fatto dallo schema!
        return $settings;
    }

    /**
     * Salva i dati a partire da un ID o una query. 
     * Per fare l'update devono essere inserite le chiavi primarie
     * @param String $dbp_id è l'id della lista, ma accetta anche una stringa con una query di select
     * @param Array $data i Dati da aggiornare hanno la stessa struttura della query del select!
     * @param Boolean $use_wp_fn Se usare le funzioni di wordpress 
     * wp_update_post & wp_update_user quando si aggiornano/creano utenti e post
     * @return array
     */
    static function save_data($dbp_id, $data, $use_wp_fn = true) {
        if (!class_exists('dbp_class_form')) {
            dbp_fn::require_init();
        }
    
        $form = new Dbp_class_form($dbp_id);
        if (is_a($data, 'stdClass')) {
            $data = [$data];
        }
        $result = $form->save_data($data, $use_wp_fn);
        return $result;
    }
    
}