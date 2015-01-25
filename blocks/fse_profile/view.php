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

$uh = Loader::helper('concrete/urls');
$bt = BlockType::getByHandle('fse_register');
?>

<form id="formCHANGEPROFILE" method="post" action="/fse_settings/profile/update">
	<fieldset class="flat">
		<header>
			<h2>
<?php echo t('Public Profile') ?>
			</h2>
		</header>
		<section class="fieldBase">
<?php echo t('Avatar') ?>
	 		<img id="imgAvatar" onclick="chooseAvatar();" src="/files/images/icon-def-avatar-small.png" />
		</section>
		<section class="description">
<?php echo t('Click the image to change your avatar.') ?>
		</section>
		<input id="inputAvatarFileID" name="avatarFileID" type="hidden" value="0" />

		<section class="fieldBase">
<?php echo t('Username') ?>
			<input readonly="true" id="USERNAME" type="text" maxlength="30" name="userName" />
		</section>
		<section class="description">
<?php echo t('You can not change username after registration. If you want to use another username, the only way is signning up a new account.') ?>
		</section>

		<section class="fieldBase">
<?php echo t('Nickname') ?>
			<input id="NICKNAME" type="text" maxlength="30" name="nickName" />
		</section>
		<section class="description">
<?php echo t('Only printable characters, digitals and underlines are allowed.') ?>
			<span id="badNICKNAME" style="display:none;">
<?php echo t('Bad nick name!') ?>
			</span>
		</section>

		<section class="fieldBase">
<?php echo t('Primary Email') ?>
			<input id="EMAIL" type="email" maxlength="128" name="emailBox" />
		</section>
		<section class="description">
<?php echo t('The primary email address will be used for account-related notifications (e.g. account changes and instructions to reset password) as well as comments for your posts.') ?>
			<span id="badEMAIL" style="display:none;">
<?php echo t('Bad or duplicated email address!') ?>
			</span>
		</section>

		<input id="OLDEMAIL" type="hidden" />

		<section class="fieldBase">
<?php echo t('Self Description') ?>
			<input id="SELFDESC" type="text" maxlength="255" name="selfDesc" />
		</section>
		<section class="description">
<?php echo t('A brief description of youself. It will be showed on your personal page.') ?>
		</section>

		<section class="fieldBase">
<?php echo t('Public Email') ?>
			<input id="PUBLICEMAIL" type="email" maxlength="128" name="publicEmail" />
		</section>
		<section class="description">
<?php echo t('This is your public email address. It will be showed on your personal page.') ?>
			<span id="badPUBLICEMAIL" style="display:none;">
<?php echo t('Bad email address!') ?>
			</span>
		</section>

		<section class="fieldBase">
<?php echo t('URL') ?>
			<input id="PUBLICURL" type="url" maxlength="255" name="publicURL" />
		</section>
		<section class="description">
			<span id="badPUBLICURL" style="display:none;">
<?php echo t('Bad URL!') ?>
			</span>
		</section>

		<section class="fieldBase">
<?php echo t('Orgnization') ?>
			<input id="PUBLICORG" type="text" maxlength="255" name="publicORG" />
		</section>

		<section class="fieldBase">
<?php echo t('Country/Region') ?>
			<select id="LOCATIONCOUNTRY">
				<option value="0">--Please choose--</option>
				<option value="1">中国</option>
			</select>
		</section>
		<input id="LOCATIONCOUNTRYWITHCODE" name="locationCountry" type="hidden" />

		<section class="fieldBase">
<?php echo t('State/Province') ?>
			<select id="LOCATIONPROVINCE">
				<option value="0">
<?php echo t('--Please choose--') ?>
				</option>
			</select>
		</section>
		<input id="LOCATIONPROVINCEWITHCODE" name="locationProvince" type="hidden" />

		<section class="fieldBase">
<?php echo t('District/County') ?>
			<select id="LOCATIONDISTRICT">
				<option value="0">
<?php echo t('--Please choose--') ?>
				</option>
			</select>
		</section>
		<input id="LOCATIONDISTRICTWITHCODE" name="locationDistrict" type="hidden" />

		<section class="description">
			<span id="badLOCATION" style="display:none;">
<?php echo t('Please choose your location!') ?>
			</span>
		</section>

		<section class="fieldBase transparent">
			<input id="CHANGEPROFILE" type="submit" value="<?php echo t('Update profile') ?>" />
		</section>
	</fieldset>
</form>

