<?php
	$disable = $_REQUEST["disable"];
	$user_lock_id = $_REQUEST["user_lock_id"];
	$message = $_REQUEST["message"];
	if ($disable === "true") {
		update_user_meta ($user_lock_id, 'ja_disable_user_des', $message);
		update_user_meta ($user_lock_id, 'ja_disable_user', 1);
	} else {
		update_user_meta ($user_lock_id, 'ja_disable_user_des', "");
		update_user_meta ($user_lock_id, 'ja_disable_user', 0);
	}

	header('Location: ' . $_SERVER['HTTP_REFERER']);
?>