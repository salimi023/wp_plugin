<!-- The Modal -->
<div id="modal" class="w3-modal">
    <div class="w3-modal-content">
        <div class="w3-container">
            <span class="w3-button w3-display-topright modal_close_btn">&times;</span>
            <div class="w3-row" id="modal_content_container">
                <p class="w3-row w3-xlarge" id="modal_title"></p>
                <div class="w3-row w3-padding w3-margin-bottom" id="modal_content">
                    <div class="w3-row w3-padding panel" id="comp_shortcode">
                        <h4>Shortcode</h4>
                        <div class="w3-row">
                            <div class="w3-half w3-padding w3-margin-bottom" id="shortcode_container"><input type="text"
                                    class="w3-input w3-border" id="shortcode" name="shortcode" readonly /></div>
                            <div class="w3-half w3-margin-bottom"><button id="copy_shortcode"
                                    class="w3-btn w3-orange w3-round w3-right">Copy to clipboard</button></div>
                        </div>
                        <div class="w3-row"><small><i>(Please, copy the shortcode to the clipboard and paste it into the relevant
                            post.)</i></small></div>
                    </div>
                    <div class="w3-row w3-padding panel" id="comp_description">
                        <h4>Description</h4>
                        <div class="w3-row" id="desc_container"></div>
                    </div>
                    <div class="w3-row w3-padding panel" id="comp_entry_codes">
                        <h4>Entry Codes</h4>
                        <div class="w3-row w3-responsive" id="table_container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>