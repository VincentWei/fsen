<?php
defined('C5_EXECUTE') or die("Access Denied.");

require_once ('helpers/fsen/FSEInfo.php');
require_once ('helpers/fsen/ProjectInfo.php');
require_once ('helpers/fsen/DocSectionManager.php');

include('inc/head.php');

$doc_lang = $this->controller->get ('fsenDocLang');
if (!isset ($doc_lang)) {
	$doc_lang = 'en';
}

?>

<body>

<div class="full-stack">

<?php
include('inc/header.php');

$my_user_name = $c->getCollectionHandle ();
$my_user_info = FSEInfo::getBasicProfile ($my_user_name);
if (count ($my_user_info) > 0) {

	$db = Loader::db();
	$my_projects = $db->getAll ("SELECT project_id FROM fsen_projects WHERE fse_id=? AND project_id NOT LIKE 'sys-__'
	UNION SELECT project_id FROM fsen_project_members WHERE fse_id=? AND project_id NOT LIKE 'sys-__'",
			array ($my_user_info['fse_id'], $my_user_info['fse_id']));

	$nr_projects = count ($my_projects);

	$my_blogs = $db->getAll ("SELECT chapter_handle FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id='sys-$doc_lang' AND domain_handle='document' AND volume_handle='blog' AND part_handle=?
	ORDER BY heat_level DESC LIMIT 20",
			array ($my_user_name));
	$nr_blogs = count ($my_blogs);

	$my_discussions = $db->getAll ("SELECT project_id, domain_handle, volume_handle, part_handle, chapter_handle
	FROM fsen_project_doc_volume_part_chapters_all
	WHERE domain_handle='community' AND fse_id=?
	ORDER BY heat_level DESC LIMIT 20",
		array ($my_user_info['fse_id']));
	$nr_discussions = count ($my_discussions);

	$my_posts = $db->getAll ("SELECT project_id, domain_handle, volume_handle, part_handle, chapter_handle,
		id, page_id, curr_ver_code, heat_level
	FROM fsen_document_sections_all
	WHERE author_id=? ORDER BY heat_level DESC LIMIT 20",
		array ($my_user_info['fse_id']));
	$nr_posts = count ($my_posts);

	$my_favorites = $db->getAll ("SELECT section_id
	FROM fsen_document_section_action_comments
	WHERE author_id=? AND action=? ORDER BY create_time DESC LIMIT 20",
		array ($my_user_info['fse_id'], DocSectionManager::COMMENT_ACTION_FAVORITE));
	$nr_favorites = count ($my_favorites);

?>
	<div class="v-seperator">
	</div>

	<article class="formal-content" lang="<?php echo $doc_lang ?>">
		<section>
			<?php  $a = new Area('Banner'); $a->display($c); ?>
		</section>

		<section class="container">
			<div class="row">
				<section class="col-sm-6 col-md-3 col-lg-3">
				<?php  $a = new Area('Side Bar'); $a->display($c); ?>
				</section>

				<section class="col-sm-6 col-md-9 col-lg-9">
				<ul class="list-group">
					<li class="list-group-item active">
						<h2 class="list-group-item-heading">
							<?php echo t('Community Credits') ?>
						</h2>
					</li>
					<li class="list-group-item">
						<span class="badge"><?php echo $my_user_info ['heat_level'] ?></span>
						<?php echo t('Total Points') ?>
					</li>
					<li class="list-group-item">
						<span class="badge"><?php echo $my_user_info ['nr_chapters'] ?></span>
						<?php echo t('Total Chapters/Threas/Questions') ?>
					</li>
					<li class="list-group-item">
						<span class="badge"><?php echo $my_user_info ['nr_sections'] ?></span>
						<?php echo t('Total Sections/Posts') ?>
					</li>
					<li class="list-group-item">
						<span class="badge"><?php echo $my_user_info ['nr_comments'] ?></span>
						<?php echo t('Total Comments') ?>
					</li>
					<li class="list-group-item">
						<span class="badge"><?php echo $my_user_info ['nr_comments_got'] ?></span>
						<?php echo t('Total Gotten Comments') ?>
					</li>
					<li class="list-group-item">
						<span class="badge"><?php echo $my_user_info ['nr_praise_got'] ?></span>
						<?php echo t('Total Gotten Praise') ?>
					</li>
					<li class="list-group-item">
						<span class="badge"><?php echo $my_user_info ['nr_favorites_got'] ?></span>
						<?php echo t('Total Gotten Favorites') ?>
					</li>
				</ul>

				<div class="panel panel-primary">
				<div class="panel-heading">
					<h2 class="panel-title">
						<?php echo t('Recent Activities') ?>
					</h2>
				</div>
				<div class="panel-body">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#divProjects" role="tab" data-toggle="tab">
								<span class="glyphicon glyphicon-settings"></span>
								<span class="hidden-xs"><?php echo t('Projects') ?></span>
							</a>
						</li>
						<li role="presentation">
							<a href="#divBlogs" role="tab" data-toggle="tab">
								<span class="glyphicon glyphicon-pen"></span>
								<span class="hidden-xs"><?php echo t('Blogs') ?></span>
							</a>
						</li>
						<li role="presentation">
							<a href="#divDiscussions" role="tab" data-toggle="tab">
								<span class="glyphicon glyphicon-conversation"></span>
								<span class="hidden-xs"><?php echo t('Discussions') ?></span>
							</a>
						</li>
						<li role="presentation">
							<a href="#divPosts" role="tab" data-toggle="tab">
								<span class="glyphicon glyphicon-notes"></span>
								<span class="hidden-xs"><?php echo t('Posts') ?></span>
							</a>
						</li>
						<li role="presentation">
							<a href="#divFavorites" role="tab" data-toggle="tab">
								<span class="glyphicon glyphicon-heart"></span>
								<span class="hidden-xs"><?php echo t('Favorites') ?></span>
							</a>
						</li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content">
						<!-- Projects -->
						<div role="tabpanel" class="tab-pane fade in active" id="divProjects">
							<div class="v-seperator">
							</div>

<?php
	if ($nr_projects > 0) {
?>
							<ul class="list-group">
<?php
		foreach ($my_projects as $prj) {
			$info = ProjectInfo::getBasicInfo ($prj['project_id']);
			$icon_url = ProjectInfo::getIconURL ($info['icon_file_id']);
			$link = '/' . $info['doc_lang'] . '/project/' . $info['project_id'];
			$owner_info = FSEInfo::getNameInfo ($info['fse_id']);
?>
<li class="list-group-item">
	<div class="media">
		<a class="media-left" href="<?php echo $link ?>">
			<img class="middle-icon" src="<?php echo $icon_url ?>" alt="Project Icon">
		</a>
		<div class="media-body">
			<span class="badge"><?php echo $info['heat_level'] ?></span>
			<h4 class="media-heading"><a href="<?php echo $link ?>"><?php echo h5($info['name']) ?></a></h4>
			<h5 class="media-heading"><?php echo FSEInfo::getPersonalHomeLink ($owner_info, true) ?></h5>
			<p><?php echo h5($info['short_desc']) ?></p>
		</div>
	</div>
</li>
<?php
		}
?>
							</ul>
<?php
	}
	else {
?>
							<div class="alert alert-info alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert">
									<span aria-hidden="true">&times;</span>
									<span class="sr-only">Close</span>
								</button>
								<p>
									<?php echo t('%s has not created or joined any project.', $my_user_info['nick_name']) ?>
								</p>
								<p>
									<a class="btn btn-primary"
											href="/<?php echo $doc_lang ?>/project"><?php echo t('Project List') ?></a>
								</p>
							</div>
<?php
	}
?>
						</div>

						<!-- Blogs -->
						<div role="tabpanel" class="tab-pane fade" id="divBlogs">
							<div class="v-seperator">
							</div>
<?php
	if ($nr_blogs > 0) {
?>
							<ul class="list-group">
<?php
		foreach ($my_blogs as $blg) {
			$info = ProjectInfo::getChapterInfo ("sys-$doc_lang", 'document', 'blog', $my_user_name, $blg['chapter_handle']);
			$link = "/$doc_lang/blog/" . $my_user_name . '/' . $blg['chapter_handle'];
?>
<li class="list-group-item">
	<span class="badge"><?php echo $info['heat_level'] ?></span>
	<h4 class="list-group-item-heading"><a href="<?php echo $link ?>"><?php echo h5($info['chapter_name']) ?></a></h4>
	<p><?php echo h5($info['chapter_desc']) ?></p>
</li>
<?php
		}
?>
							</ul>
<?php
	}
	else {
?>
							<div class="alert alert-info alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert">
									<span aria-hidden="true">&times;</span>
									<span class="sr-only">Close</span>
								</button>
								<p>
									<?php echo t('%s has not written any blog.', $my_user_info['nick_name']) ?>
								</p>
								<p>
									<a class="btn btn-primary"
											href="/<?php echo $doc_lang ?>/blog/<?php echo $my_user_name ?>"><?php echo t('Blog Zone') ?></a>
								</p>
							</div>
<?php
	}
?>
						</div>

						<!-- Discussions -->
						<div role="tabpanel" class="tab-pane fade" id="divDiscussions">
							<div class="v-seperator">
							</div>
<?php
	if ($nr_discussions > 0) {
?>
							<ul class="list-group">
<?php
		foreach ($my_discussions as $dsc) {
			$info = ProjectInfo::getChapterInfo ($dsc['project_id'], $dsc['domain_handle'],
					$dsc['volume_handle'], $dsc['part_handle'], $dsc['chapter_handle']);
			$link = ProjectInfo::assemblePath ($dsc['project_id'], $dsc['domain_handle'],
					$dsc['volume_handle'], $dsc['part_handle'], $dsc['chapter_handle']);
?>
<li class="list-group-item">
	<span class="badge"><?php echo $info['heat_level'] ?></span>
	<h4 class="list-group-item-heading"><a href="<?php echo $link ?>"><?php echo h5($info['chapter_name']) ?></a></h4>
	<p><?php echo h5($info['chapter_desc']) ?></p>
</li>
<?php
		}
?>
							</ul>
<?php
	}
	else {
?>
							<div class="alert alert-info alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert">
									<span aria-hidden="true">&times;</span>
									<span class="sr-only">Close</span>
								</button>
								<p>
									<?php echo t('%s has not been invovled in any dicussion.', $my_user_info['nick_name']) ?>
								</p>
								<p>
									<a class="btn btn-primary"
											href="/<?php echo $doc_lang ?>/community"><?php echo t('Arenas') ?></a>
								</p>
							</div>
<?php
	}
?>
						</div>

						<!-- Posts -->
						<div role="tabpanel" class="tab-pane fade" id="divPosts">
							<div class="v-seperator">
							</div>
<?php
	if ($nr_posts > 0) {
?>
							<ul class="list-group">
<?php
		foreach ($my_posts as $pst) {
			$plain_content = DocSectionManager::getPlainContent ($pst['id'], $pst['curr_ver_code']);
			$link = ProjectInfo::assemblePath ($pst['project_id'], $pst['domain_handle'],
					$pst['volume_handle'], $pst['part_handle'], $pst['chapter_handle']);
			$link .= '#section-' . $pst['id'];
			if (strlen ($plain_content['title']) == 0) {
				$page = Page::getByID ($pst['page_id']);
				$plain_content['title'] = $page->getCollectionName ();
			}
?>
<li class="list-group-item">
	<span class="badge"><?php echo $pst['heat_level'] ?></span>
	<h4 class="list-group-item-heading"><a href="<?php echo $link ?>"><?php echo h5($plain_content['title']) ?></a></h4>
	<p><?php echo $plain_content['content'] ?></p>
</li>
<?php
		}
?>
							</ul>
<?php
	}
	else {
?>
							<div class="alert alert-info alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert">
									<span aria-hidden="true">&times;</span>
									<span class="sr-only">Close</span>
								</button>
								<p>
									<?php echo t('%s has not posted any sections on this site.', $my_user_info['nick_name']) ?>
								</p>
								<p>
									<a class="btn btn-primary" href="/<?php echo $doc_lang ?>/community/>">
										<?php echo t('Arenas') ?>
									</a>
								</p>
							</div>
<?php
	}
?>
						</div>

						<!-- Favorites -->
						<div role="tabpanel" class="tab-pane fade" id="divFavorites">
							<div class="v-seperator">
							</div>
<?php
	if ($nr_favorites > 0) {
?>
							<ul class="list-group">
<?php
		foreach ($my_favorites as $fvr) {
			$info = DocSectionManager::getSectionInfo (false, $fvr['section_id']);
			$plain_content = DocSectionManager::getPlainContent ($fvr['section_id'], $info['curr_ver_code']);
			$link = ProjectInfo::assemblePath ($info['project_id'], $info['domain_handle'],
					$info['volume_handle'], $info['part_handle'], $info['chapter_handle']);
			$link .= '#section-' . $fvr['section_id'];
			if (strlen ($plain_content['title']) == 0) {
				$page = Page::getByID ($info['page_id']);
				$plain_content['title'] = $page->getCollectionName ();
			}
?>
<li class="list-group-item">
	<span class="badge"><?php echo $info['heat_level'] ?></span>
	<h4 class="list-group-item-heading"><a href="<?php echo $link ?>"><?php echo h5($plain_content['title']) ?></a></h4>
	<p><?php echo $plain_content['content'] ?></p>
</li>
<?php
		}
?>
							</ul>
<?php
	}
	else {
?>
							<div class="alert alert-info alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert">
									<span aria-hidden="true">&times;</span>
									<span class="sr-only">Close</span>
								</button>
								<p>
									<?php echo t('%s has not marked any section as his/her favorite.', $my_user_info['nick_name']) ?>
								</p>
								<p>
									<a class="btn btn-primary"
											href="/<?php echo $doc_lang ?>/project"><?php echo t('Project List') ?></a>
								</p>
							</div>
<?php
	}
?>
						</div><!-- .tab-pane -->
					</div><!-- .tab-content -->
				</div><!-- .panel panel-default -->
				</div<!-- .panel-body -->
				</section>
			</div>
		</section>
	</article>

<?php
}
include('inc/footer.php');
?>

</div>

<?php
include('inc/dynamic-nav.php');
Loader::element ('footer_required');
?>

</body>
</html>
