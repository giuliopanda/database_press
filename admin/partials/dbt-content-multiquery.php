<?php
/**
 * Mostra i risultati dell'esecuzione multipla di query scritte direttamente importate (dalla pagina import).
 * Se non ha fatto in tempo ad eseguire tutte le query continua a farlo tramite ajax (class-database-table-loader.php > dbt_multiqueries_ajax()
 * 
 * Per il rendering delle tabelle chiama: dirname(__FILE__)."/dbt-content-table-without-filter.php" 
 * 
 * @var Boolean $ajax_continue 
 * @var Array $info
 * @var $queries
 * 
 * @todo il tasto export non funziona
 * @todo Al momento visualizza i risultati delle select con solo 2 righe, ma non mi piace, preferirei mostrarne molte di più.
 *
 * @package    database-table
 * @subpackage database_tables/admin
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-header">
    <?php require(dirname(__FILE__).'/dbt-partial-tabs.php'); ?>
  
</div>
<div class="dbt-content-table js-id-dbt-content" >
    <h2><?php _e('Multi Queries executed','database_tables'); ?></h2>
    <?php if ($ajax_continue != false) {
        $link = admin_url('admin-post.php?action=dbt_download_multiquery_report&fnid=' . $ajax_continue);  
        _e(vsprintf('%s out of %s queries were performed',['<span id="dbt_count_queries_executed">'.$info['executed_queries'].'</span>',  $info['total_queries']]),'database_tables');
        ?>
        <div id="multiqueries_end_ok" class="dtf-alert-info" style="display:none">
            <?php _e('all done! everything went fine', 'database_tables'); ?> <a href="<?php echo $link; ?>"> <?php _e('Download result', 'database_tables'); ?></a>
        </div>           
        <div id="multiqueries_end_no_ok" class="dtf-alert-sql-error" style="display:none">
            <?php _e('Some queries gave an error', 'database_tables'); ?> <a href="<?php echo $link; ?>"> <?php _e('Download result', 'database_tables'); ?></a>
        </div>  
        <div id="multiqueries_cancel" class="dtf-alert-sql-error" style="display:none">
            <?php _e('Query execution was interrupted by the user.', 'database_tables'); ?> <a href="<?php echo $link; ?>"> <?php _e('Download the report for more information', 'database_tables'); ?></a>
        </div>  
        <div id="multiqueries_continue" class="dtf-alert-sql-error" style="display:<?php echo ($info['last_error'] == "") ? "none": "block"; ?>">
            <h2>Query error:</h2>
            <p id="multiqueries_last_error_msg"><?php echo $info['last_error']; ?></p>
            <?php _e('A query have failed. Do you want to continue?', 'database_tables'); ?> 
            <div class="button" onclick="dbt_multiqueries_ajax('<?php echo esc_attr($ajax_continue); ?>')"> <?php _e('Continue', 'database_tables'); ?></div> 
            
            <div class="button" onclick="dbt_multiqueries_cancel('<?php echo esc_attr($ajax_continue); ?>')"> <?php _e('Cancel', 'database_tables'); ?></div>

            <label class="dbt-label-ignore-erros"><input type="checkbox" id="dbt_ignore_errors" value="1"><?php _e('Ignore errors', 'database_tables'); ?></label>
        </div>  
        <?php 
        if ($info['last_error'] == "") { ?>
            <script>
                jQuery(document).ready(function ($) {
                    dbt_multiqueries_ajax('<?php echo esc_attr($ajax_continue); ?>');
                });
            </script>
        <?php 
        }
    } else {
        ?><p><?php _e(sprintf('%s Queries executed', count($queries))); ?></p><?php
        require (dirname(__FILE__)."/dbt-content-table-without-filter.php");
    } 
    ?>
</div>