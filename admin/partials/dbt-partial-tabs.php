<?php 
namespace DatabaseTables;
if (!defined('WPINC')) die;

function dbt_partial_tabs() {
    $current_page = 'browse';
    $page = Dbt_fn::get_request('page', '');
    $section = Dbt_fn::get_request('section', 'home');
    $base_link = admin_url("admin.php?page=".$page); 

    if (substr($section, 0,4) == "list" || $page == "dbt_list") {
        $var_name = 'dbt_id';
        $dbt_id  = Dbt_fn::get_request('dbt_id', '');
        // table diventa l'id del post della lista
        if ($dbt_id == "") {
            $array_tabs = ['list-all' => 'Browse all lists'];
            $array_icons = ['list-all' => '<span class="dashicons dashicons-admin-site-alt3"></span>'];
        } else {
            $array_tabs = ['list-browse' => 'Browse the list',  'list-form' => 'Form', 'list-sql-edit' => 'Settings', 'list-structure' => 'List view formatting', 'list-setting'=>'Frontend'];
            $array_icons = ['list-browse' => '<span class="dashicons dashicons-visibility"></span>', 'list-structure' => '<span class="dashicons dashicons-editor-table"></span>','list-sql-edit' => '<span class="dashicons dashicons-edit-page"></span>', 'list-setting'=>'<span class="dashicons dashicons-admin-settings"></span>','list-form' =>'' ];
        }
    } else {
        $table  = Dbt_fn::get_request('table', '');
        $var_name = 'table';
        if ($table != "") {
            $array_tabs = ['table-browse' => 'Browse', 'table-structure' => 'Structure', 'table-sql' => 'SQL', 'table-import' => 'IMPORT'];
            $array_icons = ['table-browse' => '<span class="dashicons dashicons-visibility"></span>', 'table-structure' => '<span class="dashicons dashicons-editor-table"></span>', 'table-sql' => '<span class="dashicons dashicons-edit-page"></span>', 'table-import' => '<span class="dashicons dashicons-database-import"></span>'];
        } else {
        
            $array_tabs = ['information-schema' => 'Show tables', 'table-sql' => 'SQL', 'table-structure|structure-edit' => 'New Table', 'table-import' => 'IMPORT'];
            $array_icons = ['information-schema' => '<span class="dashicons dashicons-admin-site-alt3"></span>', 'table-structure' => '<span class="dashicons dashicons-editor-table"></span>', 'table-sql' => '<span class="dashicons dashicons-edit-page"></span>', 'table-import' => '<span class="dashicons dashicons-database-import"></span>'];
        }
    }
    ?>
    <div class="dbt-tabs-container">
        <?php foreach ($array_tabs as $key=>$value) : ?>
            <?php
            $action  = "";
            if (strpos($key, "|") != "") {
                $temp = explode("|", $key);
                $key = array_shift($temp);
                $action =  array_shift($temp);
            }
            if ( $$var_name != "" ) {
                $link = add_query_arg(['section' => $key, $var_name => $$var_name ], $base_link);
            } else {
                if ($key == "table-browse") continue;
                $link = add_query_arg(['section'=>$key ], $base_link);
            }
            if ($action != "") {
                $link = add_query_arg(['action' => $action], $link);
            } 
            if ($section == $key) : ?>
                <a href="<?php echo $link; ?>" class="dbt-tab dbt-tab-active">
                    <?php echo $array_icons[$key]; ?>
                    <?php _e($value, 'database-table'); ?>
                </a>
            <?php else :?>
                <a href="<?php echo $link; ?>" class="dbt-tab">
                    <?php echo $array_icons[$key]; ?>
                    <?php _e($value, 'database-table'); ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php 
}

dbt_partial_tabs();