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
namespace DatabasePress;
if (!defined('WPINC')) die;
dbp_fn::require_init();
$all_columns = dbp_fn::get_all_columns();
?>
<div class="dbp-content-margin">

    <p>List of Database Press and columns</p>

    <div class="dbp-form-row">
    <input type="text" class="form-input-edit " onkeyup="dbp_help_filter(this)" data-idfilter="dbpHelpListTableFields" style="width: 99%;">
    </div>
</div>

<div class="dbp-help-table-list">
    <ul class="dbp-help-table-list-ul" id="dbpHelpListTableFields">
        <?php foreach ($all_columns as $table=>$fields) : ?>
        <li>
            <div class="dbp-help-table-name js-dbp-table-text"><?php echo $table; ?></div>
            <ul class="dbp-help-table-list-fields">
                <?php foreach ($fields as $field) : ?>
                <li>
                    <span class="js-dbp-field-text"><?php echo htmlentities($field); ?></span> 
                </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<br>
