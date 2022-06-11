<?php 
/**
* Calcola l'altezza del container per farlo entrare in altezza nella pagina
* Il fle viene caricato in fondo da dbt-page-base.php
 * il container deve essere: <div id="dbt_container"
 * 
 * @todo Allargare o strignere la sidebar tramite drag&drop e salvataggio dell'impostazione così quando riapro non devo ogni volta ritrascinarlo
 */
?>
<script>
    function dbt_set_container_height() {
        var h = window.innerHeight;
        
        document.getElementById('dbt_container').style.height = (h  - 80) + "px";
        console.log ("document.getElementById('wpbody-content').width :" + document.getElementById('wpbody-content').clientWidth );
        document.getElementById('dbt_container').style.width = (document.getElementById('wpbody-content').clientWidth  - 20) + "px";
    };
    dbt_set_container_height();
    window.addEventListener('resize', dbt_set_container_height);

   

    const resize_menu = new ResizeObserver(function(entries) {
        dbt_set_container_height();
    });
    resize_menu.observe( document.getElementById('adminmenu') );
</script>