<?php
/*
Plugin Name: XSS
Plugin URI: http://kael.me
Description: XSS demo
Version: 1.0.1
Author: Kael Zhang
Author URI: http://kael.me
*/

date_default_timezone_set('Asia/Shanghai');

define ('KM_S_PATH', dirname(__FILE__) . '/');

$km_host = 'http://' . $_SERVER['HTTP_HOST'];
$km_home = get_option('home');
$km_uri = str_replace( $km_home, '', $km_host . $_SERVER['REQUEST_URI'] );

function startsWith($haystack, $needle){
    return !strncmp($haystack, $needle, strlen($needle));
}

require_once( ABSPATH . 'wp-includes/pluggable.php' );

$table_name = $table_prefix . 'xss';


if( startsWith( $km_uri, '/save' ) ){
    global $current_user;
    get_currentuserinfo();

    $user_name = $current_user -> user_login;
    $comment = $_GET['comment'];

    // $comment = htmlentities( $comment );

    if( empty( $user_name ) ){
        echo json_encode(array(
            'code' => 403,
            'msg' => 'please log in'
        ));

    }else if( empty( $comment ) ){
        echo json_encode(array(
            'code' => 500,
            'msg' => 'comment should not be empty'
        ));

    }else{

        $wpdb -> query( "INSERT INTO $table_name (user, comment) VALUES ( '$user_name', '$comment' )" );

        echo json_encode(array(
            'code' => 200
        ));
    }

    exit;
    // echo is_user_logged_in() ? 'yes' : 'no';
}


if( startsWith( $km_uri, '/read' ) ){
    $after = $_GET['after'];

    if( !isset( $after ) ){
        echo json_encode(array(
            'code' => 500,
            'msg' => 'please specify an start time with "after"'
        ));

    }else{
        $date = date('Y-m-d H:i:s', $after); 

        $comments = $wpdb -> get_results( "SELECT user, comment, id FROM $table_name where time > '$date' ORDER BY id DESC LIMIT 100" );

        echo json_encode(array(
            'code' => 200,
            'comments' => $comments,
            'now' => time(),
            // 'sql' => "SELECT user, comment FROM $table_name where time > '$date' LIMIT 30",
            'after' => $after
        ));

    }

    exit;
}

?>