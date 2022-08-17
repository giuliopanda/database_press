<?php
/**
 * header-type:doc
 * header-title: Modificare i dati estratti
* header-tags:
* header-description: Una volta salvata una query è possibile modificare la visualizzazione dei dati dal tab List view formatting
* header-lang:ITA
*/

namespace DatabaseTables;
if (!defined('WPINC')) die;

?>
<div class="dbt-content-margin">
    <p>la lista presenta tutte le colonne che vengono estratte dalla tabella. Puoi cambiare l'ordine di visualizzazione,  scegliere di nascondere una colonna o modificare come i dati vengono visualizzati.</p>
    <p> Puoi aggiungere nuove colonne lavorando i dati estratti attraverso il template engine, ma se vuoi estrarre altri dati dovrai modificare la query di estrazione</p>

    <div id="dbt_help_title" class="dbt_help_div">
        <h4>Table title</h4>
        <p>Il titolo che avrà la colonna, non infuenza i dati o i nomi che dei dati estratti dal template engine</p>
    </div>
    <div id="dbt_help_searchable" class="dbt_help_div">
        <h4>Searchable</h4>
        <p>Quando usi il campo di ricerca  questo cercherà in tutte le colonne in chi è è stato scelto un tipo di ricerca. LIKE vuol dire che cerca all'interno del testo mentre = cercherà solo le colonne uguali al testo cercato.</p>
    </div>
    <div id="dbt_help_print" class="dbt_help_div">
        <h4>Column type</h4>
        <p>Modifica il testo visualizzato a seconda del formato scelto. Custom permette di usare gli shortcode per visualizzare il contenuto della colonna. Dall'Help puoi cliccare vars per vedere la lista delle variabili da usare</p>
    </div>
    <div id="dbt_help_user" class="dbt_help_div">
        <p>Il tipo <b>User</b> mostra il nome utente a partire dall'ID. Se vuoi mostrare altri campi dell'utente puoi usare gli shortcode del template engine dentro 'Show user attributes'. </p>
        <pre class="dbt-code">
            [%user.user_login], [%user.user_email]
        </pre>
    </div>
    <div id="dbt_help_post" class="dbt_help_div">
        <p>Il tipo <b>Post</b> mostra il titolo di un post a partire dall'ID. Se vuoi mostrare altri campi del post puoi usare gli shortcode del template engine dentro 'Show post attributes'. </p>
        <pre class="dbt-code">
            &lt;a href=&quot;[^LINK id=[%post.ID]]&quot;&gt;[%post.post_title]&lt;/a&gt;
        </pre>
    </div>
    <div id="dbt_help_format" class="dbt_help_div">
        <h3>column formatting</h3>
        <h4>change values</h4>
        <p>Cambia il valore del contenuto secondo il csv inserito</p>
        <p>I valori del csv devono essere separati da virgola. Il primo valore è quello della colonna, il secondo è come deve essere trasformato</p>
        <p>È possibile usare le scritture speciali <b>< x, > x, o =x-y</b> per un intervallo, dove x e y sono numeri.</p>
        esempio: 
        <pre class="dbt-code">
    0, NO
    1, YES
    >1, MAYBE
        </pre>
    </div>
    <div id="dbt_help_styles" class="dbt_help_div">
        <h4>change styles</h4>
        <p>Aggiunge una classe condizionata a seconda del valore del csv inserito</p>
        <p>È possibile usare le scritture speciali <b>< x, > x, o =x-y</b> per un intervallo, dove x e y sono numeri.<br>
        ecco l'elenco delle classi già configurate:
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
        esempio: 
        <pre class="dbt-code">
    0, dbt-cell-red
    =1-10, dbt-cell-green
        </pre>
    </div>
</div>