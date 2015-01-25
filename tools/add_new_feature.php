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

require_once ('helpers/check_login.php');
require_once ('helpers/fsen/ProjectInfo.php');

$page_id = (int)$_REQUEST['cID'];
$project_id = $_REQUEST['projectID'];
$doc_lang = substr ($project_id, -2);

$domain_handle = $_REQUEST['domainHandle'];
$volume_handle = $_REQUEST['volumeHandle'];
if ($domain_handle == 'home') {
	$form_action = "/fse_settings/projects/add_new_section";
}
else {
	$error_info = t('Bad domain or volume!');
}

if (!isset ($error_info)) {
	if (!fse_try_to_login ()) {
		$error_info = t('You are not signed in.');
	}
	else {
		$c = Page::getByID ($page_id);
		$a = Area::get ($c, $_REQUEST['areaHandle']);
		if (!is_object ($a)) {
			$error_info = t('Bad request!');
		}
		else {
			$project_info = ProjectInfo::getBasicInfo ($project_id);
			if ($project_info == false) {
				$error_info = t('Bad project');
			}
			else if (substr (ProjectInfo::getUserRights ($project_id, $_SESSION['FSEInfo']['fse_id']), 1, 1) != 't') {
				$error_info = t('You have no right to edit content of this project!');
			}
		}
	}
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only"><?php echo t ('Close') ?></span>
	</button>
	<h4 class="modal-title"><?php echo t ('Add New Feature') ?></h4>
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

	<form id="formNewFeature" method="post" action="<?php echo $form_action ?>" class="validate form-horizontal">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="cID" value="<?php echo $page_id ?>" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="areaHandle" value="<?php echo $_REQUEST['areaHandle'] ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $_REQUEST['partHandle'] ?>" />
		<input type="hidden" name="chapterHandle" value="<?php echo $_REQUEST['chapterHandle'] ?>" />

		<input type="hidden" name="contentType" value="feature" />
		<input type="hidden" name="contentFormat" value="heart" />
		<input type="hidden" name="contentWrapper" value="none" />
		<input type="hidden" name="contentStyle" value="none" />
		<input type="hidden" name="contentAlignment" value="none" />

		<div class="form-group">
			<label for="featureIcon" class="col-md-4 control-label"><?php echo t('Feature Icon: ') ?></label>
			<div class="col-md-8">
				<div class="dropdown">
					<button type="button" class="btn btn-default dropdown-toggle" id="dropdownMenuGlyphicon"
							data-toggle="dropdown">
						<span class="glyphicon glyphicon-heart"></span>
						<span class="caret"></span>
					</button>

					<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenuGlyphicon">
<?php
			$glyphicon_list = array ('heart-empty', 'heart', 'glass', 'leaf', 'magic', 'beach-umbrella',
						'music', 'note', 'fire', 'magnet', 'binoculars', 'road', 'search', 'coins', 'airplane',
						'star-empty', 'star', 'lightbulb', 'umbrella', 'book', 'bookmark', 'cogwheel', 'cogwheels',
						'adjust', 'adjust-alt', 'more', 'electricity', 'euro', 'usd', 'yen', 'gbp', 'sun', 'globe',
						'thumbs-up', 'crown', 'cloud-download', 'cloud-upload', 'send', 'tractor',
						'circle-exclamation-mark', 'circle-ok', 'circle-info', 'circle-question-mark', 'iphone');
			foreach ($glyphicon_list as $glyphicon) {
?>
						<li role="presentation">
							<a class="menuitem" data-value="<?php echo $glyphicon ?>"
									role="menuitem" tabindex="-1" href="#">
								<span class="glyphicon glyphicon-<?php echo $glyphicon ?>"></span>
								<?php echo $glyphicon ?>
							</a>
						</li>
		<?php
			}
		?>
					</ul>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="sectionSubject" class="col-md-4 control-label"><?php echo t('Subject: ') ?></label>
			<div class="col-md-8">
					<?php echo  $form->text ('sectionSubject', '',
					array ("required" => "true", "pattern" => ".{2,40}",
						"class" => "form-control")); ?>
			</div>
		</div>

		<div class="form-group">
			<label for="sectionContent" class="col-md-4 control-label"><?php echo t('Content: ') ?></label>
			<div class="col-md-8">
					<?php echo  $form->text ('sectionContent', '',
					array ("required" => "true", "pattern" => ".{10,255}",
						"class" => "form-control")); ?>
			<p class="help-block"><?php echo t('10 characters at least; Markdown enabled.') ?></p>
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
	<button id="btnNewFeature" type="button" class="btn btn-primary"
			data-doing-text="<?php echo t ('Submitting...') ?>"
			data-done-text="<?php echo t ('Done') ?>">
		<?php echo t ('Ok') ?>
	</button>
<?php
}
?>
</div>

<script type="text/javascript">
$('.dropdown-toggle').dropdown();
$('.menuitem').click (function (event) {
	event.preventDefault ();
	var glyphicon_name = $(this).attr ('data-value');
	$('#dropdownMenuGlyphicon').html ('<span class="glyphicon glyphicon-' + glyphicon_name + '"></span>  <span class="caret"></span>');
	$('#dropdownMenuGlyphicon').dropdown('toggle');
	$('#formNewFeature input[name="contentFormat"]').val (glyphicon_name);
});

$('#formNewFeature').submit (function (e) {
	$('#btnNewFeature').attr ('disabled', 'disabled');
	$('#btnNewFeature').bootstrapBtn ('doing');
});

$('#btnNewFeature').click (function () {
	$('#formNewFeature input[name="inputHiddenSubmit"]').click();
});

</script>
