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

<form id="formCHANGEPASSWORD" method="post" action="/fse_settings/account/change_password">
	<fieldset class="flat">
		<header><h2>
			<?php echo t('Change Password') ?>
		</h2></header>

		<section class="fieldBase">
<?php echo t('Old password') ?>
			<input id="changeOLDPASSWORD" type="password" maxlength="20" />
		</section>
		<section class="right-note">
			<a href="/fse_request_to_reset_password">
				<?php echo t('Forget password?') ?>
			</a>
		</section>

		<section class="fieldBase">
<?php echo t('New password') ?>
			<input id="changePASSWORD" type="password" maxlength="20" />
		</section>
		<section class="description">
<?php echo t('The length of password shoule be between 6 and 20.') ?>
			<span id="changeBADPASSWORD" style="display:none;">
<?php echo t('Empty, too short, or too weak password!') ?>
			</span>
		</section>
		<section class="fieldBase">
<?php echo t('Confirm') ?>
			<input id="changeREPASSWORD" type="password" maxlength="20" />
		</section>
		<section class="description">
			<span id="changeBADREPASSWORD" style="display:none;">
<?php echo t('Password does not matche!') ?>
			</span>
		</section>

		<input id="changeUSERNAME" name="userName" type="hidden" value="@@@" />
		<input id="changeOLDHASHEDPASSWD" name="oldHashedPasswd" type="hidden" value="notset" />
		<input id="changeNEWHASHEDPASSWD" name="newHashedPasswd" type="hidden" value="notset" />

		<section class="fieldBase transparent">
			<input type="submit" value="<?php echo t('Change Password') ?>" id="btnCHANGE" />
		</section>
		<section class="description">
				<span id="changeERRORSYSTEM" style="display:none;">
<?php echo t('System Error!') ?>
				</span>
		</section>
	</fieldset>
</form>

<script type="text/javascript">

function check_password (prefix)
{
	var password = $('#' + prefix + 'PASSWORD').val();
	var matched = password.match (/^[\x20-\x7E]{6,20}$/);
	if (matched == password) {
		$('#' + prefix + 'BADPASSWORD').hide ();
		return password;
	}

	$('#' + prefix + 'BADPASSWORD').show ();
	return "";
}

function check_repassword (prefix)
{
	var password = $('#' + prefix + 'PASSWORD').val();
	var repassword = $('#' + prefix + 'REPASSWORD').val ();

	if (password == repassword) {
		$('#' + prefix + 'BADREPASSWORD').hide ();
		return repassword;
	}

	$('#' + prefix + 'BADREPASSWORD').show ();
	return "";
}

$('#changePASSWORD').blur (function (event) {
	check_password ("change");
});

$('#changeREPASSWORD').blur (function (event) {
	check_repassword ("change");
});

$('#btnCHANGE').click (function (event) {
	event.preventDefault ();

	var user_name = $('#changeUSERNAME').val();
	if (user_name == "@@@") {
		$('#changeERRORSYSTEM').show ();
	}

	var old_password = $('#changeOLDPASSWORD').val();
	var old_hashed_passwd = hex_hmac_md5 (user_name.toLowerCase (), old_password);

	var password = check_password ("change");
	if (password == "") return;
	var repassword = check_repassword ("change");
	if (repassword == "") return;

	var new_hashed_passwd = hex_hmac_md5 (user_name.toLowerCase (), password);
	$('#changeOLDHASHEDPASSWD').val (old_hashed_passwd);
	$('#changeNEWHASHEDPASSWD').val (new_hashed_passwd);

	$('#formCHANGEPASSWORD').submit();
});

$(document).ready (function() {
	get_fse_basic_profile (function () {
		$('#changeUSERNAME').val (fse_basic_profile.fse_info.user_name);
	});
});
</script>

