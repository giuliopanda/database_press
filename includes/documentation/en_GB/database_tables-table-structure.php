<?php
/**
* header-type:doc
* header-title: Table structure
* header-order: 02 
* header-description: Changes the structure or state of a table.
* header-tags:Import, sql, csv, alert, create, add, structure, add, field, column, state, table
* header-package-title: Manage DB
* header-package-link: manage-db.php
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;

?>
<div class="dbt-content-margin">
    <p>Changes the structure of the table. You can only edit tables that meet the following rules: <br>
    1. The table is in state DRAFT.<br>
    2. the table has a single column auto_increment primary key.<br>
    <p>You can change the status of a table whenever you want by clicking on "Edit Status & Description".</p>
    <p>The data that is stored in the tables are numbers, texts or dates. If you want to link a column with a user or post the field will be a number and will contain the id of the linked user or post. For wordpress titles, emails and links you can choose to create a line of text instead.</p>
    <div id="dbt_help_status" class="dbt_help_div">
        <h3>Status</h3>
        <p>This concept is not unique to mysql, but was added to ensure greater data security. A table in <b> DRAFT </b> state can be edited or deleted. </p>
         <p> In the <b> PUBLISHED </b> state, a table can no longer be modified or deleted. In addition, the function to delete all data is disabled. </p>
         <p> The <b> CLOSE </b> state does not allow either the modification of the table or the insertion of data. </p> <p> This state is useful for tables whose data must remain unchanged or can only be modified by the administrator.</p>
    </div>
    <div id="dbt_help_indexes" class="dbt_help_div">
        <h3>Indexes</h3>
        <p>The indices are mainly of two types:</p>
        <p><b>Unique</b> it is used to indicate that one or a group of columns must not repeat the same on more than one row</p>
        <p><b>INDEX</b> they are used to speed up the queries in which data is filtered for one or more columns</p>
        <p>For example, if the table you are building is always filtered for a "category" column, you may want to add a "category" index to improve performance. </p> <p> The indexes however increase the time it takes for the database to save o modify a data and the space used.</p> 
    </div>
</div>