<script type="text/javascript">
function on_change_country ()
{
	var $location_province = $('#LOCATIONPROVINCE');
	$('#LOCATIONPROVINCE option').each (function () {
	       	$(this).remove ();
       	});
       	$('<option value="0"><?php echo t('--Please choose--') ?></option>').appendTo ($location_province);

	var $location_district = $('#LOCATIONDISTRICT');
	$('#LOCATIONDISTRICT option').each (function () {
	       	$(this).remove ();
       	});
       	$('<option value="0">--Please choose--</option>').appendTo ($location_district);

	var division_id = $('#LOCATIONCOUNTRY').val ();
	if (division_id != "0") {
		$location_province.attr ("disabled", "true");
		$.ajaxSetup({async:false});
		$.get ("/index.php/tools/fetch_china_administrative_divisions.php?divisionID=" + division_id,
			function (data) {
				var $location_province = $('#LOCATIONPROVINCE');
				var obj = eval ('(' + data + ')');
				if (obj.status == 'ok') {
					for (var i=0; i < obj.divisions.length; i++) {
						var division = obj.divisions [i];
						var str_html = '<option value="'+division.id+'">'+division.name+'</option>';
       						$(str_html).appendTo ($location_province);
					}
					$location_province.removeAttr ("disabled");
				}
			});
	}
}

$('#LOCATIONCOUNTRY').change (function () {
	on_change_country();
});

function on_change_province ()
{
	var $location_district = $('#LOCATIONDISTRICT');
	$('#LOCATIONDISTRICT option').each (function () {
	       	$(this).remove ();
       	});
       	$('<option value="0"><?php echo ('--Please choose--') ?></option>').appendTo ($location_district);

	var division_id = $('#LOCATIONPROVINCE').val ();
	if (division_id != "0") {
		$location_district.attr ("disabled", "true");
		$.ajaxSetup({async:false});
		$.get ("/index.php/tools/fetch_china_administrative_divisions.php?divisionID=" + division_id,
			function (data) {
				var $location_district = $('#LOCATIONDISTRICT');
				var obj = eval ('(' + data + ')');
				if (obj.status == 'ok') {
					for (var i=0; i < obj.divisions.length; i++) {
						var division = obj.divisions [i];
						var str_html = '<option value="'+division.id+'">'+division.name+'</option>';
       						$(str_html).appendTo ($location_district);
					}
					$location_district.removeAttr ("disabled");
				}
			});
	}
}

$('#LOCATIONPROVINCE').change (function () {
	on_change_province ();
});

function check_nickname ()
{
	var nickname = $('#NICKNAME').val();
	var matched = nickname.match (/^[\u4E00-\u9FA5\uf900-\ufa2d\w]{2,16}$/)
	if (matched == nickname) {
		$("#badNICKNAME").hide ();
		return nickname;
	}

	$("#badNICKNAME").show ();
	return "";
}

$('#NICKNAME').blur (function (event) {
	check_nickname ();
});

function check_email (prefix, check_db)
{
	var email = $('#' + prefix + 'EMAIL').val();
	if (email == "")
		return email;

	var old_email = $('#OLDEMAIL').val ();
	if (old_email == email)
		return email;

	email_pattern = /^[\w-]+([.+][\w-]+)*@[\w-]+(\.[\w-]+)+$/;
	if (email_pattern.test (email)) {
		if (check_db) {
			$.ajaxSetup({async:false});
			$.post ("<?php echo $uh->getBlockTypeToolsURL ($bt) ?>/check_email.php",
				{email: email},
				function (data) {
					if (data.search (/none/) >= 0) {
						$('#' + prefix + 'badEMAIL').hide ();
					}
					else {
						$('#' + prefix + 'badEMAIL').show ();
					}
				});

			if ($('#' + prefix + 'badEMAIL').is (":visible")) {
				return "";
			}
			else
				return email;
		}
		else {
			$('#' + prefix + 'badEMAIL').hide ();
			return email;
		}
	}

	$('#' + prefix + 'badEMAIL').show ();
	return "";
}

$('#EMAIL').blur (function (event) {
	check_email ("", true);
});

function check_public_email (prefix)
{
	var email = $('#' + prefix + 'PUBLICEMAIL').val();
	if (email == "")
		return '';

	email_pattern = /^[\w-]+([.+][\w-]+)*@[\w-]+(\.[\w-]+)+$/;
	if (email_pattern.test (email)) {
		return email;
	}

	return '@@@';
}

