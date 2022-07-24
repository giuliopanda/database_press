 <div class="wrap">
    <h1>PINACODE</h1>
    <form method="post" action="options.php"> 
        <?php settings_fields( 'pinacode-group' ); ?>
       
        <table class="form-table">
            <tr valign="top">
            <th scope="row">When execute</th>
            <td>
            <?php $val = esc_attr( get_option('pinacode_when_execute') );  echo $val;  ?>
            <select name="pinacode_when_execute">
                <option value=""<?php echo ($val == "") ? ' selected="selected"' : ''; ?>>After render theme</option>
                <option value="only_content"<?php echo ($val == "only_content") ? ' selected="selected"' : ''; ?>>Only content</option>
            </select>
            </td>
            </tr>

          
        </table>

    <?php submit_button(); ?>
    </form>
    </div>