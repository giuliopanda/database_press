<?php
/**
 * header-type:doc
 * header-title: Edit the extracted data
 * header-tags: edit columns, column
 * header-description: Once you have saved a query, you can change the view of the data from the List view formatting tab
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p> the list presents all the columns that are extracted from the table. You can change the display order, choose to hide a column or change how the data is displayed. </p>
     <p> You can add new columns by working the extracted data through the template engine, but if you want to extract more data you will need to modify the extraction query </p>

    <div id="dbt_help_title" class="dbt_help_div">
        <h4>Table title</h4>
        <p> The title that the column will have does not affect the data or the names of the data extracted from the template engine </p>
    </div>
    <div id="dbt_help_searchable" class="dbt_help_div">
        <h4>Searchable</h4>
        <p> When you use the search field it will search all columns for who a search type has been chosen. LIKE means that it searches within the text while = will only search for columns that match the searched text. </p>
    </div>
    <div id="dbt_help_print" class="dbt_help_div">
        <h4>Column type</h4>
        <p> Changes the displayed text according to the chosen format. Custom allows you to use shortcodes to display the contents of the column. From the Help you can click vars to see the list of variables to use </p>
    </div>
    <div id="dbt_help_user" class="dbt_help_div">
        <p>The <b> User </b> type displays the username starting with the ID. If you want to show other user fields you can use the template engine shortcodes in 'Show user attributes'. </p>
        <pre class="dbt-code">[%user.user_login], [%user.user_email]</pre>
    </div>
    <div id="dbt_help_post" class="dbt_help_div">
        <p>The <b> Post </b> type displays the title of a post starting with the ID. If you want to show other post fields you can use the template engine shortcodes in 'Show post attributes'.</p>
        <pre class="dbt-code">&lt;a href=&quot;[^LINK id=[%post.ID]]&quot;&gt;[%post.post_title]&lt;/a&gt;</pre>
    </div>
    <div id="dbt_help_format" class="dbt_help_div">
        <h3>column formatting</h3>
        <h4>change values</h4>
        <p> Change the content value according to the entered csv </p>
         <p> The csv values must be separated by commas. The first value is that of the column, the second is how it should be transformed </p>
         <p> You can use the special scripts <b> &lt;x, &gt;x, OR =x-y </b> for a range, where x and y are numbers. </p>
         example:
        <pre class="dbt-code">
    0, NO
    1, YES
    >1, MAYBE
        </pre>
    </div>
    <div id="dbt_help_styles" class="dbt_help_div">
        <h4>change styles</h4>
        <p> Adds a conditional class depending on the value of the csv inserted </p>
         <p> You can use the special writes <b> &lt;x, &gt;x, OR =x-y </b> for a range, where x and y are numbers. <br>
         here is the list of classes already configured:
            <ul>
                <li>dbt-cell-red</li>
                <li>dbt-cell-yellow </li>
                <li>dbt-cell-green</li>
                <li>dbt-cell-blue</li>
                <li>dbt-cell-dark-red</li>
                <li>dbt-cell-dark-yellow </li>
                <li>dbt-cell-dark-green </li>
                <li>dbt-cell-dark-blue</li>
                <li>dbt-cell-text-red </li>
                <li>dbt-cell-text-yellow </li>
                <li>dbt-cell-text-green</li>
                <li>dbt-cell-text-blue</li> 
            </ul>
        </p>
        example: 
        <pre class="dbt-code">
    0, dbt-cell-red
    =1-10, dbt-cell-green
        </pre>
    </div>
</div>