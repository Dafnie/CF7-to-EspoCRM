
<?php

if( !defined( 'ABSPATH' ) ) exit;


$form_fields = wpcf7_get_current_contact_form();
$cf7_fields = WPCF7_ContactForm::get_current()->scan_form_tags();
$settings = get_option('cf7toespo-' . $form_fields->id);

?>

<a class="donate" href="https://www.paypal.com/donate/?cmd=_donations&business=gjeddec@gmail.com">

    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" />
</a>

    <h2><?php _e('Contact Form 7 to EspoCRM integration', 'wptoespo'); ?></h2>

    <label> 
        <input type="checkbox" id="espo_enable" name="espo_enable" <?=($settings['espo_enable']) ? 'checked' : '' ?> />
        <?php _e( ' Send to EspoCRM', 'wptoespo' ); ?></br></br>
    </label>

    <label>
        <?php _e( 'EspoCRM url:' , 'wptoespo' );?></br>
        <input type="text" id="espo_url" name="espourl" class="large-text"
placeholder=" <?php _e( 'https://my_espocrm.com or http://my_espocrm.com' , 'wptoespo' );?>"
value=<?=$settings['espourl']; ?> >
        <p class="description" ><?php _e( 'URL to EspoCRM installation. - <strong>Important! - Use https to protect the API Key</strong>' , 'wptoespo' ); ?></p> 
    </label>

    <label>
        <?php _e( 'EspoCRM API User key:' , 'wptoespo' ); ?></br>
        <input type="text" id="espo_username" name="espo_key" value=<?=$settings['espo_key']; ?>  >
        <p class="description" ><?php _e( 'EspoCRM Authentication Method <strong>API Key</strong>. Example - <i>"a16756991fcbca5784f2a65d07db5b4e"</i>' , 'wptoespo' ); ?></p> 
    </label><br/>

    <hr>
    <label>
        <?php _e( 'Contact Form 7 to EspoCRM entity:' , 'wptoespo' );?> </br>
        <select name="parent" id="espo_type">
            <option value="Contact" <?=($settings['entity'] == 'Contact') ? 'selected' : '' ?> >Contact</option>
            <option value="Lead" <?=($settings['entity'] == 'Lead') ? 'selected' : '' ?> >Lead</option>
        </select>  
    </label></br></br>


  
    <?php _e( 'Map field the from wordpress to parent-entity in EspoCRM:' , 'wptoespo' ); ?>
    <?php //Field mapping

        if ( empty($cf7_fields) || !$settings || $settings['error'] ) {
            _e( '<p class="regulat-text code red">Save to fetch fields</p>', 'wptoespo' );
        } else {
            _mapping('parent_');
        } ?>
    </br>

    <hr>
        <?php _e( 'Key-field for duplicate search:' , 'wptoespo' );?> </br>
        <select name="duplicate" id="espo_duplicate">
            <option value="off">Disable</option>
            <?php foreach ( $cf7_fields as $field ) {
                $selected = ( $settings['duplicate'] == $field->name ) ? 'selected' : '';
                
                if ( $field->type == 'submit' ) {
                    continue;
                }
                echo '<option value="' . $field->name . '" ' . $selected . '>' . $field->name . '</option>';
            } ?>
        </select>
        <p class="description" ><?php _e( 'Cancel the creation of parent type if the data in allready exits in EspoCRM. Child entity will allways be created', 'wptoespo' ); ?></p>
    </br>

    <hr>
        <?php _e( 'Create child entity:' , 'wptoespo' );?> </br>
        <select name="child" id="espo_ass_type">
            <option value="None" <?=($settings['child'] == 'None') ? 'selected' : '' ?> >None</option>
            <option value="Call" <?=($settings['child'] == 'Call') ? 'selected' : '' ?> >Call</option>
            <option value="Task" <?=($settings['child'] == 'Task') ? 'selected' : '' ?> >Task</option>
            <option value="Opportunity" <?=($settings['child'] == 'Opportunity') ? 'selected' : '' ?> >Opportunity</option>
        </select>
        <p class="description" ><?php _e( 'The selected entity will be linked to the main type' , 'wptoespo' ); ?></p></br>

        <?php 
        if ( !($settings['child'] == 'None') ) {
        _e( 'Map field from wordpress to child-entity in EspoCRM:' , 'wptoespo' );
        if ( empty($cf7_fields) || !$settings || $settings['error'] ) {
            _e( '<p class="regulat-text code red">Save to fetch fields</p>', 'wptoespo' );
        } else {
            _mapping('child_');
        }
    } ?>
    </br>

