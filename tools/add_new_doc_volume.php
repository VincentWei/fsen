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

$project_id = $_REQUEST['projectID'];
$domain_handle = $_REQUEST['domainHandle'];

$doc_lang = substr ($project_id, -2);

if (!fse_try_to_login ()) {
	$error_info = ('You are not signed in.');
}
else if (!in_array ($domain_handle, array ('download', 'contribute', 'misc'))) {
	$error_info = t('Only support Download, Contribute, and Misc domains.');
}
else {
	$db = Loader::db ();
	$project_info = $db->getRow ("SELECT fse_id FROM fsen_projects WHERE project_id=?",
		array ($project_id));
	if ($_SESSION['FSEInfo']['fse_id'] != $project_info['fse_id']) {
		$error_info = t('You are not the owner or manager of this project!');
	}
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only"><?php echo t ('Close') ?></span>
	</button>
	<h4 class="modal-title"><?php echo t ('Add New Volume') ?></h4>
</div>
<div class="modal-body">
<?php
if (isset ($error_info)) {
?>
	<p><?php echo $error_info ?></p>
<?php
}
else {
$form_action = "/fse_settings/projects/add_new_doc_volume";
$form = Loader::helper('form');
?>
	<form id="formNewVolume" method="post" action="<?php echo $form_action ?>" class="form-horizontal">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />

		<div class="form-group">
				<label for="newVolumeName" class="col-md-4 control-label"><?php echo t('New Volume Name: ') ?> </label>
				<div class="col-md-8">
					<?php echo  $form->text ('newVolumeName', '',
							array ("required" => "true", "pattern" => ".{2,64}", "maxlength" => "64",
								"title" => "Pattern: .{2,64}",
								"class" => "form-control")); ?>
				</div>
		</div>

		<div class="form-group">
				<label for="newVolumeHandle" class="col-md-4 control-label"><?php echo t('New Volume Handle: ') ?> </label>
				<div class="col-md-8">
					<?php echo  $form->text ('newVolumeHandle', '',
							array ("required" => "true", "pattern" => "[a-z0-9\-]{4,16}", "maxlength" => "16",
								"title" => "Pattern: [a-z0-9\-]{4,16}",
								"class" => "form-control")); ?>
				</div>
		</div>

		<div class="form-group">
				<label for="newVolumeDesc" class="col-md-4 control-label"><?php echo t('New Volume Desc: ') ?> </label>
				<div class="col-md-8">
					<?php echo  $form->text ('newVolumeDesc', '',
							array ("required" => "true", "pattern" => ".{2,255}", "maxlength" => "255",
								"title" => "Pattern: .{2,255}",
								"class" => "form-control")); ?>
				</div>
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
	<button id="btnNewVolume" type="button" class="btn btn-primary"
			data-doing-text="<?php echo t ('Submitting...') ?>"
			data-done-text="<?php echo t ('Done') ?>">
		<?php echo t ('Ok') ?>
	</button>
<?php
}
?>
</div>

<script lang="text/javascript">
$('#formNewVolume').submit (function (e) {
	$('#btnNewVolume').attr ('disabled', 'disabled');
	$('#btnNewVolume').bootstrapBtn ('doing');
});

$('#btnNewVolume').click (function () {
	$('#formNewVolume input[name="inputHiddenSubmit"]').click();
});

</script>
