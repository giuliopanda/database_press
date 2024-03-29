<?php
namespace DatabasePress;
if (!defined('WPINC')) die;
$list_of_tables = dbp_fn::get_table_list();

$lists = get_posts(['post_status' => 'publish',
'numberposts' => -1,
'post_type'   => 'dbp_list']);

$post_type = dbp_fn::get_post_types();
$request_table = dbp_fn::get_request('table');
$request_dbp_id =  dbp_fn::get_request('dbp_id');
?>

<div id="sidebar-tabs" data-section="<?php echo esc_attr(@$_REQUEST['page']); ?>">
    <div class="js-sidebar-block js-dbpblock-dbpaction" data-open="database_press">
        <h3 class="js-sidebar-title dbp-sidebar-title" data-dbpname="dbpaction"><?php _e('Database table actions', 'database_press'); ?></h3>
        <div class="js-sidebar-content dbp-sidebar-content" >
            <a class="dbp-sidebar-link" href="<?php echo admin_url("admin.php?page=database_press&section=information-schema"); ?>"><span class="dashicons dashicons-editor-ul"></span> <?php _e('Show all tables', 'database_press'); ?></a>

            <a class="dbp-sidebar-link" href="<?php echo admin_url("admin.php?page=dbp_list"); ?>"><span class="dashicons dashicons-editor-ul"></span> <?php _e('Show all List (query saved)', 'database_press'); ?></a>

            <a class="dbp-sidebar-link" href="<?php echo admin_url("admin.php?page=database_press&section=table-sql"); ?>"><span class="dashicons dashicons-edit-page"></span> <?php _e('SQL command', 'database_press'); ?></a>

            <a class="dbp-sidebar-link" href="<?php echo add_query_arg(['section'=>'table-structure','action'=>'structure-edit','dbp_id'=>''], admin_url("admin.php?page=database_press")); ?>"><span class="dashicons dashicons-plus-alt2"></span> <?php _e('Create new table', 'database_press'); ?></a>

            <a class="dbp-sidebar-link" href="<?php echo add_query_arg(['section'=>'table-import'], admin_url("admin.php?page=database_press")); ?>"><span class="dashicons dashicons-database-import"></span> <?php _e('Import', 'database_press'); ?></a>

        </div>
    </div>
    <div class="js-sidebar-block js-dbpblock-dbplist" data-open="dbp_list">
        <h3 class="js-sidebar-title dbp-sidebar-title" data-dbpname="dbplist"><?php _e('LIST (Query saved)', 'database_press'); ?></h3>
        <div class="js-sidebar-content dbp-sidebar-content" >
            <div><a class="dbp-sidebar-link" href="<?php echo admin_url("admin.php?page=dbp_list"); ?>"><span class="dashicons dashicons-editor-ul"></span> <?php _e('Show all (Create new)', 'database_press'); ?></a></div>
            <div class="dbp-content-margin">
                <div class="dbp-form-row">
                <label>Search</label>
                <input type="search" class="form-input-edit " onkeyup="dbp_help_filter2(this)" data-classfilter="js-id-dbp-list-list" style="width: 70%;">
                </div>
            </div>

            <ul class="js-id-dbp-list-list">
                <?php foreach ($lists as $post) :?>
                    <?php $slt = ($request_dbp_id  == $post->ID) ? ' dbp-sidebar-link-selected' : ''; ?>
                    <?php $link = admin_url("admin.php?page=dbp_list&section=list-browse&dbp_id=".$post->ID); ?>
                    <li><a class="js-dbp-table-text dbp-sidebar-link-2<?php echo $slt; ?>" href="<?php echo $link; ?>"><?php echo $post->post_title; ?></a></li>
                <?php endforeach; ?> 
            </ul>
        </div>
    </div>
    <div class="js-sidebar-block js-dbpblock-dbptables"  data-open="no_database_press">
        <h3 class="js-sidebar-title dbp-sidebar-title" data-dbpname="dbptables"><?php _e('DB TABLES', 'database_press'); ?></h3>
        <div class="js-sidebar-content dbp-sidebar-content" >
            <div><a class="dbp-sidebar-link" href="<?php echo admin_url("admin.php?page=database_press&section=information-schema"); ?>"><span class="dashicons dashicons-editor-ul"></span> <?php _e('Show all tables', 'database_press'); ?></a></div>
            <div class="dbp-content-margin">
                <div class="dbp-form-row">
                <label>Search</label>
                <input type="search" class="form-input-edit " onkeyup="dbp_help_filter2(this)" data-classfilter="js-id-dbp-list-tables" style="width: 70%;">
                </div>
            </div>

            <?php $wordpress_tables = dbp_fn::wordpress_table_list(); ?>
            <?php $list = $list_of_tables['tables']; ?>
            <ul class="js-id-dbp-list-tables">
              
                <?php foreach ($list as $table_name) :?>
                    <?php if (! in_array( $table_name, $wordpress_tables)) : ?>
                        <?php $slt = ($request_table == $table_name) ? ' dbp-sidebar-link-selected' : ''; ?>
                        <li><a class="js-dbp-table-text dbp-sidebar-link-2<?php echo $slt; ?>" href="<?php echo add_query_arg(['section'=>'table-browse', 'table'=>$table_name], admin_url("admin.php?page=database_press")); ?>"><?php echo $table_name; ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?> 
            </ul>
            <div class="dbp-sidebar-subtitle"><span class="dashicons dashicons-wordpress-alt"></span><?php _e('WORDPRESS TABLES', 'database_press'); ?></div>
            <ul class="js-id-dbp-list-tables">
                <?php foreach ($list as $table_name) :?>
                    <?php if (in_array( $table_name, $wordpress_tables)) : ?>
                        <?php $slt = ($request_table == $table_name) ? ' dbp-sidebar-link-selected' : ''; ?>
                        <li><a class="js-dbp-table-text dbp-sidebar-link-2<?php echo $slt; ?>" href="<?php echo add_query_arg(['section'=>'table-browse', 'table'=>$table_name], admin_url("admin.php?page=database_press")); ?>"><?php echo $table_name; ?></a>
                        <?php /* if ($table_name == dbp_fn::get_prefix().'posts') : ?>
                        <ul class="dbp-ul-2">
                            <?php foreach ($post_type as $p) : ?>
                                <li>
                                    <form  method="POST" action="<?php echo admin_url('admin.php?page=database_press&section=table-browse&table='.dbp_fn::get_prefix().'posts'); ?>">
                                        <input type="hidden" name="custom_query" value="SELECT * FROM `<?php echo dbp_fn::get_prefix(); ?>posts`">
                                        <input type="hidden" name="filter[search][<?php echo dbp_fn::get_prefix(); ?>posts_post_type][op]" value="IN">
                                        <input type="hidden" name="action_query" value="filter">
                                        <input type="hidden" name="filter[search][<?php echo dbp_fn::get_prefix(); ?>posts_post_type][r]" value="2">
                                        <input type="hidden" name="filter[search][<?php echo dbp_fn::get_prefix(); ?>posts_post_type][table]" value="<?php echo dbp_fn::get_prefix(); ?>posts">
                                        <input type="hidden" name="filter[search][<?php echo dbp_fn::get_prefix(); ?>posts_post_type][column]" value="`<?php echo dbp_fn::get_prefix(); ?>posts`.`post_type`">
                                        <input type="hidden" name="filter[search][<?php echo dbp_fn::get_prefix(); ?>posts_post_type][value]" value="<?php echo esc_attr($p); ?>">
                                        <div class="dbp-ul-2-submit" onclick="jQuery(this).parent().submit();"><?php echo $p; ?></div>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; */?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?> 
            </ul>

        </div>
    </div>
    <div id="dbp_documentation_box" class="js-sidebar-block"  data-open="no_database_press2" data-homepage="<?php echo $_REQUEST['page'].((isset($_REQUEST['section'])) ? '-'.$_REQUEST['section'] : '');?>">
        <h3 class="js-sidebar-title dbp-sidebar-title" style="display:none"></h3>
        <br>
        <div id="searchPinaResult" class="js-sidebar-content dbp-sidebar-content" style="display: block !important; overflow: visible !important; height: initial !important; max-height: 0 !important; margin-top: 0.6rem;"></div>
    </div>
</div>