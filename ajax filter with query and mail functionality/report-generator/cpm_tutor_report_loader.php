<?php

/* It prevents direct access to the file. */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * It enqueues the css and js files.
 */
function tutor_commission_addon_enqueue_script()
{
    wp_enqueue_script('cpm - ', "https://code.jquery.com/jquery-3.6.4.min.js");
    wp_enqueue_style('cpm - daterangepicker.css', "https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css");
    wp_enqueue_style('cpm_custom_for_tutor_css_admin', plugin_dir_url(__FILE__) . '/cpm_custom.css');
    wp_enqueue_script('cpm - jquery 3.3.1', "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js");
    wp_enqueue_script('cpm - owl-carousel2 2.3.4', "https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js");
    wp_enqueue_script('cpm - latest/jquery', "https://cdn.jsdelivr.net/jquery/latest/jquery.min.js");
    wp_enqueue_script('cpm - moment', "https://cdn.jsdelivr.net/momentjs/latest/moment.min.js");
    wp_enqueue_script('cpm - daterangepicker', "https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js");
    wp_enqueue_script('cpm_custom_for_tutor_script_admin', plugin_dir_url(__FILE__) . 'cpm_custom.js');
    wp_localize_script('cpm_custom_for_tutor_script_admin', 'exporterajax', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_localize_script('cpm_custom_for_tutor_script_admin', 'pluginajax', array('ajaxpluginurl' => plugin_dir_url(__FILE__) . 'quiz.php'));
}
add_action('admin_enqueue_scripts', 'tutor_commission_addon_enqueue_script');


function wpdocs_theme_name_scripts()
{
    wp_enqueue_script('cpm - ', "https://code.jquery.com/jquery-3.6.4.min.js");
    wp_enqueue_style('cpm - daterangepicker.css', "https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css");
    wp_enqueue_style('cpm_custom_for_tutor_css_frontend', plugin_dir_url(__FILE__) . '/cpm_custom_frontend.css');
    wp_enqueue_script('cpm - jquery 3.3.1', "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js");
    wp_enqueue_script('cpm - owl-carousel2 2.3.4', "https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js");
    wp_enqueue_script('cpm - latest/jquery', "https://cdn.jsdelivr.net/jquery/latest/jquery.min.js");
    wp_enqueue_script('cpm - moment', "https://cdn.jsdelivr.net/momentjs/latest/moment.min.js");
    wp_enqueue_script('cpm - daterangepicker', "https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js");
    wp_enqueue_script('cpm_custom_for_tutor_script_frontend', plugin_dir_url(__FILE__) . 'cpm_custom_frontend.js');
    wp_localize_script('cpm_custom_for_tutor_script_frontend', 'exporterajax', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_localize_script('cpm_custom_for_tutor_script_frontend', 'pluginajax', array('ajaxpluginurl' => plugin_dir_url(__FILE__) . 'quiz.php'));
}
add_action('wp_enqueue_scripts', 'wpdocs_theme_name_scripts');


/* It adds a submenu page to the tutor menu. */
add_action("admin_menu", "tutor_options_submenu", 99);

add_shortcode('generate_report', 'generate_report_shortcode');
function generate_report_shortcode()
{
    ob_start();
    tutor_report_generator_page();
    $output = ob_get_contents();
    ob_get_clean();
    return $output;
}



if (!function_exists('tutor_options_submenu')) {
    function tutor_options_submenu()
    {
        add_submenu_page(
            'tutor',
            'Report Generator',
            'Report Generator',
            'manage_options',
            'tutor-report-generator',
            'tutor_report_generator_page'
        );
    }
}


function tutor_report_generator_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "wpforms_entry_fields";
    $locations = $wpdb->get_results("SELECT DISTINCT value FROM $table_name WHERE `form_id` = 12646 AND `field_id` = 3");
    if (is_user_logged_in()) {

        $user = wp_get_current_user();

        $roles = $user->roles;
        if ($roles[0] == "administrator") {
            $is_admin = true;
        } else {
            $is_admin = false;
        }
    }

    $current_user = wp_get_current_user();
    $user = get_user_meta($current_user->ID);
    $current_user_location = $user['storelocation2'][0];
    $courses_args = array(
        'post_type' => 'courses',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    // $quiz_args = array(
    //     'post_type' => 'tutor_quiz',
    //     'post_status' => 'publish',
    //     'posts_per_page' => -1,
    //     'orderby' => 'title',
    //     'order' => 'ASC',
    // );

    // $quiz = new WP_Query($quiz_args);
    $courses = new WP_Query($courses_args);
?>
    <div class="cpm-wrapper">
        <h3 class="page_title">Generate Report</h3>
        <div class="report-page">

            <div class="location">
                <div class="loaction-label">
                    <label for="loaction">Choose Location</label>
                </div>
                <div class="cpm-select-wrapper">
                    <select id="cpm-location" onchange="getComboA()" class="tutor-form-control tutor-form-select tutor-js-form-select">
                    <?php if($is_admin){ ?>
                    <option value="">Select Location</option>
                        <?php foreach ($locations as $location) { ?>
                            <option value="<?php echo $location->value; ?>"><?php echo $location->value; ?></option>
                        <?php }}else{?>
                            <option value="<?php echo $current_user_location; ?>"><?php echo $current_user_location; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="course">
                <div class="loaction-label">
                    <label for="loaction">Choose Courses</label>
                </div>
                <div class="cpm-select-wrapper">
                    <select id="cpm-course" onchange="changeCourse()" class="tutor-form-control tutor-form-select tutor-js-form-select">
                        <option value="">Select Course</option>
                        <?php while ($courses->have_posts()) : $courses->the_post(); ?>
                            <option value=" <?php echo get_the_ID(); ?> "> <?php the_title(); ?> </option>
                        <?php endwhile;
                        wp_reset_postdata(); ?>
                    </select>
                </div>
            </div>
            <div class="quiz">
                <div class="loaction-label">
                    <label for="loaction">Choose Quiz</label>
                </div>
                <div class="cpm-select-wrapper">
                    <select id="cpm-quiz" onchange="getComboA()" class="tutor-form-control tutor-form-select tutor-js-form-select">
                        <option value="">Select course first</option>
                    </select>
                </div>
            </div>
            <div class="start_date">
                <div class="loaction-label">
                    <label for="date">From</label>
                </div>
                <div class="date-wrapper">
                    <input type="text" name="daterange" class="start_date_range" onchange="getComboA()" />
                </div>
            </div>
        </div>
        <div class="btn-wrapper">
            <div class="cpm_exporter">
                <input type="submit" value="Export CSV" class="cpm-table-export e-button e-button">
            </div>
        </div>
    </div>
    <div id='loader' style='display: none;'>
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="datafetch wrapper grids" id="datafetch">
    </div>
    <?php
}




add_action('wp_ajax_data_fetch', 'data_fetch');
add_action('wp_ajax_nopriv_data_fetch', 'data_fetch');
function data_fetch()
{
    global $wpdb;
    global $manager_email;
    $user_meta_table = $wpdb->prefix . "usermeta";
    $user_table = $wpdb->prefix . "users";
    $tutor_quiz_attempt_table = $wpdb->prefix . "tutor_quiz_attempts";
    $tutor_gradebooks_results_table = $wpdb->prefix . "tutor_gradebooks_results";
    $location = "'" . $_POST['location'] . "'";
    $days = $_POST['days'];
    $start_date = "'" . $_POST['startDate'] . "'";
    $end_date = "'" . $_POST['endDate'] . "'";
    $course = "'" . $_POST['course'] . "'";
    $quiz = "'" . $_POST['quiz'] . "'";
    $location_name = $_POST['location'];
    global $is_admin;
    if (is_user_logged_in()) {

        $user = wp_get_current_user();

        $roles = $user->roles;
        if ($roles[0] == "administrator") {
            $is_admin = true;
        } else {
            $is_admin = false;
        }
    }
    // $user_location = $user['storelocation2'][0];

    $manager_datas = $wpdb->get_results("SELECT * FROM $user_table WHERE ID IN (SELECT user_id FROM $user_meta_table WHERE `meta_value` LIKE $location )");
    foreach ($manager_datas as $manager_data) {
        if (get_user_meta($manager_data->ID, 'location_manager')[0] == 'checked') {
            $manager_email = $manager_data->user_email;
            $manager_name = $manager_data->display_name;
        }
    }



    $user_data = $wpdb->get_results("SELECT * FROM $user_table WHERE ID IN (SELECT user_id FROM $user_meta_table WHERE `meta_value` LIKE $location ) AND ID IN (SELECT user_id FROM $tutor_quiz_attempt_table WHERE `course_id` = $course AND `quiz_id` = $quiz) AND ID IN (SELECT user_id FROM $tutor_quiz_attempt_table WHERE `attempt_started_at` >= $start_date AND `attempt_ended_at` < $end_date)");

    if (!empty($user_data)) {
    ?>
        <h2>User Report Table</h2>
        <div class="main_report" id="main_report">
            <div class="content_only_for_mail" style="display: grid;line-height: 25px;margin: 0px 0 25px 0px;">
                <h3 class="admin_greeting_msg">Hi, <?php echo $manager_name; ?>,</h3>
                <h3 class="custom_greeting_msg" style='display: none;'>Hi,</h3>
                <span style="font-size: 14px;font-weight: 500;">Here is the report for <?php echo get_the_title($_POST['course']); ?>’s <?php echo get_the_title($_POST['quiz']); ?> for <?php echo $location_name; ?>.</span>
                <span style=" font-size: 14px;font-weight: 500;">The report is generated for quiz takers from <?php echo $_POST['startDate']; ?> to <?php echo $_POST['endDate']; ?>.</span>
            </div>
            <table id="user_table" style="font-family: arial, sans-serif; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">First Name</th>
                        <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">Last Name</th>
                        <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">Email Address</th>
                        <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">Store Location</th>
                        <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">Date Registered</th>
                        <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">Quiz Title</th>
                        <th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($user_data as $data) {
                        $user = get_user_meta($data->ID);
                        $first_name = $user['first_name'][0];
                        $last_name = $user['last_name'][0];
                        $user_location = $user['storelocation2'][0];
                        $grades = $wpdb->get_results("SELECT grade_name FROM $tutor_gradebooks_results_table WHERE `user_id` = $data->ID AND `quiz_id` = $quiz ");
                    ?>
                        <tr>
                            <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><?php echo $first_name; ?></td>
                            <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><?php echo $last_name; ?></td>
                            <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><?php echo $data->user_email; ?></td>
                            <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><?php echo $user_location; ?></td>
                            <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><?php echo date_format(date_create($data->user_registered), "m/d/y H:i A"); ?></td>
                            <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><?php echo get_the_title($_POST['quiz']); ?></td>
                            <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><?php echo $grades[0]->grade_name; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div id='loader' style='display: none;'>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="updated notice is-dismissible" style="display: none;" id="mail_sent">Email Sent.</div>
        <div class="mail_form">
            <?php if (!empty($manager_name)) { ?>
                <div class="manager_details">
                    <form method="POST" id="email_manager_form">
                        <input type="text" name="manager_email" value="<?php echo $manager_email; ?>" id="email_text" hidden>
                        <input type="submit" name="submit" value="Mail Report" id="manager_email_submit_btn" class="e-button e-button">
                    </form>
                </div>
                <?php if ($is_admin) { ?>
                    <div class="custom_emails">
                        <form method="post" id="custom_email_form">
                            <div id="fakeinput" class="fake-input--box group">
                                <input id="tags-input" name="taginput" type="email" placeholder="Enter Email with seperated comma" />
                                <input type="submit" value="Send Mail Report" id="custom_email_submit_btn" class="e-button e-button">
                            </div>
                        </form>
                    </div>
                <?php } ?>
        </div>

    <?php } else { ?>
        <div class="notice e-notice e-notice--dismissible e-notice--extended">Manager is not assigned to selected location.</div>
    <?php } ?>
    </div>
<?php  } else { ?>
    <div class="notice e-notice e-notice--dismissible e-notice--extended no-data-found">No data found.</div>
<?php }
    die();
}

add_action('wp_ajax_mail_function', 'mail_function');
add_action('wp_ajax_nopriv_mail_function', 'mail_function');

function mail_function()
{
    $table = urldecode($_POST['table']);
    $email = $_POST['mail'];
    print_r($table);
    $admin_email = get_bloginfo('admin_email');
    $headers = "From: " . $admin_email . "\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $mail = wp_mail($email, 'Report', $table, $headers);
    if ($mail) {
        var_dump($email);
        echo 'done';
    }
}

/**
 * It adds a checkbox to the user profile page.
 * 
 * @param user The user object.
 */
add_action('show_user_profile', 'extra_user_profile_fields');
add_action('edit_user_profile', 'extra_user_profile_fields');

function extra_user_profile_fields($user)
{
    $user_manager = get_user_meta($user->ID, 'location_manager')[0];
?>
<div class="location_manager" style="display: flex;">
    <h2><?php _e("Is the user manager of his location ?", "blank"); ?></h2>
    <input type="checkbox" name="manager_checkbox" value="checked" <?php echo !empty($user_manager) ? $user_manager : ''; ?>>
</div>
<?php }

/**
 * If the nonce is valid, and the current user can edit the user, then update the user meta
 * 
 * @param user_id The ID of the user being edited.
 * 
 * @return The value of the checkbox.
 */
add_action('personal_options_update', 'save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'save_extra_user_profile_fields');

function save_extra_user_profile_fields($user_id)
{
    if (empty($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $user_id)) {
        return;
    }

    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'location_manager', $_POST['manager_checkbox']);
}


add_filter('tutor_dashboard/nav_items', 'add_some_links_dashboard');
/**
 * Add_filter('tutor_dashboard/nav_items', 'add_some_links_dashboard');
 * 
 * @param links The array of links to be displayed in the dashboard.
 * 
 * @return The  array is being returned.
 */
function add_some_links_dashboard($links)
{
    if (is_user_logged_in()) {

        $user = wp_get_current_user();

        $roles = $user->roles;
        if ($roles[0] == "um_store-manager") {
            $links['student_report'] = [
                "title" =>    __('Students Report', 'tutor'),
                "icon" => "tutor-icon-download-bold",

            ];
            return $links;
        }
        return $links;
    }
}


if (!function_exists('cpm_comment_exporter_csv_files')) {
    function cpm_comment_exporter_csv_files($post)
    {

        global $wpdb;
        global $manager_email;
        $user_meta_table = $wpdb->prefix . "usermeta";
        $user_table = $wpdb->prefix . "users";
        $tutor_quiz_attempt_table = $wpdb->prefix . "tutor_quiz_attempts";
        $tutor_gradebooks_results_table = $wpdb->prefix . "tutor_gradebooks_results";
        $location = "'" . $_POST['location'] . "'";
        $days = $_POST['days'];
        $start_date = "'" . $_POST['startDate'] . "'";
        $end_date = "'" . $_POST['endDate'] . "'";
        $course = "'" . $_POST['course'] . "'";
        $quiz = "'" . $_POST['quiz'] . "'";
        $location_name = $_POST['location'];


        $manager_datas = $wpdb->get_results("SELECT * FROM $user_table WHERE ID IN (SELECT user_id FROM $user_meta_table WHERE `meta_value` LIKE $location )");
        foreach ($manager_datas as $manager_data) {
            if (get_user_meta($manager_data->ID, 'location_manager')[0] == 'checked') {
                $manager_email = $manager_data->user_email;
                $manager_name = $manager_data->display_name;
            }
        }

        $user_data = $wpdb->get_results("SELECT * FROM $user_table WHERE ID IN (SELECT user_id FROM $user_meta_table WHERE `meta_value` LIKE $location ) AND ID IN (SELECT user_id FROM $tutor_quiz_attempt_table WHERE `course_id` = $course AND `quiz_id` = $quiz) AND ID IN (SELECT user_id FROM $tutor_quiz_attempt_table WHERE `attempt_started_at` >= $start_date AND `attempt_started_at` < $end_date)");


        if (!empty($user_data)) {

            $cpm_comment_exporter_generate_csv_filename = 'tutor' . '-' . date('Ymd_His') . '-export.csv';

            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename={$cpm_comment_exporter_generate_csv_filename}');
            $output = fopen('php://output', 'w');

            fputcsv($output, ['First Name', 'Last Name', 'Email Address', 'Store Location', 'Date Register', 'Quiz Title', 'Grade']);
            foreach ($user_data as $data) {
                # code...
                $user = get_user_meta($data->ID);
                $first_name = $user['first_name'][0];
                $last_name = $user['last_name'][0];
                $user_location = $user['storelocation2'][0];
                $grades = $wpdb->get_results("SELECT grade_name FROM $tutor_gradebooks_results_table WHERE `user_id` = $data->ID AND `quiz_id` = $quiz ");

                fputcsv($output, [$first_name,  $last_name, $data->user_email, $user_location, date_format(date_create($data->user_registered), "m/d/y H:i A"), get_the_title($_POST['quiz']), $grades[0]->grade_name]);
            }

            fclose($output);
        }


        die();
    }

    add_action('wp_ajax_cpm_comment_exporter_csv_files', 'cpm_comment_exporter_csv_files');
    add_action('wp_ajax_nopriv_cpm_comment_exporter_csv_files', 'cpm_comment_exporter_csv_files');
}


add_action('wp_ajax_custom_mail_function', 'custom_mail_function');
add_action('wp_ajax_nopriv_custom_mail_function', 'custom_mail_function');

function custom_mail_function()
{
    $table = urldecode($_POST['table']);
    $emails[] = $_POST['mail'];
    print_r($table);
    print_r($emails);
    $admin_email = get_bloginfo('admin_email');
    $headers = "From: " . $admin_email . "\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    // foreach ($emails[0] as $email) {
        $mail = wp_mail($emails[0], 'Report', $table, $headers);
        if ($mail) {
            var_dump($emails[0]);
            echo 'done';
        }
    // }
    die();
}
