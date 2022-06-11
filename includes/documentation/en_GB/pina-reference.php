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

<h3>Lista degli attributi</h3>
    <?php 
    $array_attributes = array("tmpl=","for=","print=","no-print","zero","one","singular","plural","negative",".(variable_name)=","sep=","qsep=","if=","sum","mean","count","length","get=","show","fields","date-format=","date-modify=","last-day","timestamp","datediff-year=","datediff-month","datediff-day=","datediff-hour=","datediff-minute","upper","lower","ucfirst","strip-comment","strip-tags","nl2br","left=","more","right=","trim_words=","sanitize","esc_url","trim","search=","if=","set=","set+= o set-=","decimal=","euro","floor","round","ceil","sum","mean","is_string", "is_object", "is_date");
     asort($array_attributes);
     ?><ul><?php
     foreach ($array_attributes as $attr) {
        ?><li><?php echo $attr; ?></li><?php
     }
     ?></ul>
</div>

<h3>Lista delle funzioni</h3>
<?php 
    $array_fn = array("[^USER]","[^NOW]","[^POST]","[^IMAGE]","[^LINK]","[^SET]","[^GET_THE_ID]","[^RETURN]","[^IS_USER_LOGGED_IN]","[^IS_PAGE_AUTHOR]","[^IS_PAGE_ARCHIVE]","[^IS_PAGE_TAG]","[^IS_PAGE_DATE]","[^IS_PAGE_TAX]","[^IF] ... [^ELSE] ... [^ENDIF]","[^MATH]","[^FOR]...[^ENDFOR]","[^WHILE]... [^ENDWHILE]","[^BREAK]","[^BLOCK]... [^ENDBLOCK]");
    asort($array_fn);
    ?><ul><?php
    foreach ($array_fn as $fn) {
       ?><li><?php echo $fn; ?></li><?php
    }
    ?></ul>
</div>