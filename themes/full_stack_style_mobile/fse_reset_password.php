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

include ('inc/head.php');

$doc_lang = $this->controller->get ('fsenDocLang');
if (!isset ($doc_lang)) {
	$doc_lang = 'en';
}
?>

<body>
<div class="full-stack">
	<header class="global-header">
		<section class="header-block block-left">
			<a href="/" title="Logo" id="logo"><p
				>To be</p><p><span>Full</span> <span>Stack</span> <span>Engineer</span></p></a>
		</section>
		<section class="header-block block-right">
			<nav>
				<button id="btnToggleGlobalDropdown" class="btn btn-default" type="button">
					<span class="caret"></span>
				</button>
				<ul id="ulGlobalNavList" style="display:none;">
					<li>
						<a class="menu-item"
								href="/<?php echo $doc_lang ?>/project" ><?php echo t('Projects') ?></a>
					</li>
					<li>
						<a class="menu-item"
								href="/<?php echo $doc_lang ?>/community"><?php echo t('Code Arena') ?></a>
					</li>
					<li>
						<a class="menu-item"
								href="/<?php echo $doc_lang ?>/blog"><?php echo t('Blogs') ?></a>
					</li>
					<li>
						<a class="menu-item"
								href="/<?php echo $doc_lang ?>/help"><?php echo t('Help') ?></a>
					</li>
				</ul>
	       	</nav>
		</section>
	</header>

	<article class="form-content">
		<?php  $a = new Area('Main'); $a->display($c); ?>
	</article>

<?php
	include('inc/footer.php');
?>

</div>

<?php
Loader::element ('footer_required');
?>

</body>
</html>
