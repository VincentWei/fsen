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

require_once ('helpers/misc.php');
require_once ('helpers/fsen/FSEInfo.php');

class ProjectInfo {

	const MAX_ATTACHED_FILES	= 4; /* TODO: move this definition to DocSectionManager */
	const NR_LATEST_CHAPTERS	= 5;
	const NR_TOP_BLOGS			= 20;
	const NR_SUGGESTED_BLOGS	= 50;
	const NR_ALL_BLOG_AUTHORS	= 100;
	const PROJECT_CACHE_EXPIRED_TIME = 14400; /* 4 hours */

	const DEF_ICON_URL = '/files/images/icon-fsen-144.png';

	const OWNER_MARKDOWN_TEXT = '
<div class="panel-heading">
	<h3 class="panel-title">%1$s</h3>
</div>
<div class="panel-body">
	<div class="media">
  		<a class="media-left" href="%2$s"><img
			class="media-object img-rounded" alt="Avatar"
			src="%3$s"
			style="width: 48px; height: 48px;" /></a>
  		<div class="media-body">
			<h4 class="media-heading">%4$s</h4>
				<p>%5$s</p>
  		</div>
	</div>
</div>
';

	private static $mDomain2HomePageTypeMap = array (
			'download'		=> 'project_download_homepage',
			'document'		=> 'project_doc_homepage',
			'community'		=> 'project_comm_homepage',
			'contribute'	=> 'project_contribute_homepage',
			'misc'			=> 'project_misc_homepage',

			'sys/community'	=> 'project_arena_homepage',
		);

	private static $mDomain2VolumePageTypeMap = array (
			'download' 	=> 'project_download_default',
			'document'	=> 'project_doc_volume',
			'community'	=> 'project_comm_forum_area',
			'contribute'=> 'project_contribute_default',
			'misc/faqs' => 'project_misc_faqs',
			'misc'		=> 'project_misc_default',

			'sys/community'		=> 'project_arena_zone',
			'sys/document/blog'	=> 'project_doc_all_blogs',
		);

	private static $mDomain2PartPageTypeMap = array (
			'document' => 'project_doc_part',
			'community' => 'project_comm_forum',

			'sys/community'		=> 'project_arena',
			'sys/document/blog'	=> 'project_doc_fse_blogs',
		);

	private static $mDomain2ChapterPageTypeMap = array (
			'document' => 'project_doc_chapter',
			'community' => 'project_comm_thread',

			'sys/community'		=> 'project_arena_subject',
			'sys/document/blog'	=> 'project_doc_blog',
		);

	const NR_ROLES = 5;
	public static $mMemberRoleList = array ('g-adm', 'c-adm', 'p-edt', 'c-cmt', 'g-mmb');

	public static $mDomainList = array ('home', 'download', 'document', 'community', 'contribute', 'misc');

	public static $mRoleDescriptions = array (
			'en' => array (
				'owner' => 'The project owner',
				'g-adm' => 'General administrator',
				'c-adm' => 'Community administrator',
				'p-edt' => 'Page editor',
				'c-cmt' => 'Code Commiter',
				'g-mmb' => 'General Member',
				),
			'zh' => array (
				'owner' => '项目所有者',
				'g-adm' => '一般管理员',
				'c-adm' => '社区管理员',
				'p-edt' => '页面编辑',
				'c-cmt' => '代码提交人',
				'g-mmb' => '一般成员',
			)
		);

	public static function assemblePath ($project_id, $domain_handle,
			$volume_handle = 'na', $part_handle = 'na', $chapter_handle = 'na')
	{
		$project_shortname = substr ($project_id, 0, strlen ($project_id) - 3);
		$doc_lang = substr ($project_id, -2);

		if ($project_shortname == SYSTEM_PROJECT_SHORTNAME) {
			if ($domain_handle == 'home') {
					return "/$doc_lang";
			}
			else if ($domain_handle == 'document') {
				if ($chapter_handle != 'na') {
					return "/$doc_lang/$volume_handle/$part_handle/$chapter_handle";
				}
				else if ($part_handle != 'na') {
					return "/$doc_lang/$volume_handle/$part_handle";
				}
				else if ($volume_handle != 'na') {
					return "/$doc_lang/$volume_handle";
				}
				else {
					return "/$doc_lang";
				}
			}
			else if ($domain_handle == 'misc') {
				if ($chapter_handle != 'na') {
					return "/$doc_lang/misc/$volume_handle/$part_handle/$chapter_handle";
				}
				else if ($part_handle != 'na') {
					return "/$doc_lang/misc/$volume_handle/$part_handle";
				}
				else if ($volume_handle != 'na') {
					return "/$doc_lang/misc/$volume_handle";
				}
				else {
					return "/$doc_lang/misc";
				}
			}
			else if ($domain_handle == 'community') {
				if ($chapter_handle != 'na') {
					return "/$doc_lang/community/$volume_handle/$part_handle/$chapter_handle";
				}
				else if ($part_handle != 'na') {
					return "/$doc_lang/community/$volume_handle/$part_handle";
				}
				else if ($volume_handle != 'na') {
					return "/$doc_lang/community/$volume_handle";
				}
				else {
					return "/$doc_lang/community";
				}
			}
			else {
				return "/$doc_lang";
			}
		}

		if ($domain_handle == 'home') {
			return "/$doc_lang/project/$project_id";
		}
		else if ($chapter_handle != 'na') {
			return "/$doc_lang/project/$project_id/$domain_handle/$volume_handle/$part_handle/$chapter_handle";
		}
		else if ($part_handle != 'na') {
			return "/$doc_lang/project/$project_id/$domain_handle/$volume_handle/$part_handle";
		}
		else if ($volume_handle != 'na') {
			return "/$doc_lang/project/$project_id/$domain_handle/$volume_handle";
		}
		else {
			return "/$doc_lang/project/$project_id/$domain_handle";
		}
	}

