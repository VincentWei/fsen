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

require_once ('helpers/misc.php');
require_once ('helpers/fsen/ProjectInfo.php');

function output_domain_struct ($project_id, $domain_handle, $domain_title, $domain_desc, $domain_long_desc)
{
	$doc_lang = $_REQUEST['fsenDocLang'];
	if (!isset ($doc_lang)) {
		$doc_lang = 'en';
	}

	if (in_array ($domain_handle, array ('document', 'community'))) {
		$dialog_href = "/index.php/tools/add_new_doc_part.php?fsenDocLang=$doc_lang&projectID=$project_id&domainHandle=$domain_handle";
	}
	else {
		$dialog_href = "/index.php/tools/add_new_doc_volume.php?fsenDocLang=$doc_lang&projectID=$project_id&domainHandle=$domain_handle";
	}

?>
<h2><span
		title="Doc domain name."
		contenteditable="true"
		data-project="<?php echo $project_id ?>"
		data-domain="<?php echo $domain_handle ?>"
		data-item="name"
		data-org="<?php echo h5($domain_title) ?>"><?php echo h5($domain_title) ?></span>
		<a
				href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle) ?>"><span
						class="glyphicon glyphicon-link"></span></a>
			(<span
					title="<?php echo t('Page domain\'s short description, which will be displayed on project homepage.') ?>"
					contenteditable="true"
					data-project="<?php echo $project_id ?>"
					data-domain="<?php echo $domain_handle ?>"
					data-item="desc"
					data-org="<?php echo h5($domain_desc) ?>"><?php echo h5($domain_desc) ?></span>)
