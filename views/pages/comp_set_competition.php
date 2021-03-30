<section class="w3-row w3-margin-right">
    <header class="w3-margin-bottom">
        <h1><?php !isset($comp) ? _e('Add Competition') : _e('Update Competition'); ?></h1>
        <small><?php _e('Fields marked with <span class="asterisk">*</span> are obligatory!'); ?></small>
    </header>
    <form id="<?php echo(!isset($comp)) ? 'comp_add_competition' : 'comp_update_competition'; ?>">
        <div class="w3-row w3-margin-bottom">
            <label class="label" for="comp_title"><?php _e('Title'); ?>:<span class="asterisk">*</span></label>
            <input type="text" class="w3-input w3-border valid" name="comp_title" id="comp_title" value="<?php echo(isset($comp)) ? $comp[0]['comp_title'] : ''; ?>" />
            <span class="alert"></span>
        </div>
        <div class="w3-row w3-margin-bottom">
            <label class="label" for="comp_content"><?php _e('Description'); ?>:<span class="asterisk">*</span></label>
            <?php 
            $content = isset($comp) ? $comp[0]['comp_content'] : '';
            wp_editor($content, 'comp_content_required', ['textarea_name' => 'comp_content', 'textarea_rows' => 5, 'media_buttons' => false]); ?>
            <span class="comp_content_required alert"></span>
        </div>
        <?php if(!isset($comp)) { ?>
        <div class="w3-row w3-margin-bottom">
            <label class="label" for="comp_codes"><?php _e('Upload Entry Codes'); ?>:<span class="asterisk"><?php echo(!isset($comp)) ? '*' : ''; ?></span></label>
            <input type="file" accept=".csv" class="w3-input w3-border <?php echo(!isset($comp)) ? ' valid' : ''; ?>" name="comp_codes" id="comp_codes" <?php echo(isset($comp)) ? ' disabled' : ''; ?> />
            <span class="alert"></span>
        </div>
        <?php } ?>
        <button class="w3-btn w3-green w3-round send" data-action="<?php echo(!isset($comp)) ? 'comp_add_competition' : 'comp_update_competition'; ?>" data-type="page" data-id="<?php echo(isset($comp)) ? $comp[0]['comp_id'] : ''; ?>"><?php _e('Save'); ?></button>
    </form>
</section>