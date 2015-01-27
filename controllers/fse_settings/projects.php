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

require_once ('helpers/check_login.php');
require_once ('helpers/misc.php');
require_once ('helpers/fsen/DocSectionManager.php');
require_once ('helpers/fsen/ProjectInfo.php');
require_once ('helpers/fsen/FSEInfo.php');

class ReturnInfo {
	public $status;
}

class FseSettingsProjectsController extends Controller {
	private $mItemList = array ("name", "short_desc", "icon_file_id", 'page_theme',
				"repo_location", "repo_name", "code_lang", "platform", "license");

	private $mDomainList = array ('home', 'download', 'document', 'community', 'contribute', 'misc');

	const NR_SYSTEM_DOMAIN_ROWS = 6;
	private $mSystemDomainRows = array (
			'en' => array (
				'', 'home', 'Home', 'Homepage of the project', 'The pages are under construction, please visit later. (Please delete this when ready.)', 0,
				'', 'download', 'Download', 'Download source code', 'Nothing is currently available for download.', 1,
				'', 'document',  'Documentation', 'Read documents first when you have any problem', 'The developer has not yet released any documents.', 2,
				'', 'community', 'Community', 'Discuss things with others', 'The developer has not yet created any community venue to gather and exchange ideas.', 3,
				'', 'contribute', 'Contribute', 'Get involved', 'Nothing is currently available.', 4,
				'', 'misc', 'About', 'Copyright, Authors, and Credits', 'A person\'s interest and passion of several people, may become the power to change the world even a little.', 5),
			'zh' => array (
				'', 'home', '主页', '项目主页', '本项目页面正在构建中，请稍后访问！（准备好后请删除此内容。）', 0,
				'', 'download', '下载', '下载源代码', '抱歉，当前无内容可供下载。', 1,
				'', 'document',  '文档', '遇到问题？文档能帮您！', '抱歉，开发者尚未发布任何文档。', 2,
				'', 'community', '社区', '和其他人一起讨论、交流', '抱歉，开发者尚未创建任何社区场所供收集和交换想法。', 3,
				'', 'contribute', '参与', '参与其中，贡献力量！', '抱歉，当前无内容。', 4,
				'', 'misc', '关于', '著作权、作者及荣誉榜', '一个人的兴趣，几个人的激情，可能成为改变世界的力量，哪怕只有一点点。', 5),
			);

	const NR_DOC_VOLUME_ROWS = 8;
	private $mRequiredDocVolumeRows = array (
			'en' => array (
				'', 'home', 'feature', 'Features', 'Feature list on homepage', 1, 0,
				'', 'home', 'overview', 'Overview', 'Overview on homepage', 1, 1,
				'', 'document', 'user-guide', 'User Guide', 'Introduction, installation, and so on', 1, 1,
				'', 'document', 'dev-guide', 'Developer Guide', 'Architecture, development plan, and others', 1, 2,
				'', 'community', 'general', 'General', 'General topics, such as news, events...', 1, 0,
				'', 'misc', 'vision', 'Vision', 'Motivation, Goal, and Vision', 1, 0,
				'', 'misc', 'faqs', 'FAQs', 'Frequently Asked Questions', 1, 1,
				'', 'misc', 'legal', 'Legal', 'License and Legal', 1, 2,
				),
			'zh' => array (
				'', 'home', 'feature', '特色', '主页上的功能特色清单', 1, 0,
				'', 'home', 'overview', '概述', '主页上的概述', 1, 1,
				'', 'document', 'user-guide', '用户指南', '介绍、安装、配置等面向使用者的指南文档', 1, 1,
				'', 'document', 'dev-guide', '开发指南', '架构、开发计划、规范等面向开发者的文档', 1, 2,
				'', 'community', 'general', '一般', '一般性主题，如新闻、事件等', 1, 0,
				'', 'misc', 'vision', '愿景', '动机、目标及愿景', 1, 0,
				'', 'misc', 'faqs', '常见问题', '常见问题及解答', 1, 1,
				'', 'misc', 'legal', '法律声明', '许可证说明及相关法律声明', 1, 2,
				),
		);

	const NR_DOC_PART_ROWS = 6;
	private $mRequiredDocPartRows = array (
			'en' => array (
				'', 'community', 'general', 'news', 'News', 'New releases, announcements, and so on', 1, 0,
				'', 'community', 'general', 'discussion', 'Discussion', 'Requirements, questions, solutions...', 1, 1,
				'', 'document', 'user-guide','basic-info', 'Basic Info', 'Basic information', 1, 0,
				'', 'document', 'user-guide','introduction', 'Introduction', 'Introduction for newbies', 1, 1,
				'', 'document', 'user-guide', 'installation', 'Installation', 'Instructions to install and setup', 1, 2,
				'', 'document', 'dev-guide', 'architecture', 'Architecture', 'Introduction to the architecture', 1, 0),
			'zh' => array (
				'', 'community', 'general', 'news', '新闻', '新版本发布、通告等', 1, 0,
				'', 'community', 'general', 'discussion', '讨论', '需求讨论，问题及解答', 1, 1,
				'', 'document', 'user-guide','basic-info', '基本信息', '项目背景等基本信息', 1, 0,
				'', 'document', 'user-guide','introduction', '介绍', '面向新人的介绍', 1, 1,
				'', 'document', 'user-guide', 'installation', '安装', '安装和设置介绍', 1, 2,
				'', 'document', 'dev-guide', 'architecture', '架构', '软件架构介绍', 1, 0),
		);