<?php
	if ($domain_handle != 'home') {
?>
		<span>
			<a class="launch-modal" href="<?php echo $dialog_href ?>"> (&#10010;)</a>
		</span>
<?php
	}
?>
	</h2>

	<p><span
		title="<?php echo t('Page domain\'s long description, which will be displayed on domain homepage. Markdown enabled.') ?>"
		contenteditable="true"
		data-project="<?php echo $project_id ?>"
		data-domain="<?php echo $domain_handle ?>"
		data-item="long_desc"
		data-org="<?php echo h5($domain_long_desc) ?>"><?php echo h5($domain_long_desc) ?></span></p>
<?php
	$db = Loader::db ();
	$volumes = $db->getAll ("SELECT volume_handle, volume_name, volume_desc FROM fsen_project_doc_volumes
	WHERE project_id=? AND domain_handle=? ORDER BY display_order", array ($project_id, $domain_handle));
	echo '<div>';
	foreach ($volumes as $v) {
?>
	<p style="margin-left:10px;"><span
		title="<?php echo t('Volume name') ?>"
		contenteditable="true"
		data-project="<?php echo $project_id ?>"
		data-domain="<?php echo $domain_handle ?>"
		data-volume="<?php echo $v['volume_handle'] ?>"
		data-item="name"
		data-org="<?php echo h5($v['volume_name']) ?>"><?php echo h5($v['volume_name']) ?></span> (<span
			title="<?php echo t('Volume description') ?>"
			contenteditable="true"
			data-project="<?php echo $project_id ?>"
			data-domain="<?php echo $domain_handle ?>"
			data-volume="<?php echo $v['volume_handle'] ?>"
			data-item="desc"
			data-org="<?php echo h5($v['volume_desc']) ?>"><?php echo h5($v['volume_desc']) ?></span>)</p>
<?php
		$parts = $db->getAll ("SELECT part_handle, part_name, part_desc FROM fsen_project_doc_volume_parts
	WHERE project_id=? AND domain_handle=? AND volume_handle=? ORDER BY display_order",
			array ($project_id, $domain_handle, $v['volume_handle']));
		foreach ($parts as $p) {
?>
	<p style="margin-left:20px;"><span
		title="<?php echo t('Part name') ?>"
		contenteditable="true"
		data-project="<?php echo $project_id ?>"
		data-domain="<?php echo $domain_handle ?>"
		data-volume="<?php echo $v['volume_handle'] ?>"
		data-part="<?php echo $p['part_handle'] ?>"
		data-item="name"
		data-org="<?php echo h5($p['part_name']) ?>"><?php echo h5($p['part_name']) ?></span> (<span
			title="<?php echo t('Part description') ?>"
			contenteditable="true"
			data-project="<?php echo $project_id ?>"
			data-domain="<?php echo $domain_handle ?>"
			data-volume="<?php echo $v['volume_handle'] ?>"
			data-part="<?php echo $p['part_handle'] ?>"
			data-item="desc"
			data-org="<?php echo h5($p['part_desc']) ?>"><?php echo h5($p['part_desc']) ?></span>)</p>
<?php
		}
	}
echo '</div>';
}
?>

<div class="modal fade" id="modalConfirmToDeleteProject" tabindex="-1"
		role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Delete Project</h4>
			</div>
			<div class="modal-body">
				<h4 class="lead text-center text-danger">
					<span class="glyphicon glyphicon-circle-exclamation-mark"></span>
				</h4>
				<p>
					<?php echo t('Once your delete this project, all pages, documents, forum threads, and sections will be lost and the project identifier will be available for others. <strong class="text-uppercase">Be sure to confirm your request to delete this project!</strong>') ?>
				</p>

				<form id="formDeleteProject" method="post" action="/fse_settings/projects/delete_project">
					<input name="projectID" type="hidden" />
					<div class="form-group">
						<label for="confirmDeleteProject">
							<?php echo t('Confirm your request: ') ?>
						</label>
						<input type="text" class="form-control" name="confirmDeleteProject" required="true" />
						<p class="help-block">
							<?php echo t('Type in the following phrase in above field: <em>delete this project</em>') ?>
						</p>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="btnDeleteProject" type="button" disabled="disabled" class="btn btn-danger"
					data-doing-text="Deleting"
					data-done-text="Deleted"><?php echo t('Delete!') ?></button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<form id="formNEWPROJECT" method="post" action="/fse_settings/projects/new_project">
	<fieldset class="flat">
		<header>
			<h2>
				<?php echo t('Projects') ?>
			</h2>
		</header>

<?php
$db = Loader::db ();
$projects = $db->getAll ("SELECT project_id, name, short_desc, icon_file_id,
		page_theme, repo_location, repo_name, code_lang, doc_lang, platform, license
	FROM fsen_projects WHERE fse_id=?", array ($_SESSION ['FSEInfo']['fse_id']));

if (count ($projects) > 0) {
?>
		<section class="block-list-with-icon">
			<ul>
<?php
	foreach ($projects as $project) {
		$title_license = t('License');
		$title_code_lang = t('Language');
		$title_platform = t('Platform');
		$title_page_theme = t('Page Theme');
		$title_repo_location = t('Repo. Location');
		$title_repo_name = t('Repo. Name');

		$icon_url = get_url_from_file_id ($project['icon_file_id'], '/files/images/icon-fsen-144.png');
?>
<li>
	<a class="item-icon" onclick="chooseIcon ('<?php echo $project['project_id'] ?>');" href="#"><img
		id="icon-<?php echo $project['project_id'] ?>"
		data-org="<?php echo $project['icon_file_id'] ?>"
		title="Click to change the icon"
		src="<?php echo $icon_url ?>" alt="<?php echo $project['name'] ?>"></a>
<?php
		if (!preg_match ("/^sys-[a-z]{2}$/", $project['project_id'])) {
?>
	<a class="item-manage awesome red" data-value="<?php echo $project['project_id'] ?>" href="#"><?php echo t('Delete') ?></a>
<?php
		}
?>
	<div class="item-desc">
		<h2><span contenteditable="true"
			data-project="<?php echo $project['project_id'] ?>"
			data-org="<?php echo h5($project['name']) ?>"
			data-item="name"><?php echo h5($project['name']) ?></span></h2>
		<p><span contenteditable="true"
			data-project="<?php echo $project['project_id'] ?>"
			data-org="<?php echo h5($project['short_desc']) ?>"
			data-item="short_desc"><?php echo h5($project['short_desc']) ?></span></p>
	</div>
</li>

<h1>
	<?php echo t('General Information') ?>
</h1>
<div class="form-horizontal">
	<div class="form-group form-group-sm">
		<label for="selectLicense" class="col-md-3 control-label"><?php echo $title_license ?></label>
		<div class="col-md-7">
			<select name="selectLicense" class="general-item form-control input-sm"
					data-project="<?php echo $project['project_id'] ?>"
					data-item="license">
					<?php Loader::element('oss_license_list', array ('selected_value' => $project['license'])); ?>
			</select>
		</div>
	</div>
	<div class="form-group form-group-sm">
		<label for="selectLanguage" class="col-md-3 control-label"><?php echo $title_code_lang ?></label>
		<div class="col-md-7">
			<select name="selectLanguage" class="general-item form-control"
					data-project="<?php echo $project['project_id'] ?>"
					data-item="code_lang">
					<?php Loader::element('computer_language_list', array ('selected_value' => $project['code_lang'])); ?>
			</select>
		</div>
	</div>
	<div class="form-group form-group-sm">
		<label for="selectPlatform" class="col-md-3 control-label"><?php echo $title_platform ?></label>
		<div class="col-md-7">
			<select name="selectPlatform" class="general-item form-control"
					data-project="<?php echo $project['project_id'] ?>"
					data-item="platform">
					<?php Loader::element('software_platform_list', array ('selected_value' => $project['platform'])); ?>
			</select>
		</div>
	</div>
	<div class="form-group form-group-sm">
		<label for="selectPageTheme" class="col-md-3 control-label"><?php echo $title_page_theme ?></label>
		<div class="col-md-7">
			<select name="selectPageTheme" class="general-item form-control"
					data-project="<?php echo $project['page_theme'] ?>"
					data-item="page_theme">
					<?php Loader::element('project_page_theme_list', array ('selected_value' => $project['page_theme'])); ?>
			</select>
		</div>
	</div>
	<div class="form-group form-group-sm">
		<label for="selectRepoLocation" class="col-md-3 control-label"><?php echo $title_repo_location ?></label>
		<div class="col-md-7">
			<select name="selectRepoLocation" class="general-item form-control"
					data-project="<?php echo $project['project_id'] ?>"
					data-item="repo_location">
					<?php Loader::element('repository_location_list',
						array ('selected_value' => $project['repo_location'])); ?>
			</select>
		</div>
	</div>
	<div class="form-group form-group-sm">
		<label for="inputRepoName" class="col-md-3 control-label"><?php echo $title_repo_name ?></label>
		<div class="col-md-7">
			<input name="inputRepoName" type="text" class="general-item form-control"
					data-project="<?php echo $project['project_id'] ?>"
					data-org="<?php echo h5($project['repo_name']) ?>"
					data-item="repo_name"
					placeholder="username.repo" value="<?php echo h5($project['repo_name']) ?>" />
		</div>
	</div>
</div>

<h1>
	<?php echo t('Project Page Structure') ?>
</h1>
<div class="clearfix">
<?php
	$project_id = $project['project_id'];
	foreach (ProjectInfo::$mDomainList as $domain_handle) {
		if (ProjectInfo:: getDomainName ($project_id, $domain_handle) != false) {
			output_domain_struct($project_id, $domain_handle,
					ProjectInfo::getDomainName ($project_id, $domain_handle),
					ProjectInfo::getDomainDesc ($project_id, $domain_handle),
					ProjectInfo::getDomainLongDesc ($project_id, $domain_handle));
		}
	}
?>
</div>

<?php
	}
?>
			</ul>
		</section>
<?php
}
else {
?>
		<section class="note">
			<p>
				<?php echo t('You have not created any project.') ?>
			</p>
		</section>
<?php
}
?>
		<hr />

		<section class="fieldBase">
			<?php echo t('Name') ?>
			<input name="name" type="text" maxlength="32"
				pattern=".{4,32}" required="true"
				title="4 to 32 characters"
				placeholder="My Project" />
		</section>
		<section class="description">
<?php echo t('The name of your project (required).') ?>
		</section>

		<section class="fieldBase">
			<?php echo t('Short name') ?>
			<input name="shortName" type="text" maxlength="60"
				pattern="[a-z0-9\-]{4,60}" required="true"
				title="pattern: [a-z0-9\-]{4,60}"
				placeholder="my-project" />
		</section>
		<section class="description">
<?php echo t('The unique short name of your project (required; letters and digits only, lowercases).') ?>
		</section>

		<section class="fieldBase">
			<?php echo t('Doc Lang') ?>
			<select name="docLang">
				<option value="en">English</option>
				<option value="zh">中文</option>
			</select>
		</section>
		<section class="description">
<?php echo t('Choose the default language for your project pages.') ?>
		</section>

		<section class="fieldBase">
<?php
			$al = Loader::helper('concrete/asset_library');
			echo $al->file('projectIcon', 'iconFileID', t('Upload or import project icon'));
?>
		</section>
		<section class="description">
<?php echo t('Please upload your project icon (required; 200*200 square image recommended).') ?>
		</section>

		<section class="fieldBase">
			<?php echo t('Description') ?>
			<input name="shortDesc" type="text" maxlength="255"
				pattern=".{5,255}" required="true"
				title="5 to 255 characters"
				placeholder="This project aims to ..." />
		</section>
		<section class="description">
<?php echo t('The short description of your project (required).') ?>
		</section>

		<section class="fieldBase">
			<?php echo t('Page Theme') ?>
			<select name="pageTheme">
					<?php Loader::element('project_page_theme_list'); ?>
			</select>
		</section>
		<section class="description">
<?php echo t('The page theme of your project.') ?>
		</section>

		<section class="fieldBase">
			<?php echo t('Repo. Location') ?>
			<select name="repoLocation">
					<?php Loader::element('repository_location_list'); ?>
			</select>
		</section>
		<section class="description">
<?php echo t('Only GitHub supported so far.') ?>
		</section>

		<section class="fieldBase">
			<?php echo t('Repo. Name') ?>
			<input name="repoName" type="text" maxlength="255"
				placeholder="username.repo" />
		</section>
		<section class="description">
<?php echo t('Repository Name (e.g., VincentWei.fsen).') ?>
		</section>

		<section class="fieldBase">
			<?php echo t('Code Lang') ?>
			<select name="codeLang">
					<?php Loader::element('computer_language_list'); ?>
			</select>
		</section>

		<section class="fieldBase">
			<?php echo t('Platform') ?>
			<select name="platform">
					<?php Loader::element('software_platform_list'); ?>
			</select>
		</section>

		<section class="fieldBase">
			<?php echo t('License') ?>
			<select name="license">
					<?php Loader::element('oss_license_list'); ?>
			</select>
		</section>

		<section class="fieldBase transparent">
			<input type="submit" value="<?php echo t('Create New Project') ?>" />
		</section>
		<section class="right-note">
<?php echo t('Note that your primary email address should be verified before you can create a new project.') ?>
<br/>
<?php echo t('It will take some time (about one minute) to create a project. Please wait patiently.') ?>
		</section>
	</fieldset>
</form>

<?php Loader::element('page_action_status_bar'); ?>

<script type="text/javascript">
var bootstrapButton = $.fn.button.noConflict();
$.fn.bootstrapBtn = bootstrapButton;

$('#formDeleteProject input[name="confirmDeleteProject"]').on ('input', function (e) {
	if ($(this).val() == 'delete this project') {
		$('#btnDeleteProject').removeAttr ('disabled');
	}
	else {
		$('#btnDeleteProject').attr ('disabled', 'disabled');
	}
});

$('#btnDeleteProject').click (function () {
	$(this).attr ('disabled', 'disabled');
	$(this).bootstrapBtn ('doing');
	$('#formDeleteProject').submit ();
});

$('.item-manage').click (function (event) {
	event.preventDefault ();

	var project_id = $(this).attr ("data-value");
	$("#formDeleteProject input[name='projectID']").val (project_id);
	$("#modalConfirmToDeleteProject").modal ();
});

$('select.general-item').change (function () {
	var project_id = $(this).attr ("data-project");
	var item_name = $(this).attr ("data-item");
	var item_value = $(this).val ();
	$.post ("/fse_settings/projects/change_item", {
					projectID: project_id,
					itemName: item_name,
					itemValue: item_value
				}, display_pas);
});

$('input.general-item').blur (function () {
	var project_id = $(this).attr ("data-project");
	var item_name = $(this).attr ("data-item");
	var item_org_value = $(this).attr ("data-org");
	var item_value = $(this).val ();
	if (item_value != item_org_value) {
		$(this).attr ("data-org", item_value);
		$.post ("/fse_settings/projects/change_item", {
					projectID: project_id,
					itemName: item_name,
					itemValue: item_value
				}, display_pas);
	}
});

$('span[contenteditable=true]').blur (function () {
	var project_id = $(this).attr ("data-project");
	var item_domain = $(this).attr ("data-domain");
	var item_volume = $(this).attr ("data-volume");
	if (item_domain == undefined || item_domain == null || item_domain == '') {
		var item_name = $(this).attr ("data-item");
		var item_org_value = $(this).attr ("data-org");
		var item_value = $(this).text ();
		if (item_value != item_org_value) {
			$(this).attr ("data-org", item_value);
			$.post ("/fse_settings/projects/change_item", {
					projectID: project_id,
					itemName: item_name,
					itemValue: item_value
				}, display_pas);
		}
	}
	else if (item_volume == undefined || item_volume == null || item_volume == '') {
		var item_type = $(this).attr ("data-item");
		var item_org_value = $(this).attr ("data-org");
		var item_value = $(this).text ();
		if (item_value != item_org_value) {
			$(this).attr ("data-org", item_value);
			$.post ("/fse_settings/projects/change_doc_domain_name_desc", {
					projectID: project_id,
					itemDomain: item_domain,
					itemType: item_type,
					itemValue: item_value
				}, display_pas);
		}
	}
	else {
		var item_volume = $(this).attr ("data-volume");
		var item_part = $(this).attr ("data-part");
		var item_type = $(this).attr ("data-item");
		var item_org_value = $(this).attr ("data-org");
		var item_value = $(this).text ();
		if (item_value != item_org_value) {
			$(this).attr ("data-org", item_value);
			$.post ("/fse_settings/projects/change_doc_part_name_desc", {
					projectID: project_id,
					itemDomain: item_domain,
					itemVolume: item_volume,
					itemPart: item_part,
					itemType: item_type,
					itemValue: item_value
				}, display_pas);
		}
	}
});

var curr_project_id = '';
function onChooseFile (obj) {
	if (obj.fID != undefined) {
		var $img_obj = $('#icon-' + curr_project_id);
		var org_file_id = $img_obj.attr ("data-org");
		if (org_file_id != obj.fID) {
			$img_obj.attr ('data-org', '' + obj.fID);
			$img_obj.attr ('src', obj.filePathDirect);
			$.post ("/fse_settings/projects/change_item", {
					projectID: curr_project_id,
					itemName: 'icon_file_id',
					itemValue: obj.fID
				}, display_pas);
		}
	}
};

function chooseIcon (project_id) {
	curr_project_id = project_id;
	ccm_chooseAsset = onChooseFile;


	var bootstrapButton = $.fn.button.noConflict();
	$.fn.bootstrapBtn = bootstrapButton;
	ccm_launchFileManager ('&fType=' + ccmi18n_filemanager.FTYPE_IMAGE);
}

</script>

