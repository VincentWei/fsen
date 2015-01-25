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
defined('C5_EXECUTE') or die("Access Denied.");

require_once ('helpers/fsen/FSEInfo.php');
require_once ('helpers/fsen/DocSectionManager.php');
require_once ('helpers/fsen/ProjectInfo.php');

Loader::model ('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

$txt = Loader::helper ('text');
$domain_handle = $txt->sanitize ($_GET ['domainHandle']);
$section_id = $txt->sanitize ($_GET ['sectionID']);
$nr_comments = (int)$txt->sanitize ($_GET ['nrComments']);

$json = Loader::helper ('json');
class ReturnInfo {
	public $status;
	public $section_id;
	public $nr_total_comments;
	public $title;
	public $detail;
}

$ret_info = new ReturnInfo;
$ret_info->status = 'error';
$ret_info->section_id = $section_id;
$ret_info->nr_total_comments = 0;
$ret_info->title = t('Latest Comments...');
$ret_info->detail = t('N/A');

if (!in_array ($domain_handle, ProjectInfo::$mDomainList)) {
	$ret_info->detail = t('Bad domain handle!');
	echo $json->encode ($ret_info);
	exit (0);
}

if (!preg_match ("/^[a-f0-9]{32}$/", $section_id)) {
	$ret_info->detail = t('Bad section!');
	echo $json->encode ($ret_info);
	exit (0);
}

$section_info = DocSectionManager::getSectionInfo ($domain_handle, $section_id);
if ($section_info == false) {
	$ret_info->detail = t('No such section!');
	echo $json->encode ($ret_info);
	exit (0);
}

$ret_info->status = 'success';
$ret_info->nr_total_comments = $section_info['nr_comments'];
$comments = DocSectionManager::getCachedComments ($domain_handle, $section_id);
if (count ($comments) == 0) {
	$ret_info->detail = '';
	echo $json->encode ($ret_info);
	exit (0);
}

if ($nr_comments < 1) {
	$nr_comments = 1;
}

$nr = 0;

$ret_info->detail = '<ul class="list-group" style="width:300px;">';
foreach ($comments as $comment) {
	if ($nr >= $nr_comments) {
		break;
	}

	$author_name_info = FSEInfo::getNameInfo ($comment['author_id']);
	if ($author_name_info == false) {
		continue;
	}
	$author_info = FSEInfo::getBasicProfile ($author_name_info['user_name']);

	unset ($replied_name_info);
	if (preg_match ("/^[0-9a-f]{32}$/", $comment ['replied_author_id'])) {
		$replied_name_info = FSEInfo::getNameInfo ($comment ['replied_author_id']);
		if ($replied_name_info == false) {
			unset ($replied_name_info);
		}
	}

	$ret_info->detail .= '
	<li class="list-group-item">
		<p>
			<small><strong class="text-info">' . $author_info['nick_name'] . '</strong>' . (isset ($replied_name_info)?(t('Reply to ') . '<strong class="text-info">' . $replied_name_info['nick_name']) . '</strong>' : '') . ': ' . h5 ($comment['body']) . '
			</small>
		</p>
	</li>';

	$nr++;
}

$ret_info->detail .= '</ul>';

echo $json->encode ($ret_info);
exit (0);

?>

