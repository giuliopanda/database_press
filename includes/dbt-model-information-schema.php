<?php
namespace DatabaseTables;

class Dbt_model_information_schema {

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
        $tables_info = Dbt_fn::get_all_dbt_options();
        
        foreach ($info as $row) {
         
           // TABLE_NAME, ENGINE, TABLE_ROWS, CREATE_TIME, UPDATE_TIME, (DATA_LENGTH + INDEX_LENGTH) = size
            $size = size_format($row->DATA_LENGTH  + $row->INDEX_LENGTH , 2);
            if (isset($tables_info[$row->TABLE_NAME]['status'])) {
                $status =  $tables_info[$row->TABLE_NAME]['status'];
            } else {
                $status = 'PUBLISH';
            }
            if ($status == "DRAFT") {
                $action = '<div class="btn-div-td">
                <a class="dbt-warning-link" href="'. admin_url('admin-post.php?page=database_tables&section=information-schema&action=dbt_empty_table&table='.$row->TABLE_NAME).'" onClick="return confirm(\'Are you sure to empty table\');">EMPTY TABLE</a> | 
                <a class="dbt-warning-link" href="'.admin_url('admin-post.php?page=database_tables&section=information-schema&action=dbt_drop_table&table='.$row->TABLE_NAME).'" onClick="return confirm(\'Are you sure to drop table\');">DELETE</a></div>';
            } else {
                $action = "";
            }
            $date = $row->UPDATE_TIME ;
            if ( $date == "") {
                $date = $row->CREATE_TIME;
            }
            $table = '<a href="'. add_query_arg(['section'=>'table-browse', 'table'=> $row->TABLE_NAME], admin_url("admin.php?page=database_tables")).'">'.$row->TABLE_NAME.'</a>';
          
            $backup_action = '<div id="ex'.Dbt_fn::get_uniqid().'" class="dbt_backup_block"><div class="button" onclick="dbt_get_backup(\''.esc_attr($row->TABLE_NAME).'\',0, jQuery(this).parent().prop(\'id\'))">Export SQL</div></div>';

            $result[$row->TABLE_NAME] = ['table' => $table, 'engine' => $row->ENGINE, 'rows' => $row->TABLE_ROWS, 'size'=>$size, 'collation' => $row->TABLE_COLLATION , 'updated_at' => $row->UPDATE_TIME, 'backup' =>$backup_action, 'status'=>$status, 'action'=>$action];
       
        }
        $this->total_items = count ($result);
        $this->items = array_merge([(object)['table' => 'table', 'engine' => 'engine', 'rows' => 'rows', 'size'=>'size', 'collation' => 'collation', 'updated_at' => 'updated_at','backup'=>'backup', 'status'=>'status', 'action' => 'Action']], $result);
        return ($result);
    }

    function get_count() {
         return $this->total_items;
    }

}
