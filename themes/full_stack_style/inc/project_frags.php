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

$page_id = $c->getCollectionID ();
$page_path = $c->getCollectionPath ();
$path_frags = explode ('/', trim ($page_path, '/'));
$doc_lang = $path_frags[0];

switch ($path_frags[1]) {
case 'help':
case 'blog':
	$project_id = SYSTEM_PROJECT_SHORTNAME . '-' . $doc_lang;
	$domain_handle = 'document';
	$volume_handle = $path_frags[1];

	if (isset ($path_frags[2])) {
		$part_handle = $path_frags[2];
	}
	else {
		$part_handle = 'na';
	}
	if (isset ($path_frags[3])) {
		$chapter_handle = $path_frags[3];
	}
	else {
		$chapter_handle = 'na';
	}
	break;

case 'community':
case 'misc':
	$project_id = SYSTEM_PROJECT_SHORTNAME . '-' . $doc_lang;
	$domain_handle = $path_frags[1];

	if (isset ($path_frags[2])) {
		$volume_handle = $path_frags[2];
	}
	else {
		$volume_handle = 'na';
	}
	if (isset ($path_frags[3])) {
		$part_handle = $path_frags[3];
	}
	else {
		$part_handle = 'na';
	}
	if (isset ($path_frags[4])) {
		$chapter_handle = $path_frags[4];
	}
	else {
		$chapter_handle = 'na';
	}
	break;

case 'project':
	if (isset ($path_frags[2])) {
		$project_id = $path_frags[2];
	}
	else {
		$project_id = SYSTEM_PROJECT_SHORTNAME . '-' . $doc_lang;
	}
	if (isset ($path_frags[3])) {
		$domain_handle = $path_frags[3];
	}
	else {
		$domain_handle = 'home';
	}
	if (isset ($path_frags[4])) {
		$volume_handle = $path_frags[4];
	}
	else {
		$volume_handle = 'na';
	}
	if (isset ($path_frags[5])) {
		$part_handle = $path_frags[5];
	}
	else {
		$part_handle = 'na';
	}
	if (isset ($path_frags[6])) {
		$chapter_handle = $path_frags[6];
	}
	else {
		$chapter_handle = 'na';
	}

	break;

default:
	$project_id = SYSTEM_PROJECT_SHORTNAME . '-' . $doc_lang;
	$domain_handle = 'na';
	$volume_handle = 'na';
	$part_handle = 'na';
	$chapter_handle = 'na';
	break;
}

$project_shortname = substr ($project_id, 0, strlen ($project_id) - 3);
if ($project_shortname == SYSTEM_PROJECT_SHORTNAME) {
	$page_style = 'global';
}
else {
	$page_style = 'default';
}

?>
