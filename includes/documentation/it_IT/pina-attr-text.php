<?php
/**
 * header-type:rif
 * header-title: [SHORTCODE] Attributi testo
 * header-description: Modifica dei testi
 * header-tags:upper, lower, ucfirst, strip-comment, strip-tags, nl2br, htmlentities, left, right, trim_words, sanitize, trim, Search (replace), length
 * header-package-title: Shortcode Attributes
 * header-package-link: pina-attr-index.php
 */
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <h2>upper</h2>
    <h4>Sinonimi: uppercase strtoupper</h4>
    <p>Trasforma una stringa tutta maiuscola</p>
    <pre class="dbt-code">[%"foo" upper]</pre>
    <div class="dbt-result">
        FOO
    </div>

    <h2>lower</h2>
    <h4>Sinonimi: strtolower lowercase</h4>
    <p>Trasforma una stringa tutta minuscola</p>
    <pre class="dbt-code">[%"MY FOO" lower]</pre>
    <div class="dbt-result">
        my foo
    </div>

    <h2>ucfirst</h2>
    <h4>Sinonimi: capitalize</h4>
    <p>Trasforma il primo carattere di una stringa in maiuscolo</p>
    <pre class="dbt-code">[%"my foo" ucfirst]</pre>
    <div class="dbt-result">
        My foo
    </div>

    <h2>strip-comment</h2>
    <h4>Sinonimi: strip_comment stripcomment</h4>
    <p>Rimuove i commenti &lt;!-- --&gt; o // o /* */ </p>
    <pre class="dbt-code">[^SET myvar=" &lt;div&gt;testo&lt;/div&gt;  &lt;!-- un commento--&gt; &lt;i&gt;testo&lt;/i&gt;"]
    [%myvar htmlentities]&lt;br&gt;
    [%myvar strip-comment htmlentities]
    </pre>
    <div class="dbt-result">
        &lt;div&gt;testo&lt;/div&gt; &lt;!-- un commento--&gt; &lt;i&gt;testo&lt;/i&gt;<br>
        &lt;div&gt;testo&lt;/div&gt; &lt;i&gt;testo&lt;/i&gt;
    </div>
    <pre class="dbt-code">[^SET myvar="&lt;script&gt; a =\&quot;foo\&quot;; 
    /* other comment 
    * multiline
    */
    alert(a);
    &lt;/script&gt;"]
    [%myvar htmlentities nl2br]&lt;br&gt;&lt;hr&gt;
    [%myvar strip-comment htmlentities nl2br]
    </pre>
    <div class="dbt-result">
        &lt;script&gt; a = &quot;foo&quot;;<br>
            /* other comment<br>
            * multiline<br>
            */<br>
            alert(a);<br>
        &lt;/script&gt;<br>
        &lt;script&gt; a = &quot;foo&quot;;<br>
    <br>
            alert(a);<br>
        &lt;/script&gt;<br>
    </div>

    <h2>strip-tags</h2>
    <h4>Sinonimi: strip_tags striptags</h4>
    <p>Rimuove tutti i tag html dal testo</p>
    <h2>nl2br</h2>
    <p>Trasforma gli accapi in br</p>
    <h2>htmlentities</h2>
    <p>Trasforma i caratteri speciali in entità html</p>
    <pre class="dbt-code">
    &lt;textarea&gt;[%&quot;&lt;/textarea&gt;&lt;b&gt;fff&lt;/b&gt;&quot; htmlentities]&lt;/textarea&gt;
    </pre>
    <p> l'esempio mostra come attraverso l'attributo htmlentities è possibile scrivere all'interno di una textarea dei tag html</p>

    <h2>left=</h2>
    <p>Accetta un parametro numerico. <br>Prende i primi n caratteri del testo. Accetta un secondo attributo "more" per aggiungere del testo se left ha effettivamente tagliato la stringa</p>
    <pre class="dbt-code">
        [%"A1B2C3D4E5F6G7H8I9" left=5 more=" ..."]
    </pre>
    <div class="dbt-result">
        A1B2C ...
    </div>
    <p>se il testo viene tagliato è possibile aggiungere un testo a fine riga utilizzando l'attributo more</p>
    <pre class="dbt-code">
    [%"Hello George" left=5 more=" ..."]
    [%"good afternoon" left=25 more=" ..."]
    </pre>
    <p>Nel primo caso taglia il testo e quindi mette il testo dell'attributo more, nel secondo caso non taglia il testo per cui non mette il testo del more</p>
    <div class="dbt-result">
        Hello ... good afternoon
    </div>
    <h2>right=</h2>
    <p>Accetta un parametro numerico. <br>Prende i primi n caratteri del testo</p>
    <pre class="dbt-code">
    [%"Hello George" right=6]
    </pre>
    <div class="dbt-result">
        George
    </div>

    <h2>trim_words=</h2>
    <p>Accetta un parametro numerico. <br>Prende le prime n parole del testo</p>
    <p>se il testo viene tagliato è possibile aggiungere un testo a fine riga utilizzando l'attributo more</p>
    <pre class="dbt-code">
    [%"Hello George how are you?" trim_words=2 more=" [^link id=2 text="..."]"]
    </pre>
    <div class="dbt-result">Hello George <a href="#">...</a></div>
    <h2>sanitize</h2>
    <p>Esegue la funzione wordpress sanitize_text_field</p>

    <h2>esc_url</h2>
    <p>Esegue la funzione wordpress esc_url</p>

    <h2>trim</h2>
    <p>Rimuove gli spazi prima e dopo in un testo o di tutti i campi di un array</p>

    <h2>Search=</h2>
    <p>Ritorna 1 se trova la sottostringa o 0 se non lo trova. </p>
    <pre class="dbt-code">[%"Nel mezzo del cammin di nostra vita" search="nostra" ]</pre>
    <div class="dbt-result">1</div>
    <p>Se viene invece passato il parametro replace sostituisce la stringa</p>
    <pre class="dbt-code">[%"Nel mezzo del cammin di notra vita" search="notra" replace="&lt;b&gt;nostra&lt;/b&gt;" ]</pre>
    <div class="dbt-result">Nel mezzo del cammin di <b>nostra</b> vita</div>

    <h2>if=</h2>
    <p>
    Mostra il campo se la condizione viene rispettata. La condizione la si può inserire tra virgolette oppure tra parentesi quadre con due punti [: ... :]
    </p>
    <pre class="dbt-code">
        [^POST type=post if=[: [%item.id]>30 :] length]
    </pre>
    <p>Conta il numero di articoli con id > 30</p

    <h2>set=</h2>
    <p>Imposta il valore di una variabile</p>
    <pre class="dbt-code">[%myvar set="foo"]</pre>
    <div class="dbt-result">
        foo
    </div>

    <p>set+= o set-= per sommare o sottrarre la variabile passata</p>
</div>