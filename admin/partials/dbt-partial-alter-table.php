<?php
/**
* Gestisco la parte di form per la creazione/modifica di una tabella sql
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div id="dbt_create_table" class="dbt-import-content-create-table">
<?php $print_table = ($table == "") ? $table_new_name : $table; ?>
<input type="hidden" id="dbt_structure_table_name" name="structure_table_name" value="<?php echo esc_attr($print_table); ?>">
<div class="dbt-import-table-name">
    <span style="vertical-align: middle;"><?php _e('Table name:', 'database-table'); ?></span>
   
    <label id="dbt_wp_prefix" class="dbt-wp-prefix" <?php echo ($print_table == "" || substr($print_table,0, strlen($dtf->get_prefix())) == $dtf->get_prefix()) ? '' : ' style="visibility:hidden"'; ?>><?php echo $dtf->get_prefix(); ?></label>
    <input type="text" class="js-dbt-validity" id="dbt_partial_name_table" value="<?php 
    if (substr($print_table,0, strlen($dtf->get_prefix())) == $dtf->get_prefix()) {
        echo esc_attr(substr($print_table, strlen($dtf->get_prefix())));
    } else {
        echo esc_attr($print_table); 
    }
    ?>" required>
    <label><input type="checkbox" id="dbt_table_use_prefix" value="<?php echo $dtf->get_prefix(); ?>"<?php echo ($table == "" ||  substr($table,0, strlen($dtf->get_prefix())) == $dtf->get_prefix()) ? ' checked="checked"' : ''; ?>><?php _e('Use WP prefix', 'database-table'); ?> </label>
</div>

<table class="wp-list-table widefat striped dbt-table-view-list js-dragable-table">
    <thead>
        <tr>
            <th><?php _e('Order','database_tables'); ?></th>
            <th><?php _e('Name','database_tables'); ?></th>
            <th><?php _e('Preset','database_tables'); ?></th>
            <th><?php _e('Action','database_tables'); ?></th>
            <th><?php _e('Type','database_tables'); ?></th>
            <th><?php _e('Length','database_tables'); ?></th>
            <th><?php _e('Attributes','database_tables'); ?></th>
            <th><?php _e('Default','database_tables'); ?></th>
            <th style="width:1%"><?php _e('Null','database_tables'); ?></th>
            <th title="<?php _e('Primary Key & Auto increment','database_tables'); ?>"><?php _e('Pri','database_tables'); ?></th>
          
        </tr>
    </thead>
    <?php 
    $row = 1;
    ?>
    <tr class="js-clore-master">
        <td class="js-dragable-handle"><span class="dashicons dashicons-sort"></span></td>
        <td> 
            <input type="hidden" name="table_update[field_original_name][]" value="">
            <input type="hidden" name="table_update[field_original_position][]" value="">
            <input type="hidden" class="js-field-action" name="table_update[field_action][]" value="add">
            <input type="text" name="table_update[field_name][]" value="" class="js-dbt-validity" required>
        </td>
        <td>
            <?php echo $dtf::html_select(['varchar'=>'String (1 line)', 'text'=>'Text (Multiline)','int_signed'=>'Number', 'decimal'=>'Decimal (9,2)', 'date'=>'Date', 'datetime'=>'Date Time', 'pri'=>'Primary Key','advanced'=>'Advanced'], true, 'class="js-field-preselect" onchange="dbt_preselect(this)"', false, 'varchar'); ?>  
        </td>
        <td>
            <div class="button" onClick="dbt_alter_table_delete_row(this);"><?php _e('Delete' , 'database_tables'); ?></div>
        </td>
        <td>
            <div class="js-td-advanced">
                <?php echo $dtf::html_select(Dbt_model_structure::column_list_type(), false, 'name="table_update[field_type][]" class="js-create-table-type"', 'VARCHAR(255)'); ?>
            </div>
        </td>
      
        <td>
            <div class="js-td-advanced">
                <input name="table_update[field_length][]" class="js-create-table-length" value="" style="width:70px" >
            </div>
        </td>
        <td>
            <div class="js-td-advanced">
                <?php echo $dtf::html_select([''=>'', 'UNSIGNED'=>'UNSIGNED', 'UNSIGNED ZEROFILL'=>'UNSIGNED ZEROFILL', 'on update CURRENT_TIMESTAMP'=>'on update CURRENT_TIMESTAMP'], true,'name="table_update[attributes][]" class="js-create-table-attributes"',false); ?>
            </div>
        </td>
        <td>
            <div class="js-td-advanced">
                <input name="table_update[default][]" class="js-create-table-default" value="">
            </div>
        </td>
        <td>
            <div class="js-td-advanced" style="text-align:center">
                <input type="text" class="js-check-null-value" name="table_update[null][]" value="" style="display:none">
                <input type="checkbox" class="js-check-null-checkbox" onchange="checkbox_null(this)">
            </div>
        </td>
        
        <td>
            <div class="js-td-advanced" style="text-align:center">
                <?php echo $dtf::html_select(['f'=>'NO','t'=>'YES'], true,'name="table_update[primary][]" class="js-unique-primary"'); ?>
            </div>
        </td>

    </tr>
    <?php
    // array(6) { ["field_name"]=> string(5) "Field" ["Type"]=> string(4) "Type" ["Null"]=> string(4) "Null" ["Key"]=> string(3) "Key" ["Default"]=> string(7) "Default" ["Extra"]=> string(5) "Extra" }
    array_shift($table_model->items);
    //  var_dump($table_model->items);
    $old_column_field_name = "FIRST!";
    foreach ($table_model->items as $cs) {
        $column = Dbt_fn_structure::convert_show_column_mysql_row_to_form_data($cs);
        // object(stdClass)#994 (6) { ["field_name"]=> string(2) "id" ["Type"]=> string(7) "int(10)" ["Null"]=> string(2) "NO" ["Key"]=> string(3) "PRI" ["Default"]=> NULL ["Extra"]=> string(14) "auto_increment" }
        ?>
        <tr class="js-dragable-tr">
            <td class="js-dragable-handle"><span class="dashicons dashicons-sort"></span></td>
            <td>
                <input type="hidden" name="table_update[field_original_name][]" value="<?php echo $column->field_name; ?>">
                <input type="hidden" name="table_update[field_original_position][]" value="<?php echo $old_column_field_name; ?>">
                <input type="hidden" class="js-field-action" name="table_update[field_action][]" value="">
                <input type="text" name="table_update[field_name][]" value="<?php echo $column->field_name; ?>" class="js-dbt-validity" required>
            </td>
            <td>
                <?php echo $dtf::html_select(['varchar'=>'String (1 line)', 'text'=>'Text (Multiline)','int_signed'=>'Number', 'decimal'=>'Decimal (9,2)', 'date'=>'Date', 'datetime'=>'Date Time', 'pri'=>'Primary Key','advanced'=>'Advanced'], true, 'class="js-field-preselect" onchange="dbt_preselect(this)"', false, $column->preset); ?>  
            </td>
            <td>
                <div class="button" onClick="dbt_alter_table_delete_row(this);"><?php _e('Delete' , 'database_tables'); ?></div>
            </td>
            <td>
                <div class="js-td-advanced">
                    <?php echo $dtf::html_select(Dbt_model_structure::column_list_type(), false, 'name="table_update[field_type][]" class="js-create-table-type"', false, $column->field_type); ?>
                </div>
            </td>
            <td>
                <div class="js-td-advanced">
                    <input name="table_update[field_length][]" class="js-create-table-length" value="<?php echo esc_attr($column->field_length); ?>" style="width:70px"> 
                </div>
            </td>
            <td>
                <div class="js-td-advanced">
                    <?php echo $dtf::html_select([''=>'','UNSIGNED'=>'UNSIGNED', 'UNSIGNED ZEROFILL'=>'UNSIGNED ZEROFILL', 'on update CURRENT_TIMESTAMP'=>'on update CURRENT_TIMESTAMP'], true,'name="table_update[attributes][]" class="js-create-table-attributes"',false, $column->attributes); ?>
                </div>
            </td>
            <td >
                <div class="js-td-advanced">
                <input type="text" name="table_update[default][]" class="js-create-table-default" value="<?php echo esc_attr($column->default); ?>">
                </div>
            </td>
            <td >
                <div class="js-td-advanced" style="text-align:center" >
                    <input type="text"  class="js-check-null-value" name="table_update[null][]"  value="<?php echo esc_attr($column->null); ?>" style="display:none">
                    <input type="checkbox" class="js-check-null-checkbox" onchange="checkbox_null(this)" <?php echo ($column->null == 't') ? ' checked="checked"' : ''; ?>>
                </div>
            </td>
            <td >
                <div class="js-td-advanced" style="text-align:center" >
                    <?php echo $dtf::html_select(['f'=>'NO','t'=>'YES'], true,'name="table_update[primary][]" class="js-unique-primary"',false, $column->dbt_primary); ?>
                </div>
            </td>
         
        </tr>
        <?php 
        $row++;
        $old_column_field_name = $column->field_name;
    }
    ?>
    <tr>
        <td colspan="10">
            <div onclick="dbt_alter_add_row(this, '<?php echo @$max_row_allowed; ?>')" class="button"><?php _e('Add row', 'database_tables'); ?></div>
        </td>
    </tr>
</table>
<div id="dbt_content_button_create_form_msg_no_primary" class="dtf-alert-sql-error" style="display:none">
    <?php _e('This system works with tables that have only one field set as the autoincrement primary key!','database_tables'); ?>
</div>
</div>


