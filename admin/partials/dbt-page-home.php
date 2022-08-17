<?php
/**
 * Quando carico il plugin e non c'è nessun parametro carico questa pagina
 *
 * @package    database-table
 * @subpackage bulk-image-resizer/admin
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="wrap">
    <div id="dbt_container" class="dbt-grid-container">
       
        <div class="dbt-column-content">
            <div class="dbt-content-header">
                <?php require(dirname(__FILE__).'/dbt-partial-tabs.php'); ?>
            </div>
            <div class="dbt-content-table js-id-dbt-content" >
                <div class="dbt-content-margin">
                    <h1>Easy Database tables</h1>
                    <hr>
                    <div class="dbt-home-grid">
                        <div class="dbt-form-row-column">
                            <div class="dbt-submit" style="float:right; margin-top:.7rem" onclick="dbt_create_list_show_form(false)"><?php _e('CREATE NEW LIST'); ?></div>
                            
                            <h3>LAST LIST</h3>
                            <div style="clear:both;height:1px">&nbsp;</div>
                            <a href="<?php echo admin_url("admin.php?page=dbt_list"); ?>"><?php _e('Show all List ', 'database_tables'); ?></a>
                            <?php $latest_list = (Dbt_fn::get_latest_list());
                            foreach ($latest_list as $list) :
                                ?>
                                <div class="dbt_home_card_list">
                                    <h3><a href="<?php echo admin_url("admin.php?page=dbt_list&section=list-browse&dbt_id=".$list->ID); ?>"><?php echo $list->post_title; ?></a></h3>
                                    <div class=""><?php echo $list->description; ?></div>
                                    <div class="dbt-home-query-desc js-dbt-mysql-query-text"><?php echo $list->post_content['sql']; ?></div>
                                </div>
                            <?php  endforeach;  ?>

                        </div>
                        <div class="dbt-form-row-column">
                            <h3>SQL QUERY</h3>
                            <form id="table_sql_home" method="post" action="<?php echo admin_url("admin.php?page=database_tables"); ?>">
                                <input type="hidden" name="section" value="table-browse">
                                <input type="hidden" name="action_query" value="custom_query">
                                <?php 
                                $table_model = new Dbt_model(); 
                                Dbt_html_sql::render_sql_from($table_model, true); ?>
                            </form>
                           
                            <div class="dbt-home-check">
                                <h4>Info:</h4>
                                <?php 
                                    if ($info_db != '') {
                                        echo '<div class="dtf-color-info"> &bull; '.$info_db.'</div>'; 
                                    }
                                    if ($database_name != '') {
                                        echo '<div class="dtf-color-info">&bull; Database Name: <b>' . $database_name . '</b></div>';
                                    }
                                    echo '<div class="dtf-color-info">&bull; Database Size: <b>' . $database_size . '</b></div>';
                                    ?>
                               
                            <?php 
                                if (DB_USER == "root") {
                                    ?><div class="dtf-color-error">&bull; <?php _e('Using the mysql root user to connect to the database is not a good idea.','database_tables'); ?></div> <?php 
                                }
                                if (count($processlist) > 0) {
                                    ?><div class="dtf-color-warning">
                                    Some queries are taking longer than expected to execute. <br>   
                                    <?php
                                    foreach ($processlist as $pl) {
                                        echo "<p>&bull; ".$pl."</p>";
                                    }
                                    ?></div><?php 
                                }
                                if (is_array($permission_list)) {
                                    if (count($permission_list) > 0) {
                                        ?><div class="dtf-color-error">&bull; <?php echo  sprintf(__('The mysql user does not have enough permissions to manage this plugin. Missing: <b>%s</b>', 'database_tables'), implode(", ", $permission_list)); ?></div> <?php 
                                    } else {
                                        ?><div class="dtf-color-info">&bull; <?php _e('The permissions of the mysql user are correct','database_tables'); ?></div> <?php 
                                    }
                                }
                                $max_input_vars = Dbt_fn::get_max_input_vars();
                                if ($max_input_vars  < 3000) {
                                    if ($max_input_vars >= 1000) {
                                        ?><div class="dtf-color-warning">&bull; <?php _e('the max_input_vars (php.ini) value is sufficient, but if you can increase it to at least 5000 it would be better.','database_tables'); ?></div> <?php 
                                    } else {
                                        ?><div class="dtf-color-error">&bull; <?php _e('init: the max_input_vars (php.ini) value is very low','database_tables'); ?></div> <?php   
                                    }
                                } else {
                                    ?><div class="dtf-color-info">&bull; <?php _e('max_input_vars is ok','database_tables'); ?></div> <?php 
                                }
                                if ($is_writable_dir) {
                                    ?><div class="dtf-color-info">&bull; <?php _e('Log dir is writable','database_tables'); ?></div> <?php 
                                } else {
                                    ?><div class="dtf-color-error">&bull; <?php echo  sprintf(__('Log dir (%s) is NOT writable','database_tables'), $dir); ?></div> <?php 
                                }
                            ?>
                            </div>
                        </div>
                    </div>
                </div>

              
            </div>
        </div>
        <div class="dbt-column-tables-list" id="dbt_column_sidebar">
            <?php require (dirname(__FILE__)."/dbt-partial-sidebar.php"); ?>
        </div>
        <div id="dbt_sidebar_popup" class="dbt-sidebar-popup">
            <div id="dbt_dbp_title" class="dbt-dbp-title">
                <div id="dbt_dbp_close" class="dbt-dbp-close" onclick="dbt_close_sidebar_popup()">&times;</div>
            </div>
            <div id="dbt_dbp_loader" ><div class="dbt-sidebar-loading"><div  class="dbt-spin-loader"></div></div></div>
            <div id="dbt_dbp_content" class="dbt-dbp-content"></div>
        </div>
       
    </div>
</div>

<?php require (dirname(__FILE__)."/../js/database-table-footer-script.php"); ?>