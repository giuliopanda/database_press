<?php
/**
 * header-type:rif
 * header-title: Indice della documentazione
 * header-tags: document index php hook javascript template engine
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