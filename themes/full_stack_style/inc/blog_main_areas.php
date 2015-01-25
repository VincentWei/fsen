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

$comm_request_parts = "?fsenDocLang=$doc_lang&cID=$page_id&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=$part_handle&chapterHandle=$chapter_handle";

?>

<article class="formal-content" lang="<?php echo $doc_lang ?>">

<!-- MainForeword area: Flex columns -->
	<section class="container-fluid">
		<div class="row row-md-flex row-md-flex-wrap">

<?php
$a = Area::getOrCreate ($c, 'MainForeword');
$blocks = $c->getBlocks ('MainForeword');
foreach ($blocks as $block) {
	$block->display ();
}
?>

			<section class="col-md-4 visible-for-specific-user user-<?php echo $part_handle ?> alert alert-info alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<?php echo t('<strong>Tip: </strong>This is the foreword area (flex columns).') ?>
				<a class="dialog-launch"
						title="<?php echo ('Click to add a new section.') ?>"
						dialog-append-buttons="true"
						dialog-modal="false"
						dialog-title="<?php echo ('New Section') ?>"
						dialog-width="80%"
						dialog-height="90%"
						href="/index.php/tools/add_new_section.php?<?php echo $comm_request_parts . '&areaHandle=MainForeword' ?>">
					<span class="glyphicon glyphicon-circle-plus"></span>
				</a>
			</section>

		</div>
	</section>

<!-- Main area -->
	<section class="container-fluid">
		<div class="row">
<?php
$a = Area::getOrCreate ($c, 'Main');
$blocks = $c->getBlocks ('Main');
foreach ($blocks as $block) {
	$block->display ();
}
?>
			<div class="col-md-12 visible-for-specific-user user-<?php echo $part_handle ?> alert alert-info alert-dismissible">
				<button type="button" class="close"
						data-dismiss="alert">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<?php echo t('<strong>Tip: </strong>This is the main area (grid).') ?>
				<a class="dialog-launch"
						title="<?php echo t('Click to add a new section.') ?>"
						dialog-append-buttons="true"
						dialog-modal="false"
						dialog-title="<?php echo t('New Section') ?>"
						dialog-width="80%"
						dialog-height="90%"
						href="/index.php/tools/add_new_section.php<?php echo $comm_request_parts . '&areaHandle=Main' ?>">
					<span class="glyphicon glyphicon-circle-plus"></span>
				</a>
			</div>
		</div><!-- row -->
	</section><!-- container-fluid -->

<!-- MainReversed area -->
	<section class="container-fluid">
		<div class="row">
			<div class="col-md-12 visible-for-specific-user user-<?php echo $part_handle ?> alert alert-info alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<?php echo t('<strong>Tip: </strong>This is the reversed main area (grid; new block shows first.).') ?>
				<a class="dialog-launch"
						title="<?php echo t('Click to add a new section') ?>"
						dialog-append-buttons="true"
						dialog-modal="false"
						dialog-title="<?php echo t('New Section') ?>"
						dialog-width="80%"
						dialog-height="90%"
						href="/index.php/tools/add_new_section.php<?php echo $comm_request_parts . '&areaHandle=MainReversed' ?>">
					<span class="glyphicon glyphicon-circle-plus"></span>
				</a>
			</div>
<?php
$a = Area::getOrCreate ($c, 'MainReversed');
$blocks = array_reverse ($c->getBlocks ('MainReversed'));
foreach ($blocks as $block) {
	$block->display ();
}
?>
		</div>
	</section>

<!-- MainAfterword area: Flex columns -->
	<section class="container-fluid">
		<div class="row row-md-flex row-md-flex-wrap">

<?php
$a = Area::getOrCreate ($c, 'MainAfterword');
$blocks = $c->getBlocks ('MainAfterword');
foreach ($blocks as $block) {
	$block->display ();
}
?>
			<section class="col-md-4 visible-for-specific-user user-<?php echo $part_handle ?> alert alert-info alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">Close</span>
				</button>
				<?php echo t('<strong>Tip: </strong>This is the afterword area (flex columns).') ?>
				<a class="dialog-launch"
						title="<?php echo ('Click to add a new section.') ?>"
						dialog-append-buttons="true"
						dialog-modal="false"
						dialog-title="<?php echo ('New Section') ?>"
						dialog-width="80%"
						dialog-height="90%"
						href="/index.php/tools/add_new_section.php<?php echo $comm_request_parts . '&areaHandle=MainAfterword' ?>">
					<span class="glyphicon glyphicon-circle-plus"></span>
				</a>
			</section>
		</div>
	</section>

</article>


