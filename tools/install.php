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

#exit (0);

require_once ('helpers/fsen/ProjectInfo.php');

Loader::model ('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

#exit (0);

Loader::model('single_page');
Loader::model('job');

$pkg = null;

// install themes
PageTheme::add ('full_stack_style', $pkg);
PageTheme::add ('full_stack_style_mobile', $pkg);

// install jobs
$jb = Job::installByPackage('stat_user_activities', $pkg);

// install blocks
$bts = array (
	'document_section',
	'fse_app_key',
	'fse_change_password',
	'fse_delete_account',
	'fse_email_settings',
	'fse_login',
	'fse_profile',
	'fse_projects',
	'fse_public_profile',
	'fse_register',
	'fse_request_to_reset_password',
	'fse_reset_password',
	'project_banner',
);

echo 'Installing block types... <br/>';
foreach ($bts as $bt) {
	echo '	installing ' . $bt . ': ';
	BlockType::installBlockTypeFromPackage($bt, $pkg);
	echo 'done' . '<br/>';
	flush();
	ob_flush();
}

// install page types
$page_type_handles = array (
	array ('ctHandle' => 'home', 'ctName' => 'HOME'),
	array ('ctHandle' => 'localized_home', 'ctName' => 'Localized Home'),
	array ('ctHandle' => 'localized_projects', 'ctName' => 'Localized Projects'),
	array ('ctHandle' => 'localized_engineers', 'ctName' => 'Localized Engineers'),
	array ('ctHandle' => 'personal_homepage', 'ctName' => 'Personal Home'),
	array ('ctHandle' => 'project_arena_homepage', 'ctName' => 'Arena Home'),
	array ('ctHandle' => 'project_arena', 'ctName' => 'Arena'),
	array ('ctHandle' => 'project_arena_subject', 'ctName' => 'Arena Subject'),
	array ('ctHandle' => 'project_arena_zone', 'ctName' => 'Arena Zone'),
	array ('ctHandle' => 'project_comm_forum_area', 'ctName' => 'Forum Area'),
	array ('ctHandle' => 'project_comm_forum', 'ctName' => 'Forum'),
	array ('ctHandle' => 'project_comm_homepage', 'ctName' => 'Community Home'),
	array ('ctHandle' => 'project_comm_thread', 'ctName' => 'Forum Thread'),
	array ('ctHandle' => 'project_contribute_default', 'ctName' => 'Default for Contribute'),
	array ('ctHandle' => 'project_contribute_homepage', 'ctName' => 'Contribute Home'),
	array ('ctHandle' => 'project_doc_homepage', 'ctName' => 'Document Home'),
	array ('ctHandle' => 'project_doc_volume', 'ctName' => 'Document Volume'),
	array ('ctHandle' => 'project_doc_part', 'ctName' => 'Documnet Part'),
	array ('ctHandle' => 'project_doc_chapter', 'ctName' => 'Chapter'),
	array ('ctHandle' => 'project_doc_all_blogs', 'ctName' => 'All Blogs'),
	array ('ctHandle' => 'project_doc_fse_blogs', 'ctName' => 'Blog Zone'),
	array ('ctHandle' => 'project_doc_blog', 'ctName' => 'Blog'),
	array ('ctHandle' => 'project_download_default', 'ctName' => 'Default for Download'),
	array ('ctHandle' => 'project_download_homepage', 'ctName' => 'Download Home'),
	array ('ctHandle' => 'project_homepage', 'ctName' => 'Project Home'),
	array ('ctHandle' => 'project_misc_homepage', 'ctName' => 'About'),
	array ('ctHandle' => 'project_misc_faqs', 'ctName' => 'FAQs'),
	array ('ctHandle' => 'project_misc_default', 'ctName' => 'Default for Misc'),
);

$nocache_pths = array (
	'project_arena_homepage',
	'project_arena',
	'project_arena_subject',
	'project_arena_zone',
	'project_comm_forum_area',
	'project_comm_forum',
	'project_comm_homepage',
	'project_comm_thread',
);

$short_cache_pths = array (
	'localized_home',
	'personal_homepage',
);

echo 'Installing page types... <br/>';
foreach ($page_type_handles as $pth) {
	echo '	installing ' . $pth['ctHandle'] . ': ';
	$page_type = CollectionType::getByHandle ($pth['ctHandle']);
	if (!($page_type instanceof CollectionType)) {
		$page_type = CollectionType::add ($pth, $pkg);
		echo 'installed. <br/>';
	}
	else {
		echo 'exisited. <br/>';
	}

	$masterCID = $page_type->getMasterCollectionID();
	$masterCollection = Page::getByID($masterCID);
	if (in_array ($pth['ctHandle'], $nocache_pths)) {
		// disable full page cache for specific page types
		$masterCollection->update (array ('cCacheFullPageContent' => 0));
	}
	else if (in_array ($pth['ctHandle'], $short_cache_pths)) {
		// set full page cache for specific page types
		$masterCollection->update (array (
				'cCacheFullPageContent' => 1,
				'cCacheFullPageContentOverrideLifetime' => 'custom',
				'cCacheFullPageContentLifetimeCustom' => 10,
				));
	}
	else {
		// follow system default for other page types
		$masterCollection->update (array (
				'cCacheFullPageContent' => -1,
				'cCacheFullPageContentOverrideLifetime' => '0',
				'cCacheFullPageContentLifetimeCustom' => 0,
				));
	}

	flush();
	ob_flush();
}

// Install event extends here
Events::extend('on_start', 'FSENLocalization', 'setupInterfaceLocalization4Request', 'models/fsen_localization.php');
Events::extend('on_before_render', 'FSENLocalization', 'setupInterfaceLocalization4Page', 'models/fsen_localization.php');

$home_page = Page::getByID (HOME_CID);
// disable full page cache for HOME page
// $home_page->update (array ('cCacheFullPageContent' => 0));

// create <lang>/project, <lang>/engineer pages
$languages = array (
	array (
		'home_handle' => 'en', 'home_name' => 'FSEN', 'home_desc' => 'English HOME',
		'projects_handle' => 'project', 'projects_name' => 'Projects', 'projects_desc' => 'Projects',
		'engineers_handle' => 'engineer', 'engineers_name' => 'Engineers', 'engineers_desc' => 'Engineers',
		),
	array (
		'home_handle' => 'zh', 'home_name' => '首页', 'home_desc' => '中文版首页',
		'projects_handle' => 'project', 'projects_name' => '项目', 'projects_desc' => '项目',
		'engineers_handle' => 'engineer', 'engineers_name' => '工程师', 'engineers_desc' => '工程师',
		),
);

echo 'Creating system pages... <br/>';
foreach ($languages as $lang) {
	echo 'Creating localized home page for '; echo $lang['handle']; echo '... ';

	$page_type = CollectionType::getByHandle ('localized_home');
	if (!($page_type instanceof CollectionType)) {
		echo 'Error: failed to get page type for localized home page.';
		exit (0);
	}

	$localized_home_page = Page::getByPath ('/' . $lang['home_handle']);
	if ($localized_home_page->getCollectionID() == false) {
		$localized_home_page = $home_page->add ($page_type, array (
				"cHandle" => $lang['home_handle'],
				"cName" => $lang['home_name'],
				"cDescription" => $lang['home_desc']));
		if ($localized_home_page->getCollectionID() == false) {
			echo 'Error: failed to crate localized home page. <br/>';
			exit (0);
		}
	}

	echo 'Done <br/>';
	flush();
	ob_flush();

	echo 'Creating localized projects page for '; echo $lang['home_handle']; echo '... ';
	$page_type = CollectionType::getByHandle ('localized_projects');
	if (!($page_type instanceof CollectionType)) {
		echo 'Error: failed to get page type for localized projects page.';
		exit (0);
	}

	$localized_projects_page = Page::getByPath ('/' . $lang['home_handle'] . '/project');
	if ($localized_projects_page->getCollectionID() == false) {
		$localized_projects_page = $localized_home_page->add ($page_type, array (
				"cHandle" => $lang['projects_handle'],
				"cName" => $lang['projects_name'],
				"cDescription" => $lang['projects_desc']));
		if ($localized_projects_page->getCollectionID() == false) {
			echo 'Error: failed to crate localized projects page. <br/>';
			exit (0);
		}
	}

	echo 'Creating localized engineers page for '; echo $lang['home_handle']; echo '... ';
	$page_type = CollectionType::getByHandle ('localized_engineers');
	if (!($page_type instanceof CollectionType)) {
		echo 'Error: failed to get page type for localized engineers page.';
		exit (0);
	}

	$localized_engineers_page = Page::getByPath ('/' . $lang['home_handle'] . '/engineer');
	if ($localized_engineers_page->getCollectionID() == false) {
		$localized_engineers_page = $localized_home_page->add ($page_type, array (
				"cHandle" => $lang['engineers_handle'],
				"cName" => $lang['engineers_name'],
				"cDescription" => $lang['engineers_desc']));
		if ($localized_engineers_page->getCollectionID() == false) {
			echo 'Error: failed to crate localized engineers page. <br/>';
			exit (0);
		}
	}

	echo 'Done <br/>';
	flush();
	ob_flush();

	// create system pages
	$doc_lang = $lang['home_handle'];
	$project_id = 'sys-' . $doc_lang;

	echo "Creating document and community pages for $doc_lang ... <br />";

	$db = Loader::db ();
	$domains = $db->getAll ("SELECT * FROM fsen_project_doc_domains
	WHERE project_id=? AND domain_handle != 'home'", array ($project_id));

	foreach ($domains as $d) {
		$domain_page = ProjectInfo::addDomainPage ($project_id, $localized_projects_page,
				$d['domain_handle'], $d['domain_name'], $d['domain_desc']);
		echo '	Added domain page for '; echo $d['domain_handle']; echo '<br/>';
		flush();
		ob_flush();

		$volumes = $db->getAll ("SELECT * FROM fsen_project_doc_volumes
	WHERE project_id=? AND domain_handle=? ORDER BY display_order", array ($project_id, $d['domain_handle']));
		foreach ($volumes as $v) {
			$volume_page = ProjectInfo::addVolumePage ($project_id, $domain_page, $d['domain_handle'],
					$v['volume_handle'], $v['volume_name'], $v['volume_desc']);
			echo '		Added volume page for '; echo $v['volume_handle']; echo '<br/>';
			flush();
			ob_flush();

			$parts = $db->getAll ("SELECT * FROM fsen_project_doc_volume_parts
WHERE project_id=? AND domain_handle=? AND volume_handle=? ORDER BY display_order",
				array ($project_id, $d['domain_handle'], $v['volume_handle']));
			foreach ($parts as $p) {
				$part_page = ProjectInfo::addPartPage ($project_id, $d['domain_handle'], $volume_page,
						$p['part_handle'], $p['part_name'], $p['part_desc']);
				echo '			Add part page for '; echo $p['part_handle']; echo '<br/>';
				flush();
				ob_flush();
			}
		}
	}
}

// install single pages
$single_pths = array (
		array ('cHandle' => 'fse_login', 'cName' => 'Sign in', 'cDescription' => 'Sign in',
			'blocks' => array (array ('areaHandle' => 'Main', 'btHandle' => 'fse_login'))),
		array ('cHandle' => 'fse_logout', 'cName' => 'Sign out', 'cDescription' => 'Sign out'),
		array ('cHandle' => 'fse_register', 'cName' => 'Sign up', 'cDescription' => 'Sign up',
			'blocks' => array (array ('areaHandle' => 'Main', 'btHandle' => 'fse_register'))),
		array ('cHandle' => 'fse_request_to_reset_password', 'cName' => 'Request to Reset Password', 'cDescription' => 'Request to reset password via your primary email',
			'blocks' => array (array ('areaHandle' => 'Main', 'btHandle' => 'fse_request_to_reset_password'))),
		array ('cHandle' => 'fse_reset_password','cName' => 'Reset Password', 'cDescription' => 'Reset password',
			'blocks' => array (array ('areaHandle' => 'Main', 'btHandle' => 'fse_reset_password'))),
		array ('cHandle' => 'fse_validate_email', 'cName' => 'Validate Email', 'cDescription' => 'Validate your primary email'),
		array ('cHandle' => 'fse_settings', 'cName' => 'Personal Settings', 'cDescription' => 'Your personal settings'),
		array ('cHandle' => 'fse_settings/profile', 'cName' => 'Profile', 'cDescription' => 'Your profile',
			'blocks' => array (array ('areaHandle' => 'Main', 'btHandle' => 'fse_profile'))),
		array ('cHandle' => 'fse_settings/account', 'cName' => 'Account', 'cDescription' => 'Account settings',
			'blocks' => array (
				array ('areaHandle' => 'Main', 'btHandle' => 'fse_change_password'),
				array ('areaHandle' => 'Main', 'btHandle' => 'fse_email_settings'),
				array ('areaHandle' => 'Main', 'btHandle' => 'fse_delete_account'),
			)),
		array ('cHandle' => 'fse_settings/applications', 'cName' => 'Applications', 'cDescription' => 'Your application keys',
			'blocks' => array (array ('areaHandle' => 'Main', 'btHandle' => 'fse_app_keys'))),
		array ('cHandle' => 'fse_settings/projects', 'cName' => 'Projects', 'cDescription' => 'Your projects',
			'blocks' => array (array ('areaHandle' => 'Main', 'btHandle' => 'fse_projects'))),
	);

echo 'Creating single pages... <br/>';
foreach ($single_pages as $sp) {
	echo 'Created single page for ' . $sp['cHandle'] . '...';
	$p = SinglePage::getByHandle ($sp['cHandle']);
	if ($p->getCollectionID() > 0) {
		echo 'Already installed.<br/>';
		flush();
		ob_flush();
		continue;
	}

	$p = SinglePage::add ($sp['cHandle'], $pkg);
	if (is_object ($p)) {
		$p->update (array('cName' => $sp['cName'], 'cDescription' => $sp['cDescription']));

		// add block to single pages
		if (count ($sp['blocks']) > 0) {
			foreach ($sp['blocks'] as $b) {
				$block_type = BlockType::getByHandle ($b['btHandle']);
				$area = new Area($b['areaHandle']);
				$p->addBlock ($block_type, $area, array ("strTitle" => ''));
		}
	}

	echo 'Done. <br/>';
	flush();
	ob_flush();
}

Cache::flush();

exit (0);

#### rebuild blog home page for existed users.

echo 'Creating blog home pages for FSEs... <br/>';
flush();
ob_flush();

$en_blogs_page = Page::getByPath ('/en/blog');
$zh_blogs_page = Page::getByPath ('/zh/blog');
$fses = $db->getAll ("SELECT * FROM fse_basic_profiles ORDER BY create_time");
$display_order = 100;
foreach ($fses as $fse) {
	if ($fse['def_locale'] == 'zh_CN') {
		$doc_lang = 'zh';
		$all_blogs_page = $zh_blogs_page;
		$page_desc = $fse['nick_name'] . '的博客';
	}
	else {
		$doc_lang = 'en';
		$all_blogs_page = $en_blogs_page;
		$page_desc = $fse['nick_name'] . '\'s Blogs';
	}

	$sys_project_id = SYSTEM_PROJECT_SHORTNAME . '-' . $doc_lang;

	$db->Execute ("INSERT IGNORE fsen_project_doc_volume_parts
    (project_id, domain_handle, volume_handle, part_handle, part_name, part_desc, required, display_order)
VALUES (?, 'document', 'blog', ?, ?, ?, 1, ?)",
			array ($sys_project_id, $fse['user_name'], $fse['nick_name'], $page_desc, $display_order));
	ProjectInfo::addPartPage ($sys_project_id, 'document', $all_blogs_page,
		$fse['user_name'], $fse['nick_name'], $page_desc);

	echo '	Add blog home page for ' . $fse['user_name'] . '<br/>';
	flush();
	ob_flush();

	$display_order += 1;
}

Cache::flush();

echo 'ok and cache flushed <br/>';

exit (0);
