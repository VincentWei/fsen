<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class HomePageTypeController extends Controller {

	/* redirect to the correct language home page */
	public function on_start () {

		Loader::model ('fsen_localization');
		$locale = FSENLocalization::getSessionDefaultLocale ();
		if (strncasecmp ($locale, 'zh_', 3) == 0) {
			header ('Location: /zh');
		}
		else {
			header ('Location: /en');
		}
	}
}

