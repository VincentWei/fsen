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

require_once ('helpers/misc.php');

?>

<form id="formREGISTER" method="post" action="/fse_register/do_register">
	<fieldset class="flat">
		<header><h1><?php echo t('Sign up') ?></h1></header>
		<section class="fieldBase"><?php echo t('Username') ?>
			<input id="USERNAME" type="text" name="userName" maxlength="30"
				title="<?php echo t('4 to 30 characters (letters, digitals, and underlines)') ?>"
				pattern="[\w_]{4,30}" required="true"
				placeholder="my_username">
			</input>
		</section>
		<section class="description">
<?php echo t('Only English letters, digitals, and underlines are allowed. The length should be between 4 and 30. Note that the username will be automatically converted to lowercase letters. Anyone can visit your basic profile by browsing %s/&lt;lang_code&gt;/engineer/&lt;user_name&gt;.', BASE_URL) ?>
<span id="badUSERNAME" style="display:none;"><?php echo t('Bad or duplicated username!') ?></span>
		</section>

		<section class="fieldBase"><?php echo t('Language') ?>
			<select name="userLocale">
				<option value="zh_CN">中文</option>
				<option value="en_US">English</option>
			</select>
		</section>
		<section class="description">
<?php echo t('This site provides Chinese and English editions. Please choose your favorite language.') ?>
		</section>

		<section class="fieldBase"><?php echo t('Nickname') ?>
			<input id="NICKNAME" type="text" name="nickName" maxlength="30"
				title="<?php echo t('2 to 30 characters (printable characters, digitals, and underlines)') ?>"
				pattern="[\w_]{2,30}" required="true" placeholder="<?php echo t('MyNickname') ?>">
			</input>
		</section>
		<section class="description">
<?php echo t('Only printable characters, digitals and underlines are allowed.') ?>
<span id="badNICKNAME" style="display:none;"><?php echo t('Bad nick name!') ?></span>
		</section>

		<section class="fieldBase"><?php echo t('Email Address') ?>
			<input id="EMAIL" type="email" name="emailBox" maxlength="128"
				title="<?php echo t('Your Primary Email Address') ?>"
				required="true" placeholder="abc@xyz.com">
			</input>
		</section>
		<section class="description">
<?php echo t('We will use this email address to verify your identity when you try to reset the password.') ?>
<span id="badEMAIL" style="display:none;"><?php echo t('Bad or duplicated email address!') ?></span>
		</section>

		<section class="fieldBase"><?php echo t('Country/Region') ?>
			<select id="LOCATIONCOUNTRY">
				<option value="0"><?php echo t('--Keep Private--') ?></option>
				<option value="1">中国</option>
			</select>
		</section>
		<input id="LOCATIONCOUNTRYWITHCODE" name="locationCountry" type="hidden" />

		<section class="fieldBase"><?php echo t('State/Province') ?>
			<select id="LOCATIONPROVINCE">
				<option value="0"><?php echo t('--Keep Private--') ?></option>
			</select>
		</section>
		<input id="LOCATIONPROVINCEWITHCODE" name="locationProvince" type="hidden" />

		<section class="fieldBase"><?php echo t('District/County') ?>
			<select id="LOCATIONDISTRICT">
				<option value="0"><?php echo t('--Keep Private--') ?></option>
			</select>
		</section>
		<input id="LOCATIONDISTRICTWITHCODE" name="locationDistrict" type="hidden" />

		<section class="description">
<span id="badLOCATION" style="display:none;"><?php echo t('Please choose the place you located.') ?></span>
		</section>

		<section class="fieldBase"><?php echo t('Password') ?>
			<input id="PASSWORD" type="password" maxlength="20" name="plainPassword"
				pattern=".{6,20}" required="true"
				title="<?php echo t('6 to 20 characters') ?>" />
		</section>
		<section class="description">
<?php echo t('The length of password shoule be between 6 and 20.') ?>
<span id="badPASSWORD" style="display:none;"><?php echo t('Empty, too short, or too weak password!') ?></span>
		</section>

		<section class="fieldBase"><?php echo t('Confirm password') ?>
			<input id="REPASSWORD" type="password" maxlength="20" name="rePlainPassword"
				pattern=".{6,20}" required="true"
				title="<?php echo t('6 to 20 characters') ?>" />
		</section>
		<section class="description">
