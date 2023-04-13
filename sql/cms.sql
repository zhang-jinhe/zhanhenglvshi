/*
 Navicat Premium Data Transfer

 Source Server         : 192.168.31.19
 Source Server Type    : MySQL
 Source Server Version : 80030
 Source Host           : localhost:3306
 Source Schema         : zhanhenglvshi

 Target Server Type    : MySQL
 Target Server Version : 80030
 File Encoding         : 65001

 Date: 13/04/2023 15:05:12
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for site_e_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `site_e_admin_user`;
CREATE TABLE `site_e_admin_user`  (
  `user_id` smallint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_name` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `email` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `password` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `start_pass` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `ec_salt` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `add_time` int(0) NOT NULL DEFAULT 0,
  `make_time` int(0) NOT NULL DEFAULT 0,
  `expr_time` int(0) NOT NULL DEFAULT 1,
  `last_login` int(0) NOT NULL DEFAULT 0,
  `last_ip` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `action_list` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `nav_list` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `lang_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `agency_id` smallint(0) UNSIGNED NOT NULL,
  `suppliers_id` smallint(0) UNSIGNED NULL DEFAULT NULL,
  `todolist` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `role_id` smallint(0) NULL DEFAULT NULL,
  `goodscatroles` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `articlecatroles` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `adcatroles` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `city` smallint(0) NOT NULL,
  `is_check` int(0) NOT NULL DEFAULT 1,
  `visit_count` int(0) NOT NULL DEFAULT 0,
  `moban_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `moban_desc` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `source_user_id` int(0) NOT NULL DEFAULT 0,
  `sortnumber` mediumint(0) NOT NULL,
  `stats_code` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `hash_code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `loginssid` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for site_e_article
-- ----------------------------
DROP TABLE IF EXISTS `site_e_article`;
CREATE TABLE `site_e_article`  (
  `article_id` mediumint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_id` smallint(0) NOT NULL DEFAULT 0,
  `title` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `mcontent` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `content1` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `content2` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `content3` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `content4` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `tab` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `tab1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `tab2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `tab3` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `tab4` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `keywords` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `is_show` tinyint(0) UNSIGNED NOT NULL DEFAULT 1,
  `add_time` int(0) UNSIGNED NOT NULL,
  `article_thumb` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `article_other_thumb` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `article_array_thumb` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `article_down` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `jump_url` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `click_count` int(0) NULL DEFAULT 0,
  `is_com` tinyint(1) NOT NULL,
  `spcdesc` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `title_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `content_1` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `mcontent_1` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `keywords_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `description_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `spcdesc_1` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `title_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `content_2` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `mcontent_2` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `keywords_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `description_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `spcdesc_2` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `title_3` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `content_3` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `mcontent_3` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `keywords_3` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `description_3` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `spcdesc_3` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `moban_module_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`article_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for site_e_article_cat
-- ----------------------------
DROP TABLE IF EXISTS `site_e_article_cat`;
CREATE TABLE `site_e_article_cat`  (
  `cat_id` smallint(0) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `cat_type` int(0) NOT NULL DEFAULT 1,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `keywords` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `cat_desc` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `sort_order` tinyint(0) UNSIGNED NOT NULL DEFAULT 50,
  `parent_id` smallint(0) UNSIGNED NOT NULL,
  `define_url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `is_show` int(0) NOT NULL DEFAULT 1,
  `is_nav_show` tinyint(1) NOT NULL DEFAULT 0,
  `is_nav_copy` int(0) NOT NULL DEFAULT 0,
  `article_id` int(0) NOT NULL DEFAULT 0,
  `ifxiala` int(0) NOT NULL DEFAULT 0,
  `page_size` int(0) NOT NULL DEFAULT 10,
  `moban` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `smallmoban` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `cat_thumb` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `jump_url` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `cat_name_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `title_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `cat_desc_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `keywords_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `description_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `cat_name_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `title_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `cat_desc_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `keywords_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `description_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `cat_name_3` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `title_3` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `cat_desc_3` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `keywords_3` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `description_3` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`cat_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for site_e_comment
-- ----------------------------
DROP TABLE IF EXISTS `site_e_comment`;
CREATE TABLE `site_e_comment`  (
  `comment_id` int(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_id` int(0) NOT NULL DEFAULT 0,
  `article_id` int(0) NOT NULL DEFAULT 0,
  `langvs` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `form_id` int(0) NOT NULL,
  `add_time` int(0) NOT NULL DEFAULT 0,
  `content` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `content_1` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `is_show` int(0) NOT NULL DEFAULT 0,
  PRIMARY KEY (`comment_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for site_e_fields
-- ----------------------------
DROP TABLE IF EXISTS `site_e_fields`;
CREATE TABLE `site_e_fields`  (
  `id` int(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_id` int(0) NOT NULL DEFAULT 0,
  `reg_field_name` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `reg_field_name_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `industry_id` int(0) NOT NULL,
  `dis_order` tinyint(0) UNSIGNED NOT NULL DEFAULT 100,
  `display` tinyint(0) UNSIGNED NOT NULL DEFAULT 1,
  `type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `is_need` tinyint(0) UNSIGNED NOT NULL DEFAULT 1,
  `select_options` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `select_options_1` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `width` int(0) NOT NULL,
  `height` int(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for site_e_form
-- ----------------------------
DROP TABLE IF EXISTS `site_e_form`;
CREATE TABLE `site_e_form`  (
  `form_id` int(0) NOT NULL AUTO_INCREMENT,
  `industry_id` int(0) NOT NULL DEFAULT 0,
  `title` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `spcdesc` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `add_time` int(0) NOT NULL DEFAULT 0,
  `module_id` int(0) NOT NULL DEFAULT 0,
  PRIMARY KEY (`form_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for site_e_kefu
-- ----------------------------
DROP TABLE IF EXISTS `site_e_kefu`;
CREATE TABLE `site_e_kefu`  (
  `kefu_id` int(0) NOT NULL AUTO_INCREMENT,
  `kefushow` int(0) NOT NULL DEFAULT 0,
  `skin` int(0) NOT NULL DEFAULT 0,
  `pshow` int(0) NOT NULL DEFAULT 1,
  `showlefttop` int(0) NOT NULL DEFAULT 80,
  `showleft` int(0) NOT NULL DEFAULT 10,
  `showrighttop` int(0) NOT NULL DEFAULT 80,
  `showright` int(0) NOT NULL DEFAULT 50,
  `fs_show` int(0) NOT NULL DEFAULT 0,
  `typeone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '售前',
  `kfqq` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `im` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `typetwo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '售后',
  `kfqqtwo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `imtwo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `qqqun` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `wwqun` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `kftel` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `shijian` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `kf53` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `qqcss` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `wwcss` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '1',
  `fenxiang` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `mshow` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `other` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `superlink` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`kefu_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for site_e_moban
-- ----------------------------
DROP TABLE IF EXISTS `site_e_moban`;
CREATE TABLE `site_e_moban`  (
  `moban_id` smallint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `moban_cat_id` smallint(0) UNSIGNED NOT NULL DEFAULT 0,
  `moban_name` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ' ',
  `moban_domain` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `moban_other_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `moban_link` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ' ',
  `moban_thumb` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `moban_thumb_mb` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `style_id` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `click_count` mediumint(0) UNSIGNED NOT NULL DEFAULT 0,
  `is_show` tinyint(0) UNSIGNED NOT NULL DEFAULT 1,
  `sortnumber` mediumint(0) NOT NULL,
  `cat_id` smallint(0) NOT NULL,
  `mo_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `data_id` int(0) NOT NULL DEFAULT 0,
  `color` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `add_time` int(0) NOT NULL DEFAULT 0,
  `web_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `title` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `keywords` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `stats_code` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `power_list` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `web_name_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `title_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `keywords_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description_1` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `stats_code_1` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `default_lang` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'lang',
  `open_lang` int(0) NOT NULL DEFAULT 0,
  `select_lang` int(0) NOT NULL DEFAULT 0,
  `more_lang` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `web_logo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`moban_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for site_e_module
-- ----------------------------
DROP TABLE IF EXISTS `site_e_module`;
CREATE TABLE `site_e_module`  (
  `id` smallint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `module_htmid` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `module_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `module_name` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `module_tips` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `module_link` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ' ',
  `module_thumb` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `module_data_type` int(0) NOT NULL DEFAULT 0,
  `module_div_type` int(0) NOT NULL DEFAULT 0,
  `container_json` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `click_count` mediumint(0) UNSIGNED NOT NULL DEFAULT 0,
  `is_show` tinyint(0) UNSIGNED NOT NULL DEFAULT 1,
  `sortnumber` mediumint(0) NOT NULL,
  `data` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `data_1` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `base` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `animation` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `css` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `tips` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for site_e_sessions
-- ----------------------------
DROP TABLE IF EXISTS `site_e_sessions`;
CREATE TABLE `site_e_sessions`  (
  `sesskey` char(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `expiry` int(0) UNSIGNED NOT NULL DEFAULT 0,
  `session_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `userid` mediumint(0) UNSIGNED NOT NULL DEFAULT 0,
  `adminid` mediumint(0) UNSIGNED NOT NULL DEFAULT 0,
  `ip` char(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `user_name` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `user_rank` tinyint(0) NOT NULL,
  `discount` decimal(3, 0) NOT NULL,
  `email` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`sesskey`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
