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
require_once ('helpers/Michelf/Markdown.inc.php');
require_once ('helpers/Michelf/MarkdownExtra.inc.php');
require_once ('helpers/WikiParser/wikiParser.class.php');
require_once ('libraries/htmLawed.php');

class DocSectionManager {
	const EC_OK 			= 0;
	const EC_BAD_DOMAIN 	= 1;
	const EC_BAD_PAGEAREA 	= 2;
	const EC_TOO_SHORT 		= 3;
	const EC_FILE 			= 4;
	const EC_DATABASE 		= 5;
	const EC_NO_SUCH_OBJ 	= 6;
	const EC_ON_NEW_BLOCK 	= 7;
	const EC_NO_SUCH_ENTRY	= 8;
	const EC_INV_ARG 		= 9;

	const SS_AUTHOR_DELETED	= 1;
	const SS_ADMIN_DELETED	= 2;
	const SS_ADMIN_SHIELDED	= 3;

	const MAX_NR_CACHED_COMMENTS		= 20;
	const SECTION_CACHE_EXPIRED_TIME	= 900; /* 15 minutes */
	const COMMENT_CACHE_EXPIRED_TIME	= 900; /* 15 minutes */
	const ACTION_CACHE_EXPIRED_TIME		= 600; /* 10 minutes */

	const COMMENT_ACTION_TEXT_MIN	= 0;
	const COMMENT_ACTION_TEXT_MAX	= 100;

	const COMMENT_ACTION_TEXT		= 0;
	const COMMENT_ACTION_PRAISE		= 101;
	const COMMENT_ACTION_SHARE		= 102;
	const COMMENT_ACTION_FAVORITE	= 103;
	const COMMENT_ACTION_DISLIKE	= 104;
	const COMMENT_ACTION_PASSBY		= 105;

	const MAX_LEN_COMMENT_BODY		= 140;
	const MAX_ATTACHED_FILES		= 4;
	const MAX_REFERENCE_LINKS		= 4;
	const MIN_LINK_STRLEN			= 10;

	const ACTION_COMMENT_BASE_ID	= 4611686018427387904;

	const RESERVED_TAGS		= '<a><br><hr><abbr><span><b><em><strong><cite><code><del><dfn><i><ins><kbd><m><mark><meter><s><small><sub><sup><time><u><var><wbr><pre>';

	public static $mContentTypeList = array ('plain', 'pre', 'code', 'quotation', 'quotation-reverse', 'address', 'feature', 'member', 'post', 'post-question', 'post-answer', 'post-note', 'post-reference');
	private $mDomainList = array ('home', 'download', 'document', 'community', 'contribute', 'misc');
	private $mErrorInfo = array (
			'Ok',
			'Bad domain.',
			'Bad page area.',
			'Too short content.',
			'File operation failure.',
			'Database error.',
			'No such object.',
			'Failed to create a new CMS block.',
			'No such entry in database.',
			'Invalid arguments.',
		);

	public function getErrorMessage ($err_code) {
		return t($this->mErrorInfo [$err_code]);
	}

	public static function getContentTypeHandle ($content_type, $content_format, $content_code_lang,
								$content_wrapper, $content_style, $content_alignment) {
		if (in_array ($content_type, self::$mContentTypeList)
				&& preg_match ("/^(none|col-md-([1-9]|1[0-2]))$/", $content_wrapper)
				&& preg_match ("/^(none|well|default|primary|success|info|warning|danger)$/", $content_style)
				&& preg_match ("/^(none|left|center|right|justify|nowrap)$/", $content_alignment)) {
			switch ($content_type) {
			case 'code':
			case 'post-answer':
				$type_handle = "$content_type:$content_code_lang";
				break;
			case 'pre':
				$type_handle = "$content_type:plain";
				break;
			case 'feature':
			case 'member':
				$type_handle = "$content_type:$content_format";
				$content_wrapper = 'none';
				$content_style = 'none';
				break;
			default:
				$type_handle = "$content_type:$content_format";
				break;
			}
		}
		else {
			return false;
		}

		$type_handle .= ":$content_wrapper:$content_style:$content_alignment";
		return $type_handle;
	}

