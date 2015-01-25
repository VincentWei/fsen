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

$navItems = $controller->getNavItems();
$page = Page::getByID ($controller->displayPagesCID);
?>

<nav class="block-menu">
	<header>
		<h2><?php echo t($page->getCollectionName()); ?></h2>
	</header>

	<ul>

<?php
foreach ($navItems as $ni) {
	if ($ni->isCurrent) {
		$span_class = 'menu-item-checked';
	}
	else {
		$span_class = 'menu-item-none';
	}
?>
		<li class="list-unstyled">
			<a href="<?php echo $ni->url?>"
				target="<?php echo $ni->target?>"><p><span
					class="<?php echo $span_class ?>"></span><?php echo t($ni->name) ?></p></a>
		</li>
<?php  } ?>
	</ul>
</nav>

