<?php
/**
 * ---------------------
 * Main page functions
 * ---------------------
 */
function comp_main_page()
{
    require_once(APP_ROOT . 'views/templates/comp_header_temp.php');

    $comp_table = DB_PREFIX . 'comp_competitions';
    $comp_query = "SELECT * FROM $comp_table ORDER BY comp_id ASC";
    $competitions = Data::read($comp_query);
    
    require_once(APP_ROOT . 'views/pages/comp_main_page.php');
    require_once(APP_ROOT . 'views/templates/comp_modal_temp.php');
}

/**
 * Obtaining data of selected competition from db (review modal)
 *
 * @return array - data of competition
 */
function comp_review_competition()
{
    if (isset($_POST) && !empty($_POST)) {
        if (wp_verify_nonce($_POST['nonce'])) {
            $table_comp = DB_PREFIX . 'comp_competitions';
            $table_code = DB_PREFIX . 'comp_codes';

            $sql = "SELECT comp.*, code.* FROM $table_comp AS comp INNER JOIN $table_code AS code USING(comp_id) WHERE comp.comp_id = %d";

            $comp = Data::custom($sql, $_POST['id']);            

            if (!empty($comp)) {
                wp_send_json_success($comp);
            } else {
                wp_send_json_error(AJAX_NOT_FOUND);
            }
        } else {
            wp_send_json_error(AJAX_DENIED);
        }
    }
}

add_action('wp_ajax_comp_review_competition', 'comp_review_competition');

/**
 * Removal of selected competition(s)
 *
 * @return string - Ajax status message
 */
function comp_delete_competition()
{
    if (isset($_POST) && !empty($_POST)) {
        if (wp_verify_nonce($_POST['nonce'])) {
            $delete_codes = Data::delete('comp_codes', $_POST['del'], 'comp_id');                       
            
            if ($delete_codes) {
                $delete_competition = Data::delete('comp_competitions', $_POST['del'], 'comp_id');                
            }

            if ($delete_competition && $delete_codes) {
                wp_send_json_success(AJAX_DELETED);
            } else {

                switch(true) {
                    case !$delete_competition:
                        wp_send_json_error(AJAX_NOT_DELETED);
                        break;

                    case !$delete_codes:
                        wp_send_json_error(AJAX_CODES_NOT_DELETED);
                        break;

                    default:
                        wp_send_json_error(AJAX_UNKNOWN_ERROR);
                        break;
                }                                                            
            }
        } else {
            wp_send_json_error(AJAX_DENIED);
        }
    }
}

add_action('wp_ajax_comp_delete_competition', 'comp_delete_competition');
