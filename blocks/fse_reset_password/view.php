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

$hash_value = $_GET ['hashValue'];
$db = Loader::db ();
$row = $db->getRow ("SELECT B.user_name, B.email_box FROM fse_reset_password_validation_hashes AS A, fse_basic_profiles AS B
	WHERE A.hash_value=? AND A.email_box=B.email_box", array ($hash_value));

if ($row !== false && count ($row) > 0) {
?>
<form method="post" action="">
	<fieldset class="flat">
		<header>
			<h1>
				<?php echo t('Reset Password') ?>
			</h1>
		</header>

		<section class="fieldBase">
			<?php echo t('New Password') ?>
			<input id="change_PASSWORD" type="password" maxlength="20"
				name="password">
			</input>
		</section>
		<section class="description">
		<?php echo t('The length of the password should be between 6 and 20.') ?>
			<span id="change_badPASSWORD" style="display:none;">
				<?php echo t('Empty, too short, or two weak password!') ?>
			</span>
		</section>

		<section class="fieldBase">
			<?php echo t('Confirm New Password') ?>
			<input id="change_REPASSWORD" type="password" maxlength="20"
				name="re-password">
			</input>
		</section>
		<section class="description">
			<span id="change_badREPASSWORD" style="display:none;">
				<?php echo t('The password dose not matched!') ?>
			</span>
		</section>

		<section class="fieldBase transparent">
			<input type="submit" value="<?php echo t('Reset') ?>" id="CHANGE" />
		</section>
		<section class="description">
			<span id="okCHANGE" style="display:none;">
				<?php echo t('Succeed to Reset Password!') ?>
			</span>
			<span id="badCHANGE" style="display:none;">
				<?php echo t('Failed to reset password!') ?>
			</span>
		</section>
	</fieldset>
</form>

<script type="text/javascript">

function check_password (prefix)
{
	var password = $('#' + prefix + 'PASSWORD').val();
	var matched = password.match (/^[A-Za-z0-9]{6,20}$/);
	if (matched == password) {
		$('#' + prefix + 'badPASSWORD').hide ();
		return password;
	}

	$('#' + prefix + 'badPASSWORD').show ();
	$('#' + prefix + 'PASSWORD').focus ();
	return "";
}

function check_repassword (prefix)
{
	var password = $('#' + prefix + 'PASSWORD').val();
	var repassword = $('#' + prefix + 'REPASSWORD').val ();

	if (password == repassword) {
		$('#' + prefix + 'badREPASSWORD').hide ();
		return repassword;
	}

	$('#' + prefix + 'badREPASSWORD').show ();
	$('#' + prefix + 'PASSWORD').focus ();
	return "";
}

$('#change_PASSWORD').blur (function (event) {
	check_password ("change_");
});

$('#change_REPASSWORD').blur (function (event) {
	check_repassword ("change_");
});

$('#CHANGE').click (function (event) {
	event.preventDefault ();

	$("#okCHANGE").hide ();
	$("#badCHANGE").hide ();

	var user_name = "<?php echo $row ['user_name'] ?>";
	var email_box = "<?php echo $row ['email_box'] ?>";
	var hash_value = "<?php echo $hash_value ?>";

	var password = check_password ("change_");
	if (password == "") return;
	var repassword = check_repassword ("change_");
	if (repassword == "") return;

	var hashed_passwd = hex_hmac_md5 (user_name.toLowerCase (), password);

	$.post ("<?php echo $uh->getBlockTypeToolsURL ($bt) ?>/reset_passwd.php",
		{
			userName: user_name,
			emailBox: email_box,
			hashValue: hash_value,
			hashedPasswd: hashed_passwd,
		}, function (data) {
			if (data.search (/ok/) >= 0) {
				$("#okCHANGE").show ();
				window.location.href = "/fse_login";
			}
			else {
				alert (data);
				$("#badCHANGE").show ();
			}
		});
});

</script>

<?php
}
else {
?>
<div class="banner-global">
	<div class="container-fluid">
		<h1>
			<?php echo t('WE ARE VERY SORRY!') ?>
		</h1>
		<p class="lead">
			<?php echo t('You are here because of') ?>
		</p>
		<ul class="lead">
			<li><?php echo t('You have signned out. Or') ?></li>
			<li><?php echo t('You have validated your email address. Or') ?></li>
			<li><?php echo t('You made a bad request. Or') ?></li>
			<li><?php echo t('The page you requested have been removed.') ?></li>
		</ul>
		<p class="lead">
			<?php echo t('Please return home and find some new intersting things.') ?>
		</p>
		<p class="lead">
			<a href="/" class="btn btn-primary btn-lg"><?php echo t('Back to Home.') ?></a>
		</p>
	</div>
</div>
<?php
}
?>

