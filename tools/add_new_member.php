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

if (!Loader::helper('validation/numbers')->integer($_REQUEST['cID'])) {
	die(t('Access Denied'));
}

require_once ('helpers/check_login.php');
require_once ('helpers/fsen/ProjectInfo.php');

$project_id = $_REQUEST['projectID'];
$doc_lang = substr ($project_id, -2);
$domain_handle = $_REQUEST['domainHandle'];
$volume_handle = $_REQUEST['volumeHandle'];

if (!fse_try_to_login ()) {
	$error_info = t('You are not signed in.');
}
else if ($domain_handle != 'misc') {
	$error_info = t('Bad domain or volume.');
}
else {
	$form_action = "/fse_settings/projects/add_new_member";

	$c = Page::getByID ($_REQUEST['cID']);
	$a = Area::get ($c, $_REQUEST['areaHandle']);
	if (!is_object ($a)) {
		$error_info = t('Bad request!');
	}
	else {
		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if (($project_info == false)) {
			$error_info = t('Bad project!');
		}
		else if (substr (ProjectInfo::getUserRights ($project_id, $_SESSION['FSEInfo']['fse_id']), 0, 1) != 't') {
			$error_info = t('You have no right to add new member to this project!');
		}
	}
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only"><?php echo t ('Close') ?></span>
	</button>
	<h4 class="modal-title"><?php echo t ('Add New Member') ?></h4>
</div>
<div class="modal-body">
<?php
if (isset ($error_info)) {
?>
	<p><?php echo $error_info ?></p>
<?php
}
else {
	$form = Loader::helper('form');
?>
	<form id="formMemberRoles" method="post" action="<?php echo $form_action ?>" class="validate form-horizontal">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="cID" value="<?php echo $_REQUEST['cID'] ?>" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="areaHandle" value="<?php echo $_REQUEST['areaHandle'] ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $_REQUEST['partHandle'] ?>" />
		<input type="hidden" name="chapterHandle" value="<?php echo $_REQUEST['chapterHandle'] ?>" />

		<div class="form-group">
			<label class="col-md-4 control-label" for="memberUsername">
				<?php echo t('FSEN Username: ') ?>
			</label>
			<div class="col-md-8">
				<?php echo  $form->text ('memberUsername', '',
							array ("class" => 'member-username form-control',
								"required" => "true", "pattern" => ".{2,30}")); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-md-4 control-label" for="">
				<?php echo t('Current Roles: ') ?>
			</label>
			<div class="col-md-8">
				<span id="spnMemberRolesCurrent">
					<?php echo t('Unknown') ?>
				</span>
			</div>
		</div>

		<div class="form-group">
			<label class="col-md-4 control-label" for="memberDisplayName">
				<?php echo t('Member Name: ') ?>
			</label>
			<div class="col-md-8">
				<?php echo  $form->text ('memberDisplayName', '',
					array ("required" => "true", "pattern" => ".{2,64}", 'class' => 'form-control')); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-md-4 control-label" for="memberDescription">
				<?php echo t('Description: ') ?>
			</label>
			<div class="col-md-8">
				<?php echo  $form->text ('memberDescription', '',
					array ("required" => "true", "pattern" => ".{5,255}", 'class' => 'form-control')); ?>
			</div>
		</div>

		<legend>
			<?php echo t('Member Roles:') ?>
		</legend>

		<div class="checkbox">
			<label for="memberRole0">
				<input type="checkbox" name="memberRole0" value="g-adm" />
					<?php echo t('Administrator, who has the same rights as the owner of the project.') ?>
			</label>
		</div>

		<div class="checkbox">
			<label for="memberRole1">
				<input type="checkbox" name="memberRole1" value="c-adm" />
					<?php echo t('Forum administrator, who can manage all community forums.') ?>
			</label>
		</div>

		<div class="checkbox">
			<label for="memberRole2">
				<input type="checkbox" name="memberRole2" value="p-edt" />
					<?php echo t('Project page editor, who can edit any sections of the project.') ?>
			</label>
		</div>

		<div class="checkbox">
			<label for="memberRole3">
				<input type="checkbox" name="memberRole3" value="c-cmt" />
					<?php echo t('Commiter of the project.') ?>
			</label>
		</div>

		<div class="checkbox">
			<label for="memberRole4">
				<input type="checkbox" name="memberRole4" value="g-mmb" />
					<?php echo t('General member of the project.') ?>
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
	<button id="btnMemberOk" type="button" class="btn btn-primary" disabled="true"
			data-doing-text="<?php echo t ('Submitting...') ?>"
			data-done-text="<?php echo t ('Done') ?>">
		<?php echo t ('Ok') ?>
	</button>
<?php
}
?>
</div>

<script type="text/javascript">
function on_got_bad_user (ret_info)
{
	$('#spnMemberRolesCurrent').text ("<?php echo t('Bad username') ?>");
	$('#btnMemberOk').attr ('disabled', 'true');
}

function on_got_roles (ret_info)
{
	var current_roles = ret_info.roles;

	if (current_roles == '' || current_roles == undefined || current_roles == null) {
		$('#spnMemberRolesCurrent').text ("Not specified");
		$('#btnMemberOk').removeAttr ('disabled');
	}
	else {
		$('#spnMemberRolesCurrent').text (current_roles);
		var roles = current_roles.split ('|');
		for (var i = 0, len = roles.length; i < len; i++) {
			if (roles [i] == 'g-adm') {
				$('#formMemberRoles input[name="memberRole0"]').attr ('checked', 'checked');
			}
			else if (roles [i] == 'c-adm') {
				$('#formMemberRoles input[name="memberRole1"]').attr ('checked', 'checked');
			}
			else if (roles [i] == 'p-edt') {
				$('#formMemberRoles input[name="memberRole2"]').attr ('checked', 'checked');
			}
			else if (roles [i] == 'c-cmt') {
				$('#formMemberRoles input[name="memberRole3"]').attr ('checked', 'checked');
			}
			else if (roles [i] == 'g-mmb') {
				$('#formMemberRoles input[name="memberRole4"]').attr ('checked', 'checked');
			}
		}
	}
}

function on_got_profile (fse_info)
{
	$('#formMemberRoles input[name="memberDisplayName"]').val (fse_info.nick_name);
	$('#formMemberRoles input[name="memberDescription"]').val (fse_info.self_desc);
	get_user_roles_and_rights_on_project ("<?php echo $project_id ?>", fse_info.user_name, on_got_roles, on_got_bad_user);
}

$('.member-username').blur (function (event) {
	event.preventDefault ();
	if ($(this).val() != '') {
		$('#spnMemberRolesCurrent').text ("<?php echo t('Fetching...') ?>");
		get_other_public_profile ($(this).val(), on_got_profile);
	}
});

$('#formMemberRoles').submit (function (e) {
	$('#btnMemberOk').attr ('disabled', 'disabled');
	$('#btnMemberOk').bootstrapBtn ('doing');
});

$('#btnMemberOk').click (function () {
	$('#formMemberRoles input[name="inputHiddenSubmit"]').click();
});
</script>

