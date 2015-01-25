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

$items = array (
				'none' => 'None',
				'col-md-1' => 'Bootstrap 1/12 Column',
				'col-md-2' => 'Bootstrap 2/12 Column',
				'col-md-3' => 'Bootstrap 3/12 Column',
				'col-md-4' => 'Bootstrap 4/12 Column',
				'col-md-5' => 'Bootstrap 5/12 Column',
				'col-md-6' => 'Bootstrap 6/12 Column',
				'col-md-7' => 'Bootstrap 7/12 Column',
				'col-md-8' => 'Bootstrap 8/12 Column',
				'col-md-9' => 'Bootstrap 9/12 Column',
				'col-md-10' => 'Bootstrap 10/12 Column',
				'col-md-11' => 'Bootstrap 11/12 Column',
				'col-md-12' => 'Bootstrap 12/12 Column',
			);
foreach ($items as $item_key => $item_value) {
?>
	<option value="<?php echo $item_key ?>"
		<?php echo ($selected_value == $item_key)?'selected="selected"':''; ?>><?php echo $item_value ?></option>
<?php
}
?>
