<?php
/**
* funzioni pubbliche
* get_list
*/
namespace DatabaseTables;

class Dbt
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
        Dbt_fn::require_init();

        $custom_data = apply_filters('dbt_frontend_get_list', '', $post_id);
        if ($custom_data != "") {
            return $custom_data;
        }
        $ori_params =  PinaCode::get_var('params');
        // se esiste un div_id allora verifico se esistono i params specifici per quella lista
        //if (isset($_REQUEST['dbt_div_id'])) {   
        //    $spec_params =  PinaCode::has_var('params.'.$_REQUEST['dbt_div_id']);
        //    $params = PinaCode::get_var('params.'.$_REQUEST['dbt_div_id']);
        //    PinaCode::set_var('params', $params);
        //}
        PinaCode::set_var('params', $params);
        $show_trappings = true;
        if (Dbt_fn::is_form_open()) {
            // la form che uso di solito per gestire le tabelle è stata già aperta e non chiusa
            // da un'altra quindi si sta renderizzando una lista dentro un'altra lista
            // per cui disabilito ordinamento, paginazione e ricerca!!
            $show_trappings = false;
        }
        Dbt_fn::set_open_form();
        $list =  new Dbt_render_list($post_id, null, $prefix);
        //print ("REQUEST['dbt_div_id'] ");
        //var_dump($_REQUEST);
        if ($only_table || !$show_trappings) {
            //print " OK ";   
            $list->hide_div_container();
            // setto il div
            if (isset($_REQUEST['dbt_div_id'])) {
                $list->set_uniqid($_REQUEST['dbt_div_id']);
            }
        }
		//print ("RIS : ".$list->uniqid_div."| ");
        if (!$show_trappings) {
            // Faccio finta che la form è stata già create
            $list->block_opened = true;
            $list->frontend_view_setting['table_sort'] = false;
        }
        if ( $list->get_frontend_view('type', 'TABLE_BASE') == "TABLE_BASE") {
            ob_start();
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
                PinaCode::set_var('total_row', $list->table_model->get_count());
                PinaCode::set_var('key',0);
                $first_row = Dbt_fn::remove_hide_columns_in_row($table_header, $first_row);
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
                    //PinaCode::set_var('primaries', Dbt_fn::data_primaries_values( $primaries, $table_header, $item));
                    PinaCode::set_var('key', ($key+1));
                    //$item = Dbt_fn::remove_hide_columns_in_row($table_header, $item);
                    PinaCode::set_var('data', $item);
                    $result[] = PinaCode::execute_shortcode($text);
                }
            }
            PinaCode::set_var('data', []);
            $result[] = PinaCode::execute_shortcode($list->get_frontend_view('content_footer'));
           
            if ($list->get_frontend_view('table_update') != "none") {
                ob_start() ;
                $list->end();
                $result[] = ob_get_clean();
            }
            return implode("",$result);
        }
        if ($show_trappings) {
            Dbt_fn::set_close_form();
        }
        PinaCode::set_var('params', $ori_params);
    }

    /**
     * Ritorna la classe che genera la tabella
     * return \Dbt_render_list;
     */
    static function render($dbt_id, $mode = null) {
        Dbt_fn::require_init();
        return new Dbt_render_list($dbt_id, $mode);
    }

    /**
     * Calcola il totale dei record dei dati estratti da una lista
     * @param number $post_id l'id della lista
     * @param boolean $filter se aggiungere i filtri oppure no alla query
     * @return int -1 se non riesce a fare il conto
     */
    static function get_total($post_id, $filter = false) {
        $post        = Dbt_functions_list::get_post_dbt($post_id);
        if ($filter) {
            $table_model = Dbt_functions_list::get_model_from_list_params($post->post_content);
        } else {
            $table_model = new Dbt_model();
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
     * Carica tutte le liste dbt
     * @return array 
     * ```json
     * {'id':'title','id':'...'}
     * ```
     */
    static function get_lists_names() {
        global $wpdb;
        $query_lists = $wpdb->get_results('SELECT ID, post_title FROM '.$wpdb->posts.' WHERE post_type ="dbt_list" AND `post_status` = "publish" ORDER BY post_title');
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
     * @param int $dbt_id 
     * @param bool searchable 
     * @param bool extend 
     * @return DbtDs_list_setting[]|array
     * SE extend è false
     * ```json
     * {'field_name':'field_title','field_name':'...'} 
     * ```
     * Se extend è true DbtDs_list_setting[]
     */
    static function get_list_columns($dbt_id, $searchable = true, $extend = false) {
        $post  = Dbt_functions_list::get_post_dbt($dbt_id);
        /**
         * @var DbtDs_list_setting[] $lists
         */
        $lists = [];
        /**
         * @var DbtDs_list_setting[] $list_setting
         */
        $list_setting = [];
        if (!isset($post->post_content) ) {
            return [];
        }
        if (count($post->post_content['list_setting']) == 0 ) {
            $sql = @$post->post_content['sql'];
            if ($sql != "") {
                $table_model = new Dbt_model();
                $table_model->prepare($sql);
                $table_model->list_add_limit(0, 1);
                if ($table_model->sql_type() == "select") {
                    $list_setting = Dbt_functions_list::get_list_structure_config($table_model, []);
                    
                } else {
                    return [];  
                }
            } else {
                return [];
            }
        } else {
            $list_setting = $post->post_content['list_setting'];
            
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
     * @param int $dbt_id 
     * @param bool searchable 
     * @param bool extend 
     * @return DbtDs_list_setting[]|array
     * SE extend è false
     * ```json
     * {'field_name':'field_title','field_name':'...'} 
     * ```
     * Se extend è true DbtDs_list_setting[]
     */
    static function get_ur_list_columns($dbt_id) {
        $list_settings = self::get_list_columns($dbt_id, true, true);
        $lists = [];
        foreach ($list_settings as $list_setting) {
            $lists[$list_setting->name_request] = $list_setting->name;
        }
        return $lists;
    }

    /**
     * Ritorna l'elenco delle chiavi primarie di una lista. I campi estratti sono gli alias!
     * TODO NON funziona, vorrei che fosse salvato nella lista comunque sto lavorando su model->add_primary_ids
     * @param int $dbt_id
     * @return array [table=>primary_name, ]
     */
    static function get_primaries_id($dbt_id, $name_request = true) {
        $post  = Dbt_functions_list::get_post_dbt($dbt_id);
        $sql =  @$post->post_content['sql'];
        $primaries = [];
        if ( $sql != "") {
            $table_model = new Dbt_model();
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
     * @param [type] $dbt_id
     * @param string $return items|schema|model|schema+items
     * se diverso da null e diverso da '' 
     * ritorna direttamente il campo singolo senza array
     * @param array $add_where  [[op:'', column:'',value:'' ], ... ]
     * @param string $limit
     * @param string $order_field
     * @param string $order ASC|DESC
     * @return mixed
     * @todo aggiungere i risultati lavorati dalle impostazioni oppure no.
     */
    static function get_data($dbt_id, $return = "items", $add_where = null, $limit = null, $order_field = null, $order="ASC") {
        $return = strtolower($return);
        $post       = Dbt_functions_list::get_post_dbt($dbt_id);
        $sql =  @$post->post_content['sql'];
        if ( $sql != "") {
            $table_model = new Dbt_model();
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
            $table_model->update_items_with_setting($post->post_content, false, -1);
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
     * Data la lista estrae uno o più record a partire dagli ID
     *
     * @param int $dbt_id
     * @param array|int $dbt_ids [pri_key=>val, ...] per un singolo ID perché una query può avere più primary Id a causa di left join per cui li accetto tutti. Se un integer invece lo associo al primo pri_id che mi ritorna.
     * @return \stdClass|false se torna false non bisogna esegure il template engine
     */
    static function get_data_by_id($dbt_id, $dbt_ids) {
        if (!class_exists('Dbt_functions_list')) {
            Dbt_fn::require_init();
        }
        $dbt_post = Dbt_functions_list::get_post_dbt($dbt_id);
        // qui ci devono essere tutte le chiavi primarie!
        $name_requests = self::get_primaries_id($dbt_id);
        //var_dump ($name_requests);
        $where = [];
        $columns = Dbt::get_ur_list_columns($dbt_id);
        if (is_array($dbt_ids)) {
            foreach ($name_requests as $name_request) {
                $field_setting = $dbt_post->post_content["list_setting"][$columns[$name_request]];
                $query_var = "";
                if (array_key_exists($name_request, $dbt_ids))  {
                    $query_var = $dbt_ids[$name_request];
                }
                $where[] = ['op' => '=', 'column' => $field_setting->mysql_name, 'value' => esc_sql($query_var) ];
            }
        } else {
            $name_request = reset($name_requests);
            $field_setting = $dbt_post->post_content["list_setting"][$columns[$name_request]];
            $where[] = ['op' => '=', 'column' => $field_setting->mysql_name, 'value' => esc_sql($dbt_ids) ];
        }
        //print "where: ";
        //var_dump ($where);
        $results = Dbt::get_data($dbt_id, 'items', $where, 1);
        self::$frontend_template_engine_data[$dbt_id] = $results;
        if (is_array($results) && count($results) == 1) {
          
            return reset($results);
        } else {
            return new \StdClass();
        }
        return false;
    }

    /**
     * Estrae i dati grezzi di una lista per la modifica dei record
     *
     * @param int $dbt_id
     * @param array|int $dbt_ids [pri_key=>val, ...] per un singolo ID perché una query può avere più primary Id a causa di left join per cui li accetto tutti. Se un integer invece lo associo al primo pri_id che mi ritorna.
     * @return array se torna false non bisogna esegure il template engine
     */
    static function get_form_data_by_id($dbt_id, $dbt_ids) {
        if (!class_exists('Dbt_class_form')) {
            Dbt_fn::require_init();
        }
        $form = new Dbt_class_form($dbt_id);
        if (is_array($dbt_ids)) {
            return  $form->get_data($dbt_ids);
        } else {
            $name_requests = self::get_primaries_id($dbt_id);
            $name_request = reset($name_requests);
            $dbt_post = Dbt_functions_list::get_post_dbt($dbt_id);
            $columns = Dbt::get_ur_list_columns($dbt_id);
            $field_setting = $dbt_post->post_content["list_setting"][$columns[$name_request]];
            $ids = [$field_setting->mysql_name => esc_sql($dbt_ids) ];
            return  $form->get_data($ids);
        }
    }

      /**
     * ritorna la struttura del salvataggio dei dati
     * @return array
     */
    static function get_save_data_structure($dbt_id) {
        if (!class_exists('Dbt_class_form')) {
            Dbt_fn::require_init();
        }
    
        $form = new Dbt_class_form($dbt_id);
    
        list($settings, $_) = $form->get_form();
        // TODO è un misto tra setting e schema della query perché poi il salvataggio è fatto dallo schema!
        return $settings;
    }

    /**
     * Salva i dati 
     * @return array
     */
    static function save_data($dbt_id, $data) {
        if (!class_exists('Dbt_class_form')) {
            Dbt_fn::require_init();
        }
    
        $form = new Dbt_class_form($dbt_id);
        if (is_a($data, 'stdClass')) {
            $data = [$data];
        }
        $result = $form->save_data($data);
        return $result;
    }
    
}