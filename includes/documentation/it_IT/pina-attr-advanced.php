<?php
/**
* header-type:rif
* header-title: [SHORTCODE] Attributi avanzati
* header-description: scrittura degli attributi
* header-package-title: Shortcode Attributes
* header-package-link: pina-attr-index.php
* header-tags:default print [: :] [:] attributi avanzati
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <p>Gli attributi modificano una variabile o una funzione. Possono avere un valore associato.</p>
    <p><b>NON SI può mettere lo spazio accanto al simbolo =.</b></p>
    <pre class="dbt-code">
        [%"string" uppercase]
    </pre>
    <div class="code">
        [%"1602288000" date-format="Y-m-d"]
    </div>

    <h2>default=</h2>
    <p>Se il valore o l'oggetto è vuoto restituisce il default</p>
    <pre class="dbt-code">
    [%novar default="foo"]
    </pre>

    <h2>*=[::]</h2>
    <p>I valori degli attributi non devono avere spazi. </p>
    <p>Se si deve mettere uno spazio bisogna inserire il testo tra virgolette oppure i simboli [: :]. Le virgolette all'interno di un testo già virgolettato devono essere aggiunte col backslash</p>
    <pre class="dbt-code">
    [%myvar default=foobar] [// corretto //]
    [%myvar default=foo bar] [// non corretto //]
    </pre>

    <pre class="dbt-code">
    [%myvar default=[:foo bar:]] [// corretto //]
    [%myvar default="foo bar"] [// corretto //]
    [%myvar default='foo bar'] [//  corretto //]
    </pre>

    <p>Si possono aggiungere variabili o json come valori degli attributi</p>
    <pre class="dbt-code">
    [%myvar =[^POST last]] 
    [%myvar =["foo","bar"]]
    [%myvar ={"a":"foo","b":"bar"}]
    </pre>
    <p>Non si possono mettere variabili al posto dei nomi degli attributi</p>
    <pre class="dbt-code">
    [%myvar [%var]="foo"] [// NON CORRETTO //] 
    </pre>
    <p>Questo è permesso solo dentro la funzione [^SET ] purché non ci siano spazi</p>
    <pre class="dbt-code">
    [^SET [%var]="foo"] [// CORRETTO //] 
    [^SET mypost.[%var]="foo"] [// CORRETTO //] 
    </pre>

    <p>Si possono chiamare i parametri di un oggetto tramite il .*</p>
    <pre class="dbt-code">
    [%post.title] 
    [%post.0.title] 
    </pre>
    <p>Questo ritornerà una stringa se c'è un solo post, altrimenti un array di titoli.</p>
</div>