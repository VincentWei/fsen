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

<form id="formDELETEACCOUNT" method="post" action="/fse_settings/account/delete_account">
	<fieldset class="flat">
		<header class="alert">
			<h2>
				<?php echo t('Delete Account') ?>
			</h2>
		</header>

		<section class="note">
			<p>
				<?php echo t('Once you delete your account, there is no going back. Please be certain.') ?>
			</p>
			<p>
				<?php echo t('We will immediately delete all of your projects, blogs, along with your personal home page, and your username will be available to anyone on FSEN.') ?>
			</p>
			<p>
				<?php echo t('For more help, read our article "<a href="/en/help/user-accounts/deleting-user-account">Deleting your user account</a>".') ?>
				</p>
		</section>

		<section class="fieldBase">
			<?php echo t('Confirm') ?>
			<input id="deleteINTENT" name="deleteIntent" type="text" maxlength="20" />
		</section>
		<section class="description">
			<?php echo t('Type in the following phrase in above field: <i>delete my account</i>') ?>
		</section>

		<section class="fieldBase">
			<?php echo t('Username') ?>
			<input id="deleteUSERNAME" name="userName" type="text" maxlength="30" />
		</section>

		<section class="fieldBase">
			<?php echo t('Password') ?>
			<input id="deletePASSWORD" type="password" maxlength="20" />
		</section>

		<input id="deleteHASHEDPASSWD" name="hashedPasswd" type="hidden" value="notset" />

		<section class="fieldBase transparent">
			<input class="alert" type="submit" value="<?php echo t('Delete Right Now!') ?>" id="btnDELETEACCOUNT" />
		</section>
	</fieldset>
</form>

<script type="text/javascript">

$('#btnDELETEACCOUNT').click (function (event) {
	event.preventDefault ();

	var user_name = $('#deleteUSERNAME').val();
	var password = $('#deletePASSWORD').val();
	var hashed_passwd = hex_hmac_md5 (user_name.toLowerCase (), password);
	$('#deleteHASHEDPASSWD').val (hashed_passwd);

	$('#formDELETEACCOUNT').submit();
});
</script>

