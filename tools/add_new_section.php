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

$form_token_name = '#formAddNewSection';
$form_token = hash_hmac ('md5', time (), $chapter_handle);
$_SESSION [$form_token_name] = $form_token;
?>

	<div class="btn-toolbar">
		<?php Loader::element('content_type_list', array (
						'output_type' => 'dropdown-menu',
						'label' => t('Content Type: '),
						'selected' => 'plain',
						'data_target' => 'contentType')); ?>

		<?php Loader::element('content_format_list', array (
						'output_type' => 'dropdown-menu',
						'label' => t('Content Format: '),
						'selected' => 'markdown',
						'data_target' => 'contentFormat')); ?>

		<?php Loader::element('computer_language_list', array (
						'output_type' => 'dropdown-menu',
						'label' => t('Code Language: '),
						'selected' => 'plain',
						'data_target' => 'contentCodeLang')); ?>
	</div>

	<div class="btn-toolbar">
		<?php Loader::element('content_wrapper_list', array (
						'output_type' => 'dropdown-menu',
						'label' => t('Content Wrapper: '),
						'selected' => 'none',
						'data_target' => 'contentWrapper')); ?>

		<?php Loader::element('content_style_list', array (
						'output_type' => 'dropdown-menu',
						'label' => t('Content Style: '),
						'selected' => 'none',
						'data_target' => 'contentStyle')); ?>

		<?php Loader::element('content_alignment_list', array (
						'output_type' => 'dropdown-menu',
						'label' => t('Content Alignment: '),
						'selected' => 'none',
						'data_target' => 'contentAlignment')); ?>
	</div>

	<form id="formAddNewSection" method="post" action="<?php echo $form_action ?>" class="validate form-horizontal">
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

		<input type="hidden" name="contentType" value="plain" />
		<input type="hidden" name="contentFormat" value="markdown" />
		<input type="hidden" name="contentCodeLang" value="plain" />
		<input type="hidden" name="contentWrapper" value="none" />
		<input type="hidden" name="contentStyle" value="none" />
		<input type="hidden" name="contentAlignment" value="none" />

		<div style="margin-bottom:15px;">
			<label for="sectionSubject"><?php echo t('Title (optional): ') ?></label>
			<input type="text" class="form-control" name="sectionSubject" value="" maxlength="40" style="width:100%;"/>
		</div>

		<div style="margin-bottom:15px;">
			<label for="sectionContent">
				<?php echo t('Content: ') ?>
			</label>
			<textarea name="sectionContent" rows="15" style="width:100%;"
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


<script lang="javascript">
auto_save_form_content ('#formAddNewSection', 'input[name="sectionSubject"]', 'textarea[name="sectionContent"]');

$('.dropdown-toggle').dropdown();

$('.menuitem').click (function (e) {
	e.preventDefault ();
	var value = $(this).attr ('data-value');
	var text = $(this).text ();
	var target = $(this).attr ('data-target');
	$('#formAddNewSection input[name="' + target + '"]').val (value);
	$(this).parent().parent().prev().children ('m').text (text);
});

</script>

<?php
require_once(DIR_FILES_ELEMENTS_CORE . '/dialog_footer.php');
?>
