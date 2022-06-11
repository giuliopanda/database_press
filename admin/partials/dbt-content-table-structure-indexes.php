<?php 
/**
* La modifica/creazione degli indici nella struttura della tabella
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-header">
    <?php require(dirname(__FILE__).'/dbt-partial-tabs.php'); ?>
</div>
<div class="dbt-content-table js-id-dbt-content">
    <div class="dbt-content-margin">
        <h2><?php _e(sprintf('Table %s INDEX', $table),'database_tables'); ?></h2>
        <?php if ($msg != "") : ?>
            <div class="dtf-alert-info"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if (@$msg_error != ""): ?>
            <div class="dtf-alert-sql-error"><?php echo $msg_error; ?></div>
        <?php endif ; ?>
        <div class="dbt-content-margin">
            <form id="table_structure_index" method="POST" action="<?php echo admin_url("admin.php?page=database_tables&section=table-structure&table=".$table); ?>" id="dbt_indexes">
                <input type="hidden" name="action" value="save_index" />
                <input type="hidden" name="dbt_id" value="<?php echo esc_attr($id); ?>" />
                <input type="hidden" name="original_index" value="<?php echo esc_attr($indexes->choice); ?>" />
                <input type="hidden" name="original_name" value="<?php echo esc_attr($indexes->name); ?>" />
                <?php if ($indexes->name != "") : ?>
                <div class="dbt-form-row">
                    <label><span class="dbt-form-label "><?php _e('Index name', 'database_tables'); ?></span>
                        <input name="index[name]" value="<?php echo esc_attr($indexes->name); ?>" class="dbt-input" required>
                    </label>
                </div>
                <?php endif; ?>
                <div class="dbt-form-row">
                    <label><span class="dbt-form-label "><?php _e('Index choice', 'database_tables'); ?></span>
                    <?php echo Dbt_fn::html_select(['INDEX' => 'Optimize MySQL Search (Index)', 'UNIQUE' => 'Unique values'], true, 'name="index[type]"', $indexes->choice); ?>
                    </label>
                </div>
                <div class="dbt-form-row">
                    <label><span class="dbt-form-label "><?php _e('Columns', 'database_tables'); ?></span>
                    <div class="button" onclick="clone_li_master()"><?php _e('Add new', 'database_tables'); ?></div>
                    </label>
                    <ul style="margin-left: 8rem;" class="js-drag-index-column">
                        <li class="js-clore-master">
                        <span class="js-dragable-handle"><span class="dashicons dashicons-sort"></span></span>
                            <?php echo Dbt_fn::html_select($table_fields,false, 'name="index[columns][]"') ; ?> <span class="dbt-warning-link" onclick="dbt_index_remove_cols(this)">DELETE</span>
                        </li>  
                        <?php foreach ( $indexes->columns as $column) : ?>
                        <li class="js-dragable-li">
                        <span class="js-dragable-handle"><span class="dashicons dashicons-sort"></span></span>
                            <?php echo Dbt_fn::html_select($table_fields,false, 'name="index[columns][]"', $column) ; ?> <span class="dbt-warning-link" onclick="dbt_index_remove_cols(this)">DELETE</span></li>  
                        <?php endforeach ;?>
                    </ul>
                    <input type="submit" class="dbt-submit" value="<?php _e('Save','database_tables'); ?>" >
                </div>
            </form>
        </div>
    </div>
</div>
<?php if (count($indexes->columns) == 0) : ?>
    <script>
        jQuery(document).ready(function ($) {
            clone_li_master();
        });
    </script>
<?php endif; ?>