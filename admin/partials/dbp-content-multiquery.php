<?php
/**
 * Mostra i risultati dell'esecuzione multipla di query scritte direttamente importate (dalla pagina import).
 * Se non ha fatto in tempo ad eseguire tutte le query continua a farlo tramite ajax (class-database-press-loader.php > dbp_multiqueries_ajax()
 * 
 * Per il rendering delle tabelle chiama: dirname(__FILE__)."/dbp-content-table-without-filter.php" 
 * 
 * @var Boolean $ajax_continue 
 * @var Array $info
 * @var $queries
 * 
 * @package    database_press
 * @subpackage database_press/admin
 */
namespace DatabasePress;
if (!defined('WPINC')) die;
?>
<div class="dbp-content-header">
    <?php require(dirname(__FILE__).'/dbp-partial-tabs.php'); ?>
  
</div>
<div class="dbp-content-table js-id-dbp-content" >
    <h2><?php _e('Multi Queries executed','database_press'); ?></h2>
    <?php if ($ajax_continue != false) {
        $link = admin_url('admin-post.php?action=dbp_download_multiquery_report&fnid=' . $ajax_continue);  
        _e(vsprintf('%s out of %s queries were performed',['<span id="dbp_count_queries_executed">'.$info['executed_queries'].'</span>',  $info['total_queries']]),'database_press');
        ?>
        <div id="multiqueries_end_ok" class="dbp-alert-info" style="display:none">
            <?php _e('all done! everything went fine', 'database_press'); ?> <a href="<?php echo $link; ?>"> <?php _e('Download result', 'database_press'); ?></a>
        </div>           
        <div id="multiqueries_end_no_ok" class="dbp-alert-sql-error" style="display:none">
            <?php _e('Some queries gave an error', 'database_press'); ?> <a href="<?php echo $link; ?>"> <?php _e('Download result', 'database_press'); ?></a>
        </div>  
        <div id="multiqueries_cancel" class="dbp-alert-sql-error" style="display:none">
            <?php _e('Query execution was interrupted by the user.', 'database_press'); ?> <a href="<?php echo $link; ?>"> <?php _e('Download the report for more information', 'database_press'); ?></a>
        </div>  
        <div id="multiqueries_continue" class="dbp-alert-sql-error" style="display:<?php echo ($info['last_error'] == "") ? "none": "block"; ?>">
            <h2>Query error:</h2>
            <p id="multiqueries_last_error_msg"><?php echo $info['last_error']; ?></p>
            <?php _e('A query have failed. Do you want to continue?', 'database_press'); ?> 
            <div class="button" onclick="dbp_multiqueries_ajax('<?php echo esc_attr($ajax_continue); ?>')"> <?php _e('Continue', 'database_press'); ?></div> 
            
            <div class="button" onclick="dbp_multiqueries_cancel('<?php echo esc_attr($ajax_continue); ?>')"> <?php _e('Cancel', 'database_press'); ?></div>

            <label class="dbp-label-ignore-erros"><input type="checkbox" id="dbp_ignore_errors" value="1"><?php _e('Ignore errors', 'database_press'); ?></label>
        </div>  
        <?php 
        if ($info['last_error'] == "") { ?>
            <script>
                jQuery(document).ready(function ($) {
                    dbp_multiqueries_ajax('<?php echo esc_attr($ajax_continue); ?>');
                });
            </script>
        <?php 
        }
    } else {
        ?><p><?php echo (sprintf(__('%s Queries executed', 'database_press'), count($queries))); ?></p><?php
        require (dirname(__FILE__)."/dbp-content-table-without-filter.php");
    } 
    ?>
</div>