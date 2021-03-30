<?php $version = file_get_contents(APP_ROOT . 'version.txt'); ?>
<section class="w3-panel w3-blue w3-round w3-padding w3-card-4 w3-margin-right"><span
        class="w3-xxlarge">Competition</span>&nbsp;<small><i>v<?php echo $version; ?></i></small><br /><i><small>WordPress Demo Plugin developed by ImI S</small></i>
</section>
<span id="validationStatus" class="w3-hide" data-url="<?php echo APP_URL; ?>"
    data-ajax="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce(); ?>"
    data-site="<?php echo SITE_URL; ?>"></span>