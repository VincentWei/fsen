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

Loader::model ('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

$domain_handle = $_REQUEST['domainHandle'];
$section_id = $_REQUEST['sectionID'];

require_once('helpers/check_login.php');
require_once('helpers/fsen/DocSectionManager.php');
require_once('helpers/fsen/ProjectInfo.php');

if (!fse_try_to_login ()) {
	$error_info = t('You are not signed in.');
}
else if (preg_match ("/^[a-f0-9]{32}$/", $section_id)
		&& in_array ($domain_handle, ProjectInfo::$mDomainList)) {
	$db = Loader::db ();
	$section_info = DocSectionManager::getSectionInfo ($domain_handle, $section_id);
	if (count ($section_info) == 0) {
		$error_info = t('No such section ID!');
	}
	else if ($current_ver_code > $section_info['max_ver_code']) {
		$error_info = t('Bad request!');
	}
	else {
		$project_id = $section_info ['project_id'];
		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info == false) {
			$error_info = t('Bad project');
		}
		else if (($user_right = ProjectInfo::getUserEditRight ($project_id, $domain_handle, $section_info['volume_handle'],
				$section_info['part_handle'], $section_info['chapter_handle'], $_SESSION['FSEInfo']['fse_id'])) != 0) {
			switch ($user_right) {
			case ProjectInfo::EDIT_PAGE_USER_BANNED:
				$error_info = t('You are banned currently due to the violation against the site policy!');
				break;
			case ProjectInfo::EDIT_PAGE_USER_NO_RIGHT:
				$error_info = t('You have no right to edit this section!');
				break;
			default:
				$error_info = t('Bad request!');
				break;
			}
		}
		else {
			$form_action = "/fse_settings/projects/delete_section";
		}
	}
}
else {
	$error_info = t('Bad Request!');
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only"><?php echo t ('Close') ?></span>
	</button>
	<h4 class="modal-title"><?php echo t ('Delete Section') ?></h4>
</div>
<div class="modal-body">
	<p><?php echo isset ($error_info)?$error_info:t ('Do you really want to delete this section?') ?></p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">
		<?php echo t ('Cancel') ?>
	</button>
<?php
if (!isset ($error_info)) {
?>
	<button id="btnDeleteSection" type="button" class="btn btn-warning"
			data-doing-text="<?php echo t ('Deleting...') ?>"
			data-done-text="<?php echo t ('Deleted') ?>">
		<?php echo t ('Delete') ?>
	</button>
<?php
}

$form_token_name = 'formToken4ChangeVersion';
$form_token = hash_hmac ('md5', time (), $section_id);
$_SESSION [$form_token_name] = $form_token;

$doc_lang = substr ($project_id, -2);

?>

<form id="formDeleteSection" method="post" action="<?php echo $form_action ?>">
	<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
	<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
	<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
	<input type="hidden" name="volumeHandle" value="<?php echo $section_info ['volume_handle'] ?>" />
	<input type="hidden" name="partHandle" value="<?php echo $section_info ['part_handle'] ?>" />
	<input type="hidden" name="chapterHandle" value="<?php echo $section_info ['chapter_handle'] ?>" />
	<input type="hidden" name="sectionID" value="<?php echo $section_id ?>" />

	<input type="hidden" name="formTokenName" value="<?php echo $form_token_name ?>" />
	<input type="hidden" name="formToken" value="<?php echo $form_token ?>" />

	<input type="submit" name="inputHiddenSubmit" value="submit" style="display:none" />

</form>

<script lang="JavaScript">
$('#formDeleteSection').submit (function (e) {
	$('#btnDeleteSection').attr ('disabled', 'disabled');
	$('#btnDeleteSection').bootstrapBtn ('doing');
});

$('#btnDeleteSection').on('click', function () {
	$('#formDeleteSection input[name="inputHiddenSubmit"]').click();
});
</script>
