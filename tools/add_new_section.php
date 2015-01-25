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

Loader::model('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

require_once('helpers/check_login.php');
require_once('helpers/fsen/ProjectInfo.php');

$page_id = (int)$_REQUEST['cID'];
$project_id = $_REQUEST['projectID'];
$domain_handle = $_REQUEST['domainHandle'];
$volume_handle = $_REQUEST['volumeHandle'];
$part_handle = $_REQUEST['partHandle'];
$chapter_handle = $_REQUEST['chapterHandle'];

if (!fse_try_to_login ()) {
	$error_info = t('You are not signed in.');
}
else if (!in_array ($domain_handle, ProjectInfo::$mDomainList)) {
	$error_info = t('Bad request.');
}
else {
	$project_info = ProjectInfo::getBasicInfo ($project_id);
	if ($project_info == false) {
		$error_info = t('Bad project');
	}
	else {
		$fse_id = $_SESSION['FSEInfo']['fse_id'];
		$user_rights = ProjectInfo::getUserRights ($project_id, $fse_id);

		$c = Page::getByID ($page_id);
		$a = Area::get ($c, $_REQUEST['areaHandle']);
		if (!is_object ($a)) {
			$error_info = t('Bad request!');
		}
		else if (($user_right = ProjectInfo::getUserEditRight ($project_id, $domain_handle, $volume_handle,
				$part_handle, $chapter_handle, $fse_id)) != 0) {
			switch ($user_right) {
			case ProjectInfo::EDIT_PAGE_USER_BANNED:
				$error_info = t('You are banned currently due to the violation against the site policy!');
				break;
			case ProjectInfo::EDIT_PAGE_USER_NO_RIGHT:
				$error_info = t('You have no right to edit this blog!');
				break;
			default:
				$error_info = t('Bad request!');
				break;
			}
		}
	}
}

require_once(DIR_FILES_ELEMENTS_CORE . '/dialog_header.php');
?>

<div class="ccm-ui">
<?php
if (isset ($error_info)) {
?>
	<p class="ccm-error"><?php echo $error_info ?></p>
	<div class="ccm-buttons dialog-buttons">
		<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();"
			class="btn ccm-button-left cancel"><?php echo t('Cancel') ?></a>
	</div>
</div>
<?php
	exit (0);
}

$form = Loader::helper('form');
$al = Loader::helper ('concrete/asset_library');

$form_action = "/fse_settings/projects/add_new_section";
$doc_lang = substr ($project_id, -2);

$form_token_name = 'formToken4AddNewSection';
$form_token = hash_hmac ('md5', time (), $chapter_handle);
$_SESSION [$form_token_name] = $form_token;
?>

	<form method="post" action="<?php echo $form_action ?>" enctype="multipart/form-data" class="validate form-horizontal">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="cID" value="<?php echo $page_id ?>" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="areaHandle" value="<?php echo $_REQUEST['areaHandle'] ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $part_handle ?>" />
		<input type="hidden" name="chapterHandle" value="<?php echo $chapter_handle ?>" />

		<input type="hidden" name="formTokenName" value="<?php echo $form_token_name ?>" />
		<input type="hidden" name="formToken" value="<?php echo $form_token ?>" />

		<div class="form-group form-group-sm">
			<label for="contentType" class="col-md-3 control-label">
				<?php echo t('Content Type: ') ?>
			</label>
			<div class="col-md-3">
				<?php echo $form->select ('contentType', array (
						'plain' => t('Plain Section'),
						'pre' => t('Preformatted Text'),
						'code' => t('Code'),
						'quotation' => t('Quotation'),
						'quotation-reverse' => t('Quotation Reverse'),
						'address' => t('Address')), 'plain', array ("class" => "form-control")); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label for="contentFormat" class="col-md-3 control-label">
				<?php echo t('Content Format: ') ?>
			</label>
			<div class="col-md-3">
				<?php echo $form->select ('contentFormat', array (
						'markdown' => 'Markdown',
						'markdown_extra' => 'Markdown Extra',
						'media_wiki' => 'Media Wiki Text',
						'plain' => 'Plain',
						), 'markdown', array ("class" => "form-control")); ?>
			</div>
			<div class="col-md-6">
				<p class="help-block">
					<?php echo t('Plain is only valid for Code and Preformatted type.') ?>
				</p>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label for="contentCodeLang" class="col-md-4 control-label">
				<?php echo t('Code Language: ') ?>
			</label>
			<div class="col-md-3">
				<select name="contentCodeLang" class="form-control">
					<?php Loader::element('computer_language_list', array ('selected_value' => 'plain')); ?>
				</select>
			</div>
			<div class="col-md-6">
				<p class="help-block">
					<?php echo t('Only valid for Code type.') ?>
				</p>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label for="contentWrapper" class="col-md-3 control-label">
				<?php echo t('Content Wrapper: ') ?>
			</label>
			<div class="col-md-3">
				<select name="contentWrapper" class="form-control">
					<?php Loader::element('content_wrapper_list', array ('selected_value' => 'none')); ?>
				</select>
			</div>
			<div class="col-md-6">
				<p class="help-block">
					<?php echo t('Refer to <a target="_blank" href="http://getbootstrap.com">HERE</a> for Bootstrap.') ?>
				</p>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label for="contentStyle" class="col-md-3 control-label">
				<?php echo t('Content Style: ') ?>
			</label>
			<div class="col-md-3">
				<select name="contentStyle" class="form-control">
					<?php Loader::element('content_style_list', array ('selected_value' => 'none')); ?>
				</select>
			</div>
			<div class="col-md-6">
				<p class="help-block">
					<?php echo t('Only valid when Wrapper is not none.') ?>
				</p>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label for="contentAlignment" class="col-md-3 control-label">
				<?php echo t('Content Alignment: ') ?>
			</label>
			<div class="col-md-3">
				<select name="contentAlignment" class="form-control">
					<?php Loader::element('content_alignment_list', array ('selected_value' => 'none')); ?>
				</select>
			</div>
			<div class="col-md-6">
				<p class="help-block">
					<?php echo t('Only valid when Wrapper is not none.') ?>
				</p>
			</div>
		</div>

		<div style="margin-bottom:15px;">
			<label for="sectionSubject"><?php echo t('Title (optional): ') ?></label>
			<input type="text" class="form-control" name="sectionSubject" value="" maxlength="40" style="width:100%;"/>
		</div>

		<div style="margin-bottom:15px;">
			<label for="sectionContent">
				<?php echo t('Content: ') ?>
			</label>
			<textarea name="sectionContent" rows="7" style="width:100%;"
					required="true" placeholder="<?php echo t('Content (20 characters at least)') ?>"></textarea>
		</div>

		<div class="form-group">
			<label for="attachmentFile0">
				<?php echo t('Attached Files: ') ?>
			</label>
	<?php
			for ($i = 0; $i < ProjectInfo::MAX_ATTACHED_FILES; $i++) {
				echo $al->file("attachmentFile$i", "attachmentFile$i", t('Upload or Choose File'));
			}
	?>
		</div>

		<input type="submit" name="fsen-add-section-submit" value="submit" style="display: none"
			id="btnAddSectionSubmit" />

	</form>

	<div class="ccm-buttons dialog-buttons">
		<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();"
			class="btn ccm-button-left cancel"><?php echo t('Cancel') ?></a>
		<a href="javascript:void(0)" onclick="$('#btnAddSectionSubmit').get(0).click()"
			class="ccm-button-right accept btn primary"><?php echo t('Add') ?> <i class="icon-plus-sign icon-white"></i></a>
	</div>

</div>

<?php
require_once(DIR_FILES_ELEMENTS_CORE . '/dialog_footer.php');
?>
