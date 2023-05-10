<?php
define( 'SHORTINIT', true );
$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
require($path.'wp-load.php' );
// require($path. "wp-config.php");
global $wpdb;
$course_id = $_POST['course'];
$table_name = $wpdb->prefix . "tutor_quiz_attempts";

$quizs = $wpdb->get_results("SELECT ID,post_title FROM `wp_posts` where ID IN (SELECT DISTINCT quiz_id FROM $table_name WHERE `course_id` = $course_id)");
if (!empty($quizs)) { ?>
    <option value> Select Quiz</option>
    <?php foreach ($quizs as $quiz) { 
        ?>

        <option value=" <?php echo $quiz->ID; ?> "> <?php echo $quiz->post_title; ?></option>
    <?php }
}else{ ?>
    <option value class="check"> Select Course First</option>
<?php }