	public function view () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}
	}

	protected function create_required_pages_for_domain ($project_id, $homepage, $domain_handle) {
		/* create domain homepage */
		$doc_lang = substr ($project_id, -2);
		$system_domain_info = $this->mSystemDomainRows[$doc_lang];
		for ($i = 0; $i < self::NR_SYSTEM_DOMAIN_ROWS; $i++) {
			if ($system_domain_info [$i*6 + 1] == $domain_handle) {
				$domain_name = $system_domain_info [$i*6 + 2];
				$domain_desc = $system_domain_info [$i*6 + 3];
				break;
			}
		}
		$domain_home = ProjectInfo::addDomainPage ($project_id, $homepage, $domain_handle, $domain_name, $domain_desc);
		if ($domain_home == false) {
			$this->set ('error', t('Failed to create domain page for %s!', $domain_handle));
			return false;
		}

		/* add children pages for volumes and parts */
		for ($i = 0; $i < self::NR_DOC_VOLUME_ROWS; $i++) {
			if ($this->mQueryParamsVolumes [$i*7 + 1] == $domain_handle) {
				$volume_page = ProjectInfo::addVolumePage ($project_id, $domain_home,
					$this->mQueryParamsVolumes [$i*7 + 1],
					$this->mQueryParamsVolumes [$i*7 + 2],
					$this->mQueryParamsVolumes [$i*7 + 3],
					$this->mQueryParamsVolumes [$i*7 + 4]);
				if (!$volume_page) {
					$this->set ('error', t('Failed to create volume page for %s!', $domain_handle));
					return false;
				}

				$volume_handle = $this->mQueryParamsVolumes [$i*7 + 2];
				for ($j = 0; $j < self::NR_DOC_PART_ROWS; $j++) {
					if ($this->mQueryParamsParts [$j*8 + 1] == $domain_handle
							&& $this->mQueryParamsParts [$j*8 + 2] == $volume_handle) {
						$part_page = ProjectInfo::addPartPage ($project_id, $domain_handle,
							$volume_page,
							$this->mQueryParamsParts [$j*8 + 3],
							$this->mQueryParamsParts [$j*8 + 4],
							$this->mQueryParamsParts [$j*8 + 5]);
						if (!$part_page) {
							$this->set ('error',
								t('Failed to create part page for %s!', $domain_handle));
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	protected function add_requried_doc_part_pages ($project_id, $homepage) {
		$doc_lang = substr ($project_id, -2);

		/* prepare database entries first */
		$db = Loader::db ();

		$q = 'INSERT IGNORE fsen_project_doc_volumes (project_id, domain_handle, volume_handle, volume_name, volume_desc, required, display_order) VALUES ';
		$this->mQueryParamsVolumes = $this->mRequiredDocVolumeRows[$doc_lang];
		for ($i = 0; $i < self::NR_DOC_VOLUME_ROWS; $i++) {
			$this->mQueryParamsVolumes [$i*7] = $project_id;
			$q .= '(?, ?, ?, ?, ?, ?, ?), ';
		}
		$q = rtrim ($q, ', ');
		$res = $db->Execute ($q, $this->mQueryParamsVolumes);

		$q = 'INSERT IGNORE fsen_project_doc_volume_parts (project_id, domain_handle, volume_handle, part_handle, part_name, part_desc, required, display_order) VALUES ';
		$this->mQueryParamsParts = $this->mRequiredDocPartRows[$doc_lang];
		for ($i = 0; $i < self::NR_DOC_PART_ROWS; $i++) {
			$this->mQueryParamsParts [$i*8] = $project_id;
			$q .= '(?, ?, ?, ?, ?, ?, ?, ?), ';
		}
		$q = rtrim ($q, ', ');
		$res = $db->Execute ($q, $this->mQueryParamsParts);

		/* create global navigation area
		$th = Loader::helper('text');
		$global_area_handle = $th->unhandle ('custom_banner_for_' . str_replace ('-', '_', $project_id));
		$stack = Stack::getOrCreateGlobalArea ($global_area_handle);
		if (count ($stack->getBlocks (STACKS_AREA_NAME)) == 0) {
			$block_type = BlockType::getByHandle ('project_custom_banner');
			$area = Area::getOrCreate ($stack, STACKS_AREA_NAME);
			$stack->addBlock ($block_type, $area, array ("projectID" => $project_id));
		}
		*/

		/* create pages for domains */
		foreach ($this->mDomainList as $domain_handle) {
			if ($domain_handle == 'home')
				continue;
			if (!$this->create_required_pages_for_domain ($project_id, $homepage, $domain_handle)) {
				$this->set ('error', t('Failed to create pages for %s!', $domain_handle));
				return false;
			}
		}

		return true;
	}

	protected function add_project_pages ($project_id, $name, $short_desc) {
		$homepage = Page::getByPath (ProjectInfo::assemblePath ($project_id, 'home'));
		if ($homepage->getCollectionID() > 0) {
			$this->set ('error', t ('Existed project: %s!', $project_id));
			return false;
		}

		$doc_lang = substr ($project_id, -2);
		$page_type = CollectionType::getByHandle ('project_homepage');
		$parent_page = Page::getByPath ("/$doc_lang/project");
		if ($parent_page->getCollectionID() == false) {
			$this->set ('error', t('System error (no parent to create project pages)!'));
			return false;
		}
		$homepage = $parent_page->add ($page_type, array ("cName" => $name,
				"cHandle" => $project_id, "cDescription" => $short_desc));
		if ($homepage instanceof Page) {
			$block_type = BlockType::getByHandle ("project_banner");
			$area = new Area('Banner');
			$homepage->addBlock ($block_type, $area, array ("projectID" => $project_id));
		}
		else {
			$this->set ('error', t('Failed to create project homepage for %s!', $project_id));
			return false;
		}

		return $this->add_requried_doc_part_pages ($project_id, $homepage);
	}

	protected function rebuild_project_subpages ($project_id) {
		$doc_lang = substr ($project_id, -2);
		$db = Loader::db ();

		$q = 'INSERT IGNORE fsen_project_doc_domains (project_id, domain_handle, domain_name, domain_desc, domain_long_desc, display_order) VALUES ';
		$this->mQueryParamsDomains = $this->mSystemDomainRows[$doc_lang];
		for ($i = 0; $i < self::NR_SYSTEM_DOMAIN_ROWS; $i++) {
			$this->mQueryParamsDomains [$i*6] = $project_id;
			$q .= '(?, ?, ?, ?, ?, ?), ';
		}
		$q = rtrim ($q, ', ');
		$res = $db->Execute ($q, $this->mQueryParamsDomains);

		$q = 'INSERT IGNORE fsen_project_doc_volumes (project_id, domain_handle, volume_handle, volume_name, volume_desc, required, display_order) VALUES ';
		$this->mQueryParamsVolumes = $this->mRequiredDocVolumeRows[$doc_lang];
		for ($i = 0; $i < self::NR_DOC_VOLUME_ROWS; $i++) {
			$this->mQueryParamsVolumes [$i*7] = $project_id;
			$q .= '(?, ?, ?, ?, ?, ?, ?), ';
		}
		$q = rtrim ($q, ', ');
		$res = $db->Execute ($q, $this->mQueryParamsVolumes);

		$q = 'INSERT IGNORE fsen_project_doc_volume_parts (project_id, domain_handle, volume_handle, part_handle, part_name, part_desc, required, display_order) VALUES ';
		$this->mQueryParamsParts = $this->mRequiredDocPartRows[$doc_lang];
		for ($i = 0; $i < self::NR_DOC_PART_ROWS; $i++) {
			$this->mQueryParamsParts [$i*8] = $project_id;
			$q .= '(?, ?, ?, ?, ?, ?, ?, ?), ';
		}
		$q = rtrim ($q, ', ');
		$res = $db->Execute ($q, $this->mQueryParamsParts);

		$homepage = Page::getByPath (ProjectInfo::assemblePath ($project_id, 'home'));
		if ($homepage->getCollectionID() > 0) {
			if (!$this->add_requried_doc_part_pages ($project_id, $homepage)) {
				$this->set ('error', t('Failed to create project subpages!'));
				return;
			}
		}

		$this->set ('success', t('Succeed to rebuild project subpages.'));
	}

	public function new_project () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$name = $this->post('name');
		$short_name = $this->post('shortName');
		$short_desc = $this->post('shortDesc');
		$icon_file_id = (int)$this->post('iconFileID');
		$page_theme = $this->post('pageTheme');
		$repo_location = $this->post('repoLocation');
		$repo_name = $this->post('repoName');
		$code_lang = $this->post('codeLang');
		$doc_lang = $this->post('docLang');
		$platform = $this->post('platform');
		$license = $this->post('license');

		if (mb_strlen ($name) > 32 || mb_strlen ($name) < 4) {
			$this->set ('error', t('Too short/long project name!'));
			return;
		}

		if (!preg_match ("/^[a-z0-9\-]{4,60}$/", $short_name)) {
			$this->set ('error', t('Bad project short name!'));
			return;
		}

		$txt = Loader::helper('text');
		$short_name = $txt->urlify ($short_name);
		$project_id = "$short_name-$doc_lang";

		if (mb_strlen ($short_desc) > 255 || mb_strlen ($short_desc) < 5) {
			$this->set ('error', t('Too short/long project description!'));
			return;
		}

		if (!$_SESSION['FSEInfo']['email_verified']) {
			$this->set ('error', t('Your primary email box has not been verified!'));
			return;
		}

		$fse_id = $_SESSION['FSEInfo']['fse_id'];

		$db = Loader::db ();
		$res = $db->getOne ('SELECT COUNT(*) FROM fsen_projects WHERE fse_id=?', array ($fse_id));
		if ($res >= 5) {
			$this->set ('error', t('You have created too manay projects! Only 5 projects allowed for one account.'));
			return;
		}

		$res = $db->Execute ('INSERT IGNORE fsen_projects
	(project_id, fse_id, name, short_desc, icon_file_id, page_theme, repo_location, repo_name,
		code_lang, doc_lang, platform, license, create_time)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())',
			array ($project_id, $fse_id, $name, $short_desc, $icon_file_id, $page_theme, $repo_location, $repo_name,
				$code_lang, $doc_lang, $platform, $license));
		if ($db->Affected_Rows () == 0) {
			$this->set ('error', t('Failed to create project entry: duplicated short name.'));
			return;
		}

		$q = 'INSERT IGNORE fsen_project_doc_domains VALUES ';
		$this->mQueryParamsDomains = $this->mSystemDomainRows[$doc_lang];
		for ($i = 0; $i < self::NR_SYSTEM_DOMAIN_ROWS; $i++) {
			$this->mQueryParamsDomains [$i*6] = $project_id;
			$q .= '(?, ?, ?, ?, ?, ?), ';
		}
		$q = rtrim ($q, ', ');
		$res = $db->Execute ($q, $this->mQueryParamsDomains);
		if ($db->Affected_Rows () == 0) {
			$this->set ('error', t('Failed to create project domain entries.'));
			return;
		}

		if (!$this->add_project_pages ($project_id, $name, $short_desc)) {
			return;
		}

		ProjectInfo::setUserAsOwner ($project_id, $fse_id);

		ProjectInfo::addOwnerMemberSection ($project_id, $_SESSION['FSEInfo']);

		$this->set ('success', t('CONGRATULATIONS, your project has been created successfully!'));
	}

	private function add_owner_member_section ($project_id) {
		ProjectInfo::setUserAsOwner ($project_id, $_SESSION['FSEInfo']['fse_id']);
		ProjectInfo::addOwnerMemberSection ($project_id);
	}

	public function delete_project () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');

		if (!preg_match ("/^[a-z0-9_\-]{4,64}$/", $project_id)) {
			$this->set ('error', t('Invalid project identifier!'));
			return;
		}

		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info['fse_id'] != $_SESSION['FSEInfo']['fse_id']) {
			$this->set ('error', t('You are not the owner of the project!'));
			return;
		}

		$db = Loader::db ();
		$res = $db->Execute ('DELETE FROM fsen_projects WHERE project_id=?', array ($project_id));
		if ($db->Affected_Rows () == 0) {
			$this->set ('error', t('No such project!'));
			return;
		}

		/* delete project pages */
		$page = Page::getByPath (ProjectInfo::assemblePath ($project_id, 'home'));
		if ($page->getCollectionID () > 0) {
			$page->delete ();
		}

		ProjectInfo::onDeleteProject ($project_id);

		$this->set ('success', t('Project deleted'));
	}

	public function change_item () {
		$project_id = $this->post('projectID');
		$item_name = $this->post('itemName');
		$item_value = $this->post('itemValue');

		$json = Loader::helper ('json');
		$pas = new PageActionStatus;
		$pas->action = t('Change Project Item');
		$pas->status = t('Unkown error');
		$pas->time = time ();

		if (!fse_try_to_login ()) {
			$pas->message = t('You do not sign in or session expired.');
			echo $json->encode ($pas);
			exit (0);
		}

		if (!preg_match ("/^[a-z0-9_\-]{4,64}$/", $project_id)) {
			$pas->message = t('Invalid project identifier!');
			echo $json->encode ($pas);
			exit (0);
		}

		$page = ProjectInfo::getProjectPage ($project_id, 'home');
		if ($page == false) {
			$pas->message = t('No such project!');
			echo $json->encode ($pas);
			exit (0);
		}

		if (!in_array ($item_name, $this->mItemList)) {
			$pas->message = t('Invalid item name!');
			echo $json->encode ($pas);
			exit (0);
		}

		if (strlen ($item_value) < 2) {
			$pas->message = t('Too short item value!');
			echo $json->encode ($pas);
			exit (0);
		}

		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info['fse_id'] != $_SESSION['FSEInfo']['fse_id']) {
			$pas->message = t('You are not the owner of the project!');
			return;
		}

		$db = Loader::db ();
		$res = $db->Execute ("UPDATE fsen_projects SET $item_name=? WHERE project_id=?",
			array ($item_value, $project_id));
		if ($db->Affected_Rows () == 0) {
			$pas->message = t('Nothing changed!');
			echo $json->encode ($pas);
			exit (0);
		}

		/* update page attributes */
		if ($item_name == "name") {
			$page->update (array ("cName" => $item_value));
		}
		else if ($item_name == "short_desc") {
			$page->update (array ("cDescription" => $item_value));
		}

		ProjectInfo::onUpdateProjectBasicInfo ($project_id);

		/* refresh related blocks */
		$blocks = $page->getBlocks ('Banner');
		foreach ($blocks as $block) {
			$block->refreshBlockOutputCache ();
		}

		$cache = PageCache::getLibrary();
		$cache->purge($page);

		$pas->status = 'success';
		$pas->message = t('Item changed!');
		echo $json->encode ($pas);
		exit (0);
	}

	public function add_new_doc_volume () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$new_volume_name = $this->post('newVolumeName');
		$new_volume_handle = $this->post('newVolumeHandle');
		$new_volume_desc = $this->post('newVolumeDesc');

		if (!in_array ($domain_handle, $this->mDomainList)) {
			$this->set ('error', t('Bad domain handle!'));
			return;
		}

		if ($domain_handle == 'home') {
			$this->set ('error', t('You can not add volume page to home domain!'));
			return;
		}

		if (!preg_match ("/^[a-z0-9_\-]{4,64}$/", $project_id)) {
			$this->set ('error', t('Invalid project identifier!'));
			return;
		}

		$project_home_page = ProjectInfo::getProjectPage ($project_id, 'home');
		if ($project_home_page == false) {
			$this->set ('error', t('No such project!'));
			return;
		}

		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info['fse_id'] != $_SESSION['FSEInfo']['fse_id']) {
			$this->set ('error', t('You are not the owner of the project!'));
			return;
		}

		$db = Loader::db ();
		if (!preg_match ("/^[a-z0-9\-]{3,16}$/", $new_volume_handle)) {
			$this->set ('error', t('Bad volume handle!'));
			return;
		}

		if (!preg_match ("/^.{1,64}$/", $new_volume_name)) {
			$this->set ('error', t('Bad volume name!'));
			return;
		}

		$txt = Loader::helper('text');
		$new_volume_handle = $txt->urlify ($new_volume_handle);
		$res = $db->Execute ('INSERT IGNORE fsen_project_doc_volumes (project_id, domain_handle,
		volume_handle, volume_name, volume_desc, required, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)',
				array ($project_id, $domain_handle, $new_volume_handle, $new_volume_name, $new_volume_desc, 0, time()));
		if ($db->Affected_Rows () == 0) {
			$this->set ('error', t('Failed to add new volume!'));
			return;
		}

		$domain_path = ProjectInfo::assemblePath ($project_id, $domain_handle);
		if ($domain_path == '/') {
			$domain_home = Page::getByID (HOME_CID);
		}
		else {
			$domain_home = Page::getByPath ($domain_path);
		}
		if ($domain_home->getCollectionID( )== false) {
			$this->set ('error', t('No domain homepage for the project!'));
			return;
		}

		$volume_page = ProjectInfo::addVolumePage ($project_id, $domain_home, $domain_handle,
				$new_volume_handle, $new_volume_name, $new_volume_desc);
		if ($volume_page == false) {
			$this->set ('error', t('Failed to find/create volume page!'));
			return;
		}

		ProjectInfo::onUpdateProjectDomainInfo ($project_id, $domain_handle);

		$this->set ('success', t('Success to add new volume!'));
	}

	public function change_doc_domain_name_desc () {
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('itemDomain');
		$item_type = $this->post('itemType');
		$item_value = $this->post('itemValue');

		$json = Loader::helper ('json');
		$pas = new PageActionStatus;
		$pas->action = t('Change Domain Name/Desc');
		$pas->status = t('Unkown error');
		$pas->time = time ();

		if (!fse_try_to_login ()) {
			$pas->message = t('You do not sign in or session expired.');
			echo $json->encode ($pas);
			exit (0);
		}

		if (!in_array ($domain_handle, $this->mDomainList)) {
			$pas->message = t('Bad domain handle!');
			echo $json->encode ($pas);
			exit (0);
		}

		if (!preg_match ("/^[a-z0-9_\-]{4,64}$/", $project_id)) {
			$pas->message = t('Invalid project identifier!');
			echo $json->encode ($pas);
			exit (0);
		}

		$project_home_page = ProjectInfo::getProjectPage ($project_id, 'home');
		if ($project_home_page == false) {
			$pas->message = t('No such project!');
			echo $json->encode ($pas);
			exit (0);
		}

		if ($item_type == 'name') {
			if (!preg_match ("/^.{1,64}$/", $item_value)) {
				$pas->message = t('Bad domain name!');
				echo $json->encode ($pas);
				exit (0);
			}
			$page_property = 'cName';
		}
		else if ($item_type == 'desc') {
			if (!preg_match ("/^.{2,255}$/", $item_value)) {
				$pas->message = t('Bad domain description!');
				echo $json->encode ($pas);
				exit (0);
			}
			$page_property = 'cDescription';
		}
		else if ($item_type == 'long_desc') {
			if (!preg_match ("/^.{2,255}$/", $item_value)) {
				$pas->status = "error";
				$pas->message = t('Bad long domain description!');
				echo $json->encode ($pas);
				exit (0);
			}
			$page_property = false;
		}
		else {
			$pas->status = "error";
			$pas->message = "Bad item type: $item_type!";
			echo $json->encode ($pas);
			exit (0);
		}

		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info['fse_id'] != $_SESSION['FSEInfo']['fse_id']) {
			$pas->message = t('You are not the owner of the project!');
			echo $json->encode ($pas);
			exit (0);
		}

		$db = Loader::db ();
		$res = $db->Execute ("UPDATE fsen_project_doc_domains SET domain_$item_type=?
	WHERE project_id=? AND domain_handle=?",
				array ($item_value, $project_id, $domain_handle));
		if ($db->Affected_Rows () == 0) {
			$pas->message = t('Nothing changed!');
			echo $json->encode ($pas);
			exit (0);
		}

		ProjectInfo::onUpdateProjectDomainInfo ($project_id, $domain_handle);

		/* update page attributes and refresh block caches */
		$cache = PageCache::getLibrary();
		if ($domain_handle != 'home') {
			$domain_page = ProjectInfo::getProjectPage ($project_id, $domain_handle);
			if ($domain_page != false) {
				$cache->purge ($domain_page);
			}
			if ($page_property) {
				$domain_page->update (array ($page_property => $item_value));
			}
		}
		$cache->purge ($project_home_page);

		$pas->status = 'success';
		$pas->message = t('Item changed!');
		echo $json->encode ($pas);
		exit (0);
	}

	public function add_new_doc_part () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$new_volume_name = $this->post('newVolumeName');
		$new_volume_handle = $this->post('newVolumeHandle');
		$new_volume_desc = $this->post('newVolumeDesc');
		$new_part_name = $this->post('newPartName');
		$new_part_handle = $this->post('newPartHandle');
		$new_part_desc = $this->post('newPartDesc');

		if (!in_array ($domain_handle, $this->mDomainList)) {
			$this->set ('error', t('Bad domain handle!'));
			return;
		}

		if (!preg_match ("/^[a-z0-9_\-]{4,64}$/", $project_id)) {
			$this->set ('error', t('Invalid project identifier!'));
			return;
		}

		$project_home_page = ProjectInfo::getProjectPage ($project_id, 'home');
		if (project_home_page == false) {
			$this->set ('error', t('No such project!'));
			return;
		}

		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info['fse_id'] != $_SESSION['FSEInfo']['fse_id']) {
			$this->set ('error', t('You are not the owner of the project!'));
			return;
		}

		$txt = Loader::helper('text');
		$db = Loader::db ();
		if ($volume_handle == 'new') {
			if (!preg_match ("/^[a-z0-9\-]{3,16}$/", $new_volume_handle)) {
				$this->set ('error', t('Bad volume handle!'));
				return;
			}

			if (!preg_match ("/^.{1,64}$/", $new_volume_name)) {
				$this->set ('error', t('Bad volume name!'));
				return;
			}

			$new_volume_handle = $txt->urlify ($new_volume_handle);
			$res = $db->Execute ('INSERT IGNORE fsen_project_doc_volumes (project_id, domain_handle, volume_handle, volume_name, volume_desc, required, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)',
				array ($project_id, $domain_handle, $new_volume_handle,
					$new_volume_name, $new_volume_desc, 0, time()));
			if ($db->Affected_Rows () == 0) {
				$this->set ('error', t('Failed to add new volume!'));
				return;
			}

			$volume_handle = $new_volume_handle;
		}

		if ($domain_handle != 'home') {
			$domain_home = ProjectInfo::getProjectPage ($project_id, $domain_handle);
		}
		else {
			$domain_home = $project_home_page;
		}
		if ($domain_home == false) {
			$this->set ('error', t('No domain page for this project!'));
			return;
		}

		$volume_page = ProjectInfo::addVolumePage ($project_id, $domain_home, $domain_handle,
				$volume_handle, $new_volume_name, $new_volume_desc);
		if (!($volume_page instanceof Page)) {
			$this->set ('error', t('Failed to find/create volume page!'));
			return;
		}

		if (!preg_match ("/^[a-z0-9\-]{3,16}$/", $new_part_handle)) {
			$this->set ('error', t('Bad part handle!'));
			return;
		}

		if (!preg_match ("/^.{1,64}$/", $new_part_name)) {
			$this->set ('error', t('Bad part name!'));
			return;
		}

		$new_part_handle = $txt->urlify ($new_part_handle);
		$res = $db->Execute ('INSERT IGNORE fsen_project_doc_volume_parts (project_id, domain_handle, volume_handle,
		part_handle, part_name, part_desc, required, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
			array ($project_id, $domain_handle, $volume_handle, $new_part_handle, $new_part_name, $new_part_desc, 0, time()));
		if ($db->Affected_Rows () == 0) {
			$this->set ('error', t('Failed to add new part!'));
			return;
		}

		$part_page = ProjectInfo::addPartPage ($project_id, $domain_handle, $volume_page,
			$new_part_handle, $new_part_name, $new_part_desc);
		if (!($part_page instanceof Page)) {
			$this->set ('error', t('Failed to create part page!'));
			return;
		}

		ProjectInfo::onUpdateProjectVolumeInfo ($project_id, $domain_handle, $volume_handle);

		$this->set ('success', t('Success to add new part!'));
	}

	public function change_doc_part_name_desc () {
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('itemDomain');
		$volume_handle = $this->post('itemVolume');
		$part_handle = $this->post('itemPart');
		$item_type = $this->post('itemType');
		$item_value = $this->post('itemValue');

		$json = Loader::helper ('json');
		$pas = new PageActionStatus;
		$pas->action = t('Change Volume/Part Name/Desc');
		$pas->status = t('Unkown error');
		$pas->time = time ();

		if (!fse_try_to_login ()) {
			$pas->message = t('You do not sign in or session expired.');
			echo $json->encode ($pas);
			exit (0);
		}

		if (!in_array ($domain_handle, $this->mDomainList)) {
			$pas->status = "error";
			$pas->message = "Invalid doc domain: $domain_handle!";
			echo $json->encode ($pas);
			exit (0);
		}

		if (!preg_match ("/^[a-z0-9_\-]{4,64}$/", $project_id)) {
			$pas->status = "error";
			$pas->message = "Invalid given project ID: $project_id!";
			echo $json->encode ($pas);
			exit (0);
		}

		$project_home_page = ProjectInfo::getProjectPage ($project_id, 'home');
		if ($project_home_page == false) {
			$pas->status = "error";
			$pas->message = "No such project: $project_id!";
			echo $json->encode ($pas);
			exit (0);
		}

		if ($item_type == 'name') {
			if (!preg_match ("/^.{1,64}$/", $item_value)) {
				$pas->status = "error";
				$pas->message = "Bad volume/part name: $item_value!";
				echo $json->encode ($pas);
				exit (0);
			}
			$page_property = 'cName';
		}
		else if ($item_type == 'desc') {
			if (!preg_match ("/^.{2,255}$/", $item_value)) {
				$pas->status = "error";
				$pas->message = "Bad volume/part desc: $item_value!";
				echo $json->encode ($pas);
				exit (0);
			}
			$page_property = 'cDescription';
		}
		else {
			$pas->status = "error";
			$pas->message = "Bad item type: $item_type!";
			echo $json->encode ($pas);
			exit (0);
		}

		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info['fse_id'] != $_SESSION['FSEInfo']['fse_id']) {
			$pas->status = "error";
			$pas->message = "You are not the owner of $project_id!";
			echo $json->encode ($pas);
			exit (0);
		}

		$db = Loader::db ();
		if (preg_match ("/^[a-z0-9\-]{3,16}$/", $part_handle)) {
			$res = $db->Execute ("UPDATE fsen_project_doc_volume_parts SET part_$item_type=?
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=?",
				array ($item_value, $project_id, $domain_handle, $volume_handle, $part_handle));
			ProjectInfo::onUpdateProjectPartInfo ($project_id, $domain_handle, $volume_handle, $part_handle);
		}
		else {
			$res = $db->Execute ("UPDATE fsen_project_doc_volumes SET volume_$item_type=?
	WHERE project_id=? AND domain_handle=? AND volume_handle=?",
				array ($item_value, $project_id, $domain_handle, $volume_handle));
			ProjectInfo::onUpdateProjectVolumeInfo ($project_id, $domain_handle, $volume_handle);
		}
		if ($db->Affected_Rows () == 0) {
			$pas->status = "error";
			$pas->message = "Nothing changed!";
			echo $json->encode ($pas);
			exit (0);
		}

		$cache = PageCache::getLibrary();
		/* update page attributes and refresh block caches */
		if (preg_match ("/^[a-z0-9\-]{3,16}$/", $part_handle)) {
			$page = ProjectInfo::getProjectPage ($project_id, $domain_handle, $volume_handle, $part_handle);
			if ($page != false) {
				$page->update (array ($page_property => $item_value));
				$cache->purge ($page);
			}
		}
		else {
			$page = ProjectInfo::getProjectPage ($project_id, $domain_handle, $volume_handle);
			if ($page != false) {
				$page->update (array ($page_property => $item_value));
				$cache->purge ($page);
			}
			$cache->purge ($project_home_page);
		}

		$pas->status = "success";
		$pas->message = "Item changed!";
		echo $json->encode ($pas);
		exit (0);
	}

	public function get_user_roles_and_rights ($project_id = false, $user_name = false) {
		$ret_info = new ReturnInfo;
		$ret_info->status = 'bad';

		$js = Loader::helper ('json');
		if ($project_id == false || ProjectInfo::getDomainName ($project_id, 'home') == false) {
			echo $js->encode ($ret_info);
			exit (0);
		}

		if ($user_name == false) {
			if (fse_try_to_login ()) {
				$fse_id = $_SESSION['FSEInfo']['fse_id'];
				$user_name = $_SESSION['FSEInfo']['user_name'];
			}
			else {
				echo $js->encode ($ret_info);
				exit (0);
			}
		}
		else {
			$fse_info = FSEInfo::getBasicProfile ($user_name);
			if ($fse_info == false) {
				echo $js->encode ($ret_info);
				exit (0);
			}

			$fse_id = $fse_info ['fse_id'];
		}

		$ret_info->status = 'ok';
		$ret_info->project_id = $project_id;
		$ret_info->user_name = $user_name;

		$roles = ProjectInfo::getUserRoles ($project_id, $fse_id);
		$ret_info->roles = $roles ['member_roles'];
		$ret_info->rights = $roles ['member_rights'];
		echo $js->encode ($ret_info);
		exit (0);
	}

	const MIN_CONTENT_LEN = 20;

	const MEMBER_MARKDOWN_TEXT = '
