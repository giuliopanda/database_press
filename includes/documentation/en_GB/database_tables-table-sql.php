<?php
/**
 * header-type:rif
 * header-order: 04
 * header-title: List of tables
 * header-tags:table-sql list of tables mysql column
 * header-description: Consultation page with the list of columns of all tables
 * header-package-title: Manage DB
 * header-package-link: manage-db.php
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
Dbt_fn::require_init();
$all_columns = Dbt_fn::get_all_columns();
?>
<div class="dbt-content-margin">

    <p>List of database tables and columns</p>

    <div class="dbt-form-row">
    <input type="text" class="form-input-edit " onkeyup="dbt_help_filter(this)" data-idfilter="dbtHelpListTableFields" style="width: 99%;">
    </div>
</div>

<div class="dbt-help-table-list">
    <ul class="dbt-help-table-list-ul" id="dbtHelpListTableFields">
        <?php foreach ($all_columns as $table=>$fields) : ?>
        <li>
            <div class="dbt-help-table-name js-dbt-table-text"><?php echo $table; ?></div>
            <ul class="dbt-help-table-list-fields">
                <?php foreach ($fields as $field) : ?>
                <li>
                    <span class="js-dbt-field-text"><?php echo htmlentities($field); ?></span> 
                </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<br>
