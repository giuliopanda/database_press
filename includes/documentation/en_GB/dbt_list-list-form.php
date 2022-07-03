<?php
/**
 * header-type:doc
 * header-title: Data entry form 
* header-tags: form, field, lookup, text, textarea, select, checkbox, checkboxes, table attributes
* header-description: Data Entry Form is a form that helps to enter the data. Here you can manage how the data should be entered into the tables
* header-lang:ENG
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>Manage data entry forms</p>
    <div id="dbt_help_attrs" class="dbt_help_div">
        <h4>Table attributes</h4>
        <p>
            Each insertion module is composed of one or more blocks that identify the tables from which the data is extracted. The title shows the table and the alias used in the query. <br>
             For each table, by clicking on show attributes, it is possible to modify some display parameters
        </p>
    </div>

    <div id="dbt_help_toggle" class="dbt_help_div">
        <h4>Show/Hide</h4>
        <p>Choose whether the field should be used in the data entry form</p>
    </div>

    <div id="dbt_help_js" class="dbt_help_div">
        <h4>JS Script</h4>
        <p> Insert javascript to customize the insertion experience. </p>
         <p> Example: The field is valid only if it is between 0 and 100 </p>
        <pre class="dbt-code">
 field.valid_range(0,100);
        </pre>
        <p>Example: I show the field if the value of a hypothetical checkbox is equal to 1</p>
        <pre class="dbt-code">
 field.toggle(form.get('mycheckboxlabel').val() == 1);
        </pre>
        <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=js-controller-form") ?>" target="blank" class="js-simple-link">Approfondisci</a>
    </div>
    <div id="dbt_help_class" class="dbt_help_div">
        <h4>Custom css class</h4>
        <p> Add one or more css classes to the field. </p>
        <p> You can align two fields next to each other by adding the <b> dbt-form-columns-2 </b> class to the two fields.
        <p> For checkboxes and radios it is possible to paginate the options in multiple columns by adding one of the following custom css class: <br>
        dbt-form-cb-columns-2, dbt-form-cb-columns-3, dbt-form-cb-columns-4 </p>
    </div>

    <div id="dbt_help_lookup" class="dbt_help_div">
        <h4> Lookup field </h4>
        <p> Lookup fields are used to link data with other tables via primary key. it is important that the linked list has a single primary key. <br>
        To view the data in the list, you need to change the query by adding a left join to the linked table. </p>
    </div>

    <div id="dbt_help_delete" class="dbt_help_div">
        <h4>Delete field</h4>
        <p> If the table is in DRAFT mode then from the form it is possible to modify its structure. </p>
         <p> Delete field removes the field and all data entered in that field </p>
         <p> If you don't want to remove the column or can't edit the table you can hide the field from the select show / hide </p>
    </div>
    <div id="dbt_help_new_field" class="dbt_help_div">
        <h4>New field</h4>
        <p> If the table is in DRAFT mode then it is possible to create a new field in the table. If you want to have more control in the creation of fields, you can go to the table structure and modify it. </p>
    </div>
    <div class="dbt_help_div">
        <h4>PHP Filter</h4>
        <p><a href="<?php echo admin_url("admin.php?page=dbt_docs&section=hooks") ?>" target="blank" class="js-simple-link">apply_filters('dbt_save_data', $query_to_execute, $dbt_id, $origin)</a></p>
    </div>
    <br><br>
</div>
