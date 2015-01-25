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

function after_get_login_status (obj, is_mobile)
{
	if (is_mobile) {
		if (obj.status != 'ok') {
			$('.visible-on-logged-in').hide();
			$('.visible-for-specific-user').hide();
		}
		else {
			$('.enable-on-logged-in').removeAttr("disabled");
			$('.hidden-on-logged-in').hide();
		}
	}
	else {
		if (obj.status != 'ok') {
			$('.visible-on-logged-in').hide();
			$('.visible-for-specific-user').hide();
		}
		else {
			if (obj.project_rights [0] == 't') {
				$('.visible-on-manage-member-right').show();
			}
			if (obj.project_rights [1] == 't') {
				$('.visible-on-edit-document-right').show();
			}
			if (obj.project_rights [2] == 't') {
				$('.visible-on-manage-community-right').show();
			}
			$('.visible-on-logged-in').show();
			$('.enable-on-logged-in').removeAttr("disabled");
			$('.hidden-on-logged-in').hide();

			$('.visible-for-specific-user.user-' + obj.user_name).show();
		}
	}
}

function get_fse_basic_profile (on_ok)
{
	if (typeof (fse_basic_profile) == 'undefined') {
		$.get ("/fse_settings/profile/get_public_profile", function (data) {
			fse_basic_profile = eval ('(' + data + ')');
			if (fse_basic_profile.status == 'ok') {
				on_ok ();
			}
		});
	}
	else if (fse_basic_profile.status == 'ok') {
		on_ok ();
	}
};

function get_other_public_profile (user_name, on_ok)
{
	$.get ("/fse_settings/profile/get_public_profile/" + user_name, function (data) {
			var profile = eval ('(' + data + ')');
			if (profile.status == 'ok') {
				on_ok (profile.fse_info);
			}
		});
};

function get_user_roles_and_rights_on_project (project_id, user_name, on_ok, on_bad)
{
	$.get ("/fse_settings/projects/get_user_roles_and_rights/" + project_id + '/' + user_name,
		function (data) {
			var ret_info = eval ('(' + data + ')');
			if (ret_info.status == 'ok') {
				on_ok (ret_info);
			}
			else {
				on_bad (ret_info);
			}
		});
};

function display_pas (data)
{
	var obj = eval ('(' + data + ')');
	if (obj.time != 0) {
		$('#popupStatusBarSubject').text (obj.action);
		$('#popupStatusBarDesc').text (obj.message);
		if (obj.status == 'error') {
			$('#popupStatusBar>div').removeClass ('alert-none');
			$('#popupStatusBar>div').addClass ('alert-danger');
		}
		else {
			$('#popupStatusBar>div').removeClass ('alert-none');
			$('#popupStatusBar>div').addClass ('alert-' + obj.status);
		}
		$('#popupStatusBar').removeClass ("fade-away");
		$('#popupStatusBar').addClass ("fade-in");
		setTimeout (function () {
				$('#popupStatusBar').removeClass ("fade-in");
				$('#popupStatusBar').addClass ("fade-away");
			}, 5000);
	}
}

$(document).ready (function() {
	$('.switch').click (function (event) {
		if ($(this).hasClass ("on")) {
			$(this).removeClass ("on");
			$(this).addClass ("off");

			var $checkbox = $(this).children ('input');
			$checkbox.attr ("checked", false);
			$checkbox.attr ("value", "off");
		}
		else {
			$(this).removeClass ("off");
			$(this).addClass ("on");

			var $checkbox = $(this).children ('input');
			$checkbox.attr ("checked", true);
			$checkbox.attr ("value", "on");
		}
	});

	$('#btnToggleGlobalDropdown').click (function (event) {
		if ($('#ulGlobalNavList').is(':visible')) {
			$('#ulGlobalNavList').hide ();
		}
		else {
			$('#ulGlobalNavList').show ();
		}
	});
});