<div markdown="1" class="panel-heading">
### [%1$s](%2$s) {.panel-title}
</div>
<div class="panel-body">
	<div class="media">
   		<a class="media-left" href="%2$s"><img
				class="media-object img-rounded" alt="Avatar"
				src="%6$s"
				style="width: 48px; height: 48px;" /></a>
   		<div class="media-body">
   			<h4 class="media-heading">%3$s</h4>
			<div markdown="1">
%4$s
			</div>
		</div>
	</div>
</div>
<div class="panel-footer">
%5$s
</div> ';

	public function add_new_section () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$page_id = $this->post('cID');
		$project_id = $this->post('projectID');
		$area_handle = $this->post('areaHandle');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');

		$content_type = $this->post('contentType');
		$content_format = $this->post('contentFormat');
		$content_code_lang = $this->post('contentCodeLang');
		$content_wrapper = $this->post('contentWrapper');
		$content_style = $this->post('contentStyle');
		$content_alignment = $this->post('contentAlignment');

		$section_subject = $this->post('sectionSubject');
		$section_content = $this->post('sectionContent');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);

		$fse_id = $_SESSION['FSEInfo']['fse_id'];
		$form_token_name = $this->post('formTokenName');
		if (isset ($form_token_name)) {
			$form_token = $this->post('formToken');
			if ($_SESSION [$form_token_name] != $form_token) {
				unset ($_SESSION [$form_token_name]);
				set_page_action_status ($page_id, t('New Section'), 'error', t('Bad request or session expired!'));
				header ("Location: $page_path");
				return;
			}
			unset ($_SESSION [$form_token_name]);
		}
		else {
			if (!isset ($_SESSION['FSEInfo'])) {
				set_page_action_status ($page_id, t('New Section'), 'error', t('You do not sign in or session expired.'));
				header ("Location: $page_path");
				return;
			}

			$project_info = ProjectInfo::getBasicInfo ($project_id);
			if ($project_info == false) {
				set_page_action_status ($page_id, t('New Section'), 'error', t('No such project!'));
				header ("Location: $page_path");
				return;
			}

			$user_rights = ProjectInfo::getUserRights ($project_id, $fse_id);
			if ($domain_handle == 'community' && $part_handle != 'na') {
			}
			else if ($user_rights[1] != 't') {
				set_page_action_status ($page_id, t('New Section'),
					'error', t('You have no right to edit the content of this project.'));
				header ("Location: $page_path");
				return;
			}
		}

		$type_handle = DocSectionManager::getContentTypeHandle ($content_type, $content_format, $content_code_lang,
				$content_wrapper, $content_style, $content_alignment);
		if ($type_handle == false) {
			set_page_action_status ($page_id, t('New Section'), 'error', t('Bad content type.'));
			header ("Location: $page_path");
			return;
		}

		if (mb_strlen ($section_content) < self::MIN_CONTENT_LEN) {
			set_page_action_status ($page_id, t('New Section'), 'error', t('Too short content!'));
			header ("Location: $page_path");
			return;
		}

		$attached_files = '[';
		for ($i = 0; $i < DocSectionManager::MAX_ATTACHED_FILES; $i++) {
			$attached_file_id = (int)$this->post("attachmentFile$i");
			if ($attached_file_id > 0) {
				$attached_files .= "$attached_file_id, ";
			}
		}
		$attached_files = rtrim ($attached_files, ', ');
		$attached_files .= ']';

		$section_manager = new DocSectionManager ();
		$res = $section_manager->addNewSection ($fse_id, $page_id, $area_handle,
			$project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle,
			$type_handle, $section_subject, $section_content, $attached_files);
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('New Section'),
				'error', t('Failed to add a new section: %s', $section_manager->getErrorMessage ($res)));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, t('New Section'), 'success', t('Succeed to add a new section.'));
		header ("Location: $page_path");
	}

	public function edit_section () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$section_id = $this->post('sectionID');

		$content_type = $this->post('contentType');
		$content_format = $this->post('contentFormat');
		$content_code_lang = $this->post('contentCodeLang');
		$content_wrapper = $this->post('contentWrapper');
		$content_style = $this->post('contentStyle');
		$content_alignment = $this->post('contentAlignment');

		$section_subject = $this->post('sectionSubject');
		$section_content = $this->post('sectionContent');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$page_id = Page::getByPath ($page_path)->getCollectionID();

		$fse_id = $_SESSION['FSEInfo']['fse_id'];
		$form_token_name = $this->post('formTokenName');
		if (isset ($form_token_name)) {
			$form_token = $this->post('formToken');
			if ($_SESSION [$form_token_name] != $form_token) {
				unset ($_SESSION [$form_token_name]);
				set_page_action_status ($page_id, t('Edit Section'), 'error', t('Bad request or session expired!'));
				header ("Location: $page_path");
				return;
			}
			unset ($_SESSION [$form_token_name]);
		}
		else {
			if (!isset ($_SESSION['FSEInfo'])) {
				set_page_action_status ($page_id, t('Edit Section'), 'error', t('You do not sign in or session expired.'));
				header ("Location: $page_path");
				return;
			}

			$project_info = ProjectInfo::getBasicInfo ($project_id);
			if ($project_info == false) {
				set_page_action_status ($page_id, t('Edit Section'), 'error', t('No such project!'));
				header ("Location: $page_path");
				return;
			}

			if (substr (ProjectInfo::getUserRights ($project_id, $fse_id), 1, 1) != 't') {
				set_page_action_status ($page_id, t('Edit Section'),
					'error', t('You have no right to edit the content of this project.'));
				header ("Location: $page_path");
				return;
			}
		}

		$type_handle = DocSectionManager::getContentTypeHandle ($content_type, $content_format, $content_code_lang,
				$content_wrapper, $content_style, $content_alignment);
		if ($type_handle == false) {
			set_page_action_status ($page_id, t('Edit Section'), 'error', t('Bad content type.'));
			header ("Location: $page_path");
			return;
		}

		if (mb_strlen ($section_content) < self::MIN_CONTENT_LEN) {
			set_page_action_status ($page_id, t('Edit Section'), 'error', t('Too short content!'));
			header ("Location: $page_path");
			return;
		}

		$attached_files = '[';
		for ($i = 0; $i < DocSectionManager::MAX_ATTACHED_FILES; $i++) {
			$attached_file_id = (int)$this->post("attachmentFile$i");
			if ($attached_file_id > 0) {
				$attached_files .= "$attached_file_id, ";
			}
		}
		$attached_files = rtrim ($attached_files, ', ');
		$attached_files .= ']';

		$section_manager = new DocSectionManager ();
		$res = $section_manager->addNewSectionVersion ($project_id, $fse_id, $domain_handle, $section_id,
			$type_handle, $section_subject, $section_content, $attached_files);

		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('Edit Section'),
				'error', t('Failed to add a new version: %s', $section_manager->getErrorMessage ($res)));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, t('Edit Section'), 'success', t('Succeed to add a new section version.'));
		header ("Location: $page_path");
	}

	public function set_section_version () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$section_id = $this->post('sectionID');
		$new_ver_code = $this->post('newVerCode');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$page_id = Page::getByPath ($page_path)->getCollectionID();

		$fse_id = $_SESSION['FSEInfo']['fse_id'];
		$form_token_name = $this->post('formTokenName');
		if (isset ($form_token_name)) {
			$form_token = $this->post('formToken');
			if ($_SESSION [$form_token_name] != $form_token) {
				unset ($_SESSION [$form_token_name]);
				set_page_action_status ($page_id, t('Set Section Version'), 'error', t('Bad request or session expired!'));
				header ("Location: $page_path");
				return;
			}
			unset ($_SESSION [$form_token_name]);
		}
		else {
			if (!isset ($_SESSION['FSEInfo'])) {
				set_page_action_status ($page_id, t('Set Section Version'),
					'error', t('You do not sign in or session expired.'));
				header ("Location: $page_path");
				return;
			}

			$project_info = ProjectInfo::getBasicInfo ($project_id);
			if ($project_info == false) {
				set_page_action_status ($page_id, t('Set Section Version'), 'error', t('No such project!'));
				header ("Location: $page_path");
				return;
			}

			if (substr (ProjectInfo::getUserRights ($project_id, $fse_id), 1, 1) != 't') {
				set_page_action_status ($page_id, t('Set Section Version'),
					'error', t('You have no right to edit the content of this project.'));
				header ("Location: $page_path");
				return;
			}
		}

		$section_manager = new DocSectionManager ();
		$res = $section_manager->setSectionVersion ($project_id, $domain_handle, $section_id, $new_ver_code);
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('Set Section Version'),
				'error', t('Failed to set section version: %s', $section_manager->getErrorMessage ($res)));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, t('Set Section Version'), 'success', t('Succeed to set new version.'));

		header ("Location: $page_path");
	}

	public function delete_section () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$section_id = $this->post('sectionID');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$page_id = Page::getByPath ($page_path)->getCollectionID();

		$fse_id = $_SESSION['FSEInfo']['fse_id'];
		$form_token_name = $this->post('formTokenName');
		if (isset ($form_token_name)) {
			$form_token = $this->post('formToken');
			if ($_SESSION [$form_token_name] != $form_token) {
				unset ($_SESSION [$form_token_name]);
				set_page_action_status ($page_id, t('Delete Section'), 'error', t('Bad request or session expired!'));
				header ("Location: $page_path");
				return;
			}
			unset ($_SESSION [$form_token_name]);
		}
		else {
			if (!isset ($_SESSION['FSEInfo'])) {
				set_page_action_status ($page_id, t('Delete Section'), 'error', t('You do not sign in or session expired.'));
				header ("Location: $page_path");
				return;
			}

			$project_info = ProjectInfo::getBasicInfo ($project_id);
			if ($project_info == false) {
				set_page_action_status ($page_id, t('Delete Section'), 'error', t('No such project!'));
				header ("Location: $page_path");
				return;
			}

			if (substr (ProjectInfo::getUserRights ($project_id, $fse_id), 1, 1) != 't') {
				set_page_action_status ($page_id, t('Delete Section'),
					'error', t('You have no right to edit the content of this project.'));
				header ("Location: $page_path");
				return;
			}
		}

		$section_manager = new DocSectionManager ();
		$res = $section_manager->deleteSection ($project_id, $domain_handle, $section_id);
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('Delete Section'),
				'error', t('Failed to delete section: %s', $section_manager->getErrorMessage ($res)));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, t('Delete Section'), 'success', t('Succeed to delete the section.'));

		header ("Location: $page_path");
	}

	public function add_new_member () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$area_handle = $this->post('areaHandle');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');

		$member_username = $this->post('memberUsername');
		$member_display_name = $this->post('memberDisplayName');
		$member_desc = $this->post('memberDescription');
		$member_roles = array ('g-mmb');
		for ($i = 0; $i < ProjectInfo::NR_ROLES; $i++) {
			$role = $this->post("memberRole$i");
			if (in_array ($role, ProjectInfo::$mMemberRoleList)) {
				$member_roles [] = $role;
			}
		}
		$member_roles = array_unique ($member_roles);
		$role_description = '';
		$doc_lang = substr ($project_id, -2);
		foreach ($member_roles as $role) {
			$role_description .= ProjectInfo::$mRoleDescriptions [$doc_lang][$role];
			$role_description .= ' ';
		}

		$member_roles = implode ('|', $member_roles);

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$page_id = Page::getByPath ($page_path)->getCollectionID();
		if ($page_id <= 0) {
			header ("Location: /");
			return;
		}

		if (!fse_try_to_login ()) {
			set_page_action_status ($page_id, t('Add/Edit Member Roles'), 'error', t('You do not sign in or session expired.'));
			header ("Location: $page_path");
			return;
		}

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		$curr_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);
		if ($curr_rights [0] != 't') {
			set_page_action_status ($page_id, t('Add/Edit Member Roles'), 'error', t('You have no right to edit member roles.'));
			header ("Location: $page_path");
			return;
		}

		$fse_info = FSEInfo::getBasicProfile ($member_username);
		if ($fse_info == false) {
			set_page_action_status ($page_id, t('Add/Edit Member Roles'), 'error', t('No such user!'));
			header ("Location: $page_path");
			return;
		}

		if (strlen ($member_display_name) == 0) {
			$member_display_name = $fse_info['nick_name'];
		}

		$member_rights = ProjectInfo::setUserRoles ($project_id, $fse_info ['fse_id'],
				$member_display_name, $member_desc, $member_roles);
		if (substr ($member_rights, 0, 3) == 'ttt') {
			$style = "primary";
		}
		else if ($member_rights [0] == 't') {
			$style = "success";
		}
		else if ($member_rights [1] == 't') {
			$style = "info";
		}
		else if ($member_rights [2] == 't') {
			$style = "warning";
		}
		else {
			$style = "default";
		}
		$type_handle = "member:markdown_safe:$member_username:$style:none";

		$section_content = sprintf (self::MEMBER_MARKDOWN_TEXT,
			$member_display_name, FSEInfo::getPersonalHomeLink ($fse_info), $role_description, h5($member_desc),
			h5($fse_info['self_desc']), $fse_info ['avatar_url']);

		$section_manager = new DocSectionManager ();
		$res = $section_manager->addNewSection ($curr_fse_id, $page_id, $area_handle,
			$project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle,
			$type_handle, '', $section_content, '[]');
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('Add/Edit Member Roles'), 'error',
				t('Failed to add/edit member: %s', $section_manager->getErrorMessage ($res)));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, t('Add/Edit Member Roles'), 'success', t('Succeed to add a new member.'));
		header ("Location: $page_path");
	}

	public function edit_member_roles () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$area_handle = $this->post('areaHandle');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$section_id = $this->post('sectionID');

		$member_username = $this->post('memberUsername');
		$member_display_name = $this->post('memberDisplayName');
		$member_desc = $this->post('memberDescription');
		$member_roles = array ('g-mmb');
		for ($i = 0; $i < ProjectInfo::NR_ROLES; $i++) {
			$role = $this->post("memberRole$i");
			if (in_array ($role, ProjectInfo::$mMemberRoleList)) {
				$member_roles [] = $role;
			}
		}
		$member_roles = array_unique ($member_roles);
		$role_description = '';
		$doc_lang = substr ($project_id, -2);
		foreach ($member_roles as $role) {
			$role_description .= ProjectInfo::$mRoleDescriptions [$doc_lang][$role];
			$role_description .= ' ';
		}
		$member_roles = implode ('|', $member_roles);

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		error_log ("Edit Member: $page_path\n", 3, '/var/tmp/fsen.log');
		$page_id = Page::getByPath ($page_path)->getCollectionID();
		if ($page_id <= 0) {
			header ('Location: /');
			return;
		}

		if (!fse_try_to_login ()) {
			set_page_action_status ($page_id, t('Add/Edit Member Roles'), 'error', t('You do not sign in or session expired.'));
			header ("Location: $page_path");
			return;
		}

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		$curr_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);
		if ($curr_rights [0] != 't') {
			set_page_action_status ($page_id, t('Add/Edit Member Roles'), 'error', t('You have no right to edit member roles.'));
			header ("Location: $page_path");
			return;
		}

		$fse_info = FSEInfo::getBasicProfile ($member_username);
		if ($fse_info == false) {
			set_page_action_status ($page_id, t('Add/Edit Member Roles'), 'error', t('No such user!'));
			header ("Location: $page_path");
			return;
		}

		if (strlen ($member_display_name) == 0) {
			$member_display_name = $fse_info['nick_name'];
		}

		$member_rights = ProjectInfo::setUserRoles ($project_id, $fse_info ['fse_id'],
				$member_display_name, $member_desc, $member_roles);
		if (substr ($member_rights, 0, 3) == 'ttt') {
			$style = "primary";
		}
		else if ($member_rights [0] == 't') {
			$style = "success";
		}
		else if ($member_rights [1] == 't') {
			$style = "info";
		}
		else if ($member_rights [2] == 't') {
			$style = "warning";
		}
		else {
			$style = "default";
		}
		$type_handle = "member:markdown_safe:$member_username:$style:none";

		$section_content = sprintf (self::MEMBER_MARKDOWN_TEXT,
			$member_display_name, $fse_info['user_name'], $role_description, h5($member_desc),
			h5($fse_info['self_desc']), $fse_info ['avatar_url']);

		$section_manager = new DocSectionManager ();
		$res = $section_manager->addNewSectionVersion ($project_id, $curr_fse_id, $domain_handle, $section_id,
			$type_handle, '', $section_content, '[]');
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('Add/Edit Member Roles'), 'error',
				t('Failed to add/edit member roles: %s', $section_manager->getErrorMessage ($res)));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, t('Add/Edit Member Roles'), 'success', t('Succeed to edit the member roles.'));
		header ("Location: $page_path");
	}

	public function delete_member () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$area_handle = $this->post('areaHandle');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$section_id = $this->post('sectionID');

		$member_username = $this->post('memberUsername');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$page_id = Page::getByPath ($page_path)->getCollectionID();
		if ($page_id <= 0) {
			header ('Location: /');
			return;
		}

		if (!fse_try_to_login ()) {
			set_page_action_status ($page_id, t('Delete Member'), 'error', t('You do not sign in or session expired.'));
			header ("Location: $page_path");
			return;
		}

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		$curr_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);
		if ($curr_rights [0] != 't') {
			set_page_action_status ($page_id, t('Delete Member'), 'error', t('You have no right to edit member roles.'));
			header ("Location: $page_path");
			return;
		}

		$fse_info = FSEInfo::getBasicProfile ($member_username);
		if ($fse_info == false) {
			set_page_action_status ($page_id, t('Delete Member'), 'error', t('No such user!'));
			header ("Location: $page_path");
			return;
		}

		ProjectInfo::removeMember ($project_id, $fse_info ['fse_id']);

		$section_manager = new DocSectionManager ();
		$res = $section_manager->deleteSection ($project_id, $domain_handle, $section_id);
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('Delete Member'), 'error',
				t('Failed to add/edit member roles: %s', $section_manager->getErrorMessage ($res)));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, t('Delete Member'), 'success', t('Succeed to delete the member.'));
		header ("Location: $page_path");
	}

	public function add_new_chapter () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$page_id = $this->post('cID');
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$chapter_name = $this->post('chapterSubject');
		$chapter_desc = $this->post('chapterDesc');
		$must_for_newbie = (int)$this->post('mustForNewbie');

		$curr_page = Page::getByID ($page_id);
		$page_path = $curr_page->getCollectionPath ();

		$part_page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, 'na');
		$part_page = Page::getByPath ($part_page_path);
		if ($part_page->getCollectionID() <= 0) {
			set_page_action_status ($page_id, t('Add New Chapter'), 'error', t('No parent (part) page!'));
			header ("Location: $page_path");
			return;
		}

		$txt = Loader::helper('text');
		$chapter_handle = $txt->urlify ($chapter_handle);
		if (!preg_match ("/^[a-z0-9\-]{3,120}$/", $chapter_handle)) {
			set_page_action_status ($page_id, t('Add New Chapter'), 'error', t('Bad new chapter handle!') . $chapter_handle);
			header ("Location: $page_path");
			return;
		}

		if (!preg_match ("/^.{1,64}$/", $chapter_name)) {
			set_page_action_status ($page_id, t('Add New Chapter'), 'error', t('Too short/long chapter name!'));
			header ("Location: $page_path");
			return;
		}

		if (!preg_match ("/^.{2,255}$/", $chapter_desc)) {
			set_page_action_status ($page_id, t('Add New Chapter'), 'error', t('Too short/long chapter description!'));
			header ("Location: $page_path");
			return;
		}

		if (!fse_try_to_login ()) {
			set_page_action_status ($page_id, t('Add New Chapter'), 'error', t('You do not sign in or session expired!'));
			header ("Location: $page_path");
			return;
		}

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		$curr_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);
		if ($curr_rights [1] != 't') {
			set_page_action_status ($page_id, t('Add New Chapter'), 'error',
					t('You have no right to edit the content of this project.'));
			header ("Location: $page_path");
			return;
		}

		$chapter_page = ProjectInfo::addChapterPage ($project_id, $domain_handle, $volume_handle, $part_page,
				$chapter_handle, $chapter_name, $chapter_desc, $must_for_newbie);
		if ($chapter_page == false) {
			set_page_action_status ($page_id, t('Add New Chapter'), 'error',
					t('Failed to add a chapter page.'));
			header ("Location: $page_path");
			return false;
		}

		set_page_action_status ($page_id, t('Add New Chapter'), 'success', t('Succeed to add a new chapter.'));
		header ("Location: $page_path");
	}

	public function edit_chapter () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$page_id = $this->post('cID');
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$chapter_name = $this->post('chapterSubject');
		$chapter_desc = $this->post('chapterDesc');
		$must_for_newbie = (int)$this->post('mustForNewbie');

		$page_path = Page::getByID ($page_id)->getCollectionPath ();

		$cpt_page_path
			= ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$cpt_page = Page::getByPath ($cpt_page_path);
		if ($cpt_page->getCollectionID() <= 0) {
			set_page_action_status ($page_id, t('Edit Chapter'), 'error', t('No such chapter!'));
			header ("Location: $page_path");
			return;
		}

		if (!preg_match ("/^.{1,64}$/", $chapter_name)) {
			set_page_action_status ($page_id, t('Edit Chapter'), 'error', t('Too short/long chapter name!'));
			header ("Location: $page_path");
			return;
		}

		if (!preg_match ("/^.{2,255}$/", $chapter_desc)) {
			set_page_action_status ($page_id, t('Edit Chapter'), 'error', t('Too short/long chapter description!'));
			header ("Location: $page_path");
			return;
		}

		if (!fse_try_to_login ()) {
			set_page_action_status ($page_id, t('Edit Chapter'), 'error', t('You do not sign in or session expired.'));
			header ("Location: $page_path");
			return;
		}

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		$curr_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);
		if ($curr_rights [1] != 't') {
			set_page_action_status ($page_id, t('Edit Chapter'), 'error',
					t('You have no right to edit the content of this project.'));
			header ("Location: $page_path");
			return;
		}

		$cpt_page->update (array ("cName" => $chapter_name, "cDescription" => $chapter_desc));

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$q = "UPDATE fsen_project_doc_volume_part_chapters_$doc_lang SET chapter_name=?, chapter_desc=?, required=?
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND chapter_handle=?";
		$res = $db->Execute ($q, array ($chapter_name, $chapter_desc, $must_for_newbie,
					$project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle));

		ProjectInfo::onUpdateProjectChapterInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);

		set_page_action_status ($page_id, t('Edit Chapter'), 'success', t('Succeed to edit the chapter.'));
		header ("Location: $page_path");
	}

	public function delete_chapter () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$page_id = $this->post('cID');
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');

		$page_path = Page::getByID ($page_id)->getCollectionPath ();

		$cpt_page_path
			= ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$cpt_page = Page::getByPath ($cpt_page_path);
		if ($cpt_page->getCollectionID() <= 0) {
			set_page_action_status ($page_id, t('Delete Chapter'), 'error', t('No such chapter!'));
			header ("Location: $page_path");
			return;
		}

		if (!fse_try_to_login ()) {
			set_page_action_status ($page_id, t('Delete Chapter'), 'error', t('You do not sign in or session expired.'));
			header ("Location: $page_path");
			return;
		}

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		$curr_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);
		if ($curr_rights [1] != 't') {
			set_page_action_status ($page_id, t('Delete Chapter'), 'error',
					t('You have no right to edit the content of this project.'));
			header ("Location: $page_path");
			return;
		}

		$cpt_page->delete ();

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$q = "DELETE FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND chapter_handle=?";
		$res = $db->Execute ($q, array ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle));

		ProjectInfo::onUpdateProjectChapterInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);

		set_page_action_status ($page_id, t('Delete Chapter'), 'success', t('Succeed to delete the chapter.'));
		header ("Location: $page_path");
	}

	public function add_new_forum_thread () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$page_id = $this->post('cID');
		$area_handle = $this->post('areaHandle');
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');

		$post_type = $this->post('postType');
		$thread_subject = $this->post('threadSubject');
		$post_content = $this->post('postContent');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, 'na');
		$part_page = Page::getByPath ($page_path);
		if ($part_page->getCollectionID() != $page_id) {
			header ("Location: /");
			return;
		}

		if (!preg_match ("/^.{1,64}$/", $thread_subject)) {
			set_page_action_status ($page_id, t('New Thread Post'), 'error', t('Bad subject!'));
			header ("Location: $page_path");
			return;
		}

		if (mb_strlen ($post_content) < self::MIN_CONTENT_LEN) {
			set_page_action_status ($page_id, t('New Thread Post'), 'error', t('Too short content!'));
			header ("Location: $page_path");
			return;
		}

		if (!fse_try_to_login ()) {
			set_page_action_status ($page_id, t('New Thread Post'), 'error', t('You do not sign in or session expired.'));
			header ("Location: $page_path");
			return;
		}

		$attached_files = '[';
		for ($i = 0; $i < DocSectionManager::MAX_ATTACHED_FILES; $i++) {
			$attached_file_id = (int)$this->post("attachmentFile$i");
			if ($attached_file_id > 0) {
				$attached_files .= "$attached_file_id, ";
			}
		}
		$attached_files = rtrim ($attached_files, ', ');
		$attached_files .= ']';

		$thread_desc = mb_substr ($post_content, 0, 255);
		$thread_page_handle = hash_hmac ("md5", microtime () . rand (), $thread_subject);
		$thread_page = ProjectInfo::addChapterPage ($project_id, $domain_handle, $volume_handle, $part_page,
				$thread_page_handle, $thread_subject, $thread_desc, 0);
		if ($thread_page == false) {
			set_page_action_status ($page_id, t('New Thread Post'), 'error', t('Failed to add a chapter page.'));
			header ("Location: $page_path");
			return;
		}

		$thread_page_id = $thread_page->getCollectionID ();

		/* Add a section for the new subject */
		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		if ($post_type == 'question') {
			$type_handle = "post-question:markdown_extra:none:none:none";
		}
		else {
			$type_handle = "post:markdown_extra:none:none:none";
		}
		$section_manager = new DocSectionManager ();
		$res = $section_manager->addNewSection ($curr_fse_id, $thread_page_id, $area_handle,
			$project_id, $domain_handle, $volume_handle, $part_handle, $thread_page_handle,
			$type_handle, t('Original Post'), $post_content, $attached_files);
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('New Thread Post'), 'error', $section_manager->getErrorMessage ($res));
			header ("Location: $page_path");
			return;
		}

		ProjectInfo::onChangeThreads ($project_id, $domain_handle, $volume_handle, $part_handle);
		header ("Location: $page_path/$thread_page_handle");
	}

	public function delete_thread () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$page_id = $this->post('cID');
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');

		$page_path = Page::getByID ($page_id)->getCollectionPath ();
		$cpt_page_path
				= ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$cpt_page = Page::getByPath ($cpt_page_path);
		if ($cpt_page->getCollectionID() <= 0) {
			set_page_action_status ($page_id, t('Delete Thread'), 'error', t('No such thread!'));
			header ("Location: $page_path");
			return;
		}

		if (!fse_try_to_login ()) {
			set_page_action_status ($page_id, t('Delete Thread'), 'error', t('You do not sign in or session expired.'));
			header ("Location: $page_path");
			return;
		}

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		$curr_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);
		if ($curr_rights [2] != 't') {
			set_page_action_status ($page_id, t('Delete Thread'), 'error',
					t('You have no right to manage the forum of this project.'));
			header ("Location: $page_path");
			return;
		}

		$cpt_page->delete ();

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$q = "DELETE FROM fsen_document_sections_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND chapter_handle=?";
		$res = $db->Execute ($q, array ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle));

		$q = "DELETE FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND chapter_handle=?";
		$res = $db->Execute ($q, array ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle));

		ProjectInfo::onUpdateProjectChapterInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		ProjectInfo::onChangeThreads ($project_id, $domain_handle, $volume_handle, $part_handle);

		set_page_action_status ($page_id, t('Delete Thread'), 'success', t('Succeed to delete the thread.'));
		header ("Location: $page_path");
	}

	public function toggle_thread_top () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$page_id = $this->post('cID');
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');

		$page_path = Page::getByID ($page_id)->getCollectionPath ();
		$cpt_page_path
			= ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$cpt_page = Page::getByPath ($cpt_page_path);
		if ($cpt_page->getCollectionID() <= 0) {
			set_page_action_status ($page_id, t('Top/Untop Thread'), 'error', t('No such thread!'));
			header ("Location: $page_path");
			return;
		}

		if (!fse_try_to_login ()) {
			set_page_action_status ($page_id, t('Top/Untop Thread'), 'error', t('You do not sign in or session expired.'));
			header ("Location: $page_path");
			return;
		}

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		$curr_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);
		if ($curr_rights [2] != 't') {
			set_page_action_status ($page_id, t('Top/Untop Thread'), 'error',
					t('You have no right to manage the forum of this project.'));
			header ("Location: $page_path");
			return;
		}

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$q = "UPDATE fsen_project_doc_volume_part_chapters_$doc_lang SET required=1-required
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND chapter_handle=?";
		$res = $db->Execute ($q, array ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle));

		ProjectInfo::onChangeThreads ($project_id, $domain_handle, $volume_handle, $part_handle);

		set_page_action_status ($page_id, t('Top/Untop Thread'), 'success', t('Succeed to top/untop the thread.'));
		header ("Location: $page_path");
	}

	public function add_new_thread_reply () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$page_id = $this->post('cID');
		$area_handle = $this->post('areaHandle');
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$post_subject = $this->post('postSubject');
		$post_content = $this->post('postContent');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$page = Page::getByPath ($page_path);
		if ($page->getCollectionID() != $page_id) {
			header ("Location: /");
			return;
		}

		if (mb_strlen ($post_content) < self::MIN_CONTENT_LEN) {
			set_page_action_status ($page_id, t('New Reply'), 'error', t('Too short content!'));
			header ("Location: $page_path");
			return;
		}

		if (!fse_try_to_login ()) {
			set_page_action_status ($page_id, t('New Reply'), 'error', t('You do not sign in or session expired.'));
			header ("Location: $page_path");
			return;
		}

		$attached_files = '[';
		for ($i = 0; $i < DocSectionManager::MAX_ATTACHED_FILES; $i++) {
			$attached_file_id = (int)$this->post("attachmentFile$i");
			if ($attached_file_id > 0) {
				$attached_files .= "$attached_file_id, ";
			}
		}
		$attached_files = rtrim ($attached_files, ', ');
		$attached_files .= ']';

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];

		/* Add a section for the new subject */
		$type_handle = "post:markdown_extra:none:none:none";
		$section_manager = new DocSectionManager ();
		$res = $section_manager->addNewSection ($curr_fse_id, $page_id, $area_handle,
			$project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle,
			$type_handle, $post_subject, $post_content, $attached_files);
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('New Reply'), 'error', $section_manager->getErrorMessage ($res));
			header ("Location: $page_path");
			return;
		}

		ProjectInfo::onUpdateProjectChapterInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		ProjectInfo::onChangeThreads ($project_id, $domain_handle, $volume_handle, $part_handle);

		set_page_action_status ($page_id, t('New Reply'), 'success', t('Succeed to add new reply.'));
		header ("Location: $page_path");
	}

	public function delete_post () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$section_id = $this->post('sectionID');
		$author_id = $this->post('authorID');
		$delete_or_shield = $this->post('deleteOrShield');
		$form_token = $this->post('formToken');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$page = Page::getByPath ($page_path);
		$page_id = $page->getCollectionID();

		if ($_SESSION ['formToken4DeletePost'] != $form_token) {
			set_page_action_status ($page_id, 'Delete Post', 'error', t('Bad request or session expired.'));
			header ("Location: $page_path");
			return;
		}
		unset ($_SESSION ['formToken4DeletePost']);

		/* Add a new post version */
		$doc_lang = substr ($project_id, -2);
		if ($delete_or_shield == 'shield') {
			$post_content = t('Due to the violation against the community rules, this post had been shielded by the community administrator.');
			$post_status = DocSectionManager::SS_ADMIN_SHIELDED;
		}
		else {
			$post_content = t('This post had been deleted by the author or the community administrator.');
			if ($author_id == $_SESSION['FSEInfo']['fse_id']) {
				$post_status = DocSectionManager::SS_AUTHOR_DELETED;
			}
			else {
				$post_status = DocSectionManager::SS_ADMIN_DELETED;
			}
		}

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$db->Execute ("UPDATE fsen_document_sections_$doc_lang SET status=? WHERE id=?",
				array ($post_status, $section_id));

		$type_handle = "post:markdown_extra:none:none:none";
		$section_manager = new DocSectionManager ();
		$res = $section_manager->addNewSectionVersion ($project_id, $_SESSION['FSEInfo']['fse_id'], 
			$domain_handle, $section_id, $type_handle, '', $post_content, '[]');
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, 'Delete Post', 'error', $section_manager->getErrorMessage ($res));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, 'Delete Post', 'success', t('Succeed to delete/shield post.'));
		header ("Location: $page_path");
	}

	public function edit_post () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$section_id = $this->post('sectionID');
		$form_token = $this->post('formToken');

		$type_handle = $this->post('typeHandle');
		$post_subject = $this->post('postSubject');
		$post_content = $this->post('postContent');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$page = Page::getByPath ($page_path);
		$page_id = $page->getCollectionID();

		if ($_SESSION ['formToken4EditPost'] != $form_token) {
			set_page_action_status ($page_id, t('Edit Post'), 'error', t('Bad request or session expired.'));
			header ("Location: $page_path");
			return;
		}
		unset ($_SESSION ['formToken4EditPost']);

		if (mb_strlen ($post_content) < self::MIN_CONTENT_LEN) {
			set_page_action_status ($page_id, t('Edit Post'), 'error', t('Too short content!'));
			header ("Location: $page_path");
			return;
		}

		$attached_files = '[';
		for ($i = 0; $i < DocSectionManager::MAX_ATTACHED_FILES; $i++) {
			$attached_file_id = (int)$this->post("attachmentFile$i");
			if ($attached_file_id > 0) {
				$attached_files .= "$attached_file_id, ";
			}
		}
		$attached_files = rtrim ($attached_files, ', ');
		$attached_files .= ']';

		/* Add a new post version */
		$section_manager = new DocSectionManager ();
		$res = $section_manager->addNewSectionVersion ($project_id, $_SESSION['FSEInfo']['fse_id'],
			$domain_handle, $section_id, $type_handle, $post_subject, $post_content, $attached_files);
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('Edit Post'), 'error', $section_manager->getErrorMessage ($res));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, t('Edit Post'), 'success', t('Succeed to edit post.'));
		header ("Location: $page_path");
	}

	public function recover_post () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');
		$section_id = $this->post('sectionID');
		$curr_ver_code = (int)$this->post('currentVerCode');
		$form_token = $this->post('formToken');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$page = Page::getByPath ($page_path);
		$page_id = $page->getCollectionID();

		if ($_SESSION ['formToken4RecoverPost'] != $form_token) {
			set_page_action_status ($page_id, t('Recover Post'), 'error', t('Bad request or session expired.'));
			header ("Location: $page_path");
			return;
		}
		unset ($_SESSION ['formToken4RecoverPost']);

		if ($curr_ver_code <= 0) {
			set_page_action_status ($page_id, t('Recover Post'), 'error', t('Already be the original edition.'));
			header ("Location: $page_path");
			return;
		}

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$db->Execute ("UPDATE fsen_document_sections_$doc_lang SET status=0 WHERE id=?", array ($section_id));

		$section_manager = new DocSectionManager ();
		$res = $section_manager->setSectionVersion ($project_id, $domain_handle, $section_id, $curr_ver_code - 1);
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('Recover Post'), 'error', $section_manager->getErrorMessage ($res));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, t('Recover Post'), 'success', t('Succeed to recover the post.'));
		header ("Location: $page_path");
	}

	public function add_new_ref_link_list () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$page_id = $this->post('cID');
		$project_id = $this->post('projectID');
		$area_handle = $this->post('areaHandle');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');

		$page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);

		if (!isset ($_SESSION['FSEInfo'])) {
			set_page_action_status ($page_id, t('New Reference'), 'error', t('You do not sign in or session expired.'));
			header ("Location: $page_path");
			return;
		}

		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info == false) {
			set_page_action_status ($page_id, t('New Reference'), 'error', t('No such project!'));
			header ("Location: $page_path");
			return;
		}

		$fse_id = $_SESSION['FSEInfo']['fse_id'];
		if ($domain_handle == 'community' && $part_handle != 'na') {
		}
		else if (substr (ProjectInfo::getUserRights ($project_id, $fse_id), 1, 1) != 't') {
			set_page_action_status ($page_id, t('New Reference'),
				'error', t('You have no right to edit the content of this project.'));
			header ("Location: $page_path");
			return;
		}

		$type_handle = 'post-reference:markdown_extra:none:none:none';
		$section_subject = t('Reference');
		$section_content = '';
		for ($i = 0; $i < DocSectionManager::MAX_ATTACHED_FILES; $i++) {
			$ref_title = $this->post("refTitle$i");
			$ref_link = $this->post("refLink$i");
			if (strlen ($ref_title) > 0 && strlen ($ref_link) > DocSectionManager::MIN_LINK_STRLEN) {
				$section_content .= "1. [$ref_title]($ref_link)\n";
			}
		}

		if (mb_strlen ($section_content) < self::MIN_CONTENT_LEN) {
			set_page_action_status ($page_id, t('New Reference'), 'error', t('Too short content!'));
			header ("Location: $page_path");
			return;
		}

		$section_manager = new DocSectionManager ();
		$res = $section_manager->addNewSection ($fse_id, $page_id, $area_handle,
			$project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle,
			$type_handle, $section_subject, $section_content, '[]');
		if ($res != DocSectionManager::EC_OK) {
			set_page_action_status ($page_id, t('New Reference'),
				'error', t('Failed to add a new section: %s', $section_manager->getErrorMessage ($res)));
			header ("Location: $page_path");
			return;
		}

		set_page_action_status ($page_id, t('New Reference'), 'success', t('Succeed to add new reference.'));

		ProjectInfo::onChangeThreads ($project_id, $domain_handle, $volume_handle, $part_handle);
		header ("Location: $page_path");
	}

	public function add_new_blog () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$curr_page_id = $this->post('cID');
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_name = $this->post('blogSubject');
		$chapter_desc = $this->post('blogSummary');
		$blog_category = $this->post('blogCategory');
		$blog_tags = $this->post('blogTags');
		$author_suggested = (int)$this->post('authorSuggested');

		$curr_page_path = Page::getByID ($curr_page_id)->getCollectionPath ();

		$form_token_name = $this->post('formTokenName');
		$form_token = $this->post('formToken');
		if ($_SESSION [$form_token_name] != $form_token) {
			set_page_action_status ($curr_page_id, t('Add New Blog'), 'error', t('Bad request or session expired!'));
			header ("Location: $curr_page_path");
			return;
		}
		unset ($_SESSION [$form_token_name]);

		if (!preg_match ("/^.{1,64}$/", $chapter_name)) {
			set_page_action_status ($curr_page_id, t('Add New Blog'), 'error', t('Too short/long blog name!'));
			header ("Location: $curr_page_path");
			return;
		}

		if (!preg_match ("/^.{2,255}$/", $chapter_desc)) {
			set_page_action_status ($curr_page_id, t('Add New Blog'), 'error', t('Too short/long blog summary!'));
			header ("Location: $curr_page_path");
			return;
		}

		$part_page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle);
		$part_page = Page::getByPath ($part_page_path);
		if ($part_page->getCollectionID() == false) {
			set_page_action_status ($curr_page_id, t('Add New Blog'), 'error', t('No parent (blog zone) page!'));
			header ("Location: $curr_page_path");
			return;
		}

		$chapter_handle = hash_hmac ("md5", microtime () . rand (), $chapter_name . $part_handle);
		$chapter_page = ProjectInfo::addChapterPage ($project_id, $domain_handle, $volume_handle, $part_page,
				$chapter_handle, $chapter_name, $chapter_desc, $author_suggested);
		if ($chapter_page == false) {
			set_page_action_status ($curr_page_id, t('Add New Blog'), 'error', t('Failed to add a blog page!'));
			header ("Location: $curr_page_path");
			return false;
		}

		/* store blog tags here */
		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);

		$tag = strtok ($blog_tags, " \n\t");
		while ($tag !== false) {
			$db->Execute ("INSERT IGNORE fsen_chapter_tags_$doc_lang (chapter_handle, tag) VALUES (?, ?)",
						array ($chapter_handle, $tag));
			$tag = strtok(" \n\t");
		}

		if (strlen ($blog_category) >= 2) {
			$db->Execute ('INSERT IGNORE fsen_chapter_categories (chapter_handle, category) VALUES (?, ?)',
						array ($chapter_handle, $blog_category));
		}

		set_page_action_status ($chapter_page->getCollectionID(), t('Add New Blog'), 'success', t('Succeed to add a new blog.'));
		header ('Location: ' . $chapter_page->getCollectionPath ());
	}

	public function edit_blog () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$curr_page_id = $this->post('cID');
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');

		$chapter_name = $this->post('blogSubject');
		$chapter_desc = $this->post('blogSummary');
		$blog_category = $this->post('blogCategory');
		$blog_tags = $this->post('blogTags');
		$author_suggested = (int)$this->post('authorSuggested');

		$curr_page_path = Page::getByID ($curr_page_id)->getCollectionPath ();

		$form_token_name = $this->post('formTokenName');
		$form_token = $this->post('formToken');
		if ($_SESSION [$form_token_name] != $form_token) {
			set_page_action_status ($curr_page_id, t('Edit Blog'), 'error', t('Bad request or session expired!'));
			unset ($_SESSION [$form_token_name]);
			header ("Location: $curr_page_path");
			return;
		}
		unset ($_SESSION [$form_token_name]);

		if (!preg_match ("/^.{1,64}$/", $chapter_name)) {
			set_page_action_status ($curr_page_id, t('Edit Blog'), 'error', t('Too short/long blog name!'));
			header ("Location: $curr_page_path");
			return;
		}

		if (!preg_match ("/^.{2,255}$/", $chapter_desc)) {
			set_page_action_status ($curr_page_id, t('Edit Blog'), 'error', t('Too short/long blog summary!'));
			header ("Location: $curr_page_path");
			return;
		}

		$cpt_page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$chapter_page = Page::getByPath ($cpt_page_path);
		if ($chapter_page->getCollectionID() == false) {
			set_page_action_status ($curr_page_id, t('Edit Blog'), 'error', t('No such blog page!'));
			header ("Location: $curr_page_path");
			return;
		}

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);

		$db->Execute ("UPDATE fsen_project_doc_volume_part_chapters_$doc_lang SET chapter_name=?, chapter_desc=?
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND chapter_handle=?",
				array ($chapter_name, $chapter_desc,
						$project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle));
		$db->Execute ("DELETE FROM fsen_chapter_tags_$doc_lang WHERE chapter_handle=?", array ($chapter_handle));

		$tag = strtok ($blog_tags, " \n\t");
		while ($tag !== false) {
			$db->Execute ("INSERT IGNORE fsen_chapter_tags_$doc_lang (chapter_handle, tag) VALUES (?, ?)",
						array ($chapter_handle, $tag));
			$tag = strtok(" \n\t");
		}

		if (strlen ($blog_category) >= 2) {
			$db->Execute ('INSERT INTO fsen_chapter_categories (chapter_handle, category) VALUES (?, ?)
	ON DUPLICATE KEY UPDATE category=?', array ($chapter_handle, $blog_category, $blog_category));
		}

		$chapter_page->update (array ("cName" => $chapter_name, "cDescription" => $chapter_desc));

		ProjectInfo::onUpdateBlogInfo ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);

		set_page_action_status ($chapter_page->getCollectionID(), t('Edit Blog'), 'success', t('Succeed to edit blog.'));
		header ("Location: $cpt_page_path");
	}

	public function delete_blog () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
			return;
		}

		$curr_page_id = $this->post('cID');
		$project_id = $this->post('projectID');
		$domain_handle = $this->post('domainHandle');
		$volume_handle = $this->post('volumeHandle');
		$part_handle = $this->post('partHandle');
		$chapter_handle = $this->post('chapterHandle');

		$curr_page_path = Page::getByID ($curr_page_id)->getCollectionPath ();

		$form_token_name = $this->post('formTokenName');
		$form_token = $this->post('formToken');
		if ($_SESSION [$form_token_name] != $form_token) {
			set_page_action_status ($curr_page_id, t('Delete Blog'), 'error', t('Bad request or session expired!'));
			unset ($_SESSION [$form_token_name]);
			header ("Location: $curr_page_path");
			return;
		}
		unset ($_SESSION [$form_token_name]);

		$cpt_page_path = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);
		$chapter_page = Page::getByPath ($cpt_page_path);
		if ($chapter_page->getCollectionID() == false) {
			set_page_action_status ($curr_page_id, t('Delete Blog'), 'error', t('No such blog page!'));
			header ("Location: $curr_page_path");
			return;
		}

		$chapter_page->delete ();

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);

		$db->Execute ("DELETE FROM fsen_chapter_tags_$doc_lang WHERE chapter_handle=?", array ($chapter_handle));
		$db->Execute ('DELETE FROM fsen_chapter_categories WHERE chapter_handle=?', array ($chapter_handle));
		$db->Execute ("DELETE FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id=? AND domain_handle=? AND volume_handle=? AND part_handle=? AND chapter_handle=?",
				array ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle));

		ProjectInfo::onDeleteBlog ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle);

		set_page_action_status ($curr_page_id, t('Delete Blog'), 'success', t('Succeed to delete the blog.'));
		header ("Location: $curr_page_path");
	}
}

