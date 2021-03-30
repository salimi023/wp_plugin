jQuery.noConflict();
jQuery(document).ready(function($) {

    $(document).on({
        click: function(e) {
            var el = $(e.target);
            var action = el.data("action") != undefined ? el.data("action") : "";
            var id = el.data("id") != undefined ? el.data("id") : "";
            var data;

            if (action !== "") {
                var nonce = $("span#validationStatus").data("nonce");

                if (el.hasClass("modal_open_btn") || el.hasClass("delete")) {
                    data = {
                        action: action,
                        nonce: nonce,
                        id: id
                    }
                } else {
                    data = new FormData($("form#" + action)[0]);
                    data.append("action", action);
                    data.append("nonce", nonce);
                    data.append("id", id);
                }

                /**
                 * --------------
                 * Ajax actions
                 * -------------- 
                 */
                switch (action) {

                    /** Create, update data (form) */
                    case "comp_add_competition": // Add new competition
                    case "comp_update_competition": // Update competition                                       
                        if ($("span#validationStatus").text() === "") {

                            if (window.tinymce.editors.length) {
                                var editors_keys = Object.keys(window.tinymce.editors);

                                for (var ek = 0; ek < editors_keys.length; ek++) {
                                    if (editors_keys[ek] !== "0") {
                                        var editor_name = $("textarea#" + editors_keys[ek]).prop("name");
                                        data.append(editor_name, tinymce.editors[editors_keys[ek]].getContent());
                                    }
                                }
                            }
                            ajax_form(data, action);
                        }
                        break;

                        /** Review data (modal) */
                    case "comp_review_competition":
                        ajax_page(data, action);
                        break;

                        /** Delete actions */
                    case "comp_delete_competition":
                        delete_item('competition(s)', 'del_comp', data);
                        break;
                }
            }

            /**
             * --------
             * Forms 
             * -------- 
             */
            function ajax_form(form_data, action = false) {
                var ajax_url = $("span#validationStatus").data("ajax");
                var site_url = $("span#validationStatus").data("site");

                $.ajax({
                    url: ajax_url,
                    type: "POST",
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success: function(response) {

                        /**
                         * Callback actions
                         */

                        if (response.success) { // Success

                            switch (action) {

                                case "comp_add_competition":
                                case "comp_update_competition":
                                    alert(response.data);
                                    window.location.href = site_url + '/wp-admin/admin.php?page=comp_main_page';
                                    break;

                                default:
                                    alert(response.data);
                                    location.reload();
                                    break;
                            }
                        } else { // Failure
                            switch (action) {
                                default: alert(response.data);
                                return false;
                                break;
                            }
                        }
                    }
                });
            }

            /**
             * ----------------------
             * Static page elements 
             * ----------------------
             */
            function ajax_page(page_data, action = false) {
                var ajax_url = $("span#validationStatus").data("ajax");
                var app_url = $("span#validationStatus").data("url");

                $.ajax({
                    url: ajax_url,
                    type: "POST",
                    data: page_data,
                    dataType: "html",
                    success: function(response) {
                        var resp = JSON.parse(response);

                        /**
                         * Callback actions
                         */

                        if (resp.success) { // Success

                            switch (action) {

                                // Population of the review modal
                                case "comp_review_competition":
                                    var comp = resp.data;
                                    var code_table = '<table class="w3-table-all"><thead><th>ID</th><th>Entry Code</th><th>Entry Date</th><th>E-mail Address</th></thead><tbody>';

                                    for (var index in comp) {
                                        var shortcode = '[comp_frontend_form_sc comp_id=' + comp[index].comp_id + ']';
                                        var entry_date = comp[index].entry_date != null && comp[index].entry_date != undefined ? comp[index].entry_date : '';
                                        var email = comp[index].entry_email != null && comp[index].entry_email != undefined ? comp[index].entry_email : '';
                                        code_table += '<tr><td>' + comp[index].entry_id + '</td><td>' + comp[index].entry_code + '</td><td>' + entry_date + '</td><td>' + email + '</td></tr>';
                                    }

                                    code_table += '</tbody></table>';

                                    $("p#modal_title").html(comp[0].comp_title);
                                    $("div#desc_container").html(comp[0].comp_content);
                                    $("input#shortcode").val(shortcode);
                                    $("div#table_container").html(code_table);

                                    $("div#modal").css("display", "block");
                                    break;

                                default:
                                    alert(resp.data);
                                    location.reload();
                                    break;
                            }
                        } else { // Failure
                            switch (action) {

                                default: alert(resp.data);
                                return false;
                                break;
                            }
                        }
                    }
                });
            }

            /**
             * -------------------
             * Delete function
             * -------------------
             */
            function delete_item(item_name, field_name, data) {
                var del_ids = [];

                $("input[name='" + field_name + "']").each(function() {

                    if ($(this).prop("checked")) {
                        del_ids.push($(this).prop("id"));
                    }
                });

                if (del_ids.length > 0) {
                    if (confirm("Are you sure that you want to delete the selected " + item_name + "?")) {
                        data.del = del_ids;
                        ajax_page(data);
                    }
                } else {
                    alert("Please, select at least one competition.");
                    return false;
                }
            }
        }
    });
});