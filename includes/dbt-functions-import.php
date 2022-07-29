<?php 

/**
 * Tutte le funzioni che servono per gestire le importazioni

 *
 * @package    Database table
 * @subpackage Database table/includes
 */

namespace DatabaseTables;


class Dbt_fn_import {

    /**
     * Converte i dati della form dell'import csv in un array usabile
     * @param  Array $import_tables [uniqid:table_name, ...]
     * @param Array $import_field [uniqid:[field,field, ...], ...]
     * @param Array $import_csv_column [uniqid:[field,field, ...], ...]
     * @return Array [uniqid:{table:String, fields:[]}, ...]
     */
    static function convert_csv_data_request_to_vars($import_tables, $import_field, $import_csv_column) {
        $table_insert = [];
        foreach ($import_tables as $uniqid_key=>$table) {
			$temp_fields = [];
			foreach ($import_field[$uniqid_key] as $if_key => $ifd) {
				$temp_fields[$ifd] = $import_csv_column[$uniqid_key][$if_key];
			}
			$table_insert[$uniqid_key] = (object)['table'=>$table,'fields'=>$temp_fields];
		}
        return $table_insert;
    }

    /**
     * Estrae gli id da un csv a partire dalla primary id della tabella su cui importare i dati
     * @param  Array $csv_items 
     * @param String $primary_key
     * @param Array $fields è i fields risultanti da convert_csv_data_request_to_vars
     * @return Array
     */
    static function get_ids($csv_items, $primary_key, $fields) {
        $ids = [];
        if (array_key_exists($primary_key,$fields )) {
            $csv_pri = $fields[$primary_key];
            foreach ($csv_items as $item) {
                PinaCode::set_var('item', $item) ;
                $val = PinaCode::execute_shortcode(stripslashes($csv_pri));
                if ($val > 0) {
                    $ids[] = "'".esc_sql($val)."'";
                }
                
            }
        }
        $ids = array_unique($ids);
        return $ids;
    }

    /**
     * Crea una tabella temporanea e la popola
     * @return String|Boolean
     */
    static function create_temporaly_table_from($ti, $csv_items, $primary_key) {
        global $wpdb;
        $table_temp = substr(Dbt_fn::clean_string($ti->table),0,57)."__temp";
      
     
        $r = $wpdb->query('CREATE TEMPORARY TABLE IF NOT EXISTS `'.esc_sql($table_temp).'` LIKE `'.esc_sql($ti->table).'`;');       
        $ids = Dbt_fn_import::get_ids($csv_items, $primary_key, $ti->fields);
        $ids[] = $wpdb->get_var('SELECT `'.esc_sql($primary_key).'` FROM `'.esc_sql($ti->table).'` ORDER BY `'.esc_sql($primary_key).'` DESC LIMIT 1');

        if ($r) {
            if (count ($ids) > 0) {     
                $wpdb->query('INSERT INTO `'.esc_sql($table_temp).'` SELECT * FROM `'.esc_sql($ti->table).'` WHERE `'.esc_sql($primary_key).'` IN ('.implode(",", $ids).');');
            }
            return $table_temp ;
        } else {
            return false;
        }
    }

    /**
     * Verifica se un record è da inserire oppure da aggiornare e lo inserisce o aggiorna se e solo se deve essere aggiornato/inserito un solo record
     * In caso che il where ritorni più di un record allora la funzione non esegue nulla!
     * @param String $table
     * @param Array $data
     * @param String $primary  (e autoincrement)
     * @return Array ['row':Object,'result':Boolean,'error':String]
     */
    static function wpdb_replace($table, $data, $primary_key) {
        global $wpdb;
        $result = false;
        $sql_where = '';
        $exist_record = [];
        $old_row = [];
        $query = "";
        $error = "";
        $new_row = [];
        $where = [];
        $action = "";
        $load_id = 0;
        foreach ($data as $field=>$value) {
            if ($field == $primary_key) {
                $sql_where = "`".esc_sql($primary_key)."` = '".esc_sql($value)."'";
                $where[$primary_key] = $value;
            } else {
                $update[$field] = $value;
            }
        }       
        $ris = $wpdb->get_results('SELECT * FROM `'.esc_sql($table).'`');
        if ($sql_where != "") {
            $exist_record = $wpdb->get_results('SELECT * FROM `'.esc_sql($table).'` WHERE '.$sql_where." LIMIT 10");
        }
        if (count($exist_record) == 1) {
            // https://core.trac.wordpress.org/ticket/32315 se i valori sono più lunghi della query ritorna sbaglia gli errori!
            $result = $wpdb->update($table, $update, $where);
            $query = $wpdb->last_query;
            $error = $wpdb->last_error ;
            $load_id = reset($where);
            $old_row = reset($exist_record);
            $action = "update";
        } else if (count($exist_record) == 0) {
            $old_row = [];
            // https://core.trac.wordpress.org/ticket/32315 
            $result = $wpdb->insert($table, $data);
            $query = $wpdb->last_query;
            $error = $wpdb->last_error ;
            $load_id = $wpdb->insert_id;
            $action = "insert";

        } else {
           return ['old_row'=>'', 'row'=>'','result'=>false, 'error'=> __('The query does not return a unique value', 'database_tables'), 'action'=>'' ] ;
        }
        if ( $load_id > 0) {
            $new_row = $wpdb->get_row('SELECT * FROM `'.esc_sql($table).'` WHERE `'.esc_sql($primary_key).'` = \''.esc_sql( $load_id )."'");
        }
        return ['old_row'=>$old_row,  'row'=>$new_row, 'data'=>$data, 'result'=>$result, 'sql'=> $query,  'error'=>   $error, 'id'=>$load_id, 'action'=> $action ] ;
       
    }

}