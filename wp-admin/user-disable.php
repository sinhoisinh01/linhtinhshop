<?php
	require_once( dirname( __FILE__ ) . '/admin.php' );

	$disable = $_REQUEST["disable"];
	$id = $_REQUEST["id"];
	$message = $_REQUEST["message"];
	if ($disable === "true") {
		update_user_meta ($id, 'ja_disable_user_des', $message);update_user_meta ($id, 'ja_disable_user', 1);
	} else {
		update_user_meta ($id, 'ja_disable_user_des', "");
		update_user_meta ($id, 'ja_disable_user', 0);
	}

	header('Location: ' . $_SERVER['HTTP_REFERER']);
?>