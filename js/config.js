jQuery.noConflict();
jQuery(document).ready(function($) {

    var app_url = $("span#validationStatus").data("url");

    /**
     * DataTables config
     */
    var table_id = $("table").prop("id");

    if (table_id !== "") {

        $("table#" + table_id).DataTable({
            dom: 'Bfrtip',
            buttons: ['excel', 'csvHtml5', 'pdf']
        });
    }

    /**
     * Checkbox management
     */
    $("input.check_all").on("change", function() {

        if ($(this).prop("checked")) {

            $("input.check").each(function() {

                if (!$(this).prop("disabled")) {
                    $(this).prop("checked", true);
                }
            });
        } else {
            $("input.check").prop("checked", false);
        }
    });

    /**
     * Modal
     */

    // Close Modal
    $("span.modal_close_btn").on("click", function() {
        $("div#modal").css("display", "none");
    });
});