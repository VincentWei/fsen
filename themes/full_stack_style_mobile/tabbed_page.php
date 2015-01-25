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

include('inc/head.php');

$parent_page_id = $c->getCollectionParentID();

$nr_tabs = 0;
for ($i = 0; $i < 7; $i++) {
	$title_tabs [$i] = $c->getAttribute ('title_tab_' . $i);
	if (strlen ($title_tabs [$i]) > 0)
		$nr_tabs ++;
	else
		break;
}
?>

<body>
<div class="full-stack">
<?php
	include('inc/head.php');
?>
	<article class="formal-content">
		<section>
			<?php  $a = new Area('Main'); $a->display($c); ?>
		</section>

		<section class="tab-container">
			<nav class="tabs">
				<ul data-current="0">
					<li data-value="0" class="tab-active"><a href="#tab0" class="tab-item"><?php echo $title_tabs [0] ?></a></li>
<?php
for ($i = 1; $i < $nr_tabs; $i++) {
?>
					<li data-value="<?php echo $i ?>"><a href="#tab<?php echo $i ?>" class="tab-item"><?php echo $title_tabs [$i] ?></a></li>
<?php
}
?>
				</ul>
			</nav>
			<section class="tabscontent">
<?php
for ($i = 0; $i < $nr_tabs; $i++) {
?>
				<a name="tab<?php echo $i ?>"></a>
				<article class="tabpage" id="tab<?php echo $i ?>" <?php echo ($i != 0)?'style="display:none;"':'' ?>>
					<?php  $a = new Area('Tab ' . $i); $a->display($c); ?>
				</article>
<?php
}
?>
			</section>
		</section>
	</article>

<?php
	include('inc/footer.php');
?>
</div>

<script lang="javascript">
var nav_links = $("a.tab-item");
nav_links.click (function (event) {
	event.preventDefault ();

	var $parent_ul = $(this).parents ("ul");
	var $current_li = $(this).parent ();
	$parent_ul.children ().removeClass ("tab-active");
	$parent_ul.attr ("data-current", $current_li.attr ("data-value"));
	$current_li.addClass ("tab-active");

	var href = $(this).attr ("href");
	$("article.tabpage").hide ();
	$(href).show ();
});

</script>

<?php
include('inc/dynamic-nav.php');
Loader::element ('footer_required');
?>

</body>
</html>

