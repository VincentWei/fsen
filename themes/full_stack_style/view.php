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

<?php
	include('inc/header.php');
?>

	<article style="min-height:500px" lang="<?php echo $doc_lang ?>">

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
		if ($_error instanceof Exception) {
			$my_error[] = $error->getMessage();
		} else if ($_error instanceof ValidationErrorHelper) {
			$my_error = $error->getList();
		} else if (is_array($_error)) {
			$my_error = $error;
		} else if (is_string($_error)) {
			$my_error[] = $error;
		}
?>
		<div class="v-seperator">
		</div>

		<div class="container">
			<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>
			<?php foreach($my_error as $e) { ?>
				<?php echo $e ?><br/>
			<?php } ?>
			</div>
		</div>
<?php
	}
}

if (isset ($message)) {
?>
		<div class="v-seperator">
		</div>

		<div class="container">
			<div class="alert alert-info">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<?php echo nl2br(Loader::helper('text')->entities($message))?>
			</div>
		</div>
<?php
} else if (isset($success)) {
?>
		<div class="v-seperator">
		</div>

		<div class="container">
			<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>
				<?php echo nl2br(Loader::helper('text')->entities($success))?>
			</div>
		</div>

<?php
}

if (isset($error) || (!isset($message) && !isset($success))) {
?>
		<div class="banner-global">
			<div class="container-fluid">
				<h1>
					<?php echo t('WE ARE VERY SORRY!') ?>
				</h1>
				<p class="lead">
					<?php echo t('You are here because of') ?>
				</p>
				<ul class="lead">
					<li><?php echo t('You want to reset your password duplicatedly. Or') ?></li>
					<li><?php echo t('You want to validate your email address duplicaately. Or') ?></li>
					<li><?php echo t('You made a bad request. Or') ?></li>
					<li><?php echo t('The page you requested have been removed.') ?></li>
				</ul>
				<p class="lead">
					<?php echo t('Please return home and find some new intersting things.') ?>
				</p>
				<p class="lead">
					<a href="/" class="btn btn-primary btn-lg"><?php echo t('Back to Home.') ?></a>
				</p>
			</div>
		</div>
<?php
}
else {
?>
		<div class="container">
			<p class="lead">
				<a href="/" class="btn btn-primary btn-lg"><?php echo t('Back to Home.') ?></a>
			</p>
		</div>
<?php
}
?>

		<div class="v-seperator">
		</div>

	</article>

<?php
	include('inc/footer.php');
?>

</div>

<?php
include('inc/dynamic-nav.php');
Loader::element ('footer_required');
?>

</body>
</html>
