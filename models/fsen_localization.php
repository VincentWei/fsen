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

class FSENLocalization {

	static private $mLang2LocaleMap = array ('zh' => 'zh_CN', 'en' => 'en_US');

	static function getSessionDefaultLocale () {
		if (isset ($_REQUEST['fsenDocLang'])) {
			$doc_lang = $_REQUEST['fsenDocLang'];
			$locale = self::$mLang2LocaleMap [$doc_lang];
			if (strlen ($locale)) {
				return $locale;
			}
		}

		if (isset ($_SESSION['FSEInfo'])) {
			$locale = $_SESSION['FSEInfo']['def_locale'];
			if (strlen ($locale))
				return $locale;
		}

		// they have a language in a certain session going already
		if (isset($_SESSION['DEFAULT_LOCALE'])) {
			return $_SESSION['DEFAULT_LOCALE'];
		}

		// if they've specified their own default locale to remember
		if(isset($_COOKIE['DEFAULT_LOCALE'])) {
			return $_COOKIE['DEFAULT_LOCALE'];
		}

		Loader::library('3rdparty/Zend/Locale');
		$locale = new Zend_Locale();

		return (string)$locale;
	}

	static function changeLocale ($locale) {
		// change core language to translate e.g. core blocks/themes
		if (strlen($locale)) {
			Localization::changeLocale ($locale);

			// site translations
			$loc = Localization::getInstance();
			$loc->addSiteInterfaceLanguage($locale);

			// add package translations
			$pl = PackageList::get();
			$installed = $pl->getPackages();
			foreach($installed as $pkg) {
				if($pkg instanceof Package) {
					$pkg->setupPackageLocalization($locale);
				}
			}
		}
	}

	/* call this function for ajax calls */
	static function setupInterfaceLocalization4AjaxRequest () {
		$locale = self::getSessionDefaultLocale ();
		self::changeLocale ($locale);
	}

	/* this function acts as an event hook of on_start */
	static function setupInterfaceLocalization4Request () {
		if (!defined('SYSTEM_PROJECT_SHORTNAME')) {
			define('SYSTEM_PROJECT_SHORTNAME', 'sys');
		}

		$page = Page::getCurrentPage ();
		if ($page->cID == HOME_CID) {
			return;
		}

		$page_path = $page->getCollectionPath ();
		$path_frags = explode ('/', trim ($page_path, '/'));
		$doc_lang = $path_frags[0];

		switch ($doc_lang) {
			case 'en':
				$locale = 'en_US';
				break;
			case 'zh':
				$locale = 'zh_CN';
				break;
			default:
				$locale = self::getSessionDefaultLocale ();
				if (strncasecmp ($locale, 'zh', 2) == 0) {
					$doc_lang = 'zh';
				}
				else {
					$doc_lang = 'en';
				}
				break;
		}

		$_REQUEST['fsenDocLang'] = $doc_lang;
		self::changeLocale ($locale);
	}

	/* this function acts as an event hook of on_before_render */
	static function setupInterfaceLocalization4Page ($view) {

		if ($view->c instanceof Page && $view->c->getCollectionID() != HOME_CID) {
			/* set fsenDocLang key for the current view */
			$view->controller->set ('fsenDocLang', $_REQUEST['fsenDocLang']);
		}
	}
}

