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
				'gpl-2.0' => 'GNU General Public License (GPL-2.0)',
				'gpl-3.0' => 'GNU General Public License 3.0 (GPL-3.0)',
				'lgpl-2.1' => 'GNU Library or "Lesser" General Public License 2.1 (LGPL-2.1)',
				'lgpl-3.0' => 'GNU Library or "Lesser" General Public License 3.0 (LGPL-3.0)',
				'agpl-3.0' => 'GNU Affero General Public License v3 (AGPL-3.0)',

				'apache-2.0' => 'Apache License 2.0 (Apache-2.0)',
				'mpl-2.0' => 'Mozilla Public License 2.0 (MPL-2.0)',
				'php-3.0' => 'PHP License 3.0 (PHP-3.0)',
				'epl-1.0' => 'Eclipse Public License 1.0 (EPL-1.0)',
				'postgresql' => 'The PostgreSQL License (PostgreSQL)',
				'python-2.0' => 'Python License (Python-2.0)',
				'xnet' => 'X.Net License (Xnet)',
				'w3c' => 'W3C License (W3C)',
				'zope' => 'Zope Public License 2.0 (ZPL-2.0)',
				'zlib' => 'zlib/libpng license (Zlib)',
				'lppl-1.3c' => 'LaTeX Project Public License 1.3c (LPPL-1.3c)',
				'qpl-1.0' => 'Qt Public License (QPL-1.0)',
				'wxwindows' => 'wxWindows Library License (WXwindows)',

				'osl-3.0' => 'Open Software License 3.0 (OSL-3.0)',
				'ofl-1.1' => 'Open Font License 1.1 (OFL-1.1)',
				'ecl-2.0' => 'Educational Community License, Version 2.0 (ECL-2.0)',
				'apl-3.0' => 'Academic Free License 3.0 (AFL-3.0)',
				'apl-1.0' => 'Adaptive Public License (APL-1.0)',
				'artistic-2.0' => 'Artistic license 2.0 (Artistic-2.0)',

				'bsd-3-clause' => 'BSD 3-Clause "New" or "Revised" License (BSD-3-Clause)',
				'bsd-2-clause' => 'BSD 2-Clause "Simplified" or "FreeBSD" License (BSD-2-Clause)',
				'mit' => 'MIT License (MIT)',
				'ncsa' => 'University of Illinois/NCSA Open Source License (NCSA)',

				'nasa-1.3' => 'NASA Open Source Agreement 1.3 (NASA-1.3)',
				'ipl-1.0' => 'IBM Public License 1.0 (IPL-1.0)',
				'spl-1.0' => 'Sun Public License 1.0 (SPL-1.0)',
				'apsl-2.0' => 'Apple Public Source License (APSL-2.0)',
				'ms-pl' => 'Microsoft Public License (MS-PL)',
				'public' => 'Public Domain',
			);
foreach ($items as $item_key => $item_value) {
?>
	<option value="<?php echo $item_key ?>"
		<?php echo ($selected_value == $item_key)?'selected="selected"':''; ?>><?php echo $item_value ?></option>
<?php
}
?>
