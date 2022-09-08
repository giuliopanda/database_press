<?php
/**
 * Il template della pagina amministrativa
 * Lo spazio dei grafici è impostato qui, e poi verrà disegnato in javascript
 * l'html del setup e del resize bulk invece è caricato sui due html a parte
 *
 * @package    database_press
 */
namespace DatabasePress;
if (!defined('WPINC')) die;
?>
<div class="dbp-content-header">
    <?php require(dirname(__FILE__).'/dbp-partial-tabs.php'); ?>
</div>

<div class="dbp-content-table js-id-dbp-content" >
    <div class="dbp-content-margin">
   
        <h2 class="dbp-h2-inline dbp-content-margin"><?php _e('LIST (Query saved)', 'database_press'); ?></h2>
        <span class="dbp-submit" onclick="dbp_create_list_show_form(false)"><?php _e('CREATE NEW LIST'); ?></span>
        
        <?php if ($msg != "") : ?>
            <div class="dbp-alert-info"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if (@$msg_error != ""): ?>
            <div class="dbp-alert-sql-error"><?php echo $msg_error; ?></div>
        <?php endif ; ?>
        <hr>
        <?php if ( $post_count['trash'] > 0) : ?>
        <ul class="dbp-submenu" style="margin-bottom:0">
                <?php if ($action == "show-trashed" ) : ?>
                <li><a href="<?php echo admin_url('admin.php?page=dbp_list'); ?>">All (<?php echo $post_count['publish']; ?>)</a></li>
                <li><b>Trash (<?php echo $post_count['trash']; ?>)</b></li>
                <?php else: ?>
                    <li><b>All (<?php echo $post_count['publish']; ?>)</b></li>
                    <li><a href="<?php echo admin_url('admin.php?page=dbp_list&action=show-trashed'); ?>">Trash (<?php echo $post_count['trash']; ?>)</a></li>
                <?php endif; ?>
        </ul>
        <?php endif; ?>
        
        <table class="wp-list-table widefat striped dbp-table-view-list">
            <thead>
                <tr>
                    <td>id</td>
                    <td>Name</td>
                    <td style="width:20%">Description</td>
                    <td style="width:20%">Sql</td>
                    <td>Shortcode</td>
                    <td>Remove</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list_page as $post): ?>
                    <?php $link = admin_url("admin.php?page=dbp_list&section=list-browse&dbp_id=".$post->ID); ?>
                    <tr>
                        <td><?php echo $post->ID; ?></td>
                        <td>
                            <?php if ($action != "show-trashed" ) : ?>
                            <a href="<?php echo $link; ?>"><?php echo $post->post_title; ?></a>
                            <?php else: ?>
                                <?php echo $post->post_title; ?>
                            <?php endif; ?>

                        </td>
                        <td style="width:20%">
                            <div style=" max-height:150px; overflow-y:auto; width:100%;">
                            <?php echo $post->post_excerpt; ?></a>
                            </div>
                        </td>
                        <td style="width:20%;">
                            <div class="js-dbp-mysql-query-text" style="max-height:150px; overflow-y:auto; width:100%;">
                            <?php echo $post->post_content['sql']; ?></a>
                            </div>
                        </td>
                        <td>
                            <b>[dbp_list id=<?php echo $post->ID; ?>]</b> <?php echo ($post->shortcode_param!= "") ? __('Attributes', 'database_press').":<b>".$post->shortcode_param.'</b>' : ''; ?>
                        </td>
                        <td>
                            <?php if ($post->post_status == "publish") : ?>
                                <a class="dbp-warning-link" href="<?php echo admin_url('admin.php?page=dbp_list&section=list-all&action=trash-list&dbp_id='.$post->ID); ?>" onclick="return confirm('Are you sure to remove this list?');">Trash the list</a>
                            <?php elseif ($post->post_status == "trash") :  ?> 
                                <a class="" href="<?php echo admin_url('admin.php?page=dbp_list&section=list-all&action=publish-list&dbp_id='.$post->ID); ?>">Publish</a>
                                <a class="dbp-warning-link" href="<?php echo admin_url('admin.php?page=dbp_list&section=list-all&action=remove-list&dbp_id='.$post->ID); ?>" onclick="return confirm('Are you sure to remove this list?');">Remove permanently</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach ;?>
            </tbody>
        </table>
    </div>
</div>
<?php 
// $dbp = new Dbp_fn();
$list_of_tables = dbp_fn::get_table_list();
$list_of_tables_js = [];
foreach ($list_of_tables['tables'] as $lot) {
    $list_of_tables_js[] = $lot;
}
?>
<script>
    var dbp_tables = <?php echo json_encode($list_of_tables_js); ?>;
</script>