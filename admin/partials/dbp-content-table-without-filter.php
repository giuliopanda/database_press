<?php
/**
 * Stampa i risultati di una o più query contenute dentro $items. le tabelle non hanno jabascript. 
 * è chiamata da 
 * 
 * @var Class $dbp function
 * @var Array $items è l'elenco delle tabelle da stampare [{model:table-model, content}, ...]
 * @var Array $list_of_tables
 * Tutti questi parametri dovrei portarli dentro table_model ??
 * @var database_press_model_base $table_model  
 * 
 * @todo il bottone export non funziona
 */
namespace DatabasePress;
if (!defined('WPINC')) die;
foreach ($items as $item) {
    echo '<div class="dbp-result-query dbp-css-mb-1 js-dbp-mysql-query-text">'.$item->model->get_default_query().'</div>';
    ?>
    <div class="dbp-multiquery-action">

        [<form method="post" action="<?php echo admin_url("admin.php"); ?>" class="dbp-form-single-query">
            <input type="hidden" name="page"  value="database_press">
            <input type="hidden" name="action"  value="custom_query">
            <input type="hidden" name="custom_query"  value="<?php echo esc_attr($item->model->get_current_query()); ?>">
        </form>]
        <span class="dbp-multiquery-single-query-info">
            <?php 
            if ($item->model->sql_type() == "select") {
                echo $item->model->total_items." total, ";
            } 
            echo " Query took ".$item->model->time_of_query." seconds.";
            ?>
        </span>
    </div>
    <?php
    echo $item->content; 
}
