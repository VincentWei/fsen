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

Loader::model ('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

require_once('helpers/check_login.php');
require_once('helpers/fsen/DocSectionManager.php');
require_once('helpers/fsen/ProjectInfo.php');

$page_id = $_REQUEST['cID'];
$project_id = $_REQUEST['projectID'];
$domain_handle = $_REQUEST['domainHandle'];
$volume_handle = $_REQUEST['volumeHandle'];
$part_handle = $_REQUEST['partHandle'];

$project_shortname = substr ($project_id, 0, strlen ($project_id) - 3);
$doc_lang = substr ($project_id, -2);

if (!fse_try_to_login ()) {
	$error_info = t('You are not signed in.');
}
else if ($project_shortname != SYSTEM_PROJECT_SHORTNAME || $domain_handle != 'document' || $volume_handle != 'blog') {
	$error_info = t('Bad Request!');
}
else {
	$form_action = "/fse_settings/projects/add_new_blog";

	$fse_id = $_SESSION['FSEInfo']['fse_id'];
	$user_info = FSEInfo::getBasicProfile ($part_handle);
	$user_rights = ProjectInfo::getUserRights ($project_id, $fse_id);

	if ($user_rights [1] == 't' && $user_info ['fse_id'] != $fse_id) {
			$error_info = t('Only the author of this blog zone can add new blog!');
	}
	else {
		if ($user_info ['fse_id'] != $fse_id) {
			$error_info = t('You are not the author of this blog zone!');
		}
		else if ($user_info['email_verified'] == 0) {
			$error_info = t('Your primary email address has not been verified. Please verify it first in your Personal Settings page.');
		}
		else if ($user_info['status'] > 0) {
			$error_info = t('You are banned currently due to the violation against the site policy!');
		}
	}
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only"><?php echo t ('Close') ?></span>
	</button>
	<h4 class="modal-title"><?php echo t ('Add New Blog') ?></h4>
</div>

<div class="modal-body">
<?php
if (isset ($error_info)) {
?>
	<p><?php echo $error_info ?></p>
<?php
}
else {
	# we use formToken to avoid duplicated validation of parameters
	$form_token_name = 'formToken4AddBlog';
	$form_token = hash_hmac ('md5', time (), $part_handle);
	$_SESSION [$form_token_name] = $form_token;
?>
	<form id="formAddBlog" method="post" action="<?php echo $form_action ?>" role="form">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="cID" value="<?php echo $page_id ?>" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $part_handle ?>" />

		<input type="hidden" name="formTokenName" value="<?php echo $form_token_name ?>" />
		<input type="hidden" name="formToken" value="<?php echo $form_token ?>" />

		<div class="form-group">
			<label for="blogSubject" class="control-label">
				<?php echo t('Title'); ?>
			</label>
			<input class="form-control" type="text" name="blogSubject" maxlength="64"
					required="true" pattern=".{2,64}" placeholder="<?php echo t('2~64 characters') ?>" />
		</div>

		<div class="form-group">
			<label for="blogSummary" class="control-label">
				<?php echo t('Summary') ?>
			</label>
			<input class="form-control" type="text" name="blogSummary" maxlength="255"
					required="true" pattern=".{2,255}" placeholder="<?php echo t('2~255 characters') ?>" />
		</div>

		<div class="form-group">
			<label for="blogCategory" class="control-label">
				<?php echo t('Blog category'); ?>
			</label>
			<input class="form-control" type="text" name="blogCategory" maxlength="32"
					required="true" pattern=".{2,32}" placeholder="<?php echo t('2~32 characters') ?>" />
		</div>

		<div class="form-group">
			<label for="blogTags" class="control-label">
				<?php echo t('Tags'); ?>
			</label>
			<input class="form-control" type="text" name="blogTags" maxlength="255"
					required="false" placeholder="<?php echo t('Space delimited tags') ?>" />
		</div>

		<div class="checkbox">
			<label for="authorSuggested">
				<input type="checkbox" name="authorSuggested" value="1">
				<?php echo t('This blog is author suggested.') ?>
			</label>
		</div>

		<input type="submit" name="inputHiddenSubmit" value="submit" style="display:none" />

	</form>
<?php
}
?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">
		<?php echo t ('Cancel') ?>
	</button>
<?php
if (!isset ($error_info)) {
?>
	<button id="btnSubmit" type="button" class="btn btn-primary"
			data-doing-text="<?php echo t ('Creating...') ?>"
			data-done-text="<?php echo t ('Created') ?>">
		<?php echo t ('Add New Blog') ?>
	</button>
<?php
}
?>
</div>

<script lang="JavaScript">
$('#formAddBlog').submit (function (e) {
	$('#btnSubmit').attr ('disabled', 'disabled');
	$('#btnSubmit').bootstrapBtn ('doing');
});

$('#btnSubmit').click (function () {
	$('#formAddBlog input[name="inputHiddenSubmit"]').click();
});
</script>
