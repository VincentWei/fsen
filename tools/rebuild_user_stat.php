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

exit (0);

$db = Loader::db();

echo 'Check comments... <br/>';
flush();
ob_flush();

$rs = $db->Execute ("SELECT author_id, SUM(nr_comments) AS total_comments, SUM(nr_praise) AS total_praise,
		SUM(nr_favorites) AS total_favorites
	FROM fsen_document_sections_all GROUP BY author_id");
while ($row = $rs->FetchRow()) {
	$db->Execute ("UPDATE fse_basic_profiles SET nr_comments=? WHERE fse_id=?",
			array ($row['total_comments'], $row['author_id']));

	if ($db->Affected_Rows () > 0) {
		echo "Inconsistency found: " . $row['author_id'] . '<br/>';
		flush();
		ob_flush();
	}
}

echo 'Check sections... <br/>';
flush();
ob_flush();

$rs = $db->Execute ("SELECT author_id, COUNT(*) AS total_sections FROM fsen_document_sections_all GROUP BY author_id");
while ($row = $rs->FetchRow()) {
	$db->Execute ("UPDATE fse_basic_profiles SET nr_sections=? WHERE fse_id=?",
			array ($row['total_sections'], $row['author_id']));

	if ($db->Affected_Rows () > 0) {
		echo "Inconsistency found: " . $row['author_id'] . '<br/>';
		flush();
		ob_flush();
	}
}

echo 'Check chapters... <br/>';
flush();
ob_flush();

$rs = $db->Execute ("SELECT fse_id, COUNT(*) AS total_chapters FROM fsen_project_doc_volume_part_chapters_all
	GROUP BY fse_id");
while ($row = $rs->FetchRow()) {
	$db->Execute ("UPDATE fse_basic_profiles SET nr_chapters=? WHERE fse_id=?",
			array ($row['total_chapters'], $row['fse_id']));

	if ($db->Affected_Rows () > 0) {
		echo "Inconsistency found: " . $row['fse_id'] . '<br/>';
		flush();
		ob_flush();
	}
}


