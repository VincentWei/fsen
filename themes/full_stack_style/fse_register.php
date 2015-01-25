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

$doc_lang = $this->controller->get ('fsenDocLang');
if (!isset ($doc_lang)) {
	$doc_lang = 'en';
}
?>

<body>

<div class="full-stack">
	<header class="global-header">
		<section class="header-block block-left">
			<a href="/" title="Logo" id="logo">
				<p>To be</p>
				<p><span>Full</span> <span>Stack</span> <span>Engineer</span></p>
			</a>
		</section>
		<section class="header-block block-right">
	       		<nav>
		       		<ul class="new-nav-tab" id="GlobalNavList">
					<li><a class="inline-list" href="<?php echo $doc_lang ?>/help"><?php echo t('Help') ?></a></li>
					<li><a class="button" href="/fse_login"><?php echo t('Sign in') ?></a></li>
		       		</ul>
	       		</nav>
		</section>
	</header>

	<article class="ccm-ui">
<?php
if (isset($error)) {
	if ($error instanceof Exception) {
		$_error[] = $error->getMessage();
	} else if ($error instanceof ValidationErrorHelper) {
		$_error = array();
		if ($error->has()) {
			$_error = $error->getList();
		}
	} else {
		$_error = $error;
	}

	if (count($_error) > 0) {
		Loader::element('system_errors', array('format' => 'block', 'error' => $_error));
	}
}

if (isset($message)) {
?>
		<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">×</button>
<?php echo nl2br(Loader::helper('text')->entities($message))?>
		</div>

<?php
} else if (isset($success)) {
?>
		<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>
<?php echo nl2br(Loader::helper('text')->entities($success))?>
		</div>
<script lang="javascript">
window.setTimeout (function () {
		window.location.href = "/fse_login"
	}, 5000);
</script>
<?php
}
?>
	</article>

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
