<?php
namespace DatabasePress;

class  Dbp_model_information_schema {

    /**
     * @var Array $sort [field, order] 
     */
    public  $sort = [];
     /**
     * @var Int $total_items Il numero totale di elementi della query
     */
    public  $total_items = 0;
    /**
     * @var Array $items Il risultato della query La prima riga è composta dallo schema del risultato
     */
    public  $items = [];
    
    function get_list() {
        global $wpdb;
        $sql = 'SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = "%s" AND TABLE_TYPE = "BASE TABLE"';
        $info = $wpdb->get_results($wpdb->prepare($sql, $wpdb->dbname, ));
        $result = [];
        $tables_info = dbp_fn::get_all_dbp_options();
        
        foreach ($info as $row) {
            $table_options = dbp_fn::get_dbp_option_table($row->TABLE_NAME);
           // TABLE_NAME, ENGINE, TABLE_ROWS, CREATE_TIME, UPDATE_TIME, (DATA_LENGTH + INDEX_LENGTH) = size
            $size = size_format($row->DATA_LENGTH  + $row->INDEX_LENGTH , 2);
            $tables_info[$row->TABLE_NAME]['status'] = $table_options['status'];
           
            if ($table_options['status'] == "DRAFT") {
                $action = '<div class="btn-div-td">
                <a class="dbp-warning-link" href="'. admin_url('admin-post.php?page=database_press&section=information-schema&action=dbp_empty_table&table='.$row->TABLE_NAME).'" onClick="return confirm(\'Are you sure to empty table\');">EMPTY TABLE</a> | 
                <a class="dbp-warning-link" href="'.admin_url('admin-post.php?page=database_press&section=information-schema&action=dbp_drop_table&table='.$row->TABLE_NAME).'" onClick="return confirm(\'Are you sure to drop table\');">DELETE</a></div>';
            } else {
                $action = "";
            }
            $date = $row->UPDATE_TIME ;
            if ( $date == "") {
                $date = $row->CREATE_TIME;
            }
            $table = '<a href="'. add_query_arg(['section'=>'table-browse', 'table'=> $row->TABLE_NAME], admin_url("admin.php?page=database_press")).'">'.$row->TABLE_NAME.'</a>';
          
            $backup_action = '<div id="ex'.dbp_fn::get_uniqid().'" class="dbp_backup_block"><div class="button" onclick="dbp_get_backup(\''.esc_attr($row->TABLE_NAME).'\',0, jQuery(this).parent().prop(\'id\'))">Export SQL</div></div>';

            $result[$row->TABLE_NAME] = ['table' => $table, 'engine' => $row->ENGINE, 'rows' => $row->TABLE_ROWS, 'size'=>$size, 'collation' => $row->TABLE_COLLATION , 'updated_at' => $row->UPDATE_TIME, 'backup' =>$backup_action, 'status'=>$table_options['status'], 'action'=>$action];
       
        }
        $this->total_items = count ($result);
        $this->items = array_merge([(object)['table' => 'table', 'engine' => 'engine', 'rows' => 'rows', 'size'=>'size', 'collation' => 'collation', 'updated_at' => 'updated_at','backup'=>'backup', 'status'=>'status', 'action' => 'Action']], $result);
        return ($result);
    }

    function get_count() {
         return $this->total_items;
    }

}
