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
				'actionscript3' => 'ActionScript3',
				'bash' => 'Bash/shell',
				'coldfusion' => 'ColdFusion',
				'csharp' => 'C#',
				'cpp' => 'C/C++',
				'css' => 'CSS',
				'pascal' => 'Pascal/Delphi',
				'diff' => 'Diff/Patch',
				'erlang' => 'Erlang',
				'go' => 'Go',
				'groovy' => 'Groovy',
				'javascript' => 'JavaScript',
				'java' => 'Java',
				'javafx' => 'JavaFX',
				'perl' => 'Perl',
				'php' => 'PHP',
				'plain' => 'Plain Text',
				'powershell' => 'PowerShell',
				'python' => 'Python',
				'ruby' => 'Ruby',
				'scala' => 'Scala',
				'sql' => 'SQL',
				'vb' => 'Visual Basic',
				'xml' => 'XML',
			);

if ($output_type == 'dropdown-menu') {
?>
<div class="btn-group">
	<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
		<?php echo $label ?>
		<m><?php echo $items[$selected] ?></m>
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu">
<?php
	foreach ($items as $item_key => $item_value) {
?>
		<li>
			<a class="menuitem" href="#" data-value="<?php echo $item_key ?>" data-target="<?php echo $data_target ?>">
				<?php echo $item_value ?>
			</a>
		</li>
<?php
	}
?>
	</ul>
</div>
<?php
}
else {
	foreach ($items as $item_key => $item_value) {
?>
<option value="<?php echo $item_key ?>"
	<?php echo ($selected_value == $item_key)?'selected="selected"':''; ?>><?php echo $item_value ?>
</option>
<?php
	}
}
?>
