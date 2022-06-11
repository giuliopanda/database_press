<?php
/**
 * header-type:doc
 * header-title: List Tab Frontend
 * header-tags:
 * header-description: Scegli come mostrare i dati nel front-end.
 * header-package-title: Manage List
 * header-package-link: manage-list.php
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <div id="dbt_help_admin_sidebar_menu" class="dbt_help_div">
        Ogni lista può essere visualizzata nel frontend attraverso uno shortcode, o richiamata da codice php. 
    </div>
    <h3>IF</h3>
    <p>
    Puoi decidere di mostrare i dati solo se una condizione è vera.
    es:
    <pre class="dbt-code">
[%total_row] > 0</pre>
</p>
    <h3>ELSE</h3>
    <p>
    Se la condizione IF non è soddisfatta mostra il campo else. 
    <h3>List type</h3>
    <p>Sceglie se mostrare la grafica come tabella o disegnando i dati attraverso il template engine</p>
    </p>
</div>