<?php
// Helper to display field mapping
function _mapping($type) {

    $cf7_fields = WPCF7_ContactForm::get_current()->scan_form_tags();
    $settings = get_option('cf7toespo-' . wpcf7_get_current_contact_form()->id);
    $espo_fields = $settings[$type . 'espofilds'];

    ?> <table class="<?=$type; ?>"> <?php
            // Get fields form CF7 form
            foreach ( $cf7_fields as $field ) {

                if ( $field->type == 'submit' ) {
                    continue;
                } ?>
                <tr>
                    <td class="cf7_fieldname">
                        <?=$field->name . ' ->'; ?>
                    </td>
                    <td>
                        <select name="<?=$type . $field->name; //prefix "cf7_" added to identify the form fields in the POST later ?>">

                            <?php
                            $field_setting = $settings['mapping'][$type . $field->name];
                            $selected = ( $field_setting == 'none' ) ? ' selected ' : '';
                            echo '<option value="none"' . $selected . '>' . __("- none -", "wptoespo") . '</option>';
                            // Get fields from the Espo entity
                            foreach ( $espo_fields[0] as $key=>$value ) {
                                $disable = ( in_array($key, constant('CF7_ESPO_IGNORE_fIELD')) ) ? 'disabled' : ''; // disable some Espo options
                                $selected = ($field_setting == $key) ? 'selected' : '';

                                echo '<option value="' . $key . '"' . $disable . ' ' . $selected . ' >' . $key . '</option>';
                            } ?>
                        </select>
                    </td>
                </tr>
            <?php }

            // mapping static fields
            if ( array_key_exists($type . '1_static', $settings['mapping']) ) {
                
                // get array of static input
                if ($type == 'parent_') {
                    $fields = array_filter(
                        $settings['mapping'], function($key) {
                            return preg_match("#parent.+static$#", $key);
                        }, ARRAY_FILTER_USE_KEY
                    );
                } else {
                    $fields = array_filter(
                        $settings['mapping'], function($key) {
                            return preg_match("#child.+static$#", $key);
                        }, ARRAY_FILTER_USE_KEY
                    );
                }

                
                // echo input and select...
                foreach( $fields as $key=>$field ) {
                ?> <tr>
                    <td>
                        <input type="text" class="<?=$type; ?>static" name="<?=$key; ?>" value="<?=$field; ?>" >
                    </td>
                    <td>
                        <select name="<?=$key . '_espo'; ?>">

                            <?php
                            $field_setting = $settings['mapping'][$key . '_espo'];
                            // $selected = ( $field_setting == 'none' ) ? ' selected ' : '';
                            echo '<option value="none"' . $selected . '>' . __("- none -", "wptoespo") . '</option>';
                            // Get fields from the Espo entity
                            foreach ( $espo_fields[0] as $key=>$value ) {
                                $disable = ( in_array($key, constant('CF7_ESPO_IGNORE_fIELD')) ) ? 'disabled' : ''; // disable some Espo options
                                $selected = ($field_setting == $key) ? 'selected' : '';

                                echo '<option value="' . $key . '"' . $disable . ' ' . $selected . ' >' . $key . '</option>';
                            } ?>
                        </select>
                    </td>
                </tr>
                <?php
                }
            }

            ?> </table>
            <button class="button-primary add_field" data-id="<?=$type; ?>">-> Add static field</button>
            <span class='info'><i> <?php _e('INFO: Saving empty fields will remove the fields', 'wptoespo'); ?></i></span>
            <?php
}
