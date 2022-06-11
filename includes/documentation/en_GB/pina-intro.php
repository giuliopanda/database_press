<?php
/**
* header-type:doc
* header-title: TEMPLATE ENGINE
* header-description: Il template engine integrato può essere usato sia per modificare i dati delle tabelle come ad esempio nei campi calcolati, sia per generare template personalizzati nelle liste.
* header-tags:template engine [% [^ shortcode short-code template-engine
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>Il template engine integrato può essere usato sia per modificare i dati delle tabelle come ad esempio nei campi calcolati, sia per generare template personalizzati nelle liste.</p>
    
    <p>È possibile usare le funzioni del template engine anche all'esterno del plugin inserendo il codice tra gli shortcode [dbt_tmpl_engine] {my code} [/dbt_tmpl_engine]</p>
   
    <h3>Le variabili</h3>
    <p>Le variabili vengono richiamate con shortcode che iniziano per %. Le funzioni invece ocn shortcode che iniziano con ^</p>
    <pre class="dbt-code">[%myvar]</pre>

    <h3>LA struttura del linguaggio</h3>
    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','structure-if.php', $link); ?>" class="pina-doc-title">[^IF] ... [^ELSE] ... [^ENDIF]</a>
    <pre class="dbt-code">
    [^IF 2 < 5]
        due è minore di cinque
    [^ENDIF]
    </pre>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','structure-for.php', $link); ?>" class="pina-doc-title">[^FOR]...[^ENDFOR]</a>
    <span>Lo si usa per ciclare degli oggetti</span>
    <pre class="dbt-code">&lt;ul&gt;
        [^FOR EACH=[^POST TYPE=post]]
        &lt;li&gt;[%item.title_link]&lt;/li&gt;
        [^ENDFOR]
    &lt;/ul&gt;
    </pre>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','structure-while.php', $link); ?>" class="pina-doc-title">[^WHILE] [^ENDWHILE]</a>
    <pre class="dbt-code">
    [^WHILE [%var set+=1] <= 10]
    [%var][%',' if=[:[%var] < 10:]]
    [^ENDWHILE]
    </pre>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','structure-break.php', $link); ?>" class="pina-doc-title">[^BREAK]</a>
    <span>Interrompe un ciclo</span>
    <pre class="dbt-code">
    [^WHILE [%var set+=1] < 10]
        [%var]
        [^BREAK [%var]>5],
    [^ENDWHILE]
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','structure-break.php', $link); ?>" class="pina-doc-title">[// comment //]</a>
    <span>Inserisce un commento</span>
    </div>


    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','structure-if.php', $link); ?>" class="pina-doc-title">[^BLOCK] ... [^ENDBLOCK]</a>
    <span>Inserisce l'html contenuto tra i blocchi in una variabile senza eseguire eventuali altre istruzioni</span>
    <pre class="dbt-code">[^BLOCK myblock]
        [%var]
    [^ENDBLOCK]
    [^SET var="pippo"]
    [:[%myblock]:]</pre>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','structure-set.php', $link); ?>" class="pina-doc-title">[^SET] </a>
    <span>Imposta una variabile</span>
    <pre class="dbt-code">[^SET myvar="foo" myarray=["31","42","83","12"]][%myvar]&lt;br&gt;[%myarray.0]</pre>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','structure-math.php', $link); ?>" class="pina-doc-title">[^MATH] </a>
    <span>Esegue un'espressione all'interno del codice</span>
    <pre class="dbt-code"> [^MATH 3 + 1 + .5] [// 4.5 //]</pre>
    </div>

    <div class="pina-doc-block">
    <a href="<?php echo add_query_arg('get_page','', $link); ?>" class="pina-doc-title">[^RETURN]</a>
    <span>Interrompe l'esecuzione di un blocco cancella qualsiasi dato stampato e ritorna il valore impostato
    <pre class="dbt-code">
    [^SET a=[:
    [^while [%x set+=1] < 1000]
        xxxxxxxx
    [^RETURN [%[3,4]]]
    [^ENDWHILE]
    :]][%a sep=-]</pre>
    </span>
    </div>

    <h3> Approfondimenti</h3>
  <?php Dbt_fn_documentation::echo_menu('Template Engine'); ?>
</div>