<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Ech_Professionals_List
 * @subpackage Ech_Professionals_List/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->


<div class="echPlg_wrap">
    <h1>ECH Professionals List General Settings</h1>

    <div class="plg_intro">
        <p> More shortcode attributes and guidelines, visit <a href="#" target="_blank">Github</a>. </p>
        <div class="shtcode_container">
            <pre id="sample_shortcode">[ech_pl]</pre>
            <div id="copyMsg"></div>
            <button id="copyShortcode">Copy Shortcode</button>
        </div>
    </div>

    <div class="form_container">
        <form method="post" id="ech_pl_settings_form">
        <?php 
            settings_fields( 'ech_pl_gen_settings' );
            do_settings_sections( 'ech_pl_gen_settings' );
        ?>
            <h2>General</h2>
            <div class="form_row">
                <?php $getApiEnv = get_option( 'ech_pl_apply_api_env' ); ?>
                <label>Connect to Global CMS API environment : </label>
                <select name="ech_pl_apply_api_env" id="">
                    <option value="0" <?= ($getApiEnv == "0") ? 'selected' : '' ?>>Dev/UAT</option>
                    <option value="1" <?= ($getApiEnv == "1") ? 'selected' : '' ?>>Live</option>
                </select>
            </div>
            <div class="form_row">
                <?php $getPPP = get_option( 'ech_pl_ppp' ); ?>
                <label>Post per page : </label>
                <input type="text" name="ech_pl_ppp" id="ech_pl_ppp" pattern="[1-9]{1,}" value="<?=$getPPP?>">
            </div>


            <div class="form_row">
                <button type="submit"> Save </button>
            </div>
        </form>
        <div class="statusMsg"></div>


    </div> <!-- form_container -->
</div>