	public static function getSectionContentPath ($content_file, $ver_code, $suffix)
	{
		$full_content_path_name = DIR_BASE . '/files/fsen/sections/';
		$full_content_path_name .= $content_file[0] . '/';
		$full_content_path_name .= $content_file[1] . '/';
		$full_content_path_name .= $content_file[2] . '/';
		if (!is_dir ($full_content_path_name)) {
			if (!mkdir ($full_content_path_name, 0755, true)) {
				return FALSE;
			}
		}
		$full_content_path_name .= "$content_file-$ver_code.$suffix";
		return $full_content_path_name;
	}

	protected function org2html ($section_subject, $section_content, $type_handle)
	{
		$fragments = explode (':', $type_handle);
		$type = $fragments [0];

		if ($type == 'code' || $type == 'post-answer') {
			$code_lang = $fragments [1];
			$format = 'plain';
		}
		else if ($type == 'feature') {
			$feature_icon = $fragments [1];
			$format = 'markdown';
		}
		else {
			$format = $fragments [1];
		}

		switch ($format) {
		case 'media_wiki':
			$wiki = new WikiParser ();
			$section_content_formatted = $wiki->parse ($section_content);
			break;

		case 'markdown':
			//$section_content = strip_tags ($section_content, self::RESERVED_TAGS);
			$section_content_formatted = \Michelf\Markdown::defaultTransform ($section_content);
			$config = array('safe'=>1);
			$section_content_formatted = htmLawed($section_content_formatted, $config);
			break;

		case 'markdown_extra':
			//$section_content = strip_tags ($section_content, self::RESERVED_TAGS);
			$section_content_formatted = \Michelf\MarkdownExtra::defaultTransform ($section_content);
			$config = array('safe'=>1);
			$section_content_formatted = htmLawed($section_content_formatted, $config);
			break;

		case 'markdown_safe':
			$section_content_formatted = \Michelf\MarkdownExtra::defaultTransform ($section_content);
			$config = array('safe'=>1);
			$section_content_formatted = htmLawed($section_content_formatted, $config);
			break;

		default:
			$section_content_formatted = htmlspecialchars ($section_content, ENT_NOQUOTES | ENT_HTML5);
			break;
		}

		switch ($type) {
		case 'feature':
			$section_subject_formatted = htmlspecialchars ($section_subject, ENT_NOQUOTES | ENT_HTML5);
			$section_content_html = "<div class=\"feature\">\n";
			$section_content_html .= "\t<div class=\"feature-icon\">\n";
			$section_content_html .= "\t\t<span class=\"glyphicon glyphicon-" . $feature_icon . "\"></span>\n";
			$section_content_html .= "\t</div>\n";
			$section_content_html .= "\t<h2>" . $section_subject_formatted . "</h2>\n";
			$section_content_html .= "\t$section_content_formatted\n";
			$section_content_html .= "</div>\n";
			break;

		case 'post':
		case 'plain':
		case 'post-question':
		case 'post-note':
		case 'post-reference':
			$section_content_html = "<div>\n" . $section_content_formatted . "\n</div>\n";
			break;

		case 'quotation':
			$section_content_html = "<blockquote>\n" . $section_content_formatted . "\n</blockquote>\n";
			break;

		case 'quotation-reverse':
			$section_content_html = "<blockquote class=\"blockquote-reverse\">\n" . $section_content_formatted . "\n</blockquote>\n";
			break;

		case 'address':
			$section_content_html = "<address>\n" . $section_content_formatted . "\n</address>\n";
			break;

		case 'code':
		case 'post-answer':
			$section_content_html = "<pre class=\"brush:$code_lang\">\n";
			$section_content_html .= $section_content_formatted;
			$section_content_html .= "\n</pre>\n";
			break;

		case 'member':
			$style = $fragments[3];
			$section_content_html = "<div class=\"panel panel-$style\">\n" . $section_content_formatted . "\n</div>\n";
			break;

		case 'pre':
		default:
			$section_content_html = "<pre>\n" . $section_content_formatted . "\n</pre>\n";
			break;

		}

		return $section_content_html;
	}

