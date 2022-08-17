<?php 
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-table dbt-docs-content  js-id-dbt-content" >
    <h2><?php _e('Database Tables document','database_tables'); ?></h2>
    <div class="dbt-help-p">
    Database tables allows you to view and modify the data extracted from a mysql query.
     <br>
     Starting from a query, a list allows you to manage the modification and display of data in a more advanced way. <br>
     A shortcode for displaying the frontend is generated from a list.</div>
    
     <h3>Shortcodes</h3>
    <p>To print the graphics of a list you can use the shortcode:</p>
    <p><b>[dbt_list id=list_id]</b> where id is the id of the list. If you want to display more lists within the same page that derive from the same list you can set the prefix attribute with a unique short code like prefix = "abc".
    If in the tab setting you have set filters [%params.xxx] you can pass them in the list to further filter the results. Example:<br>
    [dbt_list id=list_id xxx=23]
    </p>
    <p><b>[dbt_tmpl_engine]</b> To run the custom template engine</p>

    <h3>Template Engine</h3>
        <div class="dbt-help-p">You can modify the data you see through an integrated template engine <br>
        The integrated template engine can be used both to modify table data such as calculated fields, and to generate custom templates in lists. <br> It is possible to use the functions of the template engine also outside the plugin by inserting the code between shortcodes<b>[dbt_tmpl_engine] {my code} [/dbt_tmpl_engine]</b>
        <br><br>
        <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=pinacode") ?>">Learn more</a>
    </div>

    <h3>Form Javascript</h3>
        <div class="dbt-help-p">In the management of insertion forms you can use javascript to manage special actions in fields such as making a field appear or disappear or validate its content.<br><br>
        <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=js-controller-form") ?>">Approfondisci</a>
    </div>

    <h3>Hooks & filters</h3>
    <div class="dbt-help-p">Change plugin behavior directly from code.<br><br>
        <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=hooks") ?>">Learn more</a>
    </div>

    <h3>PHP</h3>
    <div class="dbt-help-p">Develop using program functions directly.<br><br>
        <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=code-php") ?>">Learn more</a>
    </div>

    <h3>Tutorials</h3>
    <div class="dbt-help-p">
    <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=tutorial_01") ?>">Related post</a>
    </div>
</div>