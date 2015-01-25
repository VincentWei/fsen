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

require_once ('helpers/fsen/FSEInfo.php');
require_once ('helpers/fsen/ProjectInfo.php');

include('inc/head.php');
include('inc/project_frags.php');
?>

<body>

<div class="full-stack">

<?php
include('inc/header.php');
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

	<div class="v-seperator">
	</div>

	<article class="formal-content" lang="<?php echo $doc_lang ?>">
		<section>
			<?php  $a = new Area('Banner'); $a->display($c); ?>
		</section>

		<section class="container-fluid">
			<div class="row">
				<section class="col-sm-6 col-md-9 col-lg-9">
					<header>
						<h1>
							<?php echo t('Top Projects') ?>
						</h1>
					</header>
<?php
$db = Loader::db ();
$projects = $db->getAll ("SELECT * FROM fsen_projects WHERE doc_lang=? AND project_id NOT LIKE 'sys-__' ORDER BY heat_level DESC LIMIT 10", array ($doc_lang));
if (count ($projects) > 0) {
?>
	<ul class="list-group">
<?php
	foreach ($projects as $p) {
		$icon_url = ProjectInfo::getIconURL ($p['icon_file_id']);
		$link = "/$doc_lang/project/" . $p['project_id'];
		$owner_info = FSEInfo::getNameInfo ($p['fse_id']);
?>
		<li class="list-group-item">
			<div class="media">
				<a class="media-left" href="<?php echo $link ?>">
					<img class="middle-icon" src="<?php echo $icon_url ?>" alt="Project Icon">
				</a>
				<div class="media-body" style="width:100%">
					<span class="badge"><?php echo $p['heat_level'] ?></span>
					<h4 class="media-heading"><a href="<?php echo $link ?>"><?php echo h5($p['name']) ?></a></h4>
					<h5 class="media-heading"><?php echo FSEInfo::getPersonalHomeLink ($owner_info, true) ?></h5>
					<p><?php echo h5($p['short_desc']) ?></p>
				</div>
			</div>
		</li>
<?php
	}
?>
	</ul>
<?php
}
?>
				</section>


				<section class="col-sm-6 col-md-3 col-lg-3">
					<header>
						<h1>
							<?php echo t('New Projects') ?>
						</h1>
					</header>
<?php
$db = Loader::db ();
$projects = $db->getAll ("SELECT project_id, fse_id, name FROM fsen_projects WHERE doc_lang=? AND project_id NOT LIKE 'sys-__' ORDER BY create_time DESC LIMIT 10", array ($doc_lang));
if (count ($projects) > 0) {
?>
	<ul class="list-group">
<?php
	foreach ($projects as $p) {
		$link = "/$doc_lang/project/" . $p['project_id'];
		$owner_info = FSEInfo::getNameInfo ($p['fse_id']);
?>
		<li class="list-group-item">
			<h4 class="list-group-item-heading"><a href="<?php echo $link ?>"><?php echo h5($p['name']) ?></a></h4>
			<h5 class="list-group-item-heading"><?php echo FSEInfo::getPersonalHomeLink ($owner_info, true) ?></h5>
		</li>
<?php
	}
?>
	</ul>
<?php
}
?>
						</h1>
					</header>
				</section>
			</div>
		</section>

	</article>

<?php
include('inc/footer.php');
include('inc/status-bar.php');
?>

</div>

<?php
include('inc/dynamic-nav.php');
include('inc/sh-autoloader.php');
Loader::element ('footer_required');
?>

</body>
</html>