	function getIconURL ($icon_file_id) {
		$icon_url = File::getRelativePathFromID ($icon_file_id);
		if (strlen ($icon_url) < 10) {
			return self::DEF_ICON_URL;
		}

		return $icon_url;
	}

	public static function getProjectPage ($project_id, $domain_handle, $volume_handle = 'na',
			$part_handle = 'na', $chapter_handle = 'na')
	{
		$page_path = self::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		if ($page_path == '/') {
			$page = Page::getByID (HOME_CID);
		}
		else {
			$page = Page::getByPath ($page_path);
		}

		if ($page->getCollectionID () == false) {
			return false;
		}

		return $page;
	}

	private static function getDomainPageType ($project_id, $domain_handle)
	{
		if (preg_match ("/^sys-[a-z]{2}$/", $project_id)) {
			$page_type_handle = self::$mDomain2HomePageTypeMap["sys/$domain_handle"];
		}

		if (!isset ($page_type_handle)) {
			$page_type_handle = self::$mDomain2HomePageTypeMap[$domain_handle];
		}

		return CollectionType::getByHandle ($page_type_handle);
	}

	public static function addDomainPage ($project_id, $homepage, $domain_handle, $domain_name, $domain_desc)
	{
		$page_path = self::assemblePath ($project_id, $domain_handle, 'na', 'na', 'na');
		if ($page_path == '/') {
			return Page::getByID (1);
		}

		$page = Page::getByPath ($page_path);
		if ($page->getCollectionID() > 0) {
			return $page;
		}

		$page_type = self::getDomainPageType ($project_id, $domain_handle);
		if (!($page_type instanceof CollectionType))
			return false;

		$domain_home = $homepage->add ($page_type, array (
					"cName" => $domain_name,
					"cHandle" => $domain_handle,
					"cDescription" => $domain_desc));
		if ($domain_home->getCollectionID() > 0) {
		}
		else {
			return false;
		}

		return $domain_home;
	}

	private static function getVolumePageType ($project_id, $domain_handle, $volume_handle)
	{
		if (preg_match ("/^sys-[a-z]{2}$/", $project_id)) {
			$page_type_handle = self::$mDomain2VolumePageTypeMap["sys/$domain_handle/$volume_handle"];
			if (!isset ($page_type_handle)) {
				$page_type_handle = self::$mDomain2VolumePageTypeMap["sys/$domain_handle"];
			}
		}

		if (!isset ($page_type_handle)) {
			$page_type_handle = self::$mDomain2VolumePageTypeMap ["$domain_handle/$volume_handle"];
			if (!isset ($page_type_handle)) {
				$page_type_handle = self::$mDomain2VolumePageTypeMap [$domain_handle];
			}
		}

		return CollectionType::getByHandle ($page_type_handle);
	}

	public static function addVolumePage ($project_id, $domain_page, $domain_handle,
				$volume_handle, $volume_name, $volume_desc)
	{
		$page_path = self::assemblePath ($project_id, $domain_handle, $volume_handle, 'na', 'na');
		$page = Page::getByPath ($page_path);
		if ($page->getCollectionID() > 0) {
			return $page;
		}

		$page_type = self::getVolumePageType ($project_id, $domain_handle, $volume_handle);
		if (!($page_type instanceof CollectionType)) {
			return false;
		}

		$volume_page = $domain_page->add ($page_type, array ("cName" => $volume_name,
				"cHandle" => $volume_handle, "cDescription" => $volume_desc));
		if ($volume_page->getCollectionID() > 0) {
		}
		else {
			return false;
		}

		return $volume_page;
	}

	private static function getPartPageType ($project_id, $domain_handle, $volume_handle)
	{
		if (preg_match ("/^sys-[a-z]{2}$/", $project_id)) {
			$page_type_handle = self::$mDomain2PartPageTypeMap["sys/$domain_handle/$volume_handle"];
			if (!isset ($page_type_handle)) {
				$page_type_handle = self::$mDomain2PartPageTypeMap["sys/$domain_handle"];
			}
		}

		if (!isset ($page_type_handle)) {
			$page_type_handle = self::$mDomain2PartPageTypeMap ["$domain_handle/$volume_handle"];
			if (!isset ($page_type_handle)) {
				$page_type_handle = self::$mDomain2PartPageTypeMap [$domain_handle];
			}
		}

		return CollectionType::getByHandle ($page_type_handle);
	}

	public static function addPartPage ($project_id, $domain_handle, $volume_page,
				$part_handle, $part_name, $part_desc)
	{
		$volume_handle = $volume_page->getCollectionHandle ();
		$page_path = self::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, 'na');
		$page = Page::getByPath ($page_path);
		if ($page->getCollectionID() > 0) {
			return $page;
		}

		$page_type = self::getPartPageType ($project_id, $domain_handle, $volume_handle);
		if (!($page_type instanceof CollectionType)) {
			return false;
		}

		$part_page = $volume_page->add ($page_type, array ("cName" => $part_name,
				"cHandle" => $part_handle, "cDescription" => $part_desc));
		if ($part_page->getCollectionID() > 0) {
		}
		else {
			return false;
		}

