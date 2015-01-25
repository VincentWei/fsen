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

<section class="profile">

<form method="post" action="">
	<fieldset class="flat">
		<header>
			<h1>
				<?php echo t('Request to Reset Password') ?>
			</h1>
		</header>
		<section class="fieldBase">
			<?php echo t('Email Address') ?>
			<input id="reset2_EMAIL" type="email" maxlength="128" name="email"
				required="true" placeholder="abc@xyz.com" />
		</section>
		<section class="description">
			<span id="reset2_badEMAIL" style="display:none;">
				<?php echo t('Bad email address!') ?>
			</span>
		</section>
		<section class="fieldBase transparent">
			<input type="submit" id="RESET2" value="<?php echo t('Submit') ?>" data="nosent" />
		</section>
		<section class="description">
			<?php echo t('After you clicked the button above, we will send an email to your primary email address you signed up. Please check your email box and follow the instruction in the email.') ?>
			<span id="okRESET2" style="display:none;">
				<?php echo t('Email to reset the password has been sent. Check your email box (especially the Trash folder). Do not re-submit this request! If you can not get the mail in 24 hours, it may be because that you registered with a wrong email address. In such situation, you can register a new account with another email address.') ?>
			</span>
			<span id="badRESET2" style="display:none;">
				<?php echo t('Error: The email address is not registered.') ?>
			</span>
			<span id="errRESET2" style="display:none;">
				<?php echo t('Error: Duplicated requests; please try to find the email to reset password in the Trash folder or try again after 12 hours.') ?>
			</span>
		</section>
	</fieldset>
</form>

</section>

<script type="text/javascript">
function check_email (prefix, check_db)
{
	var email = $('#' + prefix + 'EMAIL').val();
	email_pattern = /^[\w-]+([.+][\w-]+)*@[\w-]+(\.[\w-]+)+$/;
	if (email_pattern.test (email)) {
		if (check_db) {
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

$('#RESET2').click (function (event) {
	event.preventDefault ();

	var email = check_email ("reset2_", false);
	if (email == "") return;

	if ($(this).attr ('data') == 'sent') {
		return;
	}

	$("#okRESET2").hide ();
	$("#errRESET2").hide ();
	$("#badRESET2").hide ();

	$(this).attr ('data', 'sent');
	$.post ("<?php echo $uh->getBlockTypeToolsURL ($bt) ?>/request_to_reset_passwd.php",
		{
			emailBox: email
		}, function (data) {
			if (data.search (/ok/) >= 0) {
				$("#okRESET2").show ();
			}
			else if (data.search (/err/) >= 0) {
				$("#errRESET2").show ();
			}
			else {
				$("#badRESET2").show ();
			}
			$('#RESET2').attr ('data', 'notsent');
		});
});

</script>
