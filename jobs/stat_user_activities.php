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

class StatUserActivities extends Job {

	public function getJobName () {
		return "Stat User Activities";
	}

	public function getJobDescription () {
		return "This job stats the user activities and update the heat level of chapters and projects";
	}

	public function run () {

		try {
			$doc_langs = array ('en', 'zh');

			$db = Loader::db ();
			foreach ($doc_langs as $doc_lang) {
				$rs = $db->Execute ("SELECT * FROM fsen_stat_chapter_activities_$doc_lang");
				while ($row = $rs->FetchRow()) {
					$db->Execute ("UPDATE fsen_project_doc_volume_part_chapters_$doc_lang
	SET nr_sections=?, nr_comments=?, nr_praise=?, nr_shares=?, nr_favorites=?
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND chapter_handle=?",
						array ($row ['total_sections'], $row ['total_comments'], $row['total_praise'],
							$row['total_shares'], $row['total_favorites'],
							$row['project_id'], $row['domain_handle'], $row['volume_handle'], $row['part_handle'],
							$row['chapter_handle']));

				}

				$rs = $db->Execute ("SELECT project_id, (overall_sections*100 + overall_comments*70 + overall_shares*10
		+ overall_praise*10 + overall_favorites*10) AS heat_level FROM fsen_stat_project_activities_$doc_lang");
				while ($row = $rs->FetchRow()) {
					$db->Execute ("UPDATE fsen_projects SET heat_level=? WHERE project_id=?",
						array ($row ['heat_level'], $row['project_id']));

				}
			}

			return 'Done';
		}
		catch (Exception $x) {
			throw $x;
		}
	}
}
