<?php 
namespace DatabasePress;
if (!defined('WPINC')) die;
?>

<div class="dbp-content-table dbp-docs-content  js-id-dbp-content" >
    
<h2 class="dbp-h2"> <a href="<?php echo admin_url("admin.php?page=dbp_docs") ?>">Doc</a><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('LIST FORM js script','database_press'); ?></h2>

    <p>Using javascript you can improve the insertion experience for example by hiding fields or verifying the data entered.</p>
    <hr>
    <p>Inside the lists go to the form tab.</p>
    <p>Inside the individual fields there is a textarea to insert custom javascript.</p>  
    <p>The javascript inserted is executed when the form is loaded, when it is submitted and every time an element of the form is modified. You can use the <b> status </b> variable to identify when the function is called.</p>
    <pre class="dbp-code">
    if (status == 'start') {
        // The form is started
    }
    if (status == 'field_change') {
        // The field is changed
    }
    if (status == 'form_change') {
        // Any other field on the form is changed
    }
    if (status == 'submit') {
        // Viene inviata al form. Puoi bloccare l'invio impostando un campo come invalid.
    }
    </pre>
    <p>You can use the <b> field </b> variable to refer to the field in which you are writing the code. This variable extends the dbp_field object which has the following functions:</p> 
    
    <ul>
        <li><b>field.val(val)</b> // val optional if set it sets the variable</li>
        <li><b>field.valid(boolean, msg)</b> // msg optional</li>
        <li><b>field.isValid()</b> // returns if it is valid</li>
        <li><b>field.toggle(boolean)</b></li>
        <li><b>field.dom()</b> // the dom of the field returns</li>
        <li><b>field.addClass(class_name)</b></li>
        <li><b>field.removeClass()</b></li>
        <li><b>field.msg(str)</b> // Insert a message below the field</li>
        <li><b>field.valid_date()</b></li>
        <li><b>field.valid_range(min,max)</b></li>
        <li><b>field.choices()</b> // modify the options of a select</li>
        <li><b>field.required(boolean);</b></li>
    </ul>

    <p>You can refer to the other fields on the form through the form variable which extends dbp_form</p> 
            <ul>
                <li><b>form.get(field)</b> // field name | field  label </li>
                <li><b>form.get(table.field)</b> // current occurence</li>
                <li><b>form.get(table.field.number_of_occurence)</b></li>
                <li><b>form.get(table.field.next)</b></li>
                <li><b>form.get(table.field.prev)</b></li>
            </ul>
        </p>
    <br>
    <p>You don't have to enter the tag code &lt;script&gt;&lt;/script&gt;.</p>
    <br>
    <p>Attention a required field even if hidden remains required! In general, a hidden invalid field prevents the form from being submitted.</p>
    <h4>Examples:</h4>
    <p>I show a field only if the checkbox is checked. For the single checkbox, val returns null if it is not selected, otherwise it returns the value of the checkbox. In the example the checkbox has value = "1"</p>
    <pre class="dbp-code">
    field.toggle(form.get('mycheckboxlabel').val() == 1);
    </pre>
    <p>I show a field only if a particular option of a checkbox is checked.</p>
    <pre class="dbp-code">
    field.toggle(form.get('n.categories').val().indexOf('Blue') > -1);
    </pre>
    <p>I set the checkboxes with the option values (must be an array). If I want a defaul I use the array in the default field.</p>
    <pre class="dbp-code">
    field.val(["opt_val1","opt_val5"]);
    </pre>

    <p>Valid a field only if it is greater than 10</p>
    <pre class="dbp-code">
        field.valid( field.val() < 10, 'The field must be greater than 10');
    </pre>
    <p>Valid a date only if the start is greater than the date_start field
    <pre class="dbp-code">
    let a = form.get('r.date_start').val();
    field.valid_range(a);
    </pre>
    <p>The field is valid only if it is less than 100
    <pre class="dbp-code">
    field.valid_range(false,100);
    </pre>
    <p>In a repeating table I validate the next_order field saying that it must be greater than the previous instance</p>
    <pre class="dbp-code">
    if (status == 'form_change') {
    let prev_val = parseInt(form.get('my_table.next_order.prev').val());
    if (!isNaN(prev_val)) {
        field.valid( field.val() > prev_val);
    }
    }
    </pre>
    <p>Given due select changes the second's options each time the first is changed</p>
    <pre class="dbp-code">
    if (status == 'start' || status == 'form_change') {
        if (form.get('PROVIN').val() == 'scelta 1') {
            field.toggle(true).choices({'1':"Male",'2':"Female"});
        } else if (form.get('PROVIN').val() == 'scelta 2') {
            field.toggle(true).choices(["Red","Blue"]);
        } else if (form.get('PROVIN').val() == 'scelta "3') {
            field.toggle(true).choices(["A","B","C"]);
        } else {
            field.toggle(false);
        }
    }
    </pre>


</div>