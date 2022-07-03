<?php
/**
*  header-type:rif
 * header-title: [SHORTCODE] Gestire le date
 * header-description: Gestione delle date
 * header-tags:date-format date-modify last-day timestamp
 * header-package-title: Shortcode Attributes
 * header-package-link: pina-attr-index.php
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
  
    <h2>date-format=</h2>
    <p>Accetta un parametro di testo. <br> Cambia il formato della data</p>
    <pre class="dbt-code">
    [%"2020-10-10" date-format='Y']
    </pre>
    <div class="dbt-result">
        2020
    </div>
    <p>Accetta sia date, timestamp o stringhe anno mese giorno tutto attaccato o anche con orario</p>
    <pre class="dbt-code">
    [%"1602288000" date-format="Y-m-d"]
    </pre>
    <div class="dbt-result">
    2020-10-10
    </div>

    <h2>date-modify=</h2>
    <p>Accetta un parametro di testo. <br>Modifica una data</p>
    <pre class="dbt-code">
    [%"2020-10-10" date-modify="+2 days"]
    </pre>
    <div class="dbt-result">
        2020-10-12
    </div>

    <h2>last-day</h2>
    <p>Prende una data e imposta l'ultimo giorno del mese</p>
    <pre class="dbt-code">
    [%"2020-10-10" last-day]
    </pre>
    <div class="dbt-result">
        2020-10-31
    </div>

    <h2>timestamp</h2>
    <p>Prende una data e la converte in un timestamp</p>
    <pre class="dbt-code">
    [%"2020-10-10" timestamp]
    </pre>
    <div class="dbt-result">
        2020-10-31
    </div>


    <h2>datediff-year=</h2>
    <p>Ritorna la differenza in anni tra due date </p>
    <h2>datediff-month=</h2>
    <p>Ritorna la differenza in mesi tra due date </p>
    <h2>datediff-day=</h2>
    <p>Ritorna la differenza in giorni tra due date </p>
    <pre class="dbt-code">
        Sono passati: [%a ='2001-10-04 10:20:10' datediff-day='2001-09-02 10:30:00']   
    </pre>
    <h2>datediff-hour=</h2>
    <p>Ritorna la differenza in ore tra due date </p>
    <h2>datediff-minute=</h2>
    <p>Ritorna la differenza in minuti tra due date </p>
</div>