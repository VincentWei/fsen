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

class FSEInfo {
	const FSEINFO_CACHE_EXPIRED_TIME = 3600; /* 1 hour */

	static function getNameInfo ($fse_id)
	{
		if (!preg_match ("/^[0-9a-f]{32}$/", $fse_id)) {
			return false;
		}

		$fse_info = Cache::get ('FSENameInfo', $fse_id);
		if ($fse_info == false) {
			$db = Loader::db ();
			$fse_info = $db->getRow ("SELECT user_name, nick_name, def_locale, avatar_file_id, heat_level
	FROM fse_basic_profiles WHERE fse_id=?",
				array ($fse_id));
			if (count ($fse_info) == 0) {
				return false;
			}
			$fse_info['avatar_url'] = get_url_from_file_id ($fse_info['avatar_file_id']);
			Cache::set ('FSENameInfo', $fse_id, $fse_info, self::FSEINFO_CACHE_EXPIRED_TIME);
		}

		return $fse_info;
	}

	static function getPersonalHomeLink ($fse_info = false, $with_tags = false, $style_class = false)
	{
		if ($fse_info == false) {
			$fse_info = $_SESSION['FSEInfo'];
		}

		if ($fse_info == false) {
			if ($with_tags) {
				return '<a href="/" class="' . $style_class . '">' . t('No Name') . '</a>';
			}
			return '/';
		}

		if (preg_match ("/^zh/i", $fse_info['def_locale'])) {
			$doc_lang = 'zh';
		}
		else {
			$doc_lang = 'en';
		}

		$link = "/$doc_lang/engineer/" . $fse_info['user_name'];
		if ($with_tags) {
			return '<a href="' . $link . '" class="' . $style_class . '">' . h5($fse_info['nick_name']) . '</a>';
		}

		return $link;
	}

	static function getBasicProfile ($user_name)
	{
		$fse_info = Cache::get ('FSEBasicProfile', $user_name);
		if ($fse_info == false) {
			$db = Loader::db ();
			$fse_info = $db->getRow ("SELECT * FROM fse_basic_profiles WHERE user_name=?", array ($user_name));

			if (count ($fse_info) == 0) {
				return false;
			}

			$fse_info['avatar_url'] = get_url_from_file_id ($fse_info['avatar_file_id']);
			Cache::set ('FSEBasicProfile', $user_name, $fse_info, self::FSEINFO_CACHE_EXPIRED_TIME);
		}

		return $fse_info;
	}

	static function getPublicProfile ($user_name)
	{
		$fse_info = self::getBasicProfile ($user_name);

		if ($fse_info) {
			unset ($fse_info['fse_id']);
			unset ($fse_info['hashed_passwd']);
			unset ($fse_info['email_box']);
			unset ($fse_info['avatar_file_id']);
		}

		return $fse_info;
	}

	static function onUpdateProfile ($fse_info, $doc_lang)
	{
		Cache::delete ('FSENameInfo', $fse_info['fse_id']);
		Cache::delete ('FSEBasicProfile', $fse_info['user_name']);

		$page = Page::getByPath ("/$doc_lang/engineer/" . $fse_info['user_name']);
		if ($page->getCollectionID() == false) {
			return;
		}

		/* refresh related blocks */
		$blocks = $page->getBlocks ('Side Bar');
		foreach ($blocks as $block) {
			$block->refreshBlockOutputCache ();
		}

		$cache = PageCache::getLibrary();
		$cache->purge ($page);
	}
}