	/* Transfer Markdown Extra to safe HTML */
	public static function safeMarkdown2HTML ($content) {
		$content = strip_tags ($content, self::RESERVED_TAGS);

		/* TODO: filter after transformed to avoid XSS */
		return \Michelf\Markdown::defaultTransform ($content);
	}

	/*
	 * Create section entry and add a new section block into the specific page
	 * Return: error code; EC_OK on success.
	 */
	public function addNewSection ($author_id, $page_id, $area_handle,
			$project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle,
			$type_handle, $section_subject, $section_content, $attached_files)
	{
		if (!in_array ($domain_handle, $this->mDomainList)) {
			return self::EC_BAD_DOMAIN;
		}

		$page = Page::getByID ($page_id);
		$area = Area::getOrCreate ($page, $area_handle);
		if (!is_object ($area)) {
			return self::EC_BAD_PAGEAREA;
		}

		$content_file_name = hash_hmac ("md5", microtime () . rand (), $author_id);
		$full_content_path_name = $this->getSectionContentPath ($content_file_name, '0', '');

		$fp = fopen ($full_content_path_name . 'org', "w");
		if ($fp === FALSE) {
			return self::EC_FILE;
		}
		fwrite ($fp, "$author_id\n");
		fwrite ($fp, "$type_handle\n");
		fwrite ($fp, "$attached_files\n");
		fwrite ($fp, h5($section_subject) . "\n");
		fwrite ($fp, h5($section_content));
		fclose ($fp);

		$section_content_html = $this->org2html ($section_subject, $section_content, $type_handle);

		$fp = fopen ($full_content_path_name . 'html', "w");
		if ($fp === FALSE) {
			return self::EC_FILE;
		}
		fwrite ($fp, $section_content_html);
		fclose ($fp);

		$block_type = BlockType::getByHandle ("document_section");
		$block = $page->addBlock ($block_type, $area,
			array ("domainHandle" => $domain_handle, "sectionID" => $content_file_name, "currentVerCode" => 0));
		if (!($block instanceof Block)) {
			return self::EC_ON_NEW_BLOCK;
		}

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$query = "INSERT IGNORE fsen_document_sections_$doc_lang (id, author_id, page_id, area_handle, block_id,
		project_id, domain_handle, volume_handle, part_handle, chapter_handle, create_time, update_time)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
		$res = $db->Execute ($query, array ($content_file_name, $author_id, $page_id, $area_handle, $block->getBlockID(),
			$project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle));
		if ($db->Affected_Rows () == 0) {
			return self::EC_DATABASE;
		}

		/* purge the full page cache */
		$page_cache = PageCache::getLibrary();
		$page_cache->purge($page);

