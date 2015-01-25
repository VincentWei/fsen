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
 *      http://www.fullstackengineer.net/
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

DELIMITER $

-- QUERY START

-- INSERT COMMENT
DROP TRIGGER IF EXISTS fsen_on_new_comment;
CREATE TRIGGER fsen_on_new_comment AFTER INSERT ON fsen_document_section_comments
FOR EACH ROW
BEGIN
	UPDATE fse_basic_profiles SET nr_comments=nr_comments+1 WHERE fse_id=NEW.author_id;
	UPDATE fse_basic_profiles A, fsen_document_sections_all B
		SET A.nr_comments_got=A.nr_comments_got+1 WHERE B.id=NEW.section_id AND A.fse_id=B.author_id;
END$

-- INSERT ACTION COMMENT
DROP TRIGGER IF EXISTS fsen_on_new_action_comment;
CREATE TRIGGER fsen_on_new_action_comment AFTER INSERT ON fsen_document_section_action_comments
FOR EACH ROW
BEGIN
	IF NEW.action = 101 THEN
		UPDATE fse_basic_profiles A, fsen_document_sections_all B
			SET A.nr_praise_got=A.nr_praise_got+1 WHERE B.id=NEW.section_id AND A.fse_id=B.author_id;
	ELSEIF NEW.action = 102 THEN
		UPDATE fse_basic_profiles A, fsen_document_sections_all B
			SET A.nr_shares_got=A.nr_shares_got+1 WHERE B.id=NEW.section_id AND A.fse_id=B.author_id;
	ELSEIF NEW.action = 103 THEN
		UPDATE fse_basic_profiles A, fsen_document_sections_all B
			SET A.nr_favorites_got=A.nr_favorites_got+1 WHERE B.id=NEW.section_id AND A.fse_id=B.author_id;
	END IF;
END$

-- INSERT SECTION
DROP TRIGGER IF EXISTS fsen_on_new_section_en;
CREATE TRIGGER fsen_on_new_section_en AFTER INSERT ON fsen_document_sections_en
FOR EACH ROW
BEGIN
	IF NEW.chapter_handle <> 'na' THEN
		UPDATE fsen_project_doc_volume_part_chapters_en SET nr_sections=nr_sections+1
			WHERE project_id=NEW.project_id AND domain_handle=NEW.domain_handle AND volume_handle=NEW.volume_handle
				AND part_handle=NEW.part_handle AND chapter_handle=NEW.chapter_handle;
	END IF;
	UPDATE fse_basic_profiles SET nr_sections=nr_sections+1 WHERE fse_id=NEW.author_id;
END$

DROP TRIGGER IF EXISTS fsen_on_new_section_zh;
CREATE TRIGGER fsen_on_new_section_zh AFTER INSERT ON fsen_document_sections_zh
FOR EACH ROW
BEGIN
	IF NEW.chapter_handle <> 'na' THEN
		UPDATE fsen_project_doc_volume_part_chapters_zh SET nr_sections=nr_sections+1
			WHERE project_id=NEW.project_id AND domain_handle=NEW.domain_handle AND volume_handle=NEW.volume_handle
				AND part_handle=NEW.part_handle AND chapter_handle=NEW.chapter_handle;
	END IF;
	UPDATE fse_basic_profiles SET nr_sections=nr_sections+1 WHERE fse_id=NEW.author_id;
END$

-- INSERT CHAPTER
DROP TRIGGER IF EXISTS fsen_on_new_chapter_en;
CREATE TRIGGER fsen_on_new_chapter_en AFTER INSERT ON fsen_project_doc_volume_part_chapters_en
FOR EACH ROW
BEGIN
	UPDATE fsen_project_doc_volume_parts SET nr_chapters=nr_chapters+1
		WHERE project_id=NEW.project_id AND domain_handle=NEW.domain_handle
			AND volume_handle=NEW.volume_handle AND part_handle=NEW.part_handle;
	UPDATE fse_basic_profiles SET nr_chapters=nr_chapters+1 WHERE fse_id=NEW.fse_id;
END$

