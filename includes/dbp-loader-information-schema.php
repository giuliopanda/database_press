<?php
/**
 * Gestisco il filtri e hook per la schermata information-schema
 * 
 *
 * @package    database-press
 * @subpackage database-press/includes
 */
namespace DatabasePress;

class  Dbp_loader_information_schema {
    public function __construct() {
		// Questa una chiamata crea una tabella e ridirige alla struttura
		add_action( 'admin_post_dbp_create_table', [$this, 'create_table']);	
		// svuota una tabella
		add_action( 'admin_post_dbp_empty_table', [$this, 'empty_table']);	
		// elimina una tabella
		add_action( 'admin_post_dbp_drop_table', [$this, 'drop_table']);	
		// elimina una tabella
		add_action( 'wp_ajax_dbp_dump_table', [$this, 'dump_table']);	
        // Scarica il file sql
		add_action( 'admin_post_dbp_download_dump', [$this, 'dbp_download_dump']);
	}
    /**
     * Creo la tabella
     */
    public function create_table() {
        global $wpdb;
        dbp_fn::require_init();
        //TODO IN LAVORAZIONE
        if (!isset($_REQUEST['new_table'])) {
            wp_redirect( admin_url("admin.php?page=database_press&section=information-schema&msg=no_created_table"));
        }
        $use_prefix = (isset($_REQUEST['use_prefix']) && $_REQUEST['use_prefix'] == 1);
        $table_model = new Dbp_model_structure($_REQUEST['new_table']);
        $table_model->change_unique_table_name($_REQUEST['new_table'], $use_prefix);
        $table_model->insert_column('id', 'INT', 11,  "", true, false, true, false);
       
        $sql = $table_model->get_create_sql();
        $result = $wpdb->query($sql);
        if (is_wp_error($result) || !empty($wpdb->last_error)) {
            wp_redirect( admin_url("admin.php?page=database_press&section=information-schema&msg=no_created_table"));
        } else {
            dbp_fn::update_dbp_option_table_status($table_model->get_table_name(), 'DRAFT');
        }
        $link =  admin_url("admin.php?page=database_press&section=table-structure&action=structure-edit&table=".$table_model->get_table_name()."&msg=created_table");
        wp_redirect($link);
    }
    /**
     * Svuota la tabella
     */
    public function empty_table() {
        global $wpdb;
        dbp_fn::require_init();
        //TODO IN LAVORAZIONE
        if (!isset($_REQUEST['table'])) {
            wp_redirect( admin_url("admin.php?page=database_press&section=information-schema&msg=no_table_selected"));
        }
        $table_options = dbp_fn::get_dbp_option_table($_REQUEST['table']);
        if ($table_options['status'] == "DRAFT") {
            if ($wpdb->query("TRUNCATE TABLE `".esc_sql($_REQUEST['table'])."`")) {
                wp_redirect( admin_url("admin.php?page=database_press&section=information-schema&msg=empty_table_ok"));
            } else {
                wp_redirect( admin_url("admin.php?page=database_press&section=information-schema&msg=empty_table_mysql_error"));
            }
        } else {
            wp_redirect( admin_url("admin.php?page=database_press&section=information-schema&msg=no_allow_empty_table"));
        }
    }
    /**
     * Elimina la tabella
     */
    public function drop_table() {
        global $wpdb;
        dbp_fn::require_init();
        //TODO IN LAVORAZIONE
        if (!isset($_REQUEST['table'])) {
            wp_redirect( admin_url("admin.php?page=database_press&section=information-schema&msg=no_table_selected"));
        }
        $table_options = dbp_fn::get_dbp_option_table($_REQUEST['table']);
        if ($table_options['status'] == "DRAFT") {
            if ($wpdb->query("DROP TABLE `".esc_sql($_REQUEST['table'])."`")) {
              //  delete_option('dbp_'.$_REQUEST['table']);
                dbp_fn::delete_dbp_option_table_status($_REQUEST['table']);
                wp_redirect( admin_url("admin.php?page=database_press&section=information-schema&msg=drop_table_ok"));
            } else {
                wp_redirect( admin_url("admin.php?page=database_press&section=information-schema&msg=drop_table_mysql_error"));
            }
        } else {
            wp_redirect( admin_url("admin.php?page=database_press&section=information-schema&msg=no_allow_drop_table"));
        }
    }

     /**
     * dump di una tabella
     */
    public function dump_table() {
        global $wpdb;
        dbp_fn::require_init();
        $table = $_REQUEST['table'];
		$sql = 'SHOW CREATE TABLE `'.esc_sql($table).'`';
		$result = $wpdb->get_row($sql, 'ARRAY_A');
        
        if (isset($_REQUEST['limit_start'])) {
            $limit_start = $_REQUEST['limit_start'];
        } else {
            $limit_start = 0;
        }
        $temp_file = new Dbp_temporaly_files();
        if ($limit_start == 0 ) {
            if (isset($result['Create Table'])) {
                $table_structure = $result['Create Table'];
            } else {
                return '';
            }
           
            $filename = $temp_file->append($table_structure.";" . PHP_EOL . PHP_EOL);
        } else {
            $filename = $_REQUEST['filename'];
        }
        $sql = 'SELECT count(*) as tot FROM  `'.addslashes($table).'`';
        $tot = $wpdb->get_var($sql);

        $sql = 'SELECT * FROM  `'.addslashes($table).'` LIMIT '.absint($limit_start).', 5000';
        $rows = $wpdb->get_results($sql);
        $insert_rows = [];
        if (is_countable($rows)) {
            foreach ($rows as $row) {
                $insert_values_key = $insert_values_val = [];
                foreach ($row as $key=>$value) {
                    $insert_values_key[] = '`'.$key.'`'; 
                    $insert_values_val[] = "'".addslashes($value)."'";
                }
                $insert_rows[] = 'INSERT INTO `'.addslashes($table).'` ('.
                implode(", ", $insert_values_key).
                ') VALUES ('.implode(",",$insert_values_val).');' ;
            }
            $temp_file->append(implode(PHP_EOL, $insert_rows), $filename);
        }
      //  print " FILE NAME ". $filename." ";
        $link = admin_url('admin-post.php?section=information-schema&action=dbp_download_dump&fnid=' . $filename."&table=".$table);  
        $ris  = ['filename' => $filename, 'download'=>$link, 'tot'=>absint($tot), 'exec'=>count($rows), 'limit_start'=>absint($limit_start), 'table'=> $table , 'div_id'=> $_REQUEST['div_id']];
        wp_send_json($ris);
		die();
    }

    /**
	 * Scarica il dump
	 */
	public function dbp_download_dump() {
		dbp_fn::require_init();
       
		$temporaly = new Dbp_temporaly_files();
		$fnid = dbp_fn::sanitaze_request('fnid','');
		$table = dbp_fn::sanitaze_request('table','');
		$data = $temporaly->read($fnid);

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename = ".$table."_".date('Ymd_His').".sql");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        
        // Read the file
        echo $data;
        die;


	}
}