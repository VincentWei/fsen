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

require_once ('helpers/misc.php');

$db = Loader::db ();
$fse_info = $db->getRow ("SELECT user_name, nick_name, avatar_file_id,
		location_country, location_province, location_district, self_desc,
		public_email, public_url, public_org, DATE(create_time) as joined_date
	FROM fse_basic_profiles WHERE user_name=?", array ($fseUsername));
if (count ($fse_info) == 0) {
	echo "<p>No such user</p>";
}
else {
	$avatar_url = get_url_from_file_id ($fse_info['avatar_file_id'], '/files/images/icon-def-avatar.png');
	$location_country = substr (strstr ($fse_info['location_country'], ':'), 1);
	$location_province = substr (strstr ($fse_info['location_province'], ':'), 1);
	$location_district = substr (strstr ($fse_info['location_district'], ':'), 1);
?>

<section class="public-profile">
	<header>
		<img class="large-avatar" alt="Avatar" src="<?php echo $avatar_url ?>" />
		<h2><?php echo h5($fse_info['nick_name']) ?></h2>
		<p><?php echo $fse_info['user_name'] ?></p>
	</header>
	<section>
		<ul class="profile-items list-unstyled">
			<li class="orgnization"><?php echo h5($fse_info['public_org']) ?></li>
			<li class="location"><?php echo "$location_district, $location_province, $location_country" ?></li>
			<li class="email"><a href="mailto:<?php echo $fse_info['public_email'] ?>"><?php echo $fse_info['public_email'] ?></a></li>
			<li class="url"><a href="<?php echo $fse_info['public_url'] ?>"><?php echo $fse_info['public_url'] ?></a></li>
			<li class="clock">
				<?php echo t('Joined on %s', $fse_info['joined_date']) ?>
			</li>
		</ul>
		<h4>
			<?php echo h5($fse_info['self_desc']) ?>
		</h4>
	</section>
</section>

<?php
}
?>
