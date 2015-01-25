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

require_once ('helpers/fsen/DocSectionManager.php');
require_once ('helpers/fsen/ProjectInfo.php');

Loader::model ('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

$txt = Loader::helper ('text');
$domain_handle = $txt->sanitize ($_GET ['domainHandle']);
$section_id = $txt->sanitize ($_GET ['sectionID']);

$json = Loader::helper ('json');
class ReturnInfo {
	public $status;
	public $detail;
	public $comment_info;
}

$ret_info = new ReturnInfo;
$ret_info->status = 'error';
$ret_info->detail = t('N/A');
$ret_info->comment_info = array ();

if (!in_array ($domain_handle, ProjectInfo::$mDomainList)) {
	$ret_info->detail = t('Bad domain');
	echo $json->encode ($ret_info);
	exit (0);
}

if (!preg_match ("/^[a-f0-9]{32}$/", $section_id)) {
	$ret_info->detail = t('Bad section');
	echo $json->encode ($ret_info);
	exit (0);
}

$comment_info = DocSectionManager::getSectionCommentInfo ($domain_handle, $section_id);
if ($comment_info == false) {
	$ret_info->detail = t('Not existed section');
	echo $json->encode ($ret_info);
	exit (0);
}

$ret_info->status = 'success';
$ret_info->comment_info = $comment_info;
echo $json->encode ($ret_info);
exit (0);

?>

