<section class="w3-row w3-margin-right">
    <header class="w3-margin-bottom">
        <h1><?php _e('Review Competitions'); ?></h1>        
    </header>
</section>
<section id="table_container" class="w3-row w3-margin-right">
    <?php if(!empty($competitions)) {
        echo '<div class="w3-row w3-margin-bottom"><small><i>(' . __('To review a competition please click on its name in the table.') . ')</i></small></div>';
        echo '<div class="w3-row"><button class="w3-btn w3-red w3-round delete" data-action="comp_delete_competition">Delete Competition</button></div>';
        echo '<div class="w3-row w3-margin-top"><table id="competitions_table"><thead><th><input class="w3-check check_all" type="checkbox" name="del_all" />&nbsp;Delete</th><th>Title</th><th>Update</th></thead><tbody>';

        foreach($competitions as $comp) {
            echo '<tr>';
            echo "<td class=\"w3-center\"><input id=\"{$comp['comp_id']}\" type=\"checkbox\" class=\"check\" name=\"del_comp\" /></td>";
            echo '<td class="w3-center"><span class="link modal_open_btn" data-id="' . $comp['comp_id'] . '" data-action="comp_review_competition">' . $comp['comp_title'] . '</span></td>';
            echo '<td class="w3-center"><a href="' . SITE_URL . '/wp-admin/admin.php?page=comp_set_competition&id=' . $comp['comp_id'] . '" class="w3-btn w3-orange w3-round">Update</a></td>';
            echo '</tr>';                       
        }
        echo '</tbody></table></div>';
    } else { ?>
    <article id="competitions_alert" class="w3-panel w3-red w3-round w3-padding w3-card-4 w3-large"><?php _e('Currently there is
        no saved competition.'); ?></article>
    <?php } ?>
</section>