<span id="badREPASSWORD" style="display:none;"><?php echo t('Password does not matche!') ?></span>
		</section>
		<input id="HASHEDPASSWD" name="hashedPasswd" type="hidden" />

		<section class="fieldBase"><?php echo t('Captcha') ?>
			<input id="CAPTCHA" type="text" maxlength="10" name="captchaCode"
				pattern=".{6}" required="true" title="<?php echo t('Characters in the captcha image') ?>"/>
		</section>
		<section class="description">
<?php echo t('Click the image to refresh it.') ?>
<span id="badCAPTCHA" style="display:none;"><?php echo t('Incorrect captcha!') ?></span>
			<div>
				<img src="/index.php/tools/required/captcha?nocache=<?php echo time() ?>"
				alt="Captcha Code" id='CAPTCHAIMG'
				onclick="this.src = '/index.php/tools/required/captcha?nocache='+(new Date().getTime())" />
			</div>
		</section>

		<input name="fsenDocLang" type="hidden" value="<?php echo $_REQUEST['fsenDocLang'] ?>" />

		<section class="fieldBase transparent">
			<input id="REGISTER" type="submit" value="<?php echo t('Sign up') ?>" />
		</section>
		<section class="description">
<span id="badREGISTER" style="display:none;"><?php echo t('Failed to sign up: ') ?></span>
		</section>
		<section class="right-note">
<?php echo t('By clicking "Sign up", you agree to our <a href="/en/help/site-policy/terms-service">terms of service</a> and <a href="/en/help/site-policy/privacy-policy">privacy policy</a>. We will send you account related emails occasionally.') ?>
		</section>
	</fieldset>
</form>

