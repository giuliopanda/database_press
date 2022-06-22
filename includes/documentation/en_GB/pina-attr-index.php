<?php
/**
* header-type:rif
* header-title: [SHORTCODE] Attributes
* header-tags:Attributi upper lower ucfirst strip-comment
* header-description: Gestione degli attributi
 * header-package-title: Template Engine
 * header-package-link: pina-intro.php
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>All'interno dei campi del plugin spesso è possibile inserire degli shortcode speciali. Questi possono avere degli attributi</p>

    <p>Gli attributi sono elementi che modificano il risultato della funzione o della variabile che si sta usando. </p>
    <p>La loro sintassi è
    <pre class="dbt-code">[%"testo tutto maiuscolo"  strtoupper]
    </pre>
    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','pina-attr-advanced.php', $link); ?>" class="pina-doc-link">Metodi avanzati di scrittura degli attributi</a>
    <span>default, [: :], .*</span> 
    </div>

    <p>Di seguito l'elenco degli attributi utilizzabili nella maggior parte dei casi</p>
    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','pina-attr-text.php', $link); ?>" class="pina-doc-title">Modifica del testo</a>
    <span>upper, lower, ucfirst, strip-comment, strip-tags, nl2br, htmlentities, left, right, trim_words, sanitize, trim, Search (replace), length</span>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','pina-attr-date.php', $link); ?>" class="pina-doc-title">Gestione delle date</a>
    <span>date-format, date-modify, last-day, timestamp</span>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','pina-attr-array.php', $link); ?>" class="pina-doc-title">Gestione dei gruppi di dati (post, utenti)</a>
    <span>get, sep, qsep, if, mean, count, order_reverse</span>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','pina-attr-array.php', $link); ?>" class="pina-doc-title">Gestione dei numeri</a>
    <span>sep, qsep, if, mean, count</span>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','pina-attr-is.php', $link); ?>" class="pina-doc-title">is</a>
    <span>is_string, is_object, is_date</span>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','pina-attr-print.php', $link); ?>" class="pina-doc-title">print</a>
    <span>print, no_print, zero, one|singular, plural, negative, empty </span>
    </div>
</div>