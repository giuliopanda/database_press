<?php ob_start() ?>
<div class="pippo">[^post type="miopost" set=post]</div>
<ul>
[^for each=post]
[^endfor]
</ul>
<?php
$block = ob_get_clean();
list($pre_string, $block, $post_string, $type) = pina_find_block($block, 0);
print ("<p>pre_string</p>");
var_dump ($pre_string);
print ("<p>block</p>");
var_dump ($block);
print ("<p>post_string</p>");
var_dump ($type);
die;