DROP TRIGGER IF EXISTS fsen_on_new_chapter_zh;
CREATE TRIGGER fsen_on_new_chapter_zh AFTER INSERT ON fsen_project_doc_volume_part_chapters_zh
FOR EACH ROW
BEGIN
	UPDATE fsen_project_doc_volume_parts SET nr_chapters=nr_chapters+1
		WHERE project_id=NEW.project_id AND domain_handle=NEW.domain_handle
			AND volume_handle=NEW.volume_handle AND part_handle=NEW.part_handle;
	UPDATE fse_basic_profiles SET nr_chapters=nr_chapters+1 WHERE fse_id=NEW.fse_id;
END$

-- DELETE COMMENT
DROP TRIGGER IF EXISTS fsen_on_delete_comment;
CREATE TRIGGER fsen_on_delete_comment BEFORE DELETE ON fsen_document_section_comments
FOR EACH ROW
BEGIN
	UPDATE IGNORE fse_basic_profiles SET nr_comments=nr_comments-1 WHERE fse_id=OLD.author_id;
	UPDATE IGNORE fse_basic_profiles A, fsen_document_sections_all B
		SET A.nr_comments_got=A.nr_comments_got-1 WHERE B.id=OLD.section_id AND A.fse_id=B.author_id;
END$

-- DELETE ACTION COMMENT
DROP TRIGGER IF EXISTS fsen_on_delete_action_comment;
CREATE TRIGGER fsen_on_delete_action_comment BEFORE DELETE ON fsen_document_section_action_comments
FOR EACH ROW
BEGIN
	IF OLD.action = 101 THEN
		UPDATE IGNORE fse_basic_profiles A, fsen_document_sections_all B
			SET A.nr_praise_got=A.nr_praise_got-1 WHERE B.id=OLD.section_id AND A.fse_id=B.author_id;
	ELSEIF OLD.action = 102 THEN
		UPDATE IGNORE fse_basic_profiles A, fsen_document_sections_all B
			SET A.nr_shares_got=A.nr_shares_got-1 WHERE B.id=OLD.section_id AND A.fse_id=B.author_id;
	ELSEIF OLD.action = 103 THEN
		UPDATE IGNORE fse_basic_profiles A, fsen_document_sections_all B
			SET A.nr_favorites_got=A.nr_favorites_got-1 WHERE B.id=OLD.section_id AND A.fse_id=B.author_id;
	END IF;
END$

-- DELETE SECTION
DROP TRIGGER IF EXISTS fsen_on_delete_section_en;
CREATE TRIGGER fsen_on_delete_section_en BEFORE DELETE ON fsen_document_sections_en
FOR EACH ROW
BEGIN
	INSERT IGNORE fsen_document_sections_deleted
		VALUES (OLD.id, OLD.author_id, OLD.project_id, OLD.domain_handle, OLD.volume_handle, OLD.part_handle,
			OLD.chapter_handle, OLD.max_ver_code, NOW());
	IF OLD.chapter_handle <> 'na' THEN
		UPDATE IGNORE fsen_project_doc_volume_part_chapters_en SET nr_sections=nr_sections-1
			WHERE project_id=OLD.project_id AND domain_handle=OLD.domain_handle AND volume_handle=OLD.volume_handle
				AND part_handle=OLD.part_handle AND chapter_handle=OLD.chapter_handle;
	END IF;
	UPDATE IGNORE fse_basic_profiles SET nr_sections=nr_sections-1 WHERE fse_id=OLD.author_id;
END$

DROP TRIGGER IF EXISTS fsen_on_delete_section_zh;
CREATE TRIGGER fsen_on_delete_section_zh BEFORE DELETE ON fsen_document_sections_zh
FOR EACH ROW
BEGIN
	INSERT IGNORE fsen_document_sections_deleted
		VALUES (OLD.id, OLD.author_id, OLD.project_id, OLD.domain_handle, OLD.volume_handle, OLD.part_handle,
			OLD.chapter_handle, OLD.max_ver_code, NOW());
	IF OLD.chapter_handle <> 'na' THEN
		UPDATE IGNORE fsen_project_doc_volume_part_chapters_zh SET nr_sections=nr_sections-1
			WHERE project_id=OLD.project_id AND domain_handle=OLD.domain_handle AND volume_handle=OLD.volume_handle
				AND part_handle=OLD.part_handle AND chapter_handle=OLD.chapter_handle;
	END IF;
	UPDATE IGNORE fse_basic_profiles SET nr_sections=nr_sections-1 WHERE fse_id=OLD.author_id;
