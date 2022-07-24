<?php
/**
 * Caricato da includes/dbt-html-table.php 
 * Disegno il dropdown con i filtri di ricerca e tutte le opzioni della colonna
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
if ($sort !== false || ($original_field_name != "" && $filter !== false)) : ?>
<div class="dbt-dropdown-container-scroll">
<?php endif; 

if ($sort !== false) :
/**
 * Non è l'alias (alias_column), ma il nome della colonna
 */
?>
    <div class="dtf-table-sort <?php echo $sort_desc_class ; ?>" data-dtf_sort_key="<?php echo esc_attr($original_field_name); ?>" data-dtf_sort_order="DESC"><?php _e('Sort Desending', 'database_tables'); ?></div>
    <div class="dtf-table-sort <?php echo $sort_asc_class ; ?>" data-dtf_sort_key="<?php echo esc_attr($original_field_name); ?>" data-dtf_sort_order="ASC"><?php _e('Sort Ascending', 'database_tables'); ?></div>
    <div class="dtf-table-sort <?php echo $sort_remove_class ; ?>" ><?php _e('Remove sort', 'database_tables'); ?></div>
<?php endif; ?>
<?php /* ricerca */ ?>
<?php if ($original_field_name != "" && $filter !== false) : ?>
    <?php // Il campo in cui salvo filtro si sta per fare ?>
    <input type="hidden"  name="filter[search][<?php echo esc_attr($name_column); ?>][op]" id="filter_<?php echo esc_attr($name_column); ?>_op"  class="js-table-filter-select-op" value="<?php echo esc_attr($def_op); ?>">
    <?php // Rimuove i filtri ?>
    <div class="dbt-dropdown-hr"></div>
    <?php if ($def_input_value != "" || @$def_input_value_2 != "" || $default_value != "") : ?>
        <div class="js-remove-filter dbt-dropdown-line-click" data-rif="<?php echo esc_attr($name_column); ?>"><?php _e('Remove Filter', 'database_tables'); ?></div>
    <?php else: ?>
        <div class="dbt-dropdown-line-disable" data-rif="<?php echo esc_attr($name_column); ?>"><?php _e('Remove Filter', 'database_tables'); ?></div>
    <?php endif; ?>
    <div class="dbt-dropdown-hr"></div>
    <div class="dbt-dropdown-line-flex">
      
            <span class="dbt-filter-label">
                <input type="radio" name="filter[search][<?php echo esc_attr($name_column); ?>][r]" value="1" class="js-filter-search-radio" id="radio_<?php echo esc_attr($name_column); ?>_1" data-rif="<?php echo esc_attr($name_column); ?>"<?php echo (!in_array($def_op, ['IN','NOT IN'])) ? ' checked="checked"' : '' ; ?>>  
            </span>
            <span id="js_tf_select_label_<?php echo $name_column; ?>" >Filter operators</span>
            <?php
            Dbt_fn::html_select($html_select_array, true, 'class="js-table-filter-select-op-partial"  id="js_tf_select_op_'.$name_column.'_1" data-rif="'.$name_column.'"', $def_op);
            ?>

    </div>
    <?php // la textarea che tiene i valori della ricerca; ?>
    <textarea name="filter[search][<?php echo esc_attr($name_column); ?>][value]" id="dbt_dropdown_search_value_<?php echo $name_column; ?>" style="display:none"><?php echo esc_textarea(stripslashes($default_value)); ?></textarea>
    <input type="hidden" id="filter_search_original_column<?php echo $name_column; ?>" name="filter[search][<?php echo esc_attr($name_column); ?>][column]" value="<?php echo esc_attr($original_field_name); ?>">
    <input type="hidden" id="filter_search_orgtable_<?php echo esc_attr($name_column); ?>" name="filter[search][<?php echo esc_attr($name_column); ?>][table]" value="<?php echo esc_attr($original_table); ?>">
    <input type="hidden" id="filter_search_filter_<?php echo esc_attr($name_column); ?>" value="<?php echo esc_attr(($default_value != "")); ?>">
    <input type="hidden" id="filter_search_type_<?php echo esc_attr($name_column); ?>" value="<?php echo esc_attr($symple_type); ?>">
    <?php // L'input che accetta i valori per le ricerche = > < ecc...; ?>
    <div class="dbt-dropdown-line-flex" id="dbt_input_value_box_<?php echo $name_column; ?>">
        <span class="dbt-filter-label"><?php _e('Value', 'database_tables'); ?></span>
        <?php if ($symple_type == "DATE") : ?>
            <input class="dbt-table-filter js-table-filter-input-value" id="dbt_input_value_<?php echo $name_column; ?>" type="date" data-rif="<?php echo $name_column; ?>"  value="<?php echo esc_attr(str_replace(" ", "T", $def_input_value)); ?>">
        <?php else : ?>
            <input class="dbt-table-filter js-table-filter-input-value" data-rif="<?php echo $name_column; ?>"  id="dbt_input_value_<?php echo $name_column; ?>" type="text"  value="<?php echo esc_attr($def_input_value); ?>" >
        <?php endif; ?>
        
    </div>
    <?php // Il secondo input per il beetwen che accetta i valori per le ricerche = > < ecc...; ?>
    <div class="dbt-dropdown-line-flex" id="dbt_input_value2_box_<?php echo $name_column; ?>">
        <span class="dbt-filter-label"><?php _e('Value 2', 'database_tables'); ?></span>
        <?php if ($symple_type == "DATE") : ?>
            <input class="dbt-table-filter js-table-filter-input-value2" id="dbt_input_value2_<?php echo $name_column; ?>" type="date" data-rif="<?php echo $name_column; ?>"  value="<?php echo esc_attr(str_replace(" ", "T", $def_input_value_2)); ?>">
        <?php else : ?>
            <input class="dbt-table-filter js-table-filter-input-value2" data-rif="<?php echo $name_column; ?>"  id="dbt_input_value2_<?php echo $name_column; ?>" type="text"  value="<?php echo esc_attr($def_input_value_2); ?>" >
        <?php endif; ?>
        
    </div>
    <?php // I Checkboxes; ?>
    <div class="dbt-dropdown-hr"></div>
    <label  class="dbt-dropdown-line-flex">
        <span class="dbt-filter-label">
            <input type="radio" name="filter[search][<?php echo esc_attr($name_column); ?>][r]" value="2" class="js-filter-search-radio" id="radio_<?php echo esc_attr($name_column); ?>_2" data-rif="<?php echo esc_attr($name_column); ?>"<?php echo(in_array($def_op,['IN','NOT IN'])) ? ' checked="checked"' : '' ; ?>>  
            <?php _e('Search', 'database_tables'); ?>
        </span>
     
        <input class="dbt-table-filter js-table-filter-input_filter_checkboxes" data-rif="<?php echo $name_column; ?>" id="dbt_input_filter_checkboxes_<?php echo $name_column; ?>" type="text"  >
      
    </label>
    <div id="dbt_choose_values_box_<?php echo $name_column; ?>">
      

        <?php /*
        <div class="dbt-dropdown-line-flex" id="dbt_input_filter_checkboxes_row_<?php echo $name_column; ?>">
            <span class="dbt-filter-label"><?php _e('fast filter', 'database_tables'); ?></span>
            <input class="dbt-table-filter js-table-filter-input_filter_checkboxes" data-rif="<?php echo $name_column; ?>"  id="dbt_input_filter_checkboxes_<?php echo $name_column; ?>" type="text"  >
        </div>
        */ ?>

        <div class="dbt-drowpdown-click-box">
        <?php
        Dbt_fn::html_select(['IN'=>'Choose values', 'NOT IN'=>'Exclude values'], true, ' class="js-table-filter-select-op-partial dbt-small-select-for-checkbox" id="js_tf_select_op_'.$name_column.'_2" data-rif="'.$name_column.'"', $def_op);
        ?>
        <div class="dbt-dropdown-de-select-click js-dropdown-select-all-checkboxes" data-rif="<?php echo $name_column; ?>"><?php _e('Select all', 'database_tables'); ?></div>
        <div class="dbt-dropdown-de-select-click js-dropdown-deselect-all-checkboxes" data-rif="<?php echo $name_column; ?>"><?php _e('Deselect all', 'database_tables'); ?></div>
        </div>

        <div class="js-table-filter-checkbox-values" id="dbt_checkboxes_value_<?php echo $name_column; ?>" data-rif="<?php echo $name_column; ?>" data-column="<?php echo esc_attr($original_field_name); ?>">
            <ul class="dbt_dropdown_line_checkboxes_search" id="dbt_checkboxes_ul_<?php echo $name_column; ?>" data-rif="<?php echo $name_column; ?>">
                <li><label>Loading ...<label></li>
            </ul>
        </div>
        <div class="dbt-dropdown-info-count-checkboxes" id="dbt_dd_count-cb_<?php echo $name_column; ?>">
            <span class="js-dbt-cb-count-selected"></span> / <span class="js-dbt-cb-count-total"></span>
        </div>
    </div>
    <div class="dbt-dropdown-hr"></div>
    <div class="dbt-dropdown-line dbt-dropdown-line-right">
        <div class="button dbt-btn-search js-dbt-btn-search"  data-rif="<?php echo $name_column; ?>"><?php _e('OK', 'database_tables'); ?></div>
        <div class="button dbt-btn-search js-dbt-dropdown-btn-cancel"><?php _e('Cancel', 'database_tables'); ?></div>
    </div>
<?php endif;

if ($sort !== false || ($original_field_name != "" && $filter !== false)) : ?>
    </div>
<?php endif;