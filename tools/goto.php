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

require_once('helpers/fsen/FSEInfo.php');

$wiki_id = $_GET['WikiID'];
$prefix = substr ($wiki_id, 0, 3);
if ($prefix == 'FSP') {
	$project_id = substr ($wiki_id, 3);
	$doc_lang = substr ($project_id, -2);
	header ("location:/$doc_lang/project/$project_id");
	exit (0);
}
else if ($prefix == 'FSE') {
	$fse_user_name = substr ($wiki_id, 3);
	$fse_info = FSEInfo::getBasicProfile ($fse_user_name);
	if ($fse_info) {
		$home_link = FSEInfo::getPersonalHomeLink ($fse_info);
		header ("location:$home_link");
		exit (0);
	}
}
else if ($prefix == 'htt') {
	header ("location:$wiki_id");
	exit (0);
}

header ('location:/');

