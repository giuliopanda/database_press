<?php
/**
 * header-type:doc
 * header-title: Table Browse
 * header-order: 04
 * header-tags:sql browse query filtra merge, meta  data metadata edit inline create list.
 * header-description: Show data from a query. You can filter them modify the data, export them or save the query thus starting the creation of a new list.
 * header-package-title: Manage DB
 * header-package-link: manage-db.php
 */

namespace DatabasePress;
if (!defined('WPINC')) die;

?>
<div class="dbp-content-margin">
<p>
La pagina browse mostra i risultati di una query. A partire da questi risultati è possibile:
modificare i dati, inserirne di nuovi o rimuoverli.<br>
RIcordati che ogni azione interviene su tutte le tabelle che sono state interessate nella query.</p>

<h4> The buttons below the query </h4>
<p> <b> GO </b>: Executes the entered query or refreshes the page if it has not been modified.
</p>
<p> <b> EDIT INLINE </b>: Allows you to edit the query </p>
<p> <b> ORGANIZE COLUMNS </b>: Reads the query that has been written so far and allows you to manage the SELECT section. You can choose which fields to display and in what order. Once you have changed the order you have to execute it again by pressing the go button. </p>
<p> <b> MERGE </b>: Link two queries across a field (e.g. post_id or user_id). </p>
<p> <b> ADD META DATA </b>: If the system finds a table with the name of the original table + meta and with the typical structure of the metadata (primary_key, table_id, meta_key, meta_value) then it will allow to link the metadata . </p>

<p> <b> SEARCH </b>: allows you to search among all the extracted columns of a query. It is also possible to replace data, keeping the structure of the serialized data. <br> Replace is also based on the query and not on the table so it is possible to replace on multiple tables and only on the columns displayed. </p>

<h4> BULK ACTIONS </h4>

<p> You can download the results of queries or selected fields using the bulk function found at the end of the table. </p>
<p> You can also delete multiple fields or all those extracted from the query. <br> Always pay attention that if you are doing an extraction from multiple tables, you will be asked to delete the data from all the tables. however, you can always choose which tables to delete the data from. </p>

</div>