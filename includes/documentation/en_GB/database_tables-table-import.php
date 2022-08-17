<?php
/**
* header-type:doc
* header-title: Import
 * header-order: 03
* header-description: How to import sql or csv data
* header-tags:Import, sql, csv
* header-package-title: Manage DB
* header-package-link: manage-db.php
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>From the import tab it is possible to import sql statements or data series in csv.</p>
    <div id="dbt_help_sql" class="dbt_help_div">
        <p>The sql files are executed without doing any checks on the data.</p>
    </div>
    <div id="dbt_help_csv" class="dbt_help_div">
        <h3> Enter or update data via CSV </h3>
        <p> Be careful to load the sql or csv files correctly in their respective input forms. </p>
    </div>
    <div id="dbt_help_delimiter" class="dbt_help_div">
        <ul>
            <li> <b>Delimiter </b> is the character used to divide the column data in the csv </li>
             <li> <b>Use first row as Headers </b> If checked the first row will not be imported </li>
             <li> <b>Update Preview </b> Updates your chosen settings. </li>
        </ul>
    </div>
    <div id="dbt_help_choose_action" class="dbt_help_div">
        <h3>Choose Action</h3>
        <div id="dbt_help_create_table" class="dbt_help_div">
            <h4> Create table </h4>
             <p> Create a table in the database starting from the columns of the csv. You will choose later which fields to associate. </p>
        </div>
        <div id="dbt_help_insert_record" class="dbt_help_div">
            <h4> Insert / Update records </h4>
             <p> Select the table and link the fields of the csv to insert. If you associate the primary key with a field, if it exists it will update the row, otherwise it will create a new record. </p>
             <p> You can select multiple tables and choose which csv columns to insert in one or the other table. Each time a column is inserted or updated, a field is generated that you can insert in the next table to create a relationship between the two tables. </p>
             <p> If you want to modify a field you are inserting, after selecting it in the associations table, change the selection to [custom text]. The related shortcode will appear. At this point you can use all the instructions of the <a href="<?php echo add_query_arg('get_page','pina-intro.php', $link); ?>">integrated template engine</a>.</p>
        </div>  
    </div>
    <h3>Test the import</h3> 
    <p> Generates and runs an import test on temporary tables and displays the result. Check it carefully to avoid any unpleasant surprises when you import the data. If a column does not appear to contain the expected data, perhaps you are trying to insert an incorrect data type (such as a number in a date field). </p>
     <h3> I feel lucky, import the data </h3>
     <p> Import the data. Once the operation has started, there is no going back. Once the import is complete, download the report, it will contain the rows of the csv with new columns with the ids associated with the import and the result of the queries. </p>
</div>