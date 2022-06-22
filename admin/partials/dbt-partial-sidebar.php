<?php
namespace DatabaseTables;
if (!defined('WPINC')) die;
$list_of_tables = $dtf::get_table_list();

$lists = get_posts(['post_status' => 'publish',
'numberposts' => -1,
'post_type'   => 'dbt_list']);

$post_type = $dtf::get_post_types();
$request_table = @$_REQUEST['table'];
$request_dbt_id =  @$_REQUEST['dbt_id'];
?>

<div id="sidebar-tabs" data-section="<?php echo esc_attr(@$_REQUEST['page']); ?>">
    <div class="js-sidebar-block" data-open="database_tables">
        <h3 class="js-sidebar-title dbt-sidebar-title" ><?php _e('Database table actions', 'database_tables'); ?></h3>
        <div class="js-sidebar-content dbt-sidebar-content" >
            <a class="dbt-sidebar-link" href="<?php echo admin_url("admin.php?page=database_tables&section=information-schema"); ?>"><span class="dashicons dashicons-editor-ul"></span> <?php _e('Show all tables', 'database_tables'); ?></a>

            <a class="dbt-sidebar-link" href="<?php echo admin_url("admin.php?page=dbt_list"); ?>"><span class="dashicons dashicons-editor-ul"></span> <?php _e('Show all List (query saved)', 'database_tables'); ?></a>

            <a class="dbt-sidebar-link" href="<?php echo admin_url("admin.php?page=database_tables&section=table-sql"); ?>"><span class="dashicons dashicons-edit-page"></span> <?php _e('SQL command', 'database_tables'); ?></a>

            <a class="dbt-sidebar-link" href="<?php echo add_query_arg(['section'=>'table-structure','action'=>'structure-edit','dbt_id'=>''], admin_url("admin.php?page=database_tables")); ?>"><span class="dashicons dashicons-plus-alt2"></span> <?php _e('Create new table', 'database_tables'); ?></a>

            <a class="dbt-sidebar-link" href="<?php echo add_query_arg(['section'=>'table-import'], admin_url("admin.php?page=database_tables")); ?>"><span class="dashicons dashicons-database-import"></span> <?php _e('Import', 'database_tables'); ?></a>

        </div>
    </div>
    <div class="js-sidebar-block" data-open="dbt_list">
        <h3 class="js-sidebar-title dbt-sidebar-title" ><?php _e('LIST (Query saved)', 'database_tables'); ?></h3>
        <div class="js-sidebar-content dbt-sidebar-content" >
               
                <ul>
                    <?php foreach ($lists as $post) :?>
                        <?php $slt = ($request_dbt_id  == $post->ID) ? ' dbt-sidebar-link-selected' : ''; ?>
                        <?php $link = admin_url("admin.php?page=dbt_list&section=list-browse&dbt_id=".$post->ID); ?>
                        <li><a class="dbt-sidebar-link-2<?php echo $slt; ?>" href="<?php echo $link; ?>"><?php echo $post->post_title; ?></a></li>
                    <?php endforeach; ?> 
                </ul>
        </div>
    </div>
    <div class="js-sidebar-block"  data-open="no_database_tables">
        <h3 class="js-sidebar-title dbt-sidebar-title"><?php _e('DB TABLES', 'database_tables'); ?></h3>
        <div class="js-sidebar-content dbt-sidebar-content" >
            <?php $wordpress_tables = Dbt_fn::wordpress_table_list(); ?>
            <?php $list = $list_of_tables['tables']; ?>
            <ul>
                <?php foreach ($list as $table_name) :?>
                    <?php if (! in_array( $table_name, $wordpress_tables)) : ?>
                        <?php $slt = ($request_table == $table_name) ? ' dbt-sidebar-link-selected' : ''; ?>
                        <li><a  class="dbt-sidebar-link-2<?php echo $slt; ?>" href="<?php echo add_query_arg(['section'=>'table-browse', 'table'=>$table_name], admin_url("admin.php?page=database_tables")); ?>"><?php echo $table_name; ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?> 
            </ul>
            <div class="dbt-sidebar-subtitle"><span class="dashicons dashicons-wordpress-alt"></span><?php _e('WORDPRESS TABLES', 'database_tables'); ?></div>
            <ul>
                <?php foreach ($list as $table_name) :?>
                    <?php if (in_array( $table_name, $wordpress_tables)) : ?>
                        <?php $slt = ($request_table == $table_name) ? ' dbt-sidebar-link-selected' : ''; ?>
                        <li><a class="dbt-sidebar-link-2<?php echo $slt; ?>" href="<?php echo add_query_arg(['section'=>'table-browse', 'table'=>$table_name], admin_url("admin.php?page=database_tables")); ?>"><?php echo $table_name; ?></a>
                        <?php if ($table_name == $dtf::get_prefix().'posts') : ?>
                        <ul class="dbt-ul-2">
                            <?php foreach ($post_type as $p) : ?>
                                <li>
                                    <form  method="POST" action="<?php echo admin_url('admin.php?page=database_tables&section=table-browse&table='.$dtf::get_prefix().'posts'); ?>">
                                        <input type="hidden" name="custom_query" value="SELECT * FROM `<?php echo $dtf::get_prefix(); ?>posts`">
                                        <input type="hidden" name="filter[search][<?php echo $dtf::get_prefix(); ?>posts_post_type][op]" value="IN">
                                        <input type="hidden" name="action_query" value="filter">
                                        <input type="hidden" name="filter[search][<?php echo $dtf::get_prefix(); ?>posts_post_type][r]" value="2">
                                        <input type="hidden" name="filter[search][<?php echo $dtf::get_prefix(); ?>posts_post_type][table]" value="<?php echo $dtf::get_prefix(); ?>posts">
                                        <input type="hidden" name="filter[search][<?php echo $dtf::get_prefix(); ?>posts_post_type][column]" value="`<?php echo $dtf::get_prefix(); ?>posts`.`post_type`">
                                        <input type="hidden" name="filter[search][<?php echo $dtf::get_prefix(); ?>posts_post_type][value]" value="<?php echo esc_attr($p); ?>">
                                        <div class="dbt-ul-2-submit" onclick="jQuery(this).parent().submit();"><?php echo $p; ?></div>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?> 
            </ul>

        </div>
    </div>
    <div id="dbt_documentation_box" class="js-sidebar-block"  data-open="no_database_tables2" data-homepage="<?php echo $_REQUEST['page'].((isset($_REQUEST['section'])) ? '-'.$_REQUEST['section'] : '');?>">
        <h3 class="js-sidebar-title dbt-sidebar-title"><?php _e('HELP', 'database_tables'); ?></h3>
        <div id="searchPinaResult" class="js-sidebar-content dbt-sidebar-content" style="display: block !important; overflow: visible !important; height: initial !important; max-height: 0 !important; margin-top: 0.6rem;"></div>
    </div>
</div>