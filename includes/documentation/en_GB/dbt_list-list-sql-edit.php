<?php
/**
 * header-type:doc
 * header-title: List Tab Settings
* header-tags:
* header-description: Define the query to be executed, who can modify the list, etc.
* header-package-title: Manage List
* header-package-link: manage-list.php
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <div id="dbt_help_admin_sidebar_menu" class="dbt_help_div">
        <h3>Admin sidebar menu</h3>
        Add a menu item in your administration and decide who can access. <br> If you don't see the settings change immediately, reload the page.
    </div>

    <div id="dbt_help_admin_sidebar_menu_icon" class="dbt_help_div">
        <h4>Add Icon</h4> 
        <p>You can choose an icon from those on this page: <a href="https://developer.wordpress.org/resource/dashicons" target="_blank" class="js-simple-link">developer.wordpress.org/resource/dashicons</a> Clicca sull'icona che vuoi inserire e premi copia HTML. Nell'alert che apparirà prendi la seconda classe. es: <i>dashicons-image-rotate-right</i>. Copia la classe nel campo. </p>
    </div>

    <div id="dbt_help_admin_sidebar_menu_position" class="dbt_help_div">
        <h4>Position (number)</h4> 
        <p>Choose at what height to show the menu item.</p>
    </div>

    <div id="dbt_help_admin_sidebar_menu_permissions" class="dbt_help_div">
        <h4>Permissions</h4> 
        <p>Choose who can edit the list. Among the permissions there is also the administrator. You will always be able to view and edit the list within the plugin, but you can choose not to show it in the menu. </p>
    </div>

    <div id="dbt_help_admin_query" class="dbt_help_div">
        <h3>Query</h3>
        <p> You can choose to extract all data from a table, or filter it by adding WHERE clauses. You can extract only some fields or add calculated fields. Some more complex queries may not work correctly. If you want to link other tables use the LEFT JOIN ... ON clause. </p>
    </div>

    <div id="dbt_help_admin_filter" class="dbt_help_div">
        <h3>Filter</h3>
        <p> Adds a filter when the list receives a certain parameter. </p>
        <p> Parameters are written as shortcodes and are: </p>
        
        <ul>
            <li><b>[%params.xxx]</b> are the parameters added in the shortcodes</li>
            <li><b>[%request.xxx]</b> for the data received in the url</li>
        </ul> 
        <p>If required is selected but the value is not passed, the query returns no results. If required is not selected and no parameters are passed, the query returns the unfiltered results</p>
    </div>

    <div id="dbt_help_delete_options" class="dbt_help_div">
        <h3>DELETE OPTIONS</h3>
        <p>If there is only one table, choose whether or not records can be deleted from the list. <br> If the query extracts multiple tables, choose which ones should be removed when deleting a file. <br> If you select no to all tables, then the records cannot be removed in the list.</p>
    </div>
    
</div>