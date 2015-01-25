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
defined('C5_EXECUTE') or die("Access Denied.");

$uh = Loader::helper('concrete/urls');
$bt = BlockType::getByHandle('fse_register');
?>

<form id="formVERIFYEMAIL" style="display:none;" method="post" action="/fse_settings/account/verify_email">
	<input name="primaryEmail" type="hidden" value="@@@" />
</form>

<form id="formCHANGEEMAILSETTINGS" method="post" action="/fse_settings/account/change_email_settings">
	<fieldset class="flat">
		<header>
			<h2>
				<?php echo t('Email Settings') ?>
			</h2>
		</header>
		<section class="note">
			<p>
				<?php echo t('Your primary email address (<strong id="emailPRIMARY"></strong>) will be used for account-related notifications (e.g. account changes and instructions to reset password) as well as comment notifications for your posts. Note that only the users whose primary email addresses have been verified can post content to this site.') ?>
			</p>
		</section>

		<section id="emailNOTVERIFIED" class="right-note" style="display:none;">
			<?php echo t('Your primary email address is not verified. If you did not get the verifying email, please click to <a id="emailVERIFY" href="#">send again</a>.') ?>
		</section>

		<hr />

		<input id="emailUSERNAME" name="userName" type="hidden" value="@@@" />

		<section class="fieldBase">
			<?php echo t('Keep private') ?>
			<div class="switch off" id="emailKEEPPRIVATE">
				<span class="thumb"></span>
				<input type="checkbox" name="keepEmailPrivate" value="off" />
			</div>
		</section>
		<section class="description">
			<?php echo t('We will use <strong id="emailNOREPLY"></strong> when sending emails on your behalf.') ?>
		</section>
		<section class="fieldBase transparent">
			<input type="submit" value="<?php echo t('Submit') ?>" id="btnChangeEmail" />
		</section>
		<section class="description">
			<span id="emailERRORSYSTEM" style="display:none;">
				<?php echo t('System Error!') ?>
			</span>
		</section>
	</fieldset>
</form>

<script type="text/javascript">

$('#emailVERIFY').click (function (event) {
	event.preventDefault ();

	var user_name = $('#emailUSERNAME').val();
	if (user_name == "@@@") {
		$('#emailERRORSYSTEM').show ();
	}

	$('#formVERIFYEMAIL').submit();
});

$('#btnChangeEmail').click (function (event) {
	event.preventDefault ();

	var user_name = $('#emailUSERNAME').val();
	if (user_name == "@@@") {
		$('#emailERRORSYSTEM').show ();
	}

	$('#formCHANGEEMAILSETTINGS').submit();
});

$(document).ready (function() {
	get_fse_basic_profile (function () {
		$('#emailUSERNAME').val (fse_basic_profile.fse_info.user_name);
		$('#emailPRIMARY').text (fse_basic_profile.fse_info.email_box);
		$('#emailNOREPLY').text ("daemon@fullstackengineer.net");

		if (fse_basic_profile.fse_info.email_verified == 0) {
			$('#emailNOTVERIFIED').show ();
		}
		if (fse_basic_profile.fse_info.email_keep_private == 1) {
			$('#emailKEEPPRIVATE').removeClass ("off");
			$('#emailKEEPPRIVATE').addClass ("on");

			var $checkbox = $('#emailKEEPPRIVATE').children ('input');
			$checkbox.attr ("checked", true);
			$checkbox.attr ("value", "on");
		}
		$("#formVERIFYEMAIL input[name='primaryEmail']").val (fse_basic_profile.fse_info.email_box);
	});
});
</script>

