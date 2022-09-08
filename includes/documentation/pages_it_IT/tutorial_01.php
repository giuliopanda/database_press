<?php 
namespace DatabasePress;
if (!defined('WPINC')) die;
?>

<div class="dbp-content-table dbp-docs-content  js-id-dbp-content dbp-tutorial"  >
    <h2 class="dbp-h2"> <a href="<?php echo admin_url("admin.php?page=dbp_docs") ?>">Docs </a><span class="dashicons dashicons-arrow-right-alt2"></span> Tutorials <span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('Related post','database_press'); ?></h2>
    <p>In questo tutorial ti mostrerò come creare una tabella che visualizzi gli  articoli che abbiano almeno un tag in comune con l'articolo che si sta visualizzando. <br>Per prima cosa dobbiamo creare alcuni articoli che abbiano gli stessi tag. Ad esempio puoi creare una serie di articoli così composti:</p>
    <table class="dbp-tutorial-table">
        <tr><td>Post title</td><td>Tag</td></tr>
        <tr><td>Venice</td><td>Italy</td></tr>
        <tr><td>Rome</td><td>Italy, Capital</td></tr>
        <tr><td>Milan</td><td>Italy</td></tr>
        <tr><td>Paris</td><td>France, Capital</td></tr>
        <tr><td>Marseille</td><td>France</td></tr>
    </table>

    <h2 class="dbp-h2">Creazione della lista</h2>
    <p>In database_press nel menu di destra dentro Database Press actions clicchiamo sul link "SQL Command".</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_01_01.png" class="dbp-tutorial-img">

    <p>e scriviamo la seguente query:</p>
    <pre class="dbp-code">SELECT `p`.`ID`, `p`.`post_title` FROM `wps_posts` p LEFT JOIN `wps_term_relationships` tr ON `tr`.`object_id` = `p`.`ID` WHERE `p`.`post_status` = 'publish' AND `p`.`post_type` = 'post' GROUP BY `p`.`ID` ORDER BY `p`.`ID` DESC</pre>

    <p>Premiamo il bottone "go" per eseguirla. La query mostra solo gli ID e i titoli dei post.

    <p>Ora creiamo la lista dalla query premendo il bottone "Create list from query"</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_01_02.png"  class="dbp-tutorial-img">
    <p>
    <p>Scriviamo il nome "Articoli correlati" e salviamo.</p>
    <p>Ora abbiamo creato la nostra lista. Automaticamente veniamo mandati nella pagina per la gestione del modulo di inserimento dati. In questo caso non ci interessa e il modulo form, ma andiamo direttamente al tab "Settings". Compiliamo lo schema come mostra l'immagine:</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_01_03.png"  class="dbp-tutorial-img">

    <p>Questa lista non è pensata per modificare dati per cui non abbiamo necessità di avere un link specifico in amministrazione.</p>
    <p>Le options servono per decidere quanti elementi per pagina e l'ordinamento.</p>
    <p>I filtri sono invece la parte più interessante di questo tutorial. Noi vogliamo che vengano estratti tutti gli articoli che hanno almeno un tag uguale a quello dell'articolo che si sta visualizzando. Per fare questo vogliamo che tr.term_taxonomy_id sia uguale ad uno degli id dei tag (term) dell'articolo che si sta visualizzando. Per avere l'id dell'articolo che si sta visualizzando si può usare lo shortcode [^current_post.id]<br>
    Per avere l'elenco degli id dei tag di un articolo possiamo usare la funzione [^get_post_tags]. Quindi scrivendo [^get_post_tags.term_id post_id=[^current_post.id]]. Il tipo di filtro è IN (Match in array).</p>
    <p>Il secondo filtro invece dice che l'id degli articoli estratti devono essere diversi dall'id dell'articolo che si sta visualizzando. Questo per evitare di mostrare lo stesso articolo che si sta visualizzando tra gli articoli correlati.</p>

    <p>Delete Options definisce quali tabelle rimuovere quando si rimuovono le righe della lista. In questo caso noi non vogliamo che si possano rimuovere per cui diciamo a tutte le tabelle no.</p>

    <p>Salvo le impostazioni</p>

    <h2 class="dbp-h2">Pubblicazione</h2>
    <p>Lo shortcode per pubblicare la lista lo trovi in alto a destra.</p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_01_04.png"  class="dbp-tutorial-img">
    <p>Puoi pubblicare lo shortcode nel template così da applicarlo in tutte le pagine oppure nei singoli articoli</p>
    <p>Altrimenti puoi usare il php per caricare la lista e pubblicarla a tuo piacere. Ecco un esempio per caricare la lista in fondo ai post.</p>
    <pre class="dbp-code">function add_dbp_list_to_content($content) {
	$append = '';
	if (is_single()) {
		$append = DatabasePress\Dbp::get_list('113');
	}
	return $content.$append;
}
add_filter('the_content', 'add_dbp_list_to_content');</pre>

<h2 class="dbp-h2">Impostazioni avanzate</h2>
    <p>Andiamo a migliorare il nostro lavoro! Prima di tutto vogliamo modificare le colonne che stiamo visualizzando quindi sempre dentro le impostazioni della lista andiamo sul tab "List view formatting". </p>
    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/assets/tutorial_01_05.png"  class="dbp-tutorial-img">
    <p>Per nascondere l'ID selezioniamo Hide in "show in frontend"<br>
    Per creare un link all'articolo clicchiamo la matita accanto a post_title, poi su column type selezioniamo special field > Custom. Scriviamo nella textarea:</p>
    <pre class="dbp-code">&lt;a href="[^LINK id=[%data.ID]]"&gt;[%data.post_title]&lt;/a&gt;</pre></p>
    <p>Aggiungiamo una colonna custom, ovvero non collegata ai dati delle tabelle. Clicchiamo "Add custom column". Vogliamo mostrare i tag degli articoli. Utilizzeremo la funzione [^get_post_tags].</p>
    <p>Se vogliamo mostrare solo il primo tag possiamo scrivere:</p>
    <pre class="dbp-code">[^get_post_tags.0.html post_id=[%data.ID]]</pre>
    <p>Se scrivessimo:</p>
    <pre class="dbp-code">[^get_post_tags.html post_id=[%data.ID]]</pre>
    <p>Se c'è più di un tag ritornerebbe un array per cui dobbiamo ciclare i risultati. Lo possiamo fare usando l'attributo tmpl. Il valore passato all'attributo è a sua volta un
    <pre class="dbp-code">[^get_post_tags post_id=[%data.ID] tmpl=[%item.html]]</pre>

    <p style="color:red">Ricordati sempre che tra gli attributi e il valore non ci devono mai essere spazi! Quindi [^get_post_tags.html post_id = [%data.ID]] non funzionerà perché prima e dopo il simbolo = ci sono due spazi</p>
</div>