<?php
/**
 * -------------------------------
 * Set Competition page functions
 * -------------------------------
 */
function comp_set_competition()
{
    require_once(APP_ROOT . 'views/templates/comp_header_temp.php');

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $table = DB_PREFIX . 'comp_competitions';
        $sql = "SELECT * FROM $table WHERE comp_id = %d";
        $comp = Data::read($sql, $_GET['id']);
    }
    require_once(APP_ROOT . 'views/pages/comp_set_competition.php');
}

/**
 * Creating new competition
 *
 * @return string - Ajax status message
 */
function comp_add_competition()
{
    if (isset($_POST) && !empty($_POST)) {
        if (wp_verify_nonce($_POST['nonce'])) {
            $save_error = false;
            $upload_error = false;
            
            // Saving of competition data
            $create_competition = Data::create('comp_competitions', $_POST);

            if ($create_competition) {
                $saved_comp_id_table = DB_PREFIX . 'comp_competitions';
                $saved_comp_id_sql = "SELECT comp_id FROM $saved_comp_id_table ORDER BY comp_id DESC LIMIT 1";
                $saved_comp_id = Data::read($saved_comp_id_sql);
                
                // Saving of entry codes
                if (isset($_FILES['comp_codes']) && !empty($_FILES['comp_codes']['name'])) {
                    $file = $_FILES['comp_codes'];
                    $code_data = [];

                    if ($file['error'] == 0) {
                        if (pathinfo($file['name'], PATHINFO_EXTENSION) === 'csv') {
                            $file_data = fopen($file['tmp_name'], "r");

                            while ($column = fgetcsv($file_data, 50)) {
                                $code = [
                                'comp_id' => $saved_comp_id[0]['comp_id'],
                                'entry_id' => $column[0],
                                'entry_code' => $column[1]
                            ];
                                array_push($code_data, $code);
                            }
                        }
                    }
                    
                    if (count($code_data) > 1) {
                        unset($code_data[0]);
                        
                        foreach ($code_data as $code) {
                            $save_code = Data::create('comp_codes', $code);

                            if (!$save_code) {
                                $upload_error = true;
                                break;
                            }
                        }
                    }
                    else {
                        $upload_error = true;
                    }
                }
            } else {
                $save_error = true;
            }

            if (!$save_error && !$upload_error) {
                wp_send_json_success(AJAX_SAVED);
            } else {
                switch (true) {
                    case $save_error:
                        wp_send_json_error(AJAX_NOT_SAVED);
                        break;

                    case $upload_error:
                        wp_send_json_error(AJAX_UPLOAD_ERROR);
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

add_action('wp_ajax_comp_add_competition', 'comp_add_competition');

/**
 * Updating competition data
 *
 * @return string - Ajax status messages
 */
function comp_update_competition()
{
    if (isset($_POST) && !empty($_POST)) {
        if (wp_verify_nonce($_POST['nonce'])) {
            $update_competition = Data::update('comp_competitions', $_POST, ['comp_id' => $_POST['id']]);
            
            if ($update_competition) {
                wp_send_json_success(AJAX_UPDATED);
            } else {
                wp_send_json_error(AJAX_NOT_UPDATED);
            }
        } else {
            wp_send_json_error(AJAX_DENIED);
        }
    }
}

add_action('wp_ajax_comp_update_competition', 'comp_update_competition');
