<?php
/**
 * header-type:doc
 * header-title: List Tab Frontend
 * header-tags: list frontend
 * header-description: Choose how to show data in your site posts
 * header-package-title: Manage List
 * header-package-link: manage-list.php
 */
namespace DatabasePress;
if (!defined('WPINC')) die;
?>
<div class="dbp-content-margin">
    <div id="dbp_help_list_of_records" class="dbp_help_div">
        Each list can be viewed in the frontend through a shortcode, or called from php code. You can in fact call the filter<a class="js-simple-link" href="<?php echo admin_url("admin.php?page=dbp_docs&section=hooks") ?>" target="_blank">dbp_frontend_get_list</a> to redesign its content.<br>
        There are two types of views that you can generate, either a table or you can draw your own view through the <a class="js-simple-link" href="<?php echo admin_url("admin.php?page=dbp_docs&section=pinacode") ?>" target="_blank">integrated template engine</a>.
    </div>
    <div id="dbp_help_show_if" class="dbp_help_div">
        <h4>IF</h4>
        <p>
            You can decide to show data only if a condition is true.
            Conditions are written through template engine variables.
            Some examples:
            <pre class="dbp-code">[%total_row] > 0</pre>
            <pre class="dbp-code">[^IS_USER_LOGGED_IN] == 1</pre>
        </p>
        <h4>ELSE</h4>
        <p>If the IF condition is not satisfied, it shows the else field. </p>
    </div>
    <div id="dbp_help_loop_data" class="dbp_help_div">
        <h4>Loop the data</h4>
        <p>Enter the html to generate a customized view of the data. you can print the data using the instructions of the <a class="js-simple-link" href="<?php echo admin_url("admin.php?page=dbp_docs&section=pinacode") ?> "target =" _ blank "> template built-in engine </a> [% data.column_name]. Variables can be added to a number of attributes such as all caps or the date format. The block is repeated as many times as the query results set in the tab setting.</p>
    </div>
    <div id="dbp_help_update" class="dbp_help_div">
        <h4>Table Update</h4>
        <p>If pagination or search is entered, the method by which the data is updated is defined here. If you have no particular preferences you can select ajax.</p>
    </div>

    <div id="dbp_help_detail" class="dbp_help_div">
        <h4>Detailed view</h4>
        <p> If you want to show the detail of a row, you can create a popup with the data of the single row. <br> You can draw the html below using <span onclick="show_pinacode_vars()" class="dbp-link-click"> [%data.column_name] </span> to retrieve the variables to be printed. <a href="<?php echo admin_url("admin.php?page=dbp_docs&section=pinacode") ?> "> You can find out more in the guide </a>. <br>
         Once the detail page has been created, go to the "list view formatting" tab and assign the <b>Detail link</b> option within the select Column type to a column. </p>
    </div>

</div>