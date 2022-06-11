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
     * @param nteger $post_id
     * @param Boolena $ajax Se stampare i filtri e la form che la raggruppa oppure no (true)
     */
    static function get_list($post_id, $ajax = false) {
        $post        = Dbt_functions_list::get_post_dbt($post_id);
        $table_model = Dbt_functions_list::get_model_from_list_params($post->post_content);
        $extra_params =  Dbt_functions_list::get_extra_params_from_list_params(@$post->post_content['sql_filter']);
        if ($table_model) {
            PinaCode::set_var('global.dbt_filter_path', "dbt".$post_id);
            PinaCode::set_var('global.dbt_id', $post_id);
            Dbt_functions_list::add_frontend_request_filter_to_model($table_model, $post->post_content , $post_id);
            $table_model->get_list();
            $primaries = $table_model->get_pirmaries();
            //var_dump($table_model->items);
            PinaCode::set_var('global.limit', $table_model->limit);
            PinaCode::set_var('global.limit_start', $table_model->limit_start);
            $total_row = $table_model->get_count();
            //var_dump ($post->post_content["frontend_view"]);
            $frontend_setting = @$post->post_content["frontend_view"];
            if (is_array($frontend_setting) && @$frontend_setting['if_textarea'] != "" && @$frontend_setting['checkif'] == 1 ) {
                $ris = PinaCode::math_and_logic($frontend_setting['if_textarea']);
                if(!$ris)   {
                    return PinaCode::execute_shortcode($frontend_setting["content_else"]);
                }
            }
            $type = "TABLE_BASE";
            if (is_array($frontend_setting) && isset($frontend_setting['type'])) {
                $type = $frontend_setting['type'];
            } 
            $table_model->update_items_with_setting($post->post_content);
            if (is_array($table_model->items)) {
                $table_header = reset($table_model->items);
            } else {
                $table_header = [];
            }
            $table_model->check_for_filter();
            if ($type == "TABLE_BASE") {
                // TODO verificare se remove_hide_columns deve essere sostituita da  Dbt_fn::remove_hide_columns_in_row
                Dbt_fn::remove_hide_columns($table_model);
                if (isset($post->post_content['frontend_view']['detail_type']) && $post->post_content['frontend_view']['detail_type'] != "no") {
                    Dbt_fn::items_prepare_frontend_link($table_model, $post_id,$post->post_content);
                }
                $html_table   = new Dbt_html_table_frontend();
                $html_table->add_extra_params($extra_params);
                $html_table->add_list_id($post_id);
                if (isset($post->post_content['frontend_view'])) {
                    $html_table->add_frontend_view_setting($post->post_content['frontend_view']);
                }
                $html_table->add_no_result($frontend_setting['no_result_custom_text']);
              //  add_filter('dbt_frontend_table_cell', 'prepare_link',10, 5);
                return $html_table->template_render($table_model, $ajax); 
            } else {
                // EDITOR
                $result = [];
                $text = @$frontend_setting["content"];
                $items = $table_model->items;
                if (is_array($items)) {
                    $first_row = array_shift($items);
                    $first_row = array_map(function($el) {return $el->name;}, $first_row);
                    PinaCode::set_var('total_row', $total_row);
                    PinaCode::set_var('key',0);
                    $first_row = Dbt_fn::remove_hide_columns_in_row($table_header, $first_row);
                    PinaCode::set_var('data',  $first_row);
                } else {
                    PinaCode::set_var('total_row', 0);
                    PinaCode::set_var('key',0);
                    PinaCode::set_var('data',  []);
                }
                if (isset($frontend_setting["content_header"])) {
                    $result[] = PinaCode::execute_shortcode($frontend_setting["content_header"]);
                }
                //var_dump ($first_row);
                if (is_array($items) && $text != "") {
                    foreach ($items as $key=> $item) {
                        PinaCode::set_var('primaries', Dbt_fn::data_primaries_values( $primaries, $table_header, $item));
                        PinaCode::set_var('key', ($key+1));
                        $item = Dbt_fn::remove_hide_columns_in_row($table_header, $item);
                        PinaCode::set_var('data', $item);
                        $result[] = PinaCode::execute_shortcode($text);
                    }
                }
                PinaCode::set_var('data', []);
                if (isset($frontend_setting["content_footer"])) {
                    $result[] = PinaCode::execute_shortcode($frontend_setting["content_footer"]);
                }
                return implode("",$result);
            }
        }
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
     *
     * @param int $dbt_id
     * @return array [table=>primary_name, ]
     */
    static function get_primaries_id($dbt_id) {
        $post  = Dbt_functions_list::get_post_dbt($dbt_id);
        $sql =  @$post->post_content['sql'];
        $primaries = [];
        if ( $sql != "") {
            $table_model = new Dbt_model();
            $table_model->prepare($sql);
           $primaries = $table_model->get_pirmaries(true);
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
            $table_model->update_items_with_setting($post->post_content);
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
     * Passato il post_id ritorna eventuali dati estratti dal template engine
     *
     * @param int $dbt_id
     * @param array|int $dbt_ids [pri_key=>val, ...] per un singolo ID perché una query può avere più primary Id a causa di left join per cui li accetto tutti. Se un integer invece lo associo al primo pri_id che mi ritorna.
     * @return \stdClass|false se torna false non bisogna esegure il template engine
     */
    static function get_data_from_id($dbt_id, $dbt_ids) {
        if (!class_exists('Dbt_functions_list')) {
            Dbt_fn::require_init();
        }
        $dbt_post = Dbt_functions_list::get_post_dbt($dbt_id);
      
        $name_requests = self::get_primaries_id($dbt_id);
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
     * Salva i dati 
     * @return array
     */
    static function save_data($dbt_id, $data) {
        $form = new Dbt_class_form($dbt_id);
        $result = $form->save_Data($data);
        return $result;
    }
    
}