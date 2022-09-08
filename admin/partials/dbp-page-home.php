<?php
/**
 * Quando carico il plugin e non c'è nessun parametro carico questa pagina
 *
 * @package    database-press
 */
namespace DatabasePress;
if (!defined('WPINC')) die;
?>
<div class="wrap">
    <div id="dbp_container" class="dbp-grid-container">
       
        <div class="dbp-column-content">
            <div class="dbp-content-header">
                <?php require(dirname(__FILE__).'/dbp-partial-tabs.php'); ?>
            </div>
            <div class="dbp-content-table js-id-dbp-content" >
                <div class="dbp-content-margin">
                    <h1>Database Press</h1>
                    <hr>
                    <div class="dbp-home-grid">
                        <div class="dbp-form-row-column">
                            <div class="dbp-submit" style="float:right; margin-top:.7rem" onclick="dbp_create_list_show_form(false)"><?php _e('CREATE NEW LIST'); ?></div>
                            <?php $latest_list = (dbp_fn::get_latest_list());
                            if ($latest_list == 0) {
                                ?><div class="home_page_info"><?php _e("Create your first list. You can try creating a simple structure like a list of cities.
                                Inside the documentation you will also find tutorials to learn how to use the plugin.", 'database_press'); ?></div><?php
                            } else {
                                ?>
                                <h3><?php _e('Latest lists', 'database_press'); ?></h3>
                                <div style="clear:both;height:1px">&nbsp;</div>
                                <a href="<?php echo admin_url("admin.php?page=dbp_list"); ?>"><?php _e('Show all List ', 'database_press'); ?></a>
                                <?php
                                foreach ($latest_list as $list) :
                                    ?>
                                    <div class="dbp_home_card_list">
                                        <h3><a href="<?php echo admin_url("admin.php?page=dbp_list&section=list-browse&dbp_id=".$list->ID); ?>"><?php echo $list->post_title; ?></a></h3>
                                        <div class=""><?php echo $list->description; ?></div>
                                    </div>
                                <?php  endforeach;  ?>
                                <?php 
                            }
                            ?>
                        </div>
                        <div class="dbp-form-row-column">
                            <h3>SQL QUERY</h3>
                            <form id="table_sql_home" method="post" action="<?php echo admin_url("admin.php?page=database_press"); ?>">
                                <input type="hidden" name="section" value="table-browse">
                                <input type="hidden" name="action_query" value="custom_query">
                                <?php 
                                $table_model = new Dbp_model(); 
                                dbp_html_sql::render_sql_from($table_model, true); ?>
                            </form>
                           
                            <div class="dbp-home-check">
                                <h4>Info:</h4>
                                <?php 
                                    if ($info_db != '') {
                                        echo '<div class="dbp-color-info"> &bull; '.$info_db.'</div>'; 
                                    }
                                    if ($database_name != '') {
                                        echo '<div class="dbp-color-info">&bull; Database Name: <b>' . $database_name . '</b></div>';
                                    }
                                    echo '<div class="dbp-color-info">&bull; Database Size: <b>' . $database_size . '</b></div>';
                                    ?>
                               
                            <?php 
                                if (DB_USER == "root") {
                                    ?><div class="dbp-color-error">&bull; <?php _e('Using the mysql root user to connect to the database is not a good idea.','database_press'); ?></div> <?php 
                                }
                                if (count($processlist) > 0) {
                                    ?><div class="dbp-color-warning">
                                    Some queries are taking longer than expected to execute. <br>   
                                    <?php
                                    foreach ($processlist as $pl) {
                                        echo "<p>&bull; ".$pl."</p>";
                                    }
                                    ?></div><?php 
                                }
                                if (is_array($permission_list)) {
                                    if (count($permission_list) > 0) {
                                        ?><div class="dbp-color-error">&bull; <?php echo  sprintf(__('The mysql user does not have enough permissions to manage this plugin. Missing: <b>%s</b>', 'database_press'), implode(", ", $permission_list)); ?></div> <?php 
                                    } else {
                                        ?><div class="dbp-color-info">&bull; <?php _e('The permissions of the mysql user are correct','database_press'); ?></div> <?php 
                                    }
                                }
                                $max_input_vars = dbp_fn::get_max_input_vars();
                                if ($max_input_vars  < 3000) {
                                    if ($max_input_vars >= 1000) {
                                        ?><div class="dbp-color-warning">&bull; <?php _e('the max_input_vars (php.ini) value is sufficient, but if you can increase it to at least 5000 it would be better.','database_press'); ?></div> <?php 
                                    } else {
                                        ?><div class="dbp-color-error">&bull; <?php _e('init: the max_input_vars (php.ini) value is very low','database_press'); ?></div> <?php   
                                    }
                                } else {
                                    ?><div class="dbp-color-info">&bull; <?php _e('max_input_vars is ok','database_press'); ?></div> <?php 
                                }
                                if ($is_writable_dir) {
                                    ?><div class="dbp-color-info">&bull; <?php _e('Log dir is writable','database_press'); ?></div> <?php 
                                } else {
                                    ?><div class="dbp-color-error">&bull; <?php echo  sprintf(__('Log dir (%s) is NOT writable','database_press'), $dir); ?></div> <?php 
                                }
                                $d = get_option('_dbp_activete_info');
                                if (is_array($d)) {
                                    if (isset($d['date'])) {
                                        ?><div class="dbp-color-info">&bull; <?php echo  sprintf(__('the plugin was activated on %s'), $d['date']); ?></div><?php 
                                    }
                                }
                               ?>
                            </div>
                        </div>
                    </div>
                </div>

              
            </div>
        </div>
        <div class="dbp-column-tables-list" id="dbp_column_sidebar">
            <?php require (dirname(__FILE__)."/dbp-partial-sidebar.php"); ?>
        </div>
        <div id="dbp_sidebar_popup" class="dbp-sidebar-popup">
            <div id="dbp_dbp_title" class="dbp-dbp-title">
                <div id="dbp_dbp_close" class="dbp-dbp-close" onclick="dbp_close_sidebar_popup()">&times;</div>
            </div>
            <div id="dbp_dbp_loader" ><div class="dbp-sidebar-loading"><div  class="dbp-spin-loader"></div></div></div>
            <div id="dbp_dbp_content" class="dbp-dbp-content"></div>
        </div>
       
    </div>
</div>
<?php require (dirname(__FILE__)."/../js/database-press-footer-script.php"); ?>