<?php
/**
 * header-type:doc
 * header-title: Information schema
 * header-order: 01
 * header-tags:Information schema, delete table cancel remove
 * header-description: Mostra tutte le informazioni sulle tabelle e come queste sono costruite. Da qui è possibile  creare nuove tabelle, svuotare il contenuto o eliminare una tabella 
 * header-package-title: Manage DB
 * header-package-link: manage-db.php
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>Mostra tutte le tabelle del database. Le tabelle di wordpress hanno un prefisso che le identifica. Non tutte le tabelle possono essere modificate, questo dipende dalla loro.</p>
    <p>Solo le tabelle in <b>DRAFT</b> modo possono essere eliminate o svuotate. Lo <b>stato di una tabella</b> è una caratteristica aggiunta da questo plugin e che funziona solo all'interno del plugin stesso.</p>
    <p>Questo serve a prevenire cancellazioni accidentali di una tabella o del suo contenuto.</p>
    <p>Puoi modificare lo stato di una tabella cliccando sul titolo e successivamente sul tab "structure".
</p>