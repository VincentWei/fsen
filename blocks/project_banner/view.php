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

$project_info = ProjectInfo::getBasicInfo ($projectID);
if ($project_info == false) {
	echo "<p>no such project</p>";
}
else {
	$icon_url = File::getRelativePathFromID ($project_info['icon_file_id']);
	if (strlen ($icon_url) < 10) {
		$icon_url = '/files/images/icon-fsen-144.png';
	}

	if ($project_info['repo_location'] == 'github') {
		$repo_frags = explode ('.', $project_info['repo_name']);
		if (count ($repo_frags) == 2) {
			$gh_user = $repo_frags [0];
			$gh_repo = $repo_frags [1];
		}
	}

?>

<div class="row">
	<section class="col-md-3">
			<img class="img-responsive"
				src="<?php echo $icon_url ?>" alt="<?php echo h5($project_info['name']) ?>" />
	</section>

	<section class="col-md-9">
			<h1><?php echo h5($project_info['name']) ?></h1>
			<p><?php echo h5($project_info['short_desc']) ?></p>
<?php if (isset ($gh_user)) { ?>
			<div class="text-left">
				<iframe src="http://ghbtns.com/github-btn.html?user=<?php echo $gh_user ?>&amp;repo=<?php echo $gh_repo ?>&amp;type=watch&amp;count=true&amp;size=large" allowtransparency="true" frameborder="0" scrolling="0" width="170" height="30"></iframe>
				<iframe src="http://ghbtns.com/github-btn.html?user=<?php echo $gh_user ?>&amp;repo=<?php echo $gh_repo ?>&amp;type=fork&amp;count=true&amp;size=large" allowtransparency="true" frameborder="0" scrolling="0" width="170" height="30"></iframe>
				</a>
			</div>
<?php } ?>
	</section>
</div>

<?php
}
?>