<script type="text/javascript">
$('#LOCATIONCOUNTRY').change (function () {
	var $location_province = $('#LOCATIONPROVINCE');
	$('#LOCATIONPROVINCE option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('--Keep Private--') ?></option>').appendTo ($location_province);

	var $location_district = $('#LOCATIONDISTRICT');
	$('#LOCATIONDISTRICT option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('--Keep Private--') ?></option>').appendTo ($location_district);

	var division_id = $('#LOCATIONCOUNTRY').children ('option:selected').val ();
	if (division_id == "1") {
		$location_province.attr ("disabled", "true");
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
});

$('#LOCATIONPROVINCE').change (function () {
	var $location_district = $('#LOCATIONDISTRICT');
	$('#LOCATIONDISTRICT option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('--Keep Private--') ?></option>').appendTo ($location_district);

	var country_id = $('#LOCATIONCOUNTRY').children ('option:selected').val ();
	var division_id = $('#LOCATIONPROVINCE').children ('option:selected').val ();
	if (country_id == '1' && division_id != "0") {
		$location_district.attr ("disabled", "true");
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
});

function check_username (prefix, check_db)
{
	var username = $('#' + prefix + 'USERNAME').val();
	var matched = username.match (/^[\w_]{4,30}$/)
	if (matched == username) {
		if (check_db) {
			$.post ("<?php echo $uh->getBlockTypeToolsURL ($bt) ?>/check_username.php",
				{userName: username},
				function (data) {
					if (data.search (/none/) >= 0) {
						$('#' + prefix + 'badUSERNAME').hide ();
					}
					else {
						$('#' + prefix + 'badUSERNAME').show ();
					}
				});

			if ($('#' + prefix + 'badUSERNAME').is (":visible")) {
				return "";
			}
			else
				return username;
		}
		else {
			$('#' + prefix + 'badUSERNAME').hide ();
			return username;
		}
	}

	$('#' + prefix + 'badUSERNAME').show ();
	return "";
}

$('#USERNAME').blur (function (event) {
	check_username ("", true);
});

function check_nickname (prefix)
{
	var nickname = $('#' + prefix + 'NICKNAME').val();
	var matched = nickname.match (/^[\u2E80-\u9FFF\uA000-\uA4FF\uAC00-\uD7FF\uF900-\uFFFD\w_]{2,30}$/)
	if (matched == nickname) {
		$('#' + prefix + 'badNICKNAME').hide ();
		return nickname;
	}

	$('#' + prefix + 'badNICKNAME').show ();
	return "";
}

$('#NICKNAME').blur (function (event) {
	check_nickname ("");
});

function check_captcha (prefix)
{
	var captcha_code = $('#' + prefix + 'CAPTCHA').val();
	if (captcha_code != "") {
		$.post ("/index.php/tools/check_captcha.php",
			{captchaCode: captcha_code},
			function (data) {
				if (data.search (/ok/) >= 0) {
					$('#' + prefix + 'badCAPTCHA').hide ();
				}
				else {
					$('#' + prefix + 'badCAPTCHA').show ();
				}
			});

		if ($('#' + prefix + 'badCAPTCHA').is (":visible")) {
			return "";
		}
		else
			return captcha_code;
	}

	$('#' + prefix + 'badCAPTCHA').show ();
	return "";
}

$('#CAPTCHA').blur (function (event) {
	check_captcha ("");
});

function check_email (prefix, check_db)
{
	var email = $('#' + prefix + 'EMAIL').val();
	var email_pattern = /^[\w-]+([.+][\w-]+)*@[\w-]+(\.[\w-]+)+$/;
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

$('#EMAIL').blur (function (event) {
	check_email ("", true);
});

function check_reemail (prefix)
{
	var email = $('#' + prefix + 'EMAIL').val();
	var reemail = $('#' + prefix + 'REEMAIL').val ();

	if (email == reemail) {
		$('#' + prefix + 'badREEMAIL').hide ();
		return reemail;
	}

	$('#' + prefix + 'badREEMAIL').show ();
	return "";
}

function check_password (prefix)
{
	var password = $('#' + prefix + 'PASSWORD').val();
	var matched = password.match (/^[\x20-\x7E]{6,20}$/);
	if (matched == password) {
		$('#' + prefix + 'badPASSWORD').hide ();
		return password;
	}

	$('#' + prefix + 'badPASSWORD').show ();
	return "";
}

$('#PASSWORD').blur (function (event) {
	check_password ("");
});

function check_repassword (prefix)
{
	var password = $('#' + prefix + 'PASSWORD').val();
	var repassword = $('#' + prefix + 'REPASSWORD').val ();

	if (password == repassword) {
		$('#' + prefix + 'badREPASSWORD').hide ();
		return repassword;
	}

	$('#' + prefix + 'badREPASSWORD').show ();
	return "";
}

$('#REPASSWORD').blur (function (event) {
	check_repassword ("");
});

$('#REGISTER').click (function (event) {
	event.preventDefault ();

	var user_name = check_username ("", true);
	if (user_name == "") return;

	var nick_name = check_nickname ("");
	if (nick_name == "") return;

	var email_box = check_email ("", true);
	if (email_box == "") return;

	var captcha_code = check_captcha ("");
	if (captcha_code == "") return;

	var location_country = $('#LOCATIONCOUNTRY').val();
	location_country = location_country + ":" + $('#LOCATIONCOUNTRY').find ('option:selected').text ();
	$("#LOCATIONCOUNTRYWITHCODE").val (location_country);

	var location_province = $('#LOCATIONPROVINCE').val();
	location_province = location_province + ":" + $('#LOCATIONPROVINCE').find ('option:selected').text ();
	$("#LOCATIONPROVINCEWITHCODE").val (location_province);

	var location_district = $('#LOCATIONDISTRICT').val();
	location_district = location_district + ":" + $('#LOCATIONDISTRICT').find ('option:selected').text ();
	$("#LOCATIONDISTRICTWITHCODE").val (location_district);
	$("#badLOCATION").hide ();

	var password = check_password ("");
	if (password == "") return;
	var repassword = check_repassword ("");
	if (repassword == "") return;

	hashed_passwd = hex_hmac_md5 (user_name.toLowerCase(), password);
	$('#HASHEDPASSWD').val (hashed_passwd);

	$('#formREGISTER').submit();
});

</script>

