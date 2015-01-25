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
				'linux' => 'Linux/Unix',
				'lamp' => 'LAMP (PHP without framework)',
				'codeigniter' => 'CodeIgniter (PHP)',
				'cakephp' => 'CakePHP (PHP)',
				'zend' => 'Zend (PHP)',
				'django' => 'Django (Python)',
				'rubyonrails' => 'Ruby on Rails (Ruby)',
				'android' => 'Android (Java)',
				'ios-oc' => 'iOS (Objective C)',
				'ios-swift' => 'iOS (Swift)',
				'windows-mfc' => 'Windows MFC (C++)',
				'windows-net' => 'Windows .Net',
				'node.js' => 'Node.js (JavaScript)',
				'j2ee' => 'J2EE',
				'none' => 'Not Specified',
			);
foreach ($items as $item_key => $item_value) {
?>
	<option value="<?php echo $item_key ?>"
		<?php echo ($selected_value == $item_key)?'selected="selected"':''; ?>><?php echo $item_value ?></option>
<?php
}
?>
