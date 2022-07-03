<?php
/**
 * header-type:doc
 * header-title: Information schema
 * header-order: 01
 * header-tags:Information schema, delete table cancel remove
 * header-description: Show The list of tables in the database used by wordpress. From here you can create new tables, empty or delete a table or download its contents.
 * header-package-title: Manage DB
 * header-package-link: manage-db.php
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p> Show all database tables. Wordpress tables have a prefix that identifies them. Not all tables can be modified, this depends on their state. </p>
     <p> The <b> state of a table </b> is a feature added by this plugin that only works within the plugin itself. </p>
     <p> This is to prevent accidental changes to a table or its contents. </p>
     <p> You can change the status of a table by clicking on the title and then on the "structure" tab. </p>
    <ul>
        <li> <b> DRAFT </b>: Allows any operation. You can change the structure of the table, delete its contents or delete it. </li>
         <li> <b> PUBLISH </b>: You can manage the contents of a table, but not edit it. It is no longer allowed to delete the table </li>
         <li> <b> CLOSE </b>: You can no longer edit the table data, you can only view it </li>
    </ul>
    <br>
    There is a hook to change state from code. Through this hook the wordpress tables are blocked in the publish state. 
    <a class="js-simple-link" href="<?php echo admin_url("admin.php?page=dbt_docs&section=hooks") ?>" target="_blank">Learn more: Hooks & filters</a>
</div>