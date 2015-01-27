<?php
/**
 * This file is a part of FullStackEngineer.Net Project.
 *
 * FullStackEngineer.Net is a web site for hosting webpages
 * (especially the documents, forums) of open source projects.
 *
 * FullStackEngineer project itself is an open source project.
 *
 * For more information, please refer to:
 *
 *		http://www.fullstackengineer.net/
 *
 * Copyright (C) 2015 WEI Yongming
 * <http://www.fullstackengineer.net/zh/engineer/weiyongming>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
defined('C5_EXECUTE') or die('Access Denied.');

Class PageActionStatus {
	public $time;
	public $action;
	public $status;
	public $message;
	public $form_id;
}

function set_page_action_status ($page_id, $action, $status, $message, $form_id = '')
{
	$pas = New PageActionStatus;
	$pas->time = time();
	$pas->action = $action;
	$pas->status = $status;
	$pas->message = $message;
	$pas->form_id = '' . $form_id;

	$_SESSION["PAS-$page_id"] = $pas;
}

function get_page_action_status ($page_id, $clear = TRUE)
{
	$pas = $_SESSION["PAS-$page_id"];
	if ($pas == NULL) {
		$pas = New PageActionStatus;
		$pas->time = 0;
		$pas->action = 'na';
		$pas->status = 'clear';
		$pas->message = 'N/A';
		$pas->form_id = '';
	}
	else if ($clear) {
		unset ($_SESSION["PAS-$page_id"]);
	}

	return $pas;
}

function get_url_from_file_id ($avatar_file_id, $def_url = '/files/images/icon-def-avatar.png')
{
	$avatar_url = File::getRelativePathFromID ($avatar_file_id);
	if (strlen ($avatar_url) < 10) {
		return $def_url;
	}

	return $avatar_url;
}

function get_thumbnail_url_from_file_id ($avatar_file_id, $def_url, $level = 1)
{
	$f = File::getByID ($avatar_file_id);
	if ($f->error != 0) {
		$avatar_url = $def_url;
	}
	else {
		$fv = $f->getRecentVersion();
		$avatar_url = $fv->getThumbnailSRC ($level);
	}

	if (strlen ($avatar_url) < 10) {
		return $def_url;
	}

	return $avatar_url;
}

function h5 ($str)
{
	return htmlspecialchars ($str, ENT_QUOTES | ENT_HTML5);
}

function get_session_default_locale () {
	// they have a language in a certain session going already
	if (isset($_SESSION['DEFAULT_LOCALE'])) {
		return $_SESSION['DEFAULT_LOCALE'];
	}

	// if they've specified their own default locale to remember
	if(isset($_COOKIE['DEFAULT_LOCALE'])) {
		return $_COOKIE['DEFAULT_LOCALE'];
	}

	Loader::library('3rdparty/Zend/Locale');
	$locale = new Zend_Locale();

	return (string)$locale;
}