END$

-- DELETE CHAPTER
DROP TRIGGER IF EXISTS fsen_on_delete_chapter_en;
CREATE TRIGGER fsen_on_delete_chapter_en BEFORE DELETE ON fsen_project_doc_volume_part_chapters_en
FOR EACH ROW
BEGIN
	UPDATE IGNORE fsen_project_doc_volume_parts SET nr_chapters=nr_chapters-1
		WHERE project_id=OLD.project_id AND domain_handle=OLD.domain_handle
			AND volume_handle=OLD.volume_handle AND part_handle=OLD.part_handle;
	UPDATE IGNORE fse_basic_profiles SET nr_chapters=nr_chapters-1 WHERE fse_id=OLD.fse_id;
END$

DROP TRIGGER IF EXISTS fsen_on_delete_chapter_zh;
CREATE TRIGGER fsen_on_delete_chapter_zh BEFORE DELETE ON fsen_project_doc_volume_part_chapters_zh
FOR EACH ROW
BEGIN
	UPDATE IGNORE fsen_project_doc_volume_parts SET nr_chapters=nr_chapters-1
		WHERE project_id=OLD.project_id AND domain_handle=OLD.domain_handle
			AND volume_handle=OLD.volume_handle AND part_handle=OLD.part_handle;
	UPDATE IGNORE fse_basic_profiles SET nr_chapters=nr_chapters-1 WHERE fse_id=OLD.fse_id;
END$

-- BEFORE UPDATE SECTION
DROP TRIGGER IF EXISTS fsen_on_before_update_section_en;
CREATE TRIGGER fsen_on_before_update_section_en BEFORE UPDATE ON fsen_document_sections_en
FOR EACH ROW
BEGIN
	SET NEW.heat_level = (NEW.nr_comments*70 + NEW.nr_shares*10 + NEW.nr_praise*10 + NEW.nr_favorites*10);
	SET NEW.display_order = (NEW.nr_comments*10 + NEW.nr_shares*10 + NEW.nr_praise*70 + NEW.nr_favorites*10);
END$

DROP TRIGGER IF EXISTS fsen_on_before_update_section_zh;
CREATE TRIGGER fsen_on_before_update_section_zh BEFORE UPDATE ON fsen_document_sections_zh
FOR EACH ROW
BEGIN
	SET NEW.heat_level = (NEW.nr_comments*70 + NEW.nr_shares*10 + NEW.nr_praise*10 + NEW.nr_favorites*10);
	SET NEW.display_order = (NEW.nr_comments*10 + NEW.nr_shares*10 + NEW.nr_praise*70 + NEW.nr_favorites*10);
END$

-- BEFORE UPDATE CHAPTER
DROP TRIGGER IF EXISTS fsen_on_before_update_chapter_en;
CREATE TRIGGER fsen_on_before_update_chapter_en BEFORE UPDATE ON fsen_project_doc_volume_part_chapters_en
FOR EACH ROW
BEGIN
	SET NEW.heat_level = (NEW.required*100 + NEW.nr_sections*100
			+ NEW.nr_comments*70 + NEW.nr_shares*10 + NEW.nr_praise*10 + NEW.nr_favorites*10);
END$

DROP TRIGGER IF EXISTS fsen_on_before_update_chapter_zh;
CREATE TRIGGER fsen_on_before_update_chapter_zh BEFORE UPDATE ON fsen_project_doc_volume_part_chapters_zh
FOR EACH ROW
BEGIN
	SET NEW.heat_level = (NEW.required*100 + NEW.nr_sections*100
			+ NEW.nr_comments*70 + NEW.nr_shares*10 + NEW.nr_praise*10 + NEW.nr_favorites*10);
END$

-- BEFORE UPDATE PROFILE
DROP TRIGGER IF EXISTS fsen_on_before_update_profile;
CREATE TRIGGER fsen_on_before_update_profile BEFORE UPDATE ON fse_basic_profiles
FOR EACH ROW
BEGIN
	SET NEW.heat_level = (NEW.nr_chapters*60 + NEW.nr_sections*30 + NEW.nr_comments*10
			+ NEW.nr_comments_got*2 + NEW.nr_praise_got*4 + NEW.nr_favorites_got*2 + NEW.nr_shares_got*2);
END$

-- QUERY END

DELIMITER ;

