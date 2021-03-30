<?php
/**
 * Shortcode management
 */
// jQuery
wp_register_script('comp_jquery', APP_URL . 'libs/jquery-3.5.1.min.js');
wp_enqueue_script('comp_jquery');

/**
 * Frontend registration form
 * 
 * @param array - Shortcode attribute (competition ID) * 
 * @return string - HTML code of the frontend form
 */
function comp_frontend_form($atts)
{
    $comp_id = shortcode_atts([
        'comp_id' => '1'
    ], $atts, 'comp_frontend_form_sc');

    $table_comp = DB_PREFIX . 'comp_competitions';
    $table_codes = DB_PREFIX . 'comp_codes';
    $comp_sql = "SELECT * FROM $table_comp WHERE comp_id = %d";
    $comp_data = Data::read($comp_sql, $comp_id['comp_id']);

    if (!empty($comp_data)) {
        $html = '<div class="w3-row">';
        $html .= '<div class="w3-panel w3-grey w3-padding w3-round w3-card-4 w3-xlarge"><h2>' . $comp_data[0]['comp_title'] . '</h2></div><hr>';
        $html .= '<div class="w3-row"><h4>Description</h4>' . $comp_data[0]['comp_content'] . '</div><hr>';
        $html .= '<div class="w3-row"><h4>Registration</h4>';
        
        $html .= '<form id="comp_reg_form" data-comp="' . $comp_data[0]['comp_id'] . '">';
        $html .= '<div class="w3-row w3-margin-bottom">';
        $html .= '<input type="text" name="comp_reg_code" id="comp_reg_code" placeholder="Entry code" required />';
        $html .= '</div>';
        $html .= '<div class="w3-row w3-margin-bottom">';
        $html .= '<input type="email" name="comp_reg_email" id="comp_reg_email" placeholder="E-mail address" required />';
        $html .= '</div>';
        $html .= '<input type="submit" name="comp_reg_submit" id="comp_reg_submit" data-action="comp_reg_submit" value="Registration" data-nonce="' . wp_create_nonce() . '" />';
        $html .= '</form>';
        
        $html .= '</div>';
        $html .= '</div>';
    }
    else {
        $html = '<div class="w3-row">Sorry, currently the data of this competition cannot be accessed. Please, try again.</div>';
    }
    
    echo $html;
}

add_shortcode('comp_frontend_form_sc', 'comp_frontend_form');

/**
 * Frontend form AJAX js 
 */
function comp_shortcode_ajax_js()
{
    echo '<script>
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $("input[name=\'comp_reg_submit\']").on("click", function(e) {
            e.preventDefault();
            var code_reg = $("input#comp_reg_code").val();
            var email_reg = $("input#comp_reg_email").val();
            var nonce_reg = $(this).data("nonce");
            var comp_id = $("form#comp_reg_form").data("comp");
            
            var rem = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            var check_email = rem.test(email_reg);

            if(code_reg !== "" && email_reg !== "" && check_email == true) {

                $.ajax({
                    url: "' . admin_url('admin-ajax.php') . '",
                    type: "POST",
                    data: {action: "comp_frontend_registration", comp: comp_id, code: code_reg, email: email_reg, nonce: nonce_reg },
                    dataType: "html",
                    success: function(response) {                        
                        var resp = JSON.parse(response);                        
                        if(resp.success == true) {
                            alert(resp.data);
                            location.reload();
                        }
                        else {
                            alert(resp.data);
                            return false;
                        }
                    }
                });
            }
            else {
                switch(true) {
                    case code_reg === "":
                        alert("Please provide an entry code");
                        return false;
                        break;

                    case email_reg === "":
                        alert("Please provide an e-mail");
                        return false;
                        break;

                    case check_email == false:
                        alert("Please provide a valid e-mail");
                        return false;
                        break;
                }
            }
        });
    });    
    </script>';
}

add_action('wp_head', 'comp_shortcode_ajax_js');

/**
 * Frontend form AJAX PHP
 */
function comp_frontend_registration()
{
    if (isset($_POST) && !empty($_POST)) {
        $reg_data = $_POST;
        
        if (wp_verify_nonce($reg_data['nonce'])) {
            $code_check_table = DB_PREFIX . 'comp_codes';
            $reg_code = $reg_data['code'];
            $comp_id = $reg_data['comp'];
            $code_check_sql = "SELECT code_id FROM $code_check_table WHERE entry_code = '%s' AND comp_id = $comp_id AND (entry_email != NULL OR entry_email != '') LIMIT 1";
            
            $code_check = Data::read($code_check_sql, $reg_code);

            if (empty($code_check)) {
                $code_update_table = $code_check_table;
                $code_update_sql = "SELECT code_id FROM $code_update_table WHERE entry_code = '%s' AND comp_id = $comp_id LIMIT 1";
                $code_update_id = Data::read($code_update_sql, $reg_code);

                if (!empty($code_update_id)) {
                    $data = [
                    'entry_date' => date('Y-m-d H:i:s'),
                    'entry_email' => $reg_data['email']
                ];

                    $update_code = '';
                    $update_code = Data::update('comp_codes', $data, ['code_id' => $code_update_id[0]['code_id']]);
                    
                    if (!empty($update_code) && $update_code) {
                        wp_send_json_success('Thank you. Your entry has been recorded.');
                    } elseif (empty($update_code)) {
                        wp_send_json_error(AJAX_UNKNOWN_ERROR);
                    }
                } else {
                    wp_send_json_error('Sorry, code invalid');
                }
            } else {
                wp_send_json_error('Sorry, code invalid');
            }
        }
    }
}

add_action('wp_ajax_comp_frontend_registration', 'comp_frontend_registration');
add_action('wp_ajax_nopriv_comp_frontend_registration', 'comp_frontend_registration');
