<?php
/**
 * header-type:rif
 * header-title: [SHORTCODE] Attributi per i numeri
 * header-tags:decimal floor ceil sum around set+ set-
 * header-description: Gestione delle date
 * header-package-title: Shortcode Attributes
 * header-package-link: pina-attr-index.php
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>set+= o set-= per sommare o sottrarre la variabile passata</p>
    <h2>decimal=</h2>
    <p>Imposta il numero di valori dopo la virgola da mostrare. Accetta altri due parametri dec_point e thousands_sep</p>
    <pre class="dbt-code">
    [%"1203.23" decimal=1]&lt;br&gt;
    [%"1203.23" decimal=1 dec_point=, thousands_sep=.]
    </pre>
    <div class="dbt-result">
    1203.2
    1.203,2
    </div>
    <h2>euro</h2>
    <p>Formatta un numero come valuta euro</p>
    <h2>floor</h2>
    <p>Arrotonda per difetto il valore di un numero</p>
    <h2>round</h2>
    <p>Arrotonda il valore un numero</p>
    <h2>ceil</h2>
    <p>Arrotonda per eccesso il valore di un numero</p>

    <h2>sum</h2>
    <p>Fa la somma di un vettore</p>
    <p>TODO può essere passato un parametro aggiuntivo per cui fa la somma di un campo di un oggetto (ad esempio age, fa le somme dell'età degli utenti)</p>

    <h2>mean</h2>
    <p>Fa la media matematica</p>
</div>