$('#PUBLICEMAIL').blur (function (event) {
	if (check_public_email ("") == '@@@') {
		$("#badPUBLICEMAIL").show ();
	}
	else {
		$("#badPUBLICEMAIL").hide ();
	}
});

function check_public_url (prefix)
{
	var url = $('#' + prefix + 'PUBLICURL').val();
	if (url == "")
		return '';

	url_pattern = /^[a-zA-z]+:\/\/[^\s]*$/;
	if (url_pattern.test (url)) {
		return url;
	}

	return '@@@';
}

$('#PUBLICURL').blur (function (event) {
	if (check_public_url ("") == '@@@') {
		$("#badPUBLICURL").show ();
	}
	else {
		$("#badPUBLICURL").hide ();
	}
});

$('#CHANGEPROFILE').click (function (event) {
	event.preventDefault ();

	var nick_name = check_nickname ();
	if (nick_name == "") return;

	var email_box = check_email ("", true);
	if (email_box == "") return;

	if (check_public_email ("") == '@@@') {
		$("#badPUBLICEMAIL").show ();
		return;
	}
	else {
		$("#badPUBLICEMAIL").hide ();
	}

	if (check_public_url ("") == '@@@') {
		$("#badPUBLICURL").show ();
		return;
	}
	else {
		$("#badPUBLICURL").hide ();
	}

	var location_country = $('#LOCATIONCOUNTRY').val();
	if (location_country == "0") {
		$("#badLOCATION").show ();
		return;
	}
	location_country = location_country + ":" + $('#LOCATIONCOUNTRY').find ('option:selected').text ();
	$("#LOCATIONCOUNTRYWITHCODE").val (location_country);

	var location_province = $('#LOCATIONPROVINCE').val();
	if (location_province == "0") {
		$("#badLOCATION").show ();
		return;
	}
	location_province = location_province + ":" + $('#LOCATIONPROVINCE').find ('option:selected').text ();
	$("#LOCATIONPROVINCEWITHCODE").val (location_province);

	var location_district = $('#LOCATIONDISTRICT').val();
	if (location_district == "0") {
		$("#badLOCATION").show ();
		return;
	}
	location_district = location_district + ":" + $('#LOCATIONDISTRICT').find ('option:selected').text ();
	$("#LOCATIONDISTRICTWITHCODE").val (location_district);
	$("#badLOCATION").hide ();

	$('#formCHANGEPROFILE').submit ();
});

function chooseAvatar () {
	ccm_chooseAsset = function (obj) {
		if (obj.fID != undefined) {
			$('#inputAvatarFileID').val ('' + obj.fID);
			$('#imgAvatar').attr ('src', obj.thumbnailLevel1);
		}
	};

    /* resolve the conflict between jQuery UI and Bootstrap */
    var bootstrapButton = $.fn.button.noConflict();
    $.fn.bootstrapBtn = bootstrapButton;
	ccm_launchFileManager ('&fType=' + ccmi18n_filemanager.FTYPE_IMAGE);
}

$(document).ready (function() {
	/* TODO: fill LOCATIONCOUNTRY options here */

	get_fse_basic_profile (function () {
		$('#imgAvatar').attr ('src', fse_basic_profile.fse_info.small_avatar_url);
		$('#inputAvatarFileID').val (fse_basic_profile.fse_info.avatar_file_id);

		$('#USERNAME').val (fse_basic_profile.fse_info.user_name);
		$('#NICKNAME').val (fse_basic_profile.fse_info.nick_name);
		$('#EMAIL').val (fse_basic_profile.fse_info.email_box);
		$('#OLDEMAIL').val (fse_basic_profile.fse_info.email_box);

		$('#SELFDESC').val (fse_basic_profile.fse_info.self_desc);
		$('#PUBLICEMAIL').val (fse_basic_profile.fse_info.public_email);
		$('#PUBLICURL').val (fse_basic_profile.fse_info.public_url);
		$('#PUBLICORG').val (fse_basic_profile.fse_info.public_org);

		var location_code = fse_basic_profile.fse_info.location_country.split (":")[0];
		$('#LOCATIONCOUNTRY').val(location_code);
		on_change_country();

		location_code = fse_basic_profile.fse_info.location_province.split (":")[0];
		$('#LOCATIONPROVINCE').val(location_code);
		on_change_province();

		location_code = fse_basic_profile.fse_info.location_district.split (":")[0];
		$('#LOCATIONDISTRICT').val(location_code);
	});
});
</script>

