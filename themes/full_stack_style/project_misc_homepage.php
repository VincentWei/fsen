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

require_once ('helpers/fsen/ProjectInfo.php');

include('inc/head.php');
include ('inc/project_frags.php');
?>

<body>

<div class="full-stack">

<?php
	include('inc/header.php');
	if ($project_shortname != SYSTEM_PROJECT_SHORTNAME) {
		include ('inc/project_navbar.php');
		include ('inc/project_alert_banner.php');
	}
?>
	<header class="banner-<?php echo $page_style ?>">
		<div class="container">
			<h1>
				<?php echo h5($c->getCollectionName()); ?>
			</h1>
			<p class="lead">
				<?php echo h5($c->getCollectionDescription()); ?>
			</p>
		</div>
	</header>

<?php
	if ($project_shortname != SYSTEM_PROJECT_SHORTNAME) {
		$domain_long_desc = ProjectInfo::getDomainLongDesc ($project_id, $domain_handle);
		$domain_long_desc = DocSectionManager::safeMarkdown2HTML ($domain_long_desc);
?>
	<header class="text-center project-subpage-desc">
		<span class="glyphicon glyphicon-circle-info big-glyph text-major-default"></span><?php echo $domain_long_desc ?>
	</header>
<?php
	}
?>

	<article class="formal-content" lang="<?php echo $doc_lang ?>">

		<section>
			<header>
				<h1><?php echo t('Copyright') ?>
					<span
							class="section-action visible-on-edit-document-right">
						<a class="dialog-launch"
								dialog-append-buttons="true"
								dialog-modal="false"
								dialog-title="<?php echo t('New Section') ?>"
								dialog-width="80%"
								dialog-height="90%"
								href="/index.php/tools/add_new_section.php?<?php echo "fsenDocLang=$doc_lang&cID=$page_id&areaHandle=Copyright&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=na&chapterHandle=na" ?>">
							<m class="glyphicon glyphicon-circle-plus"></m>
						</a>
					</span>
				</h1>
			</header>

			<section class="container-fluid">
			<div class="row">
				<?php  $a = new Area('Copyright'); $a->display($c); ?>
			</div>
			</section>

		</section>

		<section>
			<header>
				<h1><?php echo t('Members') ?>
					<span class="section-action visible-on-manage-member-right">
						<a class="launch-modal"
								href="/index.php/tools/add_new_member.php?<?php echo "fsenDocLang=$doc_lang&cID=$page_id&areaHandle=Members&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=na&chapterHandle=na" ?>">
							<m class="glyphicon glyphicon-circle-plus"></m>
						</a>
					</span>
				</h1>
			</header>

			<section class="container">
				<?php  $a = new Area('Members'); $a->display($c); ?>
			</section>

		</section>

		<section>
			<header>
				<h1><?php echo t('Acknowledgement') ?>
					<span class="section-action visible-on-edit-document-right">
						<a class="dialog-launch"
								dialog-append-buttons="true"
								dialog-modal="false"
								dialog-title="<?php echo t('New Section') ?>"
								dialog-width="80%"
								dialog-height="90%"
								href="/index.php/tools/add_new_section.php?<?php echo "fsenDocLang=$doc_lang&cID=$page_id&areaHandle=Acknowledgement&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=na&chapterHandle=na" ?>">
							<m class="glyphicon glyphicon-circle-plus"></m>
						</a>
					</span>
				</h1>
			</header>

			<section class="container-fluid">
			<div class="row">
				<?php  $a = new Area('Acknowledgement'); $a->display($c); ?>
			</div>
			</section>
		</section>

	</article>

<?php
	if ($project_shortname != SYSTEM_PROJECT_SHORTNAME) {
		include 'inc/project_footer.php';
	}
	include('inc/footer.php');
	include('inc/status-bar.php');
?>

</div>

<?php
include ('inc/dynamic-nav.php');
include('inc/sh-autoloader.php');
Loader::element ('footer_required');
?>

</body>
</html>