		return self::EC_OK;
	}

	/*
	 * Add a section version, i.e., edit a section.
	 * Return: error code; EC_OK on success.
	 */
	public function addNewSectionVersion ($project_id, $author_id, $domain_handle, $section_id,
		$type_handle, $section_subject, $section_content, $attached_files)
	{
		if (!in_array ($domain_handle, $this->mDomainList)) {
			return self::EC_BAD_DOMAIN;
		}

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$section_row = $db->getRow ("SELECT page_id, area_handle, block_id, max_ver_code
	FROM fsen_document_sections_$doc_lang WHERE id=?", array ($section_id));
		if (count ($section_row) == 0) {
			return self::EC_NO_SUCH_ENTRY;
		}

		$page = Page::getByID ($section_row ['page_id']);
		$area = Area::get ($page, $section_row ['area_handle']);
		if (!is_object ($area)) {
			return self::EC_BAD_PAGEAREA;
		}

		$block = Block::getByID ($section_row['block_id'], $page, $section_row ['area_handle']);
		if (!($block instanceof Block)) {
			return self::EC_NO_SUCH_OBJ;
		}

		$content_file_name = $section_id;
		$new_ver_code = $section_row ['max_ver_code'] + 1;
		$full_content_path_name = $this->getSectionContentPath ($content_file_name, $new_ver_code, '');

		$fp = fopen ($full_content_path_name . 'org', "w");
		if ($fp === FALSE) {
			return self::EC_FILE;
		}
		fwrite ($fp, "$author_id\n");
		fwrite ($fp, "$type_handle\n");
		fwrite ($fp, "$attached_files\n");
		fwrite ($fp, h5($section_subject) . "\n");
		fwrite ($fp, h5($section_content));
		fclose ($fp);

		$section_content_html = $this->org2html ($section_subject, $section_content, $type_handle);

		$fp = fopen ($full_content_path_name . 'html', "w");
		if ($fp === FALSE) {
			return self::EC_FILE;
		}
		fwrite ($fp, $section_content_html);
		fclose ($fp);

		$query = "UPDATE fsen_document_sections_$doc_lang
	SET max_ver_code=max_ver_code+1, curr_ver_code=max_ver_code WHERE id=?";
		$res = $db->Execute ($query, array ($section_id));
		if ($db->Affected_Rows () == 0) {
			return self::EC_DATABASE;
		}

		$block->update (array ("domainHandle" => $domain_handle, "sectionID" => $content_file_name,
			"currentVerCode" => $new_ver_code));

		self::updateCachedSectionInfo ($domain_handle, $section_id, 'max_ver_code', $new_ver_code);

		/* purge the full page cache */
		$page_cache = PageCache::getLibrary();
		$page_cache->purge($page);

		return self::EC_OK;
	}

	/*
	 * Set version code of a section
	 * Return: error code; EC_OK on success.
	 */
	public function setSectionVersion ($project_id, $domain_handle, $section_id, $current_ver_code)
	{
		if (!in_array ($domain_handle, $this->mDomainList)) {
			return self::EC_BAD_DOMAIN;
		}

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$section_row = $db->getRow ("SELECT page_id, area_handle, block_id, max_ver_code
	FROM fsen_document_sections_$doc_lang WHERE id=?", array ($section_id));
		if (count ($section_row) == 0) {
			return self::EC_NO_SUCH_ENTRY;
		}

		if ($current_ver_code > $section_row ['max_ver_code']) {
			return self::EC_INV_ARG;
		}

		$db->Execute ("UPDATE fsen_document_sections_$doc_lang SET curr_ver_code=? WHERE id=?",
					array ($current_ver_code, $section_id));
		$page = Page::getByID ($section_row ['page_id']);
		$area = Area::get ($page, $section_row ['area_handle']);
		if (!is_object ($area)) {
			return self::EC_BAD_PAGEAREA;
		}

		$block = Block::getByID ($section_row['block_id'], $page, $section_row ['area_handle']);
		if (!($block instanceof Block)) {
			return self::EC_NO_SUCH_OBJ;
		}
		$block->update (array ("domainHandle" => $domain_handle, "sectionID" => $section_id,
			"currentVerCode" => $current_ver_code));

		self::updateCachedSectionInfo ($domain_handle, $section_id, 'curr_ver_code', $new_ver_code);

		/* purge the full page cache */
		$page_cache = PageCache::getLibrary();
		$page_cache->purge($page);

		return self::EC_OK;
	}

	/*
	 * Delete a section block
	 * Return: error code; EC_OK on success.
	 */
	public function deleteSection ($project_id, $domain_handle, $section_id)
	{
		if (!in_array ($domain_handle, $this->mDomainList)) {
			return self::EC_BAD_DOMAIN;
		}

		$db = Loader::db ();
		$doc_lang = substr ($project_id, -2);
		$section_row = $db->getRow ("SELECT page_id, area_handle, block_id, max_ver_code
	FROM fsen_document_sections_$doc_lang WHERE id=?", array ($section_id));
		if (count ($section_row) == 0) {
			return self::EC_NO_SUCH_ENTRY;
		}

		$res = $db->Execute ("DELETE FROM fsen_document_sections_$doc_lang WHERE id=?", array ($section_id));

		$page = Page::getByID ($section_row ['page_id']);
		$area = Area::get ($page, $section_row ['area_handle']);
		if (!is_object ($area)) {
			return self::EC_BAD_PAGEAREA;
		}

		$block = Block::getByID ($section_row['block_id'], $page, $section_row ['area_handle']);
		if (!($block instanceof Block)) {
			return self::EC_NO_SUCH_OBJ;
		}

		$block->delete();

		/* purge the full page cache */
		$page_cache = PageCache::getLibrary();
		$page_cache->purge($page);

		return self::EC_OK;
	}

	public static function getPlainContent ($section_id, $curr_ver_code, $max_length = 140) {
		$plain_content = array ();

		$filename = self::getSectionContentPath ($section_id, $curr_ver_code, 'org');
		$fp = fopen ($filename, "r");
		if ($fp) {
			$plain_content['author_id'] = trim (fgets ($fp));
			$tmp = trim (fgets ($fp));
			$tmp = fgets ($fp);
			$plain_content['title'] = trim (fgets ($fp));
			$fstats = fstat($fp);
			$plain_content['edit_time'] = date ('Y-m-d H:i', $fstats['mtime']) . ' CST';
			fclose ($fp);
			unset ($fp);
			unset ($fstats);
			unset ($tmp);
		}
		else {
			return false;
		}

		$filename = DocSectionManager::getSectionContentPath ($section_id, $curr_ver_code, 'html');
		$html_content = file_get_contents ($filename);
		if ($html_content) {
			$plain_content['content'] = strip_tags ($html_content);
			if (mb_strlen ($plain_content['content']) > $max_length) {
				$plain_content['content'] = mb_substr ($plain_content['content'], 0, $max_length) . '…';
			}
		}

		return $plain_content;
	}

	/*
	 * Get detailed info of the specified section
	 * Return: false on error
	 */
	public static function getSectionInfo ($domain_handle, $section_id)
	{
		$section_info = Cache::get ('SectionInfo', $section_id);
		if ($section_info == false) {
			$db = Loader::db ();
			$section_info = $db->getRow ("SELECT * FROM fsen_document_sections_all WHERE id=?", array ($section_id));
			if (count ($section_info) == 0) {
				return false;
			}
			Cache::set ('SectionInfo', $section_id, $section_info, self::SECTION_CACHE_EXPIRED_TIME);
		}

		return $section_info;
	}

	public static function updateCachedSectionInfo ($domain_handle, $section_id, $field, $value) {
		$section_info = Cache::get ('SectionInfo', $section_id);
		if ($section_info != false) {
			$section_info [$field] = $value;
			Cache::set ('SectionInfo', $section_id, $section_info, self::SECTION_CACHE_EXPIRED_TIME);
		}
	}

	/*
	 * get all action comments on the specified section
	 * Return: actionis (empty array for none)
	 */
	public static function getActionComments ($domain_handle, $section_id, $fse_id)
	{
		$actions = Cache::get ('ActionComments', $section_id . $fse_id);
		if ($actions === false) {
			$db = Loader::db ();
			$actions = $db->getAll ("SELECT id, action FROM fsen_document_section_action_comments
	WHERE section_id=? AND author_id=?", array ($section_id, $fse_id));
			Cache::set ('ActionComments', $section_id . $fse_id, $actions, self::ACTION_CACHE_EXPIRED_TIME);
		}

		return $actions;
	}

	/*
	 * get all action comments on the specified section
	 * Return: actionis (empty array for none)
	 */
	public static function checkActionComment ($domain_handle, $section_id, $fse_id, $action)
	{
		$actions = self::getActionComments ($domain_handle, $section_id, $fse_id);

		foreach ($actions as $a) {
			if ($a['action'] == $action) {
				return $a['id'];
			}
		}

		return false;
	}
	/*
	 * Get comment info of the specified section
	 * Return: false on error
	 */
	public static function getSectionCommentInfo ($domain_handle, $section_id)
	{
		$section_info = self::getSectionInfo ($domain_handle, $section_id);
		if ($section_info == false) {
			return false;
		}

		$section_comment_info ['section_id'] = $section_id;
		$section_comment_info ['nr_shares'] = $section_info ['nr_shares'];
		$section_comment_info ['nr_comments'] = $section_info ['nr_comments'];
		$section_comment_info ['nr_praise'] = $section_info ['nr_praise'];
		$section_comment_info ['nr_favorites'] = $section_info ['nr_favorites'];
		$section_comment_info ['nr_dislike'] = $section_info ['nr_dislike'];
		$section_comment_info ['nr_bypass'] = $section_info ['nr_bypass'];
		$section_comment_info ['status'] = $section_info ['status'];
		$section_comment_info ['create_time'] = $section_info ['create_time'];
		$section_comment_info ['update_time'] = $section_info ['update_time'];
		$section_comment_info ['heat_level'] = $section_info ['heat_level'];

		$section_comment_info ['praised'] = 0;
		$section_comment_info ['shared'] = 0;
		$section_comment_info ['favorited'] = 0;
		$section_comment_info ['disliked'] = 0;
		$section_comment_info ['bypassed'] = 0;

		if (isset ($_SESSION['FSEInfo'])) {
			$actions = self::getActionComments ($domain_handle, $section_id, $_SESSION['FSEInfo']['fse_id']);
			foreach ($actions as $action) {
				switch ($action['action']) {
				case self::COMMENT_ACTION_PRAISE:
					$section_comment_info ['praised'] = 1;
					break;
				case self::COMMENT_ACTION_SHARE:
					$section_comment_info ['shared'] = 1;
					break;
				case self::COMMENT_ACTION_FAVORITE:
					$section_comment_info ['favorited'] = 1;
					break;
				case self::COMMENT_ACTION_DISLIKE:
					$section_comment_info ['disliked'] = 1;
					break;
				case self::COMMENT_ACTION_BYPASS:
					$section_comment_info ['bypassed'] = 1;
					break;
				}
			}
		}

		return $section_comment_info;
	}

	/*
	 * Get info of the specified comment
	 * Return: false on error
	 */
	public static function getCommentInfo ($domain_handle, $section_id, $comment_id)
	{
		$comment_info = Cache::get ('CommentInfo', $comment_id);
		if ($comment_info == false) {

			$db = Loader::db ();
			if ($comment_id < self::ACTION_COMMENT_BASE_ID) {
				$comment_info = $db->getRow ("SELECT A.author_id, A.reply_to, A.replied_author_id, A.action, A.create_time,
			B.author_id AS section_author_id
	FROM fsen_document_section_comments AS A, fsen_document_sections_all AS B
	WHERE A.id=? AND A.section_id=B.id", array ($comment_id));
			}
			else {
				$comment_info = $db->getRow ("SELECT A.author_id, A.action, A.create_time, B.author_id AS section_author_id
	FROM fsen_document_section_action_comments AS A, fsen_document_sections_all AS B
	WHERE A.id=? AND A.section_id=B.id", array ($comment_id));
			}

			if (count ($comment_info) == 0) {
				return false;
			}

			Cache::set ('CommentInfo', $comment_id, $comment_info, self::COMMENT_CACHE_EXPIRED_TIME);
		}

		return $comment_info;
	}

	/*
	 * Get cached comments of the specified section
	 * Return: false on error
	 */
	public static function getCachedComments ($domain_handle, $section_id)
	{
		$comments = Cache::get ('SectionComments', $section_id);
		if ($comments == false) {
			$db = Loader::db ();
			$comments = $db->getAll ("SELECT * FROM fsen_document_section_comments
	WHERE section_id=? ORDER BY create_time DESC LIMIT ?",
					array ($section_id, self::MAX_NR_CACHED_COMMENTS));
			Cache::set ('SectionComments', $section_id, $comments, self::SECTION_CACHE_EXPIRED_TIME);
		}

		return $comments;
	}

	/*
	 * Get cached action comments of the specified section
	 * Return: false on error
	 */
	public static function getCachedActionComments ($domain_handle, $section_id)
	{
		$comments = Cache::get ('SectionActionComments', $section_id);
		if ($comments == false) {
			$db = Loader::db ();
			$comments = $db->getAll ("SELECT * FROM fsen_document_section_action_comments
	WHERE section_id=? ORDER BY create_time DESC LIMIT ?",
					array ($section_id, self::MAX_NR_CACHED_COMMENTS));
			Cache::set ('SectionActionComments', $section_id, $comments, self::SECTION_CACHE_EXPIRED_TIME);
		}

		return $comments;
	}

	/*
	 * Add a new comment to the specified section
	 * Return: false on error
	 */
	public static function newComment ($domain_handle, $section_id, $author_id, $action, $body, $reply_to = NULL)
	{
		$section_info = self::getSectionInfo ($domain_handle, $section_id);
		if (count ($section_info) == 0) {
			return false;
		}

		if ($reply_to !== NULL) {
			$replied_info = self::getCommentInfo ($domain_handle, $section_id, (int)$reply_to);
			if (count ($replied_info) > 0) {
				$replied_author_id = $replied_info ['author_id'];
			}
		}

		switch ((int)$action) {
			case self::COMMENT_ACTION_PRAISE:
				$update_field = 'nr_praise';
				$body = NULL;
				break;
			case self::COMMENT_ACTION_SHARE:
				$update_field = 'nr_shares';
				$body = NULL;
				break;
			case self::COMMENT_ACTION_FAVORITE:
				$update_field = 'nr_favorites';
				$body = NULL;
				break;
			case self::COMMENT_ACTION_DISLIKE:
				$update_field = 'nr_dislike';
				$body = NULL;
				break;
			case self::COMMENT_ACTION_PASSBY:
				$update_field = 'nr_passby';
				$body = NULL;
				break;
			default:
				if ($action > self::COMMENT_ACTION_TEXT_MAX || $action < self::COMMENT_ACTION_TEXT_MIN)
					$action = self::COMMENT_ACTION_TEXT;
				$update_field = 'nr_comments';
				$body_len = mb_strlen ($body);
				if ($body_len == 0 || $body_len > self::MAX_LEN_COMMENT_BODY) {
					return false;
				}
				break;
		}

		/* get cached comments first */
		if ($action > self::COMMENT_ACTION_TEXT_MAX) {
			Cache::delete ('ActionComments', $section_id . $author_id);
			$comments = self::getCachedActionComments ($domain_handle, $section_id);
		}
		else {
			$comments = self::getCachedComments ($domain_handle, $section_id);
		}

		/* update db then */
		$db = Loader::db ();
		if ($action <= self::COMMENT_ACTION_TEXT_MAX) {
			$query = "INSERT INTO fsen_document_section_comments
	(section_id, reply_to, replied_author_id, author_id, action, body, create_time)
	VALUES (?, ?, ?, ?, ?, ?, NOW())";
			$db->Execute ($query, array ($section_id, $reply_to, $replied_author_id, $author_id, $action, $body));
		}
		else {
			$query = "INSERT IGNORE fsen_document_section_action_comments
	(section_id, author_id, action, create_time)
	VALUES (?, ?, ?, NOW())";
			$db->Execute ($query, array ($section_id, $author_id, $action));
		}

		if ($db->Insert_ID () == 0) {
			return false;
		}
		$new_comment_id = $db->Insert_ID();

		$doc_lang = substr ($section_info ['project_id'], -2);
		$update_query = "UPDATE fsen_document_sections_$doc_lang SET $update_field=$update_field+1 WHERE id=?";
		$db->Execute ($update_query, array ($section_id));

		/* update cache last */
		$section_info [$update_field] = $section_info [$update_field] + 1;
		Cache::set ('SectionInfo', $section_id, $section_info, self::SECTION_CACHE_EXPIRED_TIME);

		$new_comment ['id'] = $new_comment_id;
		$new_comment ['section_id'] = $section_id;
		$new_comment ['author_id'] = $author_id;
		$new_comment ['action'] = $action;
		$new_comment ['create_time'] = date ("Y-m-d H:i:s");
		if ($action <= self::COMMENT_ACTION_TEXT_MAX) {
			$new_comment ['reply_to'] = $reply_to;
			$new_comment ['replied_author_id'] = $replied_author_id;
			$new_comment ['body'] = $body;
		}

		array_unshift ($comments, $new_comment);
		if (count ($comments) > self::MAX_NR_CACHED_COMMENTS) {
			array_pop ($comments);
		}

		if ($action > self::COMMENT_ACTION_TEXT_MAX) {
			Cache::set ('SectionActionComments', $section_id, $comments, self::SECTION_CACHE_EXPIRED_TIME);
		}
		else {
			Cache::set ('SectionComments', $section_id, $comments, self::SECTION_CACHE_EXPIRED_TIME);
		}

		return $new_comment;
	}

	/*
	 * Cancel a comment to the specified section
	 * Return: false on error
	 */
	public static function cancelComment ($domain_handle, $section_id, $comment_id)
	{
		$section_info = self::getSectionInfo ($domain_handle, $section_id);
		if (count ($section_info) == 0) {
			return false;
		}

		$comment_info = self::getCommentInfo ($domain_handle, $section_id, $comment_id);
		if ($comment_info == false) {
			return false;
		}

		$org_action = $comment_info ['action'];
		switch ($org_action) {
			case self::COMMENT_ACTION_TEXT:
				$update_field = 'nr_comments';
				break;
			case self::COMMENT_ACTION_PRAISE:
				$update_field = 'nr_praise';
				break;
			case self::COMMENT_ACTION_DISLIKE:
				$update_field = 'nr_dislike';
				break;
			case self::COMMENT_ACTION_SHARE:
				$update_field = 'nr_shares';
				break;
			case self::COMMENT_ACTION_FAVORITE:
				$update_field = 'nr_favorites';
				break;
			case self::COMMENT_ACTION_PASSBY:
				$update_field = 'nr_passby';
				break;
			default:
				$update_field = 'nr_comments';
				break;
		}

		/* get comments first */
		if ($comment_id >= self::ACTION_COMMENT_BASE_ID) {
			$comments = self::getCachedActionComments ($domain_handle, $section_id);
		}
		else {
			$comments = self::getCachedComments ($domain_handle, $section_id);
		}

		/* update db then */
		$db = Loader::db ();
		if ($comment_id >= self::ACTION_COMMENT_BASE_ID) {
			$db->Execute ("DELETE FROM fsen_document_section_action_comments WHERE id=?", array ($comment_id));
		}
		else {
			$db->Execute ("DELETE FROM fsen_document_section_comments WHERE id=?", array ($comment_id));
		}

		if ($db->Affected_Rows () == 0) {
			return false;
		}

		$doc_lang = substr ($section_info ['project_id'], -2);
		$update_query = "UPDATE fsen_document_sections_$doc_lang SET $update_field=$update_field-1 WHERE id=?";
		$db->Execute ($update_query, array ($section_id));
		if ($db->Affected_Rows () == 0) {
			return false;
		}

		/* update cache last */
		$section_info [$update_field] = $section_info [$update_field] - 1;
		Cache::set ('SectionInfo', $section_id, $section_info, self::SECTION_CACHE_EXPIRED_TIME);

		$i = 0;
		foreach ($comments as $comment) {
			if ($comment ['id'] == $comment_id) {
				unset ($comments [$i]);
				$comments = array_values ($comments);
				break;
			}
			$i += 1;
		}
		if ($comment_id >= self::ACTION_COMMENT_BASE_ID) {
			Cache::delete ('ActionComments', $section_id . $comment_info['author_id']);
			Cache::set ('SectionActionComments', $section_id, $comments, self::SECTION_CACHE_EXPIRED_TIME);
		}
		else {
			Cache::delete ('CommentInfo', $comment_id);
			Cache::set ('SectionComments', $section_id, $comments, self::SECTION_CACHE_EXPIRED_TIME);
		}

		return $section_info;
	}

	/*
	 * Get comments older than earlier_comment_id
	 * Return: empty array on error
	 */
	public static function getEarlierComments ($domain_handle, $section_id, $earlier_comment_id)
	{
		$db = Loader::db ();
		return $db->getAll ("SELECT * FROM fsen_document_section_comments
	WHERE id<? AND section_id=? ORDER BY create_time DESC LIMIT ?",
				array ($earlier_comment_id, $section_id, self::MAX_NR_CACHED_COMMENTS));
	}

	/*
	 * Get action comments older than earlier_comment_id
	 * Return: empty array on error
	 */
	public static function getEarlierActionComments ($domain_handle, $section_id)
	{
		$db = Loader::db ();
		return $db->getAll ("SELECT * FROM fsen_document_section_action_comments
	WHERE id<? AND section_id=? ORDER BY create_time DESC LIMIT ?",
				array ($earlier_comment_id, $section_id, self::MAX_NR_CACHED_COMMENTS));
	}
}