		return $part_page;
	}

	private static function getChapterPageType ($project_id, $domain_handle, $volume_handle)
	{
		if (preg_match ("/^sys-[a-z]{2}$/", $project_id)) {
			$page_type_handle = self::$mDomain2ChapterPageTypeMap["sys/$domain_handle/$volume_handle"];
			if (!isset ($page_type_handle)) {
				$page_type_handle = self::$mDomain2ChapterPageTypeMap["sys/$domain_handle"];
			}
		}

		if (!isset ($page_type_handle)) {
			$page_type_handle = self::$mDomain2ChapterPageTypeMap ["$domain_handle/$volume_handle"];
			if (!isset ($page_type_handle)) {
				$page_type_handle = self::$mDomain2ChapterPageTypeMap [$domain_handle];
			}
		}

		return CollectionType::getByHandle ($page_type_handle);
	}

	public static function addChapterPage ($project_id, $domain_handle, $volume_handle, $part_page,
				$chapter_handle, $chapter_name, $chapter_desc, $required)
	{
		$part_handle = $part_page->getCollectionHandle ();
		$page_path = self::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$page = Page::getByPath ($page_path);
		if ($page->getCollectionID() > 0) {
			return $page;
		}

		$doc_lang = substr ($project_id, -2);

		$txt = Loader::helper('text');
		$chapter_handle = $txt->urlify ($chapter_handle);

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		$db = Loader::db ();
		$q = "INSERT IGNORE fsen_project_doc_volume_part_chapters_$doc_lang (project_id, domain_handle, volume_handle,
		part_handle, chapter_handle, chapter_name, chapter_desc, required, display_order, fse_id,
		create_time, update_time)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
		$res = $db->Execute ($q, array ($project_id, $domain_handle, $volume_handle, $part_handle,
					$chapter_handle, $chapter_name, $chapter_desc, $required, time(), $curr_fse_id));
		if ($db->Affected_Rows () == 0) {
			return false;
		}

		$page_type = self::getChapterPageType ($project_id, $domain_handle, $volume_handle);
		if (!($page_type instanceof CollectionType)) {
			return false;
		}

		$chapter_page = $part_page->add ($page_type, array ("cName" => $chapter_name,
				"cHandle" => $chapter_handle, "cDescription" => $chapter_desc));
		if ($chapter_page->getCollectionID() > 0) {
		}
		else {
			return false;
		}

		self::onUpdateProjectChapterInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);

		return $chapter_page;
	}

	public static function addOwnerMemberSection ($project_id, $fse_info = false)
	{
		/* add owner member section on misc page */
		if ($fse_info == false) {
			$project_info = ProjectInfo::getBasicInfo ($project_id);
			if ($project_info == false) {
				exit (0);
			}
			$fse_info = FSEInfo::getNameInfo ($project_info['fse_id']);
			$fse_info = FSEInfo::getBasicProfile ($fse_info['user_name']);
		}
		else {
			$fse_info['avatar_url'] = get_url_from_file_id ($fse_info['avatar_file_id']);
		}

		$page_path = self::assemblePath ($project_id, 'misc');
		$about_page = Page::getByPath ($page_path);
		$type_handle = 'member:markdown_safe:' . $fse_info ['user_name'] . ':primary:none';
		$section_content = sprintf (self::OWNER_MARKDOWN_TEXT,
			FSEInfo::getPersonalHomeLink ($fse_info, true),
			FSEInfo::getPersonalHomeLink ($fse_info), $fse_info ['avatar_url'],
			ProjectInfo::$mRoleDescriptions [substr ($project_id, -2)]['owner'], h5($fse_info['self_desc']));
		$section_manager = new DocSectionManager ();
		$res = $section_manager->addNewSection ($fse_info['fse_id'], $about_page->getCollectionID(), 'Members',
			$project_id, 'misc', 'na', 'na', 'na', $type_handle, '', $section_content, '[]');
	}

	public static function getBasicInfo ($project_id)
	{
		$project_info = Cache::get ('ProjectInfo', $project_id);
		if ($project_info == false) {
			$db = Loader::db ();
			$project_info = $db->getRow ("SELECT * FROM fsen_projects WHERE project_id=?", array ($project_id));
			if (count ($project_info) == 0) {
				return false;
			}
			Cache::set ('ProjectInfo', $project_id, $project_info, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $project_info;
	}

	public static function getOwnerID ($project_id)
	{
		$project_info = self::getBasicInfo ($project_id);
		if ($project_info) {
			return $project_info['fse_id'];
		}

		return false;
	}
	public static function getDomainName ($project_id, $domain_handle)
	{
		$domain_info = Cache::get ('ProjectInfo', $project_id . $domain_handle);
		if ($domain_info == false) {
			$db = Loader::db ();
			$domain_info = $db->getRow ("SELECT domain_name, domain_desc, domain_long_desc FROM fsen_project_doc_domains
	WHERE project_id=? AND domain_handle=?", array ($project_id, $domain_handle));
			if (count ($domain_info) == 0) {
				return false;
			}
			Cache::set ('ProjectInfo', $project_id . $domain_handle, $domain_info, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return h5($domain_info ['domain_name']);
	}

	public static function getDomainDesc ($project_id, $domain_handle)
	{
		$domain_info = Cache::get ('ProjectInfo', $project_id . $domain_handle);
		if ($domain_info == false) {
			$db = Loader::db ();
			$domain_info = $db->getRow ("SELECT domain_name, domain_desc, domain_long_desc FROM fsen_project_doc_domains
	WHERE project_id=? AND domain_handle=?", array ($project_id, $domain_handle));
			if (count ($domain_info) == 0) {
				return false;
			}
			Cache::set ('ProjectInfo', $project_id . $domain_handle, $domain_info, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return h5($domain_info ['domain_desc']);
	}

	public static function getDomainLongDesc ($project_id, $domain_handle)
	{
		$domain_info = Cache::get ('ProjectInfo', $project_id . $domain_handle);
		if ($domain_info == false) {
			$db = Loader::db ();
			$domain_info = $db->getRow ("SELECT domain_name, domain_desc, domain_long_desc FROM fsen_project_doc_domains
	WHERE project_id=? AND domain_handle=?", array ($project_id, $domain_handle));
			if (count ($domain_info) == 0) {
				return false;
			}
			Cache::set ('ProjectInfo', $project_id . $domain_handle, $domain_info, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $domain_info ['domain_long_desc'];
	}

	public static function getVolumeInfo ($project_id, $domain_handle, $volume_handle)
	{
		$volume_info = Cache::get ('ProjectInfo', $project_id . $domain_handle . $volume_handle);
		if ($volume_info == false) {
			$db = Loader::db ();
			$volume_info = $db->getRow ("SELECT volume_name, volume_desc FROM fsen_project_doc_volumes
	WHERE project_id=? AND domain_handle=? AND volume_handle=?", array ($project_id, $domain_handle, $volume_handle));
			if (count ($volume_info) == 0) {
				return false;
			}
			Cache::set ('ProjectInfo', $project_id . $domain_handle . $volume_handle,
					$volume_info, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $volume_info;
	}

	public static function getVolumeName ($project_id, $domain_handle, $volume_handle)
	{
		$volume_info = self::getVolumeInfo ($project_id, $domain_handle, $volume_handle);
		if ($volume_info == false) {
			return false;
		}

		return h5($volume_info ['volume_name']);
	}

	public static function getVolumeDesc ($project_id, $domain_handle)
	{
		$volume_info = self::getVolumeInfo ($project_id, $domain_handle, $volume_handle);
		if ($volume_info == false) {
			return false;
		}

		return h5($volume_info ['volume_desc']);
	}

	public static function getPartInfo ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		$part_info = Cache::get ('ProjectInfo', $project_id . $domain_handle . $volume_handle . $part_handle);
		if ($part_info == false) {
			$db = Loader::db ();
			$part_info = $db->getRow ("SELECT part_name, part_desc, nr_chapters FROM fsen_project_doc_volume_parts
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=?",
				array ($project_id, $domain_handle, $volume_handle, $part_handle));
			if (count ($part_info) == 0) {
				return false;
			}
			Cache::set ('ProjectInfo', $project_id . $domain_handle . $volume_handle . $part_handle,
					$part_info, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $part_info;
	}

	public static function getPartName ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		$part_info = self::getPartInfo ($project_id, $domain_handle, $volume_handle, $part_handle);
		if ($part_info == false) {
			return false;
		}

		return h5($part_info ['part_name']);
	}

	public static function getPartDesc ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		$part_info = self::getPartInfo ($project_id, $domain_handle, $volume_handle, $part_handle);
		if ($part_info == false) {
			return false;
		}

		return h5($part_info ['part_desc']);
	}

	public static function getChapterInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle)
	{
		$chapter_info = Cache::get ('ProjectInfo',
							$project_id . $domain_handle . $volume_handle . $part_handle . $chapter_handle);
		if ($chapter_info == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$chapter_info = $db->getRow ("SELECT chapter_name, chapter_desc, required, create_time, update_time,
		nr_sections, heat_level
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND chapter_handle=?",
				array ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle));
			if (count ($chapter_info) == 0) {
				return false;
			}
			Cache::set ('ProjectInfo', $project_id . $domain_handle . $volume_handle . $part_handle . $chapter_handle,
					$chapter_info, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $chapter_info;
	}

	public static function getChapterName ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle)
	{
		$chapter_info = self::getChapterInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		if ($chapter_info == false)
			return false;

		return h5($chapter_info ['chapter_name']);
	}

	public static function getChapterDesc ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle)
	{
		$chapter_info = self::getChapterInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		if ($chapter_info == false)
			return false;

		return h5($chapter_info ['chapter_desc']);
	}

	public static function getAllVolumes ($project_id, $domain_handle)
	{
		$volumes = Cache::get ('ProjectAllVolumes', $project_id . $domain_handle);
		if ($volumes == false) {
			$db = Loader::db ();
			$volumes = $db->getAll ("SELECT volume_handle, volume_name, volume_desc FROM fsen_project_doc_volumes
	WHERE project_id=? AND domain_handle=? ORDER BY display_order", array ($project_id, $domain_handle));
			Cache::set ('ProjectAllVolumes', $project_id . $domain_handle, $volumes, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $volumes;
	}

	public static function getAllParts ($project_id, $domain_handle, $volume_handle)
	{
		$parts = Cache::get ('ProjectAllParts', $project_id . $domain_handle . $volume_handle);
		if ($parts == false) {
			$db = Loader::db ();
			$parts = $db->getAll ("SELECT part_handle, part_name, part_desc, nr_chapters
	FROM fsen_project_doc_volume_parts
	WHERE project_id=? AND domain_handle=? AND volume_handle=? ORDER BY display_order",
					array ($project_id, $domain_handle, $volume_handle));
			Cache::set ('ProjectAllParts', $project_id . $domain_handle . $volume_handle,
					$parts, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $parts;
	}

	public static function getAllChapters ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		$chapters = Cache::get ('ProjectAllChapters', $project_id . $domain_handle . $volume_handle . $part_handle);
		if ($chapters == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$chapters = $db->getAll ("SELECT chapter_handle, chapter_name, chapter_desc, nr_sections
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? ORDER BY display_order",
					array ($project_id, $domain_handle, $volume_handle, $part_handle));
			Cache::set ('ProjectAllChapters', $project_id . $domain_handle . $volume_handle . $part_handle,
					$chapters, self::PROJECT_CACHE_EXPIRED_TIME);
		}
		return $chapters;
	}

	public static function getLatestChapters ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		$chapters = Cache::get ('LatestChapters', $project_id . $domain_handle . $volume_handle . $part_handle);
		if ($chapters == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$chapters = $db->getAll ("SELECT chapter_handle, chapter_name, chapter_desc, nr_sections, fse_id
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=?
	ORDER BY required DESC, update_time DESC LIMIT ?",
					array ($project_id, $domain_handle, $volume_handle, $part_handle, self::NR_LATEST_CHAPTERS));
			Cache::set ('LatestChapters', $project_id . $domain_handle . $volume_handle . $part_handle,
					$chapters, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $chapters;
	}

	public static function getLatestChapterInfo ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		$chapter_info = Cache::get ('LatestChapterInfo', $project_id . $domain_handle . $volume_handle . $part_handle);
		if ($chapter_info == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$chapter_info = $db->getRow ("SELECT chapter_handle, chapter_name, chapter_desc, nr_sections, fse_id
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? ORDER BY required DESC, update_time DESC",
					array ($project_id, $domain_handle, $volume_handle, $part_handle));
			Cache::set ('LatestChapterInfo', $project_id . $domain_handle . $volume_handle . $part_handle,
					$chapter_info, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $chapter_info;
	}

	public static function getRequiredChapters ($project_id, $domain_handle, $volume_handle)
	{
		$chapters = Cache::get ('RequiredChapters', $project_id . $domain_handle . $volume_handle);
		if ($chapters == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$chapters = $db->getAll ("SELECT part_handle, chapter_handle, chapter_name, chapter_desc, nr_sections,
		UNIX_TIMESTAMP(update_time) AS create_ctime
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND required=1 ORDER BY display_order",
					array ($project_id, $domain_handle, $volume_handle));
			Cache::set ('RequiredChapters', $project_id . $domain_handle . $volume_handle,
					$chapters, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $chapters;
	}

	public static function getNormalThreads ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		$normal_threads = Cache::get ('ForumNormalThreads', $project_id . $domain_handle . $volume_handle . $part_handle);
		if ($normal_threads == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$normal_threads = $db->getAll ("SELECT chapter_handle, chapter_name, chapter_desc, nr_sections, fse_id,
		UNIX_TIMESTAMP(update_time) AS create_ctime
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND required=0
	ORDER BY update_time DESC",
					array ($project_id, $domain_handle, $volume_handle, $part_handle));
			Cache::set ('ForumNormalThreads', $project_id . $domain_handle . $volume_handle . $part_handle,
					$normal_threads, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $normal_threads;
	}

	public static function getTopThreads ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		$top_threads = Cache::get ('ForumTopThreads', $project_id . $domain_handle . $volume_handle . $part_handle);
		if ($top_threads == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$top_threads = $db->getAll ("SELECT chapter_handle, chapter_name, chapter_desc, nr_sections, fse_id,
		UNIX_TIMESTAMP(update_time) AS create_ctime
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND required=1 ORDER BY update_time DESC",
				array ($project_id, $domain_handle, $volume_handle, $part_handle));
			Cache::set ('ForumTopThreads', $project_id . $domain_handle . $volume_handle . $part_handle,
					$top_threads, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $top_threads;
	}

	public static function onChangeThreads ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		Cache::delete ('ForumTopThreads', $project_id . $domain_handle . $volume_handle . $part_handle);
		Cache::delete ('ForumNormalThreads', $project_id . $domain_handle . $volume_handle . $part_handle);
		Cache::delete ('LatestChapterInfo', $project_id . $domain_handle . $volume_handle . $part_handle);
		Cache::delete ('LatestChapters', $project_id . $domain_handle . $volume_handle . $part_handle);
	}

	public static function getLastSectionInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle)
	{
		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$last_section = $db->getRow ("SELECT author_id, UNIX_TIMESTAMP(update_time) AS create_ctime
	FROM fsen_document_sections_$doc_lang
	WHERE project_id=? AND volume_handle=? AND part_handle=? AND chapter_handle=? ORDER BY update_time DESC",
				array ($project_id, $volume_handle, $part_handle, $chapter_handle));
		return $last_section;
	}

	protected static function deleteCacheEntries ($project_id)
	{
		Cache::delete ('ProjectInfo', $project_id);
		foreach (ProjectInfo::$mDomainList as $domain_handle) {
			Cache::delete ('ProjectInfo', $project_id . $domain_handle);
			Cache::delete ('ProjectAllVolumes', $project_id . $domain_handle);
			$volumes = self::getAllVolumes ($project_id, $domain_handle);
			foreach ($volumes as $v) {
				Cache::delete ('ProjectInfo', $project_id . $domain_handle . $v['volume_handle']);
				Cache::delete ('ProjectAllParts', $project_id . $domain_handle . $v['volume_handle']);
				$parts = self::getAllParts ($project_id, $domain_handle, $v['volume_handle']);
				foreach ($parts as $p) {
					Cache::delete ('ProjectInfo', $project_id . $domain_handle . $v['volume_handle'] . $p['part_handle']);
					Cache::delete ('ForumTopThreads', $project_id.$domain_handle.$v['volume_handle'].$p['part_handle']);
					Cache::delete ('ProjectAllChapters', $project_id . $domain_handle.$v['volume_handle'].$p['part_handle']);
					$chapters = self::getAllChapters ($project_id, $domain_handle, $v['volume_handle'], $p['part_handle']);
					foreach ($chapters as $c) {
						Cache::delete ('ProjectInfo',
							$project_id . $domain_handle . $v['volume_handle'] . $p['part_handle'] . $c['chapter_handle']);
					}
				}
			}
		}
	}

	/* project_id and fse_id must be vaild */
	public static function getUserRoles ($project_id, $fse_id)
	{
		$roles = Cache::get ('ProjectUserRoles', $project_id . $fse_id);
		if ($roles == false) {
			$db = Loader::db ();
			$query = "SELECT member_roles, member_rights, display_name, description
	FROM fsen_project_members WHERE project_id=? AND fse_id=?";
			$roles = $db->getRow ($query, array ($project_id, $fse_id));
			if (count ($roles) == 0) {
				$roles = array ('member_roles' => '', 'member_rights' => '0123456789abcdef',
					'display_name' => '', 'description' => '');
			}
			Cache::set ('ProjectUserRoles', $project_id . $fse_id, $roles, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $roles;
	}

	/* project_id and fse_id must be vaild */
	public static function getUserRights ($project_id, $fse_id)
	{
		$roles = self::getUserRoles ($project_id, $fse_id);
		return $roles ['member_rights'];
	}

	public static function removeMember ($project_id, $fse_id)
	{
		$db = Loader::db ();
		$query = "DELETE FROM fsen_project_members WHERE project_id=? AND fse_id=?";
		$res = $db->Execute ($query, array ($project_id, $fse_id));
		Cache::delete ('ProjectUserRoles', $project_id . $fse_id);
	}

	public static function setUserAsOwner ($project_id, $fse_id)
	{
	 	$fse_name_info = FSEInfo::getNameInfo ($fse_id);
		if ($fse_name_info == false)
			return false;

		$rights = 'tttttttttttttttt';

		$db = Loader::db ();
		$query = "INSERT INTO fsen_project_members VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY
	UPDATE member_roles=?, member_rights=?";
		$res = $db->Execute ($query, array ($project_id, $fse_id,
					$fse_name_info['nick_name'], 'The owner of the project', 'owner', $rights, 'owner', $rights));

		return true;
	}

	public static function setUserRoles ($project_id, $fse_id, $display_name, $description, $roles)
	{
		$rights = '0123456789abcdef';
		$role_fragments = explode ("|", $roles);
		foreach ($role_fragments as $role) {
			switch ($role) {
			case 'g-adm':
				$rights [0] = 't';
				$rights [1] = 't';
				$rights [2] = 't';
				break;
			case 'p-edt':
				$rights [1] = 't';
				break;
			case 'c-adm':
				$rights [2] = 't';
				break;
			}
		}

		$query = "INSERT INTO fsen_project_members VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY
	UPDATE display_name=?, description=?, member_roles=?, member_rights=?";
		$db = Loader::db ();
		$res = $db->Execute ($query, array ($project_id, $fse_id, $display_name, $description, $roles, $rights,
					$display_name, $description, $roles, $rights));
		Cache::delete ('ProjectUserRoles', $project_id . $fse_id);

		return $rights;
	}

	public static function onUpdateProjectBasicInfo ($project_id)
	{
		Cache::delete ('ProjectInfo', $project_id);

		/* no need to update the '/project' page at once
		$cache = PageCache::getLibrary ();
		$page = Page::getByPath ('/project');
		$cache->purge ($page);
		*/
	}

	public static function onUpdateProjectDomainInfo ($project_id, $domain_handle)
	{
		Cache::delete ('ProjectInfo', $project_id . $domain_handle);
		Cache::delete ('ProjectAllVolumes', $project_id . $domain_handle);

		$page = self::getProjectPage ($project_id, $domain_handle);
		if ($page != false) {
			$cache = PageCache::getLibrary ();
			$cache->purge ($page);
		}
	}

	public static function onUpdateProjectVolumeInfo ($project_id, $domain_handle, $volume_handle)
	{
		Cache::delete ('ProjectInfo', $project_id . $domain_handle . $volume_handle);
		Cache::delete ('ProjectAllParts', $project_id . $domain_handle . $volume_handle);

		$cache = PageCache::getLibrary ();
		$page = Page::getByPath (self::assemblePath ($project_id, $domain_handle, $volume_handle));
		$cache->purge ($page);

		self::onUpdateProjectDomainInfo ($project_id, $domain_handle);
	}

	public static function onUpdateProjectPartInfo ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		Cache::delete ('ProjectInfo', $project_id . $domain_handle . $volume_handle . $part_handle);
		Cache::delete ('ProjectAllChapters', $project_id . $domain_handle . $volume_handle . $part_handle);
		Cache::delete ('LatestChapters', $project_id . $domain_handle . $volume_handle . $part_handle);

		$cache = PageCache::getLibrary ();
		$page = Page::getByPath (self::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle));
		if ($page->getCollectionID () > 0)
			$cache->purge ($page);

		self::onUpdateProjectVolumeInfo ($project_id, $domain_handle, $volume_handle);
		self::onUpdateProjectDomainInfo ($project_id, $domain_handle);
	}

	public static function onUpdateProjectChapterInfo ($project_id,
			$domain_handle, $volume_handle, $part_handle, $chapter_handle)
	{
		Cache::delete ('ProjectInfo', $project_id . $domain_handle . $volume_handle . $part_handle . $chapter_handle);
		Cache::delete ('LatestChapterInfo', $project_id . $domain_handle . $volume_handle . $part_handle);
		Cache::delete ('RequiredChapters', $project_id . $domain_handle . $volume_handle);

		self::onUpdateProjectPartInfo ($project_id, $domain_handle, $volume_handle, $part_handle);
		self::onUpdateProjectVolumeInfo ($project_id, $domain_handle, $volume_handle);
		self::onUpdateProjectDomainInfo ($project_id, $domain_handle);

		$cache = PageCache::getLibrary ();
		$chapters = self::getAllChapters ($project_id, $domain_handle, $volume_handle, $part_handle);
		foreach ($chapters as $c) {
			$page = Page::getByPath (self::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle,
					$c['chapter_handle']));
			if ($page->getCollectionID () > 0)
				$cache->purge ($page);
		}
	}

	public static function deleteProjectDocPart ($project_id, $domain_handle, $volume_handle, $part_handle) {
		$page = self::getProjectPage ($project_id, $domain_handle, $volume_handle, $part_handle);
		if ($page != false) {
			$page->delete ();

			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$db->Execute ("DELETE FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=?",
					$project_id, $domain_handle, $volume_handle, $part_handle);

			$db->Execute ("DELETE FROM fsen_project_doc_volume_parts
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=?",
					$project_id, $domain_handle, $volume_handle, $part_handle);

			self::onUpdateProjectPartInfo ($project_id, $domain_handle, $volume_handle, $part_handle);
		}
	}

	public static function getSuggestedBlogs ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		$blogs = Cache::get ('SuggestedBlogs', $project_id . $domain_handle . $volume_handle . $part_handle);
		if ($blogs == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$blogs = $db->getAll ("SELECT chapter_handle, chapter_name, chapter_desc, nr_sections,
		UNIX_TIMESTAMP(update_time) AS create_ctime
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? and required=1 ORDER BY display_order",
					array ($project_id, $domain_handle, $volume_handle, $part_handle));
			Cache::set ('SuggestedBlogs', $project_id . $domain_handle . $volume_handle . $part_handle,
					$blogs, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $blogs;
	}

	public static function getNormalBlogs ($project_id, $domain_handle, $volume_handle, $part_handle)
	{
		$blogs = Cache::get ('NormalBlogs', $project_id . $domain_handle . $volume_handle . $part_handle);
		if ($blogs == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$blogs = $db->getAll ("SELECT chapter_handle, chapter_name, chapter_desc, nr_sections,
		UNIX_TIMESTAMP(update_time) AS create_ctime
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? and required=0 ORDER BY display_order",
					array ($project_id, $domain_handle, $volume_handle, $part_handle));
			Cache::set ('NormalBlogs', $project_id . $domain_handle . $volume_handle . $part_handle,
					$blogs, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $blogs;
	}

	public static function getTopBlogs ($doc_lang) {
		$project_id = SYSTEM_PROJECT_SHORTNAME . "-$doc_lang";
		$top_blogs = Cache::get ('TopBlogs', $project_id);
		if ($top_blogs == false) {
			$db = Loader::db ();
			$top_blogs = $db->getAll ("SELECT part_handle, chapter_handle, chapter_name, chapter_desc, create_time, update_time, nr_sections
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle='document' AND volume_handle='blog'
	ORDER BY heat_level DESC, update_time DESC LIMIT ?", array ($project_id, self::NR_TOP_BLOGS));
			Cache::set ('TopBlogs', $project_id, $top_blogs, 3600 * 24);
		}

		return $top_blogs;
	}

	public static function getAllSuggestedBlogs ($doc_lang) {
		$project_id = SYSTEM_PROJECT_SHORTNAME . "-$doc_lang";
		$blogs = Cache::get ('AuthorSuggestedBlogs', $project_id);
		if ($blogs == false) {
			$db = Loader::db ();
			$blogs = $db->getAll ("SELECT part_handle, chapter_handle, chapter_name, chapter_desc, create_time, update_time, nr_sections
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle='document' AND volume_handle='blog' AND required=1
	ORDER BY heat_level DESC, update_time DESC LIMIT ?", array ($project_id, self::NR_SUGGESTED_BLOGS));
			Cache::set ('AuthorSuggestedBlogs', $project_id, $blogs, 3600 * 24);
		}

		return $blogs;
	}

	public static function getTopBlogAuthors ($doc_lang) {
		$project_id = SYSTEM_PROJECT_SHORTNAME . "-$doc_lang";
		$blogs = Cache::get ('AllBlogAuthors', $project_id);
		if ($blogs == false) {
			$db = Loader::db ();
			$blogs = $db->getAll ("SELECT part_handle, SUM(heat_level)
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle='document' AND volume_handle='blog'
	GROUP BY part_handle
	ORDER BY SUM(heat_level) DESC LIMIT ?", array ($project_id, self::NR_ALL_BLOG_AUTHORS));
			Cache::set ('AllBlogAuthors', $project_id, $blogs, 3600 * 24);
		}

		return $blogs;
	}

	public static function getBlogInfo ($chapter_handle) {
		$bi = Cache::get ('BlogInfo', $chapter_handle);
		if ($bi == false) {
			$db = Loader::db ();
			$bi['info'] = $db->getRow ("SELECT chapter_name, chapter_desc, required, create_time, update_time, nr_sections
	FROM fsen_project_doc_volume_part_chapters_all
	WHERE chapter_handle=?", array ($chapter_handle));
			if (count ($bi['info']) == 0) {
				return false;
			}

			$bi['tags'] = $db->getAll ("SELECT tag FROM fsen_chapter_tags_all WHERE chapter_handle=?",
					array ($chapter_handle));
			$bi['category'] = $db->getOne ("SELECT category FROM fsen_chapter_categories WHERE chapter_handle=?",
					array ($chapter_handle));
			Cache::set ('BlogInfo', $chapter_handle, $bi, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $bi;
	}

	public static function onDeleteBlog ($chapter_handle) {
		/* we only flush the cache of the blog */
		Cache::delete ('BlogInfo', $chapter_handle);
	}

	public static function onUpdateBlogInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle) {
		/* we only flush the cache of the blog and the blog page */
		Cache::delete ('BlogInfo', $chapter_handle);

		$cache = PageCache::getLibrary ();
		$page = Page::getByPath (self::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle));
		$cache->purge ($page);
	}

	public static function onDeleteProject ($project_id) {
		self::deleteCacheEntries ($project_id);

		$tables = array (
				'fsen_project_members',
				'fsen_document_sections_en',
				'fsen_document_sections_zh',
				'fsen_project_doc_volume_part_chapters_en',
				'fsen_project_doc_volume_part_chapters_zh',
				'fsen_project_doc_volumes',
				'fsen_project_doc_volume_parts',
				'fsen_project_doc_domains',
			);

		$db = Loader::db ();
		foreach ($tables as $table) {
			$db->Execute ("DELETE FROM $table WHERE project_id='$project_id'");
		}
	}

	const EDIT_PAGE_USER_ERROR		= 1;
	const EDIT_PAGE_USER_BANNED		= 2;
	const EDIT_PAGE_USER_NO_RIGHT	= 3;

	public static function getUserEditRight ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle, $fse_id) {

		$user_rights = self::getUserRights ($project_id, $fse_id);
		$project_shortname = substr ($project_id, 0, strlen ($project_id) - 3);

		if ($project_shortname == SYSTEM_PROJECT_SHORTNAME && $domain_handle == 'document'
			&& $volume_handle == 'blog' && $part_handle != 'na' && $chapter_handle != 'na') {

			$author_info = FSEInfo::getBasicProfile ($part_handle);
			if ($author_info == false) {
				return self::EDIT_PAGE_USER_ERROR;
			}

			if ($fse_id == $author_info ['fse_id']) {
				if ($user_info ['status'] > 0) {
					return self::EDIT_PAGE_USER_BANNED;
				}
			}
			else if ($user_rights [1] != 't') {
				return self::EDIT_PAGE_USER_NO_RIGHT;
			}
		}
		else if ($user_rights [1] != 't') {
			return self::EDIT_PAGE_USER_NO_RIGHT;
		}

		return 0;
	}

	public static function getRecentNews ($project_id)
	{
		$rows = Cache::get ('RecentNews', $project_id);
		if ($rows == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$rows = $db->getAll ("SELECT chapter_handle, chapter_name, chapter_desc, nr_sections,
		UNIX_TIMESTAMP(create_time) AS create_ctime
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle='community' AND volume_handle='general' AND part_handle='news'
	ORDER BY create_time DESC LIMIT 5", array ($project_id));
			Cache::set ('RecentNews', $project_id, $rows, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $rows;
	}

	public static function getHotDiscussions ($project_id) {
		$rows = Cache::get ('HotDisussions', $project_id);
		if ($rows == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$rows = $db->getAll ("SELECT chapter_handle, chapter_name, chapter_desc, nr_sections,
		UNIX_TIMESTAMP(create_time) AS create_ctime
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle='community' AND volume_handle='general' AND part_handle='discussion'
	ORDER BY heat_level DESC LIMIT 5", array ($project_id));
			Cache::set ('HotDisussions', $project_id, $rows, self::PROJECT_CACHE_EXPIRED_TIME);
		}

		return $rows;
	}

	public static function getMustDocuments ($project_id) {
		$rows = Cache::get ('MustDocuments', $project_id);
		if ($rows == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$rows = $db->getAll ("SELECT volume_handle, part_handle, chapter_handle, chapter_name, chapter_desc, nr_sections,
		UNIX_TIMESTAMP(create_time) AS create_ctime
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle='document' AND required=1
	ORDER BY update_time DESC LIMIT 5", array ($project_id));
			Cache::set ('MustDocuments', $project_id, $rows, self::PROJECT_CACHE_EXPIRED_TIME * 6);
		}

		return $rows;
	}

	public static function getLatestDocuments ($project_id) {
		$rows = Cache::get ('LatestDocuments', $project_id);
		if ($rows == false) {
			$db = Loader::db ();
			$doc_lang = substr ($project_id, -2);
			$rows = $db->getAll ("SELECT volume_handle, part_handle, chapter_handle, chapter_name, chapter_desc, nr_sections,
		UNIX_TIMESTAMP(create_time) AS create_ctime
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle='document' ORDER BY create_time DESC LIMIT 5", array ($project_id));
			Cache::set ('LatestDocuments', $project_id, $rows, self::PROJECT_CACHE_EXPIRED_TIME * 6);
		}

		return $rows;
	}

	static function onUpdatePersonalProfile ($fse_info, $doc_lang) {
		$db = Loader::db ();

		$page = Page::getByPath ("/$doc_lang/engineer/" . $fse_info['user_name']);
		if ($page->getCollectionID() > 0) {
			$page->update (array ('cName' => $fse_info['nick_name'],
					"cDescription" => $fse_info['self_desc']));
		}
		else {
			error_log ("onUpdatePersonalProfile: no personal homepage found for user: " . $fse_info['fse_id'] . PHP_EOL,
				3, '/var/tmp/fsen.log');
		}

		$page = Page::getByPath ("/$doc_lang/blog/" . $fse_info['user_name']);
		if ($page->getCollectionID() > 0) {
			$page->update (array ('cName' => $fse_info['nick_name'],
					"cDescription" => t('Blogs of %s', $fse_info['nick_name'])));
		}
		else {
			error_log ("onUpdatePersonalProfile: no blog page found for user: " . $fse_info['fse_id'] . PHP_EOL,
				3, '/var/tmp/fsen.log');
		}

		$res = $db->Execute ("UPDATE fsen_project_doc_volume_parts SET part_name=?, part_desc=?
	WHERE project_id='sys-$doc_lang' AND domain_handle='document' AND volume_handle='blog' AND part_handle=?",
				array ($fse_info['nick_name'], t('Blogs of %s', $fse_info['nick_name']), $fse_info['user_name']));

		self::onUpdateProjectPartInfo ("sys-$doc_lang", 'document', 'blog', $fse_info['user_name']);
	}
}

