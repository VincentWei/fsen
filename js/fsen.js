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

		if (obj.status == 'success' && obj.form_id != '') {
			if (supports_local_storage ()) {
				var fields = localStorage.getItem (obj.form_id);
				if (fields != undefined && fields != null && fields != '') {
					fields = fields.split (',');

					var f;
					for (f in fields) {
						var key = obj.form_id + fields[f];
						localStorage.removeItem (key);
					}
				}
			}
		}

		$('#popupStatusBar').removeClass ("fade-away");
		$('#popupStatusBar').addClass ("fade-in");
		setTimeout (function () {
				$('#popupStatusBar').removeClass ("fade-in");
				$('#popupStatusBar').addClass ("fade-away");
			}, 5000);
	}
}

function supports_local_storage ()
{
	return ('localStorage' in window) && (window['localStorage'] != null);
}

function auto_save_form_content () {
	this.form_id = arguments[0];
	this.fields = new Array ();

	var i;
	for (i = 1; i < arguments.length; i++) {
		this.fields[i-1] = ' ' + arguments [i];
	}

	this.init = function () {
		this.restore_form_content ();

		var f;
		for (f in this.fields) {
			var element_id = this.form_id + this.fields[f];
			$(element_id).on ('input', this.on_change);
		}
	}

	this.on_timeout = function () {
		this.store_form_content ();
	}

	this.timer_id = 0;
	this.on_change = function () {
		if (this.timer_id != 0) {
			clearTimeout (this.timer_id);
			this.timer_id = 0;
		}
		this.timer_id = setTimeout (function () {this.on_timeout();}, 3000);
	}

	this.store_form_content = function  () {
		if (supports_local_storage ()) {
			var f;
			for (f in this.fields) {
				var field_name = this.fields[f];
				var key = this.form_id + field_name;
				var value = $(this.form_id + field_name).val ();
				if (value != undefined && value != null && value != '') {
					localStorage.setItem (key, value);
				}
				else {
					localStorage.removeItem (key);
				}
			}
			localStorage.setItem (this.form_id, this.fields);
		}
	}

	this.restore_form_content = function  () {
		if (supports_local_storage ()) {
			var f;
			for (f in this.fields) {
				var field_name = this.fields[f];
				var key = this.form_id + field_name;
				var value = localStorage.getItem (key);
				if (value != undefined && value != null && value != '') {
					$(this.form_id + field_name).val (value);
				}
			}
		}
	}

	this.init();

	return this;
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

