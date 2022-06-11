<?php
/**
 * header-type:doc
 * header-title: Indice della documentazione
* header-tags: document index
* header-description: L'indice delle pagine della documentazione
* header-lang:ITA
*/
namespace DatabaseTables;
if (!defined('WPINC')) die;
?>
<div class="dbt-content-margin">
    <ul>
        <li>
            <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=js-controller-form") ?>" class="js-simple-link">Form javascript</a> 
        </li>
        <li>
            <a href="<?php echo admin_url("admin.php?page=dbt_docs&section=pinacode") ?>" class="js-simple-link">Template Engine</a>
        </li>
        <li><a class="js-simple-link" href="<?php echo admin_url("admin.php?page=dbt_docs&section=hooks") ?>">Hooks & filters</a></li>
        <li><a class="js-simple-link" href="<?php echo admin_url("admin.php?page=dbt_docs&section=code-php") ?>">PHP</a></li>
    </ul>


</div>