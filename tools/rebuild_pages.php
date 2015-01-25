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

require_once ('helpers/fsen/ProjectInfo.php');

$short_cache_pths = array (
	'localized_home',
	'personal_homepage',
);

echo 'Set default cache settings for some page types... <br/>';
foreach ($short_cache_pths as $pth) {
	echo "=> Setting: $pth...";
	$page_type = CollectionType::getByHandle ($pth);
	if (!($page_type instanceof CollectionType)) {
		echo "No such page type: $pth. <br/>";
		continue;
	}

	$masterCID = $page_type->getMasterCollectionID();
	$masterCollection = Page::getByID($masterCID);
	$masterCollection->update (array (
			'cCacheFullPageContent' => 1,
			'cCacheFullPageContentOverrideLifetime' => 'custom',
			'cCacheFullPageContentLifetimeCustom' => 10,
		));

	echo "Done. <br/>";
	flush();
	ob_flush();
}

echo 'Creating blog home pages for FSEs and reset the cache settings for personal homepages... <br/>';

$en_blogs_page = Page::getByPath ('/en/blog');
$zh_blogs_page = Page::getByPath ('/zh/blog');

$db = Loader::db();
$fses = $db->getAll ("SELECT * FROM fse_basic_profiles ORDER BY create_time");
$display_order = 100;

foreach ($fses as $fse) {
	echo '=> Adding blog home page for ' . $fse['user_name'] . '...';
	if ($fse['def_locale'] == 'zh_CN') {
		$doc_lang = 'zh';
		$all_blogs_page = $zh_blogs_page;
		$page_desc = $fse['nick_name'] . '的博客';
	}
	else {
		$doc_lang = 'en';
		$all_blogs_page = $en_blogs_page;
		$page_desc = 'Blogs of ' . $fse['nick_name'];
	}

	$sys_project_id = SYSTEM_PROJECT_SHORTNAME . '-' . $doc_lang;

	$db->Execute ("INSERT IGNORE fsen_project_doc_volume_parts
    (project_id, domain_handle, volume_handle, part_handle, part_name, part_desc, required, display_order)
VALUES (?, 'document', 'blog', ?, ?, ?, 1, ?)",
			array ($sys_project_id, $fse['user_name'], $fse['nick_name'], $page_desc, $display_order));

	$page = ProjectInfo::getProjectPage ($sys_project_id, 'document', 'blog', $fse['user_name']);
	if ($page == false) {
		$page = ProjectInfo::addPartPage ($sys_project_id, 'document', $all_blogs_page, $fse['user_name'],
			$fse['nick_name'], $page_desc);
		echo 'Created.<br/>';
	}
	else {
		echo 'Existed.<br/>';
	}

	flush();
	ob_flush();

	$display_order += 1;

	// use system default for blog page
	$page->update (array (
			'cCacheFullPageContent' => -1,
			'cCacheFullPageContentOverrideLifetime' => '0',
			'cCacheFullPageContentLifetimeCustom' => 0,
		));

	$page = Page::getByPath ("/$doc_lang/engineer/" . $fse['user_name']);
	// short cache for the personal page.
	$page->update (array (
			'cCacheFullPageContent' => 1,
			'cCacheFullPageContentOverrideLifetime' => 'custom',
			'cCacheFullPageContentLifetimeCustom' => 10,
		));
}

Cache::flush();


