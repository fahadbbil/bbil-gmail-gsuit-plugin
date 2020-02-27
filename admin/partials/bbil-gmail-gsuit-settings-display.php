<form action="" method="POST" style="margin-top: 30px">
    <label for="" style="margin-bottom: 15px; font-size: 21px">Please Select form where you want to implement:</label>
    <div class="form-group" style=" font-weight: normal;">
       <div class="radio-inline">
         <label style=" font-weight: normal;">
           <input type="radio" name="bgg_settings" value="1" checked="checked">
           Contact Form 7
         </label>
        </div>

        <div class="radio-inline">
         <label style=" font-weight: normal;">
           <input type="radio" name="bgg_settings" value="2">
           Gravity Form
         </label>
        </div>

        <div class="radio-inline">
         <label style=" font-weight: normal;">
           <input type="radio" name="bgg_settings" value="3">
           Both
         </label>
        </div> 
    </div>

    <div class="form-group">
        <button class="btn btn-primary" type="submit" id="save_donation_alignment" name="save_bgg_settings">Save</button>
    </div>
</form>
<?php
if (isset($_POST['save_bgg_settings'])) {
    $bgg_settings_value = $_POST['bgg_settings'];
    update_option( 'bgg_settings_value', $bgg_settings_value);
    echo '
            <div class="alert alert-success" role="alert" id="success-alert">
              <strong>Saved</strong>
            </div>
        ';
}
$bgg_settings_value = get_option( 'bgg_settings_value');
?>
<input id="bgg_settings_value" class="hidden" type="text" value="<?php echo $bgg_settings_value;?>">
<script>
    jQuery(function($){
        $( document ).ready(function() {
            var bgg_settings_value = $("#bgg_settings_value").val();
            // alert(bgg_settings_value);
            // $("#donation_alignment").val(bgg_settings_value);
            $("input[name=bgg_settings][value=" + bgg_settings_value + "]").attr('checked', 'checked');
        });

        $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
            $("#success-alert").slideUp(500);
        });
    });
</script>