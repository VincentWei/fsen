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

require_once ('helpers/misc.php');

$uh = Loader::helper('concrete/urls');
$bt = BlockType::getByHandle('fse_register');

?>

<form method="post" action="">
	<fieldset class="flat">
		<header>
			<h1><?php echo t('Sign in') ?></h1>
		</header>
		<section class="fieldBase"><?php echo t('Username or Email') ?>
			<input id="login_USERNAME" type="text" maxlength="20" name="username"
				required="true" />
		</section>
		<section class="description">
<span id="login_badUSERNAME" style="display:none;"><?php echo t ('Bad username or email!') ?></span>
		</section>

		<section class="fieldBase"><?php echo t('Password') ?>
			<input id="login_PASSWORD" type="password" maxlength="20" name="password"
				required="true" />
		</section>
		<section class="description">
<span id="login_badPASSWORD" style="display:none;"><?php echo t ('Bad password!') ?></span>
		<section class="right-note">
<a href="/fse_request_to_reset_password"><?php echo t('Forget password') ?></a>
		</section>
		</section>

		<section class="fieldBase"><?php echo t('Remember me') ?>
			<div class="switch on" id="login_SAVEPASSWD">
				<span class="thumb"></span>
				<input type="checkbox" value="on" checked="checked">
			</div>
		</section>
		<section class="description">
<?php echo t ('Only valid for one week.') ?>
		</section>

		<section class="fieldBase transparent">
			<input type="submit" value="<?php echo t('Sign in') ?>" id="LOGIN" />
		</section>
		<section class="description">
<span id="badLOGIN" style="display:none;"></span>
		<section class="right-note">
<a href="/fse_register"><?php echo t('Sign up') ?></a>
		</section>
		</section>
	</fieldset>
</form>


<script type="text/javascript">
function check_username (prefix)
{
	var username_or_email = $('#' + prefix + 'USERNAME').val();
	var username_pattern = /^[\w_]{4,30}$/;
	var email_pattern = /^[\w-]+([.+][\w-]+)*@[\w-]+(\.[\w-]+)+$/;

	$('#' + prefix + 'USERNAME').attr ("data-value", "");
	if (username_pattern.test (username_or_email)) {
		$('#' + prefix + 'badUSERNAME').hide ();
		$('#' + prefix + 'USERNAME').attr ("data-value", username_or_email);
		return username_or_email;
	}
	else if (email_pattern.test (username_or_email)) {
		$.ajaxSetup({async:false});
		$.post ("<?php echo $uh->getBlockTypeToolsURL ($bt) ?>/get_username_from_email.php",
			{emailBox: username_or_email},
			function (data, status) {
				if (status != "success") {
					data = "@@@";
				}
				data = data.trim ();
				if (data.search (/@@@/) >= 0) {
					$('#' + prefix + 'badUSERNAME').show ();
					$('#' + prefix + 'USERNAME').attr ("data-value", "");
				}
				else {
					$('#' + prefix + 'badUSERNAME').hide ();
					$('#' + prefix + 'USERNAME').attr ("data-value", data);
				}
			});
	}

	return $('#' + prefix + 'USERNAME').attr ("data-value");
}

$('#login_USERNAME').focus (function (event) {
	$("#badLOGIN").hide ();
});

$('#login_PASSWORD').focus (function (event) {
	$("#badLOGIN").hide ();
});

$('#LOGIN').click (function (event) {
	event.preventDefault ();

	$("#badLOGIN").hide ();

	var username = check_username ("login_");
	if (username == "") return;

	var password = $('#login_PASSWORD').val();
	if (password == "") return;

	hashed_passwd = hex_hmac_md5 (username.toLowerCase(), password);

	var save_passwd = $("#login_SAVEPASSWD").children ('input').attr ("value");

	$.ajaxSetup({async:false});
	$.post ("<?php echo $uh->getBlockTypeToolsURL ($bt) ?>/login.php",
		{
			fsenDocLang: "<?php echo $_REQUEST['fsenDocLang'] ?>",
			userName: username,
			hashedPasswd: hashed_passwd,
			savePasswd: save_passwd,
			redirectURL: "<?php echo $_GET['redirectURL'] ?>"
		}, function (data) {
			var ret_info = eval ('(' + data + ')');
			if (ret_info.status == 'ok') {
				window.location.href = ret_info.detail;
			}
			else {
				$("#badLOGIN").text (ret_info.detail);
				$("#badLOGIN").show ();
				$('#login_PASSWORD').val("");
			}
		});
});

</script>
