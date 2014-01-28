-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: moodle
-- ------------------------------------------------------
-- Server version	5.5.35-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `mdl_assign`
--

DROP TABLE IF EXISTS `mdl_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assign` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `alwaysshowdescription` tinyint(2) NOT NULL DEFAULT '0',
  `nosubmissions` tinyint(2) NOT NULL DEFAULT '0',
  `submissiondrafts` tinyint(2) NOT NULL DEFAULT '0',
  `sendnotifications` tinyint(2) NOT NULL DEFAULT '0',
  `sendlatenotifications` tinyint(2) NOT NULL DEFAULT '0',
  `duedate` bigint(10) NOT NULL DEFAULT '0',
  `allowsubmissionsfromdate` bigint(10) NOT NULL DEFAULT '0',
  `grade` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `requiresubmissionstatement` tinyint(2) NOT NULL DEFAULT '0',
  `completionsubmit` tinyint(2) NOT NULL DEFAULT '0',
  `cutoffdate` bigint(10) NOT NULL DEFAULT '0',
  `teamsubmission` tinyint(2) NOT NULL DEFAULT '0',
  `requireallteammemberssubmit` tinyint(2) NOT NULL DEFAULT '0',
  `teamsubmissiongroupingid` bigint(10) NOT NULL DEFAULT '0',
  `blindmarking` tinyint(2) NOT NULL DEFAULT '0',
  `revealidentities` tinyint(2) NOT NULL DEFAULT '0',
  `attemptreopenmethod` varchar(10) NOT NULL DEFAULT 'none',
  `maxattempts` mediumint(6) NOT NULL DEFAULT '-1',
  `markingworkflow` tinyint(2) NOT NULL DEFAULT '0',
  `markingallocation` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assi_cou_ix` (`course`),
  KEY `mdl_assi_tea_ix` (`teamsubmissiongroupingid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table saves information about an instance of mod_assign';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assign`
--

LOCK TABLES `mdl_assign` WRITE;
/*!40000 ALTER TABLE `mdl_assign` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assign_grades`
--

DROP TABLE IF EXISTS `mdl_assign_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assign_grades` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `grader` bigint(10) NOT NULL DEFAULT '0',
  `grade` decimal(10,5) DEFAULT '0.00000',
  `attemptnumber` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_assigrad_assuseatt_uix` (`assignment`,`userid`,`attemptnumber`),
  KEY `mdl_assigrad_use_ix` (`userid`),
  KEY `mdl_assigrad_att_ix` (`attemptnumber`),
  KEY `mdl_assigrad_ass_ix` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Grading information about a single assignment submission.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assign_grades`
--

LOCK TABLES `mdl_assign_grades` WRITE;
/*!40000 ALTER TABLE `mdl_assign_grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assign_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assign_plugin_config`
--

DROP TABLE IF EXISTS `mdl_assign_plugin_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assign_plugin_config` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `plugin` varchar(28) NOT NULL DEFAULT '',
  `subtype` varchar(28) NOT NULL DEFAULT '',
  `name` varchar(28) NOT NULL DEFAULT '',
  `value` longtext,
  PRIMARY KEY (`id`),
  KEY `mdl_assiplugconf_plu_ix` (`plugin`),
  KEY `mdl_assiplugconf_sub_ix` (`subtype`),
  KEY `mdl_assiplugconf_nam_ix` (`name`),
  KEY `mdl_assiplugconf_ass_ix` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Config data for an instance of a plugin in an assignment.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assign_plugin_config`
--

LOCK TABLES `mdl_assign_plugin_config` WRITE;
/*!40000 ALTER TABLE `mdl_assign_plugin_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assign_plugin_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assign_submission`
--

DROP TABLE IF EXISTS `mdl_assign_submission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assign_submission` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `status` varchar(10) DEFAULT NULL,
  `groupid` bigint(10) NOT NULL DEFAULT '0',
  `attemptnumber` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_assisubm_assusegroatt_uix` (`assignment`,`userid`,`groupid`,`attemptnumber`),
  KEY `mdl_assisubm_use_ix` (`userid`),
  KEY `mdl_assisubm_att_ix` (`attemptnumber`),
  KEY `mdl_assisubm_ass_ix` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table keeps information about student interactions with';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assign_submission`
--

LOCK TABLES `mdl_assign_submission` WRITE;
/*!40000 ALTER TABLE `mdl_assign_submission` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assign_submission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assign_user_flags`
--

DROP TABLE IF EXISTS `mdl_assign_user_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assign_user_flags` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `locked` bigint(10) NOT NULL DEFAULT '0',
  `mailed` smallint(4) NOT NULL DEFAULT '0',
  `extensionduedate` bigint(10) NOT NULL DEFAULT '0',
  `workflowstate` varchar(20) DEFAULT NULL,
  `allocatedmarker` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assiuserflag_mai_ix` (`mailed`),
  KEY `mdl_assiuserflag_use_ix` (`userid`),
  KEY `mdl_assiuserflag_ass_ix` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List of flags that can be set for a single user in a single ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assign_user_flags`
--

LOCK TABLES `mdl_assign_user_flags` WRITE;
/*!40000 ALTER TABLE `mdl_assign_user_flags` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assign_user_flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assign_user_mapping`
--

DROP TABLE IF EXISTS `mdl_assign_user_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assign_user_mapping` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assiusermapp_ass_ix` (`assignment`),
  KEY `mdl_assiusermapp_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Map an assignment specific id number to a user';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assign_user_mapping`
--

LOCK TABLES `mdl_assign_user_mapping` WRITE;
/*!40000 ALTER TABLE `mdl_assign_user_mapping` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assign_user_mapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assignfeedback_comments`
--

DROP TABLE IF EXISTS `mdl_assignfeedback_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assignfeedback_comments` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `grade` bigint(10) NOT NULL DEFAULT '0',
  `commenttext` longtext,
  `commentformat` smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assicomm_ass_ix` (`assignment`),
  KEY `mdl_assicomm_gra_ix` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Text feedback for submitted assignments';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assignfeedback_comments`
--

LOCK TABLES `mdl_assignfeedback_comments` WRITE;
/*!40000 ALTER TABLE `mdl_assignfeedback_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assignfeedback_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assignfeedback_editpdf_annot`
--

DROP TABLE IF EXISTS `mdl_assignfeedback_editpdf_annot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assignfeedback_editpdf_annot` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `gradeid` bigint(10) NOT NULL DEFAULT '0',
  `pageno` bigint(10) NOT NULL DEFAULT '0',
  `x` bigint(10) DEFAULT '0',
  `y` bigint(10) DEFAULT '0',
  `endx` bigint(10) DEFAULT '0',
  `endy` bigint(10) DEFAULT '0',
  `path` longtext,
  `type` varchar(10) DEFAULT 'line',
  `colour` varchar(10) DEFAULT 'black',
  `draft` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_assieditanno_grapag_ix` (`gradeid`,`pageno`),
  KEY `mdl_assieditanno_gra_ix` (`gradeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='stores annotations added to pdfs submitted by students';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assignfeedback_editpdf_annot`
--

LOCK TABLES `mdl_assignfeedback_editpdf_annot` WRITE;
/*!40000 ALTER TABLE `mdl_assignfeedback_editpdf_annot` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assignfeedback_editpdf_annot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assignfeedback_editpdf_cmnt`
--

DROP TABLE IF EXISTS `mdl_assignfeedback_editpdf_cmnt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assignfeedback_editpdf_cmnt` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `gradeid` bigint(10) NOT NULL DEFAULT '0',
  `x` bigint(10) DEFAULT '0',
  `y` bigint(10) DEFAULT '0',
  `width` bigint(10) DEFAULT '120',
  `rawtext` longtext,
  `pageno` bigint(10) NOT NULL DEFAULT '0',
  `colour` varchar(10) DEFAULT 'black',
  `draft` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_assieditcmnt_grapag_ix` (`gradeid`,`pageno`),
  KEY `mdl_assieditcmnt_gra_ix` (`gradeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores comments added to pdfs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assignfeedback_editpdf_cmnt`
--

LOCK TABLES `mdl_assignfeedback_editpdf_cmnt` WRITE;
/*!40000 ALTER TABLE `mdl_assignfeedback_editpdf_cmnt` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assignfeedback_editpdf_cmnt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assignfeedback_editpdf_quick`
--

DROP TABLE IF EXISTS `mdl_assignfeedback_editpdf_quick`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assignfeedback_editpdf_quick` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `rawtext` longtext NOT NULL,
  `width` bigint(10) NOT NULL DEFAULT '120',
  `colour` varchar(10) DEFAULT 'yellow',
  PRIMARY KEY (`id`),
  KEY `mdl_assieditquic_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores teacher specified quicklist comments';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assignfeedback_editpdf_quick`
--

LOCK TABLES `mdl_assignfeedback_editpdf_quick` WRITE;
/*!40000 ALTER TABLE `mdl_assignfeedback_editpdf_quick` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assignfeedback_editpdf_quick` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assignfeedback_file`
--

DROP TABLE IF EXISTS `mdl_assignfeedback_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assignfeedback_file` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `grade` bigint(10) NOT NULL DEFAULT '0',
  `numfiles` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assifile_ass2_ix` (`assignment`),
  KEY `mdl_assifile_gra_ix` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores info about the number of files submitted by a student';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assignfeedback_file`
--

LOCK TABLES `mdl_assignfeedback_file` WRITE;
/*!40000 ALTER TABLE `mdl_assignfeedback_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assignfeedback_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assignment`
--

DROP TABLE IF EXISTS `mdl_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assignment` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `assignmenttype` varchar(50) NOT NULL DEFAULT '',
  `resubmit` tinyint(2) NOT NULL DEFAULT '0',
  `preventlate` tinyint(2) NOT NULL DEFAULT '0',
  `emailteachers` tinyint(2) NOT NULL DEFAULT '0',
  `var1` bigint(10) DEFAULT '0',
  `var2` bigint(10) DEFAULT '0',
  `var3` bigint(10) DEFAULT '0',
  `var4` bigint(10) DEFAULT '0',
  `var5` bigint(10) DEFAULT '0',
  `maxbytes` bigint(10) NOT NULL DEFAULT '100000',
  `timedue` bigint(10) NOT NULL DEFAULT '0',
  `timeavailable` bigint(10) NOT NULL DEFAULT '0',
  `grade` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assi_cou2_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines assignments';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assignment`
--

LOCK TABLES `mdl_assignment` WRITE;
/*!40000 ALTER TABLE `mdl_assignment` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assignment_submissions`
--

DROP TABLE IF EXISTS `mdl_assignment_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assignment_submissions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `numfiles` bigint(10) NOT NULL DEFAULT '0',
  `data1` longtext,
  `data2` longtext,
  `grade` bigint(11) NOT NULL DEFAULT '0',
  `submissioncomment` longtext NOT NULL,
  `format` smallint(4) NOT NULL DEFAULT '0',
  `teacher` bigint(10) NOT NULL DEFAULT '0',
  `timemarked` bigint(10) NOT NULL DEFAULT '0',
  `mailed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assisubm_use2_ix` (`userid`),
  KEY `mdl_assisubm_mai_ix` (`mailed`),
  KEY `mdl_assisubm_tim_ix` (`timemarked`),
  KEY `mdl_assisubm_ass2_ix` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Info about submitted assignments';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assignment_submissions`
--

LOCK TABLES `mdl_assignment_submissions` WRITE;
/*!40000 ALTER TABLE `mdl_assignment_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assignment_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assignsubmission_file`
--

DROP TABLE IF EXISTS `mdl_assignsubmission_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assignsubmission_file` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `submission` bigint(10) NOT NULL DEFAULT '0',
  `numfiles` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assifile_ass_ix` (`assignment`),
  KEY `mdl_assifile_sub_ix` (`submission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Info about file submissions for assignments';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assignsubmission_file`
--

LOCK TABLES `mdl_assignsubmission_file` WRITE;
/*!40000 ALTER TABLE `mdl_assignsubmission_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assignsubmission_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_assignsubmission_onlinetext`
--

DROP TABLE IF EXISTS `mdl_assignsubmission_onlinetext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_assignsubmission_onlinetext` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `submission` bigint(10) NOT NULL DEFAULT '0',
  `onlinetext` longtext,
  `onlineformat` smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_assionli_ass_ix` (`assignment`),
  KEY `mdl_assionli_sub_ix` (`submission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Info about onlinetext submission';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_assignsubmission_onlinetext`
--

LOCK TABLES `mdl_assignsubmission_onlinetext` WRITE;
/*!40000 ALTER TABLE `mdl_assignsubmission_onlinetext` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_assignsubmission_onlinetext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_backup_controllers`
--

DROP TABLE IF EXISTS `mdl_backup_controllers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_backup_controllers` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `backupid` varchar(32) NOT NULL DEFAULT '',
  `operation` varchar(20) NOT NULL DEFAULT 'backup',
  `type` varchar(10) NOT NULL DEFAULT '',
  `itemid` bigint(10) NOT NULL,
  `format` varchar(20) NOT NULL DEFAULT '',
  `interactive` smallint(4) NOT NULL,
  `purpose` smallint(4) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `status` smallint(4) NOT NULL,
  `execution` smallint(4) NOT NULL,
  `executiontime` bigint(10) NOT NULL,
  `checksum` varchar(32) NOT NULL DEFAULT '',
  `timecreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  `controller` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_backcont_bac_uix` (`backupid`),
  KEY `mdl_backcont_typite_ix` (`type`,`itemid`),
  KEY `mdl_backcont_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='To store the backup_controllers as they are used';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_backup_controllers`
--

LOCK TABLES `mdl_backup_controllers` WRITE;
/*!40000 ALTER TABLE `mdl_backup_controllers` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_backup_controllers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_backup_courses`
--

DROP TABLE IF EXISTS `mdl_backup_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_backup_courses` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `laststarttime` bigint(10) NOT NULL DEFAULT '0',
  `lastendtime` bigint(10) NOT NULL DEFAULT '0',
  `laststatus` varchar(1) NOT NULL DEFAULT '5',
  `nextstarttime` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_backcour_cou_uix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='To store every course backup status';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_backup_courses`
--

LOCK TABLES `mdl_backup_courses` WRITE;
/*!40000 ALTER TABLE `mdl_backup_courses` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_backup_courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_backup_logs`
--

DROP TABLE IF EXISTS `mdl_backup_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_backup_logs` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `backupid` varchar(32) NOT NULL DEFAULT '',
  `loglevel` smallint(4) NOT NULL,
  `message` longtext NOT NULL,
  `timecreated` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_backlogs_bacid_uix` (`backupid`,`id`),
  KEY `mdl_backlogs_bac_ix` (`backupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='To store all the logs from backup and restore operations (by';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_backup_logs`
--

LOCK TABLES `mdl_backup_logs` WRITE;
/*!40000 ALTER TABLE `mdl_backup_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_backup_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_badge`
--

DROP TABLE IF EXISTS `mdl_badge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_badge` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` longtext,
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `usercreated` bigint(10) NOT NULL,
  `usermodified` bigint(10) NOT NULL,
  `issuername` varchar(255) NOT NULL DEFAULT '',
  `issuerurl` varchar(255) NOT NULL DEFAULT '',
  `issuercontact` varchar(255) DEFAULT NULL,
  `expiredate` bigint(10) DEFAULT NULL,
  `expireperiod` bigint(10) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `courseid` bigint(10) DEFAULT NULL,
  `message` longtext NOT NULL,
  `messagesubject` longtext NOT NULL,
  `attachment` tinyint(1) NOT NULL DEFAULT '1',
  `notification` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `nextcron` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badg_typ_ix` (`type`),
  KEY `mdl_badg_cou_ix` (`courseid`),
  KEY `mdl_badg_use_ix` (`usermodified`),
  KEY `mdl_badg_use2_ix` (`usercreated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines badge';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_badge`
--

LOCK TABLES `mdl_badge` WRITE;
/*!40000 ALTER TABLE `mdl_badge` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_badge` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_badge_backpack`
--

DROP TABLE IF EXISTS `mdl_badge_backpack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_badge_backpack` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `email` varchar(100) NOT NULL DEFAULT '',
  `backpackurl` varchar(255) NOT NULL DEFAULT '',
  `backpackuid` bigint(10) NOT NULL,
  `autosync` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badgback_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines settings for connecting external backpack';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_badge_backpack`
--

LOCK TABLES `mdl_badge_backpack` WRITE;
/*!40000 ALTER TABLE `mdl_badge_backpack` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_badge_backpack` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_badge_criteria`
--

DROP TABLE IF EXISTS `mdl_badge_criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_badge_criteria` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `badgeid` bigint(10) NOT NULL DEFAULT '0',
  `criteriatype` bigint(10) DEFAULT NULL,
  `method` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_badgcrit_badcri_uix` (`badgeid`,`criteriatype`),
  KEY `mdl_badgcrit_cri_ix` (`criteriatype`),
  KEY `mdl_badgcrit_bad_ix` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines criteria for issuing badges';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_badge_criteria`
--

LOCK TABLES `mdl_badge_criteria` WRITE;
/*!40000 ALTER TABLE `mdl_badge_criteria` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_badge_criteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_badge_criteria_met`
--

DROP TABLE IF EXISTS `mdl_badge_criteria_met`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_badge_criteria_met` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `issuedid` bigint(10) DEFAULT NULL,
  `critid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `datemet` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badgcritmet_cri_ix` (`critid`),
  KEY `mdl_badgcritmet_use_ix` (`userid`),
  KEY `mdl_badgcritmet_iss_ix` (`issuedid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines criteria that were met for an issued badge';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_badge_criteria_met`
--

LOCK TABLES `mdl_badge_criteria_met` WRITE;
/*!40000 ALTER TABLE `mdl_badge_criteria_met` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_badge_criteria_met` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_badge_criteria_param`
--

DROP TABLE IF EXISTS `mdl_badge_criteria_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_badge_criteria_param` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `critid` bigint(10) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badgcritpara_cri_ix` (`critid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines parameters for badges criteria';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_badge_criteria_param`
--

LOCK TABLES `mdl_badge_criteria_param` WRITE;
/*!40000 ALTER TABLE `mdl_badge_criteria_param` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_badge_criteria_param` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_badge_external`
--

DROP TABLE IF EXISTS `mdl_badge_external`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_badge_external` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `backpackid` bigint(10) NOT NULL,
  `collectionid` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badgexte_bac_ix` (`backpackid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Setting for external badges display';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_badge_external`
--

LOCK TABLES `mdl_badge_external` WRITE;
/*!40000 ALTER TABLE `mdl_badge_external` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_badge_external` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_badge_issued`
--

DROP TABLE IF EXISTS `mdl_badge_issued`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_badge_issued` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `badgeid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `uniquehash` longtext NOT NULL,
  `dateissued` bigint(10) NOT NULL DEFAULT '0',
  `dateexpire` bigint(10) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `issuernotified` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_badgissu_baduse_uix` (`badgeid`,`userid`),
  KEY `mdl_badgissu_bad_ix` (`badgeid`),
  KEY `mdl_badgissu_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines issued badges';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_badge_issued`
--

LOCK TABLES `mdl_badge_issued` WRITE;
/*!40000 ALTER TABLE `mdl_badge_issued` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_badge_issued` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_badge_manual_award`
--

DROP TABLE IF EXISTS `mdl_badge_manual_award`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_badge_manual_award` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `badgeid` bigint(10) NOT NULL,
  `recipientid` bigint(10) NOT NULL,
  `issuerid` bigint(10) NOT NULL,
  `issuerrole` bigint(10) NOT NULL,
  `datemet` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_badgmanuawar_bad_ix` (`badgeid`),
  KEY `mdl_badgmanuawar_rec_ix` (`recipientid`),
  KEY `mdl_badgmanuawar_iss_ix` (`issuerid`),
  KEY `mdl_badgmanuawar_iss2_ix` (`issuerrole`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Track manual award criteria for badges';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_badge_manual_award`
--

LOCK TABLES `mdl_badge_manual_award` WRITE;
/*!40000 ALTER TABLE `mdl_badge_manual_award` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_badge_manual_award` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_block`
--

DROP TABLE IF EXISTS `mdl_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_block` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL DEFAULT '',
  `cron` bigint(10) NOT NULL DEFAULT '0',
  `lastcron` bigint(10) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_bloc_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='contains all installed blocks';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_block`
--

LOCK TABLES `mdl_block` WRITE;
/*!40000 ALTER TABLE `mdl_block` DISABLE KEYS */;
INSERT INTO `mdl_block` VALUES (1,'activity_modules',0,0,1),(2,'admin_bookmarks',0,0,1),(3,'badges',0,0,1),(4,'blog_menu',0,0,1),(5,'blog_recent',0,0,1),(6,'blog_tags',0,0,1),(7,'calendar_month',0,0,1),(8,'calendar_upcoming',0,0,1),(9,'comments',0,0,1),(10,'community',0,0,1),(11,'completionstatus',0,0,1),(12,'course_list',0,0,1),(13,'course_overview',0,0,1),(14,'course_summary',0,0,1),(15,'feedback',0,0,0),(16,'glossary_random',0,0,1),(17,'html',0,0,1),(18,'login',0,0,1),(19,'mentees',0,0,1),(20,'messages',0,0,1),(21,'mnet_hosts',0,0,1),(22,'myprofile',0,0,1),(23,'navigation',0,0,1),(24,'news_items',0,0,1),(25,'online_users',0,0,1),(26,'participants',0,0,1),(27,'private_files',0,0,1),(28,'quiz_results',0,0,1),(29,'recent_activity',0,0,1),(30,'rss_client',300,1390473608,1),(31,'search_forums',0,0,1),(32,'section_links',0,0,1),(33,'selfcompletion',0,0,1),(34,'settings',0,0,1),(35,'site_main_menu',0,0,1),(36,'social_activities',0,0,1),(37,'tag_flickr',0,0,1),(38,'tag_youtube',0,0,1),(39,'tags',0,0,1);
/*!40000 ALTER TABLE `mdl_block` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_block_community`
--

DROP TABLE IF EXISTS `mdl_block_community`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_block_community` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL,
  `coursename` varchar(255) NOT NULL DEFAULT '',
  `coursedescription` longtext,
  `courseurl` varchar(255) NOT NULL DEFAULT '',
  `imageurl` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Community block';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_block_community`
--

LOCK TABLES `mdl_block_community` WRITE;
/*!40000 ALTER TABLE `mdl_block_community` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_block_community` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_block_instances`
--

DROP TABLE IF EXISTS `mdl_block_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_block_instances` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `blockname` varchar(40) NOT NULL DEFAULT '',
  `parentcontextid` bigint(10) NOT NULL,
  `showinsubcontexts` smallint(4) NOT NULL,
  `pagetypepattern` varchar(64) NOT NULL DEFAULT '',
  `subpagepattern` varchar(16) DEFAULT NULL,
  `defaultregion` varchar(16) NOT NULL DEFAULT '',
  `defaultweight` bigint(10) NOT NULL,
  `configdata` longtext,
  PRIMARY KEY (`id`),
  KEY `mdl_blocinst_parshopagsub_ix` (`parentcontextid`,`showinsubcontexts`,`pagetypepattern`,`subpagepattern`),
  KEY `mdl_blocinst_par_ix` (`parentcontextid`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='This table stores block instances. The type of block this is';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_block_instances`
--

LOCK TABLES `mdl_block_instances` WRITE;
/*!40000 ALTER TABLE `mdl_block_instances` DISABLE KEYS */;
INSERT INTO `mdl_block_instances` VALUES (1,'site_main_menu',2,0,'site-index',NULL,'side-pre',0,''),(2,'course_summary',2,0,'site-index',NULL,'side-post',0,''),(3,'calendar_month',2,0,'site-index',NULL,'side-post',1,''),(4,'navigation',1,1,'*',NULL,'side-pre',0,''),(5,'settings',1,1,'*',NULL,'side-pre',1,''),(6,'admin_bookmarks',1,0,'admin-*',NULL,'side-pre',0,''),(7,'private_files',1,0,'my-index','2','side-post',0,''),(8,'online_users',1,0,'my-index','2','side-post',1,''),(9,'course_overview',1,0,'my-index','2','content',0,''),(10,'search_forums',17,0,'course-view-*',NULL,'side-post',0,''),(11,'news_items',17,0,'course-view-*',NULL,'side-post',1,''),(12,'calendar_upcoming',17,0,'course-view-*',NULL,'side-post',2,''),(13,'recent_activity',17,0,'course-view-*',NULL,'side-post',3,'');
/*!40000 ALTER TABLE `mdl_block_instances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_block_positions`
--

DROP TABLE IF EXISTS `mdl_block_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_block_positions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `blockinstanceid` bigint(10) NOT NULL,
  `contextid` bigint(10) NOT NULL,
  `pagetype` varchar(64) NOT NULL DEFAULT '',
  `subpage` varchar(16) NOT NULL DEFAULT '',
  `visible` smallint(4) NOT NULL,
  `region` varchar(16) NOT NULL DEFAULT '',
  `weight` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_blocposi_bloconpagsub_uix` (`blockinstanceid`,`contextid`,`pagetype`,`subpage`),
  KEY `mdl_blocposi_blo_ix` (`blockinstanceid`),
  KEY `mdl_blocposi_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the position of a sticky block_instance on a another ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_block_positions`
--

LOCK TABLES `mdl_block_positions` WRITE;
/*!40000 ALTER TABLE `mdl_block_positions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_block_positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_block_rss_client`
--

DROP TABLE IF EXISTS `mdl_block_rss_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_block_rss_client` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `title` longtext NOT NULL,
  `preferredtitle` varchar(64) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `shared` tinyint(2) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Remote news feed information. Contains the news feed id, the';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_block_rss_client`
--

LOCK TABLES `mdl_block_rss_client` WRITE;
/*!40000 ALTER TABLE `mdl_block_rss_client` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_block_rss_client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_blog_association`
--

DROP TABLE IF EXISTS `mdl_blog_association`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_blog_association` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextid` bigint(10) NOT NULL,
  `blogid` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_blogasso_con_ix` (`contextid`),
  KEY `mdl_blogasso_blo_ix` (`blogid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Associations of blog entries with courses and module instanc';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_blog_association`
--

LOCK TABLES `mdl_blog_association` WRITE;
/*!40000 ALTER TABLE `mdl_blog_association` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_blog_association` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_blog_external`
--

DROP TABLE IF EXISTS `mdl_blog_external`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_blog_external` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` longtext,
  `url` longtext NOT NULL,
  `filtertags` varchar(255) DEFAULT NULL,
  `failedlastsync` tinyint(1) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) DEFAULT NULL,
  `timefetched` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_blogexte_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='External blog links used for RSS copying of blog entries to ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_blog_external`
--

LOCK TABLES `mdl_blog_external` WRITE;
/*!40000 ALTER TABLE `mdl_blog_external` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_blog_external` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_book`
--

DROP TABLE IF EXISTS `mdl_book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_book` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `numbering` smallint(4) NOT NULL DEFAULT '0',
  `customtitles` tinyint(2) NOT NULL DEFAULT '0',
  `revision` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines book';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_book`
--

LOCK TABLES `mdl_book` WRITE;
/*!40000 ALTER TABLE `mdl_book` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_book` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_book_chapters`
--

DROP TABLE IF EXISTS `mdl_book_chapters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_book_chapters` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `bookid` bigint(10) NOT NULL DEFAULT '0',
  `pagenum` bigint(10) NOT NULL DEFAULT '0',
  `subchapter` bigint(10) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `contentformat` smallint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(2) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `importsrc` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines book_chapters';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_book_chapters`
--

LOCK TABLES `mdl_book_chapters` WRITE;
/*!40000 ALTER TABLE `mdl_book_chapters` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_book_chapters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_cache_filters`
--

DROP TABLE IF EXISTS `mdl_cache_filters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_cache_filters` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `filter` varchar(32) NOT NULL DEFAULT '',
  `version` bigint(10) NOT NULL DEFAULT '0',
  `md5key` varchar(32) NOT NULL DEFAULT '',
  `rawtext` longtext NOT NULL,
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_cachfilt_filmd5_ix` (`filter`,`md5key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='For keeping information about cached data';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_cache_filters`
--

LOCK TABLES `mdl_cache_filters` WRITE;
/*!40000 ALTER TABLE `mdl_cache_filters` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_cache_filters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_cache_flags`
--

DROP TABLE IF EXISTS `mdl_cache_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_cache_flags` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `flagtype` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `value` longtext NOT NULL,
  `expiry` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_cachflag_fla_ix` (`flagtype`),
  KEY `mdl_cachflag_nam_ix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Cache of time-sensitive flags';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_cache_flags`
--

LOCK TABLES `mdl_cache_flags` WRITE;
/*!40000 ALTER TABLE `mdl_cache_flags` DISABLE KEYS */;
INSERT INTO `mdl_cache_flags` VALUES (1,'userpreferenceschanged','2',1390925785,'1',1390932985),(2,'accesslib/dirtycontexts','/1/15',1390487566,'1',1390494766),(3,'accesslib/dirtycontexts','/1/3/17',1390504615,'1',1390511815),(4,'accesslib/dirtycontexts','/1/16',1390505820,'1',1390513020),(5,'accesslib/dirtycontexts','/1',1390841824,'1',1390849024);
/*!40000 ALTER TABLE `mdl_cache_flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_cache_text`
--

DROP TABLE IF EXISTS `mdl_cache_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_cache_text` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `md5key` varchar(32) NOT NULL DEFAULT '',
  `formattedtext` longtext NOT NULL,
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_cachtext_md5_ix` (`md5key`),
  KEY `mdl_cachtext_tim_ix` (`timemodified`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='For storing temporary copies of processed texts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_cache_text`
--

LOCK TABLES `mdl_cache_text` WRITE;
/*!40000 ALTER TABLE `mdl_cache_text` DISABLE KEYS */;
INSERT INTO `mdl_cache_text` VALUES (1,'7088784e04c075cc90016a72cf938cb2','<p>When players are enabled in these settings, files can be embedded using the media filter (if enabled) or using a File or URL resources with the Embed option. When not enabled, these formats are not embedded and users can manually download or follow links to these resources.</p>\n\n<p>Where two players support the same format, enabling both increases compatibility across different devices such as mobile phones. It is possible to increase compatibility further by providing multiple files in different formats for a single audio or video clip.</p>\n',1390841825),(2,'2cae00a94e62991ff989b011b15cb785','<div class=\"no-overflow\"><div class=\"text_to_html\"><p>lorem ipsum, etc.</p></div></div>',1390841754),(3,'51d05a02e374e78a7cdaacb7570c6541','<p>lorem ipsum, etc.</p>',1390841755),(4,'b151fd3052716debf3b8de739995f44a','<p>test role</p>',1390841826);
/*!40000 ALTER TABLE `mdl_cache_text` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_capabilities`
--

DROP TABLE IF EXISTS `mdl_capabilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_capabilities` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `captype` varchar(50) NOT NULL DEFAULT '',
  `contextlevel` bigint(10) NOT NULL DEFAULT '0',
  `component` varchar(100) NOT NULL DEFAULT '',
  `riskbitmask` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_capa_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=501 DEFAULT CHARSET=utf8 COMMENT='this defines all capabilities';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_capabilities`
--

LOCK TABLES `mdl_capabilities` WRITE;
/*!40000 ALTER TABLE `mdl_capabilities` DISABLE KEYS */;
INSERT INTO `mdl_capabilities` VALUES (1,'moodle/site:config','write',10,'moodle',62),(2,'moodle/site:readallmessages','read',10,'moodle',8),(3,'moodle/site:sendmessage','write',10,'moodle',16),(4,'moodle/site:approvecourse','write',10,'moodle',4),(5,'moodle/backup:backupcourse','write',50,'moodle',28),(6,'moodle/backup:backupsection','write',50,'moodle',28),(7,'moodle/backup:backupactivity','write',70,'moodle',28),(8,'moodle/backup:backuptargethub','write',50,'moodle',28),(9,'moodle/backup:backuptargetimport','write',50,'moodle',28),(10,'moodle/backup:downloadfile','write',50,'moodle',28),(11,'moodle/backup:configure','write',50,'moodle',28),(12,'moodle/backup:userinfo','read',50,'moodle',8),(13,'moodle/backup:anonymise','read',50,'moodle',8),(14,'moodle/restore:restorecourse','write',50,'moodle',28),(15,'moodle/restore:restoresection','write',50,'moodle',28),(16,'moodle/restore:restoreactivity','write',50,'moodle',28),(17,'moodle/restore:viewautomatedfilearea','write',50,'moodle',28),(18,'moodle/restore:restoretargethub','write',50,'moodle',28),(19,'moodle/restore:restoretargetimport','write',50,'moodle',28),(20,'moodle/restore:uploadfile','write',50,'moodle',28),(21,'moodle/restore:configure','write',50,'moodle',28),(22,'moodle/restore:rolldates','write',50,'moodle',0),(23,'moodle/restore:userinfo','write',50,'moodle',30),(24,'moodle/restore:createuser','write',10,'moodle',24),(25,'moodle/site:manageblocks','write',80,'moodle',20),(26,'moodle/site:accessallgroups','read',50,'moodle',0),(27,'moodle/site:viewfullnames','read',50,'moodle',0),(28,'moodle/site:viewuseridentity','read',50,'moodle',0),(29,'moodle/site:viewreports','read',50,'moodle',8),(30,'moodle/site:trustcontent','write',50,'moodle',4),(31,'moodle/site:uploadusers','write',10,'moodle',24),(32,'moodle/filter:manage','write',50,'moodle',0),(33,'moodle/user:create','write',10,'moodle',24),(34,'moodle/user:delete','write',10,'moodle',8),(35,'moodle/user:update','write',10,'moodle',24),(36,'moodle/user:viewdetails','read',50,'moodle',0),(37,'moodle/user:viewalldetails','read',30,'moodle',8),(38,'moodle/user:viewhiddendetails','read',50,'moodle',8),(39,'moodle/user:loginas','write',50,'moodle',30),(40,'moodle/user:managesyspages','write',10,'moodle',0),(41,'moodle/user:manageblocks','write',30,'moodle',0),(42,'moodle/user:manageownblocks','write',10,'moodle',0),(43,'moodle/user:manageownfiles','write',10,'moodle',0),(44,'moodle/user:ignoreuserquota','write',10,'moodle',0),(45,'moodle/my:configsyspages','write',10,'moodle',0),(46,'moodle/role:assign','write',50,'moodle',28),(47,'moodle/role:review','read',50,'moodle',8),(48,'moodle/role:override','write',50,'moodle',28),(49,'moodle/role:safeoverride','write',50,'moodle',16),(50,'moodle/role:manage','write',10,'moodle',28),(51,'moodle/role:switchroles','read',50,'moodle',12),(52,'moodle/category:manage','write',40,'moodle',4),(53,'moodle/category:viewhiddencategories','read',40,'moodle',0),(54,'moodle/cohort:manage','write',40,'moodle',0),(55,'moodle/cohort:assign','write',40,'moodle',0),(56,'moodle/cohort:view','read',50,'moodle',0),(57,'moodle/course:create','write',40,'moodle',4),(58,'moodle/course:request','write',10,'moodle',0),(59,'moodle/course:delete','write',50,'moodle',32),(60,'moodle/course:update','write',50,'moodle',4),(61,'moodle/course:view','read',50,'moodle',0),(62,'moodle/course:enrolreview','read',50,'moodle',8),(63,'moodle/course:enrolconfig','write',50,'moodle',8),(64,'moodle/course:bulkmessaging','write',50,'moodle',16),(65,'moodle/course:viewhiddenuserfields','read',50,'moodle',8),(66,'moodle/course:viewhiddencourses','read',50,'moodle',0),(67,'moodle/course:visibility','write',50,'moodle',0),(68,'moodle/course:managefiles','write',50,'moodle',4),(69,'moodle/course:ignorefilesizelimits','write',50,'moodle',0),(70,'moodle/course:manageactivities','write',70,'moodle',4),(71,'moodle/course:activityvisibility','write',70,'moodle',0),(72,'moodle/course:viewhiddenactivities','write',70,'moodle',0),(73,'moodle/course:viewparticipants','read',50,'moodle',0),(74,'moodle/course:changefullname','write',50,'moodle',4),(75,'moodle/course:changeshortname','write',50,'moodle',4),(76,'moodle/course:changeidnumber','write',50,'moodle',4),(77,'moodle/course:changecategory','write',50,'moodle',4),(78,'moodle/course:changesummary','write',50,'moodle',4),(79,'moodle/site:viewparticipants','read',10,'moodle',0),(80,'moodle/course:isincompletionreports','read',50,'moodle',0),(81,'moodle/course:viewscales','read',50,'moodle',0),(82,'moodle/course:managescales','write',50,'moodle',0),(83,'moodle/course:managegroups','write',50,'moodle',0),(84,'moodle/course:reset','write',50,'moodle',32),(85,'moodle/course:viewsuspendedusers','read',10,'moodle',0),(86,'moodle/blog:view','read',10,'moodle',0),(87,'moodle/blog:search','read',10,'moodle',0),(88,'moodle/blog:viewdrafts','read',10,'moodle',8),(89,'moodle/blog:create','write',10,'moodle',16),(90,'moodle/blog:manageentries','write',10,'moodle',16),(91,'moodle/blog:manageexternal','write',10,'moodle',16),(92,'moodle/blog:associatecourse','write',50,'moodle',0),(93,'moodle/blog:associatemodule','write',70,'moodle',0),(94,'moodle/calendar:manageownentries','write',50,'moodle',16),(95,'moodle/calendar:managegroupentries','write',50,'moodle',16),(96,'moodle/calendar:manageentries','write',50,'moodle',16),(97,'moodle/user:editprofile','write',30,'moodle',24),(98,'moodle/user:editownprofile','write',10,'moodle',16),(99,'moodle/user:changeownpassword','write',10,'moodle',0),(100,'moodle/user:readuserposts','read',30,'moodle',0),(101,'moodle/user:readuserblogs','read',30,'moodle',0),(102,'moodle/user:viewuseractivitiesreport','read',30,'moodle',8),(103,'moodle/user:editmessageprofile','write',30,'moodle',16),(104,'moodle/user:editownmessageprofile','write',10,'moodle',0),(105,'moodle/question:managecategory','write',50,'moodle',20),(106,'moodle/question:add','write',50,'moodle',20),(107,'moodle/question:editmine','write',50,'moodle',20),(108,'moodle/question:editall','write',50,'moodle',20),(109,'moodle/question:viewmine','read',50,'moodle',0),(110,'moodle/question:viewall','read',50,'moodle',0),(111,'moodle/question:usemine','read',50,'moodle',0),(112,'moodle/question:useall','read',50,'moodle',0),(113,'moodle/question:movemine','write',50,'moodle',0),(114,'moodle/question:moveall','write',50,'moodle',0),(115,'moodle/question:config','write',10,'moodle',2),(116,'moodle/question:flag','write',50,'moodle',0),(117,'moodle/site:doclinks','read',10,'moodle',0),(118,'moodle/course:sectionvisibility','write',50,'moodle',0),(119,'moodle/course:useremail','write',50,'moodle',0),(120,'moodle/course:viewhiddensections','write',50,'moodle',0),(121,'moodle/course:setcurrentsection','write',50,'moodle',0),(122,'moodle/course:movesections','write',50,'moodle',0),(123,'moodle/site:mnetlogintoremote','read',10,'moodle',0),(124,'moodle/grade:viewall','read',50,'moodle',8),(125,'moodle/grade:view','read',50,'moodle',0),(126,'moodle/grade:viewhidden','read',50,'moodle',8),(127,'moodle/grade:import','write',50,'moodle',12),(128,'moodle/grade:export','read',50,'moodle',8),(129,'moodle/grade:manage','write',50,'moodle',12),(130,'moodle/grade:edit','write',50,'moodle',12),(131,'moodle/grade:managegradingforms','write',50,'moodle',12),(132,'moodle/grade:sharegradingforms','write',10,'moodle',4),(133,'moodle/grade:managesharedforms','write',10,'moodle',4),(134,'moodle/grade:manageoutcomes','write',50,'moodle',0),(135,'moodle/grade:manageletters','write',50,'moodle',0),(136,'moodle/grade:hide','write',50,'moodle',0),(137,'moodle/grade:lock','write',50,'moodle',0),(138,'moodle/grade:unlock','write',50,'moodle',0),(139,'moodle/my:manageblocks','write',10,'moodle',0),(140,'moodle/notes:view','read',50,'moodle',0),(141,'moodle/notes:manage','write',50,'moodle',16),(142,'moodle/tag:manage','write',10,'moodle',16),(143,'moodle/tag:create','write',10,'moodle',16),(144,'moodle/tag:edit','write',10,'moodle',16),(145,'moodle/tag:flag','write',10,'moodle',16),(146,'moodle/tag:editblocks','write',10,'moodle',0),(147,'moodle/block:view','read',80,'moodle',0),(148,'moodle/block:edit','write',80,'moodle',20),(149,'moodle/portfolio:export','read',10,'moodle',0),(150,'moodle/comment:view','read',50,'moodle',0),(151,'moodle/comment:post','write',50,'moodle',24),(152,'moodle/comment:delete','write',50,'moodle',32),(153,'moodle/webservice:createtoken','write',10,'moodle',62),(154,'moodle/webservice:createmobiletoken','write',10,'moodle',24),(155,'moodle/rating:view','read',50,'moodle',0),(156,'moodle/rating:viewany','read',50,'moodle',8),(157,'moodle/rating:viewall','read',50,'moodle',8),(158,'moodle/rating:rate','write',50,'moodle',0),(159,'moodle/course:publish','write',10,'moodle',24),(160,'moodle/course:markcomplete','write',50,'moodle',0),(161,'moodle/community:add','write',10,'moodle',0),(162,'moodle/community:download','write',10,'moodle',0),(163,'moodle/badges:manageglobalsettings','write',10,'moodle',34),(164,'moodle/badges:viewbadges','read',50,'moodle',0),(165,'moodle/badges:manageownbadges','write',30,'moodle',0),(166,'moodle/badges:viewotherbadges','read',30,'moodle',0),(167,'moodle/badges:earnbadge','write',50,'moodle',0),(168,'moodle/badges:createbadge','write',50,'moodle',16),(169,'moodle/badges:deletebadge','write',50,'moodle',32),(170,'moodle/badges:configuredetails','write',50,'moodle',16),(171,'moodle/badges:configurecriteria','write',50,'moodle',0),(172,'moodle/badges:configuremessages','write',50,'moodle',16),(173,'moodle/badges:awardbadge','write',50,'moodle',16),(174,'moodle/badges:viewawarded','read',50,'moodle',8),(175,'mod/assign:view','read',70,'mod_assign',0),(176,'mod/assign:submit','write',70,'mod_assign',0),(177,'mod/assign:grade','write',70,'mod_assign',4),(178,'mod/assign:exportownsubmission','read',70,'mod_assign',0),(179,'mod/assign:addinstance','write',50,'mod_assign',4),(180,'mod/assign:grantextension','write',70,'mod_assign',0),(181,'mod/assign:revealidentities','write',70,'mod_assign',0),(182,'mod/assign:reviewgrades','write',70,'mod_assign',0),(183,'mod/assign:releasegrades','write',70,'mod_assign',0),(184,'mod/assign:managegrades','write',70,'mod_assign',0),(185,'mod/assign:manageallocations','write',70,'mod_assign',0),(186,'mod/assignment:view','read',70,'mod_assignment',0),(187,'mod/assignment:addinstance','write',50,'mod_assignment',4),(188,'mod/assignment:submit','write',70,'mod_assignment',0),(189,'mod/assignment:grade','write',70,'mod_assignment',4),(190,'mod/assignment:exportownsubmission','read',70,'mod_assignment',0),(191,'mod/book:addinstance','write',50,'mod_book',4),(192,'mod/book:read','read',70,'mod_book',0),(193,'mod/book:viewhiddenchapters','read',70,'mod_book',0),(194,'mod/book:edit','write',70,'mod_book',4),(195,'mod/chat:addinstance','write',50,'mod_chat',4),(196,'mod/chat:chat','write',70,'mod_chat',16),(197,'mod/chat:readlog','read',70,'mod_chat',0),(198,'mod/chat:deletelog','write',70,'mod_chat',0),(199,'mod/chat:exportparticipatedsession','read',70,'mod_chat',8),(200,'mod/chat:exportsession','read',70,'mod_chat',8),(201,'mod/choice:addinstance','write',50,'mod_choice',4),(202,'mod/choice:choose','write',70,'mod_choice',0),(203,'mod/choice:readresponses','read',70,'mod_choice',0),(204,'mod/choice:deleteresponses','write',70,'mod_choice',0),(205,'mod/choice:downloadresponses','read',70,'mod_choice',0),(206,'mod/data:addinstance','write',50,'mod_data',4),(207,'mod/data:viewentry','read',70,'mod_data',0),(208,'mod/data:writeentry','write',70,'mod_data',16),(209,'mod/data:comment','write',70,'mod_data',16),(210,'mod/data:rate','write',70,'mod_data',0),(211,'mod/data:viewrating','read',70,'mod_data',0),(212,'mod/data:viewanyrating','read',70,'mod_data',8),(213,'mod/data:viewallratings','read',70,'mod_data',8),(214,'mod/data:approve','write',70,'mod_data',16),(215,'mod/data:manageentries','write',70,'mod_data',16),(216,'mod/data:managecomments','write',70,'mod_data',16),(217,'mod/data:managetemplates','write',70,'mod_data',20),(218,'mod/data:viewalluserpresets','read',70,'mod_data',0),(219,'mod/data:manageuserpresets','write',70,'mod_data',20),(220,'mod/data:exportentry','read',70,'mod_data',8),(221,'mod/data:exportownentry','read',70,'mod_data',0),(222,'mod/data:exportallentries','read',70,'mod_data',8),(223,'mod/data:exportuserinfo','read',70,'mod_data',8),(224,'mod/feedback:addinstance','write',50,'mod_feedback',4),(225,'mod/feedback:view','read',70,'mod_feedback',0),(226,'mod/feedback:complete','write',70,'mod_feedback',16),(227,'mod/feedback:viewanalysepage','read',70,'mod_feedback',8),(228,'mod/feedback:deletesubmissions','write',70,'mod_feedback',0),(229,'mod/feedback:mapcourse','write',70,'mod_feedback',0),(230,'mod/feedback:edititems','write',70,'mod_feedback',20),(231,'mod/feedback:createprivatetemplate','write',70,'mod_feedback',16),(232,'mod/feedback:createpublictemplate','write',70,'mod_feedback',16),(233,'mod/feedback:deletetemplate','write',70,'mod_feedback',0),(234,'mod/feedback:viewreports','read',70,'mod_feedback',8),(235,'mod/feedback:receivemail','read',70,'mod_feedback',8),(236,'mod/folder:addinstance','write',50,'mod_folder',4),(237,'mod/folder:view','read',70,'mod_folder',0),(238,'mod/folder:managefiles','write',70,'mod_folder',16),(239,'mod/forum:addinstance','write',50,'mod_forum',4),(240,'mod/forum:viewdiscussion','read',70,'mod_forum',0),(241,'mod/forum:viewhiddentimedposts','read',70,'mod_forum',0),(242,'mod/forum:startdiscussion','write',70,'mod_forum',16),(243,'mod/forum:replypost','write',70,'mod_forum',16),(244,'mod/forum:addnews','write',70,'mod_forum',16),(245,'mod/forum:replynews','write',70,'mod_forum',16),(246,'mod/forum:viewrating','read',70,'mod_forum',0),(247,'mod/forum:viewanyrating','read',70,'mod_forum',8),(248,'mod/forum:viewallratings','read',70,'mod_forum',8),(249,'mod/forum:rate','write',70,'mod_forum',0),(250,'mod/forum:createattachment','write',70,'mod_forum',16),(251,'mod/forum:deleteownpost','read',70,'mod_forum',0),(252,'mod/forum:deleteanypost','read',70,'mod_forum',0),(253,'mod/forum:splitdiscussions','read',70,'mod_forum',0),(254,'mod/forum:movediscussions','read',70,'mod_forum',0),(255,'mod/forum:editanypost','write',70,'mod_forum',16),(256,'mod/forum:viewqandawithoutposting','read',70,'mod_forum',0),(257,'mod/forum:viewsubscribers','read',70,'mod_forum',0),(258,'mod/forum:managesubscriptions','read',70,'mod_forum',16),(259,'mod/forum:postwithoutthrottling','write',70,'mod_forum',16),(260,'mod/forum:exportdiscussion','read',70,'mod_forum',8),(261,'mod/forum:exportpost','read',70,'mod_forum',8),(262,'mod/forum:exportownpost','read',70,'mod_forum',8),(263,'mod/forum:addquestion','write',70,'mod_forum',16),(264,'mod/forum:allowforcesubscribe','read',70,'mod_forum',0),(265,'mod/glossary:addinstance','write',50,'mod_glossary',4),(266,'mod/glossary:view','read',70,'mod_glossary',0),(267,'mod/glossary:write','write',70,'mod_glossary',16),(268,'mod/glossary:manageentries','write',70,'mod_glossary',16),(269,'mod/glossary:managecategories','write',70,'mod_glossary',16),(270,'mod/glossary:comment','write',70,'mod_glossary',16),(271,'mod/glossary:managecomments','write',70,'mod_glossary',16),(272,'mod/glossary:import','write',70,'mod_glossary',16),(273,'mod/glossary:export','read',70,'mod_glossary',0),(274,'mod/glossary:approve','write',70,'mod_glossary',16),(275,'mod/glossary:rate','write',70,'mod_glossary',0),(276,'mod/glossary:viewrating','read',70,'mod_glossary',0),(277,'mod/glossary:viewanyrating','read',70,'mod_glossary',8),(278,'mod/glossary:viewallratings','read',70,'mod_glossary',8),(279,'mod/glossary:exportentry','read',70,'mod_glossary',8),(280,'mod/glossary:exportownentry','read',70,'mod_glossary',0),(281,'mod/imscp:view','read',70,'mod_imscp',0),(282,'mod/imscp:addinstance','write',50,'mod_imscp',4),(283,'mod/label:addinstance','write',50,'mod_label',4),(284,'mod/lesson:addinstance','write',50,'mod_lesson',4),(285,'mod/lesson:edit','write',70,'mod_lesson',4),(286,'mod/lesson:manage','write',70,'mod_lesson',0),(287,'mod/lti:view','read',70,'mod_lti',0),(288,'mod/lti:addinstance','write',50,'mod_lti',4),(289,'mod/lti:grade','write',70,'mod_lti',8),(290,'mod/lti:manage','write',70,'mod_lti',8),(291,'mod/lti:addcoursetool','write',50,'mod_lti',0),(292,'mod/lti:requesttooladd','write',50,'mod_lti',0),(293,'mod/page:view','read',70,'mod_page',0),(294,'mod/page:addinstance','write',50,'mod_page',4),(295,'mod/quiz:view','read',70,'mod_quiz',0),(296,'mod/quiz:addinstance','write',50,'mod_quiz',4),(297,'mod/quiz:attempt','write',70,'mod_quiz',16),(298,'mod/quiz:reviewmyattempts','read',70,'mod_quiz',0),(299,'mod/quiz:manage','write',70,'mod_quiz',16),(300,'mod/quiz:manageoverrides','write',70,'mod_quiz',0),(301,'mod/quiz:preview','write',70,'mod_quiz',0),(302,'mod/quiz:grade','write',70,'mod_quiz',16),(303,'mod/quiz:regrade','write',70,'mod_quiz',16),(304,'mod/quiz:viewreports','read',70,'mod_quiz',8),(305,'mod/quiz:deleteattempts','write',70,'mod_quiz',32),(306,'mod/quiz:ignoretimelimits','read',70,'mod_quiz',0),(307,'mod/quiz:emailconfirmsubmission','read',70,'mod_quiz',0),(308,'mod/quiz:emailnotifysubmission','read',70,'mod_quiz',0),(309,'mod/quiz:emailwarnoverdue','read',70,'mod_quiz',0),(310,'mod/resource:view','read',70,'mod_resource',0),(311,'mod/resource:addinstance','write',50,'mod_resource',4),(312,'mod/scorm:addinstance','write',50,'mod_scorm',4),(313,'mod/scorm:viewreport','read',70,'mod_scorm',0),(314,'mod/scorm:skipview','write',70,'mod_scorm',0),(315,'mod/scorm:savetrack','write',70,'mod_scorm',0),(316,'mod/scorm:viewscores','read',70,'mod_scorm',0),(317,'mod/scorm:deleteresponses','read',70,'mod_scorm',0),(318,'mod/scorm:deleteownresponses','write',70,'mod_scorm',0),(319,'mod/survey:addinstance','write',50,'mod_survey',4),(320,'mod/survey:participate','read',70,'mod_survey',0),(321,'mod/survey:readresponses','read',70,'mod_survey',0),(322,'mod/survey:download','read',70,'mod_survey',0),(323,'mod/url:view','read',70,'mod_url',0),(324,'mod/url:addinstance','write',50,'mod_url',4),(325,'mod/wiki:addinstance','write',50,'mod_wiki',4),(326,'mod/wiki:viewpage','read',70,'mod_wiki',0),(327,'mod/wiki:editpage','write',70,'mod_wiki',16),(328,'mod/wiki:createpage','write',70,'mod_wiki',16),(329,'mod/wiki:viewcomment','read',70,'mod_wiki',0),(330,'mod/wiki:editcomment','write',70,'mod_wiki',16),(331,'mod/wiki:managecomment','write',70,'mod_wiki',0),(332,'mod/wiki:managefiles','write',70,'mod_wiki',0),(333,'mod/wiki:overridelock','write',70,'mod_wiki',0),(334,'mod/wiki:managewiki','write',70,'mod_wiki',0),(335,'mod/workshop:view','read',70,'mod_workshop',0),(336,'mod/workshop:addinstance','write',50,'mod_workshop',4),(337,'mod/workshop:switchphase','write',70,'mod_workshop',0),(338,'mod/workshop:editdimensions','write',70,'mod_workshop',4),(339,'mod/workshop:submit','write',70,'mod_workshop',0),(340,'mod/workshop:peerassess','write',70,'mod_workshop',0),(341,'mod/workshop:manageexamples','write',70,'mod_workshop',0),(342,'mod/workshop:allocate','write',70,'mod_workshop',0),(343,'mod/workshop:publishsubmissions','write',70,'mod_workshop',0),(344,'mod/workshop:viewauthornames','read',70,'mod_workshop',0),(345,'mod/workshop:viewreviewernames','read',70,'mod_workshop',0),(346,'mod/workshop:viewallsubmissions','read',70,'mod_workshop',0),(347,'mod/workshop:viewpublishedsubmissions','read',70,'mod_workshop',0),(348,'mod/workshop:viewauthorpublished','read',70,'mod_workshop',0),(349,'mod/workshop:viewallassessments','read',70,'mod_workshop',0),(350,'mod/workshop:overridegrades','write',70,'mod_workshop',0),(351,'mod/workshop:ignoredeadlines','write',70,'mod_workshop',0),(352,'enrol/category:synchronised','write',10,'enrol_category',0),(353,'enrol/cohort:config','write',50,'enrol_cohort',0),(354,'enrol/cohort:unenrol','write',50,'enrol_cohort',0),(355,'enrol/database:unenrol','write',50,'enrol_database',0),(356,'enrol/flatfile:manage','write',50,'enrol_flatfile',0),(357,'enrol/flatfile:unenrol','write',50,'enrol_flatfile',0),(358,'enrol/guest:config','write',50,'enrol_guest',0),(359,'enrol/ldap:manage','write',50,'enrol_ldap',0),(360,'enrol/manual:config','write',50,'enrol_manual',0),(361,'enrol/manual:enrol','write',50,'enrol_manual',0),(362,'enrol/manual:manage','write',50,'enrol_manual',0),(363,'enrol/manual:unenrol','write',50,'enrol_manual',0),(364,'enrol/manual:unenrolself','write',50,'enrol_manual',0),(365,'enrol/meta:config','write',50,'enrol_meta',0),(366,'enrol/meta:selectaslinked','read',50,'enrol_meta',0),(367,'enrol/meta:unenrol','write',50,'enrol_meta',0),(368,'enrol/paypal:config','write',50,'enrol_paypal',0),(369,'enrol/paypal:manage','write',50,'enrol_paypal',0),(370,'enrol/paypal:unenrol','write',50,'enrol_paypal',0),(371,'enrol/paypal:unenrolself','write',50,'enrol_paypal',0),(372,'enrol/self:config','write',50,'enrol_self',0),(373,'enrol/self:manage','write',50,'enrol_self',0),(374,'enrol/self:unenrolself','write',50,'enrol_self',0),(375,'enrol/self:unenrol','write',50,'enrol_self',0),(376,'block/activity_modules:addinstance','write',80,'block_activity_modules',20),(377,'block/admin_bookmarks:myaddinstance','write',10,'block_admin_bookmarks',0),(378,'block/admin_bookmarks:addinstance','write',80,'block_admin_bookmarks',20),(379,'block/badges:addinstance','read',80,'block_badges',0),(380,'block/badges:myaddinstance','read',10,'block_badges',8),(381,'block/blog_menu:addinstance','write',80,'block_blog_menu',20),(382,'block/blog_recent:addinstance','write',80,'block_blog_recent',20),(383,'block/blog_tags:addinstance','write',80,'block_blog_tags',20),(384,'block/calendar_month:myaddinstance','write',10,'block_calendar_month',0),(385,'block/calendar_month:addinstance','write',80,'block_calendar_month',20),(386,'block/calendar_upcoming:myaddinstance','write',10,'block_calendar_upcoming',0),(387,'block/calendar_upcoming:addinstance','write',80,'block_calendar_upcoming',20),(388,'block/comments:myaddinstance','write',10,'block_comments',0),(389,'block/comments:addinstance','write',80,'block_comments',20),(390,'block/community:myaddinstance','write',10,'block_community',0),(391,'block/community:addinstance','write',80,'block_community',20),(392,'block/completionstatus:addinstance','write',80,'block_completionstatus',20),(393,'block/course_list:myaddinstance','write',10,'block_course_list',0),(394,'block/course_list:addinstance','write',80,'block_course_list',20),(395,'block/course_overview:myaddinstance','write',10,'block_course_overview',0),(396,'block/course_overview:addinstance','write',80,'block_course_overview',20),(397,'block/course_summary:addinstance','write',80,'block_course_summary',20),(398,'block/feedback:addinstance','write',80,'block_feedback',20),(399,'block/glossary_random:myaddinstance','write',10,'block_glossary_random',0),(400,'block/glossary_random:addinstance','write',80,'block_glossary_random',20),(401,'block/html:myaddinstance','write',10,'block_html',0),(402,'block/html:addinstance','write',80,'block_html',20),(403,'block/login:addinstance','write',80,'block_login',20),(404,'block/mentees:myaddinstance','write',10,'block_mentees',0),(405,'block/mentees:addinstance','write',80,'block_mentees',20),(406,'block/messages:myaddinstance','write',10,'block_messages',0),(407,'block/messages:addinstance','write',80,'block_messages',20),(408,'block/mnet_hosts:myaddinstance','write',10,'block_mnet_hosts',0),(409,'block/mnet_hosts:addinstance','write',80,'block_mnet_hosts',20),(410,'block/myprofile:myaddinstance','write',10,'block_myprofile',0),(411,'block/myprofile:addinstance','write',80,'block_myprofile',20),(412,'block/navigation:myaddinstance','write',10,'block_navigation',0),(413,'block/navigation:addinstance','write',80,'block_navigation',20),(414,'block/news_items:myaddinstance','write',10,'block_news_items',0),(415,'block/news_items:addinstance','write',80,'block_news_items',20),(416,'block/online_users:myaddinstance','write',10,'block_online_users',0),(417,'block/online_users:addinstance','write',80,'block_online_users',20),(418,'block/online_users:viewlist','read',80,'block_online_users',0),(419,'block/participants:addinstance','write',80,'block_participants',20),(420,'block/private_files:myaddinstance','write',10,'block_private_files',0),(421,'block/private_files:addinstance','write',80,'block_private_files',20),(422,'block/quiz_results:addinstance','write',80,'block_quiz_results',20),(423,'block/recent_activity:addinstance','write',80,'block_recent_activity',20),(424,'block/rss_client:myaddinstance','write',10,'block_rss_client',0),(425,'block/rss_client:addinstance','write',80,'block_rss_client',20),(426,'block/rss_client:manageownfeeds','write',80,'block_rss_client',0),(427,'block/rss_client:manageanyfeeds','write',80,'block_rss_client',16),(428,'block/search_forums:addinstance','write',80,'block_search_forums',20),(429,'block/section_links:addinstance','write',80,'block_section_links',20),(430,'block/selfcompletion:addinstance','write',80,'block_selfcompletion',20),(431,'block/settings:myaddinstance','write',10,'block_settings',0),(432,'block/settings:addinstance','write',80,'block_settings',20),(433,'block/site_main_menu:addinstance','write',80,'block_site_main_menu',20),(434,'block/social_activities:addinstance','write',80,'block_social_activities',20),(435,'block/tag_flickr:addinstance','write',80,'block_tag_flickr',20),(436,'block/tag_youtube:addinstance','write',80,'block_tag_youtube',20),(437,'block/tags:myaddinstance','write',10,'block_tags',0),(438,'block/tags:addinstance','write',80,'block_tags',20),(439,'report/completion:view','read',50,'report_completion',8),(440,'report/courseoverview:view','read',10,'report_courseoverview',8),(441,'report/log:view','read',50,'report_log',8),(442,'report/log:viewtoday','read',50,'report_log',8),(443,'report/loglive:view','read',50,'report_loglive',8),(444,'report/outline:view','read',50,'report_outline',8),(445,'report/participation:view','read',50,'report_participation',8),(446,'report/performance:view','read',10,'report_performance',2),(447,'report/progress:view','read',50,'report_progress',8),(448,'report/questioninstances:view','read',10,'report_questioninstances',0),(449,'report/security:view','read',10,'report_security',2),(450,'report/stats:view','read',50,'report_stats',8),(451,'gradeexport/ods:view','read',50,'gradeexport_ods',8),(452,'gradeexport/ods:publish','read',50,'gradeexport_ods',8),(453,'gradeexport/txt:view','read',50,'gradeexport_txt',8),(454,'gradeexport/txt:publish','read',50,'gradeexport_txt',8),(455,'gradeexport/xls:view','read',50,'gradeexport_xls',8),(456,'gradeexport/xls:publish','read',50,'gradeexport_xls',8),(457,'gradeexport/xml:view','read',50,'gradeexport_xml',8),(458,'gradeexport/xml:publish','read',50,'gradeexport_xml',8),(459,'gradeimport/csv:view','write',50,'gradeimport_csv',0),(460,'gradeimport/xml:view','write',50,'gradeimport_xml',0),(461,'gradeimport/xml:publish','write',50,'gradeimport_xml',0),(462,'gradereport/grader:view','read',50,'gradereport_grader',8),(463,'gradereport/outcomes:view','read',50,'gradereport_outcomes',8),(464,'gradereport/overview:view','read',50,'gradereport_overview',8),(465,'gradereport/user:view','read',50,'gradereport_user',8),(466,'webservice/amf:use','read',50,'webservice_amf',62),(467,'webservice/rest:use','read',50,'webservice_rest',62),(468,'webservice/soap:use','read',50,'webservice_soap',62),(469,'webservice/xmlrpc:use','read',50,'webservice_xmlrpc',62),(470,'repository/alfresco:view','read',70,'repository_alfresco',0),(471,'repository/areafiles:view','read',70,'repository_areafiles',0),(472,'repository/boxnet:view','read',70,'repository_boxnet',0),(473,'repository/coursefiles:view','read',70,'repository_coursefiles',0),(474,'repository/dropbox:view','read',70,'repository_dropbox',0),(475,'repository/equella:view','read',70,'repository_equella',0),(476,'repository/filesystem:view','read',70,'repository_filesystem',0),(477,'repository/flickr:view','read',70,'repository_flickr',0),(478,'repository/flickr_public:view','read',70,'repository_flickr_public',0),(479,'repository/googledocs:view','read',70,'repository_googledocs',0),(480,'repository/local:view','read',70,'repository_local',0),(481,'repository/merlot:view','read',70,'repository_merlot',0),(482,'repository/picasa:view','read',70,'repository_picasa',0),(483,'repository/recent:view','read',70,'repository_recent',0),(484,'repository/s3:view','read',70,'repository_s3',0),(485,'repository/skydrive:view','read',70,'repository_skydrive',0),(486,'repository/upload:view','read',70,'repository_upload',0),(487,'repository/url:view','read',70,'repository_url',0),(488,'repository/user:view','read',70,'repository_user',0),(489,'repository/webdav:view','read',70,'repository_webdav',0),(490,'repository/wikimedia:view','read',70,'repository_wikimedia',0),(491,'repository/youtube:view','read',70,'repository_youtube',0),(492,'tool/customlang:view','read',10,'tool_customlang',2),(493,'tool/customlang:edit','write',10,'tool_customlang',6),(494,'tool/uploaduser:uploaduserpictures','write',10,'tool_uploaduser',16),(495,'booktool/exportimscp:export','read',70,'booktool_exportimscp',0),(496,'booktool/importhtml:import','write',70,'booktool_importhtml',4),(497,'booktool/print:print','read',70,'booktool_print',0),(498,'quiz/grading:viewstudentnames','read',70,'quiz_grading',0),(499,'quiz/grading:viewidnumber','read',70,'quiz_grading',0),(500,'quiz/statistics:view','read',70,'quiz_statistics',0);
/*!40000 ALTER TABLE `mdl_capabilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_chat`
--

DROP TABLE IF EXISTS `mdl_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_chat` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `keepdays` bigint(11) NOT NULL DEFAULT '0',
  `studentlogs` smallint(4) NOT NULL DEFAULT '0',
  `chattime` bigint(10) NOT NULL DEFAULT '0',
  `schedule` smallint(4) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_chat_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Each of these is a chat room';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_chat`
--

LOCK TABLES `mdl_chat` WRITE;
/*!40000 ALTER TABLE `mdl_chat` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_chat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_chat_messages`
--

DROP TABLE IF EXISTS `mdl_chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_chat_messages` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `chatid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `groupid` bigint(10) NOT NULL DEFAULT '0',
  `system` tinyint(1) NOT NULL DEFAULT '0',
  `message` longtext NOT NULL,
  `timestamp` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_chatmess_use_ix` (`userid`),
  KEY `mdl_chatmess_gro_ix` (`groupid`),
  KEY `mdl_chatmess_timcha_ix` (`timestamp`,`chatid`),
  KEY `mdl_chatmess_cha_ix` (`chatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores all the actual chat messages';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_chat_messages`
--

LOCK TABLES `mdl_chat_messages` WRITE;
/*!40000 ALTER TABLE `mdl_chat_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_chat_messages_current`
--

DROP TABLE IF EXISTS `mdl_chat_messages_current`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_chat_messages_current` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `chatid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `groupid` bigint(10) NOT NULL DEFAULT '0',
  `system` tinyint(1) NOT NULL DEFAULT '0',
  `message` longtext NOT NULL,
  `timestamp` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_chatmesscurr_use_ix` (`userid`),
  KEY `mdl_chatmesscurr_gro_ix` (`groupid`),
  KEY `mdl_chatmesscurr_timcha_ix` (`timestamp`,`chatid`),
  KEY `mdl_chatmesscurr_cha_ix` (`chatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores current session';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_chat_messages_current`
--

LOCK TABLES `mdl_chat_messages_current` WRITE;
/*!40000 ALTER TABLE `mdl_chat_messages_current` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_chat_messages_current` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_chat_users`
--

DROP TABLE IF EXISTS `mdl_chat_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_chat_users` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `chatid` bigint(11) NOT NULL DEFAULT '0',
  `userid` bigint(11) NOT NULL DEFAULT '0',
  `groupid` bigint(11) NOT NULL DEFAULT '0',
  `version` varchar(16) NOT NULL DEFAULT '',
  `ip` varchar(45) NOT NULL DEFAULT '',
  `firstping` bigint(10) NOT NULL DEFAULT '0',
  `lastping` bigint(10) NOT NULL DEFAULT '0',
  `lastmessageping` bigint(10) NOT NULL DEFAULT '0',
  `sid` varchar(32) NOT NULL DEFAULT '',
  `course` bigint(10) NOT NULL DEFAULT '0',
  `lang` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_chatuser_use_ix` (`userid`),
  KEY `mdl_chatuser_las_ix` (`lastping`),
  KEY `mdl_chatuser_gro_ix` (`groupid`),
  KEY `mdl_chatuser_cha_ix` (`chatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Keeps track of which users are in which chat rooms';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_chat_users`
--

LOCK TABLES `mdl_chat_users` WRITE;
/*!40000 ALTER TABLE `mdl_chat_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_chat_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_choice`
--

DROP TABLE IF EXISTS `mdl_choice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_choice` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `publish` tinyint(2) NOT NULL DEFAULT '0',
  `showresults` tinyint(2) NOT NULL DEFAULT '0',
  `display` smallint(4) NOT NULL DEFAULT '0',
  `allowupdate` tinyint(2) NOT NULL DEFAULT '0',
  `showunanswered` tinyint(2) NOT NULL DEFAULT '0',
  `limitanswers` tinyint(2) NOT NULL DEFAULT '0',
  `timeopen` bigint(10) NOT NULL DEFAULT '0',
  `timeclose` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `completionsubmit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_choi_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available choices are stored here';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_choice`
--

LOCK TABLES `mdl_choice` WRITE;
/*!40000 ALTER TABLE `mdl_choice` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_choice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_choice_answers`
--

DROP TABLE IF EXISTS `mdl_choice_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_choice_answers` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `choiceid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `optionid` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_choiansw_use_ix` (`userid`),
  KEY `mdl_choiansw_cho_ix` (`choiceid`),
  KEY `mdl_choiansw_opt_ix` (`optionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='choices performed by users';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_choice_answers`
--

LOCK TABLES `mdl_choice_answers` WRITE;
/*!40000 ALTER TABLE `mdl_choice_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_choice_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_choice_options`
--

DROP TABLE IF EXISTS `mdl_choice_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_choice_options` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `choiceid` bigint(10) NOT NULL DEFAULT '0',
  `text` longtext,
  `maxanswers` bigint(10) DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_choiopti_cho_ix` (`choiceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='available options to choice';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_choice_options`
--

LOCK TABLES `mdl_choice_options` WRITE;
/*!40000 ALTER TABLE `mdl_choice_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_choice_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_cohort`
--

DROP TABLE IF EXISTS `mdl_cohort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_cohort` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextid` bigint(10) NOT NULL,
  `name` varchar(254) NOT NULL DEFAULT '',
  `idnumber` varchar(100) DEFAULT NULL,
  `description` longtext,
  `descriptionformat` tinyint(2) NOT NULL,
  `component` varchar(100) NOT NULL DEFAULT '',
  `timecreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_coho_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Each record represents one cohort (aka site-wide group).';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_cohort`
--

LOCK TABLES `mdl_cohort` WRITE;
/*!40000 ALTER TABLE `mdl_cohort` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_cohort` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_cohort_members`
--

DROP TABLE IF EXISTS `mdl_cohort_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_cohort_members` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `cohortid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `timeadded` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_cohomemb_cohuse_uix` (`cohortid`,`userid`),
  KEY `mdl_cohomemb_coh_ix` (`cohortid`),
  KEY `mdl_cohomemb_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link a user to a cohort.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_cohort_members`
--

LOCK TABLES `mdl_cohort_members` WRITE;
/*!40000 ALTER TABLE `mdl_cohort_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_cohort_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_comments`
--

DROP TABLE IF EXISTS `mdl_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_comments` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextid` bigint(10) NOT NULL,
  `commentarea` varchar(255) NOT NULL DEFAULT '',
  `itemid` bigint(10) NOT NULL,
  `content` longtext NOT NULL,
  `format` tinyint(2) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL,
  `timecreated` bigint(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='moodle comments module';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_comments`
--

LOCK TABLES `mdl_comments` WRITE;
/*!40000 ALTER TABLE `mdl_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_config`
--

DROP TABLE IF EXISTS `mdl_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_config` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_conf_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=439 DEFAULT CHARSET=utf8 COMMENT='Moodle configuration variables';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_config`
--

LOCK TABLES `mdl_config` WRITE;
/*!40000 ALTER TABLE `mdl_config` DISABLE KEYS */;
INSERT INTO `mdl_config` VALUES (2,'rolesactive','1'),(3,'auth','email'),(4,'auth_pop3mailbox','INBOX'),(5,'enrol_plugins_enabled','manual,guest,self,cohort'),(6,'theme','standard'),(7,'filter_multilang_converted','1'),(8,'siteidentifier','B6s80rgtMuvsOKjJFK5dYMwHxjAHYeax127.0.0.1'),(9,'backup_version','2008111700'),(10,'backup_release','2.0 dev'),(11,'mnet_dispatcher_mode','off'),(12,'sessiontimeout','7200'),(13,'stringfilters',''),(14,'filterall','0'),(15,'texteditors','tinymce,textarea'),(16,'mnet_localhost_id','1'),(17,'mnet_all_hosts_id','2'),(18,'siteguest','1'),(19,'siteadmins','2'),(20,'themerev','1390505190'),(21,'jsrev','1390505189'),(22,'gdversion','2'),(23,'licenses','unknown,allrightsreserved,public,cc,cc-nd,cc-nc-nd,cc-nc,cc-nc-sa,cc-sa'),(24,'version','2013111801.01'),(25,'enableoutcomes','0'),(26,'usecomments','1'),(27,'usetags','1'),(28,'enablenotes','1'),(29,'enableportfolios','0'),(30,'enablewebservices','0'),(31,'messaging','1'),(32,'messaginghidereadnotifications','0'),(33,'messagingdeletereadnotificationsdelay','604800'),(34,'messagingallowemailoverride','0'),(35,'enablestats','0'),(36,'enablerssfeeds','0'),(37,'enableblogs','1'),(38,'enablecompletion','0'),(39,'completiondefault','1'),(40,'enableavailability','0'),(41,'enableplagiarism','0'),(42,'enablebadges','1'),(43,'autologinguests','0'),(44,'hiddenuserfields',''),(45,'showuseridentity','email'),(46,'fullnamedisplay','language'),(47,'maxusersperpage','100'),(48,'enablegravatar','0'),(49,'gravatardefaulturl','mm'),(50,'enablecourserequests','0'),(51,'defaultrequestcategory','1'),(52,'requestcategoryselection','0'),(53,'courserequestnotify',''),(54,'grade_profilereport','user'),(55,'grade_aggregationposition','1'),(56,'grade_includescalesinaggregation','1'),(57,'grade_hiddenasdate','0'),(58,'gradepublishing','0'),(59,'grade_export_displaytype','1'),(60,'grade_export_decimalpoints','2'),(61,'grade_navmethod','0'),(62,'grade_export_userprofilefields','firstname,lastname,idnumber,institution,department,email'),(63,'grade_export_customprofilefields',''),(64,'recovergradesdefault','0'),(65,'gradeexport',''),(66,'unlimitedgrades','0'),(67,'grade_hideforcedsettings','1'),(68,'grade_aggregation','11'),(69,'grade_aggregation_flag','0'),(70,'grade_aggregations_visible','0,10,11,12,2,4,6,8,13'),(71,'grade_aggregateonlygraded','1'),(72,'grade_aggregateonlygraded_flag','2'),(73,'grade_aggregateoutcomes','0'),(74,'grade_aggregateoutcomes_flag','2'),(75,'grade_aggregatesubcats','0'),(76,'grade_aggregatesubcats_flag','2'),(77,'grade_keephigh','0'),(78,'grade_keephigh_flag','3'),(79,'grade_droplow','0'),(80,'grade_droplow_flag','2'),(81,'grade_displaytype','1'),(82,'grade_decimalpoints','2'),(83,'grade_item_advanced','iteminfo,idnumber,gradepass,plusfactor,multfactor,display,decimals,hiddenuntil,locktime'),(84,'grade_report_studentsperpage','100'),(85,'grade_report_showonlyactiveenrol','1'),(86,'grade_report_quickgrading','1'),(87,'grade_report_showquickfeedback','0'),(88,'grade_report_fixedstudents','0'),(89,'grade_report_meanselection','1'),(90,'grade_report_enableajax','0'),(91,'grade_report_showcalculations','0'),(92,'grade_report_showeyecons','0'),(93,'grade_report_showaverages','1'),(94,'grade_report_showlocks','0'),(95,'grade_report_showranges','0'),(96,'grade_report_showanalysisicon','1'),(97,'grade_report_showuserimage','1'),(98,'grade_report_showactivityicons','1'),(99,'grade_report_shownumberofgrades','0'),(100,'grade_report_averagesdisplaytype','inherit'),(101,'grade_report_rangesdisplaytype','inherit'),(102,'grade_report_averagesdecimalpoints','inherit'),(103,'grade_report_rangesdecimalpoints','inherit'),(104,'grade_report_overview_showrank','0'),(105,'grade_report_overview_showtotalsifcontainhidden','0'),(106,'grade_report_user_showrank','0'),(107,'grade_report_user_showpercentage','1'),(108,'grade_report_user_showgrade','1'),(109,'grade_report_user_showfeedback','1'),(110,'grade_report_user_showrange','1'),(111,'grade_report_user_showweight','0'),(112,'grade_report_user_showaverage','0'),(113,'grade_report_user_showlettergrade','0'),(114,'grade_report_user_rangedecimals','0'),(115,'grade_report_user_showhiddenitems','1'),(116,'grade_report_user_showtotalsifcontainhidden','0'),(117,'badges_defaultissuername',''),(118,'badges_defaultissuercontact',''),(119,'badges_badgesalt','badges1390419884'),(120,'badges_allowexternalbackpack','1'),(121,'badges_allowcoursebadges','1'),(122,'timezone','99'),(123,'forcetimezone','99'),(124,'country','0'),(125,'defaultcity',''),(126,'geoipfile','/home/daniel/moodledata/geoip/GeoLiteCity.dat'),(127,'googlemapkey3',''),(128,'allcountrycodes',''),(129,'autolang','1'),(130,'lang','en'),(131,'langmenu','1'),(132,'langlist',''),(133,'langrev','1390505190'),(134,'langcache','1'),(135,'langstringcache','1'),(136,'locale',''),(137,'latinexcelexport','0'),(139,'authpreventaccountcreation','0'),(140,'loginpageautofocus','0'),(141,'guestloginbutton','1'),(142,'alternateloginurl',''),(143,'forgottenpasswordurl',''),(144,'auth_instructions',''),(145,'allowemailaddresses',''),(146,'denyemailaddresses',''),(147,'verifychangedemail','1'),(148,'recaptchapublickey',''),(149,'recaptchaprivatekey',''),(150,'sitedefaultlicense','allrightsreserved'),(151,'cachetext','60'),(152,'filteruploadedfiles','0'),(153,'filtermatchoneperpage','0'),(154,'filtermatchonepertext','0'),(155,'portfolio_moderate_filesize_threshold','1048576'),(156,'portfolio_high_filesize_threshold','5242880'),(157,'portfolio_moderate_db_threshold','20'),(158,'portfolio_high_db_threshold','50'),(159,'repositorycacheexpire','120'),(160,'repositoryallowexternallinks','1'),(161,'legacyfilesinnewcourses','0'),(162,'legacyfilesaddallowed','1'),(163,'mobilecssurl',''),(164,'enablewsdocumentation','0'),(165,'allowbeforeblock','0'),(166,'allowedip',''),(167,'blockedip',''),(168,'protectusernames','1'),(169,'forcelogin','0'),(170,'forceloginforprofiles','1'),(171,'forceloginforprofileimage','0'),(172,'opentogoogle','0'),(173,'maxbytes','0'),(174,'userquota','104857600'),(175,'allowobjectembed','0'),(176,'enabletrusttext','0'),(177,'maxeditingtime','1800'),(178,'extendedusernamechars','0'),(179,'sitepolicy',''),(180,'sitepolicyguest',''),(181,'keeptagnamecase','1'),(182,'profilesforenrolledusersonly','1'),(183,'cronclionly','0'),(184,'cronremotepassword',''),(185,'lockoutthreshold','0'),(186,'lockoutwindow','1800'),(187,'lockoutduration','1800'),(188,'passwordpolicy','1'),(189,'minpasswordlength','8'),(190,'minpassworddigits','1'),(191,'minpasswordlower','1'),(192,'minpasswordupper','1'),(193,'minpasswordnonalphanum','1'),(194,'maxconsecutiveidentchars','0'),(195,'pwresettime','1800'),(196,'groupenrolmentkeypolicy','1'),(197,'disableuserimages','0'),(198,'emailchangeconfirmation','1'),(199,'rememberusername','2'),(200,'strictformsrequired','0'),(201,'loginhttps','0'),(202,'cookiesecure','0'),(203,'cookiehttponly','0'),(204,'allowframembedding','0'),(205,'loginpasswordautocomplete','0'),(206,'displayloginfailures',''),(207,'notifyloginfailures',''),(208,'notifyloginthreshold','10'),(209,'runclamonupload','0'),(210,'pathtoclam',''),(211,'quarantinedir',''),(212,'clamfailureonupload','donothing'),(213,'themelist',''),(214,'themedesignermode','0'),(215,'allowuserthemes','0'),(216,'allowcoursethemes','0'),(217,'allowcategorythemes','0'),(218,'allowthemechangeonurl','0'),(219,'allowuserblockhiding','1'),(220,'allowblockstodock','1'),(221,'custommenuitems',''),(222,'enabledevicedetection','1'),(223,'devicedetectregex','[]'),(224,'calendar_adminseesall','0'),(225,'calendar_site_timeformat','0'),(226,'calendar_startwday','0'),(227,'calendar_weekend','65'),(228,'calendar_lookahead','21'),(229,'calendar_maxevents','10'),(230,'enablecalendarexport','1'),(231,'calendar_customexport','1'),(232,'calendar_exportlookahead','365'),(233,'calendar_exportlookback','5'),(234,'calendar_exportsalt','02Z4qihRq5Zq5JumMSEziKpVISIFFsQ8KUg7xf4JW7YG4uL1RQ07yCAYmE6G'),(235,'calendar_showicalsource','1'),(236,'useblogassociations','1'),(237,'bloglevel','4'),(238,'useexternalblogs','1'),(239,'externalblogcrontime','86400'),(240,'maxexternalblogsperuser','1'),(241,'blogusecomments','1'),(242,'blogshowcommentscount','1'),(243,'defaulthomepage','0'),(244,'allowguestmymoodle','1'),(245,'navshowfullcoursenames','0'),(246,'navshowcategories','1'),(247,'navshowmycoursecategories','0'),(248,'navshowallcourses','0'),(249,'navsortmycoursessort','sortorder'),(250,'navcourselimit','20'),(251,'usesitenameforsitepages','0'),(252,'linkadmincategories','0'),(253,'navshowfrontpagemods','1'),(254,'navadduserpostslinks','1'),(255,'formatstringstriptags','1'),(256,'emoticons','[{\"text\":\":-)\",\"imagename\":\"s\\/smiley\",\"imagecomponent\":\"core\",\"altidentifier\":\"smiley\",\"altcomponent\":\"core_pix\"},{\"text\":\":)\",\"imagename\":\"s\\/smiley\",\"imagecomponent\":\"core\",\"altidentifier\":\"smiley\",\"altcomponent\":\"core_pix\"},{\"text\":\":-D\",\"imagename\":\"s\\/biggrin\",\"imagecomponent\":\"core\",\"altidentifier\":\"biggrin\",\"altcomponent\":\"core_pix\"},{\"text\":\";-)\",\"imagename\":\"s\\/wink\",\"imagecomponent\":\"core\",\"altidentifier\":\"wink\",\"altcomponent\":\"core_pix\"},{\"text\":\":-\\/\",\"imagename\":\"s\\/mixed\",\"imagecomponent\":\"core\",\"altidentifier\":\"mixed\",\"altcomponent\":\"core_pix\"},{\"text\":\"V-.\",\"imagename\":\"s\\/thoughtful\",\"imagecomponent\":\"core\",\"altidentifier\":\"thoughtful\",\"altcomponent\":\"core_pix\"},{\"text\":\":-P\",\"imagename\":\"s\\/tongueout\",\"imagecomponent\":\"core\",\"altidentifier\":\"tongueout\",\"altcomponent\":\"core_pix\"},{\"text\":\":-p\",\"imagename\":\"s\\/tongueout\",\"imagecomponent\":\"core\",\"altidentifier\":\"tongueout\",\"altcomponent\":\"core_pix\"},{\"text\":\"B-)\",\"imagename\":\"s\\/cool\",\"imagecomponent\":\"core\",\"altidentifier\":\"cool\",\"altcomponent\":\"core_pix\"},{\"text\":\"^-)\",\"imagename\":\"s\\/approve\",\"imagecomponent\":\"core\",\"altidentifier\":\"approve\",\"altcomponent\":\"core_pix\"},{\"text\":\"8-)\",\"imagename\":\"s\\/wideeyes\",\"imagecomponent\":\"core\",\"altidentifier\":\"wideeyes\",\"altcomponent\":\"core_pix\"},{\"text\":\":o)\",\"imagename\":\"s\\/clown\",\"imagecomponent\":\"core\",\"altidentifier\":\"clown\",\"altcomponent\":\"core_pix\"},{\"text\":\":-(\",\"imagename\":\"s\\/sad\",\"imagecomponent\":\"core\",\"altidentifier\":\"sad\",\"altcomponent\":\"core_pix\"},{\"text\":\":(\",\"imagename\":\"s\\/sad\",\"imagecomponent\":\"core\",\"altidentifier\":\"sad\",\"altcomponent\":\"core_pix\"},{\"text\":\"8-.\",\"imagename\":\"s\\/shy\",\"imagecomponent\":\"core\",\"altidentifier\":\"shy\",\"altcomponent\":\"core_pix\"},{\"text\":\":-I\",\"imagename\":\"s\\/blush\",\"imagecomponent\":\"core\",\"altidentifier\":\"blush\",\"altcomponent\":\"core_pix\"},{\"text\":\":-X\",\"imagename\":\"s\\/kiss\",\"imagecomponent\":\"core\",\"altidentifier\":\"kiss\",\"altcomponent\":\"core_pix\"},{\"text\":\"8-o\",\"imagename\":\"s\\/surprise\",\"imagecomponent\":\"core\",\"altidentifier\":\"surprise\",\"altcomponent\":\"core_pix\"},{\"text\":\"P-|\",\"imagename\":\"s\\/blackeye\",\"imagecomponent\":\"core\",\"altidentifier\":\"blackeye\",\"altcomponent\":\"core_pix\"},{\"text\":\"8-[\",\"imagename\":\"s\\/angry\",\"imagecomponent\":\"core\",\"altidentifier\":\"angry\",\"altcomponent\":\"core_pix\"},{\"text\":\"(grr)\",\"imagename\":\"s\\/angry\",\"imagecomponent\":\"core\",\"altidentifier\":\"angry\",\"altcomponent\":\"core_pix\"},{\"text\":\"xx-P\",\"imagename\":\"s\\/dead\",\"imagecomponent\":\"core\",\"altidentifier\":\"dead\",\"altcomponent\":\"core_pix\"},{\"text\":\"|-.\",\"imagename\":\"s\\/sleepy\",\"imagecomponent\":\"core\",\"altidentifier\":\"sleepy\",\"altcomponent\":\"core_pix\"},{\"text\":\"}-]\",\"imagename\":\"s\\/evil\",\"imagecomponent\":\"core\",\"altidentifier\":\"evil\",\"altcomponent\":\"core_pix\"},{\"text\":\"(h)\",\"imagename\":\"s\\/heart\",\"imagecomponent\":\"core\",\"altidentifier\":\"heart\",\"altcomponent\":\"core_pix\"},{\"text\":\"(heart)\",\"imagename\":\"s\\/heart\",\"imagecomponent\":\"core\",\"altidentifier\":\"heart\",\"altcomponent\":\"core_pix\"},{\"text\":\"(y)\",\"imagename\":\"s\\/yes\",\"imagecomponent\":\"core\",\"altidentifier\":\"yes\",\"altcomponent\":\"core\"},{\"text\":\"(n)\",\"imagename\":\"s\\/no\",\"imagecomponent\":\"core\",\"altidentifier\":\"no\",\"altcomponent\":\"core\"},{\"text\":\"(martin)\",\"imagename\":\"s\\/martin\",\"imagecomponent\":\"core\",\"altidentifier\":\"martin\",\"altcomponent\":\"core_pix\"},{\"text\":\"( )\",\"imagename\":\"s\\/egg\",\"imagecomponent\":\"core\",\"altidentifier\":\"egg\",\"altcomponent\":\"core_pix\"}]'),(257,'core_media_enable_youtube','1'),(258,'core_media_enable_vimeo','0'),(259,'core_media_enable_mp3','1'),(260,'core_media_enable_flv','1'),(261,'core_media_enable_swf','1'),(262,'core_media_enable_html5audio','1'),(263,'core_media_enable_html5video','1'),(264,'core_media_enable_qt','1'),(265,'core_media_enable_wmp','1'),(266,'core_media_enable_rm','1'),(267,'docroot','http://docs.moodle.org'),(268,'doctonewwindow','0'),(269,'courselistshortnames','0'),(270,'coursesperpage','20'),(271,'courseswithsummarieslimit','10'),(272,'courseoverviewfileslimit','1'),(273,'courseoverviewfilesext','.jpg,.gif,.png'),(274,'enableajax','1'),(275,'useexternalyui','0'),(276,'yuicomboloading','1'),(277,'cachejs','1'),(278,'modchooserdefault','1'),(279,'modeditingmenu','1'),(280,'blockeditingmenu','1'),(281,'additionalhtmlhead',''),(282,'additionalhtmltopofbody',''),(283,'additionalhtmlfooter',''),(284,'pathtodu',''),(285,'aspellpath',''),(286,'pathtodot',''),(287,'supportpage',''),(288,'dbsessions','0'),(289,'sessioncookie',''),(290,'sessioncookiepath',''),(291,'sessioncookiedomain',''),(292,'statsfirstrun','none'),(293,'statsmaxruntime','0'),(294,'statsruntimedays','31'),(295,'statsruntimestarthour','0'),(296,'statsruntimestartminute','0'),(297,'statsuserthreshold','0'),(298,'slasharguments','1'),(299,'getremoteaddrconf','0'),(300,'proxyhost',''),(301,'proxyport','0'),(302,'proxytype','HTTP'),(303,'proxyuser',''),(304,'proxypassword',''),(305,'proxybypass','localhost, 127.0.0.1'),(306,'maintenance_enabled','0'),(307,'maintenance_message',''),(308,'deleteunconfirmed','168'),(309,'deleteincompleteusers','0'),(310,'logguests','1'),(311,'loglifetime','0'),(312,'disablegradehistory','0'),(313,'gradehistorylifetime','0'),(314,'extramemorylimit','512M'),(315,'curlcache','120'),(316,'curltimeoutkbitrate','56'),(317,'updateautocheck','1'),(318,'updateautodeploy','0'),(319,'updateminmaturity','200'),(320,'updatenotifybuilds','0'),(321,'enablesafebrowserintegration','0'),(322,'enablegroupmembersonly','0'),(323,'dndallowtextandlinks','0'),(324,'enablecssoptimiser','0'),(325,'enabletgzbackups','0'),(326,'debug','0'),(327,'debugdisplay','0'),(328,'debugsmtp','0'),(329,'perfdebug','7'),(330,'debugstringids','0'),(331,'debugvalidators','0'),(332,'debugpageinfo','0'),(333,'release','2.6.1+ (Build: 20140117)'),(334,'branch','26'),(335,'localcachedirpurged','1390505190'),(336,'allversionshash','940f58f26b588e74b205f37790ca42052e88ee7b'),(338,'notloggedinroleid','6'),(339,'guestroleid','6'),(340,'defaultuserroleid','7'),(341,'creatornewroleid','3'),(342,'restorernewroleid','3'),(343,'gradebookroles','5'),(344,'assignment_maxbytes','1048576'),(345,'assignment_itemstocount','1'),(346,'assignment_showrecentsubmissions','1'),(347,'chat_method','ajax'),(348,'chat_refresh_userlist','10'),(349,'chat_old_ping','35'),(350,'chat_refresh_room','5'),(351,'chat_normal_updatemode','jsupdate'),(352,'chat_serverhost','127.0.0.1'),(353,'chat_serverip','127.0.0.1'),(354,'chat_serverport','9111'),(355,'chat_servermax','100'),(356,'data_enablerssfeeds','0'),(357,'feedback_allowfullanonymous','0'),(358,'forum_displaymode','3'),(359,'forum_replytouser','1'),(360,'forum_shortpost','300'),(361,'forum_longpost','600'),(362,'forum_manydiscussions','100'),(363,'forum_maxbytes','512000'),(364,'forum_maxattachments','9'),(365,'forum_trackingtype','1'),(366,'forum_trackreadposts','1'),(367,'forum_allowforcedreadtracking','0'),(368,'forum_oldpostdays','14'),(369,'forum_usermarksread','0'),(370,'forum_cleanreadtime','2'),(371,'digestmailtime','17'),(372,'forum_enablerssfeeds','0'),(373,'forum_enabletimedposts','0'),(374,'glossary_entbypage','10'),(375,'glossary_dupentries','0'),(376,'glossary_allowcomments','0'),(377,'glossary_linkbydefault','1'),(378,'glossary_defaultapproval','1'),(379,'glossary_enablerssfeeds','0'),(380,'glossary_linkentries','0'),(381,'glossary_casesensitive','0'),(382,'glossary_fullmatch','0'),(383,'lesson_slideshowwidth','640'),(384,'lesson_slideshowheight','480'),(385,'lesson_slideshowbgcolor','#FFFFFF'),(386,'lesson_mediawidth','640'),(387,'lesson_mediaheight','480'),(388,'lesson_mediaclose','0'),(389,'lesson_maxhighscores','10'),(390,'lesson_maxanswers','4'),(391,'lesson_defaultnextpage','0'),(392,'block_course_list_adminview','all'),(393,'block_course_list_hideallcourseslink','0'),(394,'block_html_allowcssclasses','0'),(395,'block_online_users_timetosee','5'),(396,'block_rss_client_num_entries','5'),(397,'block_rss_client_timeout','30'),(398,'block_tags_showcoursetags','0'),(399,'smtphosts',''),(400,'smtpsecure',''),(401,'smtpuser',''),(402,'smtppass',''),(403,'smtpmaxbulk','1'),(404,'noreplyaddress','noreply@127.0.0.1'),(405,'sitemailcharset','0'),(406,'allowusermailcharset','0'),(407,'allowattachments','1'),(408,'mailnewline','LF'),(409,'jabberhost',''),(410,'jabberserver',''),(411,'jabberusername',''),(412,'jabberpassword',''),(413,'jabberport','5222'),(414,'filter_censor_badwords',''),(415,'filter_multilang_force_old','0'),(416,'filter_tex_latexpreamble','\\usepackage[latin1]{inputenc}\n\\usepackage{amsmath}\n\\usepackage{amsfonts}\n\\RequirePackage{amsmath,amssymb,latexsym}\n'),(417,'filter_tex_latexbackground','#FFFFFF'),(418,'filter_tex_density','120'),(419,'filter_tex_pathlatex','/usr/bin/latex'),(420,'filter_tex_pathdvips','/usr/bin/dvips'),(421,'filter_tex_pathconvert','/usr/bin/convert'),(422,'filter_tex_convertformat','gif'),(423,'profileroles','5,4,3'),(424,'coursecontact','3'),(425,'frontpage','6'),(426,'frontpageloggedin','6'),(427,'maxcategorydepth','2'),(428,'frontpagecourselimit','200'),(429,'commentsperpage','15'),(430,'defaultfrontpageroleid','8'),(431,'supportname','Admin User'),(432,'supportemail','de.kosinski@gmail.com'),(433,'registerauth',''),(434,'digestmailtimelast','0'),(435,'forum_lastreadclean','1390473610'),(436,'scorm_updatetimelast','1390473611'),(437,'fileslastcleanup','1390473616');
/*!40000 ALTER TABLE `mdl_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_config_log`
--

DROP TABLE IF EXISTS `mdl_config_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_config_log` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  `plugin` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` longtext,
  `oldvalue` longtext,
  PRIMARY KEY (`id`),
  KEY `mdl_conflog_tim_ix` (`timemodified`),
  KEY `mdl_conflog_use_ix` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=877 DEFAULT CHARSET=utf8 COMMENT='Changes done in server configuration through admin UI';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_config_log`
--

LOCK TABLES `mdl_config_log` WRITE;
/*!40000 ALTER TABLE `mdl_config_log` DISABLE KEYS */;
INSERT INTO `mdl_config_log` VALUES (1,0,1390420025,NULL,'enableoutcomes','0',NULL),(2,0,1390420026,NULL,'usecomments','1',NULL),(3,0,1390420027,NULL,'usetags','1',NULL),(4,0,1390420027,NULL,'enablenotes','1',NULL),(5,0,1390420027,NULL,'enableportfolios','0',NULL),(6,0,1390420028,NULL,'enablewebservices','0',NULL),(7,0,1390420028,NULL,'messaging','1',NULL),(8,0,1390420028,NULL,'messaginghidereadnotifications','0',NULL),(9,0,1390420028,NULL,'messagingdeletereadnotificationsdelay','604800',NULL),(10,0,1390420029,NULL,'messagingallowemailoverride','0',NULL),(11,0,1390420029,NULL,'enablestats','0',NULL),(12,0,1390420029,NULL,'enablerssfeeds','0',NULL),(13,0,1390420029,NULL,'enableblogs','1',NULL),(14,0,1390420030,NULL,'enablecompletion','0',NULL),(15,0,1390420030,NULL,'completiondefault','1',NULL),(16,0,1390420031,NULL,'enableavailability','0',NULL),(17,0,1390420031,NULL,'enableplagiarism','0',NULL),(18,0,1390420031,NULL,'enablebadges','1',NULL),(19,0,1390420031,NULL,'autologinguests','0',NULL),(20,0,1390420032,NULL,'hiddenuserfields','',NULL),(21,0,1390420032,NULL,'showuseridentity','email',NULL),(22,0,1390420032,NULL,'fullnamedisplay','language',NULL),(23,0,1390420033,NULL,'maxusersperpage','100',NULL),(24,0,1390420033,NULL,'enablegravatar','0',NULL),(25,0,1390420034,NULL,'gravatardefaulturl','mm',NULL),(26,0,1390420034,'moodlecourse','visible','1',NULL),(27,0,1390420034,'moodlecourse','format','weeks',NULL),(28,0,1390420035,'moodlecourse','maxsections','52',NULL),(29,0,1390420035,'moodlecourse','numsections','10',NULL),(30,0,1390420036,'moodlecourse','hiddensections','0',NULL),(31,0,1390420036,'moodlecourse','coursedisplay','0',NULL),(32,0,1390420037,'moodlecourse','lang','',NULL),(33,0,1390420038,'moodlecourse','newsitems','5',NULL),(34,0,1390420039,'moodlecourse','showgrades','1',NULL),(35,0,1390420039,'moodlecourse','showreports','0',NULL),(36,0,1390420039,'moodlecourse','maxbytes','0',NULL),(37,0,1390420039,'moodlecourse','enablecompletion','0',NULL),(38,0,1390420040,'moodlecourse','groupmode','0',NULL),(39,0,1390420040,'moodlecourse','groupmodeforce','0',NULL),(40,0,1390420040,NULL,'enablecourserequests','0',NULL),(41,0,1390420040,NULL,'defaultrequestcategory','1',NULL),(42,0,1390420041,NULL,'requestcategoryselection','0',NULL),(43,0,1390420041,NULL,'courserequestnotify','',NULL),(44,0,1390420041,'backup','loglifetime','30',NULL),(45,0,1390420042,'backup','backup_general_users','1',NULL),(46,0,1390420042,'backup','backup_general_users_locked','',NULL),(47,0,1390420042,'backup','backup_general_anonymize','0',NULL),(48,0,1390420042,'backup','backup_general_anonymize_locked','',NULL),(49,0,1390420042,'backup','backup_general_role_assignments','1',NULL),(50,0,1390420043,'backup','backup_general_role_assignments_locked','',NULL),(51,0,1390420044,'backup','backup_general_activities','1',NULL),(52,0,1390420044,'backup','backup_general_activities_locked','',NULL),(53,0,1390420044,'backup','backup_general_blocks','1',NULL),(54,0,1390420045,'backup','backup_general_blocks_locked','',NULL),(55,0,1390420045,'backup','backup_general_filters','1',NULL),(56,0,1390420046,'backup','backup_general_filters_locked','',NULL),(57,0,1390420046,'backup','backup_general_comments','1',NULL),(58,0,1390420046,'backup','backup_general_comments_locked','',NULL),(59,0,1390420046,'backup','backup_general_badges','1',NULL),(60,0,1390420047,'backup','backup_general_badges_locked','',NULL),(61,0,1390420047,'backup','backup_general_userscompletion','1',NULL),(62,0,1390420047,'backup','backup_general_userscompletion_locked','',NULL),(63,0,1390420047,'backup','backup_general_logs','0',NULL),(64,0,1390420048,'backup','backup_general_logs_locked','',NULL),(65,0,1390420048,'backup','backup_general_histories','0',NULL),(66,0,1390420048,'backup','backup_general_histories_locked','',NULL),(67,0,1390420049,'backup','backup_general_questionbank','1',NULL),(68,0,1390420050,'backup','backup_general_questionbank_locked','',NULL),(69,0,1390420050,'backup','import_general_maxresults','10',NULL),(70,0,1390420051,'backup','backup_auto_active','0',NULL),(71,0,1390420051,'backup','backup_auto_weekdays','0000000',NULL),(72,0,1390420051,'backup','backup_auto_hour','0',NULL),(73,0,1390420051,'backup','backup_auto_minute','0',NULL),(74,0,1390420051,'backup','backup_auto_storage','0',NULL),(75,0,1390420052,'backup','backup_auto_destination','',NULL),(76,0,1390420052,'backup','backup_auto_keep','1',NULL),(77,0,1390420052,'backup','backup_shortname','0',NULL),(78,0,1390420052,'backup','backup_auto_skip_hidden','1',NULL),(79,0,1390420052,'backup','backup_auto_skip_modif_days','30',NULL),(80,0,1390420052,'backup','backup_auto_skip_modif_prev','0',NULL),(81,0,1390420052,'backup','backup_auto_users','1',NULL),(82,0,1390420053,'backup','backup_auto_role_assignments','1',NULL),(83,0,1390420053,'backup','backup_auto_activities','1',NULL),(84,0,1390420054,'backup','backup_auto_blocks','1',NULL),(85,0,1390420054,'backup','backup_auto_filters','1',NULL),(86,0,1390420054,'backup','backup_auto_comments','1',NULL),(87,0,1390420054,'backup','backup_auto_badges','1',NULL),(88,0,1390420054,'backup','backup_auto_userscompletion','1',NULL),(89,0,1390420054,'backup','backup_auto_logs','0',NULL),(90,0,1390420054,'backup','backup_auto_histories','0',NULL),(91,0,1390420055,'backup','backup_auto_questionbank','1',NULL),(92,0,1390420055,NULL,'grade_profilereport','user',NULL),(93,0,1390420055,NULL,'grade_aggregationposition','1',NULL),(94,0,1390420056,NULL,'grade_includescalesinaggregation','1',NULL),(95,0,1390420056,NULL,'grade_hiddenasdate','0',NULL),(96,0,1390420056,NULL,'gradepublishing','0',NULL),(97,0,1390420056,NULL,'grade_export_displaytype','1',NULL),(98,0,1390420057,NULL,'grade_export_decimalpoints','2',NULL),(99,0,1390420057,NULL,'grade_navmethod','0',NULL),(100,0,1390420057,NULL,'grade_export_userprofilefields','firstname,lastname,idnumber,institution,department,email',NULL),(101,0,1390420057,NULL,'grade_export_customprofilefields','',NULL),(102,0,1390420057,NULL,'recovergradesdefault','0',NULL),(103,0,1390420057,NULL,'gradeexport','',NULL),(104,0,1390420058,NULL,'unlimitedgrades','0',NULL),(105,0,1390420058,NULL,'grade_hideforcedsettings','1',NULL),(106,0,1390420058,NULL,'grade_aggregation','11',NULL),(107,0,1390420059,NULL,'grade_aggregation_flag','0',NULL),(108,0,1390420059,NULL,'grade_aggregations_visible','0,10,11,12,2,4,6,8,13',NULL),(109,0,1390420059,NULL,'grade_aggregateonlygraded','1',NULL),(110,0,1390420059,NULL,'grade_aggregateonlygraded_flag','2',NULL),(111,0,1390420059,NULL,'grade_aggregateoutcomes','0',NULL),(112,0,1390420059,NULL,'grade_aggregateoutcomes_flag','2',NULL),(113,0,1390420059,NULL,'grade_aggregatesubcats','0',NULL),(114,0,1390420060,NULL,'grade_aggregatesubcats_flag','2',NULL),(115,0,1390420060,NULL,'grade_keephigh','0',NULL),(116,0,1390420060,NULL,'grade_keephigh_flag','3',NULL),(117,0,1390420060,NULL,'grade_droplow','0',NULL),(118,0,1390420060,NULL,'grade_droplow_flag','2',NULL),(119,0,1390420061,NULL,'grade_displaytype','1',NULL),(120,0,1390420061,NULL,'grade_decimalpoints','2',NULL),(121,0,1390420062,NULL,'grade_item_advanced','iteminfo,idnumber,gradepass,plusfactor,multfactor,display,decimals,hiddenuntil,locktime',NULL),(122,0,1390420062,NULL,'grade_report_studentsperpage','100',NULL),(123,0,1390420062,NULL,'grade_report_showonlyactiveenrol','1',NULL),(124,0,1390420063,NULL,'grade_report_quickgrading','1',NULL),(125,0,1390420063,NULL,'grade_report_showquickfeedback','0',NULL),(126,0,1390420063,NULL,'grade_report_fixedstudents','0',NULL),(127,0,1390420063,NULL,'grade_report_meanselection','1',NULL),(128,0,1390420064,NULL,'grade_report_enableajax','0',NULL),(129,0,1390420064,NULL,'grade_report_showcalculations','0',NULL),(130,0,1390420065,NULL,'grade_report_showeyecons','0',NULL),(131,0,1390420065,NULL,'grade_report_showaverages','1',NULL),(132,0,1390420065,NULL,'grade_report_showlocks','0',NULL),(133,0,1390420065,NULL,'grade_report_showranges','0',NULL),(134,0,1390420066,NULL,'grade_report_showanalysisicon','1',NULL),(135,0,1390420066,NULL,'grade_report_showuserimage','1',NULL),(136,0,1390420066,NULL,'grade_report_showactivityicons','1',NULL),(137,0,1390420067,NULL,'grade_report_shownumberofgrades','0',NULL),(138,0,1390420067,NULL,'grade_report_averagesdisplaytype','inherit',NULL),(139,0,1390420068,NULL,'grade_report_rangesdisplaytype','inherit',NULL),(140,0,1390420068,NULL,'grade_report_averagesdecimalpoints','inherit',NULL),(141,0,1390420068,NULL,'grade_report_rangesdecimalpoints','inherit',NULL),(142,0,1390420069,NULL,'grade_report_overview_showrank','0',NULL),(143,0,1390420069,NULL,'grade_report_overview_showtotalsifcontainhidden','0',NULL),(144,0,1390420069,NULL,'grade_report_user_showrank','0',NULL),(145,0,1390420069,NULL,'grade_report_user_showpercentage','1',NULL),(146,0,1390420070,NULL,'grade_report_user_showgrade','1',NULL),(147,0,1390420070,NULL,'grade_report_user_showfeedback','1',NULL),(148,0,1390420070,NULL,'grade_report_user_showrange','1',NULL),(149,0,1390420070,NULL,'grade_report_user_showweight','0',NULL),(150,0,1390420070,NULL,'grade_report_user_showaverage','0',NULL),(151,0,1390420070,NULL,'grade_report_user_showlettergrade','0',NULL),(152,0,1390420071,NULL,'grade_report_user_rangedecimals','0',NULL),(153,0,1390420071,NULL,'grade_report_user_showhiddenitems','1',NULL),(154,0,1390420071,NULL,'grade_report_user_showtotalsifcontainhidden','0',NULL),(155,0,1390420072,NULL,'badges_defaultissuername','',NULL),(156,0,1390420073,NULL,'badges_defaultissuercontact','',NULL),(157,0,1390420073,NULL,'badges_badgesalt','badges1390419884',NULL),(158,0,1390420074,NULL,'badges_allowexternalbackpack','1',NULL),(159,0,1390420074,NULL,'badges_allowcoursebadges','1',NULL),(160,0,1390420074,NULL,'timezone','99',NULL),(161,0,1390420074,NULL,'forcetimezone','99',NULL),(162,0,1390420075,NULL,'country','0',NULL),(163,0,1390420075,NULL,'defaultcity','',NULL),(164,0,1390420075,NULL,'geoipfile','/home/daniel/moodledata/geoip/GeoLiteCity.dat',NULL),(165,0,1390420076,NULL,'googlemapkey3','',NULL),(166,0,1390420076,NULL,'allcountrycodes','',NULL),(167,0,1390420076,NULL,'autolang','1',NULL),(168,0,1390420076,NULL,'lang','en',NULL),(169,0,1390420077,NULL,'langmenu','1',NULL),(170,0,1390420077,NULL,'langlist','',NULL),(171,0,1390420078,NULL,'langcache','1',NULL),(172,0,1390420079,NULL,'langstringcache','1',NULL),(173,0,1390420079,NULL,'locale','',NULL),(174,0,1390420080,NULL,'latinexcelexport','0',NULL),(175,0,1390420080,NULL,'registerauth','',NULL),(176,0,1390420080,NULL,'authpreventaccountcreation','0',NULL),(177,0,1390420081,NULL,'loginpageautofocus','0',NULL),(178,0,1390420081,NULL,'guestloginbutton','1',NULL),(179,0,1390420082,NULL,'alternateloginurl','',NULL),(180,0,1390420083,NULL,'forgottenpasswordurl','',NULL),(181,0,1390420083,NULL,'auth_instructions','',NULL),(182,0,1390420083,NULL,'allowemailaddresses','',NULL),(183,0,1390420083,NULL,'denyemailaddresses','',NULL),(184,0,1390420084,NULL,'verifychangedemail','1',NULL),(185,0,1390420085,NULL,'recaptchapublickey','',NULL),(186,0,1390420085,NULL,'recaptchaprivatekey','',NULL),(187,0,1390420086,NULL,'sitedefaultlicense','allrightsreserved',NULL),(188,0,1390420086,NULL,'cachetext','60',NULL),(189,0,1390420086,NULL,'filteruploadedfiles','0',NULL),(190,0,1390420086,NULL,'filtermatchoneperpage','0',NULL),(191,0,1390420087,NULL,'filtermatchonepertext','0',NULL),(192,0,1390420088,NULL,'portfolio_moderate_filesize_threshold','1048576',NULL),(193,0,1390420088,NULL,'portfolio_high_filesize_threshold','5242880',NULL),(194,0,1390420088,NULL,'portfolio_moderate_db_threshold','20',NULL),(195,0,1390420088,NULL,'portfolio_high_db_threshold','50',NULL),(196,0,1390420089,NULL,'repositorycacheexpire','120',NULL),(197,0,1390420089,NULL,'repositoryallowexternallinks','1',NULL),(198,0,1390420089,NULL,'legacyfilesinnewcourses','0',NULL),(199,0,1390420090,NULL,'legacyfilesaddallowed','1',NULL),(200,0,1390420090,NULL,'mobilecssurl','',NULL),(201,0,1390420091,NULL,'enablewsdocumentation','0',NULL),(202,0,1390420091,'question_preview','behaviour','deferredfeedback',NULL),(203,0,1390420092,'question_preview','correctness','1',NULL),(204,0,1390420092,'question_preview','marks','1',NULL),(205,0,1390420092,'question_preview','markdp','2',NULL),(206,0,1390420092,'question_preview','feedback','1',NULL),(207,0,1390420093,'question_preview','generalfeedback','1',NULL),(208,0,1390420093,'question_preview','rightanswer','1',NULL),(209,0,1390420094,'question_preview','history','0',NULL),(210,0,1390420094,'cachestore_memcache','testservers','',NULL),(211,0,1390420095,'cachestore_memcached','testservers','',NULL),(212,0,1390420096,'cachestore_mongodb','testserver','',NULL),(213,0,1390420096,NULL,'allowbeforeblock','0',NULL),(214,0,1390420096,NULL,'allowedip','',NULL),(215,0,1390420096,NULL,'blockedip','',NULL),(216,0,1390420096,NULL,'protectusernames','1',NULL),(217,0,1390420097,NULL,'forcelogin','0',NULL),(218,0,1390420097,NULL,'forceloginforprofiles','1',NULL),(219,0,1390420098,NULL,'forceloginforprofileimage','0',NULL),(220,0,1390420098,NULL,'opentogoogle','0',NULL),(221,0,1390420098,NULL,'maxbytes','0',NULL),(222,0,1390420098,NULL,'userquota','104857600',NULL),(223,0,1390420099,NULL,'allowobjectembed','0',NULL),(224,0,1390420099,NULL,'enabletrusttext','0',NULL),(225,0,1390420099,NULL,'maxeditingtime','1800',NULL),(226,0,1390420099,NULL,'extendedusernamechars','0',NULL),(227,0,1390420100,NULL,'sitepolicy','',NULL),(228,0,1390420100,NULL,'sitepolicyguest','',NULL),(229,0,1390420100,NULL,'keeptagnamecase','1',NULL),(230,0,1390420101,NULL,'profilesforenrolledusersonly','1',NULL),(231,0,1390420101,NULL,'cronclionly','0',NULL),(232,0,1390420101,NULL,'cronremotepassword','',NULL),(233,0,1390420101,NULL,'lockoutthreshold','0',NULL),(234,0,1390420101,NULL,'lockoutwindow','1800',NULL),(235,0,1390420102,NULL,'lockoutduration','1800',NULL),(236,0,1390420102,NULL,'passwordpolicy','1',NULL),(237,0,1390420102,NULL,'minpasswordlength','8',NULL),(238,0,1390420102,NULL,'minpassworddigits','1',NULL),(239,0,1390420102,NULL,'minpasswordlower','1',NULL),(240,0,1390420102,NULL,'minpasswordupper','1',NULL),(241,0,1390420103,NULL,'minpasswordnonalphanum','1',NULL),(242,0,1390420103,NULL,'maxconsecutiveidentchars','0',NULL),(243,0,1390420103,NULL,'pwresettime','1800',NULL),(244,0,1390420104,NULL,'groupenrolmentkeypolicy','1',NULL),(245,0,1390420104,NULL,'disableuserimages','0',NULL),(246,0,1390420105,NULL,'emailchangeconfirmation','1',NULL),(247,0,1390420105,NULL,'rememberusername','2',NULL),(248,0,1390420106,NULL,'strictformsrequired','0',NULL),(249,0,1390420106,NULL,'loginhttps','0',NULL),(250,0,1390420106,NULL,'cookiesecure','0',NULL),(251,0,1390420107,NULL,'cookiehttponly','0',NULL),(252,0,1390420107,NULL,'allowframembedding','0',NULL),(253,0,1390420107,NULL,'loginpasswordautocomplete','0',NULL),(254,0,1390420107,NULL,'displayloginfailures','',NULL),(255,0,1390420108,NULL,'notifyloginfailures','',NULL),(256,0,1390420108,NULL,'notifyloginthreshold','10',NULL),(257,0,1390420108,NULL,'runclamonupload','0',NULL),(258,0,1390420109,NULL,'pathtoclam','',NULL),(259,0,1390420109,NULL,'quarantinedir','',NULL),(260,0,1390420109,NULL,'clamfailureonupload','donothing',NULL),(261,0,1390420109,NULL,'themelist','',NULL),(262,0,1390420110,NULL,'themedesignermode','0',NULL),(263,0,1390420110,NULL,'allowuserthemes','0',NULL),(264,0,1390420111,NULL,'allowcoursethemes','0',NULL),(265,0,1390420111,NULL,'allowcategorythemes','0',NULL),(266,0,1390420112,NULL,'allowthemechangeonurl','0',NULL),(267,0,1390420112,NULL,'allowuserblockhiding','1',NULL),(268,0,1390420112,NULL,'allowblockstodock','1',NULL),(269,0,1390420113,NULL,'custommenuitems','',NULL),(270,0,1390420114,NULL,'enabledevicedetection','1',NULL),(271,0,1390420114,NULL,'devicedetectregex','[]',NULL),(272,0,1390420114,'theme_afterburner','logo','',NULL),(273,0,1390420115,'theme_afterburner','footnote','',NULL),(274,0,1390420115,'theme_afterburner','customcss','',NULL),(275,0,1390420116,'theme_anomaly','tagline','',NULL),(276,0,1390420116,'theme_anomaly','customcss','',NULL),(277,0,1390420116,'theme_arialist','logo','',NULL),(278,0,1390420116,'theme_arialist','tagline','',NULL),(279,0,1390420117,'theme_arialist','linkcolor','#f25f0f',NULL),(280,0,1390420117,'theme_arialist','regionwidth','250',NULL),(281,0,1390420117,'theme_arialist','customcss','',NULL),(282,0,1390420118,'theme_brick','logo','',NULL),(283,0,1390420119,'theme_brick','linkcolor','#06365b',NULL),(284,0,1390420119,'theme_brick','linkhover','#5487ad',NULL),(285,0,1390420120,'theme_brick','maincolor','#8e2800',NULL),(286,0,1390420120,'theme_brick','maincolorlink','#fff0a5',NULL),(287,0,1390420120,'theme_brick','headingcolor','#5c3500',NULL),(288,0,1390420121,'theme_clean','invert','0',NULL),(289,0,1390420121,'theme_clean','logo','',NULL),(290,0,1390420121,'theme_clean','customcss','',NULL),(291,0,1390420122,'theme_clean','footnote','',NULL),(292,0,1390420122,'theme_formal_white','fontsizereference','13',NULL),(293,0,1390420123,'theme_formal_white','noframe','0',NULL),(294,0,1390420123,'theme_formal_white','framemargin','15',NULL),(295,0,1390420123,'theme_formal_white','headercontent','1',NULL),(296,0,1390420124,'theme_formal_white','trendcolor','mink',NULL),(297,0,1390420124,'theme_formal_white','customlogourl','',NULL),(298,0,1390420125,'theme_formal_white','frontpagelogourl','',NULL),(299,0,1390420125,'theme_formal_white','headerbgc','#E3DFD4',NULL),(300,0,1390420125,'theme_formal_white','creditstomoodleorg','2',NULL),(301,0,1390420125,'theme_formal_white','blockcolumnwidth','200',NULL),(302,0,1390420126,'theme_formal_white','blockpadding','8',NULL),(303,0,1390420126,'theme_formal_white','blockcontentbgc','#F6F6F6',NULL),(304,0,1390420126,'theme_formal_white','lblockcolumnbgc','#E3DFD4',NULL),(305,0,1390420127,'theme_formal_white','rblockcolumnbgc','',NULL),(306,0,1390420128,'theme_formal_white','footnote','',NULL),(307,0,1390420128,'theme_formal_white','customcss','',NULL),(308,0,1390420128,'theme_fusion','linkcolor','#2d83d5',NULL),(309,0,1390420128,'theme_fusion','tagline','',NULL),(310,0,1390420129,'theme_fusion','footertext','',NULL),(311,0,1390420130,'theme_fusion','customcss','',NULL),(312,0,1390420131,'theme_magazine','background','',NULL),(313,0,1390420131,'theme_magazine','logo','',NULL),(314,0,1390420132,'theme_magazine','linkcolor','#32529a',NULL),(315,0,1390420132,'theme_magazine','linkhover','#4e2300',NULL),(316,0,1390420133,'theme_magazine','maincolor','#002f2f',NULL),(317,0,1390420133,'theme_magazine','maincoloraccent','#092323',NULL),(318,0,1390420133,'theme_magazine','headingcolor','#4e0000',NULL),(319,0,1390420133,'theme_magazine','blockcolor','#002f2f',NULL),(320,0,1390420133,'theme_magazine','forumback','#e6e2af',NULL),(321,0,1390420134,'theme_nimble','tagline','',NULL),(322,0,1390420134,'theme_nimble','footerline','',NULL),(323,0,1390420134,'theme_nimble','backgroundcolor','#454545',NULL),(324,0,1390420135,'theme_nimble','linkcolor','#2a65b1',NULL),(325,0,1390420135,'theme_nimble','linkhover','#222222',NULL),(326,0,1390420135,'theme_nonzero','regionprewidth','200',NULL),(327,0,1390420136,'theme_nonzero','regionpostwidth','200',NULL),(328,0,1390420136,'theme_nonzero','customcss','',NULL),(329,0,1390420136,'theme_overlay','linkcolor','#428ab5',NULL),(330,0,1390420137,'theme_overlay','headercolor','#2a4c7b',NULL),(331,0,1390420137,'theme_overlay','footertext','',NULL),(332,0,1390420137,'theme_overlay','customcss','',NULL),(333,0,1390420138,'theme_sky_high','logo','',NULL),(334,0,1390420138,'theme_sky_high','regionwidth','240',NULL),(335,0,1390420138,'theme_sky_high','footnote','',NULL),(336,0,1390420138,'theme_sky_high','customcss','',NULL),(337,0,1390420138,'theme_splash','logo','',NULL),(338,0,1390420138,'theme_splash','tagline','Virtual learning center',NULL),(339,0,1390420139,'theme_splash','hide_tagline','0',NULL),(340,0,1390420139,'theme_splash','footnote','',NULL),(341,0,1390420140,'theme_splash','customcss','',NULL),(342,0,1390420140,NULL,'calendar_adminseesall','0',NULL),(343,0,1390420140,NULL,'calendar_site_timeformat','0',NULL),(344,0,1390420140,NULL,'calendar_startwday','0',NULL),(345,0,1390420141,NULL,'calendar_weekend','65',NULL),(346,0,1390420141,NULL,'calendar_lookahead','21',NULL),(347,0,1390420141,NULL,'calendar_maxevents','10',NULL),(348,0,1390420141,NULL,'enablecalendarexport','1',NULL),(349,0,1390420142,NULL,'calendar_customexport','1',NULL),(350,0,1390420142,NULL,'calendar_exportlookahead','365',NULL),(351,0,1390420142,NULL,'calendar_exportlookback','5',NULL),(352,0,1390420143,NULL,'calendar_exportsalt','02Z4qihRq5Zq5JumMSEziKpVISIFFsQ8KUg7xf4JW7YG4uL1RQ07yCAYmE6G',NULL),(353,0,1390420143,NULL,'calendar_showicalsource','1',NULL),(354,0,1390420144,NULL,'useblogassociations','1',NULL),(355,0,1390420144,NULL,'bloglevel','4',NULL),(356,0,1390420144,NULL,'useexternalblogs','1',NULL),(357,0,1390420144,NULL,'externalblogcrontime','86400',NULL),(358,0,1390420144,NULL,'maxexternalblogsperuser','1',NULL),(359,0,1390420145,NULL,'blogusecomments','1',NULL),(360,0,1390420145,NULL,'blogshowcommentscount','1',NULL),(361,0,1390420145,NULL,'defaulthomepage','0',NULL),(362,0,1390420145,NULL,'allowguestmymoodle','1',NULL),(363,0,1390420146,NULL,'navshowfullcoursenames','0',NULL),(364,0,1390420146,NULL,'navshowcategories','1',NULL),(365,0,1390420146,NULL,'navshowmycoursecategories','0',NULL),(366,0,1390420146,NULL,'navshowallcourses','0',NULL),(367,0,1390420146,NULL,'navsortmycoursessort','sortorder',NULL),(368,0,1390420147,NULL,'navcourselimit','20',NULL),(369,0,1390420147,NULL,'usesitenameforsitepages','0',NULL),(370,0,1390420147,NULL,'linkadmincategories','0',NULL),(371,0,1390420147,NULL,'navshowfrontpagemods','1',NULL),(372,0,1390420147,NULL,'navadduserpostslinks','1',NULL),(373,0,1390420147,NULL,'formatstringstriptags','1',NULL),(374,0,1390420147,NULL,'emoticons','[{\"text\":\":-)\",\"imagename\":\"s\\/smiley\",\"imagecomponent\":\"core\",\"altidentifier\":\"smiley\",\"altcomponent\":\"core_pix\"},{\"text\":\":)\",\"imagename\":\"s\\/smiley\",\"imagecomponent\":\"core\",\"altidentifier\":\"smiley\",\"altcomponent\":\"core_pix\"},{\"text\":\":-D\",\"imagename\":\"s\\/biggrin\",\"imagecomponent\":\"core\",\"altidentifier\":\"biggrin\",\"altcomponent\":\"core_pix\"},{\"text\":\";-)\",\"imagename\":\"s\\/wink\",\"imagecomponent\":\"core\",\"altidentifier\":\"wink\",\"altcomponent\":\"core_pix\"},{\"text\":\":-\\/\",\"imagename\":\"s\\/mixed\",\"imagecomponent\":\"core\",\"altidentifier\":\"mixed\",\"altcomponent\":\"core_pix\"},{\"text\":\"V-.\",\"imagename\":\"s\\/thoughtful\",\"imagecomponent\":\"core\",\"altidentifier\":\"thoughtful\",\"altcomponent\":\"core_pix\"},{\"text\":\":-P\",\"imagename\":\"s\\/tongueout\",\"imagecomponent\":\"core\",\"altidentifier\":\"tongueout\",\"altcomponent\":\"core_pix\"},{\"text\":\":-p\",\"imagename\":\"s\\/tongueout\",\"imagecomponent\":\"core\",\"altidentifier\":\"tongueout\",\"altcomponent\":\"core_pix\"},{\"text\":\"B-)\",\"imagename\":\"s\\/cool\",\"imagecomponent\":\"core\",\"altidentifier\":\"cool\",\"altcomponent\":\"core_pix\"},{\"text\":\"^-)\",\"imagename\":\"s\\/approve\",\"imagecomponent\":\"core\",\"altidentifier\":\"approve\",\"altcomponent\":\"core_pix\"},{\"text\":\"8-)\",\"imagename\":\"s\\/wideeyes\",\"imagecomponent\":\"core\",\"altidentifier\":\"wideeyes\",\"altcomponent\":\"core_pix\"},{\"text\":\":o)\",\"imagename\":\"s\\/clown\",\"imagecomponent\":\"core\",\"altidentifier\":\"clown\",\"altcomponent\":\"core_pix\"},{\"text\":\":-(\",\"imagename\":\"s\\/sad\",\"imagecomponent\":\"core\",\"altidentifier\":\"sad\",\"altcomponent\":\"core_pix\"},{\"text\":\":(\",\"imagename\":\"s\\/sad\",\"imagecomponent\":\"core\",\"altidentifier\":\"sad\",\"altcomponent\":\"core_pix\"},{\"text\":\"8-.\",\"imagename\":\"s\\/shy\",\"imagecomponent\":\"core\",\"altidentifier\":\"shy\",\"altcomponent\":\"core_pix\"},{\"text\":\":-I\",\"imagename\":\"s\\/blush\",\"imagecomponent\":\"core\",\"altidentifier\":\"blush\",\"altcomponent\":\"core_pix\"},{\"text\":\":-X\",\"imagename\":\"s\\/kiss\",\"imagecomponent\":\"core\",\"altidentifier\":\"kiss\",\"altcomponent\":\"core_pix\"},{\"text\":\"8-o\",\"imagename\":\"s\\/surprise\",\"imagecomponent\":\"core\",\"altidentifier\":\"surprise\",\"altcomponent\":\"core_pix\"},{\"text\":\"P-|\",\"imagename\":\"s\\/blackeye\",\"imagecomponent\":\"core\",\"altidentifier\":\"blackeye\",\"altcomponent\":\"core_pix\"},{\"text\":\"8-[\",\"imagename\":\"s\\/angry\",\"imagecomponent\":\"core\",\"altidentifier\":\"angry\",\"altcomponent\":\"core_pix\"},{\"text\":\"(grr)\",\"imagename\":\"s\\/angry\",\"imagecomponent\":\"core\",\"altidentifier\":\"angry\",\"altcomponent\":\"core_pix\"},{\"text\":\"xx-P\",\"imagename\":\"s\\/dead\",\"imagecomponent\":\"core\",\"altidentifier\":\"dead\",\"altcomponent\":\"core_pix\"},{\"text\":\"|-.\",\"imagename\":\"s\\/sleepy\",\"imagecomponent\":\"core\",\"altidentifier\":\"sleepy\",\"altcomponent\":\"core_pix\"},{\"text\":\"}-]\",\"imagename\":\"s\\/evil\",\"imagecomponent\":\"core\",\"altidentifier\":\"evil\",\"altcomponent\":\"core_pix\"},{\"text\":\"(h)\",\"imagename\":\"s\\/heart\",\"imagecomponent\":\"core\",\"altidentifier\":\"heart\",\"altcomponent\":\"core_pix\"},{\"text\":\"(heart)\",\"imagename\":\"s\\/heart\",\"imagecomponent\":\"core\",\"altidentifier\":\"heart\",\"altcomponent\":\"core_pix\"},{\"text\":\"(y)\",\"imagename\":\"s\\/yes\",\"imagecomponent\":\"core\",\"altidentifier\":\"yes\",\"altcomponent\":\"core\"},{\"text\":\"(n)\",\"imagename\":\"s\\/no\",\"imagecomponent\":\"core\",\"altidentifier\":\"no\",\"altcomponent\":\"core\"},{\"text\":\"(martin)\",\"imagename\":\"s\\/martin\",\"imagecomponent\":\"core\",\"altidentifier\":\"martin\",\"altcomponent\":\"core_pix\"},{\"text\":\"( )\",\"imagename\":\"s\\/egg\",\"imagecomponent\":\"core\",\"altidentifier\":\"egg\",\"altcomponent\":\"core_pix\"}]',NULL),(375,0,1390420147,NULL,'core_media_enable_youtube','1',NULL),(376,0,1390420148,NULL,'core_media_enable_vimeo','0',NULL),(377,0,1390420148,NULL,'core_media_enable_mp3','1',NULL),(378,0,1390420148,NULL,'core_media_enable_flv','1',NULL),(379,0,1390420148,NULL,'core_media_enable_swf','1',NULL),(380,0,1390420149,NULL,'core_media_enable_html5audio','1',NULL),(381,0,1390420149,NULL,'core_media_enable_html5video','1',NULL),(382,0,1390420149,NULL,'core_media_enable_qt','1',NULL),(383,0,1390420150,NULL,'core_media_enable_wmp','1',NULL),(384,0,1390420150,NULL,'core_media_enable_rm','1',NULL),(385,0,1390420150,NULL,'docroot','http://docs.moodle.org',NULL),(386,0,1390420150,NULL,'doctonewwindow','0',NULL),(387,0,1390420151,NULL,'courselistshortnames','0',NULL),(388,0,1390420151,NULL,'coursesperpage','20',NULL),(389,0,1390420151,NULL,'courseswithsummarieslimit','10',NULL),(390,0,1390420151,NULL,'courseoverviewfileslimit','1',NULL),(391,0,1390420152,NULL,'courseoverviewfilesext','.jpg,.gif,.png',NULL),(392,0,1390420152,NULL,'enableajax','1',NULL),(393,0,1390420154,NULL,'useexternalyui','0',NULL),(394,0,1390420154,NULL,'yuicomboloading','1',NULL),(395,0,1390420155,NULL,'cachejs','1',NULL),(396,0,1390420155,NULL,'modchooserdefault','1',NULL),(397,0,1390420156,NULL,'modeditingmenu','1',NULL),(398,0,1390420156,NULL,'blockeditingmenu','1',NULL),(399,0,1390420156,NULL,'additionalhtmlhead','',NULL),(400,0,1390420157,NULL,'additionalhtmltopofbody','',NULL),(401,0,1390420157,NULL,'additionalhtmlfooter','',NULL),(402,0,1390420157,NULL,'pathtodu','',NULL),(403,0,1390420158,NULL,'aspellpath','',NULL),(404,0,1390420159,NULL,'pathtodot','',NULL),(405,0,1390420159,NULL,'supportpage','',NULL),(406,0,1390420159,NULL,'dbsessions','0',NULL),(407,0,1390420160,NULL,'sessioncookie','',NULL),(408,0,1390420160,NULL,'sessioncookiepath','',NULL),(409,0,1390420160,NULL,'sessioncookiedomain','',NULL),(410,0,1390420161,NULL,'statsfirstrun','none',NULL),(411,0,1390420161,NULL,'statsmaxruntime','0',NULL),(412,0,1390420161,NULL,'statsruntimedays','31',NULL),(413,0,1390420161,NULL,'statsruntimestarthour','0',NULL),(414,0,1390420162,NULL,'statsruntimestartminute','0',NULL),(415,0,1390420162,NULL,'statsuserthreshold','0',NULL),(416,0,1390420162,NULL,'slasharguments','1',NULL),(417,0,1390420162,NULL,'getremoteaddrconf','0',NULL),(418,0,1390420163,NULL,'proxyhost','',NULL),(419,0,1390420163,NULL,'proxyport','0',NULL),(420,0,1390420164,NULL,'proxytype','HTTP',NULL),(421,0,1390420164,NULL,'proxyuser','',NULL),(422,0,1390420164,NULL,'proxypassword','',NULL),(423,0,1390420164,NULL,'proxybypass','localhost, 127.0.0.1',NULL),(424,0,1390420164,NULL,'maintenance_enabled','0',NULL),(425,0,1390420165,NULL,'maintenance_message','',NULL),(426,0,1390420166,NULL,'deleteunconfirmed','168',NULL),(427,0,1390420166,NULL,'deleteincompleteusers','0',NULL),(428,0,1390420166,NULL,'logguests','1',NULL),(429,0,1390420166,NULL,'loglifetime','0',NULL),(430,0,1390420166,NULL,'disablegradehistory','0',NULL),(431,0,1390420166,NULL,'gradehistorylifetime','0',NULL),(432,0,1390420167,NULL,'extramemorylimit','512M',NULL),(433,0,1390420167,NULL,'curlcache','120',NULL),(434,0,1390420167,NULL,'curltimeoutkbitrate','56',NULL),(435,0,1390420168,NULL,'updateautocheck','1',NULL),(436,0,1390420168,NULL,'updateautodeploy','0',NULL),(437,0,1390420168,NULL,'updateminmaturity','200',NULL),(438,0,1390420169,NULL,'updatenotifybuilds','0',NULL),(439,0,1390420169,NULL,'enablesafebrowserintegration','0',NULL),(440,0,1390420169,NULL,'enablegroupmembersonly','0',NULL),(441,0,1390420170,NULL,'dndallowtextandlinks','0',NULL),(442,0,1390420171,NULL,'enablecssoptimiser','0',NULL),(443,0,1390420171,NULL,'enabletgzbackups','0',NULL),(444,0,1390420172,NULL,'debug','0',NULL),(445,0,1390420172,NULL,'debugdisplay','0',NULL),(446,0,1390420173,NULL,'debugsmtp','0',NULL),(447,0,1390420174,NULL,'perfdebug','7',NULL),(448,0,1390420174,NULL,'debugstringids','0',NULL),(449,0,1390420175,NULL,'debugvalidators','0',NULL),(450,0,1390420175,NULL,'debugpageinfo','0',NULL),(451,0,1390420641,'activitynames','filter_active','1',''),(452,0,1390420647,'mediaplugin','filter_active','1',''),(453,2,1390421835,NULL,'notloggedinroleid','6',NULL),(454,2,1390421835,NULL,'guestroleid','6',NULL),(455,2,1390421835,NULL,'defaultuserroleid','7',NULL),(456,2,1390421835,NULL,'creatornewroleid','3',NULL),(457,2,1390421836,NULL,'restorernewroleid','3',NULL),(458,2,1390421836,NULL,'gradebookroles','5',NULL),(459,2,1390421836,'assign','feedback_plugin_for_gradebook','assignfeedback_comments',NULL),(460,2,1390421837,'assign','showrecentsubmissions','0',NULL),(461,2,1390421837,'assign','submissionreceipts','1',NULL),(462,2,1390421837,'assign','submissionstatement','This assignment is my own work, except where I have acknowledged the use of the works of other people.',NULL),(463,2,1390421838,'assign','alwaysshowdescription','1',NULL),(464,2,1390421838,'assign','alwaysshowdescription_adv','',NULL),(465,2,1390421839,'assign','alwaysshowdescription_locked','',NULL),(466,2,1390421839,'assign','allowsubmissionsfromdate','0',NULL),(467,2,1390421839,'assign','allowsubmissionsfromdate_enabled','1',NULL),(468,2,1390421839,'assign','allowsubmissionsfromdate_adv','',NULL),(469,2,1390421840,'assign','duedate','604800',NULL),(470,2,1390421840,'assign','duedate_enabled','1',NULL),(471,2,1390421841,'assign','duedate_adv','',NULL),(472,2,1390421841,'assign','cutoffdate','1209600',NULL),(473,2,1390421841,'assign','cutoffdate_enabled','',NULL),(474,2,1390421841,'assign','cutoffdate_adv','',NULL),(475,2,1390421842,'assign','submissiondrafts','0',NULL),(476,2,1390421842,'assign','submissiondrafts_adv','',NULL),(477,2,1390421842,'assign','submissiondrafts_locked','',NULL),(478,2,1390421842,'assign','requiresubmissionstatement','0',NULL),(479,2,1390421842,'assign','requiresubmissionstatement_adv','',NULL),(480,2,1390421842,'assign','requiresubmissionstatement_locked','',NULL),(481,2,1390421843,'assign','attemptreopenmethod','none',NULL),(482,2,1390421843,'assign','attemptreopenmethod_adv','',NULL),(483,2,1390421843,'assign','attemptreopenmethod_locked','',NULL),(484,2,1390421843,'assign','maxattempts','-1',NULL),(485,2,1390421843,'assign','maxattempts_adv','',NULL),(486,2,1390421843,'assign','maxattempts_locked','',NULL),(487,2,1390421843,'assign','teamsubmission','0',NULL),(488,2,1390421843,'assign','teamsubmission_adv','',NULL),(489,2,1390421844,'assign','teamsubmission_locked','',NULL),(490,2,1390421844,'assign','requireallteammemberssubmit','0',NULL),(491,2,1390421844,'assign','requireallteammemberssubmit_adv','',NULL),(492,2,1390421844,'assign','requireallteammemberssubmit_locked','',NULL),(493,2,1390421845,'assign','teamsubmissiongroupingid','',NULL),(494,2,1390421845,'assign','teamsubmissiongroupingid_adv','',NULL),(495,2,1390421845,'assign','sendnotifications','0',NULL),(496,2,1390421845,'assign','sendnotifications_adv','',NULL),(497,2,1390421845,'assign','sendnotifications_locked','',NULL),(498,2,1390421845,'assign','sendlatenotifications','0',NULL),(499,2,1390421845,'assign','sendlatenotifications_adv','',NULL),(500,2,1390421846,'assign','sendlatenotifications_locked','',NULL),(501,2,1390421846,'assign','blindmarking','0',NULL),(502,2,1390421846,'assign','blindmarking_adv','',NULL),(503,2,1390421847,'assign','blindmarking_locked','',NULL),(504,2,1390421847,'assign','markingworkflow','0',NULL),(505,2,1390421847,'assign','markingworkflow_adv','',NULL),(506,2,1390421847,'assign','markingworkflow_locked','',NULL),(507,2,1390421848,'assign','markingallocation','0',NULL),(508,2,1390421848,'assign','markingallocation_adv','',NULL),(509,2,1390421848,'assign','markingallocation_locked','',NULL),(510,2,1390421848,'assignsubmission_file','default','1',NULL),(511,2,1390421848,'assignsubmission_file','maxbytes','1048576',NULL),(512,2,1390421849,'assignsubmission_onlinetext','default','0',NULL),(513,2,1390421849,'assignfeedback_comments','default','1',NULL),(514,2,1390421849,'assignfeedback_editpdf','stamps','',NULL),(515,2,1390421849,'assignfeedback_editpdf','gspath','/usr/bin/gs',NULL),(516,2,1390421849,'assignfeedback_file','default','0',NULL),(517,2,1390421849,'assignfeedback_offline','default','0',NULL),(518,2,1390421849,NULL,'assignment_maxbytes','1048576',NULL),(519,2,1390421850,NULL,'assignment_itemstocount','1',NULL),(520,2,1390421850,NULL,'assignment_showrecentsubmissions','1',NULL),(521,2,1390421850,'book','requiremodintro','1',NULL),(522,2,1390421850,'book','numberingoptions','0,1,2,3',NULL),(523,2,1390421850,'book','numbering','1',NULL),(524,2,1390421851,NULL,'chat_method','ajax',NULL),(525,2,1390421851,NULL,'chat_refresh_userlist','10',NULL),(526,2,1390421851,NULL,'chat_old_ping','35',NULL),(527,2,1390421851,NULL,'chat_refresh_room','5',NULL),(528,2,1390421852,NULL,'chat_normal_updatemode','jsupdate',NULL),(529,2,1390421853,NULL,'chat_serverhost','127.0.0.1',NULL),(530,2,1390421853,NULL,'chat_serverip','127.0.0.1',NULL),(531,2,1390421853,NULL,'chat_serverport','9111',NULL),(532,2,1390421854,NULL,'chat_servermax','100',NULL),(533,2,1390421854,NULL,'data_enablerssfeeds','0',NULL),(534,2,1390421855,NULL,'feedback_allowfullanonymous','0',NULL),(535,2,1390421855,'folder','requiremodintro','1',NULL),(536,2,1390421855,'folder','showexpanded','1',NULL),(537,2,1390421856,NULL,'forum_displaymode','3',NULL),(538,2,1390421856,NULL,'forum_replytouser','1',NULL),(539,2,1390421856,NULL,'forum_shortpost','300',NULL),(540,2,1390421856,NULL,'forum_longpost','600',NULL),(541,2,1390421857,NULL,'forum_manydiscussions','100',NULL),(542,2,1390421857,NULL,'forum_maxbytes','512000',NULL),(543,2,1390421857,NULL,'forum_maxattachments','9',NULL),(544,2,1390421857,NULL,'forum_trackingtype','1',NULL),(545,2,1390421857,NULL,'forum_trackreadposts','1',NULL),(546,2,1390421858,NULL,'forum_allowforcedreadtracking','0',NULL),(547,2,1390421858,NULL,'forum_oldpostdays','14',NULL),(548,2,1390421858,NULL,'forum_usermarksread','0',NULL),(549,2,1390421858,NULL,'forum_cleanreadtime','2',NULL),(550,2,1390421858,NULL,'digestmailtime','17',NULL),(551,2,1390421859,NULL,'forum_enablerssfeeds','0',NULL),(552,2,1390421859,NULL,'forum_enabletimedposts','0',NULL),(553,2,1390421859,NULL,'glossary_entbypage','10',NULL),(554,2,1390421859,NULL,'glossary_dupentries','0',NULL),(555,2,1390421859,NULL,'glossary_allowcomments','0',NULL),(556,2,1390421860,NULL,'glossary_linkbydefault','1',NULL),(557,2,1390421860,NULL,'glossary_defaultapproval','1',NULL),(558,2,1390421860,NULL,'glossary_enablerssfeeds','0',NULL),(559,2,1390421862,NULL,'glossary_linkentries','0',NULL),(560,2,1390421863,NULL,'glossary_casesensitive','0',NULL),(561,2,1390421863,NULL,'glossary_fullmatch','0',NULL),(562,2,1390421863,'imscp','requiremodintro','1',NULL),(563,2,1390421863,'imscp','keepold','1',NULL),(564,2,1390421863,'imscp','keepold_adv','',NULL),(565,2,1390421864,'label','dndmedia','1',NULL),(566,2,1390421864,'label','dndresizewidth','400',NULL),(567,2,1390421864,'label','dndresizeheight','400',NULL),(568,2,1390421865,NULL,'lesson_slideshowwidth','640',NULL),(569,2,1390421865,NULL,'lesson_slideshowheight','480',NULL),(570,2,1390421865,NULL,'lesson_slideshowbgcolor','#FFFFFF',NULL),(571,2,1390421865,NULL,'lesson_mediawidth','640',NULL),(572,2,1390421865,NULL,'lesson_mediaheight','480',NULL),(573,2,1390421866,NULL,'lesson_mediaclose','0',NULL),(574,2,1390421866,NULL,'lesson_maxhighscores','10',NULL),(575,2,1390421866,NULL,'lesson_maxanswers','4',NULL),(576,2,1390421867,NULL,'lesson_defaultnextpage','0',NULL),(577,2,1390421867,'page','requiremodintro','1',NULL),(578,2,1390421867,'page','displayoptions','5',NULL),(579,2,1390421867,'page','printintro','0',NULL),(580,2,1390421867,'page','display','5',NULL),(581,2,1390421867,'page','popupwidth','620',NULL),(582,2,1390421868,'page','popupheight','450',NULL),(583,2,1390421868,'quiz','timelimit','0',NULL),(584,2,1390421868,'quiz','timelimit_adv','',NULL),(585,2,1390421868,'quiz','overduehandling','autoabandon',NULL),(586,2,1390421868,'quiz','overduehandling_adv','',NULL),(587,2,1390421869,'quiz','graceperiod','86400',NULL),(588,2,1390421869,'quiz','graceperiod_adv','',NULL),(589,2,1390421869,'quiz','graceperiodmin','60',NULL),(590,2,1390421869,'quiz','attempts','0',NULL),(591,2,1390421869,'quiz','attempts_adv','',NULL),(592,2,1390421869,'quiz','grademethod','1',NULL),(593,2,1390421869,'quiz','grademethod_adv','',NULL),(594,2,1390421869,'quiz','maximumgrade','10',NULL),(595,2,1390421869,'quiz','shufflequestions','0',NULL),(596,2,1390421870,'quiz','shufflequestions_adv','',NULL),(597,2,1390421870,'quiz','questionsperpage','1',NULL),(598,2,1390421870,'quiz','questionsperpage_adv','',NULL),(599,2,1390421870,'quiz','navmethod','free',NULL),(600,2,1390421871,'quiz','navmethod_adv','1',NULL),(601,2,1390421871,'quiz','shuffleanswers','1',NULL),(602,2,1390421871,'quiz','shuffleanswers_adv','',NULL),(603,2,1390421871,'quiz','preferredbehaviour','deferredfeedback',NULL),(604,2,1390421872,'quiz','attemptonlast','0',NULL),(605,2,1390421872,'quiz','attemptonlast_adv','1',NULL),(606,2,1390421873,'quiz','reviewattempt','69904',NULL),(607,2,1390421873,'quiz','reviewcorrectness','69904',NULL),(608,2,1390421873,'quiz','reviewmarks','69904',NULL),(609,2,1390421873,'quiz','reviewspecificfeedback','69904',NULL),(610,2,1390421874,'quiz','reviewgeneralfeedback','69904',NULL),(611,2,1390421874,'quiz','reviewrightanswer','69904',NULL),(612,2,1390421874,'quiz','reviewoverallfeedback','4368',NULL),(613,2,1390421874,'quiz','showuserpicture','0',NULL),(614,2,1390421874,'quiz','showuserpicture_adv','',NULL),(615,2,1390421875,'quiz','decimalpoints','2',NULL),(616,2,1390421876,'quiz','decimalpoints_adv','',NULL),(617,2,1390421877,'quiz','questiondecimalpoints','-1',NULL),(618,2,1390421877,'quiz','questiondecimalpoints_adv','1',NULL),(619,2,1390421877,'quiz','showblocks','0',NULL),(620,2,1390421877,'quiz','showblocks_adv','1',NULL),(621,2,1390421878,'quiz','password','',NULL),(622,2,1390421878,'quiz','password_adv','1',NULL),(623,2,1390421878,'quiz','subnet','',NULL),(624,2,1390421879,'quiz','subnet_adv','1',NULL),(625,2,1390421879,'quiz','delay1','0',NULL),(626,2,1390421879,'quiz','delay1_adv','1',NULL),(627,2,1390421879,'quiz','delay2','0',NULL),(628,2,1390421879,'quiz','delay2_adv','1',NULL),(629,2,1390421880,'quiz','browsersecurity','-',NULL),(630,2,1390421880,'quiz','browsersecurity_adv','1',NULL),(631,2,1390421880,'quiz','autosaveperiod','0',NULL),(632,2,1390421880,'resource','framesize','130',NULL),(633,2,1390421881,'resource','requiremodintro','1',NULL),(634,2,1390421881,'resource','displayoptions','0,1,4,5,6',NULL),(635,2,1390421881,'resource','printintro','1',NULL),(636,2,1390421882,'resource','display','0',NULL),(637,2,1390421882,'resource','showsize','0',NULL),(638,2,1390421883,'resource','showtype','0',NULL),(639,2,1390421883,'resource','popupwidth','620',NULL),(640,2,1390421883,'resource','popupheight','450',NULL),(641,2,1390421884,'resource','filterfiles','0',NULL),(642,2,1390421884,'scorm','displaycoursestructure','0',NULL),(643,2,1390421884,'scorm','displaycoursestructure_adv','',NULL),(644,2,1390421884,'scorm','popup','0',NULL),(645,2,1390421884,'scorm','popup_adv','',NULL),(646,2,1390421885,'scorm','framewidth','100',NULL),(647,2,1390421885,'scorm','framewidth_adv','1',NULL),(648,2,1390421885,'scorm','frameheight','500',NULL),(649,2,1390421885,'scorm','frameheight_adv','1',NULL),(650,2,1390421885,'scorm','winoptgrp_adv','1',NULL),(651,2,1390421886,'scorm','scrollbars','0',NULL),(652,2,1390421886,'scorm','directories','0',NULL),(653,2,1390421886,'scorm','location','0',NULL),(654,2,1390421886,'scorm','menubar','0',NULL),(655,2,1390421887,'scorm','toolbar','0',NULL),(656,2,1390421887,'scorm','status','0',NULL),(657,2,1390421888,'scorm','skipview','0',NULL),(658,2,1390421888,'scorm','skipview_adv','1',NULL),(659,2,1390421888,'scorm','hidebrowse','0',NULL),(660,2,1390421889,'scorm','hidebrowse_adv','1',NULL),(661,2,1390421889,'scorm','hidetoc','0',NULL),(662,2,1390421889,'scorm','hidetoc_adv','1',NULL),(663,2,1390421890,'scorm','nav','1',NULL),(664,2,1390421890,'scorm','nav_adv','1',NULL),(665,2,1390421890,'scorm','navpositionleft','-100',NULL),(666,2,1390421890,'scorm','navpositionleft_adv','1',NULL),(667,2,1390421891,'scorm','navpositiontop','-100',NULL),(668,2,1390421891,'scorm','navpositiontop_adv','1',NULL),(669,2,1390421891,'scorm','collapsetocwinsize','767',NULL),(670,2,1390421892,'scorm','collapsetocwinsize_adv','1',NULL),(671,2,1390421892,'scorm','displayattemptstatus','1',NULL),(672,2,1390421892,'scorm','displayattemptstatus_adv','',NULL),(673,2,1390421893,'scorm','grademethod','1',NULL),(674,2,1390421893,'scorm','maxgrade','100',NULL),(675,2,1390421893,'scorm','maxattempt','0',NULL),(676,2,1390421893,'scorm','whatgrade','0',NULL),(677,2,1390421893,'scorm','forcecompleted','0',NULL),(678,2,1390421894,'scorm','forcenewattempt','0',NULL),(679,2,1390421894,'scorm','lastattemptlock','0',NULL),(680,2,1390421894,'scorm','auto','0',NULL),(681,2,1390421894,'scorm','updatefreq','0',NULL),(682,2,1390421894,'scorm','allowtypeexternal','0',NULL),(683,2,1390421894,'scorm','allowtypelocalsync','0',NULL),(684,2,1390421894,'scorm','allowtypeexternalaicc','0',NULL),(685,2,1390421895,'scorm','allowaicchacp','0',NULL),(686,2,1390421895,'scorm','aicchacptimeout','30',NULL),(687,2,1390421895,'scorm','aicchacpkeepsessiondata','1',NULL),(688,2,1390421895,'scorm','forcejavascript','1',NULL),(689,2,1390421895,'scorm','allowapidebug','0',NULL),(690,2,1390421896,'scorm','apidebugmask','.*',NULL),(691,2,1390421896,'url','framesize','130',NULL),(692,2,1390421896,'url','requiremodintro','1',NULL),(693,2,1390421896,'url','secretphrase','',NULL),(694,2,1390421896,'url','rolesinparams','0',NULL),(695,2,1390421897,'url','displayoptions','0,1,5,6',NULL),(696,2,1390421897,'url','printintro','1',NULL),(697,2,1390421897,'url','display','0',NULL),(698,2,1390421898,'url','popupwidth','620',NULL),(699,2,1390421899,'url','popupheight','450',NULL),(700,2,1390421899,'workshop','grade','80',NULL),(701,2,1390421900,'workshop','gradinggrade','20',NULL),(702,2,1390421900,'workshop','gradedecimals','0',NULL),(703,2,1390421901,'workshop','maxbytes','0',NULL),(704,2,1390421901,'workshop','strategy','accumulative',NULL),(705,2,1390421901,'workshop','examplesmode','0',NULL),(706,2,1390421901,'workshopallocation_random','numofreviews','5',NULL),(707,2,1390421901,'workshopform_numerrors','grade0','No',NULL),(708,2,1390421901,'workshopform_numerrors','grade1','Yes',NULL),(709,2,1390421902,'workshopeval_best','comparison','5',NULL),(710,2,1390421902,'format_singleactivity','activitytype','forum',NULL),(711,2,1390421902,NULL,'block_course_list_adminview','all',NULL),(712,2,1390421902,NULL,'block_course_list_hideallcourseslink','0',NULL),(713,2,1390421902,'block_course_overview','defaultmaxcourses','10',NULL),(714,2,1390421902,'block_course_overview','forcedefaultmaxcourses','0',NULL),(715,2,1390421903,'block_course_overview','showchildren','0',NULL),(716,2,1390421903,'block_course_overview','showwelcomearea','0',NULL),(717,2,1390421903,NULL,'block_html_allowcssclasses','0',NULL),(718,2,1390421903,NULL,'block_online_users_timetosee','5',NULL),(719,2,1390421904,NULL,'block_rss_client_num_entries','5',NULL),(720,2,1390421904,NULL,'block_rss_client_timeout','30',NULL),(721,2,1390421904,'block_section_links','numsections1','22',NULL),(722,2,1390421904,'block_section_links','incby1','2',NULL),(723,2,1390421904,'block_section_links','numsections2','40',NULL),(724,2,1390421905,'block_section_links','incby2','5',NULL),(725,2,1390421905,NULL,'block_tags_showcoursetags','0',NULL),(726,2,1390421905,NULL,'smtphosts','',NULL),(727,2,1390421906,NULL,'smtpsecure','',NULL),(728,2,1390421906,NULL,'smtpuser','',NULL),(729,2,1390421906,NULL,'smtppass','',NULL),(730,2,1390421906,NULL,'smtpmaxbulk','1',NULL),(731,2,1390421907,NULL,'noreplyaddress','noreply@127.0.0.1',NULL),(732,2,1390421907,NULL,'sitemailcharset','0',NULL),(733,2,1390421907,NULL,'allowusermailcharset','0',NULL),(734,2,1390421908,NULL,'allowattachments','1',NULL),(735,2,1390421908,NULL,'mailnewline','LF',NULL),(736,2,1390421908,NULL,'jabberhost','',NULL),(737,2,1390421908,NULL,'jabberserver','',NULL),(738,2,1390421909,NULL,'jabberusername','',NULL),(739,2,1390421910,NULL,'jabberpassword','',NULL),(740,2,1390421911,NULL,'jabberport','5222',NULL),(741,2,1390421911,'enrol_cohort','roleid','5',NULL),(742,2,1390421911,'enrol_cohort','unenrolaction','0',NULL),(743,2,1390421911,'enrol_database','dbtype','',NULL),(744,2,1390421912,'enrol_database','dbhost','localhost',NULL),(745,2,1390421912,'enrol_database','dbuser','',NULL),(746,2,1390421912,'enrol_database','dbpass','',NULL),(747,2,1390421913,'enrol_database','dbname','',NULL),(748,2,1390421913,'enrol_database','dbencoding','utf-8',NULL),(749,2,1390421914,'enrol_database','dbsetupsql','',NULL),(750,2,1390421914,'enrol_database','dbsybasequoting','0',NULL),(751,2,1390421915,'enrol_database','debugdb','0',NULL),(752,2,1390421915,'enrol_database','localcoursefield','idnumber',NULL),(753,2,1390421915,'enrol_database','localuserfield','idnumber',NULL),(754,2,1390421915,'enrol_database','localrolefield','shortname',NULL),(755,2,1390421916,'enrol_database','localcategoryfield','id',NULL),(756,2,1390421917,'enrol_database','remoteenroltable','',NULL),(757,2,1390421917,'enrol_database','remotecoursefield','',NULL),(758,2,1390421917,'enrol_database','remoteuserfield','',NULL),(759,2,1390421918,'enrol_database','remoterolefield','',NULL),(760,2,1390421918,'enrol_database','defaultrole','5',NULL),(761,2,1390421918,'enrol_database','ignorehiddencourses','0',NULL),(762,2,1390421918,'enrol_database','unenrolaction','0',NULL),(763,2,1390421918,'enrol_database','newcoursetable','',NULL),(764,2,1390421919,'enrol_database','newcoursefullname','fullname',NULL),(765,2,1390421919,'enrol_database','newcourseshortname','shortname',NULL),(766,2,1390421919,'enrol_database','newcourseidnumber','idnumber',NULL),(767,2,1390421920,'enrol_database','newcoursecategory','',NULL),(768,2,1390421920,'enrol_database','defaultcategory','1',NULL),(769,2,1390421920,'enrol_database','templatecourse','',NULL),(770,2,1390421921,'enrol_flatfile','location','',NULL),(771,2,1390421921,'enrol_flatfile','encoding','UTF-8',NULL),(772,2,1390421922,'enrol_flatfile','mailstudents','0',NULL),(773,2,1390421922,'enrol_flatfile','mailteachers','0',NULL),(774,2,1390421922,'enrol_flatfile','mailadmins','0',NULL),(775,2,1390421922,'enrol_flatfile','unenrolaction','3',NULL),(776,2,1390421922,'enrol_flatfile','expiredaction','3',NULL),(777,2,1390421923,'enrol_guest','requirepassword','0',NULL),(778,2,1390421923,'enrol_guest','usepasswordpolicy','0',NULL),(779,2,1390421923,'enrol_guest','showhint','0',NULL),(780,2,1390421923,'enrol_guest','defaultenrol','1',NULL),(781,2,1390421924,'enrol_guest','status','1',NULL),(782,2,1390421924,'enrol_guest','status_adv','',NULL),(783,2,1390421925,'enrol_imsenterprise','imsfilelocation','',NULL),(784,2,1390421926,'enrol_imsenterprise','logtolocation','',NULL),(785,2,1390421926,'enrol_imsenterprise','mailadmins','0',NULL),(786,2,1390421927,'enrol_imsenterprise','createnewusers','0',NULL),(787,2,1390421927,'enrol_imsenterprise','imsdeleteusers','0',NULL),(788,2,1390421928,'enrol_imsenterprise','fixcaseusernames','0',NULL),(789,2,1390421928,'enrol_imsenterprise','fixcasepersonalnames','0',NULL),(790,2,1390421928,'enrol_imsenterprise','imssourcedidfallback','0',NULL),(791,2,1390421928,'enrol_imsenterprise','imsrolemap01','5',NULL),(792,2,1390421929,'enrol_imsenterprise','imsrolemap02','3',NULL),(793,2,1390421929,'enrol_imsenterprise','imsrolemap03','3',NULL),(794,2,1390421929,'enrol_imsenterprise','imsrolemap04','5',NULL),(795,2,1390421929,'enrol_imsenterprise','imsrolemap05','0',NULL),(796,2,1390421930,'enrol_imsenterprise','imsrolemap06','4',NULL),(797,2,1390421930,'enrol_imsenterprise','imsrolemap07','0',NULL),(798,2,1390421930,'enrol_imsenterprise','imsrolemap08','4',NULL),(799,2,1390421931,'enrol_imsenterprise','truncatecoursecodes','0',NULL),(800,2,1390421931,'enrol_imsenterprise','createnewcourses','0',NULL),(801,2,1390421931,'enrol_imsenterprise','createnewcategories','0',NULL),(802,2,1390421932,'enrol_imsenterprise','imsunenrol','0',NULL),(803,2,1390421932,'enrol_imsenterprise','imscoursemapshortname','coursecode',NULL),(804,2,1390421932,'enrol_imsenterprise','imscoursemapfullname','short',NULL),(805,2,1390421934,'enrol_imsenterprise','imscoursemapsummary','ignore',NULL),(806,2,1390421934,'enrol_imsenterprise','imsrestricttarget','',NULL),(807,2,1390421934,'enrol_imsenterprise','imscapitafix','0',NULL),(808,2,1390421934,'enrol_manual','expiredaction','1',NULL),(809,2,1390421934,'enrol_manual','expirynotifyhour','6',NULL),(810,2,1390421934,'enrol_manual','defaultenrol','1',NULL),(811,2,1390421935,'enrol_manual','status','0',NULL),(812,2,1390421935,'enrol_manual','roleid','5',NULL),(813,2,1390421936,'enrol_manual','enrolperiod','0',NULL),(814,2,1390421936,'enrol_manual','expirynotify','0',NULL),(815,2,1390421936,'enrol_manual','expirythreshold','86400',NULL),(816,2,1390421937,'enrol_meta','nosyncroleids','',NULL),(817,2,1390421937,'enrol_meta','syncall','1',NULL),(818,2,1390421938,'enrol_meta','unenrolaction','3',NULL),(819,2,1390421938,'enrol_mnet','roleid','5',NULL),(820,2,1390421938,'enrol_mnet','roleid_adv','1',NULL),(821,2,1390421938,'enrol_paypal','paypalbusiness','',NULL),(822,2,1390421938,'enrol_paypal','mailstudents','0',NULL),(823,2,1390421939,'enrol_paypal','mailteachers','0',NULL),(824,2,1390421939,'enrol_paypal','mailadmins','0',NULL),(825,2,1390421939,'enrol_paypal','expiredaction','3',NULL),(826,2,1390421940,'enrol_paypal','status','1',NULL),(827,2,1390421940,'enrol_paypal','cost','0',NULL),(828,2,1390421940,'enrol_paypal','currency','USD',NULL),(829,2,1390421941,'enrol_paypal','roleid','5',NULL),(830,2,1390421941,'enrol_paypal','enrolperiod','0',NULL),(831,2,1390421941,'enrol_self','requirepassword','0',NULL),(832,2,1390421942,'enrol_self','usepasswordpolicy','0',NULL),(833,2,1390421942,'enrol_self','showhint','0',NULL),(834,2,1390421942,'enrol_self','expiredaction','1',NULL),(835,2,1390421942,'enrol_self','expirynotifyhour','6',NULL),(836,2,1390421943,'enrol_self','defaultenrol','1',NULL),(837,2,1390421943,'enrol_self','status','1',NULL),(838,2,1390421943,'enrol_self','newenrols','1',NULL),(839,2,1390421943,'enrol_self','groupkey','0',NULL),(840,2,1390421943,'enrol_self','roleid','5',NULL),(841,2,1390421943,'enrol_self','enrolperiod','0',NULL),(842,2,1390421944,'enrol_self','expirynotify','0',NULL),(843,2,1390421946,'enrol_self','expirythreshold','86400',NULL),(844,2,1390421946,'enrol_self','longtimenosee','0',NULL),(845,2,1390421946,'enrol_self','maxenrolled','0',NULL),(846,2,1390421946,'enrol_self','sendcoursewelcomemessage','1',NULL),(847,2,1390421946,'editor_tinymce','customtoolbar','wrap,formatselect,wrap,bold,italic,wrap,bullist,numlist,wrap,link,unlink,wrap,image\n\nundo,redo,wrap,underline,strikethrough,sub,sup,wrap,justifyleft,justifycenter,justifyright,wrap,outdent,indent,wrap,forecolor,backcolor,wrap,ltr,rtl\n\nfontselect,fontsizeselect,wrap,code,search,replace,wrap,nonbreaking,charmap,table,wrap,cleanup,removeformat,pastetext,pasteword,wrap,fullscreen',NULL),(848,2,1390421947,'editor_tinymce','fontselectlist','Trebuchet=Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;Wingdings=wingdings',NULL),(849,2,1390421947,'editor_tinymce','customconfig','',NULL),(850,2,1390421947,'tinymce_dragmath','requiretex','1',NULL),(851,2,1390421947,'tinymce_moodleemoticon','requireemoticon','1',NULL),(852,2,1390421948,'tinymce_spellchecker','spellengine','',NULL),(853,2,1390421948,'tinymce_spellchecker','spelllanguagelist','+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv',NULL),(854,2,1390421948,NULL,'filter_censor_badwords','',NULL),(855,2,1390421949,'filter_emoticon','formats','1,4,0',NULL),(856,2,1390421949,NULL,'filter_multilang_force_old','0',NULL),(857,2,1390421949,NULL,'filter_tex_latexpreamble','\\usepackage[latin1]{inputenc}\n\\usepackage{amsmath}\n\\usepackage{amsfonts}\n\\RequirePackage{amsmath,amssymb,latexsym}\n',NULL),(858,2,1390421949,NULL,'filter_tex_latexbackground','#FFFFFF',NULL),(859,2,1390421949,NULL,'filter_tex_density','120',NULL),(860,2,1390421950,NULL,'filter_tex_pathlatex','/usr/bin/latex',NULL),(861,2,1390421950,NULL,'filter_tex_pathdvips','/usr/bin/dvips',NULL),(862,2,1390421950,NULL,'filter_tex_pathconvert','/usr/bin/convert',NULL),(863,2,1390421951,NULL,'filter_tex_convertformat','gif',NULL),(864,2,1390421951,'filter_urltolink','formats','0',NULL),(865,2,1390421951,'filter_urltolink','embedimages','1',NULL),(866,2,1390421952,NULL,'profileroles','5,4,3',NULL),(867,2,1390421952,NULL,'coursecontact','3',NULL),(868,2,1390421952,NULL,'frontpage','6',NULL),(869,2,1390421953,NULL,'frontpageloggedin','6',NULL),(870,2,1390421953,NULL,'maxcategorydepth','2',NULL),(871,2,1390421953,NULL,'frontpagecourselimit','200',NULL),(872,2,1390421954,NULL,'commentsperpage','15',NULL),(873,2,1390421954,NULL,'defaultfrontpageroleid','8',NULL),(874,2,1390421954,NULL,'supportname','Admin User',NULL),(875,2,1390421954,NULL,'supportemail','de.kosinski@gmail.com',NULL),(876,2,1390422094,NULL,'registerauth','',NULL);
/*!40000 ALTER TABLE `mdl_config_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_config_plugins`
--

DROP TABLE IF EXISTS `mdl_config_plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_config_plugins` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(100) NOT NULL DEFAULT 'core',
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_confplug_plunam_uix` (`plugin`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1028 DEFAULT CHARSET=utf8 COMMENT='Moodle modules and plugins configuration variables';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_config_plugins`
--

LOCK TABLES `mdl_config_plugins` WRITE;
/*!40000 ALTER TABLE `mdl_config_plugins` DISABLE KEYS */;
INSERT INTO `mdl_config_plugins` VALUES (1,'moodlecourse','visible','1'),(2,'moodlecourse','format','weeks'),(3,'moodlecourse','maxsections','52'),(4,'moodlecourse','numsections','10'),(5,'moodlecourse','hiddensections','0'),(6,'moodlecourse','coursedisplay','0'),(7,'moodlecourse','lang',''),(8,'moodlecourse','newsitems','5'),(9,'moodlecourse','showgrades','1'),(10,'moodlecourse','showreports','0'),(11,'moodlecourse','maxbytes','0'),(12,'moodlecourse','enablecompletion','0'),(13,'moodlecourse','groupmode','0'),(14,'moodlecourse','groupmodeforce','0'),(15,'backup','loglifetime','30'),(16,'backup','backup_general_users','1'),(17,'backup','backup_general_users_locked',''),(18,'backup','backup_general_anonymize','0'),(19,'backup','backup_general_anonymize_locked',''),(20,'backup','backup_general_role_assignments','1'),(21,'backup','backup_general_role_assignments_locked',''),(22,'backup','backup_general_activities','1'),(23,'backup','backup_general_activities_locked',''),(24,'backup','backup_general_blocks','1'),(25,'backup','backup_general_blocks_locked',''),(26,'backup','backup_general_filters','1'),(27,'backup','backup_general_filters_locked',''),(28,'backup','backup_general_comments','1'),(29,'backup','backup_general_comments_locked',''),(30,'backup','backup_general_badges','1'),(31,'backup','backup_general_badges_locked',''),(32,'backup','backup_general_userscompletion','1'),(33,'backup','backup_general_userscompletion_locked',''),(34,'backup','backup_general_logs','0'),(35,'backup','backup_general_logs_locked',''),(36,'backup','backup_general_histories','0'),(37,'backup','backup_general_histories_locked',''),(38,'backup','backup_general_questionbank','1'),(39,'backup','backup_general_questionbank_locked',''),(40,'backup','import_general_maxresults','10'),(41,'backup','backup_auto_active','0'),(42,'backup','backup_auto_weekdays','0000000'),(43,'backup','backup_auto_hour','0'),(44,'backup','backup_auto_minute','0'),(45,'backup','backup_auto_storage','0'),(46,'backup','backup_auto_destination',''),(47,'backup','backup_auto_keep','1'),(48,'backup','backup_shortname','0'),(49,'backup','backup_auto_skip_hidden','1'),(50,'backup','backup_auto_skip_modif_days','30'),(51,'backup','backup_auto_skip_modif_prev','0'),(52,'backup','backup_auto_users','1'),(53,'backup','backup_auto_role_assignments','1'),(54,'backup','backup_auto_activities','1'),(55,'backup','backup_auto_blocks','1'),(56,'backup','backup_auto_filters','1'),(57,'backup','backup_auto_comments','1'),(58,'backup','backup_auto_badges','1'),(59,'backup','backup_auto_userscompletion','1'),(60,'backup','backup_auto_logs','0'),(61,'backup','backup_auto_histories','0'),(62,'backup','backup_auto_questionbank','1'),(63,'question_preview','behaviour','deferredfeedback'),(64,'question_preview','correctness','1'),(65,'question_preview','marks','1'),(66,'question_preview','markdp','2'),(67,'question_preview','feedback','1'),(68,'question_preview','generalfeedback','1'),(69,'question_preview','rightanswer','1'),(70,'question_preview','history','0'),(71,'cachestore_memcache','testservers',''),(72,'cachestore_memcached','testservers',''),(73,'cachestore_mongodb','testserver',''),(74,'theme_afterburner','logo',''),(75,'theme_afterburner','footnote',''),(76,'theme_afterburner','customcss',''),(77,'theme_anomaly','tagline',''),(78,'theme_anomaly','customcss',''),(79,'theme_arialist','logo',''),(80,'theme_arialist','tagline',''),(81,'theme_arialist','linkcolor','#f25f0f'),(82,'theme_arialist','regionwidth','250'),(83,'theme_arialist','customcss',''),(84,'theme_brick','logo',''),(85,'theme_brick','linkcolor','#06365b'),(86,'theme_brick','linkhover','#5487ad'),(87,'theme_brick','maincolor','#8e2800'),(88,'theme_brick','maincolorlink','#fff0a5'),(89,'theme_brick','headingcolor','#5c3500'),(90,'theme_clean','invert','0'),(91,'theme_clean','logo',''),(92,'theme_clean','customcss',''),(93,'theme_clean','footnote',''),(94,'theme_formal_white','fontsizereference','13'),(95,'theme_formal_white','noframe','0'),(96,'theme_formal_white','framemargin','15'),(97,'theme_formal_white','headercontent','1'),(98,'theme_formal_white','trendcolor','mink'),(99,'theme_formal_white','customlogourl',''),(100,'theme_formal_white','frontpagelogourl',''),(101,'theme_formal_white','headerbgc','#E3DFD4'),(102,'theme_formal_white','creditstomoodleorg','2'),(103,'theme_formal_white','blockcolumnwidth','200'),(104,'theme_formal_white','blockpadding','8'),(105,'theme_formal_white','blockcontentbgc','#F6F6F6'),(106,'theme_formal_white','lblockcolumnbgc','#E3DFD4'),(107,'theme_formal_white','rblockcolumnbgc',''),(108,'theme_formal_white','footnote',''),(109,'theme_formal_white','customcss',''),(110,'theme_fusion','linkcolor','#2d83d5'),(111,'theme_fusion','tagline',''),(112,'theme_fusion','footertext',''),(113,'theme_fusion','customcss',''),(114,'theme_magazine','background',''),(115,'theme_magazine','logo',''),(116,'theme_magazine','linkcolor','#32529a'),(117,'theme_magazine','linkhover','#4e2300'),(118,'theme_magazine','maincolor','#002f2f'),(119,'theme_magazine','maincoloraccent','#092323'),(120,'theme_magazine','headingcolor','#4e0000'),(121,'theme_magazine','blockcolor','#002f2f'),(122,'theme_magazine','forumback','#e6e2af'),(123,'theme_nimble','tagline',''),(124,'theme_nimble','footerline',''),(125,'theme_nimble','backgroundcolor','#454545'),(126,'theme_nimble','linkcolor','#2a65b1'),(127,'theme_nimble','linkhover','#222222'),(128,'theme_nonzero','regionprewidth','200'),(129,'theme_nonzero','regionpostwidth','200'),(130,'theme_nonzero','customcss',''),(131,'theme_overlay','linkcolor','#428ab5'),(132,'theme_overlay','headercolor','#2a4c7b'),(133,'theme_overlay','footertext',''),(134,'theme_overlay','customcss',''),(135,'theme_sky_high','logo',''),(136,'theme_sky_high','regionwidth','240'),(137,'theme_sky_high','footnote',''),(138,'theme_sky_high','customcss',''),(139,'theme_splash','logo',''),(140,'theme_splash','tagline','Virtual learning center'),(141,'theme_splash','hide_tagline','0'),(142,'theme_splash','footnote',''),(143,'theme_splash','customcss',''),(144,'qtype_calculated','version','2013110500'),(145,'qtype_calculatedmulti','version','2013110500'),(146,'qtype_calculatedsimple','version','2013110500'),(147,'qtype_description','version','2013110500'),(148,'qtype_essay','version','2013110500'),(149,'qtype_match','version','2013110500'),(150,'qtype_missingtype','version','2013110500'),(151,'qtype_multianswer','version','2013110500'),(152,'qtype_multichoice','version','2013110500'),(153,'qtype_numerical','version','2013110500'),(154,'qtype_random','version','2013110500'),(155,'qtype_randomsamatch','version','2013110500'),(156,'qtype_shortanswer','version','2013110500'),(157,'qtype_truefalse','version','2013110500'),(158,'mod_assign','version','2013110500'),(159,'mod_assignment','version','2013110500'),(161,'mod_book','version','2013110500'),(162,'mod_chat','version','2013110500'),(163,'mod_choice','version','2013110500'),(164,'mod_data','version','2013110500'),(165,'mod_feedback','version','2013110500'),(167,'mod_folder','version','2013110500'),(169,'mod_forum','version','2013110500'),(170,'mod_glossary','version','2013110500'),(171,'mod_imscp','version','2013110500'),(173,'mod_label','version','2013110500'),(174,'mod_lesson','version','2013110500'),(175,'mod_lti','version','2013110500'),(176,'mod_page','version','2013110500'),(178,'mod_quiz','version','2013110501'),(179,'mod_resource','version','2013110500'),(180,'mod_scorm','version','2013110501'),(181,'mod_survey','version','2013110500'),(183,'mod_url','version','2013110500'),(185,'mod_wiki','version','2013110500'),(187,'mod_workshop','version','2013110500'),(188,'auth_cas','version','2013110500'),(190,'auth_db','version','2013110500'),(192,'auth_email','version','2013110500'),(193,'auth_fc','version','2013110500'),(195,'auth_imap','version','2013110500'),(197,'auth_ldap','version','2013110500'),(199,'auth_manual','version','2013110500'),(200,'auth_mnet','version','2013110500'),(202,'auth_nntp','version','2013110500'),(204,'auth_nologin','version','2013110500'),(205,'auth_none','version','2013110500'),(206,'auth_pam','version','2013110500'),(208,'auth_pop3','version','2013110500'),(210,'auth_radius','version','2013110500'),(212,'auth_shibboleth','version','2013110500'),(214,'auth_webservice','version','2013110500'),(215,'calendartype_gregorian','version','2013110500'),(216,'enrol_category','version','2013110500'),(218,'enrol_cohort','version','2013110500'),(219,'enrol_database','version','2013110500'),(221,'enrol_flatfile','version','2013110500'),(223,'enrol_flatfile','map_1','manager'),(224,'enrol_flatfile','map_2','coursecreator'),(225,'enrol_flatfile','map_3','editingteacher'),(226,'enrol_flatfile','map_4','teacher'),(227,'enrol_flatfile','map_5','student'),(228,'enrol_flatfile','map_6','guest'),(229,'enrol_flatfile','map_7','user'),(230,'enrol_flatfile','map_8','frontpage'),(231,'enrol_guest','version','2013110500'),(232,'enrol_imsenterprise','version','2013110500'),(234,'enrol_ldap','version','2013110500'),(236,'enrol_manual','version','2013110500'),(238,'enrol_meta','version','2013110500'),(240,'enrol_mnet','version','2013110500'),(241,'enrol_paypal','version','2013110500'),(242,'enrol_self','version','2013110501'),(244,'message_email','version','2013110500'),(246,'message','email_provider_enrol_flatfile_flatfile_enrolment_permitted','permitted'),(247,'message','message_provider_enrol_flatfile_flatfile_enrolment_loggedin','email'),(248,'message','message_provider_enrol_flatfile_flatfile_enrolment_loggedoff','email'),(249,'message','email_provider_enrol_imsenterprise_imsenterprise_enrolment_permitted','permitted'),(250,'message','message_provider_enrol_imsenterprise_imsenterprise_enrolment_loggedin','email'),(251,'message','message_provider_enrol_imsenterprise_imsenterprise_enrolment_loggedoff','email'),(252,'message','email_provider_enrol_manual_expiry_notification_permitted','permitted'),(253,'message','message_provider_enrol_manual_expiry_notification_loggedin','email'),(254,'message','message_provider_enrol_manual_expiry_notification_loggedoff','email'),(255,'message','email_provider_enrol_paypal_paypal_enrolment_permitted','permitted'),(256,'message','message_provider_enrol_paypal_paypal_enrolment_loggedin','email'),(257,'message','message_provider_enrol_paypal_paypal_enrolment_loggedoff','email'),(258,'message','email_provider_enrol_self_expiry_notification_permitted','permitted'),(259,'message','message_provider_enrol_self_expiry_notification_loggedin','email'),(260,'message','message_provider_enrol_self_expiry_notification_loggedoff','email'),(261,'message','email_provider_mod_assign_assign_notification_permitted','permitted'),(262,'message','message_provider_mod_assign_assign_notification_loggedin','email'),(263,'message','message_provider_mod_assign_assign_notification_loggedoff','email'),(264,'message','email_provider_mod_assignment_assignment_updates_permitted','permitted'),(265,'message','message_provider_mod_assignment_assignment_updates_loggedin','email'),(266,'message','message_provider_mod_assignment_assignment_updates_loggedoff','email'),(267,'message','email_provider_mod_feedback_submission_permitted','permitted'),(268,'message','message_provider_mod_feedback_submission_loggedin','email'),(269,'message','message_provider_mod_feedback_submission_loggedoff','email'),(270,'message','email_provider_mod_feedback_message_permitted','permitted'),(271,'message','message_provider_mod_feedback_message_loggedin','email'),(272,'message','message_provider_mod_feedback_message_loggedoff','email'),(273,'message','email_provider_mod_forum_posts_permitted','permitted'),(274,'message','message_provider_mod_forum_posts_loggedin','email'),(275,'message','message_provider_mod_forum_posts_loggedoff','email'),(276,'message','email_provider_mod_lesson_graded_essay_permitted','permitted'),(277,'message','message_provider_mod_lesson_graded_essay_loggedin','email'),(278,'message','message_provider_mod_lesson_graded_essay_loggedoff','email'),(279,'message','email_provider_mod_quiz_submission_permitted','permitted'),(280,'message','message_provider_mod_quiz_submission_loggedin','email'),(281,'message','message_provider_mod_quiz_submission_loggedoff','email'),(282,'message','email_provider_mod_quiz_confirmation_permitted','permitted'),(283,'message','message_provider_mod_quiz_confirmation_loggedin','email'),(284,'message','message_provider_mod_quiz_confirmation_loggedoff','email'),(285,'message','email_provider_mod_quiz_attempt_overdue_permitted','permitted'),(286,'message','message_provider_mod_quiz_attempt_overdue_loggedin','email'),(287,'message','message_provider_mod_quiz_attempt_overdue_loggedoff','email'),(288,'message','email_provider_moodle_notices_permitted','permitted'),(289,'message','message_provider_moodle_notices_loggedin','email'),(290,'message','message_provider_moodle_notices_loggedoff','email'),(291,'message','email_provider_moodle_errors_permitted','permitted'),(292,'message','message_provider_moodle_errors_loggedin','email'),(293,'message','message_provider_moodle_errors_loggedoff','email'),(294,'message','email_provider_moodle_availableupdate_permitted','permitted'),(295,'message','message_provider_moodle_availableupdate_loggedin','email'),(296,'message','message_provider_moodle_availableupdate_loggedoff','email'),(297,'message','email_provider_moodle_instantmessage_permitted','permitted'),(298,'message','message_provider_moodle_instantmessage_loggedoff','popup,email'),(299,'message','email_provider_moodle_backup_permitted','permitted'),(300,'message','message_provider_moodle_backup_loggedin','email'),(301,'message','message_provider_moodle_backup_loggedoff','email'),(302,'message','email_provider_moodle_courserequested_permitted','permitted'),(303,'message','message_provider_moodle_courserequested_loggedin','email'),(304,'message','message_provider_moodle_courserequested_loggedoff','email'),(305,'message','email_provider_moodle_courserequestapproved_permitted','permitted'),(306,'message','message_provider_moodle_courserequestapproved_loggedin','email'),(307,'message','message_provider_moodle_courserequestapproved_loggedoff','email'),(308,'message','email_provider_moodle_courserequestrejected_permitted','permitted'),(309,'message','message_provider_moodle_courserequestrejected_loggedin','email'),(310,'message','message_provider_moodle_courserequestrejected_loggedoff','email'),(311,'message','email_provider_moodle_badgerecipientnotice_permitted','permitted'),(312,'message','message_provider_moodle_badgerecipientnotice_loggedoff','popup,email'),(313,'message','email_provider_moodle_badgecreatornotice_permitted','permitted'),(314,'message','message_provider_moodle_badgecreatornotice_loggedoff','email'),(315,'message_jabber','version','2013110500'),(317,'message','jabber_provider_enrol_flatfile_flatfile_enrolment_permitted','permitted'),(318,'message','jabber_provider_enrol_imsenterprise_imsenterprise_enrolment_permitted','permitted'),(319,'message','jabber_provider_enrol_manual_expiry_notification_permitted','permitted'),(320,'message','jabber_provider_enrol_paypal_paypal_enrolment_permitted','permitted'),(321,'message','jabber_provider_enrol_self_expiry_notification_permitted','permitted'),(322,'message','jabber_provider_mod_assign_assign_notification_permitted','permitted'),(323,'message','jabber_provider_mod_assignment_assignment_updates_permitted','permitted'),(324,'message','jabber_provider_mod_feedback_submission_permitted','permitted'),(325,'message','jabber_provider_mod_feedback_message_permitted','permitted'),(326,'message','jabber_provider_mod_forum_posts_permitted','permitted'),(327,'message','jabber_provider_mod_lesson_graded_essay_permitted','permitted'),(328,'message','jabber_provider_mod_quiz_submission_permitted','permitted'),(329,'message','jabber_provider_mod_quiz_confirmation_permitted','permitted'),(330,'message','jabber_provider_mod_quiz_attempt_overdue_permitted','permitted'),(331,'message','jabber_provider_moodle_notices_permitted','permitted'),(332,'message','jabber_provider_moodle_errors_permitted','permitted'),(333,'message','jabber_provider_moodle_availableupdate_permitted','permitted'),(334,'message','jabber_provider_moodle_instantmessage_permitted','permitted'),(335,'message','jabber_provider_moodle_backup_permitted','permitted'),(336,'message','jabber_provider_moodle_courserequested_permitted','permitted'),(337,'message','jabber_provider_moodle_courserequestapproved_permitted','permitted'),(338,'message','jabber_provider_moodle_courserequestrejected_permitted','permitted'),(339,'message','jabber_provider_moodle_badgerecipientnotice_permitted','permitted'),(340,'message','jabber_provider_moodle_badgecreatornotice_permitted','permitted'),(341,'message_popup','version','2013110500'),(343,'message','popup_provider_enrol_flatfile_flatfile_enrolment_permitted','permitted'),(344,'message','popup_provider_enrol_imsenterprise_imsenterprise_enrolment_permitted','permitted'),(345,'message','popup_provider_enrol_manual_expiry_notification_permitted','permitted'),(346,'message','popup_provider_enrol_paypal_paypal_enrolment_permitted','permitted'),(347,'message','popup_provider_enrol_self_expiry_notification_permitted','permitted'),(348,'message','popup_provider_mod_assign_assign_notification_permitted','permitted'),(349,'message','popup_provider_mod_assignment_assignment_updates_permitted','permitted'),(350,'message','popup_provider_mod_feedback_submission_permitted','permitted'),(351,'message','popup_provider_mod_feedback_message_permitted','permitted'),(352,'message','popup_provider_mod_forum_posts_permitted','permitted'),(353,'message','popup_provider_mod_lesson_graded_essay_permitted','permitted'),(354,'message','popup_provider_mod_quiz_submission_permitted','permitted'),(355,'message','popup_provider_mod_quiz_confirmation_permitted','permitted'),(356,'message','popup_provider_mod_quiz_attempt_overdue_permitted','permitted'),(357,'message','popup_provider_moodle_notices_permitted','permitted'),(358,'message','popup_provider_moodle_errors_permitted','permitted'),(359,'message','popup_provider_moodle_availableupdate_permitted','permitted'),(360,'message','popup_provider_moodle_instantmessage_permitted','permitted'),(361,'message','message_provider_moodle_instantmessage_loggedin','popup'),(362,'message','popup_provider_moodle_backup_permitted','permitted'),(363,'message','popup_provider_moodle_courserequested_permitted','permitted'),(364,'message','popup_provider_moodle_courserequestapproved_permitted','permitted'),(365,'message','popup_provider_moodle_courserequestrejected_permitted','permitted'),(366,'message','popup_provider_moodle_badgerecipientnotice_permitted','permitted'),(367,'message','message_provider_moodle_badgerecipientnotice_loggedin','popup'),(368,'message','popup_provider_moodle_badgecreatornotice_permitted','permitted'),(369,'block_activity_modules','version','2013110500'),(370,'block_admin_bookmarks','version','2013110500'),(371,'block_badges','version','2013110500'),(372,'block_blog_menu','version','2013110500'),(373,'block_blog_recent','version','2013110500'),(374,'block_blog_tags','version','2013110500'),(375,'block_calendar_month','version','2013110500'),(376,'block_calendar_upcoming','version','2013110500'),(377,'block_comments','version','2013110500'),(378,'block_community','version','2013110500'),(379,'block_completionstatus','version','2013110500'),(380,'block_course_list','version','2013110500'),(381,'block_course_overview','version','2013110500'),(382,'block_course_summary','version','2013110500'),(383,'block_feedback','version','2013110500'),(385,'block_glossary_random','version','2013110500'),(386,'block_html','version','2013110500'),(387,'block_login','version','2013110500'),(388,'block_mentees','version','2013110500'),(389,'block_messages','version','2013110500'),(390,'block_mnet_hosts','version','2013110500'),(391,'block_myprofile','version','2013110500'),(392,'block_navigation','version','2013110500'),(393,'block_news_items','version','2013110500'),(394,'block_online_users','version','2013110500'),(395,'block_participants','version','2013110500'),(396,'block_private_files','version','2013110500'),(397,'block_quiz_results','version','2013110500'),(398,'block_recent_activity','version','2013110500'),(399,'block_rss_client','version','2013110500'),(400,'block_search_forums','version','2013110500'),(401,'block_section_links','version','2013110500'),(402,'block_selfcompletion','version','2013110500'),(403,'block_settings','version','2013110500'),(404,'block_site_main_menu','version','2013110500'),(405,'block_social_activities','version','2013110500'),(406,'block_tag_flickr','version','2013110500'),(407,'block_tag_youtube','version','2013110500'),(408,'block_tags','version','2013110500'),(409,'filter_activitynames','version','2013110500'),(411,'filter_algebra','version','2013110500'),(412,'filter_censor','version','2013110500'),(413,'filter_data','version','2013110500'),(415,'filter_emailprotect','version','2013110500'),(416,'filter_emoticon','version','2013110500'),(417,'filter_glossary','version','2013110500'),(419,'filter_mediaplugin','version','2013110500'),(421,'filter_multilang','version','2013110500'),(422,'filter_tex','version','2013110500'),(424,'filter_tidy','version','2013110500'),(425,'filter_urltolink','version','2013110500'),(426,'editor_textarea','version','2013110500'),(427,'editor_tinymce','version','2013110600'),(428,'format_singleactivity','version','2013110500'),(429,'format_social','version','2013110500'),(430,'format_topics','version','2013110500'),(431,'format_weeks','version','2013110500'),(432,'profilefield_checkbox','version','2013110500'),(433,'profilefield_datetime','version','2013110500'),(434,'profilefield_menu','version','2013110500'),(435,'profilefield_text','version','2013110500'),(436,'profilefield_textarea','version','2013110500'),(437,'report_backups','version','2013110500'),(438,'report_completion','version','2013110500'),(440,'report_configlog','version','2013110500'),(441,'report_courseoverview','version','2013110500'),(442,'report_log','version','2013110500'),(444,'report_loglive','version','2013110500'),(445,'report_outline','version','2013110500'),(447,'report_participation','version','2013110500'),(449,'report_performance','version','2013110500'),(450,'report_progress','version','2013110500'),(452,'report_questioninstances','version','2013110500'),(453,'report_security','version','2013110500'),(454,'report_stats','version','2013110500'),(456,'gradeexport_ods','version','2013110500'),(457,'gradeexport_txt','version','2013110500'),(458,'gradeexport_xls','version','2013110500'),(459,'gradeexport_xml','version','2013110500'),(460,'gradeimport_csv','version','2013110500'),(461,'gradeimport_xml','version','2013110500'),(462,'gradereport_grader','version','2013110500'),(463,'gradereport_outcomes','version','2013110500'),(464,'gradereport_overview','version','2013110500'),(465,'gradereport_user','version','2013110500'),(466,'gradingform_guide','version','2013110500'),(467,'gradingform_rubric','version','2013110500'),(468,'mnetservice_enrol','version','2013110500'),(469,'webservice_amf','version','2013110500'),(470,'webservice_rest','version','2013110500'),(471,'webservice_soap','version','2013110500'),(472,'webservice_xmlrpc','version','2013110500'),(473,'repository_alfresco','version','2013110500'),(474,'repository_areafiles','version','2013110500'),(476,'areafiles','enablecourseinstances','0'),(477,'areafiles','enableuserinstances','0'),(478,'repository_boxnet','version','2013110700'),(479,'repository_coursefiles','version','2013110500'),(480,'repository_dropbox','version','2013110500'),(481,'repository_equella','version','2013110500'),(482,'repository_filesystem','version','2013110500'),(483,'repository_flickr','version','2013110500'),(484,'repository_flickr_public','version','2013110500'),(485,'repository_googledocs','version','2013110500'),(486,'repository_local','version','2013110500'),(488,'local','enablecourseinstances','0'),(489,'local','enableuserinstances','0'),(490,'repository_merlot','version','2013110500'),(491,'repository_picasa','version','2013110500'),(492,'repository_recent','version','2013110500'),(494,'recent','enablecourseinstances','0'),(495,'recent','enableuserinstances','0'),(496,'repository_s3','version','2013110500'),(497,'repository_skydrive','version','2013110500'),(498,'repository_upload','version','2013110500'),(500,'upload','enablecourseinstances','0'),(501,'upload','enableuserinstances','0'),(502,'repository_url','version','2013110500'),(504,'url','enablecourseinstances','0'),(505,'url','enableuserinstances','0'),(506,'repository_user','version','2013110500'),(508,'user','enablecourseinstances','0'),(509,'user','enableuserinstances','0'),(510,'repository_webdav','version','2013110500'),(511,'repository_wikimedia','version','2013110500'),(513,'wikimedia','enablecourseinstances','0'),(514,'wikimedia','enableuserinstances','0'),(515,'repository_youtube','version','2013110500'),(517,'youtube','enablecourseinstances','0'),(518,'youtube','enableuserinstances','0'),(519,'portfolio_boxnet','version','2013110602'),(520,'portfolio_download','version','2013110500'),(521,'portfolio_flickr','version','2013110500'),(522,'portfolio_googledocs','version','2013110500'),(523,'portfolio_mahara','version','2013110500'),(524,'portfolio_picasa','version','2013110500'),(525,'qbehaviour_adaptive','version','2013110500'),(526,'qbehaviour_adaptivenopenalty','version','2013110500'),(527,'qbehaviour_deferredcbm','version','2013110500'),(528,'qbehaviour_deferredfeedback','version','2013110500'),(529,'qbehaviour_immediatecbm','version','2013110500'),(530,'qbehaviour_immediatefeedback','version','2013110500'),(531,'qbehaviour_informationitem','version','2013110500'),(532,'qbehaviour_interactive','version','2013110500'),(533,'qbehaviour_interactivecountback','version','2013110500'),(534,'qbehaviour_manualgraded','version','2013110500'),(536,'question','disabledbehaviours','manualgraded'),(537,'qbehaviour_missing','version','2013110500'),(538,'qformat_aiken','version','2013110500'),(539,'qformat_blackboard_six','version','2013110500'),(540,'qformat_examview','version','2013110500'),(541,'qformat_gift','version','2013110500'),(542,'qformat_learnwise','version','2013110500'),(543,'qformat_missingword','version','2013110500'),(544,'qformat_multianswer','version','2013110500'),(545,'qformat_webct','version','2013110500'),(546,'qformat_xhtml','version','2013110500'),(547,'qformat_xml','version','2013110500'),(548,'tool_assignmentupgrade','version','2013110500'),(549,'tool_behat','version','2013110501'),(550,'tool_capability','version','2013110500'),(551,'tool_customlang','version','2013110500'),(553,'tool_dbtransfer','version','2013110500'),(554,'tool_generator','version','2013110500'),(555,'tool_health','version','2013110500'),(556,'tool_innodb','version','2013110500'),(557,'tool_installaddon','version','2013110500'),(558,'tool_langimport','version','2013110500'),(559,'tool_multilangupgrade','version','2013110500'),(560,'tool_phpunit','version','2013110500'),(561,'tool_profiling','version','2013110500'),(562,'tool_qeupgradehelper','version','2013110500'),(564,'tool_replace','version','2013110501'),(565,'tool_spamcleaner','version','2013110500'),(566,'tool_timezoneimport','version','2013110500'),(567,'tool_unsuproles','version','2013110500'),(569,'tool_uploadcourse','version','2013110500'),(570,'tool_uploaduser','version','2013110500'),(571,'tool_xmldb','version','2013110500'),(572,'cachestore_file','version','2013110500'),(573,'cachestore_memcache','version','2013110500'),(574,'cachestore_memcached','version','2013110500'),(575,'cachestore_mongodb','version','2013110500'),(576,'cachestore_session','version','2013110500'),(577,'cachestore_static','version','2013110500'),(578,'cachelock_file','version','2013110500'),(579,'theme_afterburner','version','2013110500'),(580,'theme_anomaly','version','2013110500'),(581,'theme_arialist','version','2013110500'),(582,'theme_base','version','2013110500'),(583,'theme_binarius','version','2013110500'),(584,'theme_bootstrapbase','version','2013110500'),(585,'theme_boxxie','version','2013110500'),(586,'theme_brick','version','2013110500'),(587,'theme_canvas','version','2013110500'),(588,'theme_clean','version','2013110500'),(589,'theme_formal_white','version','2013110500'),(591,'theme_formfactor','version','2013110500'),(592,'theme_fusion','version','2013110500'),(593,'theme_leatherbound','version','2013110500'),(594,'theme_magazine','version','2013110500'),(595,'theme_nimble','version','2013110500'),(596,'theme_nonzero','version','2013110500'),(597,'theme_overlay','version','2013110500'),(598,'theme_serenity','version','2013110500'),(599,'theme_sky_high','version','2013110500'),(600,'theme_splash','version','2013110500'),(601,'theme_standard','version','2013110500'),(602,'theme_standardold','version','2013110500'),(603,'assignsubmission_comments','version','2013110500'),(605,'assignsubmission_file','sortorder','1'),(606,'assignsubmission_comments','sortorder','2'),(607,'assignsubmission_onlinetext','sortorder','0'),(608,'assignsubmission_file','version','2013110500'),(609,'assignsubmission_onlinetext','version','2013110500'),(611,'assignfeedback_comments','version','2013110500'),(613,'assignfeedback_comments','sortorder','0'),(614,'assignfeedback_editpdf','sortorder','1'),(615,'assignfeedback_file','sortorder','3'),(616,'assignfeedback_offline','sortorder','2'),(617,'assignfeedback_editpdf','version','2013110800'),(619,'assignfeedback_file','version','2013110500'),(621,'assignfeedback_offline','version','2013110500'),(622,'assignment_offline','version','2013110500'),(623,'assignment_online','version','2013110500'),(624,'assignment_upload','version','2013110500'),(625,'assignment_uploadsingle','version','2013110500'),(626,'booktool_exportimscp','version','2013110500'),(627,'booktool_importhtml','version','2013110500'),(628,'booktool_print','version','2013110500'),(629,'datafield_checkbox','version','2013110500'),(630,'datafield_date','version','2013110500'),(631,'datafield_file','version','2013110500'),(632,'datafield_latlong','version','2013110500'),(633,'datafield_menu','version','2013110500'),(634,'datafield_multimenu','version','2013110500'),(635,'datafield_number','version','2013110500'),(636,'datafield_picture','version','2013110500'),(637,'datafield_radiobutton','version','2013110500'),(638,'datafield_text','version','2013110500'),(639,'datafield_textarea','version','2013110500'),(640,'datafield_url','version','2013110500'),(641,'datapreset_imagegallery','version','2013110500'),(642,'quiz_grading','version','2013110500'),(644,'quiz_overview','version','2013110500'),(646,'quiz_responses','version','2013110500'),(648,'quiz_statistics','version','2013110500'),(650,'quizaccess_delaybetweenattempts','version','2013110500'),(651,'quizaccess_ipaddress','version','2013110500'),(652,'quizaccess_numattempts','version','2013110500'),(653,'quizaccess_openclosedate','version','2013110500'),(654,'quizaccess_password','version','2013110500'),(655,'quizaccess_safebrowser','version','2013110500'),(656,'quizaccess_securewindow','version','2013110500'),(657,'quizaccess_timelimit','version','2013110500'),(658,'scormreport_basic','version','2013110500'),(659,'scormreport_graphs','version','2013110500'),(660,'scormreport_interactions','version','2013110500'),(661,'scormreport_objectives','version','2013110500'),(662,'workshopform_accumulative','version','2013110500'),(664,'workshopform_comments','version','2013110500'),(666,'workshopform_numerrors','version','2013110500'),(668,'workshopform_rubric','version','2013110500'),(670,'workshopallocation_manual','version','2013110500'),(671,'workshopallocation_random','version','2013110500'),(672,'workshopallocation_scheduled','version','2013110500'),(673,'workshopeval_best','version','2013110500'),(674,'tinymce_ctrlhelp','version','2013110500'),(675,'tinymce_dragmath','version','2013110500'),(676,'tinymce_managefiles','version','2014010800'),(677,'tinymce_moodleemoticon','version','2013110500'),(678,'tinymce_moodleimage','version','2013110500'),(679,'tinymce_moodlemedia','version','2013110500'),(680,'tinymce_moodlenolink','version','2013110500'),(681,'tinymce_pdw','version','2013110500'),(682,'tinymce_spellchecker','version','2013110500'),(684,'tinymce_wrap','version','2013110500'),(685,'assign','feedback_plugin_for_gradebook','assignfeedback_comments'),(686,'assign','showrecentsubmissions','0'),(687,'assign','submissionreceipts','1'),(688,'assign','submissionstatement','This assignment is my own work, except where I have acknowledged the use of the works of other people.'),(689,'assign','alwaysshowdescription','1'),(690,'assign','alwaysshowdescription_adv',''),(691,'assign','alwaysshowdescription_locked',''),(692,'assign','allowsubmissionsfromdate','0'),(693,'assign','allowsubmissionsfromdate_enabled','1'),(694,'assign','allowsubmissionsfromdate_adv',''),(695,'assign','duedate','604800'),(696,'assign','duedate_enabled','1'),(697,'assign','duedate_adv',''),(698,'assign','cutoffdate','1209600'),(699,'assign','cutoffdate_enabled',''),(700,'assign','cutoffdate_adv',''),(701,'assign','submissiondrafts','0'),(702,'assign','submissiondrafts_adv',''),(703,'assign','submissiondrafts_locked',''),(704,'assign','requiresubmissionstatement','0'),(705,'assign','requiresubmissionstatement_adv',''),(706,'assign','requiresubmissionstatement_locked',''),(707,'assign','attemptreopenmethod','none'),(708,'assign','attemptreopenmethod_adv',''),(709,'assign','attemptreopenmethod_locked',''),(710,'assign','maxattempts','-1'),(711,'assign','maxattempts_adv',''),(712,'assign','maxattempts_locked',''),(713,'assign','teamsubmission','0'),(714,'assign','teamsubmission_adv',''),(715,'assign','teamsubmission_locked',''),(716,'assign','requireallteammemberssubmit','0'),(717,'assign','requireallteammemberssubmit_adv',''),(718,'assign','requireallteammemberssubmit_locked',''),(719,'assign','teamsubmissiongroupingid',''),(720,'assign','teamsubmissiongroupingid_adv',''),(721,'assign','sendnotifications','0'),(722,'assign','sendnotifications_adv',''),(723,'assign','sendnotifications_locked',''),(724,'assign','sendlatenotifications','0'),(725,'assign','sendlatenotifications_adv',''),(726,'assign','sendlatenotifications_locked',''),(727,'assign','blindmarking','0'),(728,'assign','blindmarking_adv',''),(729,'assign','blindmarking_locked',''),(730,'assign','markingworkflow','0'),(731,'assign','markingworkflow_adv',''),(732,'assign','markingworkflow_locked',''),(733,'assign','markingallocation','0'),(734,'assign','markingallocation_adv',''),(735,'assign','markingallocation_locked',''),(736,'assignsubmission_file','default','1'),(737,'assignsubmission_file','maxbytes','1048576'),(738,'assignsubmission_onlinetext','default','0'),(739,'assignfeedback_comments','default','1'),(740,'assignfeedback_editpdf','stamps',''),(741,'assignfeedback_editpdf','gspath','/usr/bin/gs'),(742,'assignfeedback_file','default','0'),(743,'assignfeedback_offline','default','0'),(744,'book','requiremodintro','1'),(745,'book','numberingoptions','0,1,2,3'),(746,'book','numbering','1'),(747,'folder','requiremodintro','1'),(748,'folder','showexpanded','1'),(749,'imscp','requiremodintro','1'),(750,'imscp','keepold','1'),(751,'imscp','keepold_adv',''),(752,'label','dndmedia','1'),(753,'label','dndresizewidth','400'),(754,'label','dndresizeheight','400'),(755,'page','requiremodintro','1'),(756,'page','displayoptions','5'),(757,'page','printintro','0'),(758,'page','display','5'),(759,'page','popupwidth','620'),(760,'page','popupheight','450'),(761,'quiz','timelimit','0'),(762,'quiz','timelimit_adv',''),(763,'quiz','overduehandling','autoabandon'),(764,'quiz','overduehandling_adv',''),(765,'quiz','graceperiod','86400'),(766,'quiz','graceperiod_adv',''),(767,'quiz','graceperiodmin','60'),(768,'quiz','attempts','0'),(769,'quiz','attempts_adv',''),(770,'quiz','grademethod','1'),(771,'quiz','grademethod_adv',''),(772,'quiz','maximumgrade','10'),(773,'quiz','shufflequestions','0'),(774,'quiz','shufflequestions_adv',''),(775,'quiz','questionsperpage','1'),(776,'quiz','questionsperpage_adv',''),(777,'quiz','navmethod','free'),(778,'quiz','navmethod_adv','1'),(779,'quiz','shuffleanswers','1'),(780,'quiz','shuffleanswers_adv',''),(781,'quiz','preferredbehaviour','deferredfeedback'),(782,'quiz','attemptonlast','0'),(783,'quiz','attemptonlast_adv','1'),(784,'quiz','reviewattempt','69904'),(785,'quiz','reviewcorrectness','69904'),(786,'quiz','reviewmarks','69904'),(787,'quiz','reviewspecificfeedback','69904'),(788,'quiz','reviewgeneralfeedback','69904'),(789,'quiz','reviewrightanswer','69904'),(790,'quiz','reviewoverallfeedback','4368'),(791,'quiz','showuserpicture','0'),(792,'quiz','showuserpicture_adv',''),(793,'quiz','decimalpoints','2'),(794,'quiz','decimalpoints_adv',''),(795,'quiz','questiondecimalpoints','-1'),(796,'quiz','questiondecimalpoints_adv','1'),(797,'quiz','showblocks','0'),(798,'quiz','showblocks_adv','1'),(799,'quiz','password',''),(800,'quiz','password_adv','1'),(801,'quiz','subnet',''),(802,'quiz','subnet_adv','1'),(803,'quiz','delay1','0'),(804,'quiz','delay1_adv','1'),(805,'quiz','delay2','0'),(806,'quiz','delay2_adv','1'),(807,'quiz','browsersecurity','-'),(808,'quiz','browsersecurity_adv','1'),(809,'quiz','autosaveperiod','0'),(810,'resource','framesize','130'),(811,'resource','requiremodintro','1'),(812,'resource','displayoptions','0,1,4,5,6'),(813,'resource','printintro','1'),(814,'resource','display','0'),(815,'resource','showsize','0'),(816,'resource','showtype','0'),(817,'resource','popupwidth','620'),(818,'resource','popupheight','450'),(819,'resource','filterfiles','0'),(820,'scorm','displaycoursestructure','0'),(821,'scorm','displaycoursestructure_adv',''),(822,'scorm','popup','0'),(823,'scorm','popup_adv',''),(824,'scorm','framewidth','100'),(825,'scorm','framewidth_adv','1'),(826,'scorm','frameheight','500'),(827,'scorm','frameheight_adv','1'),(828,'scorm','winoptgrp_adv','1'),(829,'scorm','scrollbars','0'),(830,'scorm','directories','0'),(831,'scorm','location','0'),(832,'scorm','menubar','0'),(833,'scorm','toolbar','0'),(834,'scorm','status','0'),(835,'scorm','skipview','0'),(836,'scorm','skipview_adv','1'),(837,'scorm','hidebrowse','0'),(838,'scorm','hidebrowse_adv','1'),(839,'scorm','hidetoc','0'),(840,'scorm','hidetoc_adv','1'),(841,'scorm','nav','1'),(842,'scorm','nav_adv','1'),(843,'scorm','navpositionleft','-100'),(844,'scorm','navpositionleft_adv','1'),(845,'scorm','navpositiontop','-100'),(846,'scorm','navpositiontop_adv','1'),(847,'scorm','collapsetocwinsize','767'),(848,'scorm','collapsetocwinsize_adv','1'),(849,'scorm','displayattemptstatus','1'),(850,'scorm','displayattemptstatus_adv',''),(851,'scorm','grademethod','1'),(852,'scorm','maxgrade','100'),(853,'scorm','maxattempt','0'),(854,'scorm','whatgrade','0'),(855,'scorm','forcecompleted','0'),(856,'scorm','forcenewattempt','0'),(857,'scorm','lastattemptlock','0'),(858,'scorm','auto','0'),(859,'scorm','updatefreq','0'),(860,'scorm','allowtypeexternal','0'),(861,'scorm','allowtypelocalsync','0'),(862,'scorm','allowtypeexternalaicc','0'),(863,'scorm','allowaicchacp','0'),(864,'scorm','aicchacptimeout','30'),(865,'scorm','aicchacpkeepsessiondata','1'),(866,'scorm','forcejavascript','1'),(867,'scorm','allowapidebug','0'),(868,'scorm','apidebugmask','.*'),(869,'url','framesize','130'),(870,'url','requiremodintro','1'),(871,'url','secretphrase',''),(872,'url','rolesinparams','0'),(873,'url','displayoptions','0,1,5,6'),(874,'url','printintro','1'),(875,'url','display','0'),(876,'url','popupwidth','620'),(877,'url','popupheight','450'),(878,'workshop','grade','80'),(879,'workshop','gradinggrade','20'),(880,'workshop','gradedecimals','0'),(881,'workshop','maxbytes','0'),(882,'workshop','strategy','accumulative'),(883,'workshop','examplesmode','0'),(884,'workshopallocation_random','numofreviews','5'),(885,'workshopform_numerrors','grade0','No'),(886,'workshopform_numerrors','grade1','Yes'),(887,'workshopeval_best','comparison','5'),(888,'format_singleactivity','activitytype','forum'),(889,'block_course_overview','defaultmaxcourses','10'),(890,'block_course_overview','forcedefaultmaxcourses','0'),(891,'block_course_overview','showchildren','0'),(892,'block_course_overview','showwelcomearea','0'),(893,'block_section_links','numsections1','22'),(894,'block_section_links','incby1','2'),(895,'block_section_links','numsections2','40'),(896,'block_section_links','incby2','5'),(897,'enrol_cohort','roleid','5'),(898,'enrol_cohort','unenrolaction','0'),(899,'enrol_database','dbtype',''),(900,'enrol_database','dbhost','localhost'),(901,'enrol_database','dbuser',''),(902,'enrol_database','dbpass',''),(903,'enrol_database','dbname',''),(904,'enrol_database','dbencoding','utf-8'),(905,'enrol_database','dbsetupsql',''),(906,'enrol_database','dbsybasequoting','0'),(907,'enrol_database','debugdb','0'),(908,'enrol_database','localcoursefield','idnumber'),(909,'enrol_database','localuserfield','idnumber'),(910,'enrol_database','localrolefield','shortname'),(911,'enrol_database','localcategoryfield','id'),(912,'enrol_database','remoteenroltable',''),(913,'enrol_database','remotecoursefield',''),(914,'enrol_database','remoteuserfield',''),(915,'enrol_database','remoterolefield',''),(916,'enrol_database','defaultrole','5'),(917,'enrol_database','ignorehiddencourses','0'),(918,'enrol_database','unenrolaction','0'),(919,'enrol_database','newcoursetable',''),(920,'enrol_database','newcoursefullname','fullname'),(921,'enrol_database','newcourseshortname','shortname'),(922,'enrol_database','newcourseidnumber','idnumber'),(923,'enrol_database','newcoursecategory',''),(924,'enrol_database','defaultcategory','1'),(925,'enrol_database','templatecourse',''),(926,'enrol_flatfile','location',''),(927,'enrol_flatfile','encoding','UTF-8'),(928,'enrol_flatfile','mailstudents','0'),(929,'enrol_flatfile','mailteachers','0'),(930,'enrol_flatfile','mailadmins','0'),(931,'enrol_flatfile','unenrolaction','3'),(932,'enrol_flatfile','expiredaction','3'),(933,'enrol_guest','requirepassword','0'),(934,'enrol_guest','usepasswordpolicy','0'),(935,'enrol_guest','showhint','0'),(936,'enrol_guest','defaultenrol','1'),(937,'enrol_guest','status','1'),(938,'enrol_guest','status_adv',''),(939,'enrol_imsenterprise','imsfilelocation',''),(940,'enrol_imsenterprise','logtolocation',''),(941,'enrol_imsenterprise','mailadmins','0'),(942,'enrol_imsenterprise','createnewusers','0'),(943,'enrol_imsenterprise','imsdeleteusers','0'),(944,'enrol_imsenterprise','fixcaseusernames','0'),(945,'enrol_imsenterprise','fixcasepersonalnames','0'),(946,'enrol_imsenterprise','imssourcedidfallback','0'),(947,'enrol_imsenterprise','imsrolemap01','5'),(948,'enrol_imsenterprise','imsrolemap02','3'),(949,'enrol_imsenterprise','imsrolemap03','3'),(950,'enrol_imsenterprise','imsrolemap04','5'),(951,'enrol_imsenterprise','imsrolemap05','0'),(952,'enrol_imsenterprise','imsrolemap06','4'),(953,'enrol_imsenterprise','imsrolemap07','0'),(954,'enrol_imsenterprise','imsrolemap08','4'),(955,'enrol_imsenterprise','truncatecoursecodes','0'),(956,'enrol_imsenterprise','createnewcourses','0'),(957,'enrol_imsenterprise','createnewcategories','0'),(958,'enrol_imsenterprise','imsunenrol','0'),(959,'enrol_imsenterprise','imscoursemapshortname','coursecode'),(960,'enrol_imsenterprise','imscoursemapfullname','short'),(961,'enrol_imsenterprise','imscoursemapsummary','ignore'),(962,'enrol_imsenterprise','imsrestricttarget',''),(963,'enrol_imsenterprise','imscapitafix','0'),(964,'enrol_manual','expiredaction','1'),(965,'enrol_manual','expirynotifyhour','6'),(966,'enrol_manual','defaultenrol','1'),(967,'enrol_manual','status','0'),(968,'enrol_manual','roleid','5'),(969,'enrol_manual','enrolperiod','0'),(970,'enrol_manual','expirynotify','0'),(971,'enrol_manual','expirythreshold','86400'),(972,'enrol_meta','nosyncroleids',''),(973,'enrol_meta','syncall','1'),(974,'enrol_meta','unenrolaction','3'),(975,'enrol_mnet','roleid','5'),(976,'enrol_mnet','roleid_adv','1'),(977,'enrol_paypal','paypalbusiness',''),(978,'enrol_paypal','mailstudents','0'),(979,'enrol_paypal','mailteachers','0'),(980,'enrol_paypal','mailadmins','0'),(981,'enrol_paypal','expiredaction','3'),(982,'enrol_paypal','status','1'),(983,'enrol_paypal','cost','0'),(984,'enrol_paypal','currency','USD'),(985,'enrol_paypal','roleid','5'),(986,'enrol_paypal','enrolperiod','0'),(987,'enrol_self','requirepassword','0'),(988,'enrol_self','usepasswordpolicy','0'),(989,'enrol_self','showhint','0'),(990,'enrol_self','expiredaction','1'),(991,'enrol_self','expirynotifyhour','6'),(992,'enrol_self','defaultenrol','1'),(993,'enrol_self','status','1'),(994,'enrol_self','newenrols','1'),(995,'enrol_self','groupkey','0'),(996,'enrol_self','roleid','5'),(997,'enrol_self','enrolperiod','0'),(998,'enrol_self','expirynotify','0'),(999,'enrol_self','expirythreshold','86400'),(1000,'enrol_self','longtimenosee','0'),(1001,'enrol_self','maxenrolled','0'),(1002,'enrol_self','sendcoursewelcomemessage','1'),(1003,'editor_tinymce','customtoolbar','wrap,formatselect,wrap,bold,italic,wrap,bullist,numlist,wrap,link,unlink,wrap,image\n\nundo,redo,wrap,underline,strikethrough,sub,sup,wrap,justifyleft,justifycenter,justifyright,wrap,outdent,indent,wrap,forecolor,backcolor,wrap,ltr,rtl\n\nfontselect,fontsizeselect,wrap,code,search,replace,wrap,nonbreaking,charmap,table,wrap,cleanup,removeformat,pastetext,pasteword,wrap,fullscreen'),(1004,'editor_tinymce','fontselectlist','Trebuchet=Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;Wingdings=wingdings'),(1005,'editor_tinymce','customconfig',''),(1006,'tinymce_dragmath','requiretex','1'),(1007,'tinymce_moodleemoticon','requireemoticon','1'),(1008,'tinymce_spellchecker','spellengine',''),(1009,'tinymce_spellchecker','spelllanguagelist','+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv'),(1010,'filter_emoticon','formats','1,4,0'),(1011,'filter_urltolink','formats','0'),(1012,'filter_urltolink','embedimages','1'),(1013,'enrol_manual','expirynotifylast','1390473608'),(1014,'enrol_manual','lastcron','1390473608'),(1015,'enrol_self','expirynotifylast','1390473609'),(1016,'enrol_self','lastcron','1390473609'),(1017,'enrol_cohort','lastcron','1390473609'),(1018,'quiz_statistics','lastcron','1390473611'),(1019,'workshopallocation_scheduled','lastcron','1390473612'),(1020,'registration','crontime','1390473612'),(1021,'core_plugin','recentfetch','1390473614'),(1022,'core_plugin','recentresponse','{\"status\":\"OK\",\"provider\":\"https:\\/\\/download.moodle.org\\/api\\/1.2\\/updates.php\",\"apiver\":\"1.2\",\"timegenerated\":1390473614,\"ticket\":\"JUM5JTkxMyVGQzUlRDAlMjklMjklQjYlQzMlOEElRDQlRTM0RCVFMkglQ0YlQzklQTklMDJjJUM5KyUyQiVFRCVGRExtJUFEJTJCWiU4REUlMUIlODUlN0IlOEMlODlM\",\"forbranch\":\"2.6\",\"forversion\":\"2013111801.01\",\"updates\":{\"core\":[{\"version\":2013111801.01,\"release\":\"2.6.1+ (Build: 20140117)\",\"branch\":\"2.6\",\"maturity\":200,\"url\":\"http:\\/\\/download.moodle.org\",\"download\":\"http:\\/\\/download.moodle.org\\/download.php\\/direct\\/stable26\\/moodle-latest-26.zip\"},{\"version\":2014011700,\"release\":\"2.7dev (Build: 20140117)\",\"branch\":\"2.7\",\"maturity\":50,\"url\":\"http:\\/\\/download.moodle.org\",\"download\":\"http:\\/\\/download.moodle.org\\/download.php\\/direct\\/moodle\\/moodle-latest.zip\"}]}}'),(1023,'repository_dropbox','lastcron','1390473615'),(1024,'repository_filesystem','lastcron','1390473615'),(1025,'tool_qeupgradehelper','lastcron','1390473616'),(1026,'enrol_ldap','objectclass','(objectClass=*)'),(1027,'qformat_wordtable','version','2014010201');
/*!40000 ALTER TABLE `mdl_config_plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_context`
--

DROP TABLE IF EXISTS `mdl_context`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_context` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextlevel` bigint(10) NOT NULL DEFAULT '0',
  `instanceid` bigint(10) NOT NULL DEFAULT '0',
  `path` varchar(255) DEFAULT NULL,
  `depth` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_cont_conins_uix` (`contextlevel`,`instanceid`),
  KEY `mdl_cont_ins_ix` (`instanceid`),
  KEY `mdl_cont_pat_ix` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='one of these must be set';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_context`
--

LOCK TABLES `mdl_context` WRITE;
/*!40000 ALTER TABLE `mdl_context` DISABLE KEYS */;
INSERT INTO `mdl_context` VALUES (1,10,0,'/1',1),(2,50,1,'/1/2',2),(3,40,1,'/1/3',2),(4,30,1,'/1/4',2),(5,30,2,'/1/5',2),(6,80,1,'/1/2/6',3),(7,80,2,'/1/2/7',3),(8,80,3,'/1/2/8',3),(9,80,4,'/1/9',2),(10,80,5,'/1/10',2),(11,80,6,'/1/11',2),(12,80,7,'/1/12',2),(13,80,8,'/1/13',2),(14,80,9,'/1/14',2),(17,50,2,'/1/3/17',3),(18,80,10,'/1/3/17/18',4),(19,80,11,'/1/3/17/19',4),(20,80,12,'/1/3/17/20',4),(21,80,13,'/1/3/17/21',4),(22,30,5,'/1/22',2),(23,30,6,'/1/23',2),(24,70,1,'/1/3/17/24',4);
/*!40000 ALTER TABLE `mdl_context` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_context_temp`
--

DROP TABLE IF EXISTS `mdl_context_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_context_temp` (
  `id` bigint(10) NOT NULL,
  `path` varchar(255) NOT NULL DEFAULT '',
  `depth` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Used by build_context_path() in upgrade and cron to keep con';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_context_temp`
--

LOCK TABLES `mdl_context_temp` WRITE;
/*!40000 ALTER TABLE `mdl_context_temp` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_context_temp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course`
--

DROP TABLE IF EXISTS `mdl_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `category` bigint(10) NOT NULL DEFAULT '0',
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  `fullname` varchar(254) NOT NULL DEFAULT '',
  `shortname` varchar(255) NOT NULL DEFAULT '',
  `idnumber` varchar(100) NOT NULL DEFAULT '',
  `summary` longtext,
  `summaryformat` tinyint(2) NOT NULL DEFAULT '0',
  `format` varchar(21) NOT NULL DEFAULT 'topics',
  `showgrades` tinyint(2) NOT NULL DEFAULT '1',
  `newsitems` mediumint(5) NOT NULL DEFAULT '1',
  `startdate` bigint(10) NOT NULL DEFAULT '0',
  `marker` bigint(10) NOT NULL DEFAULT '0',
  `maxbytes` bigint(10) NOT NULL DEFAULT '0',
  `legacyfiles` smallint(4) NOT NULL DEFAULT '0',
  `showreports` smallint(4) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `visibleold` tinyint(1) NOT NULL DEFAULT '1',
  `groupmode` smallint(4) NOT NULL DEFAULT '0',
  `groupmodeforce` smallint(4) NOT NULL DEFAULT '0',
  `defaultgroupingid` bigint(10) NOT NULL DEFAULT '0',
  `lang` varchar(30) NOT NULL DEFAULT '',
  `calendartype` varchar(30) NOT NULL DEFAULT '',
  `theme` varchar(50) NOT NULL DEFAULT '',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `requested` tinyint(1) NOT NULL DEFAULT '0',
  `enablecompletion` tinyint(1) NOT NULL DEFAULT '0',
  `completionnotify` tinyint(1) NOT NULL DEFAULT '0',
  `cacherev` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_cour_cat_ix` (`category`),
  KEY `mdl_cour_idn_ix` (`idnumber`),
  KEY `mdl_cour_sho_ix` (`shortname`),
  KEY `mdl_cour_sor_ix` (`sortorder`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Central course table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course`
--

LOCK TABLES `mdl_course` WRITE;
/*!40000 ALTER TABLE `mdl_course` DISABLE KEYS */;
INSERT INTO `mdl_course` VALUES (1,0,1,'moodle-dev','dev','','<p>lorem ipsum, etc.</p>',0,'site',1,3,0,0,0,0,0,1,1,0,0,0,'','','',1390419884,1390422094,0,0,0,1390505190),(2,1,10001,'testcourse1','tc1','','',1,'weeks',1,5,1390518000,0,0,0,0,1,1,0,0,0,'','','',1390504611,1390504611,0,0,0,1390925725);
/*!40000 ALTER TABLE `mdl_course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_categories`
--

DROP TABLE IF EXISTS `mdl_course_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_categories` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `idnumber` varchar(100) DEFAULT NULL,
  `description` longtext,
  `descriptionformat` tinyint(2) NOT NULL DEFAULT '0',
  `parent` bigint(10) NOT NULL DEFAULT '0',
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  `coursecount` bigint(10) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `visibleold` tinyint(1) NOT NULL DEFAULT '1',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `depth` bigint(10) NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `theme` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_courcate_par_ix` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Course categories';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_categories`
--

LOCK TABLES `mdl_course_categories` WRITE;
/*!40000 ALTER TABLE `mdl_course_categories` DISABLE KEYS */;
INSERT INTO `mdl_course_categories` VALUES (1,'Miscellaneous',NULL,NULL,0,0,10000,1,1,1,1390419885,1,'/1',NULL);
/*!40000 ALTER TABLE `mdl_course_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_completion_aggr_methd`
--

DROP TABLE IF EXISTS `mdl_course_completion_aggr_methd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_completion_aggr_methd` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `criteriatype` bigint(10) DEFAULT NULL,
  `method` tinyint(1) NOT NULL DEFAULT '0',
  `value` decimal(10,5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courcompaggrmeth_coucr_uix` (`course`,`criteriatype`),
  KEY `mdl_courcompaggrmeth_cou_ix` (`course`),
  KEY `mdl_courcompaggrmeth_cri_ix` (`criteriatype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Course completion aggregation methods for criteria';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_completion_aggr_methd`
--

LOCK TABLES `mdl_course_completion_aggr_methd` WRITE;
/*!40000 ALTER TABLE `mdl_course_completion_aggr_methd` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_completion_aggr_methd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_completion_crit_compl`
--

DROP TABLE IF EXISTS `mdl_course_completion_crit_compl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_completion_crit_compl` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `course` bigint(10) NOT NULL DEFAULT '0',
  `criteriaid` bigint(10) NOT NULL DEFAULT '0',
  `gradefinal` decimal(10,5) DEFAULT NULL,
  `unenroled` bigint(10) DEFAULT NULL,
  `timecompleted` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courcompcritcomp_useco_uix` (`userid`,`course`,`criteriaid`),
  KEY `mdl_courcompcritcomp_use_ix` (`userid`),
  KEY `mdl_courcompcritcomp_cou_ix` (`course`),
  KEY `mdl_courcompcritcomp_cri_ix` (`criteriaid`),
  KEY `mdl_courcompcritcomp_tim_ix` (`timecompleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Course completion user records';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_completion_crit_compl`
--

LOCK TABLES `mdl_course_completion_crit_compl` WRITE;
/*!40000 ALTER TABLE `mdl_course_completion_crit_compl` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_completion_crit_compl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_completion_criteria`
--

DROP TABLE IF EXISTS `mdl_course_completion_criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_completion_criteria` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `criteriatype` bigint(10) NOT NULL DEFAULT '0',
  `module` varchar(100) DEFAULT NULL,
  `moduleinstance` bigint(10) DEFAULT NULL,
  `courseinstance` bigint(10) DEFAULT NULL,
  `enrolperiod` bigint(10) DEFAULT NULL,
  `timeend` bigint(10) DEFAULT NULL,
  `gradepass` decimal(10,5) DEFAULT NULL,
  `role` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_courcompcrit_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Course completion criteria';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_completion_criteria`
--

LOCK TABLES `mdl_course_completion_criteria` WRITE;
/*!40000 ALTER TABLE `mdl_course_completion_criteria` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_completion_criteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_completions`
--

DROP TABLE IF EXISTS `mdl_course_completions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_completions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `course` bigint(10) NOT NULL DEFAULT '0',
  `timeenrolled` bigint(10) NOT NULL DEFAULT '0',
  `timestarted` bigint(10) NOT NULL DEFAULT '0',
  `timecompleted` bigint(10) DEFAULT NULL,
  `reaggregate` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courcomp_usecou_uix` (`userid`,`course`),
  KEY `mdl_courcomp_use_ix` (`userid`),
  KEY `mdl_courcomp_cou_ix` (`course`),
  KEY `mdl_courcomp_tim_ix` (`timecompleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Course completion records';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_completions`
--

LOCK TABLES `mdl_course_completions` WRITE;
/*!40000 ALTER TABLE `mdl_course_completions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_completions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_format_options`
--

DROP TABLE IF EXISTS `mdl_course_format_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_format_options` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL,
  `format` varchar(21) NOT NULL DEFAULT '',
  `sectionid` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courformopti_couforsec_uix` (`courseid`,`format`,`sectionid`,`name`),
  KEY `mdl_courformopti_cou_ix` (`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Stores format-specific options for the course or course sect';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_format_options`
--

LOCK TABLES `mdl_course_format_options` WRITE;
/*!40000 ALTER TABLE `mdl_course_format_options` DISABLE KEYS */;
INSERT INTO `mdl_course_format_options` VALUES (1,1,'site',0,'numsections','1'),(2,2,'weeks',0,'numsections','10'),(3,2,'weeks',0,'hiddensections','0'),(4,2,'weeks',0,'coursedisplay','0');
/*!40000 ALTER TABLE `mdl_course_format_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_modules`
--

DROP TABLE IF EXISTS `mdl_course_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_modules` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `module` bigint(10) NOT NULL DEFAULT '0',
  `instance` bigint(10) NOT NULL DEFAULT '0',
  `section` bigint(10) NOT NULL DEFAULT '0',
  `idnumber` varchar(100) DEFAULT NULL,
  `added` bigint(10) NOT NULL DEFAULT '0',
  `score` smallint(4) NOT NULL DEFAULT '0',
  `indent` mediumint(5) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `visibleold` tinyint(1) NOT NULL DEFAULT '1',
  `groupmode` smallint(4) NOT NULL DEFAULT '0',
  `groupingid` bigint(10) NOT NULL DEFAULT '0',
  `groupmembersonly` smallint(4) NOT NULL DEFAULT '0',
  `completion` tinyint(1) NOT NULL DEFAULT '0',
  `completiongradeitemnumber` bigint(10) DEFAULT NULL,
  `completionview` tinyint(1) NOT NULL DEFAULT '0',
  `completionexpected` bigint(10) NOT NULL DEFAULT '0',
  `availablefrom` bigint(10) NOT NULL DEFAULT '0',
  `availableuntil` bigint(10) NOT NULL DEFAULT '0',
  `showavailability` tinyint(1) NOT NULL DEFAULT '0',
  `showdescription` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_courmodu_vis_ix` (`visible`),
  KEY `mdl_courmodu_cou_ix` (`course`),
  KEY `mdl_courmodu_mod_ix` (`module`),
  KEY `mdl_courmodu_ins_ix` (`instance`),
  KEY `mdl_courmodu_idncou_ix` (`idnumber`,`course`),
  KEY `mdl_courmodu_gro_ix` (`groupingid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='course_modules table retrofitted from MySQL';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_modules`
--

LOCK TABLES `mdl_course_modules` WRITE;
/*!40000 ALTER TABLE `mdl_course_modules` DISABLE KEYS */;
INSERT INTO `mdl_course_modules` VALUES (1,2,9,1,1,NULL,1390925721,0,0,1,1,0,0,0,0,NULL,0,0,0,0,0,0);
/*!40000 ALTER TABLE `mdl_course_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_modules_avail_fields`
--

DROP TABLE IF EXISTS `mdl_course_modules_avail_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_modules_avail_fields` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `coursemoduleid` bigint(10) NOT NULL,
  `userfield` varchar(50) DEFAULT NULL,
  `customfieldid` bigint(10) DEFAULT NULL,
  `operator` varchar(20) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_courmoduavaifiel_cou_ix` (`coursemoduleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores user field conditions that affect whether an activity';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_modules_avail_fields`
--

LOCK TABLES `mdl_course_modules_avail_fields` WRITE;
/*!40000 ALTER TABLE `mdl_course_modules_avail_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_modules_avail_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_modules_availability`
--

DROP TABLE IF EXISTS `mdl_course_modules_availability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_modules_availability` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `coursemoduleid` bigint(10) NOT NULL,
  `sourcecmid` bigint(10) DEFAULT NULL,
  `requiredcompletion` tinyint(1) DEFAULT NULL,
  `gradeitemid` bigint(10) DEFAULT NULL,
  `grademin` decimal(10,5) DEFAULT NULL,
  `grademax` decimal(10,5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_courmoduavai_cou_ix` (`coursemoduleid`),
  KEY `mdl_courmoduavai_sou_ix` (`sourcecmid`),
  KEY `mdl_courmoduavai_gra_ix` (`gradeitemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table stores conditions that affect whether a module/activit';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_modules_availability`
--

LOCK TABLES `mdl_course_modules_availability` WRITE;
/*!40000 ALTER TABLE `mdl_course_modules_availability` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_modules_availability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_modules_completion`
--

DROP TABLE IF EXISTS `mdl_course_modules_completion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_modules_completion` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `coursemoduleid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `completionstate` tinyint(1) NOT NULL,
  `viewed` tinyint(1) DEFAULT NULL,
  `timemodified` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_courmoducomp_usecou_uix` (`userid`,`coursemoduleid`),
  KEY `mdl_courmoducomp_cou_ix` (`coursemoduleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the completion state (completed or not completed, etc';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_modules_completion`
--

LOCK TABLES `mdl_course_modules_completion` WRITE;
/*!40000 ALTER TABLE `mdl_course_modules_completion` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_modules_completion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_published`
--

DROP TABLE IF EXISTS `mdl_course_published`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_published` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `huburl` varchar(255) DEFAULT NULL,
  `courseid` bigint(10) NOT NULL,
  `timepublished` bigint(10) NOT NULL,
  `enrollable` tinyint(1) NOT NULL DEFAULT '1',
  `hubcourseid` bigint(10) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `timechecked` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Information about how and when an local courses were publish';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_published`
--

LOCK TABLES `mdl_course_published` WRITE;
/*!40000 ALTER TABLE `mdl_course_published` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_published` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_request`
--

DROP TABLE IF EXISTS `mdl_course_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_request` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(254) NOT NULL DEFAULT '',
  `shortname` varchar(100) NOT NULL DEFAULT '',
  `summary` longtext NOT NULL,
  `summaryformat` tinyint(2) NOT NULL DEFAULT '0',
  `category` bigint(10) NOT NULL DEFAULT '0',
  `reason` longtext NOT NULL,
  `requester` bigint(10) NOT NULL DEFAULT '0',
  `password` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_courrequ_sho_ix` (`shortname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='course requests';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_request`
--

LOCK TABLES `mdl_course_request` WRITE;
/*!40000 ALTER TABLE `mdl_course_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_sections`
--

DROP TABLE IF EXISTS `mdl_course_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_sections` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `section` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `summary` longtext,
  `summaryformat` tinyint(2) NOT NULL DEFAULT '0',
  `sequence` longtext,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `availablefrom` bigint(10) NOT NULL DEFAULT '0',
  `availableuntil` bigint(10) NOT NULL DEFAULT '0',
  `showavailability` tinyint(1) NOT NULL DEFAULT '0',
  `groupingid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_coursect_cousec_uix` (`course`,`section`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='to define the sections for each course';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_sections`
--

LOCK TABLES `mdl_course_sections` WRITE;
/*!40000 ALTER TABLE `mdl_course_sections` DISABLE KEYS */;
INSERT INTO `mdl_course_sections` VALUES (1,2,0,NULL,'',1,'1',1,0,0,0,0),(2,2,1,NULL,'',1,'',1,0,0,0,0),(3,2,2,NULL,'',1,'',1,0,0,0,0),(4,2,3,NULL,'',1,'',1,0,0,0,0),(5,2,4,NULL,'',1,'',1,0,0,0,0),(6,2,5,NULL,'',1,'',1,0,0,0,0),(7,2,6,NULL,'',1,'',1,0,0,0,0),(8,2,7,NULL,'',1,'',1,0,0,0,0),(9,2,8,NULL,'',1,'',1,0,0,0,0),(10,2,9,NULL,'',1,'',1,0,0,0,0),(11,2,10,NULL,'',1,'',1,0,0,0,0);
/*!40000 ALTER TABLE `mdl_course_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_sections_avail_fields`
--

DROP TABLE IF EXISTS `mdl_course_sections_avail_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_sections_avail_fields` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `coursesectionid` bigint(10) NOT NULL,
  `userfield` varchar(50) DEFAULT NULL,
  `customfieldid` bigint(10) DEFAULT NULL,
  `operator` varchar(20) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_coursectavaifiel_cou_ix` (`coursesectionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores user field conditions that affect whether an activity';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_sections_avail_fields`
--

LOCK TABLES `mdl_course_sections_avail_fields` WRITE;
/*!40000 ALTER TABLE `mdl_course_sections_avail_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_sections_avail_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_course_sections_availability`
--

DROP TABLE IF EXISTS `mdl_course_sections_availability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_course_sections_availability` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `coursesectionid` bigint(10) NOT NULL,
  `sourcecmid` bigint(10) DEFAULT NULL,
  `requiredcompletion` tinyint(1) DEFAULT NULL,
  `gradeitemid` bigint(10) DEFAULT NULL,
  `grademin` decimal(10,5) DEFAULT NULL,
  `grademax` decimal(10,5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_coursectavai_cou_ix` (`coursesectionid`),
  KEY `mdl_coursectavai_sou_ix` (`sourcecmid`),
  KEY `mdl_coursectavai_gra_ix` (`gradeitemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Completion or grade conditions that affect if a section is c';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_course_sections_availability`
--

LOCK TABLES `mdl_course_sections_availability` WRITE;
/*!40000 ALTER TABLE `mdl_course_sections_availability` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_course_sections_availability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_data`
--

DROP TABLE IF EXISTS `mdl_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_data` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `comments` smallint(4) NOT NULL DEFAULT '0',
  `timeavailablefrom` bigint(10) NOT NULL DEFAULT '0',
  `timeavailableto` bigint(10) NOT NULL DEFAULT '0',
  `timeviewfrom` bigint(10) NOT NULL DEFAULT '0',
  `timeviewto` bigint(10) NOT NULL DEFAULT '0',
  `requiredentries` int(8) NOT NULL DEFAULT '0',
  `requiredentriestoview` int(8) NOT NULL DEFAULT '0',
  `maxentries` int(8) NOT NULL DEFAULT '0',
  `rssarticles` smallint(4) NOT NULL DEFAULT '0',
  `singletemplate` longtext,
  `listtemplate` longtext,
  `listtemplateheader` longtext,
  `listtemplatefooter` longtext,
  `addtemplate` longtext,
  `rsstemplate` longtext,
  `rsstitletemplate` longtext,
  `csstemplate` longtext,
  `jstemplate` longtext,
  `asearchtemplate` longtext,
  `approval` smallint(4) NOT NULL DEFAULT '0',
  `scale` bigint(10) NOT NULL DEFAULT '0',
  `assessed` bigint(10) NOT NULL DEFAULT '0',
  `assesstimestart` bigint(10) NOT NULL DEFAULT '0',
  `assesstimefinish` bigint(10) NOT NULL DEFAULT '0',
  `defaultsort` bigint(10) NOT NULL DEFAULT '0',
  `defaultsortdir` smallint(4) NOT NULL DEFAULT '0',
  `editany` smallint(4) NOT NULL DEFAULT '0',
  `notification` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_data_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='all database activities';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_data`
--

LOCK TABLES `mdl_data` WRITE;
/*!40000 ALTER TABLE `mdl_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_data_content`
--

DROP TABLE IF EXISTS `mdl_data_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_data_content` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `fieldid` bigint(10) NOT NULL DEFAULT '0',
  `recordid` bigint(10) NOT NULL DEFAULT '0',
  `content` longtext,
  `content1` longtext,
  `content2` longtext,
  `content3` longtext,
  `content4` longtext,
  PRIMARY KEY (`id`),
  KEY `mdl_datacont_rec_ix` (`recordid`),
  KEY `mdl_datacont_fie_ix` (`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='the content introduced in each record/fields';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_data_content`
--

LOCK TABLES `mdl_data_content` WRITE;
/*!40000 ALTER TABLE `mdl_data_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_data_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_data_fields`
--

DROP TABLE IF EXISTS `mdl_data_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_data_fields` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `dataid` bigint(10) NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `param1` longtext,
  `param2` longtext,
  `param3` longtext,
  `param4` longtext,
  `param5` longtext,
  `param6` longtext,
  `param7` longtext,
  `param8` longtext,
  `param9` longtext,
  `param10` longtext,
  PRIMARY KEY (`id`),
  KEY `mdl_datafiel_typdat_ix` (`type`,`dataid`),
  KEY `mdl_datafiel_dat_ix` (`dataid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='every field available';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_data_fields`
--

LOCK TABLES `mdl_data_fields` WRITE;
/*!40000 ALTER TABLE `mdl_data_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_data_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_data_records`
--

DROP TABLE IF EXISTS `mdl_data_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_data_records` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `groupid` bigint(10) NOT NULL DEFAULT '0',
  `dataid` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `approved` smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_datareco_dat_ix` (`dataid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='every record introduced';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_data_records`
--

LOCK TABLES `mdl_data_records` WRITE;
/*!40000 ALTER TABLE `mdl_data_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_data_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_enrol`
--

DROP TABLE IF EXISTS `mdl_enrol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_enrol` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `enrol` varchar(20) NOT NULL DEFAULT '',
  `status` bigint(10) NOT NULL DEFAULT '0',
  `courseid` bigint(10) NOT NULL,
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `enrolperiod` bigint(10) DEFAULT '0',
  `enrolstartdate` bigint(10) DEFAULT '0',
  `enrolenddate` bigint(10) DEFAULT '0',
  `expirynotify` tinyint(1) DEFAULT '0',
  `expirythreshold` bigint(10) DEFAULT '0',
  `notifyall` tinyint(1) DEFAULT '0',
  `password` varchar(50) DEFAULT NULL,
  `cost` varchar(20) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `roleid` bigint(10) DEFAULT '0',
  `customint1` bigint(10) DEFAULT NULL,
  `customint2` bigint(10) DEFAULT NULL,
  `customint3` bigint(10) DEFAULT NULL,
  `customint4` bigint(10) DEFAULT NULL,
  `customint5` bigint(10) DEFAULT NULL,
  `customint6` bigint(10) DEFAULT NULL,
  `customint7` bigint(10) DEFAULT NULL,
  `customint8` bigint(10) DEFAULT NULL,
  `customchar1` varchar(255) DEFAULT NULL,
  `customchar2` varchar(255) DEFAULT NULL,
  `customchar3` varchar(1333) DEFAULT NULL,
  `customdec1` decimal(12,7) DEFAULT NULL,
  `customdec2` decimal(12,7) DEFAULT NULL,
  `customtext1` longtext,
  `customtext2` longtext,
  `customtext3` longtext,
  `customtext4` longtext,
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_enro_enr_ix` (`enrol`),
  KEY `mdl_enro_cou_ix` (`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Instances of enrolment plugins used in courses, fields marke';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_enrol`
--

LOCK TABLES `mdl_enrol` WRITE;
/*!40000 ALTER TABLE `mdl_enrol` DISABLE KEYS */;
INSERT INTO `mdl_enrol` VALUES (1,'manual',0,2,0,NULL,0,0,0,0,86400,0,NULL,NULL,NULL,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1390504615,1390504615),(2,'guest',1,2,1,NULL,0,0,0,0,0,0,'',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1390504615,1390504615),(3,'self',1,2,2,NULL,0,0,0,0,86400,0,NULL,NULL,NULL,5,0,0,0,1,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1390504616,1390504616);
/*!40000 ALTER TABLE `mdl_enrol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_enrol_flatfile`
--

DROP TABLE IF EXISTS `mdl_enrol_flatfile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_enrol_flatfile` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `action` varchar(30) NOT NULL DEFAULT '',
  `roleid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `courseid` bigint(10) NOT NULL,
  `timestart` bigint(10) NOT NULL DEFAULT '0',
  `timeend` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_enroflat_cou_ix` (`courseid`),
  KEY `mdl_enroflat_use_ix` (`userid`),
  KEY `mdl_enroflat_rol_ix` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='enrol_flatfile table retrofitted from MySQL';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_enrol_flatfile`
--

LOCK TABLES `mdl_enrol_flatfile` WRITE;
/*!40000 ALTER TABLE `mdl_enrol_flatfile` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_enrol_flatfile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_enrol_paypal`
--

DROP TABLE IF EXISTS `mdl_enrol_paypal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_enrol_paypal` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `business` varchar(255) NOT NULL DEFAULT '',
  `receiver_email` varchar(255) NOT NULL DEFAULT '',
  `receiver_id` varchar(255) NOT NULL DEFAULT '',
  `item_name` varchar(255) NOT NULL DEFAULT '',
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `instanceid` bigint(10) NOT NULL DEFAULT '0',
  `memo` varchar(255) NOT NULL DEFAULT '',
  `tax` varchar(255) NOT NULL DEFAULT '',
  `option_name1` varchar(255) NOT NULL DEFAULT '',
  `option_selection1_x` varchar(255) NOT NULL DEFAULT '',
  `option_name2` varchar(255) NOT NULL DEFAULT '',
  `option_selection2_x` varchar(255) NOT NULL DEFAULT '',
  `payment_status` varchar(255) NOT NULL DEFAULT '',
  `pending_reason` varchar(255) NOT NULL DEFAULT '',
  `reason_code` varchar(30) NOT NULL DEFAULT '',
  `txn_id` varchar(255) NOT NULL DEFAULT '',
  `parent_txn_id` varchar(255) NOT NULL DEFAULT '',
  `payment_type` varchar(30) NOT NULL DEFAULT '',
  `timeupdated` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds all known information about PayPal transactions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_enrol_paypal`
--

LOCK TABLES `mdl_enrol_paypal` WRITE;
/*!40000 ALTER TABLE `mdl_enrol_paypal` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_enrol_paypal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_event`
--

DROP TABLE IF EXISTS `mdl_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_event` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `description` longtext NOT NULL,
  `format` smallint(4) NOT NULL DEFAULT '0',
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `groupid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `repeatid` bigint(10) NOT NULL DEFAULT '0',
  `modulename` varchar(20) NOT NULL DEFAULT '',
  `instance` bigint(10) NOT NULL DEFAULT '0',
  `eventtype` varchar(20) NOT NULL DEFAULT '',
  `timestart` bigint(10) NOT NULL DEFAULT '0',
  `timeduration` bigint(10) NOT NULL DEFAULT '0',
  `visible` smallint(4) NOT NULL DEFAULT '1',
  `uuid` varchar(255) NOT NULL DEFAULT '',
  `sequence` bigint(10) NOT NULL DEFAULT '1',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `subscriptionid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_even_cou_ix` (`courseid`),
  KEY `mdl_even_use_ix` (`userid`),
  KEY `mdl_even_tim_ix` (`timestart`),
  KEY `mdl_even_tim2_ix` (`timeduration`),
  KEY `mdl_even_grocouvisuse_ix` (`groupid`,`courseid`,`visible`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='For everything with a time associated to it';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_event`
--

LOCK TABLES `mdl_event` WRITE;
/*!40000 ALTER TABLE `mdl_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_event_subscriptions`
--

DROP TABLE IF EXISTS `mdl_event_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_event_subscriptions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '',
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `groupid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `eventtype` varchar(20) NOT NULL DEFAULT '',
  `pollinterval` bigint(10) NOT NULL DEFAULT '0',
  `lastupdated` bigint(10) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tracks subscriptions to remote calendars.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_event_subscriptions`
--

LOCK TABLES `mdl_event_subscriptions` WRITE;
/*!40000 ALTER TABLE `mdl_event_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_event_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_events_handlers`
--

DROP TABLE IF EXISTS `mdl_events_handlers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_events_handlers` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `eventname` varchar(166) NOT NULL DEFAULT '',
  `component` varchar(166) NOT NULL DEFAULT '',
  `handlerfile` varchar(255) NOT NULL DEFAULT '',
  `handlerfunction` longtext,
  `schedule` varchar(255) DEFAULT NULL,
  `status` bigint(10) NOT NULL DEFAULT '0',
  `internal` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_evenhand_evecom_uix` (`eventname`,`component`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='This table is for storing which components requests what typ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_events_handlers`
--

LOCK TABLES `mdl_events_handlers` WRITE;
/*!40000 ALTER TABLE `mdl_events_handlers` DISABLE KEYS */;
INSERT INTO `mdl_events_handlers` VALUES (1,'portfolio_send','moodle','/lib/portfoliolib.php','s:22:\"portfolio_handle_event\";','cron',0,0);
/*!40000 ALTER TABLE `mdl_events_handlers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_events_queue`
--

DROP TABLE IF EXISTS `mdl_events_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_events_queue` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `eventdata` longtext NOT NULL,
  `stackdump` longtext,
  `userid` bigint(10) DEFAULT NULL,
  `timecreated` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_evenqueu_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table is for storing queued events. It stores only one ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_events_queue`
--

LOCK TABLES `mdl_events_queue` WRITE;
/*!40000 ALTER TABLE `mdl_events_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_events_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_events_queue_handlers`
--

DROP TABLE IF EXISTS `mdl_events_queue_handlers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_events_queue_handlers` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `queuedeventid` bigint(10) NOT NULL,
  `handlerid` bigint(10) NOT NULL,
  `status` bigint(10) DEFAULT NULL,
  `errormessage` longtext,
  `timemodified` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_evenqueuhand_que_ix` (`queuedeventid`),
  KEY `mdl_evenqueuhand_han_ix` (`handlerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This is the list of queued handlers for processing. The even';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_events_queue_handlers`
--

LOCK TABLES `mdl_events_queue_handlers` WRITE;
/*!40000 ALTER TABLE `mdl_events_queue_handlers` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_events_queue_handlers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_external_functions`
--

DROP TABLE IF EXISTS `mdl_external_functions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_external_functions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `classname` varchar(100) NOT NULL DEFAULT '',
  `methodname` varchar(100) NOT NULL DEFAULT '',
  `classpath` varchar(255) DEFAULT NULL,
  `component` varchar(100) NOT NULL DEFAULT '',
  `capabilities` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_extefunc_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8 COMMENT='list of all external functions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_external_functions`
--

LOCK TABLES `mdl_external_functions` WRITE;
/*!40000 ALTER TABLE `mdl_external_functions` DISABLE KEYS */;
INSERT INTO `mdl_external_functions` VALUES (1,'core_cohort_create_cohorts','core_cohort_external','create_cohorts','cohort/externallib.php','moodle','moodle/cohort:manage'),(2,'core_cohort_delete_cohorts','core_cohort_external','delete_cohorts','cohort/externallib.php','moodle','moodle/cohort:manage'),(3,'core_cohort_get_cohorts','core_cohort_external','get_cohorts','cohort/externallib.php','moodle','moodle/cohort:view'),(4,'core_cohort_update_cohorts','core_cohort_external','update_cohorts','cohort/externallib.php','moodle','moodle/cohort:manage'),(5,'core_cohort_add_cohort_members','core_cohort_external','add_cohort_members','cohort/externallib.php','moodle','moodle/cohort:assign'),(6,'core_cohort_delete_cohort_members','core_cohort_external','delete_cohort_members','cohort/externallib.php','moodle','moodle/cohort:assign'),(7,'core_cohort_get_cohort_members','core_cohort_external','get_cohort_members','cohort/externallib.php','moodle','moodle/cohort:view'),(8,'moodle_group_create_groups','core_group_external','create_groups','group/externallib.php','moodle','moodle/course:managegroups'),(9,'core_group_create_groups','core_group_external','create_groups','group/externallib.php','moodle','moodle/course:managegroups'),(10,'moodle_group_get_groups','core_group_external','get_groups','group/externallib.php','moodle','moodle/course:managegroups'),(11,'core_group_get_groups','core_group_external','get_groups','group/externallib.php','moodle','moodle/course:managegroups'),(12,'moodle_group_get_course_groups','core_group_external','get_course_groups','group/externallib.php','moodle','moodle/course:managegroups'),(13,'core_group_get_course_groups','core_group_external','get_course_groups','group/externallib.php','moodle','moodle/course:managegroups'),(14,'moodle_group_delete_groups','core_group_external','delete_groups','group/externallib.php','moodle','moodle/course:managegroups'),(15,'core_group_delete_groups','core_group_external','delete_groups','group/externallib.php','moodle','moodle/course:managegroups'),(16,'moodle_group_get_groupmembers','core_group_external','get_group_members','group/externallib.php','moodle','moodle/course:managegroups'),(17,'core_group_get_group_members','core_group_external','get_group_members','group/externallib.php','moodle','moodle/course:managegroups'),(18,'moodle_group_add_groupmembers','core_group_external','add_group_members','group/externallib.php','moodle','moodle/course:managegroups'),(19,'core_group_add_group_members','core_group_external','add_group_members','group/externallib.php','moodle','moodle/course:managegroups'),(20,'moodle_group_delete_groupmembers','core_group_external','delete_group_members','group/externallib.php','moodle','moodle/course:managegroups'),(21,'core_group_delete_group_members','core_group_external','delete_group_members','group/externallib.php','moodle','moodle/course:managegroups'),(22,'core_group_create_groupings','core_group_external','create_groupings','group/externallib.php','moodle',''),(23,'core_group_update_groupings','core_group_external','update_groupings','group/externallib.php','moodle',''),(24,'core_group_get_groupings','core_group_external','get_groupings','group/externallib.php','moodle',''),(25,'core_group_get_course_groupings','core_group_external','get_course_groupings','group/externallib.php','moodle',''),(26,'core_group_delete_groupings','core_group_external','delete_groupings','group/externallib.php','moodle',''),(27,'core_group_assign_grouping','core_group_external','assign_grouping','group/externallib.php','moodle',''),(28,'core_group_unassign_grouping','core_group_external','unassign_grouping','group/externallib.php','moodle',''),(29,'moodle_file_get_files','core_files_external','get_files','files/externallib.php','moodle',''),(30,'core_files_get_files','core_files_external','get_files','files/externallib.php','moodle',''),(31,'moodle_file_upload','core_files_external','upload','files/externallib.php','moodle',''),(32,'core_files_upload','core_files_external','upload','files/externallib.php','moodle',''),(33,'moodle_user_create_users','core_user_external','create_users','user/externallib.php','moodle','moodle/user:create'),(34,'core_user_create_users','core_user_external','create_users','user/externallib.php','moodle','moodle/user:create'),(35,'core_user_get_users','core_user_external','get_users','user/externallib.php','moodle','moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update'),(36,'moodle_user_get_users_by_id','core_user_external','get_users_by_id','user/externallib.php','moodle','moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update'),(37,'core_user_get_users_by_field','core_user_external','get_users_by_field','user/externallib.php','moodle','moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update'),(38,'core_user_get_users_by_id','core_user_external','get_users_by_id','user/externallib.php','moodle','moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update'),(39,'moodle_user_get_users_by_courseid','core_enrol_external','get_enrolled_users','enrol/externallib.php','moodle','moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups'),(40,'moodle_user_get_course_participants_by_id','core_user_external','get_course_user_profiles','user/externallib.php','moodle','moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups'),(41,'core_user_get_course_user_profiles','core_user_external','get_course_user_profiles','user/externallib.php','moodle','moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups'),(42,'moodle_user_delete_users','core_user_external','delete_users','user/externallib.php','moodle','moodle/user:delete'),(43,'core_user_delete_users','core_user_external','delete_users','user/externallib.php','moodle','moodle/user:delete'),(44,'moodle_user_update_users','core_user_external','update_users','user/externallib.php','moodle','moodle/user:update'),(45,'core_user_update_users','core_user_external','update_users','user/externallib.php','moodle','moodle/user:update'),(46,'core_user_add_user_device','core_user_external','add_user_device','user/externallib.php','moodle',''),(47,'core_enrol_get_enrolled_users_with_capability','core_enrol_external','get_enrolled_users_with_capability','enrol/externallib.php','moodle',''),(48,'moodle_enrol_get_enrolled_users','moodle_enrol_external','get_enrolled_users','enrol/externallib.php','moodle','moodle/site:viewparticipants, moodle/course:viewparticipants,\n            moodle/role:review, moodle/site:accessallgroups, moodle/course:enrolreview'),(49,'core_enrol_get_enrolled_users','core_enrol_external','get_enrolled_users','enrol/externallib.php','moodle','moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups'),(50,'moodle_enrol_get_users_courses','core_enrol_external','get_users_courses','enrol/externallib.php','moodle','moodle/course:viewparticipants'),(51,'core_enrol_get_users_courses','core_enrol_external','get_users_courses','enrol/externallib.php','moodle','moodle/course:viewparticipants'),(52,'core_enrol_get_course_enrolment_methods','core_enrol_external','get_course_enrolment_methods','enrol/externallib.php','moodle',''),(53,'moodle_role_assign','core_role_external','assign_roles','enrol/externallib.php','moodle','moodle/role:assign'),(54,'core_role_assign_roles','core_role_external','assign_roles','enrol/externallib.php','moodle','moodle/role:assign'),(55,'moodle_role_unassign','core_role_external','unassign_roles','enrol/externallib.php','moodle','moodle/role:assign'),(56,'core_role_unassign_roles','core_role_external','unassign_roles','enrol/externallib.php','moodle','moodle/role:assign'),(57,'core_course_get_contents','core_course_external','get_course_contents','course/externallib.php','moodle','moodle/course:update,moodle/course:viewhiddencourses'),(58,'moodle_course_get_courses','core_course_external','get_courses','course/externallib.php','moodle','moodle/course:view,moodle/course:update,moodle/course:viewhiddencourses'),(59,'core_course_get_courses','core_course_external','get_courses','course/externallib.php','moodle','moodle/course:view,moodle/course:update,moodle/course:viewhiddencourses'),(60,'moodle_course_create_courses','core_course_external','create_courses','course/externallib.php','moodle','moodle/course:create,moodle/course:visibility'),(61,'core_course_create_courses','core_course_external','create_courses','course/externallib.php','moodle','moodle/course:create,moodle/course:visibility'),(62,'core_course_delete_courses','core_course_external','delete_courses','course/externallib.php','moodle','moodle/course:delete'),(63,'core_course_delete_modules','core_course_external','delete_modules','course/externallib.php','moodle','moodle/course:manageactivities'),(64,'core_course_duplicate_course','core_course_external','duplicate_course','course/externallib.php','moodle','moodle/backup:backupcourse,moodle/restore:restorecourse,moodle/course:create'),(65,'core_course_update_courses','core_course_external','update_courses','course/externallib.php','moodle','moodle/course:update,moodle/course:changecategory,moodle/course:changefullname,moodle/course:changeshortname,moodle/course:changeidnumber,moodle/course:changesummary,moodle/course:visibility'),(66,'core_course_get_categories','core_course_external','get_categories','course/externallib.php','moodle','moodle/category:viewhiddencategories'),(67,'core_course_create_categories','core_course_external','create_categories','course/externallib.php','moodle','moodle/category:manage'),(68,'core_course_update_categories','core_course_external','update_categories','course/externallib.php','moodle','moodle/category:manage'),(69,'core_course_delete_categories','core_course_external','delete_categories','course/externallib.php','moodle','moodle/category:manage'),(70,'core_course_import_course','core_course_external','import_course','course/externallib.php','moodle','moodle/backup:backuptargetimport, moodle/restore:restoretargetimport'),(71,'moodle_message_send_instantmessages','core_message_external','send_instant_messages','message/externallib.php','moodle','moodle/site:sendmessage'),(72,'core_message_send_instant_messages','core_message_external','send_instant_messages','message/externallib.php','moodle','moodle/site:sendmessage'),(73,'core_message_create_contacts','core_message_external','create_contacts','message/externallib.php','moodle',''),(74,'core_message_delete_contacts','core_message_external','delete_contacts','message/externallib.php','moodle',''),(75,'core_message_block_contacts','core_message_external','block_contacts','message/externallib.php','moodle',''),(76,'core_message_unblock_contacts','core_message_external','unblock_contacts','message/externallib.php','moodle',''),(77,'core_message_get_contacts','core_message_external','get_contacts','message/externallib.php','moodle',''),(78,'core_message_search_contacts','core_message_external','search_contacts','message/externallib.php','moodle',''),(79,'moodle_notes_create_notes','core_notes_external','create_notes','notes/externallib.php','moodle','moodle/notes:manage'),(80,'core_notes_create_notes','core_notes_external','create_notes','notes/externallib.php','moodle','moodle/notes:manage'),(81,'core_notes_delete_notes','core_notes_external','delete_notes','notes/externallib.php','moodle','moodle/notes:manage'),(82,'core_notes_get_notes','core_notes_external','get_notes','notes/externallib.php','moodle','moodle/notes:view'),(83,'core_notes_update_notes','core_notes_external','update_notes','notes/externallib.php','moodle','moodle/notes:manage'),(84,'core_grading_get_definitions','core_grading_external','get_definitions','grade/externallib.php','moodle',''),(85,'core_grade_get_definitions','core_grade_external','get_definitions','grade/externallib.php','moodle',''),(86,'core_grading_get_gradingform_instances','core_grading_external','get_gradingform_instances','grade/externallib.php','moodle',''),(87,'moodle_webservice_get_siteinfo','core_webservice_external','get_site_info','webservice/externallib.php','moodle',''),(88,'core_webservice_get_site_info','core_webservice_external','get_site_info','webservice/externallib.php','moodle',''),(89,'core_get_string','core_external','get_string','lib/external/externallib.php','moodle',''),(90,'core_get_strings','core_external','get_strings','lib/external/externallib.php','moodle',''),(91,'core_get_component_strings','core_external','get_component_strings','lib/external/externallib.php','moodle',''),(92,'core_calendar_delete_calendar_events','core_calendar_external','delete_calendar_events','calendar/externallib.php','moodle','moodle/calendar:manageentries'),(93,'core_calendar_get_calendar_events','core_calendar_external','get_calendar_events','calendar/externallib.php','moodle','moodle/calendar:manageentries'),(94,'core_calendar_create_calendar_events','core_calendar_external','create_calendar_events','calendar/externallib.php','moodle','moodle/calendar:manageentries'),(95,'mod_assign_get_grades','mod_assign_external','get_grades','mod/assign/externallib.php','mod_assign',''),(96,'mod_assign_get_assignments','mod_assign_external','get_assignments','mod/assign/externallib.php','mod_assign',''),(97,'mod_assign_get_submissions','mod_assign_external','get_submissions','mod/assign/externallib.php','mod_assign',''),(98,'mod_assign_get_user_flags','mod_assign_external','get_user_flags','mod/assign/externallib.php','mod_assign',''),(99,'mod_assign_set_user_flags','mod_assign_external','set_user_flags','mod/assign/externallib.php','mod_assign','mod/assign:grade'),(100,'mod_assign_get_user_mappings','mod_assign_external','get_user_mappings','mod/assign/externallib.php','mod_assign',''),(101,'mod_assign_revert_submissions_to_draft','mod_assign_external','revert_submissions_to_draft','mod/assign/externallib.php','mod_assign',''),(102,'mod_assign_lock_submissions','mod_assign_external','lock_submissions','mod/assign/externallib.php','mod_assign',''),(103,'mod_assign_unlock_submissions','mod_assign_external','unlock_submissions','mod/assign/externallib.php','mod_assign',''),(104,'mod_assign_save_submission','mod_assign_external','save_submission','mod/assign/externallib.php','mod_assign',''),(105,'mod_assign_submit_for_grading','mod_assign_external','submit_for_grading','mod/assign/externallib.php','mod_assign',''),(106,'mod_assign_save_grade','mod_assign_external','save_grade','mod/assign/externallib.php','mod_assign',''),(107,'mod_assign_save_user_extensions','mod_assign_external','save_user_extensions','mod/assign/externallib.php','mod_assign',''),(108,'mod_assign_reveal_identities','mod_assign_external','reveal_identities','mod/assign/externallib.php','mod_assign',''),(109,'mod_forum_get_forums_by_courses','mod_forum_external','get_forums_by_courses','mod/forum/externallib.php','mod_forum','mod/forum:viewdiscussion'),(110,'mod_forum_get_forum_discussions','mod_forum_external','get_forum_discussions','mod/forum/externallib.php','mod_forum','mod/forum:viewdiscussion, mod/forum:viewqandawithoutposting'),(111,'moodle_enrol_manual_enrol_users','enrol_manual_external','enrol_users','enrol/manual/externallib.php','enrol_manual','enrol/manual:enrol'),(112,'enrol_manual_enrol_users','enrol_manual_external','enrol_users','enrol/manual/externallib.php','enrol_manual','enrol/manual:enrol'),(113,'enrol_self_get_instance_info','enrol_self_external','get_instance_info','enrol/self/externallib.php','enrol_self','');
/*!40000 ALTER TABLE `mdl_external_functions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_external_services`
--

DROP TABLE IF EXISTS `mdl_external_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_external_services` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL,
  `requiredcapability` varchar(150) DEFAULT NULL,
  `restrictedusers` tinyint(1) NOT NULL,
  `component` varchar(100) DEFAULT NULL,
  `timecreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) DEFAULT NULL,
  `shortname` varchar(255) DEFAULT NULL,
  `downloadfiles` tinyint(1) NOT NULL DEFAULT '0',
  `uploadfiles` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_exteserv_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='built in and custom external services';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_external_services`
--

LOCK TABLES `mdl_external_services` WRITE;
/*!40000 ALTER TABLE `mdl_external_services` DISABLE KEYS */;
INSERT INTO `mdl_external_services` VALUES (1,'Moodle mobile web service',0,NULL,0,'moodle',1390420019,NULL,'moodle_mobile_app',1,1);
/*!40000 ALTER TABLE `mdl_external_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_external_services_functions`
--

DROP TABLE IF EXISTS `mdl_external_services_functions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_external_services_functions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `externalserviceid` bigint(10) NOT NULL,
  `functionname` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_exteservfunc_ext_ix` (`externalserviceid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='lists functions available in each service group';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_external_services_functions`
--

LOCK TABLES `mdl_external_services_functions` WRITE;
/*!40000 ALTER TABLE `mdl_external_services_functions` DISABLE KEYS */;
INSERT INTO `mdl_external_services_functions` VALUES (1,1,'moodle_enrol_get_users_courses'),(2,1,'moodle_enrol_get_enrolled_users'),(3,1,'moodle_user_get_users_by_id'),(4,1,'moodle_webservice_get_siteinfo'),(5,1,'moodle_notes_create_notes'),(6,1,'moodle_user_get_course_participants_by_id'),(7,1,'moodle_user_get_users_by_courseid'),(8,1,'moodle_message_send_instantmessages'),(9,1,'core_course_get_contents'),(10,1,'core_get_component_strings'),(11,1,'core_user_add_user_device');
/*!40000 ALTER TABLE `mdl_external_services_functions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_external_services_users`
--

DROP TABLE IF EXISTS `mdl_external_services_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_external_services_users` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `externalserviceid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `iprestriction` varchar(255) DEFAULT NULL,
  `validuntil` bigint(10) DEFAULT NULL,
  `timecreated` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_exteservuser_ext_ix` (`externalserviceid`),
  KEY `mdl_exteservuser_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='users allowed to use services with restricted users flag';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_external_services_users`
--

LOCK TABLES `mdl_external_services_users` WRITE;
/*!40000 ALTER TABLE `mdl_external_services_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_external_services_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_external_tokens`
--

DROP TABLE IF EXISTS `mdl_external_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_external_tokens` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `token` varchar(128) NOT NULL DEFAULT '',
  `tokentype` smallint(4) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `externalserviceid` bigint(10) NOT NULL,
  `sid` varchar(128) DEFAULT NULL,
  `contextid` bigint(10) NOT NULL,
  `creatorid` bigint(10) NOT NULL DEFAULT '1',
  `iprestriction` varchar(255) DEFAULT NULL,
  `validuntil` bigint(10) DEFAULT NULL,
  `timecreated` bigint(10) NOT NULL,
  `lastaccess` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_extetoke_use_ix` (`userid`),
  KEY `mdl_extetoke_ext_ix` (`externalserviceid`),
  KEY `mdl_extetoke_con_ix` (`contextid`),
  KEY `mdl_extetoke_cre_ix` (`creatorid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Security tokens for accessing of external services';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_external_tokens`
--

LOCK TABLES `mdl_external_tokens` WRITE;
/*!40000 ALTER TABLE `mdl_external_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_external_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_feedback`
--

DROP TABLE IF EXISTS `mdl_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_feedback` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `anonymous` tinyint(1) NOT NULL DEFAULT '1',
  `email_notification` tinyint(1) NOT NULL DEFAULT '1',
  `multiple_submit` tinyint(1) NOT NULL DEFAULT '1',
  `autonumbering` tinyint(1) NOT NULL DEFAULT '1',
  `site_after_submit` varchar(255) NOT NULL DEFAULT '',
  `page_after_submit` longtext NOT NULL,
  `page_after_submitformat` tinyint(2) NOT NULL DEFAULT '0',
  `publish_stats` tinyint(1) NOT NULL DEFAULT '0',
  `timeopen` bigint(10) NOT NULL DEFAULT '0',
  `timeclose` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `completionsubmit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_feed_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='all feedbacks';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_feedback`
--

LOCK TABLES `mdl_feedback` WRITE;
/*!40000 ALTER TABLE `mdl_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_feedback_completed`
--

DROP TABLE IF EXISTS `mdl_feedback_completed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_feedback_completed` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `feedback` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `random_response` bigint(10) NOT NULL DEFAULT '0',
  `anonymous_response` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_feedcomp_use_ix` (`userid`),
  KEY `mdl_feedcomp_fee_ix` (`feedback`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='filled out feedback';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_feedback_completed`
--

LOCK TABLES `mdl_feedback_completed` WRITE;
/*!40000 ALTER TABLE `mdl_feedback_completed` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_feedback_completed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_feedback_completedtmp`
--

DROP TABLE IF EXISTS `mdl_feedback_completedtmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_feedback_completedtmp` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `feedback` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `guestid` varchar(255) NOT NULL DEFAULT '',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `random_response` bigint(10) NOT NULL DEFAULT '0',
  `anonymous_response` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_feedcomp_use2_ix` (`userid`),
  KEY `mdl_feedcomp_fee2_ix` (`feedback`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='filled out feedback';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_feedback_completedtmp`
--

LOCK TABLES `mdl_feedback_completedtmp` WRITE;
/*!40000 ALTER TABLE `mdl_feedback_completedtmp` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_feedback_completedtmp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_feedback_item`
--

DROP TABLE IF EXISTS `mdl_feedback_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_feedback_item` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `feedback` bigint(10) NOT NULL DEFAULT '0',
  `template` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) NOT NULL DEFAULT '',
  `presentation` longtext NOT NULL,
  `typ` varchar(255) NOT NULL DEFAULT '',
  `hasvalue` tinyint(1) NOT NULL DEFAULT '0',
  `position` smallint(3) NOT NULL DEFAULT '0',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `dependitem` bigint(10) NOT NULL DEFAULT '0',
  `dependvalue` varchar(255) NOT NULL DEFAULT '',
  `options` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_feeditem_fee_ix` (`feedback`),
  KEY `mdl_feeditem_tem_ix` (`template`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='feedback_items';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_feedback_item`
--

LOCK TABLES `mdl_feedback_item` WRITE;
/*!40000 ALTER TABLE `mdl_feedback_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_feedback_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_feedback_sitecourse_map`
--

DROP TABLE IF EXISTS `mdl_feedback_sitecourse_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_feedback_sitecourse_map` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `feedbackid` bigint(10) NOT NULL DEFAULT '0',
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_feedsitemap_cou_ix` (`courseid`),
  KEY `mdl_feedsitemap_fee_ix` (`feedbackid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='feedback sitecourse map';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_feedback_sitecourse_map`
--

LOCK TABLES `mdl_feedback_sitecourse_map` WRITE;
/*!40000 ALTER TABLE `mdl_feedback_sitecourse_map` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_feedback_sitecourse_map` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_feedback_template`
--

DROP TABLE IF EXISTS `mdl_feedback_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_feedback_template` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `ispublic` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_feedtemp_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='templates of feedbackstructures';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_feedback_template`
--

LOCK TABLES `mdl_feedback_template` WRITE;
/*!40000 ALTER TABLE `mdl_feedback_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_feedback_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_feedback_tracking`
--

DROP TABLE IF EXISTS `mdl_feedback_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_feedback_tracking` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `feedback` bigint(10) NOT NULL DEFAULT '0',
  `completed` bigint(10) NOT NULL DEFAULT '0',
  `tmp_completed` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_feedtrac_use_ix` (`userid`),
  KEY `mdl_feedtrac_fee_ix` (`feedback`),
  KEY `mdl_feedtrac_com_ix` (`completed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='feedback trackingdata';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_feedback_tracking`
--

LOCK TABLES `mdl_feedback_tracking` WRITE;
/*!40000 ALTER TABLE `mdl_feedback_tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_feedback_tracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_feedback_value`
--

DROP TABLE IF EXISTS `mdl_feedback_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_feedback_value` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course_id` bigint(10) NOT NULL DEFAULT '0',
  `item` bigint(10) NOT NULL DEFAULT '0',
  `completed` bigint(10) NOT NULL DEFAULT '0',
  `tmp_completed` bigint(10) NOT NULL DEFAULT '0',
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_feedvalu_cou_ix` (`course_id`),
  KEY `mdl_feedvalu_ite_ix` (`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='values of the completeds';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_feedback_value`
--

LOCK TABLES `mdl_feedback_value` WRITE;
/*!40000 ALTER TABLE `mdl_feedback_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_feedback_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_feedback_valuetmp`
--

DROP TABLE IF EXISTS `mdl_feedback_valuetmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_feedback_valuetmp` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course_id` bigint(10) NOT NULL DEFAULT '0',
  `item` bigint(10) NOT NULL DEFAULT '0',
  `completed` bigint(10) NOT NULL DEFAULT '0',
  `tmp_completed` bigint(10) NOT NULL DEFAULT '0',
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_feedvalu_cou2_ix` (`course_id`),
  KEY `mdl_feedvalu_ite2_ix` (`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='values of the completedstmp';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_feedback_valuetmp`
--

LOCK TABLES `mdl_feedback_valuetmp` WRITE;
/*!40000 ALTER TABLE `mdl_feedback_valuetmp` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_feedback_valuetmp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_files`
--

DROP TABLE IF EXISTS `mdl_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_files` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contenthash` varchar(40) NOT NULL DEFAULT '',
  `pathnamehash` varchar(40) NOT NULL DEFAULT '',
  `contextid` bigint(10) NOT NULL,
  `component` varchar(100) NOT NULL DEFAULT '',
  `filearea` varchar(50) NOT NULL DEFAULT '',
  `itemid` bigint(10) NOT NULL,
  `filepath` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `userid` bigint(10) DEFAULT NULL,
  `filesize` bigint(10) NOT NULL,
  `mimetype` varchar(100) DEFAULT NULL,
  `status` bigint(10) NOT NULL DEFAULT '0',
  `source` longtext,
  `author` varchar(255) DEFAULT NULL,
  `license` varchar(255) DEFAULT NULL,
  `timecreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  `referencefileid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_file_pat_uix` (`pathnamehash`),
  KEY `mdl_file_comfilconite_ix` (`component`,`filearea`,`contextid`,`itemid`),
  KEY `mdl_file_con_ix` (`contenthash`),
  KEY `mdl_file_con2_ix` (`contextid`),
  KEY `mdl_file_use_ix` (`userid`),
  KEY `mdl_file_ref_ix` (`referencefileid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='description of files, content is stored in sha1 file pool';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_files`
--

LOCK TABLES `mdl_files` WRITE;
/*!40000 ALTER TABLE `mdl_files` DISABLE KEYS */;
INSERT INTO `mdl_files` VALUES (1,'fb262df98d67c4e2a5c9802403821e00cf2992af','508e674d49c30d4fde325fe6c7f6fd3d56b247e1',1,'assignfeedback_editpdf','stamps',0,'/','smile.png',2,1600,'image/png',0,NULL,NULL,NULL,1390420853,1390420853,0,NULL),(2,'da39a3ee5e6b4b0d3255bfef95601890afd80709','70b7cdade7b4e27d4e83f0cdaad10d6a3c0cccb5',1,'assignfeedback_editpdf','stamps',0,'/','.',2,0,NULL,0,NULL,NULL,NULL,1390420853,1390420853,0,NULL),(3,'a4f146f120e7e00d21291b924e26aaabe9f4297a','68317eab56c67d32aeaee5acf509a0c4aa828b6b',1,'assignfeedback_editpdf','stamps',0,'/','sad.png',2,1702,'image/png',0,NULL,NULL,NULL,1390420853,1390420853,0,NULL),(4,'33957e31ba9c763a74638b825f0a9154acf475e1','695a55ff780e61c9e59428aa425430b0d6bde53b',1,'assignfeedback_editpdf','stamps',0,'/','tick.png',2,1187,'image/png',0,NULL,NULL,NULL,1390420853,1390420853,0,NULL),(5,'d613d55f37bb76d38d4ffb4b7b83e6c694778c30','373e63af262a9b8466ba8632551520be793c37ff',1,'assignfeedback_editpdf','stamps',0,'/','cross.png',2,1230,'image/png',0,NULL,NULL,NULL,1390420853,1390420853,0,NULL),(6,'d4775cfd5315ab3938742bf4743a390bd887688a','b41e8a14a62aff9751561da0f09c78b2f10e9507',5,'user','draft',482864195,'/','grumpycat.jpg',2,181631,'image/jpeg',0,'O:8:\"stdClass\":1:{s:6:\"source\";s:13:\"grumpycat.jpg\";}','Admin User','public',1390925786,1390925786,0,NULL),(7,'da39a3ee5e6b4b0d3255bfef95601890afd80709','5f43d4a533cd298b5fa5969c60c6fa95581c7682',5,'user','draft',482864195,'/','.',2,0,NULL,0,NULL,NULL,NULL,1390925786,1390925786,0,NULL),(8,'07aaf42fd44fd76623f8478ae7db41d32dab205b','1b057b07c650520799d26522c437380257f3f66d',1,'core','preview',0,'/thumb/','d4775cfd5315ab3938742bf4743a390bd887688a',NULL,16931,'image/png',0,NULL,NULL,NULL,1390925788,1390925788,0,NULL),(9,'da39a3ee5e6b4b0d3255bfef95601890afd80709','74c104d54c05b5f8c633a36da516d37e6c5279e4',1,'core','preview',0,'/thumb/','.',NULL,0,NULL,0,NULL,NULL,NULL,1390925789,1390925789,0,NULL),(10,'da39a3ee5e6b4b0d3255bfef95601890afd80709','884555719c50529b9df662a38619d04b5b11e25c',1,'core','preview',0,'/','.',NULL,0,NULL,0,NULL,NULL,NULL,1390925789,1390925789,0,NULL),(13,'9746eb1ff028736722e888ae8498cae95c4f20a1','1c43628aa186dc380c301b3250a3afbd74e62c2b',5,'user','icon',0,'/','f1.png',NULL,20450,'image/png',0,NULL,NULL,NULL,1390925793,1390925793,0,NULL),(14,'da39a3ee5e6b4b0d3255bfef95601890afd80709','535824e8097c2aef8aa81b506a392b2bc65f58f0',5,'user','icon',0,'/','.',NULL,0,NULL,0,NULL,NULL,NULL,1390925793,1390925793,0,NULL),(15,'6d6f9648253c2888d390cc6b42f40911d7852a53','0e935ee776b28b0546bf5bcd8b81d65a4dd888f8',5,'user','icon',0,'/','f2.png',NULL,3003,'image/png',0,NULL,NULL,NULL,1390925793,1390925793,0,NULL),(16,'8687f4eed476b185785d24a8e63ba04bfd11b4e2','1486473a480bed8006fce8a14aed94a11246a0f7',5,'user','icon',0,'/','f3.png',NULL,396545,'image/png',0,NULL,NULL,NULL,1390925794,1390925794,0,NULL);
/*!40000 ALTER TABLE `mdl_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_files_reference`
--

DROP TABLE IF EXISTS `mdl_files_reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_files_reference` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `repositoryid` bigint(10) NOT NULL,
  `lastsync` bigint(10) DEFAULT NULL,
  `reference` longtext,
  `referencehash` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_filerefe_repref_uix` (`repositoryid`,`referencehash`),
  KEY `mdl_filerefe_rep_ix` (`repositoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Store files references';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_files_reference`
--

LOCK TABLES `mdl_files_reference` WRITE;
/*!40000 ALTER TABLE `mdl_files_reference` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_files_reference` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_filter_active`
--

DROP TABLE IF EXISTS `mdl_filter_active`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_filter_active` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `filter` varchar(32) NOT NULL DEFAULT '',
  `contextid` bigint(10) NOT NULL,
  `active` smallint(4) NOT NULL,
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_filtacti_confil_uix` (`contextid`,`filter`),
  KEY `mdl_filtacti_con_ix` (`contextid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Stores information about which filters are active in which c';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_filter_active`
--

LOCK TABLES `mdl_filter_active` WRITE;
/*!40000 ALTER TABLE `mdl_filter_active` DISABLE KEYS */;
INSERT INTO `mdl_filter_active` VALUES (1,'activitynames',1,1,1),(2,'mediaplugin',1,1,2);
/*!40000 ALTER TABLE `mdl_filter_active` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_filter_config`
--

DROP TABLE IF EXISTS `mdl_filter_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_filter_config` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `filter` varchar(32) NOT NULL DEFAULT '',
  `contextid` bigint(10) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_filtconf_confilnam_uix` (`contextid`,`filter`,`name`),
  KEY `mdl_filtconf_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores per-context configuration settings for filters which ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_filter_config`
--

LOCK TABLES `mdl_filter_config` WRITE;
/*!40000 ALTER TABLE `mdl_filter_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_filter_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_folder`
--

DROP TABLE IF EXISTS `mdl_folder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_folder` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `revision` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `display` smallint(4) NOT NULL DEFAULT '0',
  `showexpanded` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_fold_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='each record is one folder resource';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_folder`
--

LOCK TABLES `mdl_folder` WRITE;
/*!40000 ALTER TABLE `mdl_folder` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_folder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_forum`
--

DROP TABLE IF EXISTS `mdl_forum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_forum` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'general',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `assessed` bigint(10) NOT NULL DEFAULT '0',
  `assesstimestart` bigint(10) NOT NULL DEFAULT '0',
  `assesstimefinish` bigint(10) NOT NULL DEFAULT '0',
  `scale` bigint(10) NOT NULL DEFAULT '0',
  `maxbytes` bigint(10) NOT NULL DEFAULT '0',
  `maxattachments` bigint(10) NOT NULL DEFAULT '1',
  `forcesubscribe` tinyint(1) NOT NULL DEFAULT '0',
  `trackingtype` tinyint(2) NOT NULL DEFAULT '1',
  `rsstype` tinyint(2) NOT NULL DEFAULT '0',
  `rssarticles` tinyint(2) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `warnafter` bigint(10) NOT NULL DEFAULT '0',
  `blockafter` bigint(10) NOT NULL DEFAULT '0',
  `blockperiod` bigint(10) NOT NULL DEFAULT '0',
  `completiondiscussions` int(9) NOT NULL DEFAULT '0',
  `completionreplies` int(9) NOT NULL DEFAULT '0',
  `completionposts` int(9) NOT NULL DEFAULT '0',
  `displaywordcount` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_foru_cou_ix` (`course`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Forums contain and structure discussion';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_forum`
--

LOCK TABLES `mdl_forum` WRITE;
/*!40000 ALTER TABLE `mdl_forum` DISABLE KEYS */;
INSERT INTO `mdl_forum` VALUES (1,2,'news','News forum','General news and announcements',0,0,0,0,0,0,1,1,1,0,0,1390925721,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `mdl_forum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_forum_digests`
--

DROP TABLE IF EXISTS `mdl_forum_digests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_forum_digests` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL,
  `forum` bigint(10) NOT NULL,
  `maildigest` tinyint(1) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_forudige_forusemai_uix` (`forum`,`userid`,`maildigest`),
  KEY `mdl_forudige_use_ix` (`userid`),
  KEY `mdl_forudige_for_ix` (`forum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Keeps track of user mail delivery preferences for each forum';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_forum_digests`
--

LOCK TABLES `mdl_forum_digests` WRITE;
/*!40000 ALTER TABLE `mdl_forum_digests` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_forum_digests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_forum_discussions`
--

DROP TABLE IF EXISTS `mdl_forum_discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_forum_discussions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `forum` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `firstpost` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `groupid` bigint(10) NOT NULL DEFAULT '-1',
  `assessed` tinyint(1) NOT NULL DEFAULT '1',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `usermodified` bigint(10) NOT NULL DEFAULT '0',
  `timestart` bigint(10) NOT NULL DEFAULT '0',
  `timeend` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_forudisc_use_ix` (`userid`),
  KEY `mdl_forudisc_for_ix` (`forum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Forums are composed of discussions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_forum_discussions`
--

LOCK TABLES `mdl_forum_discussions` WRITE;
/*!40000 ALTER TABLE `mdl_forum_discussions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_forum_discussions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_forum_posts`
--

DROP TABLE IF EXISTS `mdl_forum_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_forum_posts` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `discussion` bigint(10) NOT NULL DEFAULT '0',
  `parent` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `created` bigint(10) NOT NULL DEFAULT '0',
  `modified` bigint(10) NOT NULL DEFAULT '0',
  `mailed` tinyint(2) NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` longtext NOT NULL,
  `messageformat` tinyint(2) NOT NULL DEFAULT '0',
  `messagetrust` tinyint(2) NOT NULL DEFAULT '0',
  `attachment` varchar(100) NOT NULL DEFAULT '',
  `totalscore` smallint(4) NOT NULL DEFAULT '0',
  `mailnow` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_forupost_use_ix` (`userid`),
  KEY `mdl_forupost_cre_ix` (`created`),
  KEY `mdl_forupost_mai_ix` (`mailed`),
  KEY `mdl_forupost_dis_ix` (`discussion`),
  KEY `mdl_forupost_par_ix` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='All posts are stored in this table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_forum_posts`
--

LOCK TABLES `mdl_forum_posts` WRITE;
/*!40000 ALTER TABLE `mdl_forum_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_forum_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_forum_queue`
--

DROP TABLE IF EXISTS `mdl_forum_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_forum_queue` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `discussionid` bigint(10) NOT NULL DEFAULT '0',
  `postid` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_foruqueu_use_ix` (`userid`),
  KEY `mdl_foruqueu_dis_ix` (`discussionid`),
  KEY `mdl_foruqueu_pos_ix` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='For keeping track of posts that will be mailed in digest for';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_forum_queue`
--

LOCK TABLES `mdl_forum_queue` WRITE;
/*!40000 ALTER TABLE `mdl_forum_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_forum_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_forum_read`
--

DROP TABLE IF EXISTS `mdl_forum_read`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_forum_read` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `forumid` bigint(10) NOT NULL DEFAULT '0',
  `discussionid` bigint(10) NOT NULL DEFAULT '0',
  `postid` bigint(10) NOT NULL DEFAULT '0',
  `firstread` bigint(10) NOT NULL DEFAULT '0',
  `lastread` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_foruread_usefor_ix` (`userid`,`forumid`),
  KEY `mdl_foruread_usedis_ix` (`userid`,`discussionid`),
  KEY `mdl_foruread_usepos_ix` (`userid`,`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tracks each users read posts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_forum_read`
--

LOCK TABLES `mdl_forum_read` WRITE;
/*!40000 ALTER TABLE `mdl_forum_read` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_forum_read` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_forum_subscriptions`
--

DROP TABLE IF EXISTS `mdl_forum_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_forum_subscriptions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `forum` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_forusubs_use_ix` (`userid`),
  KEY `mdl_forusubs_for_ix` (`forum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Keeps track of who is subscribed to what forum';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_forum_subscriptions`
--

LOCK TABLES `mdl_forum_subscriptions` WRITE;
/*!40000 ALTER TABLE `mdl_forum_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_forum_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_forum_track_prefs`
--

DROP TABLE IF EXISTS `mdl_forum_track_prefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_forum_track_prefs` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `forumid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_forutracpref_usefor_ix` (`userid`,`forumid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tracks each users untracked forums';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_forum_track_prefs`
--

LOCK TABLES `mdl_forum_track_prefs` WRITE;
/*!40000 ALTER TABLE `mdl_forum_track_prefs` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_forum_track_prefs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_glossary`
--

DROP TABLE IF EXISTS `mdl_glossary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_glossary` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `allowduplicatedentries` tinyint(2) NOT NULL DEFAULT '0',
  `displayformat` varchar(50) NOT NULL DEFAULT 'dictionary',
  `mainglossary` tinyint(2) NOT NULL DEFAULT '0',
  `showspecial` tinyint(2) NOT NULL DEFAULT '1',
  `showalphabet` tinyint(2) NOT NULL DEFAULT '1',
  `showall` tinyint(2) NOT NULL DEFAULT '1',
  `allowcomments` tinyint(2) NOT NULL DEFAULT '0',
  `allowprintview` tinyint(2) NOT NULL DEFAULT '1',
  `usedynalink` tinyint(2) NOT NULL DEFAULT '1',
  `defaultapproval` tinyint(2) NOT NULL DEFAULT '1',
  `approvaldisplayformat` varchar(50) NOT NULL DEFAULT 'default',
  `globalglossary` tinyint(2) NOT NULL DEFAULT '0',
  `entbypage` smallint(3) NOT NULL DEFAULT '10',
  `editalways` tinyint(2) NOT NULL DEFAULT '0',
  `rsstype` tinyint(2) NOT NULL DEFAULT '0',
  `rssarticles` tinyint(2) NOT NULL DEFAULT '0',
  `assessed` bigint(10) NOT NULL DEFAULT '0',
  `assesstimestart` bigint(10) NOT NULL DEFAULT '0',
  `assesstimefinish` bigint(10) NOT NULL DEFAULT '0',
  `scale` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `completionentries` int(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_glos_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='all glossaries';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_glossary`
--

LOCK TABLES `mdl_glossary` WRITE;
/*!40000 ALTER TABLE `mdl_glossary` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_glossary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_glossary_alias`
--

DROP TABLE IF EXISTS `mdl_glossary_alias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_glossary_alias` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `entryid` bigint(10) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_glosalia_ent_ix` (`entryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='entries alias';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_glossary_alias`
--

LOCK TABLES `mdl_glossary_alias` WRITE;
/*!40000 ALTER TABLE `mdl_glossary_alias` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_glossary_alias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_glossary_categories`
--

DROP TABLE IF EXISTS `mdl_glossary_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_glossary_categories` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `glossaryid` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `usedynalink` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_gloscate_glo_ix` (`glossaryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='all categories for glossary entries';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_glossary_categories`
--

LOCK TABLES `mdl_glossary_categories` WRITE;
/*!40000 ALTER TABLE `mdl_glossary_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_glossary_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_glossary_entries`
--

DROP TABLE IF EXISTS `mdl_glossary_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_glossary_entries` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `glossaryid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `concept` varchar(255) NOT NULL DEFAULT '',
  `definition` longtext NOT NULL,
  `definitionformat` tinyint(2) NOT NULL DEFAULT '0',
  `definitiontrust` tinyint(2) NOT NULL DEFAULT '0',
  `attachment` varchar(100) NOT NULL DEFAULT '',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `teacherentry` tinyint(2) NOT NULL DEFAULT '0',
  `sourceglossaryid` bigint(10) NOT NULL DEFAULT '0',
  `usedynalink` tinyint(2) NOT NULL DEFAULT '1',
  `casesensitive` tinyint(2) NOT NULL DEFAULT '0',
  `fullmatch` tinyint(2) NOT NULL DEFAULT '1',
  `approved` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_glosentr_use_ix` (`userid`),
  KEY `mdl_glosentr_con_ix` (`concept`),
  KEY `mdl_glosentr_glo_ix` (`glossaryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='all glossary entries';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_glossary_entries`
--

LOCK TABLES `mdl_glossary_entries` WRITE;
/*!40000 ALTER TABLE `mdl_glossary_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_glossary_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_glossary_entries_categories`
--

DROP TABLE IF EXISTS `mdl_glossary_entries_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_glossary_entries_categories` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `categoryid` bigint(10) NOT NULL DEFAULT '0',
  `entryid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_glosentrcate_cat_ix` (`categoryid`),
  KEY `mdl_glosentrcate_ent_ix` (`entryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='categories of each glossary entry';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_glossary_entries_categories`
--

LOCK TABLES `mdl_glossary_entries_categories` WRITE;
/*!40000 ALTER TABLE `mdl_glossary_entries_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_glossary_entries_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_glossary_formats`
--

DROP TABLE IF EXISTS `mdl_glossary_formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_glossary_formats` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `popupformatname` varchar(50) NOT NULL DEFAULT '',
  `visible` tinyint(2) NOT NULL DEFAULT '1',
  `showgroup` tinyint(2) NOT NULL DEFAULT '1',
  `defaultmode` varchar(50) NOT NULL DEFAULT '',
  `defaulthook` varchar(50) NOT NULL DEFAULT '',
  `sortkey` varchar(50) NOT NULL DEFAULT '',
  `sortorder` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Setting of the display formats';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_glossary_formats`
--

LOCK TABLES `mdl_glossary_formats` WRITE;
/*!40000 ALTER TABLE `mdl_glossary_formats` DISABLE KEYS */;
INSERT INTO `mdl_glossary_formats` VALUES (1,'continuous','continuous',1,1,'','','',''),(2,'dictionary','dictionary',1,1,'','','',''),(3,'encyclopedia','encyclopedia',1,1,'','','',''),(4,'entrylist','entrylist',1,1,'','','',''),(5,'faq','faq',1,1,'','','',''),(6,'fullwithauthor','fullwithauthor',1,1,'','','',''),(7,'fullwithoutauthor','fullwithoutauthor',1,1,'','','','');
/*!40000 ALTER TABLE `mdl_glossary_formats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_categories`
--

DROP TABLE IF EXISTS `mdl_grade_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_categories` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL,
  `parent` bigint(10) DEFAULT NULL,
  `depth` bigint(10) NOT NULL DEFAULT '0',
  `path` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL DEFAULT '',
  `aggregation` bigint(10) NOT NULL DEFAULT '0',
  `keephigh` bigint(10) NOT NULL DEFAULT '0',
  `droplow` bigint(10) NOT NULL DEFAULT '0',
  `aggregateonlygraded` tinyint(1) NOT NULL DEFAULT '0',
  `aggregateoutcomes` tinyint(1) NOT NULL DEFAULT '0',
  `aggregatesubcats` tinyint(1) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_gradcate_cou_ix` (`courseid`),
  KEY `mdl_gradcate_par_ix` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table keeps information about categories, used for grou';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_categories`
--

LOCK TABLES `mdl_grade_categories` WRITE;
/*!40000 ALTER TABLE `mdl_grade_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_categories_history`
--

DROP TABLE IF EXISTS `mdl_grade_categories_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_categories_history` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `action` bigint(10) NOT NULL DEFAULT '0',
  `oldid` bigint(10) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `timemodified` bigint(10) DEFAULT NULL,
  `loggeduser` bigint(10) DEFAULT NULL,
  `courseid` bigint(10) NOT NULL,
  `parent` bigint(10) DEFAULT NULL,
  `depth` bigint(10) NOT NULL DEFAULT '0',
  `path` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL DEFAULT '',
  `aggregation` bigint(10) NOT NULL DEFAULT '0',
  `keephigh` bigint(10) NOT NULL DEFAULT '0',
  `droplow` bigint(10) NOT NULL DEFAULT '0',
  `aggregateonlygraded` tinyint(1) NOT NULL DEFAULT '0',
  `aggregateoutcomes` tinyint(1) NOT NULL DEFAULT '0',
  `aggregatesubcats` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_gradcatehist_act_ix` (`action`),
  KEY `mdl_gradcatehist_old_ix` (`oldid`),
  KEY `mdl_gradcatehist_cou_ix` (`courseid`),
  KEY `mdl_gradcatehist_par_ix` (`parent`),
  KEY `mdl_gradcatehist_log_ix` (`loggeduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='History of grade_categories';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_categories_history`
--

LOCK TABLES `mdl_grade_categories_history` WRITE;
/*!40000 ALTER TABLE `mdl_grade_categories_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_categories_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_grades`
--

DROP TABLE IF EXISTS `mdl_grade_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_grades` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `itemid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `rawgrade` decimal(10,5) DEFAULT NULL,
  `rawgrademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
  `rawgrademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `rawscaleid` bigint(10) DEFAULT NULL,
  `usermodified` bigint(10) DEFAULT NULL,
  `finalgrade` decimal(10,5) DEFAULT NULL,
  `hidden` bigint(10) NOT NULL DEFAULT '0',
  `locked` bigint(10) NOT NULL DEFAULT '0',
  `locktime` bigint(10) NOT NULL DEFAULT '0',
  `exported` bigint(10) NOT NULL DEFAULT '0',
  `overridden` bigint(10) NOT NULL DEFAULT '0',
  `excluded` bigint(10) NOT NULL DEFAULT '0',
  `feedback` longtext,
  `feedbackformat` bigint(10) NOT NULL DEFAULT '0',
  `information` longtext,
  `informationformat` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) DEFAULT NULL,
  `timemodified` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradgrad_useite_uix` (`userid`,`itemid`),
  KEY `mdl_gradgrad_locloc_ix` (`locked`,`locktime`),
  KEY `mdl_gradgrad_ite_ix` (`itemid`),
  KEY `mdl_gradgrad_use_ix` (`userid`),
  KEY `mdl_gradgrad_raw_ix` (`rawscaleid`),
  KEY `mdl_gradgrad_use2_ix` (`usermodified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='grade_grades  This table keeps individual grades for each us';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_grades`
--

LOCK TABLES `mdl_grade_grades` WRITE;
/*!40000 ALTER TABLE `mdl_grade_grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_grades_history`
--

DROP TABLE IF EXISTS `mdl_grade_grades_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_grades_history` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `action` bigint(10) NOT NULL DEFAULT '0',
  `oldid` bigint(10) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `timemodified` bigint(10) DEFAULT NULL,
  `loggeduser` bigint(10) DEFAULT NULL,
  `itemid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `rawgrade` decimal(10,5) DEFAULT NULL,
  `rawgrademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
  `rawgrademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `rawscaleid` bigint(10) DEFAULT NULL,
  `usermodified` bigint(10) DEFAULT NULL,
  `finalgrade` decimal(10,5) DEFAULT NULL,
  `hidden` bigint(10) NOT NULL DEFAULT '0',
  `locked` bigint(10) NOT NULL DEFAULT '0',
  `locktime` bigint(10) NOT NULL DEFAULT '0',
  `exported` bigint(10) NOT NULL DEFAULT '0',
  `overridden` bigint(10) NOT NULL DEFAULT '0',
  `excluded` bigint(10) NOT NULL DEFAULT '0',
  `feedback` longtext,
  `feedbackformat` bigint(10) NOT NULL DEFAULT '0',
  `information` longtext,
  `informationformat` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_gradgradhist_act_ix` (`action`),
  KEY `mdl_gradgradhist_tim_ix` (`timemodified`),
  KEY `mdl_gradgradhist_old_ix` (`oldid`),
  KEY `mdl_gradgradhist_ite_ix` (`itemid`),
  KEY `mdl_gradgradhist_use_ix` (`userid`),
  KEY `mdl_gradgradhist_raw_ix` (`rawscaleid`),
  KEY `mdl_gradgradhist_use2_ix` (`usermodified`),
  KEY `mdl_gradgradhist_log_ix` (`loggeduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='History table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_grades_history`
--

LOCK TABLES `mdl_grade_grades_history` WRITE;
/*!40000 ALTER TABLE `mdl_grade_grades_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_grades_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_import_newitem`
--

DROP TABLE IF EXISTS `mdl_grade_import_newitem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_import_newitem` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `itemname` varchar(255) NOT NULL DEFAULT '',
  `importcode` bigint(10) NOT NULL,
  `importer` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradimponewi_imp_ix` (`importer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='temporary table for storing new grade_item names from grade ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_import_newitem`
--

LOCK TABLES `mdl_grade_import_newitem` WRITE;
/*!40000 ALTER TABLE `mdl_grade_import_newitem` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_import_newitem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_import_values`
--

DROP TABLE IF EXISTS `mdl_grade_import_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_import_values` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `itemid` bigint(10) DEFAULT NULL,
  `newgradeitem` bigint(10) DEFAULT NULL,
  `userid` bigint(10) NOT NULL,
  `finalgrade` decimal(10,5) DEFAULT NULL,
  `feedback` longtext,
  `importcode` bigint(10) NOT NULL,
  `importer` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradimpovalu_ite_ix` (`itemid`),
  KEY `mdl_gradimpovalu_new_ix` (`newgradeitem`),
  KEY `mdl_gradimpovalu_imp_ix` (`importer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Temporary table for importing grades';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_import_values`
--

LOCK TABLES `mdl_grade_import_values` WRITE;
/*!40000 ALTER TABLE `mdl_grade_import_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_import_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_items`
--

DROP TABLE IF EXISTS `mdl_grade_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_items` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) DEFAULT NULL,
  `categoryid` bigint(10) DEFAULT NULL,
  `itemname` varchar(255) DEFAULT NULL,
  `itemtype` varchar(30) NOT NULL DEFAULT '',
  `itemmodule` varchar(30) DEFAULT NULL,
  `iteminstance` bigint(10) DEFAULT NULL,
  `itemnumber` bigint(10) DEFAULT NULL,
  `iteminfo` longtext,
  `idnumber` varchar(255) DEFAULT NULL,
  `calculation` longtext,
  `gradetype` smallint(4) NOT NULL DEFAULT '1',
  `grademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
  `grademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `scaleid` bigint(10) DEFAULT NULL,
  `outcomeid` bigint(10) DEFAULT NULL,
  `gradepass` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `multfactor` decimal(10,5) NOT NULL DEFAULT '1.00000',
  `plusfactor` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `aggregationcoef` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  `display` bigint(10) NOT NULL DEFAULT '0',
  `decimals` tinyint(1) DEFAULT NULL,
  `hidden` bigint(10) NOT NULL DEFAULT '0',
  `locked` bigint(10) NOT NULL DEFAULT '0',
  `locktime` bigint(10) NOT NULL DEFAULT '0',
  `needsupdate` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) DEFAULT NULL,
  `timemodified` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_graditem_locloc_ix` (`locked`,`locktime`),
  KEY `mdl_graditem_itenee_ix` (`itemtype`,`needsupdate`),
  KEY `mdl_graditem_gra_ix` (`gradetype`),
  KEY `mdl_graditem_idncou_ix` (`idnumber`,`courseid`),
  KEY `mdl_graditem_cou_ix` (`courseid`),
  KEY `mdl_graditem_cat_ix` (`categoryid`),
  KEY `mdl_graditem_sca_ix` (`scaleid`),
  KEY `mdl_graditem_out_ix` (`outcomeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table keeps information about gradeable items (ie colum';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_items`
--

LOCK TABLES `mdl_grade_items` WRITE;
/*!40000 ALTER TABLE `mdl_grade_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_items_history`
--

DROP TABLE IF EXISTS `mdl_grade_items_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_items_history` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `action` bigint(10) NOT NULL DEFAULT '0',
  `oldid` bigint(10) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `timemodified` bigint(10) DEFAULT NULL,
  `loggeduser` bigint(10) DEFAULT NULL,
  `courseid` bigint(10) DEFAULT NULL,
  `categoryid` bigint(10) DEFAULT NULL,
  `itemname` varchar(255) DEFAULT NULL,
  `itemtype` varchar(30) NOT NULL DEFAULT '',
  `itemmodule` varchar(30) DEFAULT NULL,
  `iteminstance` bigint(10) DEFAULT NULL,
  `itemnumber` bigint(10) DEFAULT NULL,
  `iteminfo` longtext,
  `idnumber` varchar(255) DEFAULT NULL,
  `calculation` longtext,
  `gradetype` smallint(4) NOT NULL DEFAULT '1',
  `grademax` decimal(10,5) NOT NULL DEFAULT '100.00000',
  `grademin` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `scaleid` bigint(10) DEFAULT NULL,
  `outcomeid` bigint(10) DEFAULT NULL,
  `gradepass` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `multfactor` decimal(10,5) NOT NULL DEFAULT '1.00000',
  `plusfactor` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `aggregationcoef` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  `hidden` bigint(10) NOT NULL DEFAULT '0',
  `locked` bigint(10) NOT NULL DEFAULT '0',
  `locktime` bigint(10) NOT NULL DEFAULT '0',
  `needsupdate` bigint(10) NOT NULL DEFAULT '0',
  `display` bigint(10) NOT NULL DEFAULT '0',
  `decimals` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_graditemhist_act_ix` (`action`),
  KEY `mdl_graditemhist_old_ix` (`oldid`),
  KEY `mdl_graditemhist_cou_ix` (`courseid`),
  KEY `mdl_graditemhist_cat_ix` (`categoryid`),
  KEY `mdl_graditemhist_sca_ix` (`scaleid`),
  KEY `mdl_graditemhist_out_ix` (`outcomeid`),
  KEY `mdl_graditemhist_log_ix` (`loggeduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='History of grade_items';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_items_history`
--

LOCK TABLES `mdl_grade_items_history` WRITE;
/*!40000 ALTER TABLE `mdl_grade_items_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_items_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_letters`
--

DROP TABLE IF EXISTS `mdl_grade_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_letters` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextid` bigint(10) NOT NULL,
  `lowerboundary` decimal(10,5) NOT NULL,
  `letter` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradlett_conlowlet_uix` (`contextid`,`lowerboundary`,`letter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Repository for grade letters, for courses and other moodle e';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_letters`
--

LOCK TABLES `mdl_grade_letters` WRITE;
/*!40000 ALTER TABLE `mdl_grade_letters` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_letters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_outcomes`
--

DROP TABLE IF EXISTS `mdl_grade_outcomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_outcomes` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) DEFAULT NULL,
  `shortname` varchar(255) NOT NULL DEFAULT '',
  `fullname` longtext NOT NULL,
  `scaleid` bigint(10) DEFAULT NULL,
  `description` longtext,
  `descriptionformat` tinyint(2) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) DEFAULT NULL,
  `timemodified` bigint(10) DEFAULT NULL,
  `usermodified` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradoutc_cousho_uix` (`courseid`,`shortname`),
  KEY `mdl_gradoutc_cou_ix` (`courseid`),
  KEY `mdl_gradoutc_sca_ix` (`scaleid`),
  KEY `mdl_gradoutc_use_ix` (`usermodified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table describes the outcomes used in the system. An out';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_outcomes`
--

LOCK TABLES `mdl_grade_outcomes` WRITE;
/*!40000 ALTER TABLE `mdl_grade_outcomes` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_outcomes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_outcomes_courses`
--

DROP TABLE IF EXISTS `mdl_grade_outcomes_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_outcomes_courses` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL,
  `outcomeid` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradoutccour_couout_uix` (`courseid`,`outcomeid`),
  KEY `mdl_gradoutccour_cou_ix` (`courseid`),
  KEY `mdl_gradoutccour_out_ix` (`outcomeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='stores what outcomes are used in what courses.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_outcomes_courses`
--

LOCK TABLES `mdl_grade_outcomes_courses` WRITE;
/*!40000 ALTER TABLE `mdl_grade_outcomes_courses` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_outcomes_courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_outcomes_history`
--

DROP TABLE IF EXISTS `mdl_grade_outcomes_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_outcomes_history` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `action` bigint(10) NOT NULL DEFAULT '0',
  `oldid` bigint(10) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `timemodified` bigint(10) DEFAULT NULL,
  `loggeduser` bigint(10) DEFAULT NULL,
  `courseid` bigint(10) DEFAULT NULL,
  `shortname` varchar(255) NOT NULL DEFAULT '',
  `fullname` longtext NOT NULL,
  `scaleid` bigint(10) DEFAULT NULL,
  `description` longtext,
  `descriptionformat` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_gradoutchist_act_ix` (`action`),
  KEY `mdl_gradoutchist_old_ix` (`oldid`),
  KEY `mdl_gradoutchist_cou_ix` (`courseid`),
  KEY `mdl_gradoutchist_sca_ix` (`scaleid`),
  KEY `mdl_gradoutchist_log_ix` (`loggeduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='History table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_outcomes_history`
--

LOCK TABLES `mdl_grade_outcomes_history` WRITE;
/*!40000 ALTER TABLE `mdl_grade_outcomes_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_outcomes_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grade_settings`
--

DROP TABLE IF EXISTS `mdl_grade_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grade_settings` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradsett_counam_uix` (`courseid`,`name`),
  KEY `mdl_gradsett_cou_ix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='gradebook settings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grade_settings`
--

LOCK TABLES `mdl_grade_settings` WRITE;
/*!40000 ALTER TABLE `mdl_grade_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grade_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grading_areas`
--

DROP TABLE IF EXISTS `mdl_grading_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grading_areas` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextid` bigint(10) NOT NULL,
  `component` varchar(100) NOT NULL DEFAULT '',
  `areaname` varchar(100) NOT NULL DEFAULT '',
  `activemethod` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradarea_concomare_uix` (`contextid`,`component`,`areaname`),
  KEY `mdl_gradarea_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Identifies gradable areas where advanced grading can happen.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grading_areas`
--

LOCK TABLES `mdl_grading_areas` WRITE;
/*!40000 ALTER TABLE `mdl_grading_areas` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grading_areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grading_definitions`
--

DROP TABLE IF EXISTS `mdl_grading_definitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grading_definitions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `areaid` bigint(10) NOT NULL,
  `method` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` longtext,
  `descriptionformat` tinyint(2) DEFAULT NULL,
  `status` bigint(10) NOT NULL DEFAULT '0',
  `copiedfromid` bigint(10) DEFAULT NULL,
  `timecreated` bigint(10) NOT NULL,
  `usercreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  `usermodified` bigint(10) NOT NULL,
  `timecopied` bigint(10) DEFAULT '0',
  `options` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_graddefi_aremet_uix` (`areaid`,`method`),
  KEY `mdl_graddefi_are_ix` (`areaid`),
  KEY `mdl_graddefi_use_ix` (`usermodified`),
  KEY `mdl_graddefi_use2_ix` (`usercreated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains the basic information about an advanced grading for';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grading_definitions`
--

LOCK TABLES `mdl_grading_definitions` WRITE;
/*!40000 ALTER TABLE `mdl_grading_definitions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grading_definitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_grading_instances`
--

DROP TABLE IF EXISTS `mdl_grading_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_grading_instances` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `definitionid` bigint(10) NOT NULL,
  `raterid` bigint(10) NOT NULL,
  `itemid` bigint(10) DEFAULT NULL,
  `rawgrade` decimal(10,5) DEFAULT NULL,
  `status` bigint(10) NOT NULL DEFAULT '0',
  `feedback` longtext,
  `feedbackformat` tinyint(2) DEFAULT NULL,
  `timemodified` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradinst_def_ix` (`definitionid`),
  KEY `mdl_gradinst_rat_ix` (`raterid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Grading form instance is an assessment record for one gradab';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_grading_instances`
--

LOCK TABLES `mdl_grading_instances` WRITE;
/*!40000 ALTER TABLE `mdl_grading_instances` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_grading_instances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_gradingform_guide_comments`
--

DROP TABLE IF EXISTS `mdl_gradingform_guide_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_gradingform_guide_comments` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `definitionid` bigint(10) NOT NULL,
  `sortorder` bigint(10) NOT NULL,
  `description` longtext,
  `descriptionformat` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradguidcomm_def_ix` (`definitionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='frequently used comments used in marking guide';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_gradingform_guide_comments`
--

LOCK TABLES `mdl_gradingform_guide_comments` WRITE;
/*!40000 ALTER TABLE `mdl_gradingform_guide_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_gradingform_guide_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_gradingform_guide_criteria`
--

DROP TABLE IF EXISTS `mdl_gradingform_guide_criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_gradingform_guide_criteria` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `definitionid` bigint(10) NOT NULL,
  `sortorder` bigint(10) NOT NULL,
  `shortname` varchar(255) NOT NULL DEFAULT '',
  `description` longtext,
  `descriptionformat` tinyint(2) DEFAULT NULL,
  `descriptionmarkers` longtext,
  `descriptionmarkersformat` tinyint(2) DEFAULT NULL,
  `maxscore` decimal(10,5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradguidcrit_def_ix` (`definitionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the rows of the criteria grid.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_gradingform_guide_criteria`
--

LOCK TABLES `mdl_gradingform_guide_criteria` WRITE;
/*!40000 ALTER TABLE `mdl_gradingform_guide_criteria` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_gradingform_guide_criteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_gradingform_guide_fillings`
--

DROP TABLE IF EXISTS `mdl_gradingform_guide_fillings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_gradingform_guide_fillings` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `instanceid` bigint(10) NOT NULL,
  `criterionid` bigint(10) NOT NULL,
  `remark` longtext,
  `remarkformat` tinyint(2) DEFAULT NULL,
  `score` decimal(10,5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradguidfill_inscri_uix` (`instanceid`,`criterionid`),
  KEY `mdl_gradguidfill_ins_ix` (`instanceid`),
  KEY `mdl_gradguidfill_cri_ix` (`criterionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the data of how the guide is filled by a particular r';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_gradingform_guide_fillings`
--

LOCK TABLES `mdl_gradingform_guide_fillings` WRITE;
/*!40000 ALTER TABLE `mdl_gradingform_guide_fillings` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_gradingform_guide_fillings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_gradingform_rubric_criteria`
--

DROP TABLE IF EXISTS `mdl_gradingform_rubric_criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_gradingform_rubric_criteria` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `definitionid` bigint(10) NOT NULL,
  `sortorder` bigint(10) NOT NULL,
  `description` longtext,
  `descriptionformat` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradrubrcrit_def_ix` (`definitionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the rows of the rubric grid.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_gradingform_rubric_criteria`
--

LOCK TABLES `mdl_gradingform_rubric_criteria` WRITE;
/*!40000 ALTER TABLE `mdl_gradingform_rubric_criteria` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_gradingform_rubric_criteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_gradingform_rubric_fillings`
--

DROP TABLE IF EXISTS `mdl_gradingform_rubric_fillings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_gradingform_rubric_fillings` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `instanceid` bigint(10) NOT NULL,
  `criterionid` bigint(10) NOT NULL,
  `levelid` bigint(10) DEFAULT NULL,
  `remark` longtext,
  `remarkformat` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_gradrubrfill_inscri_uix` (`instanceid`,`criterionid`),
  KEY `mdl_gradrubrfill_lev_ix` (`levelid`),
  KEY `mdl_gradrubrfill_ins_ix` (`instanceid`),
  KEY `mdl_gradrubrfill_cri_ix` (`criterionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the data of how the rubric is filled by a particular ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_gradingform_rubric_fillings`
--

LOCK TABLES `mdl_gradingform_rubric_fillings` WRITE;
/*!40000 ALTER TABLE `mdl_gradingform_rubric_fillings` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_gradingform_rubric_fillings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_gradingform_rubric_levels`
--

DROP TABLE IF EXISTS `mdl_gradingform_rubric_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_gradingform_rubric_levels` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `criterionid` bigint(10) NOT NULL,
  `score` decimal(10,5) NOT NULL,
  `definition` longtext,
  `definitionformat` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_gradrubrleve_cri_ix` (`criterionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the columns of the rubric grid.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_gradingform_rubric_levels`
--

LOCK TABLES `mdl_gradingform_rubric_levels` WRITE;
/*!40000 ALTER TABLE `mdl_gradingform_rubric_levels` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_gradingform_rubric_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_groupings`
--

DROP TABLE IF EXISTS `mdl_groupings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_groupings` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `idnumber` varchar(100) NOT NULL DEFAULT '',
  `description` longtext,
  `descriptionformat` tinyint(2) NOT NULL DEFAULT '0',
  `configdata` longtext,
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_grou_idn2_ix` (`idnumber`),
  KEY `mdl_grou_cou2_ix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A grouping is a collection of groups. WAS: groups_groupings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_groupings`
--

LOCK TABLES `mdl_groupings` WRITE;
/*!40000 ALTER TABLE `mdl_groupings` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_groupings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_groupings_groups`
--

DROP TABLE IF EXISTS `mdl_groupings_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_groupings_groups` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `groupingid` bigint(10) NOT NULL DEFAULT '0',
  `groupid` bigint(10) NOT NULL DEFAULT '0',
  `timeadded` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_grougrou_gro_ix` (`groupingid`),
  KEY `mdl_grougrou_gro2_ix` (`groupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link a grouping to a group (note, groups can be in multiple ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_groupings_groups`
--

LOCK TABLES `mdl_groupings_groups` WRITE;
/*!40000 ALTER TABLE `mdl_groupings_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_groupings_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_groups`
--

DROP TABLE IF EXISTS `mdl_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_groups` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL,
  `idnumber` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(254) NOT NULL DEFAULT '',
  `description` longtext,
  `descriptionformat` tinyint(2) NOT NULL DEFAULT '0',
  `enrolmentkey` varchar(50) DEFAULT NULL,
  `picture` bigint(10) NOT NULL DEFAULT '0',
  `hidepicture` tinyint(1) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_grou_idn_ix` (`idnumber`),
  KEY `mdl_grou_cou_ix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Each record represents a group.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_groups`
--

LOCK TABLES `mdl_groups` WRITE;
/*!40000 ALTER TABLE `mdl_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_groups_members`
--

DROP TABLE IF EXISTS `mdl_groups_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_groups_members` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `groupid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `timeadded` bigint(10) NOT NULL DEFAULT '0',
  `component` varchar(100) NOT NULL DEFAULT '',
  `itemid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_groumemb_gro_ix` (`groupid`),
  KEY `mdl_groumemb_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link a user to a group.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_groups_members`
--

LOCK TABLES `mdl_groups_members` WRITE;
/*!40000 ALTER TABLE `mdl_groups_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_groups_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_imscp`
--

DROP TABLE IF EXISTS `mdl_imscp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_imscp` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `revision` bigint(10) NOT NULL DEFAULT '0',
  `keepold` bigint(10) NOT NULL DEFAULT '-1',
  `structure` longtext,
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_imsc_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='each record is one imscp resource';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_imscp`
--

LOCK TABLES `mdl_imscp` WRITE;
/*!40000 ALTER TABLE `mdl_imscp` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_imscp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_label`
--

DROP TABLE IF EXISTS `mdl_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_label` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_labe_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines labels';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_label`
--

LOCK TABLES `mdl_label` WRITE;
/*!40000 ALTER TABLE `mdl_label` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lesson`
--

DROP TABLE IF EXISTS `mdl_lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lesson` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `practice` smallint(3) NOT NULL DEFAULT '0',
  `modattempts` smallint(3) NOT NULL DEFAULT '0',
  `usepassword` smallint(3) NOT NULL DEFAULT '0',
  `password` varchar(32) NOT NULL DEFAULT '',
  `dependency` bigint(10) NOT NULL DEFAULT '0',
  `conditions` longtext NOT NULL,
  `grade` smallint(3) NOT NULL DEFAULT '0',
  `custom` smallint(3) NOT NULL DEFAULT '0',
  `ongoing` smallint(3) NOT NULL DEFAULT '0',
  `usemaxgrade` smallint(3) NOT NULL DEFAULT '0',
  `maxanswers` smallint(3) NOT NULL DEFAULT '4',
  `maxattempts` smallint(3) NOT NULL DEFAULT '5',
  `review` smallint(3) NOT NULL DEFAULT '0',
  `nextpagedefault` smallint(3) NOT NULL DEFAULT '0',
  `feedback` smallint(3) NOT NULL DEFAULT '1',
  `minquestions` smallint(3) NOT NULL DEFAULT '0',
  `maxpages` smallint(3) NOT NULL DEFAULT '0',
  `timed` smallint(3) NOT NULL DEFAULT '0',
  `maxtime` bigint(10) NOT NULL DEFAULT '0',
  `retake` smallint(3) NOT NULL DEFAULT '1',
  `activitylink` bigint(10) NOT NULL DEFAULT '0',
  `mediafile` varchar(255) NOT NULL DEFAULT '',
  `mediaheight` bigint(10) NOT NULL DEFAULT '100',
  `mediawidth` bigint(10) NOT NULL DEFAULT '650',
  `mediaclose` smallint(3) NOT NULL DEFAULT '0',
  `slideshow` smallint(3) NOT NULL DEFAULT '0',
  `width` bigint(10) NOT NULL DEFAULT '640',
  `height` bigint(10) NOT NULL DEFAULT '480',
  `bgcolor` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  `displayleft` smallint(3) NOT NULL DEFAULT '0',
  `displayleftif` smallint(3) NOT NULL DEFAULT '0',
  `progressbar` smallint(3) NOT NULL DEFAULT '0',
  `highscores` smallint(3) NOT NULL DEFAULT '0',
  `maxhighscores` bigint(10) NOT NULL DEFAULT '0',
  `available` bigint(10) NOT NULL DEFAULT '0',
  `deadline` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_less_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines lesson';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lesson`
--

LOCK TABLES `mdl_lesson` WRITE;
/*!40000 ALTER TABLE `mdl_lesson` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lesson` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lesson_answers`
--

DROP TABLE IF EXISTS `mdl_lesson_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lesson_answers` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `lessonid` bigint(10) NOT NULL DEFAULT '0',
  `pageid` bigint(10) NOT NULL DEFAULT '0',
  `jumpto` bigint(11) NOT NULL DEFAULT '0',
  `grade` smallint(4) NOT NULL DEFAULT '0',
  `score` bigint(10) NOT NULL DEFAULT '0',
  `flags` smallint(3) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `answer` longtext,
  `answerformat` tinyint(2) NOT NULL DEFAULT '0',
  `response` longtext,
  `responseformat` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lessansw_les_ix` (`lessonid`),
  KEY `mdl_lessansw_pag_ix` (`pageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines lesson_answers';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lesson_answers`
--

LOCK TABLES `mdl_lesson_answers` WRITE;
/*!40000 ALTER TABLE `mdl_lesson_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lesson_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lesson_attempts`
--

DROP TABLE IF EXISTS `mdl_lesson_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lesson_attempts` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `lessonid` bigint(10) NOT NULL DEFAULT '0',
  `pageid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `answerid` bigint(10) NOT NULL DEFAULT '0',
  `retry` smallint(3) NOT NULL DEFAULT '0',
  `correct` bigint(10) NOT NULL DEFAULT '0',
  `useranswer` longtext,
  `timeseen` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lessatte_use_ix` (`userid`),
  KEY `mdl_lessatte_les_ix` (`lessonid`),
  KEY `mdl_lessatte_pag_ix` (`pageid`),
  KEY `mdl_lessatte_ans_ix` (`answerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines lesson_attempts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lesson_attempts`
--

LOCK TABLES `mdl_lesson_attempts` WRITE;
/*!40000 ALTER TABLE `mdl_lesson_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lesson_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lesson_branch`
--

DROP TABLE IF EXISTS `mdl_lesson_branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lesson_branch` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `lessonid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `pageid` bigint(10) NOT NULL DEFAULT '0',
  `retry` bigint(10) NOT NULL DEFAULT '0',
  `flag` smallint(3) NOT NULL DEFAULT '0',
  `timeseen` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lessbran_use_ix` (`userid`),
  KEY `mdl_lessbran_les_ix` (`lessonid`),
  KEY `mdl_lessbran_pag_ix` (`pageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='branches for each lesson/user';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lesson_branch`
--

LOCK TABLES `mdl_lesson_branch` WRITE;
/*!40000 ALTER TABLE `mdl_lesson_branch` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lesson_branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lesson_grades`
--

DROP TABLE IF EXISTS `mdl_lesson_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lesson_grades` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `lessonid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `grade` double NOT NULL DEFAULT '0',
  `late` smallint(3) NOT NULL DEFAULT '0',
  `completed` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lessgrad_use_ix` (`userid`),
  KEY `mdl_lessgrad_les_ix` (`lessonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines lesson_grades';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lesson_grades`
--

LOCK TABLES `mdl_lesson_grades` WRITE;
/*!40000 ALTER TABLE `mdl_lesson_grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lesson_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lesson_high_scores`
--

DROP TABLE IF EXISTS `mdl_lesson_high_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lesson_high_scores` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `lessonid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `gradeid` bigint(10) NOT NULL DEFAULT '0',
  `nickname` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_lesshighscor_use_ix` (`userid`),
  KEY `mdl_lesshighscor_les_ix` (`lessonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='high scores for each lesson';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lesson_high_scores`
--

LOCK TABLES `mdl_lesson_high_scores` WRITE;
/*!40000 ALTER TABLE `mdl_lesson_high_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lesson_high_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lesson_pages`
--

DROP TABLE IF EXISTS `mdl_lesson_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lesson_pages` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `lessonid` bigint(10) NOT NULL DEFAULT '0',
  `prevpageid` bigint(10) NOT NULL DEFAULT '0',
  `nextpageid` bigint(10) NOT NULL DEFAULT '0',
  `qtype` smallint(3) NOT NULL DEFAULT '0',
  `qoption` smallint(3) NOT NULL DEFAULT '0',
  `layout` smallint(3) NOT NULL DEFAULT '1',
  `display` smallint(3) NOT NULL DEFAULT '1',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `contents` longtext NOT NULL,
  `contentsformat` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lesspage_les_ix` (`lessonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines lesson_pages';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lesson_pages`
--

LOCK TABLES `mdl_lesson_pages` WRITE;
/*!40000 ALTER TABLE `mdl_lesson_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lesson_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lesson_timer`
--

DROP TABLE IF EXISTS `mdl_lesson_timer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lesson_timer` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `lessonid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `starttime` bigint(10) NOT NULL DEFAULT '0',
  `lessontime` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_lesstime_use_ix` (`userid`),
  KEY `mdl_lesstime_les_ix` (`lessonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='lesson timer for each lesson';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lesson_timer`
--

LOCK TABLES `mdl_lesson_timer` WRITE;
/*!40000 ALTER TABLE `mdl_lesson_timer` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lesson_timer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_license`
--

DROP TABLE IF EXISTS `mdl_license`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_license` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `shortname` varchar(255) DEFAULT NULL,
  `fullname` longtext,
  `source` varchar(255) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `version` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='store licenses used by moodle';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_license`
--

LOCK TABLES `mdl_license` WRITE;
/*!40000 ALTER TABLE `mdl_license` DISABLE KEYS */;
INSERT INTO `mdl_license` VALUES (1,'unknown','Unknown license','',1,2010033100),(2,'allrightsreserved','All rights reserved','http://en.wikipedia.org/wiki/All_rights_reserved',1,2010033100),(3,'public','Public Domain','http://creativecommons.org/licenses/publicdomain/',1,2010033100),(4,'cc','Creative Commons','http://creativecommons.org/licenses/by/3.0/',1,2010033100),(5,'cc-nd','Creative Commons - NoDerivs','http://creativecommons.org/licenses/by-nd/3.0/',1,2010033100),(6,'cc-nc-nd','Creative Commons - No Commercial NoDerivs','http://creativecommons.org/licenses/by-nc-nd/3.0/',1,2010033100),(7,'cc-nc','Creative Commons - No Commercial','http://creativecommons.org/licenses/by-nc/3.0/',1,2013051500),(8,'cc-nc-sa','Creative Commons - No Commercial ShareAlike','http://creativecommons.org/licenses/by-nc-sa/3.0/',1,2010033100),(9,'cc-sa','Creative Commons - ShareAlike','http://creativecommons.org/licenses/by-sa/3.0/',1,2010033100);
/*!40000 ALTER TABLE `mdl_license` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_log`
--

DROP TABLE IF EXISTS `mdl_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_log` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `time` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `ip` varchar(45) NOT NULL DEFAULT '',
  `course` bigint(10) NOT NULL DEFAULT '0',
  `module` varchar(20) NOT NULL DEFAULT '',
  `cmid` bigint(10) NOT NULL DEFAULT '0',
  `action` varchar(40) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_log_coumodact_ix` (`course`,`module`,`action`),
  KEY `mdl_log_tim_ix` (`time`),
  KEY `mdl_log_act_ix` (`action`),
  KEY `mdl_log_usecou_ix` (`userid`,`course`),
  KEY `mdl_log_cmi_ix` (`cmid`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='Every action is logged as far as possible';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_log`
--

LOCK TABLES `mdl_log` WRITE;
/*!40000 ALTER TABLE `mdl_log` DISABLE KEYS */;
INSERT INTO `mdl_log` VALUES (1,1390421786,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(2,1390421786,2,'127.0.0.1',1,'user',0,'login','view.php?id=2&course=1','2'),(3,1390421830,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(4,1390421830,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(5,1390422097,2,'127.0.0.1',1,'course',0,'view','view.php?id=1','1'),(6,1390422944,0,'0.0.0.0',1,'user',0,'update','view.php?id=3',''),(7,1390422944,0,'0.0.0.0',1,'user',0,'add','/view.php?id=3','testuser testuser'),(8,1390422951,2,'127.0.0.1',1,'course',0,'view','view.php?id=1','1'),(9,1390473577,0,'127.0.0.1',1,'login',0,'error','index.php','admin'),(10,1390473592,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(11,1390473592,2,'127.0.0.1',1,'user',0,'login','view.php?id=2&course=1','2'),(12,1390486847,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(13,1390486847,2,'127.0.0.1',1,'user',0,'login','view.php?id=2&course=1','2'),(14,1390487566,2,'127.0.0.1',1,'user',0,'update','view.php?id=3',''),(15,1390487566,2,'127.0.0.1',1,'user',0,'delete','view.php?id=3','testuser testuser'),(16,1390487598,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(17,1390487599,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(18,1390496354,0,'0.0.0.0',1,'user',0,'update','view.php?id=4',''),(19,1390496354,0,'0.0.0.0',1,'user',0,'add','/view.php?id=4','bruce wayne'),(20,1390504546,0,'127.0.0.1',1,'login',0,'error','index.php','admin'),(21,1390504555,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(22,1390504555,2,'127.0.0.1',1,'user',0,'login','view.php?id=2&course=1','2'),(23,1390504616,2,'127.0.0.1',1,'course',0,'new','view.php?id=2','testcourse1 (ID 2)'),(24,1390504641,2,'127.0.0.1',1,'course',0,'view','view.php?id=1','1'),(25,1390505242,2,'127.0.0.1',1,'course',0,'view','view.php?id=1','1'),(26,1390505784,0,'0.0.0.0',1,'user',0,'update','view.php?id=5',''),(27,1390505784,0,'0.0.0.0',1,'user',0,'add','/view.php?id=5','test user'),(28,1390505820,2,'127.0.0.1',1,'user',0,'update','view.php?id=4',''),(29,1390505820,2,'127.0.0.1',1,'user',0,'delete','view.php?id=4','bruce wayne'),(30,1390505838,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(31,1390505923,0,'0.0.0.0',1,'user',0,'update','view.php?id=6',''),(32,1390505923,0,'0.0.0.0',1,'user',0,'add','/view.php?id=6','test teacher'),(33,1390841747,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(34,1390841747,2,'127.0.0.1',1,'user',0,'login','view.php?id=2&course=1','2'),(35,1390841754,2,'127.0.0.1',1,'course',0,'view','view.php?id=1','1'),(36,1390841824,2,'127.0.0.1',1,'role',0,'add','admin/roles/define.php?action=view&roleid=9','deleteme'),(37,1390925719,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(38,1390925719,2,'127.0.0.1',1,'user',0,'login','view.php?id=2&course=1','2'),(39,1390925721,2,'127.0.0.1',2,'course',0,'view','view.php?id=2','2'),(40,1390925792,2,'127.0.0.1',1,'user',0,'update','view.php?id=2',''),(41,1390925796,2,'127.0.0.1',2,'user',0,'view','view.php?id=2&course=2','2');
/*!40000 ALTER TABLE `mdl_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_log_display`
--

DROP TABLE IF EXISTS `mdl_log_display`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_log_display` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(20) NOT NULL DEFAULT '',
  `action` varchar(40) NOT NULL DEFAULT '',
  `mtable` varchar(30) NOT NULL DEFAULT '',
  `field` varchar(200) NOT NULL DEFAULT '',
  `component` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_logdisp_modact_uix` (`module`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8 COMMENT='For a particular module/action, specifies a moodle table/fie';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_log_display`
--

LOCK TABLES `mdl_log_display` WRITE;
/*!40000 ALTER TABLE `mdl_log_display` DISABLE KEYS */;
INSERT INTO `mdl_log_display` VALUES (1,'course','user report','user','CONCAT(firstname, \' \', lastname)','moodle'),(2,'course','view','course','fullname','moodle'),(3,'course','view section','course_sections','name','moodle'),(4,'course','update','course','fullname','moodle'),(5,'course','hide','course','fullname','moodle'),(6,'course','show','course','fullname','moodle'),(7,'course','move','course','fullname','moodle'),(8,'course','enrol','course','fullname','moodle'),(9,'course','unenrol','course','fullname','moodle'),(10,'course','report log','course','fullname','moodle'),(11,'course','report live','course','fullname','moodle'),(12,'course','report outline','course','fullname','moodle'),(13,'course','report participation','course','fullname','moodle'),(14,'course','report stats','course','fullname','moodle'),(15,'category','add','course_categories','name','moodle'),(16,'category','hide','course_categories','name','moodle'),(17,'category','move','course_categories','name','moodle'),(18,'category','show','course_categories','name','moodle'),(19,'category','update','course_categories','name','moodle'),(20,'message','write','user','CONCAT(firstname, \' \', lastname)','moodle'),(21,'message','read','user','CONCAT(firstname, \' \', lastname)','moodle'),(22,'message','add contact','user','CONCAT(firstname, \' \', lastname)','moodle'),(23,'message','remove contact','user','CONCAT(firstname, \' \', lastname)','moodle'),(24,'message','block contact','user','CONCAT(firstname, \' \', lastname)','moodle'),(25,'message','unblock contact','user','CONCAT(firstname, \' \', lastname)','moodle'),(26,'group','view','groups','name','moodle'),(27,'tag','update','tag','name','moodle'),(28,'tag','flag','tag','name','moodle'),(29,'user','view','user','CONCAT(firstname, \' \', lastname)','moodle'),(30,'assign','add','assign','name','mod_assign'),(31,'assign','delete mod','assign','name','mod_assign'),(32,'assign','download all submissions','assign','name','mod_assign'),(33,'assign','grade submission','assign','name','mod_assign'),(34,'assign','lock submission','assign','name','mod_assign'),(35,'assign','reveal identities','assign','name','mod_assign'),(36,'assign','revert submission to draft','assign','name','mod_assign'),(37,'assign','set marking workflow state','assign','name','mod_assign'),(38,'assign','submission statement accepted','assign','name','mod_assign'),(39,'assign','submit','assign','name','mod_assign'),(40,'assign','submit for grading','assign','name','mod_assign'),(41,'assign','unlock submission','assign','name','mod_assign'),(42,'assign','update','assign','name','mod_assign'),(43,'assign','upload','assign','name','mod_assign'),(44,'assign','view','assign','name','mod_assign'),(45,'assign','view all','course','fullname','mod_assign'),(46,'assign','view confirm submit assignment form','assign','name','mod_assign'),(47,'assign','view grading form','assign','name','mod_assign'),(48,'assign','view submission','assign','name','mod_assign'),(49,'assign','view submission grading table','assign','name','mod_assign'),(50,'assign','view submit assignment form','assign','name','mod_assign'),(51,'assign','view feedback','assign','name','mod_assign'),(52,'assign','view batch set marking workflow state','assign','name','mod_assign'),(53,'assignment','view','assignment','name','mod_assignment'),(54,'assignment','add','assignment','name','mod_assignment'),(55,'assignment','update','assignment','name','mod_assignment'),(56,'assignment','view submission','assignment','name','mod_assignment'),(57,'assignment','upload','assignment','name','mod_assignment'),(58,'book','add','book','name','mod_book'),(59,'book','update','book','name','mod_book'),(60,'book','view','book','name','mod_book'),(61,'book','add chapter','book_chapters','title','mod_book'),(62,'book','update chapter','book_chapters','title','mod_book'),(63,'book','view chapter','book_chapters','title','mod_book'),(64,'chat','view','chat','name','mod_chat'),(65,'chat','add','chat','name','mod_chat'),(66,'chat','update','chat','name','mod_chat'),(67,'chat','report','chat','name','mod_chat'),(68,'chat','talk','chat','name','mod_chat'),(69,'choice','view','choice','name','mod_choice'),(70,'choice','update','choice','name','mod_choice'),(71,'choice','add','choice','name','mod_choice'),(72,'choice','report','choice','name','mod_choice'),(73,'choice','choose','choice','name','mod_choice'),(74,'choice','choose again','choice','name','mod_choice'),(75,'data','view','data','name','mod_data'),(76,'data','add','data','name','mod_data'),(77,'data','update','data','name','mod_data'),(78,'data','record delete','data','name','mod_data'),(79,'data','fields add','data_fields','name','mod_data'),(80,'data','fields update','data_fields','name','mod_data'),(81,'data','templates saved','data','name','mod_data'),(82,'data','templates def','data','name','mod_data'),(83,'feedback','startcomplete','feedback','name','mod_feedback'),(84,'feedback','submit','feedback','name','mod_feedback'),(85,'feedback','delete','feedback','name','mod_feedback'),(86,'feedback','view','feedback','name','mod_feedback'),(87,'feedback','view all','course','shortname','mod_feedback'),(88,'folder','view','folder','name','mod_folder'),(89,'folder','view all','folder','name','mod_folder'),(90,'folder','update','folder','name','mod_folder'),(91,'folder','add','folder','name','mod_folder'),(92,'forum','add','forum','name','mod_forum'),(93,'forum','update','forum','name','mod_forum'),(94,'forum','add discussion','forum_discussions','name','mod_forum'),(95,'forum','add post','forum_posts','subject','mod_forum'),(96,'forum','update post','forum_posts','subject','mod_forum'),(97,'forum','user report','user','CONCAT(firstname, \' \', lastname)','mod_forum'),(98,'forum','move discussion','forum_discussions','name','mod_forum'),(99,'forum','view subscribers','forum','name','mod_forum'),(100,'forum','view discussion','forum_discussions','name','mod_forum'),(101,'forum','view forum','forum','name','mod_forum'),(102,'forum','subscribe','forum','name','mod_forum'),(103,'forum','unsubscribe','forum','name','mod_forum'),(104,'glossary','add','glossary','name','mod_glossary'),(105,'glossary','update','glossary','name','mod_glossary'),(106,'glossary','view','glossary','name','mod_glossary'),(107,'glossary','view all','glossary','name','mod_glossary'),(108,'glossary','add entry','glossary','name','mod_glossary'),(109,'glossary','update entry','glossary','name','mod_glossary'),(110,'glossary','add category','glossary','name','mod_glossary'),(111,'glossary','update category','glossary','name','mod_glossary'),(112,'glossary','delete category','glossary','name','mod_glossary'),(113,'glossary','approve entry','glossary','name','mod_glossary'),(114,'glossary','disapprove entry','glossary','name','mod_glossary'),(115,'glossary','view entry','glossary_entries','concept','mod_glossary'),(116,'imscp','view','imscp','name','mod_imscp'),(117,'imscp','view all','imscp','name','mod_imscp'),(118,'imscp','update','imscp','name','mod_imscp'),(119,'imscp','add','imscp','name','mod_imscp'),(120,'label','add','label','name','mod_label'),(121,'label','update','label','name','mod_label'),(122,'lesson','start','lesson','name','mod_lesson'),(123,'lesson','end','lesson','name','mod_lesson'),(124,'lesson','view','lesson_pages','title','mod_lesson'),(125,'lti','view','lti','name','mod_lti'),(126,'lti','launch','lti','name','mod_lti'),(127,'lti','view all','lti','name','mod_lti'),(128,'page','view','page','name','mod_page'),(129,'page','view all','page','name','mod_page'),(130,'page','update','page','name','mod_page'),(131,'page','add','page','name','mod_page'),(132,'quiz','add','quiz','name','mod_quiz'),(133,'quiz','update','quiz','name','mod_quiz'),(134,'quiz','view','quiz','name','mod_quiz'),(135,'quiz','report','quiz','name','mod_quiz'),(136,'quiz','attempt','quiz','name','mod_quiz'),(137,'quiz','submit','quiz','name','mod_quiz'),(138,'quiz','review','quiz','name','mod_quiz'),(139,'quiz','editquestions','quiz','name','mod_quiz'),(140,'quiz','preview','quiz','name','mod_quiz'),(141,'quiz','start attempt','quiz','name','mod_quiz'),(142,'quiz','close attempt','quiz','name','mod_quiz'),(143,'quiz','continue attempt','quiz','name','mod_quiz'),(144,'quiz','edit override','quiz','name','mod_quiz'),(145,'quiz','delete override','quiz','name','mod_quiz'),(146,'quiz','view summary','quiz','name','mod_quiz'),(147,'resource','view','resource','name','mod_resource'),(148,'resource','view all','resource','name','mod_resource'),(149,'resource','update','resource','name','mod_resource'),(150,'resource','add','resource','name','mod_resource'),(151,'scorm','view','scorm','name','mod_scorm'),(152,'scorm','review','scorm','name','mod_scorm'),(153,'scorm','update','scorm','name','mod_scorm'),(154,'scorm','add','scorm','name','mod_scorm'),(155,'survey','add','survey','name','mod_survey'),(156,'survey','update','survey','name','mod_survey'),(157,'survey','download','survey','name','mod_survey'),(158,'survey','view form','survey','name','mod_survey'),(159,'survey','view graph','survey','name','mod_survey'),(160,'survey','view report','survey','name','mod_survey'),(161,'survey','submit','survey','name','mod_survey'),(162,'url','view','url','name','mod_url'),(163,'url','view all','url','name','mod_url'),(164,'url','update','url','name','mod_url'),(165,'url','add','url','name','mod_url'),(166,'workshop','add','workshop','name','mod_workshop'),(167,'workshop','update','workshop','name','mod_workshop'),(168,'workshop','view','workshop','name','mod_workshop'),(169,'workshop','view all','workshop','name','mod_workshop'),(170,'workshop','add submission','workshop_submissions','title','mod_workshop'),(171,'workshop','update submission','workshop_submissions','title','mod_workshop'),(172,'workshop','view submission','workshop_submissions','title','mod_workshop'),(173,'workshop','add assessment','workshop_submissions','title','mod_workshop'),(174,'workshop','update assessment','workshop_submissions','title','mod_workshop'),(175,'workshop','add example','workshop_submissions','title','mod_workshop'),(176,'workshop','update example','workshop_submissions','title','mod_workshop'),(177,'workshop','view example','workshop_submissions','title','mod_workshop'),(178,'workshop','add reference assessment','workshop_submissions','title','mod_workshop'),(179,'workshop','update reference assessment','workshop_submissions','title','mod_workshop'),(180,'workshop','add example assessment','workshop_submissions','title','mod_workshop'),(181,'workshop','update example assessment','workshop_submissions','title','mod_workshop'),(182,'workshop','update aggregate grades','workshop','name','mod_workshop'),(183,'workshop','update clear aggregated grades','workshop','name','mod_workshop'),(184,'workshop','update clear assessments','workshop','name','mod_workshop'),(185,'book','exportimscp','book','name','booktool_exportimscp'),(186,'book','print','book','name','booktool_print'),(187,'book','print chapter','book_chapters','title','booktool_print');
/*!40000 ALTER TABLE `mdl_log_display` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_log_queries`
--

DROP TABLE IF EXISTS `mdl_log_queries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_log_queries` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `qtype` mediumint(5) NOT NULL,
  `sqltext` longtext NOT NULL,
  `sqlparams` longtext,
  `error` mediumint(5) NOT NULL DEFAULT '0',
  `info` longtext,
  `backtrace` longtext,
  `exectime` decimal(10,5) NOT NULL,
  `timelogged` bigint(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Logged database queries.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_log_queries`
--

LOCK TABLES `mdl_log_queries` WRITE;
/*!40000 ALTER TABLE `mdl_log_queries` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_log_queries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lti`
--

DROP TABLE IF EXISTS `mdl_lti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lti` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext,
  `introformat` smallint(4) DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `typeid` bigint(10) DEFAULT NULL,
  `toolurl` longtext NOT NULL,
  `securetoolurl` longtext,
  `instructorchoicesendname` tinyint(1) DEFAULT NULL,
  `instructorchoicesendemailaddr` tinyint(1) DEFAULT NULL,
  `instructorchoiceallowroster` tinyint(1) DEFAULT NULL,
  `instructorchoiceallowsetting` tinyint(1) DEFAULT NULL,
  `instructorcustomparameters` varchar(255) DEFAULT NULL,
  `instructorchoiceacceptgrades` tinyint(1) DEFAULT NULL,
  `grade` decimal(10,5) NOT NULL DEFAULT '100.00000',
  `launchcontainer` tinyint(2) NOT NULL DEFAULT '1',
  `resourcekey` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `debuglaunch` tinyint(1) NOT NULL DEFAULT '0',
  `showtitlelaunch` tinyint(1) NOT NULL DEFAULT '0',
  `showdescriptionlaunch` tinyint(1) NOT NULL DEFAULT '0',
  `servicesalt` varchar(40) DEFAULT NULL,
  `icon` longtext,
  `secureicon` longtext,
  PRIMARY KEY (`id`),
  KEY `mdl_lti_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table contains Basic LTI activities instances';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lti`
--

LOCK TABLES `mdl_lti` WRITE;
/*!40000 ALTER TABLE `mdl_lti` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lti_submission`
--

DROP TABLE IF EXISTS `mdl_lti_submission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lti_submission` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `ltiid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `datesubmitted` bigint(10) NOT NULL,
  `dateupdated` bigint(10) NOT NULL,
  `gradepercent` decimal(10,5) NOT NULL,
  `originalgrade` decimal(10,5) NOT NULL,
  `launchid` bigint(10) NOT NULL,
  `state` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_ltisubm_lti_ix` (`ltiid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Keeps track of individual submissions for LTI activities.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lti_submission`
--

LOCK TABLES `mdl_lti_submission` WRITE;
/*!40000 ALTER TABLE `mdl_lti_submission` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lti_submission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lti_types`
--

DROP TABLE IF EXISTS `mdl_lti_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lti_types` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT 'basiclti Activity',
  `baseurl` longtext NOT NULL,
  `tooldomain` varchar(255) NOT NULL DEFAULT '',
  `state` tinyint(2) NOT NULL DEFAULT '2',
  `course` bigint(10) NOT NULL,
  `coursevisible` tinyint(1) NOT NULL DEFAULT '0',
  `createdby` bigint(10) NOT NULL,
  `timecreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_ltitype_cou_ix` (`course`),
  KEY `mdl_ltitype_too_ix` (`tooldomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Basic LTI pre-configured activities';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lti_types`
--

LOCK TABLES `mdl_lti_types` WRITE;
/*!40000 ALTER TABLE `mdl_lti_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lti_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_lti_types_config`
--

DROP TABLE IF EXISTS `mdl_lti_types_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_lti_types_config` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `typeid` bigint(10) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_ltitypeconf_typ_ix` (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Basic LTI types configuration';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_lti_types_config`
--

LOCK TABLES `mdl_lti_types_config` WRITE;
/*!40000 ALTER TABLE `mdl_lti_types_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_lti_types_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_message`
--

DROP TABLE IF EXISTS `mdl_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_message` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `useridfrom` bigint(10) NOT NULL DEFAULT '0',
  `useridto` bigint(10) NOT NULL DEFAULT '0',
  `subject` longtext,
  `fullmessage` longtext,
  `fullmessageformat` smallint(4) DEFAULT '0',
  `fullmessagehtml` longtext,
  `smallmessage` longtext,
  `notification` tinyint(1) DEFAULT '0',
  `contexturl` longtext,
  `contexturlname` longtext,
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_mess_use_ix` (`useridfrom`),
  KEY `mdl_mess_use2_ix` (`useridto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores all unread messages';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_message`
--

LOCK TABLES `mdl_message` WRITE;
/*!40000 ALTER TABLE `mdl_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_message_contacts`
--

DROP TABLE IF EXISTS `mdl_message_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_message_contacts` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `contactid` bigint(10) NOT NULL DEFAULT '0',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_messcont_usecon_uix` (`userid`,`contactid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Maintains lists of relationships between users';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_message_contacts`
--

LOCK TABLES `mdl_message_contacts` WRITE;
/*!40000 ALTER TABLE `mdl_message_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_message_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_message_processors`
--

DROP TABLE IF EXISTS `mdl_message_processors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_message_processors` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(166) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='List of message output plugins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_message_processors`
--

LOCK TABLES `mdl_message_processors` WRITE;
/*!40000 ALTER TABLE `mdl_message_processors` DISABLE KEYS */;
INSERT INTO `mdl_message_processors` VALUES (1,'email',1),(2,'jabber',1),(3,'popup',1);
/*!40000 ALTER TABLE `mdl_message_processors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_message_providers`
--

DROP TABLE IF EXISTS `mdl_message_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_message_providers` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `component` varchar(200) NOT NULL DEFAULT '',
  `capability` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_messprov_comnam_uix` (`component`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='This table stores the message providers (modules and core sy';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_message_providers`
--

LOCK TABLES `mdl_message_providers` WRITE;
/*!40000 ALTER TABLE `mdl_message_providers` DISABLE KEYS */;
INSERT INTO `mdl_message_providers` VALUES (1,'notices','moodle','moodle/site:config'),(2,'errors','moodle','moodle/site:config'),(3,'availableupdate','moodle','moodle/site:config'),(4,'instantmessage','moodle',NULL),(5,'backup','moodle','moodle/site:config'),(6,'courserequested','moodle','moodle/site:approvecourse'),(7,'courserequestapproved','moodle','moodle/course:request'),(8,'courserequestrejected','moodle','moodle/course:request'),(9,'badgerecipientnotice','moodle','moodle/badges:earnbadge'),(10,'badgecreatornotice','moodle',NULL),(11,'assign_notification','mod_assign',NULL),(12,'assignment_updates','mod_assignment',NULL),(13,'submission','mod_feedback',NULL),(14,'message','mod_feedback',NULL),(15,'posts','mod_forum',NULL),(16,'graded_essay','mod_lesson',NULL),(17,'submission','mod_quiz','mod/quiz:emailnotifysubmission'),(18,'confirmation','mod_quiz','mod/quiz:emailconfirmsubmission'),(19,'attempt_overdue','mod_quiz','mod/quiz:emailwarnoverdue'),(20,'flatfile_enrolment','enrol_flatfile',NULL),(21,'imsenterprise_enrolment','enrol_imsenterprise',NULL),(22,'expiry_notification','enrol_manual',NULL),(23,'paypal_enrolment','enrol_paypal',NULL),(24,'expiry_notification','enrol_self',NULL);
/*!40000 ALTER TABLE `mdl_message_providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_message_read`
--

DROP TABLE IF EXISTS `mdl_message_read`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_message_read` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `useridfrom` bigint(10) NOT NULL DEFAULT '0',
  `useridto` bigint(10) NOT NULL DEFAULT '0',
  `subject` longtext,
  `fullmessage` longtext,
  `fullmessageformat` smallint(4) DEFAULT '0',
  `fullmessagehtml` longtext,
  `smallmessage` longtext,
  `notification` tinyint(1) DEFAULT '0',
  `contexturl` longtext,
  `contexturlname` longtext,
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timeread` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_messread_use_ix` (`useridfrom`),
  KEY `mdl_messread_use2_ix` (`useridto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores all messages that have been read';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_message_read`
--

LOCK TABLES `mdl_message_read` WRITE;
/*!40000 ALTER TABLE `mdl_message_read` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_message_read` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_message_working`
--

DROP TABLE IF EXISTS `mdl_message_working`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_message_working` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `unreadmessageid` bigint(10) NOT NULL,
  `processorid` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_messwork_unr_ix` (`unreadmessageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Lists all the messages and processors that need to be proces';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_message_working`
--

LOCK TABLES `mdl_message_working` WRITE;
/*!40000 ALTER TABLE `mdl_message_working` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_message_working` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_application`
--

DROP TABLE IF EXISTS `mdl_mnet_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_application` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `display_name` varchar(50) NOT NULL DEFAULT '',
  `xmlrpc_server_url` varchar(255) NOT NULL DEFAULT '',
  `sso_land_url` varchar(255) NOT NULL DEFAULT '',
  `sso_jump_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Information about applications on remote hosts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_application`
--

LOCK TABLES `mdl_mnet_application` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_application` DISABLE KEYS */;
INSERT INTO `mdl_mnet_application` VALUES (1,'moodle','Moodle','/mnet/xmlrpc/server.php','/auth/mnet/land.php','/auth/mnet/jump.php'),(2,'mahara','Mahara','/api/xmlrpc/server.php','/auth/xmlrpc/land.php','/auth/xmlrpc/jump.php');
/*!40000 ALTER TABLE `mdl_mnet_application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_host`
--

DROP TABLE IF EXISTS `mdl_mnet_host`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_host` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `wwwroot` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varchar(45) NOT NULL DEFAULT '',
  `name` varchar(80) NOT NULL DEFAULT '',
  `public_key` longtext NOT NULL,
  `public_key_expires` bigint(10) NOT NULL DEFAULT '0',
  `transport` tinyint(2) NOT NULL DEFAULT '0',
  `portno` mediumint(5) NOT NULL DEFAULT '0',
  `last_connect_time` bigint(10) NOT NULL DEFAULT '0',
  `last_log_id` bigint(10) NOT NULL DEFAULT '0',
  `force_theme` tinyint(1) NOT NULL DEFAULT '0',
  `theme` varchar(100) DEFAULT NULL,
  `applicationid` bigint(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_mnethost_app_ix` (`applicationid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Information about the local and remote hosts for RPC';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_host`
--

LOCK TABLES `mdl_mnet_host` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_host` DISABLE KEYS */;
INSERT INTO `mdl_mnet_host` VALUES (1,0,'http://127.0.0.1','127.0.0.1','','',0,0,0,0,0,0,NULL,1),(2,0,'','','All Hosts','',0,0,0,0,0,0,NULL,1);
/*!40000 ALTER TABLE `mdl_mnet_host` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_host2service`
--

DROP TABLE IF EXISTS `mdl_mnet_host2service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_host2service` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `hostid` bigint(10) NOT NULL DEFAULT '0',
  `serviceid` bigint(10) NOT NULL DEFAULT '0',
  `publish` tinyint(1) NOT NULL DEFAULT '0',
  `subscribe` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnethost_hosser_uix` (`hostid`,`serviceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Information about the services for a given host';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_host2service`
--

LOCK TABLES `mdl_mnet_host2service` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_host2service` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_mnet_host2service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_log`
--

DROP TABLE IF EXISTS `mdl_mnet_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_log` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `hostid` bigint(10) NOT NULL DEFAULT '0',
  `remoteid` bigint(10) NOT NULL DEFAULT '0',
  `time` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `ip` varchar(45) NOT NULL DEFAULT '',
  `course` bigint(10) NOT NULL DEFAULT '0',
  `coursename` varchar(40) NOT NULL DEFAULT '',
  `module` varchar(20) NOT NULL DEFAULT '',
  `cmid` bigint(10) NOT NULL DEFAULT '0',
  `action` varchar(40) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_mnetlog_hosusecou_ix` (`hostid`,`userid`,`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Store session data from users migrating to other sites';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_log`
--

LOCK TABLES `mdl_mnet_log` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_mnet_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_remote_rpc`
--

DROP TABLE IF EXISTS `mdl_mnet_remote_rpc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_remote_rpc` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `functionname` varchar(40) NOT NULL DEFAULT '',
  `xmlrpcpath` varchar(80) NOT NULL DEFAULT '',
  `plugintype` varchar(20) NOT NULL DEFAULT '',
  `pluginname` varchar(20) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='This table describes functions that might be called remotely';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_remote_rpc`
--

LOCK TABLES `mdl_mnet_remote_rpc` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_remote_rpc` DISABLE KEYS */;
INSERT INTO `mdl_mnet_remote_rpc` VALUES (1,'user_authorise','auth/mnet/auth.php/user_authorise','auth','mnet',1),(2,'keepalive_server','auth/mnet/auth.php/keepalive_server','auth','mnet',1),(3,'kill_children','auth/mnet/auth.php/kill_children','auth','mnet',1),(4,'refresh_log','auth/mnet/auth.php/refresh_log','auth','mnet',1),(5,'fetch_user_image','auth/mnet/auth.php/fetch_user_image','auth','mnet',1),(6,'fetch_theme_info','auth/mnet/auth.php/fetch_theme_info','auth','mnet',1),(7,'update_enrolments','auth/mnet/auth.php/update_enrolments','auth','mnet',1),(8,'keepalive_client','auth/mnet/auth.php/keepalive_client','auth','mnet',1),(9,'kill_child','auth/mnet/auth.php/kill_child','auth','mnet',1),(10,'available_courses','enrol/mnet/enrol.php/available_courses','enrol','mnet',1),(11,'user_enrolments','enrol/mnet/enrol.php/user_enrolments','enrol','mnet',1),(12,'enrol_user','enrol/mnet/enrol.php/enrol_user','enrol','mnet',1),(13,'unenrol_user','enrol/mnet/enrol.php/unenrol_user','enrol','mnet',1),(14,'course_enrolments','enrol/mnet/enrol.php/course_enrolments','enrol','mnet',1),(15,'send_content_intent','portfolio/mahara/lib.php/send_content_intent','portfolio','mahara',1),(16,'send_content_ready','portfolio/mahara/lib.php/send_content_ready','portfolio','mahara',1);
/*!40000 ALTER TABLE `mdl_mnet_remote_rpc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_remote_service2rpc`
--

DROP TABLE IF EXISTS `mdl_mnet_remote_service2rpc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_remote_service2rpc` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `serviceid` bigint(10) NOT NULL DEFAULT '0',
  `rpcid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnetremoserv_rpcser_uix` (`rpcid`,`serviceid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='Group functions or methods under a service';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_remote_service2rpc`
--

LOCK TABLES `mdl_mnet_remote_service2rpc` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_remote_service2rpc` DISABLE KEYS */;
INSERT INTO `mdl_mnet_remote_service2rpc` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,2,8),(9,2,9),(10,3,10),(11,3,11),(12,3,12),(13,3,13),(14,3,14),(15,4,15),(16,4,16);
/*!40000 ALTER TABLE `mdl_mnet_remote_service2rpc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_rpc`
--

DROP TABLE IF EXISTS `mdl_mnet_rpc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_rpc` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `functionname` varchar(40) NOT NULL DEFAULT '',
  `xmlrpcpath` varchar(80) NOT NULL DEFAULT '',
  `plugintype` varchar(20) NOT NULL DEFAULT '',
  `pluginname` varchar(20) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `help` longtext NOT NULL,
  `profile` longtext NOT NULL,
  `filename` varchar(100) NOT NULL DEFAULT '',
  `classname` varchar(150) DEFAULT NULL,
  `static` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_mnetrpc_enaxml_ix` (`enabled`,`xmlrpcpath`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='Functions or methods that we may publish or subscribe to';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_rpc`
--

LOCK TABLES `mdl_mnet_rpc` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_rpc` DISABLE KEYS */;
INSERT INTO `mdl_mnet_rpc` VALUES (1,'user_authorise','auth/mnet/auth.php/user_authorise','auth','mnet',1,'Return user data for the provided token, compare with user_agent string.','a:2:{s:10:\"parameters\";a:2:{i:0;a:3:{s:4:\"name\";s:5:\"token\";s:4:\"type\";s:6:\"string\";s:11:\"description\";s:37:\"The unique ID provided by remotehost.\";}i:1;a:3:{s:4:\"name\";s:9:\"useragent\";s:4:\"type\";s:6:\"string\";s:11:\"description\";s:0:\"\";}}s:6:\"return\";a:2:{s:4:\"type\";s:5:\"array\";s:11:\"description\";s:44:\"$userdata Array of user info for remote host\";}}','auth.php','auth_plugin_mnet',0),(2,'keepalive_server','auth/mnet/auth.php/keepalive_server','auth','mnet',1,'Receives an array of usernames from a remote machine and prods their\nsessions to keep them alive','a:2:{s:10:\"parameters\";a:1:{i:0;a:3:{s:4:\"name\";s:5:\"array\";s:4:\"type\";s:5:\"array\";s:11:\"description\";s:21:\"An array of usernames\";}}s:6:\"return\";a:2:{s:4:\"type\";s:6:\"string\";s:11:\"description\";s:28:\"\"All ok\" or an error message\";}}','auth.php','auth_plugin_mnet',0),(3,'kill_children','auth/mnet/auth.php/kill_children','auth','mnet',1,'The IdP uses this function to kill child sessions on other hosts','a:2:{s:10:\"parameters\";a:2:{i:0;a:3:{s:4:\"name\";s:8:\"username\";s:4:\"type\";s:6:\"string\";s:11:\"description\";s:28:\"Username for session to kill\";}i:1;a:3:{s:4:\"name\";s:9:\"useragent\";s:4:\"type\";s:6:\"string\";s:11:\"description\";s:0:\"\";}}s:6:\"return\";a:2:{s:4:\"type\";s:6:\"string\";s:11:\"description\";s:39:\"A plaintext report of what has happened\";}}','auth.php','auth_plugin_mnet',0),(4,'refresh_log','auth/mnet/auth.php/refresh_log','auth','mnet',1,'Receives an array of log entries from an SP and adds them to the mnet_log\ntable','a:2:{s:10:\"parameters\";a:1:{i:0;a:3:{s:4:\"name\";s:5:\"array\";s:4:\"type\";s:5:\"array\";s:11:\"description\";s:21:\"An array of usernames\";}}s:6:\"return\";a:2:{s:4:\"type\";s:6:\"string\";s:11:\"description\";s:28:\"\"All ok\" or an error message\";}}','auth.php','auth_plugin_mnet',0),(5,'fetch_user_image','auth/mnet/auth.php/fetch_user_image','auth','mnet',1,'Returns the user\'s profile image info\nIf the user exists and has a profile picture, the returned array will contain keys:\n f1          - the content of the default 100x100px image\n f1_mimetype - the mimetype of the f1 file\n f2          - the content of the 35x35px variant of the image\n f2_mimetype - the mimetype of the f2 file\nThe mimetype information was added in Moodle 2.0. In Moodle 1.x, images are always jpegs.','a:2:{s:10:\"parameters\";a:1:{i:0;a:3:{s:4:\"name\";s:8:\"username\";s:4:\"type\";s:3:\"int\";s:11:\"description\";s:18:\"The id of the user\";}}s:6:\"return\";a:2:{s:4:\"type\";s:5:\"array\";s:11:\"description\";s:84:\"false if user not found, empty array if no picture exists, array with data otherwise\";}}','auth.php','auth_plugin_mnet',0),(6,'fetch_theme_info','auth/mnet/auth.php/fetch_theme_info','auth','mnet',1,'Returns the theme information and logo url as strings.','a:2:{s:10:\"parameters\";a:0:{}s:6:\"return\";a:2:{s:4:\"type\";s:6:\"string\";s:11:\"description\";s:14:\"The theme info\";}}','auth.php','auth_plugin_mnet',0),(7,'update_enrolments','auth/mnet/auth.php/update_enrolments','auth','mnet',1,'Invoke this function _on_ the IDP to update it with enrolment info local to\nthe SP right after calling user_authorise()\nNormally called by the SP after calling user_authorise()','a:2:{s:10:\"parameters\";a:2:{i:0;a:3:{s:4:\"name\";s:8:\"username\";s:4:\"type\";s:6:\"string\";s:11:\"description\";s:12:\"The username\";}i:1;a:3:{s:4:\"name\";s:7:\"courses\";s:4:\"type\";s:5:\"array\";s:11:\"description\";s:0:\"\";}}s:6:\"return\";a:2:{s:4:\"type\";s:4:\"bool\";s:11:\"description\";s:0:\"\";}}','auth.php','auth_plugin_mnet',0),(8,'keepalive_client','auth/mnet/auth.php/keepalive_client','auth','mnet',1,'Poll the IdP server to let it know that a user it has authenticated is still\nonline','a:2:{s:10:\"parameters\";a:0:{}s:6:\"return\";a:2:{s:4:\"type\";s:4:\"void\";s:11:\"description\";s:0:\"\";}}','auth.php','auth_plugin_mnet',0),(9,'kill_child','auth/mnet/auth.php/kill_child','auth','mnet',1,'When the IdP requests that child sessions are terminated,\nthis function will be called on each of the child hosts. The machine that\ncalls the function (over xmlrpc) provides us with the mnethostid we need.','a:2:{s:10:\"parameters\";a:2:{i:0;a:3:{s:4:\"name\";s:8:\"username\";s:4:\"type\";s:6:\"string\";s:11:\"description\";s:28:\"Username for session to kill\";}i:1;a:3:{s:4:\"name\";s:9:\"useragent\";s:4:\"type\";s:6:\"string\";s:11:\"description\";s:0:\"\";}}s:6:\"return\";a:2:{s:4:\"type\";s:4:\"bool\";s:11:\"description\";s:15:\"True on success\";}}','auth.php','auth_plugin_mnet',0),(10,'available_courses','enrol/mnet/enrol.php/available_courses','enrol','mnet',1,'Returns list of courses that we offer to the caller for remote enrolment of their users\nSince Moodle 2.0, courses are made available for MNet peers by creating an instance\nof enrol_mnet plugin for the course. Hidden courses are not returned. If there are two\ninstances - one specific for the host and one for \'All hosts\', the setting of the specific\none is used. The id of the peer is kept in customint1, no other custom fields are used.','a:2:{s:10:\"parameters\";a:0:{}s:6:\"return\";a:2:{s:4:\"type\";s:5:\"array\";s:11:\"description\";s:0:\"\";}}','enrol.php','enrol_mnet_mnetservice_enrol',0),(11,'user_enrolments','enrol/mnet/enrol.php/user_enrolments','enrol','mnet',1,'This method has never been implemented in Moodle MNet API','a:2:{s:10:\"parameters\";a:0:{}s:6:\"return\";a:2:{s:4:\"type\";s:5:\"array\";s:11:\"description\";s:11:\"empty array\";}}','enrol.php','enrol_mnet_mnetservice_enrol',0),(12,'enrol_user','enrol/mnet/enrol.php/enrol_user','enrol','mnet',1,'Enrol remote user to our course\nIf we do not have local record for the remote user in our database,\nit gets created here.','a:2:{s:10:\"parameters\";a:2:{i:0;a:3:{s:4:\"name\";s:8:\"userdata\";s:4:\"type\";s:5:\"array\";s:11:\"description\";s:14:\"user details {\";}i:1;a:3:{s:4:\"name\";s:8:\"courseid\";s:4:\"type\";s:3:\"int\";s:11:\"description\";s:19:\"our local course id\";}}s:6:\"return\";a:2:{s:4:\"type\";s:4:\"bool\";s:11:\"description\";s:69:\"true if the enrolment has been successful, throws exception otherwise\";}}','enrol.php','enrol_mnet_mnetservice_enrol',0),(13,'unenrol_user','enrol/mnet/enrol.php/unenrol_user','enrol','mnet',1,'Unenrol remote user from our course\nOnly users enrolled via enrol_mnet plugin can be unenrolled remotely. If the\nremote user is enrolled into the local course via some other enrol plugin\n(enrol_manual for example), the remote host can\'t touch such enrolment. Please\ndo not report this behaviour as bug, it is a feature ;-)','a:2:{s:10:\"parameters\";a:2:{i:0;a:3:{s:4:\"name\";s:8:\"username\";s:4:\"type\";s:6:\"string\";s:11:\"description\";s:18:\"of the remote user\";}i:1;a:3:{s:4:\"name\";s:8:\"courseid\";s:4:\"type\";s:3:\"int\";s:11:\"description\";s:0:\"\";}}s:6:\"return\";a:2:{s:4:\"type\";s:4:\"bool\";s:11:\"description\";s:71:\"true if the unenrolment has been successful, throws exception otherwise\";}}','enrol.php','enrol_mnet_mnetservice_enrol',0),(14,'course_enrolments','enrol/mnet/enrol.php/course_enrolments','enrol','mnet',1,'Returns a list of users from the client server who are enrolled in our course\nSuitable instance of enrol_mnet must be created in the course. This method will not\nreturn any information about the enrolments in courses that are not available for\nremote enrolment, even if their users are enrolled into them via other plugin\n(note the difference from {@link self::user_enrolments()}).\nThis method will return enrolment information for users from hosts regardless\nthe enrolment plugin. It does not matter if the user was enrolled remotely by\ntheir admin or locally. Once the course is available for remote enrolments, we\nwill tell them everything about their users.\nIn Moodle 1.x the returned array used to be indexed by username. The side effect\nof MDL-19219 fix is that we do not need to use such index and therefore we can\nreturn all enrolment records. MNet clients 1.x will only use the last record for\nthe student, if she is enrolled via multiple plugins.','a:2:{s:10:\"parameters\";a:2:{i:0;a:3:{s:4:\"name\";s:8:\"courseid\";s:4:\"type\";s:3:\"int\";s:11:\"description\";s:16:\"ID of our course\";}i:1;a:3:{s:4:\"name\";s:5:\"roles\";s:4:\"type\";s:5:\"array\";s:11:\"description\";s:0:\"\";}}s:6:\"return\";a:2:{s:4:\"type\";s:5:\"array\";s:11:\"description\";s:0:\"\";}}','enrol.php','enrol_mnet_mnetservice_enrol',0),(15,'fetch_file','portfolio/mahara/lib.php/fetch_file','portfolio','mahara',1,'xmlrpc (mnet) function to get the file.\nreads in the file and returns it base_64 encoded\nso that it can be enrypted by mnet.','a:2:{s:10:\"parameters\";a:1:{i:0;a:3:{s:4:\"name\";s:5:\"token\";s:4:\"type\";s:6:\"string\";s:11:\"description\";s:56:\"the token recieved previously during send_content_intent\";}}s:6:\"return\";a:2:{s:4:\"type\";s:4:\"void\";s:11:\"description\";s:0:\"\";}}','lib.php','portfolio_plugin_mahara',1);
/*!40000 ALTER TABLE `mdl_mnet_rpc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_service`
--

DROP TABLE IF EXISTS `mdl_mnet_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_service` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL DEFAULT '',
  `description` varchar(40) NOT NULL DEFAULT '',
  `apiversion` varchar(10) NOT NULL DEFAULT '',
  `offer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='A service is a group of functions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_service`
--

LOCK TABLES `mdl_mnet_service` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_service` DISABLE KEYS */;
INSERT INTO `mdl_mnet_service` VALUES (1,'sso_idp','','1',1),(2,'sso_sp','','1',1),(3,'mnet_enrol','','1',1),(4,'pf','','1',1);
/*!40000 ALTER TABLE `mdl_mnet_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_service2rpc`
--

DROP TABLE IF EXISTS `mdl_mnet_service2rpc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_service2rpc` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `serviceid` bigint(10) NOT NULL DEFAULT '0',
  `rpcid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnetserv_rpcser_uix` (`rpcid`,`serviceid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='Group functions or methods under a service';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_service2rpc`
--

LOCK TABLES `mdl_mnet_service2rpc` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_service2rpc` DISABLE KEYS */;
INSERT INTO `mdl_mnet_service2rpc` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,2,8),(9,2,9),(10,3,10),(11,3,11),(12,3,12),(13,3,13),(14,3,14),(15,4,15);
/*!40000 ALTER TABLE `mdl_mnet_service2rpc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_session`
--

DROP TABLE IF EXISTS `mdl_mnet_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_session` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `username` varchar(100) NOT NULL DEFAULT '',
  `token` varchar(40) NOT NULL DEFAULT '',
  `mnethostid` bigint(10) NOT NULL DEFAULT '0',
  `useragent` varchar(40) NOT NULL DEFAULT '',
  `confirm_timeout` bigint(10) NOT NULL DEFAULT '0',
  `session_id` varchar(40) NOT NULL DEFAULT '',
  `expires` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnetsess_tok_uix` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Store session data from users migrating to other sites';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_session`
--

LOCK TABLES `mdl_mnet_session` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_session` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_mnet_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnet_sso_access_control`
--

DROP TABLE IF EXISTS `mdl_mnet_sso_access_control`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnet_sso_access_control` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '',
  `mnet_host_id` bigint(10) NOT NULL DEFAULT '0',
  `accessctrl` varchar(20) NOT NULL DEFAULT 'allow',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnetssoaccecont_mneuse_uix` (`mnet_host_id`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users by host permitted (or not) to login from a remote prov';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnet_sso_access_control`
--

LOCK TABLES `mdl_mnet_sso_access_control` WRITE;
/*!40000 ALTER TABLE `mdl_mnet_sso_access_control` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_mnet_sso_access_control` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnetservice_enrol_courses`
--

DROP TABLE IF EXISTS `mdl_mnetservice_enrol_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnetservice_enrol_courses` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `hostid` bigint(10) NOT NULL,
  `remoteid` bigint(10) NOT NULL,
  `categoryid` bigint(10) NOT NULL,
  `categoryname` varchar(255) NOT NULL DEFAULT '',
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  `fullname` varchar(254) NOT NULL DEFAULT '',
  `shortname` varchar(100) NOT NULL DEFAULT '',
  `idnumber` varchar(100) NOT NULL DEFAULT '',
  `summary` longtext NOT NULL,
  `summaryformat` smallint(3) DEFAULT '0',
  `startdate` bigint(10) NOT NULL,
  `roleid` bigint(10) NOT NULL,
  `rolename` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_mnetenrocour_hosrem_uix` (`hostid`,`remoteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Caches the information fetched via XML-RPC about courses on ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnetservice_enrol_courses`
--

LOCK TABLES `mdl_mnetservice_enrol_courses` WRITE;
/*!40000 ALTER TABLE `mdl_mnetservice_enrol_courses` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_mnetservice_enrol_courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_mnetservice_enrol_enrolments`
--

DROP TABLE IF EXISTS `mdl_mnetservice_enrol_enrolments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_mnetservice_enrol_enrolments` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `hostid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `remotecourseid` bigint(10) NOT NULL,
  `rolename` varchar(255) NOT NULL DEFAULT '',
  `enroltime` bigint(10) NOT NULL DEFAULT '0',
  `enroltype` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_mnetenroenro_use_ix` (`userid`),
  KEY `mdl_mnetenroenro_hos_ix` (`hostid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Caches the information about enrolments of our local users i';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_mnetservice_enrol_enrolments`
--

LOCK TABLES `mdl_mnetservice_enrol_enrolments` WRITE;
/*!40000 ALTER TABLE `mdl_mnetservice_enrol_enrolments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_mnetservice_enrol_enrolments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_modules`
--

DROP TABLE IF EXISTS `mdl_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_modules` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `cron` bigint(10) NOT NULL DEFAULT '0',
  `lastcron` bigint(10) NOT NULL DEFAULT '0',
  `search` varchar(255) NOT NULL DEFAULT '',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_modu_nam_ix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='modules available in the site';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_modules`
--

LOCK TABLES `mdl_modules` WRITE;
/*!40000 ALTER TABLE `mdl_modules` DISABLE KEYS */;
INSERT INTO `mdl_modules` VALUES (1,'assign',60,0,'',1),(2,'assignment',60,0,'',0),(3,'book',0,0,'',1),(4,'chat',300,1390473608,'',1),(5,'choice',0,0,'',1),(6,'data',0,0,'',1),(7,'feedback',0,0,'',0),(8,'folder',0,0,'',1),(9,'forum',60,1390473608,'',1),(10,'glossary',0,0,'',1),(11,'imscp',0,0,'',1),(12,'label',0,0,'',1),(13,'lesson',0,0,'',1),(14,'lti',0,0,'',1),(15,'page',0,0,'',1),(16,'quiz',60,1390473608,'',1),(17,'resource',0,0,'',1),(18,'scorm',300,1390473608,'',1),(19,'survey',0,0,'',1),(20,'url',0,0,'',1),(21,'wiki',0,0,'',1),(22,'workshop',60,1390473608,'',1);
/*!40000 ALTER TABLE `mdl_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_my_pages`
--

DROP TABLE IF EXISTS `mdl_my_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_my_pages` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) DEFAULT '0',
  `name` varchar(200) NOT NULL DEFAULT '',
  `private` tinyint(1) NOT NULL DEFAULT '1',
  `sortorder` mediumint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_mypage_usepri_ix` (`userid`,`private`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Extra user pages for the My Moodle system';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_my_pages`
--

LOCK TABLES `mdl_my_pages` WRITE;
/*!40000 ALTER TABLE `mdl_my_pages` DISABLE KEYS */;
INSERT INTO `mdl_my_pages` VALUES (1,NULL,'__default',0,0),(2,NULL,'__default',1,0);
/*!40000 ALTER TABLE `mdl_my_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_page`
--

DROP TABLE IF EXISTS `mdl_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_page` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `content` longtext,
  `contentformat` smallint(4) NOT NULL DEFAULT '0',
  `legacyfiles` smallint(4) NOT NULL DEFAULT '0',
  `legacyfileslast` bigint(10) DEFAULT NULL,
  `display` smallint(4) NOT NULL DEFAULT '0',
  `displayoptions` longtext,
  `revision` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_page_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Each record is one page and its config data';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_page`
--

LOCK TABLES `mdl_page` WRITE;
/*!40000 ALTER TABLE `mdl_page` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_portfolio_instance`
--

DROP TABLE IF EXISTS `mdl_portfolio_instance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_portfolio_instance` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='base table (not including config data) for instances of port';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_portfolio_instance`
--

LOCK TABLES `mdl_portfolio_instance` WRITE;
/*!40000 ALTER TABLE `mdl_portfolio_instance` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_portfolio_instance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_portfolio_instance_config`
--

DROP TABLE IF EXISTS `mdl_portfolio_instance_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_portfolio_instance_config` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `instance` bigint(10) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` longtext,
  PRIMARY KEY (`id`),
  KEY `mdl_portinstconf_nam_ix` (`name`),
  KEY `mdl_portinstconf_ins_ix` (`instance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='config for portfolio plugin instances';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_portfolio_instance_config`
--

LOCK TABLES `mdl_portfolio_instance_config` WRITE;
/*!40000 ALTER TABLE `mdl_portfolio_instance_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_portfolio_instance_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_portfolio_instance_user`
--

DROP TABLE IF EXISTS `mdl_portfolio_instance_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_portfolio_instance_user` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `instance` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` longtext,
  PRIMARY KEY (`id`),
  KEY `mdl_portinstuser_ins_ix` (`instance`),
  KEY `mdl_portinstuser_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='user data for portfolio instances.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_portfolio_instance_user`
--

LOCK TABLES `mdl_portfolio_instance_user` WRITE;
/*!40000 ALTER TABLE `mdl_portfolio_instance_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_portfolio_instance_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_portfolio_log`
--

DROP TABLE IF EXISTS `mdl_portfolio_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_portfolio_log` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL,
  `time` bigint(10) NOT NULL,
  `portfolio` bigint(10) NOT NULL,
  `caller_class` varchar(150) NOT NULL DEFAULT '',
  `caller_file` varchar(255) NOT NULL DEFAULT '',
  `caller_component` varchar(255) DEFAULT NULL,
  `caller_sha1` varchar(255) NOT NULL DEFAULT '',
  `tempdataid` bigint(10) NOT NULL DEFAULT '0',
  `returnurl` varchar(255) NOT NULL DEFAULT '',
  `continueurl` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_portlog_use_ix` (`userid`),
  KEY `mdl_portlog_por_ix` (`portfolio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='log of portfolio transfers (used to later check for duplicat';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_portfolio_log`
--

LOCK TABLES `mdl_portfolio_log` WRITE;
/*!40000 ALTER TABLE `mdl_portfolio_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_portfolio_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_portfolio_mahara_queue`
--

DROP TABLE IF EXISTS `mdl_portfolio_mahara_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_portfolio_mahara_queue` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `transferid` bigint(10) NOT NULL,
  `token` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_portmahaqueu_tok_ix` (`token`),
  KEY `mdl_portmahaqueu_tra_ix` (`transferid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='maps mahara tokens to transfer ids';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_portfolio_mahara_queue`
--

LOCK TABLES `mdl_portfolio_mahara_queue` WRITE;
/*!40000 ALTER TABLE `mdl_portfolio_mahara_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_portfolio_mahara_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_portfolio_tempdata`
--

DROP TABLE IF EXISTS `mdl_portfolio_tempdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_portfolio_tempdata` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `data` longtext,
  `expirytime` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `instance` bigint(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_porttemp_use_ix` (`userid`),
  KEY `mdl_porttemp_ins_ix` (`instance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='stores temporary data for portfolio exports. the id of this ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_portfolio_tempdata`
--

LOCK TABLES `mdl_portfolio_tempdata` WRITE;
/*!40000 ALTER TABLE `mdl_portfolio_tempdata` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_portfolio_tempdata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_post`
--

DROP TABLE IF EXISTS `mdl_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_post` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(20) NOT NULL DEFAULT '',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `groupid` bigint(10) NOT NULL DEFAULT '0',
  `moduleid` bigint(10) NOT NULL DEFAULT '0',
  `coursemoduleid` bigint(10) NOT NULL DEFAULT '0',
  `subject` varchar(128) NOT NULL DEFAULT '',
  `summary` longtext,
  `content` longtext,
  `uniquehash` varchar(255) NOT NULL DEFAULT '',
  `rating` bigint(10) NOT NULL DEFAULT '0',
  `format` bigint(10) NOT NULL DEFAULT '0',
  `summaryformat` tinyint(2) NOT NULL DEFAULT '0',
  `attachment` varchar(100) DEFAULT NULL,
  `publishstate` varchar(20) NOT NULL DEFAULT 'draft',
  `lastmodified` bigint(10) NOT NULL DEFAULT '0',
  `created` bigint(10) NOT NULL DEFAULT '0',
  `usermodified` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_post_iduse_uix` (`id`,`userid`),
  KEY `mdl_post_las_ix` (`lastmodified`),
  KEY `mdl_post_mod_ix` (`module`),
  KEY `mdl_post_sub_ix` (`subject`),
  KEY `mdl_post_use_ix` (`usermodified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Generic post table to hold data blog entries etc in differen';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_post`
--

LOCK TABLES `mdl_post` WRITE;
/*!40000 ALTER TABLE `mdl_post` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_profiling`
--

DROP TABLE IF EXISTS `mdl_profiling`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_profiling` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `runid` varchar(32) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `totalexecutiontime` bigint(10) NOT NULL,
  `totalcputime` bigint(10) NOT NULL,
  `totalcalls` bigint(10) NOT NULL,
  `totalmemory` bigint(10) NOT NULL,
  `runreference` tinyint(2) NOT NULL DEFAULT '0',
  `runcomment` varchar(255) NOT NULL DEFAULT '',
  `timecreated` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_prof_run_uix` (`runid`),
  KEY `mdl_prof_urlrun_ix` (`url`,`runreference`),
  KEY `mdl_prof_timrun_ix` (`timecreated`,`runreference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the results of all the profiling runs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_profiling`
--

LOCK TABLES `mdl_profiling` WRITE;
/*!40000 ALTER TABLE `mdl_profiling` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_profiling` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_qtype_essay_options`
--

DROP TABLE IF EXISTS `mdl_qtype_essay_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_qtype_essay_options` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `questionid` bigint(10) NOT NULL,
  `responseformat` varchar(16) NOT NULL DEFAULT 'editor',
  `responsefieldlines` smallint(4) NOT NULL DEFAULT '15',
  `attachments` smallint(4) NOT NULL DEFAULT '0',
  `graderinfo` longtext,
  `graderinfoformat` smallint(4) NOT NULL DEFAULT '0',
  `responsetemplate` longtext,
  `responsetemplateformat` smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_qtypessaopti_que_uix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Extra options for essay questions.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_qtype_essay_options`
--

LOCK TABLES `mdl_qtype_essay_options` WRITE;
/*!40000 ALTER TABLE `mdl_qtype_essay_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_qtype_essay_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_qtype_match_options`
--

DROP TABLE IF EXISTS `mdl_qtype_match_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_qtype_match_options` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `questionid` bigint(10) NOT NULL DEFAULT '0',
  `shuffleanswers` smallint(4) NOT NULL DEFAULT '1',
  `correctfeedback` longtext NOT NULL,
  `correctfeedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext NOT NULL,
  `partiallycorrectfeedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext NOT NULL,
  `incorrectfeedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  `shownumcorrect` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_qtypmatcopti_que_uix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines the question-type specific options for matching ques';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_qtype_match_options`
--

LOCK TABLES `mdl_qtype_match_options` WRITE;
/*!40000 ALTER TABLE `mdl_qtype_match_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_qtype_match_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_qtype_match_subquestions`
--

DROP TABLE IF EXISTS `mdl_qtype_match_subquestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_qtype_match_subquestions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `questionid` bigint(10) NOT NULL DEFAULT '0',
  `questiontext` longtext NOT NULL,
  `questiontextformat` tinyint(2) NOT NULL DEFAULT '0',
  `answertext` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_qtypmatcsubq_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The subquestions that make up a matching question';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_qtype_match_subquestions`
--

LOCK TABLES `mdl_qtype_match_subquestions` WRITE;
/*!40000 ALTER TABLE `mdl_qtype_match_subquestions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_qtype_match_subquestions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_qtype_multichoice_options`
--

DROP TABLE IF EXISTS `mdl_qtype_multichoice_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_qtype_multichoice_options` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `questionid` bigint(10) NOT NULL DEFAULT '0',
  `layout` smallint(4) NOT NULL DEFAULT '0',
  `single` smallint(4) NOT NULL DEFAULT '0',
  `shuffleanswers` smallint(4) NOT NULL DEFAULT '1',
  `correctfeedback` longtext NOT NULL,
  `correctfeedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext NOT NULL,
  `partiallycorrectfeedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext NOT NULL,
  `incorrectfeedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  `answernumbering` varchar(10) NOT NULL DEFAULT 'abc',
  `shownumcorrect` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_qtypmultopti_que_uix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Options for multiple choice questions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_qtype_multichoice_options`
--

LOCK TABLES `mdl_qtype_multichoice_options` WRITE;
/*!40000 ALTER TABLE `mdl_qtype_multichoice_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_qtype_multichoice_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_qtype_shortanswer_options`
--

DROP TABLE IF EXISTS `mdl_qtype_shortanswer_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_qtype_shortanswer_options` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `questionid` bigint(10) NOT NULL DEFAULT '0',
  `usecase` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_qtypshoropti_que_uix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Options for short answer questions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_qtype_shortanswer_options`
--

LOCK TABLES `mdl_qtype_shortanswer_options` WRITE;
/*!40000 ALTER TABLE `mdl_qtype_shortanswer_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_qtype_shortanswer_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question`
--

DROP TABLE IF EXISTS `mdl_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `category` bigint(10) NOT NULL DEFAULT '0',
  `parent` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `questiontext` longtext NOT NULL,
  `questiontextformat` tinyint(2) NOT NULL DEFAULT '0',
  `generalfeedback` longtext NOT NULL,
  `generalfeedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  `defaultmark` decimal(12,7) NOT NULL DEFAULT '1.0000000',
  `penalty` decimal(12,7) NOT NULL DEFAULT '0.3333333',
  `qtype` varchar(20) NOT NULL DEFAULT '',
  `length` bigint(10) NOT NULL DEFAULT '1',
  `stamp` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(255) NOT NULL DEFAULT '',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `createdby` bigint(10) DEFAULT NULL,
  `modifiedby` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_ques_cat_ix` (`category`),
  KEY `mdl_ques_par_ix` (`parent`),
  KEY `mdl_ques_cre_ix` (`createdby`),
  KEY `mdl_ques_mod_ix` (`modifiedby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The questions themselves';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question`
--

LOCK TABLES `mdl_question` WRITE;
/*!40000 ALTER TABLE `mdl_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_answers`
--

DROP TABLE IF EXISTS `mdl_question_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_answers` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `question` bigint(10) NOT NULL DEFAULT '0',
  `answer` longtext NOT NULL,
  `answerformat` tinyint(2) NOT NULL DEFAULT '0',
  `fraction` decimal(12,7) NOT NULL DEFAULT '0.0000000',
  `feedback` longtext NOT NULL,
  `feedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quesansw_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Answers, with a fractional grade (0-1) and feedback';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_answers`
--

LOCK TABLES `mdl_question_answers` WRITE;
/*!40000 ALTER TABLE `mdl_question_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_attempt_step_data`
--

DROP TABLE IF EXISTS `mdl_question_attempt_step_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_attempt_step_data` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `attemptstepid` bigint(10) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `value` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quesattestepdata_attna_uix` (`attemptstepid`,`name`),
  KEY `mdl_quesattestepdata_att_ix` (`attemptstepid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Each question_attempt_step has an associative array of the d';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_attempt_step_data`
--

LOCK TABLES `mdl_question_attempt_step_data` WRITE;
/*!40000 ALTER TABLE `mdl_question_attempt_step_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_attempt_step_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_attempt_steps`
--

DROP TABLE IF EXISTS `mdl_question_attempt_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_attempt_steps` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `questionattemptid` bigint(10) NOT NULL,
  `sequencenumber` bigint(10) NOT NULL,
  `state` varchar(13) NOT NULL DEFAULT '',
  `fraction` decimal(12,7) DEFAULT NULL,
  `timecreated` bigint(10) NOT NULL,
  `userid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quesattestep_queseq_uix` (`questionattemptid`,`sequencenumber`),
  KEY `mdl_quesattestep_que_ix` (`questionattemptid`),
  KEY `mdl_quesattestep_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores one step in in a question attempt. As well as the dat';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_attempt_steps`
--

LOCK TABLES `mdl_question_attempt_steps` WRITE;
/*!40000 ALTER TABLE `mdl_question_attempt_steps` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_attempt_steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_attempts`
--

DROP TABLE IF EXISTS `mdl_question_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_attempts` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `questionusageid` bigint(10) NOT NULL,
  `slot` bigint(10) NOT NULL,
  `behaviour` varchar(32) NOT NULL DEFAULT '',
  `questionid` bigint(10) NOT NULL,
  `variant` bigint(10) NOT NULL DEFAULT '1',
  `maxmark` decimal(12,7) NOT NULL,
  `minfraction` decimal(12,7) NOT NULL,
  `maxfraction` decimal(12,7) NOT NULL DEFAULT '1.0000000',
  `flagged` tinyint(1) NOT NULL DEFAULT '0',
  `questionsummary` longtext,
  `rightanswer` longtext,
  `responsesummary` longtext,
  `timemodified` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quesatte_queslo_uix` (`questionusageid`,`slot`),
  KEY `mdl_quesatte_que_ix` (`questionid`),
  KEY `mdl_quesatte_que2_ix` (`questionusageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Each row here corresponds to an attempt at one question, as ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_attempts`
--

LOCK TABLES `mdl_question_attempts` WRITE;
/*!40000 ALTER TABLE `mdl_question_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_calculated`
--

DROP TABLE IF EXISTS `mdl_question_calculated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_calculated` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `question` bigint(10) NOT NULL DEFAULT '0',
  `answer` bigint(10) NOT NULL DEFAULT '0',
  `tolerance` varchar(20) NOT NULL DEFAULT '0.0',
  `tolerancetype` bigint(10) NOT NULL DEFAULT '1',
  `correctanswerlength` bigint(10) NOT NULL DEFAULT '2',
  `correctanswerformat` bigint(10) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `mdl_quescalc_ans_ix` (`answer`),
  KEY `mdl_quescalc_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Options for questions of type calculated';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_calculated`
--

LOCK TABLES `mdl_question_calculated` WRITE;
/*!40000 ALTER TABLE `mdl_question_calculated` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_calculated` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_calculated_options`
--

DROP TABLE IF EXISTS `mdl_question_calculated_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_calculated_options` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `question` bigint(10) NOT NULL DEFAULT '0',
  `synchronize` tinyint(2) NOT NULL DEFAULT '0',
  `single` smallint(4) NOT NULL DEFAULT '0',
  `shuffleanswers` smallint(4) NOT NULL DEFAULT '0',
  `correctfeedback` longtext,
  `correctfeedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  `partiallycorrectfeedback` longtext,
  `partiallycorrectfeedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  `incorrectfeedback` longtext,
  `incorrectfeedbackformat` tinyint(2) NOT NULL DEFAULT '0',
  `answernumbering` varchar(10) NOT NULL DEFAULT 'abc',
  `shownumcorrect` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quescalcopti_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Options for questions of type calculated';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_calculated_options`
--

LOCK TABLES `mdl_question_calculated_options` WRITE;
/*!40000 ALTER TABLE `mdl_question_calculated_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_calculated_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_categories`
--

DROP TABLE IF EXISTS `mdl_question_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_categories` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `contextid` bigint(10) NOT NULL DEFAULT '0',
  `info` longtext NOT NULL,
  `infoformat` tinyint(2) NOT NULL DEFAULT '0',
  `stamp` varchar(255) NOT NULL DEFAULT '',
  `parent` bigint(10) NOT NULL DEFAULT '0',
  `sortorder` bigint(10) NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`),
  KEY `mdl_quescate_con_ix` (`contextid`),
  KEY `mdl_quescate_par_ix` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categories are for grouping questions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_categories`
--

LOCK TABLES `mdl_question_categories` WRITE;
/*!40000 ALTER TABLE `mdl_question_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_dataset_definitions`
--

DROP TABLE IF EXISTS `mdl_question_dataset_definitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_dataset_definitions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `category` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` bigint(10) NOT NULL DEFAULT '0',
  `options` varchar(255) NOT NULL DEFAULT '',
  `itemcount` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quesdatadefi_cat_ix` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Organises and stores properties for dataset items';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_dataset_definitions`
--

LOCK TABLES `mdl_question_dataset_definitions` WRITE;
/*!40000 ALTER TABLE `mdl_question_dataset_definitions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_dataset_definitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_dataset_items`
--

DROP TABLE IF EXISTS `mdl_question_dataset_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_dataset_items` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `definition` bigint(10) NOT NULL DEFAULT '0',
  `itemnumber` bigint(10) NOT NULL DEFAULT '0',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_quesdataitem_def_ix` (`definition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Individual dataset items';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_dataset_items`
--

LOCK TABLES `mdl_question_dataset_items` WRITE;
/*!40000 ALTER TABLE `mdl_question_dataset_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_dataset_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_datasets`
--

DROP TABLE IF EXISTS `mdl_question_datasets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_datasets` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `question` bigint(10) NOT NULL DEFAULT '0',
  `datasetdefinition` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quesdata_quedat_ix` (`question`,`datasetdefinition`),
  KEY `mdl_quesdata_que_ix` (`question`),
  KEY `mdl_quesdata_dat_ix` (`datasetdefinition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Many-many relation between questions and dataset definitions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_datasets`
--

LOCK TABLES `mdl_question_datasets` WRITE;
/*!40000 ALTER TABLE `mdl_question_datasets` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_datasets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_hints`
--

DROP TABLE IF EXISTS `mdl_question_hints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_hints` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `questionid` bigint(10) NOT NULL,
  `hint` longtext NOT NULL,
  `hintformat` smallint(4) NOT NULL DEFAULT '0',
  `shownumcorrect` tinyint(1) DEFAULT NULL,
  `clearwrong` tinyint(1) DEFAULT NULL,
  `options` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_queshint_que_ix` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the the part of the question definition that gives di';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_hints`
--

LOCK TABLES `mdl_question_hints` WRITE;
/*!40000 ALTER TABLE `mdl_question_hints` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_hints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_multianswer`
--

DROP TABLE IF EXISTS `mdl_question_multianswer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_multianswer` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `question` bigint(10) NOT NULL DEFAULT '0',
  `sequence` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_quesmult_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Options for multianswer questions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_multianswer`
--

LOCK TABLES `mdl_question_multianswer` WRITE;
/*!40000 ALTER TABLE `mdl_question_multianswer` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_multianswer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_numerical`
--

DROP TABLE IF EXISTS `mdl_question_numerical`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_numerical` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `question` bigint(10) NOT NULL DEFAULT '0',
  `answer` bigint(10) NOT NULL DEFAULT '0',
  `tolerance` varchar(255) NOT NULL DEFAULT '0.0',
  PRIMARY KEY (`id`),
  KEY `mdl_quesnume_ans_ix` (`answer`),
  KEY `mdl_quesnume_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Options for numerical questions.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_numerical`
--

LOCK TABLES `mdl_question_numerical` WRITE;
/*!40000 ALTER TABLE `mdl_question_numerical` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_numerical` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_numerical_options`
--

DROP TABLE IF EXISTS `mdl_question_numerical_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_numerical_options` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `question` bigint(10) NOT NULL DEFAULT '0',
  `showunits` smallint(4) NOT NULL DEFAULT '0',
  `unitsleft` smallint(4) NOT NULL DEFAULT '0',
  `unitgradingtype` smallint(4) NOT NULL DEFAULT '0',
  `unitpenalty` decimal(12,7) NOT NULL DEFAULT '0.1000000',
  PRIMARY KEY (`id`),
  KEY `mdl_quesnumeopti_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Options for questions of type numerical This table is also u';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_numerical_options`
--

LOCK TABLES `mdl_question_numerical_options` WRITE;
/*!40000 ALTER TABLE `mdl_question_numerical_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_numerical_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_numerical_units`
--

DROP TABLE IF EXISTS `mdl_question_numerical_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_numerical_units` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `question` bigint(10) NOT NULL DEFAULT '0',
  `multiplier` decimal(40,20) NOT NULL DEFAULT '1.00000000000000000000',
  `unit` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quesnumeunit_queuni_uix` (`question`,`unit`),
  KEY `mdl_quesnumeunit_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Optional unit options for numerical questions. This table is';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_numerical_units`
--

LOCK TABLES `mdl_question_numerical_units` WRITE;
/*!40000 ALTER TABLE `mdl_question_numerical_units` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_numerical_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_randomsamatch`
--

DROP TABLE IF EXISTS `mdl_question_randomsamatch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_randomsamatch` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `question` bigint(10) NOT NULL DEFAULT '0',
  `choose` bigint(10) NOT NULL DEFAULT '4',
  PRIMARY KEY (`id`),
  KEY `mdl_quesrand_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Info about a random short-answer matching question';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_randomsamatch`
--

LOCK TABLES `mdl_question_randomsamatch` WRITE;
/*!40000 ALTER TABLE `mdl_question_randomsamatch` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_randomsamatch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_response_analysis`
--

DROP TABLE IF EXISTS `mdl_question_response_analysis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_response_analysis` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `hashcode` varchar(40) NOT NULL DEFAULT '',
  `timemodified` bigint(10) NOT NULL,
  `questionid` bigint(10) NOT NULL,
  `subqid` varchar(100) NOT NULL DEFAULT '',
  `aid` varchar(100) DEFAULT NULL,
  `response` longtext,
  `rcount` bigint(10) DEFAULT NULL,
  `credit` decimal(15,5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Analysis of student responses given to questions.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_response_analysis`
--

LOCK TABLES `mdl_question_response_analysis` WRITE;
/*!40000 ALTER TABLE `mdl_question_response_analysis` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_response_analysis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_sessions`
--

DROP TABLE IF EXISTS `mdl_question_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_sessions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `attemptid` bigint(10) NOT NULL DEFAULT '0',
  `questionid` bigint(10) NOT NULL DEFAULT '0',
  `newest` bigint(10) NOT NULL DEFAULT '0',
  `newgraded` bigint(10) NOT NULL DEFAULT '0',
  `sumpenalty` decimal(12,7) NOT NULL DEFAULT '0.0000000',
  `manualcomment` longtext NOT NULL,
  `manualcommentformat` tinyint(2) NOT NULL DEFAULT '0',
  `flagged` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quessess_attque_uix` (`attemptid`,`questionid`),
  KEY `mdl_quessess_att_ix` (`attemptid`),
  KEY `mdl_quessess_que_ix` (`questionid`),
  KEY `mdl_quessess_new_ix` (`newest`),
  KEY `mdl_quessess_new2_ix` (`newgraded`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Gives ids of the newest open and newest graded states';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_sessions`
--

LOCK TABLES `mdl_question_sessions` WRITE;
/*!40000 ALTER TABLE `mdl_question_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_states`
--

DROP TABLE IF EXISTS `mdl_question_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_states` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `attempt` bigint(10) NOT NULL DEFAULT '0',
  `question` bigint(10) NOT NULL DEFAULT '0',
  `seq_number` mediumint(6) NOT NULL DEFAULT '0',
  `answer` longtext NOT NULL,
  `timestamp` bigint(10) NOT NULL DEFAULT '0',
  `event` smallint(4) NOT NULL DEFAULT '0',
  `grade` decimal(12,7) NOT NULL DEFAULT '0.0000000',
  `raw_grade` decimal(12,7) NOT NULL DEFAULT '0.0000000',
  `penalty` decimal(12,7) NOT NULL DEFAULT '0.0000000',
  PRIMARY KEY (`id`),
  KEY `mdl_quesstat_att_ix` (`attempt`),
  KEY `mdl_quesstat_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores user responses to an attempt, and percentage grades';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_states`
--

LOCK TABLES `mdl_question_states` WRITE;
/*!40000 ALTER TABLE `mdl_question_states` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_statistics`
--

DROP TABLE IF EXISTS `mdl_question_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_statistics` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `hashcode` varchar(40) NOT NULL DEFAULT '',
  `timemodified` bigint(10) NOT NULL,
  `questionid` bigint(10) NOT NULL,
  `slot` bigint(10) DEFAULT NULL,
  `subquestion` smallint(4) NOT NULL,
  `s` bigint(10) NOT NULL DEFAULT '0',
  `effectiveweight` decimal(15,5) DEFAULT NULL,
  `negcovar` tinyint(2) NOT NULL DEFAULT '0',
  `discriminationindex` decimal(15,5) DEFAULT NULL,
  `discriminativeefficiency` decimal(15,5) DEFAULT NULL,
  `sd` decimal(15,10) DEFAULT NULL,
  `facility` decimal(15,10) DEFAULT NULL,
  `subquestions` longtext,
  `maxmark` decimal(12,7) DEFAULT NULL,
  `positions` longtext,
  `randomguessscore` decimal(12,7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Statistics for individual questions used in an activity.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_statistics`
--

LOCK TABLES `mdl_question_statistics` WRITE;
/*!40000 ALTER TABLE `mdl_question_statistics` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_statistics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_truefalse`
--

DROP TABLE IF EXISTS `mdl_question_truefalse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_truefalse` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `question` bigint(10) NOT NULL DEFAULT '0',
  `trueanswer` bigint(10) NOT NULL DEFAULT '0',
  `falseanswer` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_questrue_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Options for True-False questions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_truefalse`
--

LOCK TABLES `mdl_question_truefalse` WRITE;
/*!40000 ALTER TABLE `mdl_question_truefalse` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_truefalse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_question_usages`
--

DROP TABLE IF EXISTS `mdl_question_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_question_usages` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextid` bigint(10) NOT NULL,
  `component` varchar(255) NOT NULL DEFAULT '',
  `preferredbehaviour` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_quesusag_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table''s main purpose it to assign a unique id to each a';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_question_usages`
--

LOCK TABLES `mdl_question_usages` WRITE;
/*!40000 ALTER TABLE `mdl_question_usages` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_question_usages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_quiz`
--

DROP TABLE IF EXISTS `mdl_quiz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_quiz` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `timeopen` bigint(10) NOT NULL DEFAULT '0',
  `timeclose` bigint(10) NOT NULL DEFAULT '0',
  `timelimit` bigint(10) NOT NULL DEFAULT '0',
  `overduehandling` varchar(16) NOT NULL DEFAULT 'autoabandon',
  `graceperiod` bigint(10) NOT NULL DEFAULT '0',
  `preferredbehaviour` varchar(32) NOT NULL DEFAULT '',
  `attempts` mediumint(6) NOT NULL DEFAULT '0',
  `attemptonlast` smallint(4) NOT NULL DEFAULT '0',
  `grademethod` smallint(4) NOT NULL DEFAULT '1',
  `decimalpoints` smallint(4) NOT NULL DEFAULT '2',
  `questiondecimalpoints` smallint(4) NOT NULL DEFAULT '-1',
  `reviewattempt` mediumint(6) NOT NULL DEFAULT '0',
  `reviewcorrectness` mediumint(6) NOT NULL DEFAULT '0',
  `reviewmarks` mediumint(6) NOT NULL DEFAULT '0',
  `reviewspecificfeedback` mediumint(6) NOT NULL DEFAULT '0',
  `reviewgeneralfeedback` mediumint(6) NOT NULL DEFAULT '0',
  `reviewrightanswer` mediumint(6) NOT NULL DEFAULT '0',
  `reviewoverallfeedback` mediumint(6) NOT NULL DEFAULT '0',
  `questionsperpage` bigint(10) NOT NULL DEFAULT '0',
  `navmethod` varchar(16) NOT NULL DEFAULT 'free',
  `shufflequestions` smallint(4) NOT NULL DEFAULT '0',
  `shuffleanswers` smallint(4) NOT NULL DEFAULT '0',
  `questions` longtext NOT NULL,
  `sumgrades` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `grade` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '',
  `subnet` varchar(255) NOT NULL DEFAULT '',
  `browsersecurity` varchar(32) NOT NULL DEFAULT '',
  `delay1` bigint(10) NOT NULL DEFAULT '0',
  `delay2` bigint(10) NOT NULL DEFAULT '0',
  `showuserpicture` smallint(4) NOT NULL DEFAULT '0',
  `showblocks` smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quiz_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The settings for each quiz.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_quiz`
--

LOCK TABLES `mdl_quiz` WRITE;
/*!40000 ALTER TABLE `mdl_quiz` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_quiz` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_quiz_attempts`
--

DROP TABLE IF EXISTS `mdl_quiz_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_quiz_attempts` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `quiz` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `attempt` mediumint(6) NOT NULL DEFAULT '0',
  `uniqueid` bigint(10) NOT NULL DEFAULT '0',
  `layout` longtext NOT NULL,
  `currentpage` bigint(10) NOT NULL DEFAULT '0',
  `preview` smallint(3) NOT NULL DEFAULT '0',
  `state` varchar(16) NOT NULL DEFAULT 'inprogress',
  `timestart` bigint(10) NOT NULL DEFAULT '0',
  `timefinish` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `timecheckstate` bigint(10) DEFAULT '0',
  `sumgrades` decimal(10,5) DEFAULT NULL,
  `needsupgradetonewqe` smallint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quizatte_quiuseatt_uix` (`quiz`,`userid`,`attempt`),
  UNIQUE KEY `mdl_quizatte_uni_uix` (`uniqueid`),
  KEY `mdl_quizatte_statim_ix` (`state`,`timecheckstate`),
  KEY `mdl_quizatte_qui_ix` (`quiz`),
  KEY `mdl_quizatte_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores users attempts at quizzes.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_quiz_attempts`
--

LOCK TABLES `mdl_quiz_attempts` WRITE;
/*!40000 ALTER TABLE `mdl_quiz_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_quiz_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_quiz_feedback`
--

DROP TABLE IF EXISTS `mdl_quiz_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_quiz_feedback` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `quizid` bigint(10) NOT NULL DEFAULT '0',
  `feedbacktext` longtext NOT NULL,
  `feedbacktextformat` tinyint(2) NOT NULL DEFAULT '0',
  `mingrade` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `maxgrade` decimal(10,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  KEY `mdl_quizfeed_qui_ix` (`quizid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Feedback given to students based on which grade band their o';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_quiz_feedback`
--

LOCK TABLES `mdl_quiz_feedback` WRITE;
/*!40000 ALTER TABLE `mdl_quiz_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_quiz_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_quiz_grades`
--

DROP TABLE IF EXISTS `mdl_quiz_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_quiz_grades` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `quiz` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `grade` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_quizgrad_use_ix` (`userid`),
  KEY `mdl_quizgrad_qui_ix` (`quiz`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the overall grade for each user on the quiz, based on';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_quiz_grades`
--

LOCK TABLES `mdl_quiz_grades` WRITE;
/*!40000 ALTER TABLE `mdl_quiz_grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_quiz_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_quiz_overrides`
--

DROP TABLE IF EXISTS `mdl_quiz_overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_quiz_overrides` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `quiz` bigint(10) NOT NULL DEFAULT '0',
  `groupid` bigint(10) DEFAULT NULL,
  `userid` bigint(10) DEFAULT NULL,
  `timeopen` bigint(10) DEFAULT NULL,
  `timeclose` bigint(10) DEFAULT NULL,
  `timelimit` bigint(10) DEFAULT NULL,
  `attempts` mediumint(6) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_quizover_qui_ix` (`quiz`),
  KEY `mdl_quizover_gro_ix` (`groupid`),
  KEY `mdl_quizover_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The overrides to quiz settings on a per-user and per-group b';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_quiz_overrides`
--

LOCK TABLES `mdl_quiz_overrides` WRITE;
/*!40000 ALTER TABLE `mdl_quiz_overrides` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_quiz_overrides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_quiz_overview_regrades`
--

DROP TABLE IF EXISTS `mdl_quiz_overview_regrades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_quiz_overview_regrades` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `questionusageid` bigint(10) NOT NULL,
  `slot` bigint(10) NOT NULL,
  `newfraction` decimal(12,7) DEFAULT NULL,
  `oldfraction` decimal(12,7) DEFAULT NULL,
  `regraded` smallint(4) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table records which question attempts need regrading an';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_quiz_overview_regrades`
--

LOCK TABLES `mdl_quiz_overview_regrades` WRITE;
/*!40000 ALTER TABLE `mdl_quiz_overview_regrades` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_quiz_overview_regrades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_quiz_question_instances`
--

DROP TABLE IF EXISTS `mdl_quiz_question_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_quiz_question_instances` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `quiz` bigint(10) NOT NULL DEFAULT '0',
  `question` bigint(10) NOT NULL DEFAULT '0',
  `grade` decimal(12,7) NOT NULL DEFAULT '0.0000000',
  PRIMARY KEY (`id`),
  KEY `mdl_quizquesinst_qui_ix` (`quiz`),
  KEY `mdl_quizquesinst_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the maximum possible grade (weight) for each question';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_quiz_question_instances`
--

LOCK TABLES `mdl_quiz_question_instances` WRITE;
/*!40000 ALTER TABLE `mdl_quiz_question_instances` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_quiz_question_instances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_quiz_reports`
--

DROP TABLE IF EXISTS `mdl_quiz_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_quiz_reports` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `displayorder` bigint(10) NOT NULL,
  `capability` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_quizrepo_nam_uix` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Lists all the installed quiz reports and their display order';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_quiz_reports`
--

LOCK TABLES `mdl_quiz_reports` WRITE;
/*!40000 ALTER TABLE `mdl_quiz_reports` DISABLE KEYS */;
INSERT INTO `mdl_quiz_reports` VALUES (1,'grading',6000,'mod/quiz:grade'),(2,'overview',10000,NULL),(3,'responses',9000,NULL),(4,'statistics',8000,'quiz/statistics:view');
/*!40000 ALTER TABLE `mdl_quiz_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_quiz_statistics`
--

DROP TABLE IF EXISTS `mdl_quiz_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_quiz_statistics` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `hashcode` varchar(40) NOT NULL DEFAULT '',
  `whichattempts` smallint(4) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  `firstattemptscount` bigint(10) NOT NULL,
  `highestattemptscount` bigint(10) NOT NULL,
  `lastattemptscount` bigint(10) NOT NULL,
  `allattemptscount` bigint(10) NOT NULL,
  `firstattemptsavg` decimal(15,5) DEFAULT NULL,
  `highestattemptsavg` decimal(15,5) DEFAULT NULL,
  `lastattemptsavg` decimal(15,5) DEFAULT NULL,
  `allattemptsavg` decimal(15,5) DEFAULT NULL,
  `median` decimal(15,5) DEFAULT NULL,
  `standarddeviation` decimal(15,5) DEFAULT NULL,
  `skewness` decimal(15,10) DEFAULT NULL,
  `kurtosis` decimal(15,5) DEFAULT NULL,
  `cic` decimal(15,10) DEFAULT NULL,
  `errorratio` decimal(15,10) DEFAULT NULL,
  `standarderror` decimal(15,10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table to cache results from analysis done in statistics repo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_quiz_statistics`
--

LOCK TABLES `mdl_quiz_statistics` WRITE;
/*!40000 ALTER TABLE `mdl_quiz_statistics` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_quiz_statistics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_rating`
--

DROP TABLE IF EXISTS `mdl_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_rating` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextid` bigint(10) NOT NULL,
  `component` varchar(100) NOT NULL DEFAULT '',
  `ratingarea` varchar(50) NOT NULL DEFAULT '',
  `itemid` bigint(10) NOT NULL,
  `scaleid` bigint(10) NOT NULL,
  `rating` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `timecreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_rati_comratconite_ix` (`component`,`ratingarea`,`contextid`,`itemid`),
  KEY `mdl_rati_con_ix` (`contextid`),
  KEY `mdl_rati_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='moodle ratings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_rating`
--

LOCK TABLES `mdl_rating` WRITE;
/*!40000 ALTER TABLE `mdl_rating` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_rating` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_registration_hubs`
--

DROP TABLE IF EXISTS `mdl_registration_hubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_registration_hubs` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL DEFAULT '',
  `hubname` varchar(255) NOT NULL DEFAULT '',
  `huburl` varchar(255) NOT NULL DEFAULT '',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `secret` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='hub where the site is registered on with their associated to';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_registration_hubs`
--

LOCK TABLES `mdl_registration_hubs` WRITE;
/*!40000 ALTER TABLE `mdl_registration_hubs` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_registration_hubs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_repository`
--

DROP TABLE IF EXISTS `mdl_repository`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_repository` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT '',
  `visible` tinyint(1) DEFAULT '1',
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='This table contains one entry for every configured external ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_repository`
--

LOCK TABLES `mdl_repository` WRITE;
/*!40000 ALTER TABLE `mdl_repository` DISABLE KEYS */;
INSERT INTO `mdl_repository` VALUES (1,'areafiles',1,1),(2,'local',1,2),(3,'recent',1,3),(4,'upload',1,4),(5,'url',1,5),(6,'user',1,6),(7,'wikimedia',1,7),(8,'youtube',1,8);
/*!40000 ALTER TABLE `mdl_repository` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_repository_instance_config`
--

DROP TABLE IF EXISTS `mdl_repository_instance_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_repository_instance_config` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `instanceid` bigint(10) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The config for intances';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_repository_instance_config`
--

LOCK TABLES `mdl_repository_instance_config` WRITE;
/*!40000 ALTER TABLE `mdl_repository_instance_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_repository_instance_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_repository_instances`
--

DROP TABLE IF EXISTS `mdl_repository_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_repository_instances` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `typeid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `contextid` bigint(10) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `timecreated` bigint(10) DEFAULT NULL,
  `timemodified` bigint(10) DEFAULT NULL,
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='This table contains one entry for every configured external ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_repository_instances`
--

LOCK TABLES `mdl_repository_instances` WRITE;
/*!40000 ALTER TABLE `mdl_repository_instances` DISABLE KEYS */;
INSERT INTO `mdl_repository_instances` VALUES (1,'',1,0,1,NULL,NULL,1390420729,1390420729,0),(2,'',2,0,1,NULL,NULL,1390420742,1390420742,0),(3,'',3,0,1,NULL,NULL,1390420748,1390420748,0),(4,'',4,0,1,NULL,NULL,1390420754,1390420754,0),(5,'',5,0,1,NULL,NULL,1390420756,1390420756,0),(6,'',6,0,1,NULL,NULL,1390420758,1390420758,0),(7,'',7,0,1,NULL,NULL,1390420761,1390420761,0),(8,'',8,0,1,NULL,NULL,1390420766,1390420766,0);
/*!40000 ALTER TABLE `mdl_repository_instances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_resource`
--

DROP TABLE IF EXISTS `mdl_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_resource` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `tobemigrated` smallint(4) NOT NULL DEFAULT '0',
  `legacyfiles` smallint(4) NOT NULL DEFAULT '0',
  `legacyfileslast` bigint(10) DEFAULT NULL,
  `display` smallint(4) NOT NULL DEFAULT '0',
  `displayoptions` longtext,
  `filterfiles` smallint(4) NOT NULL DEFAULT '0',
  `revision` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_reso_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Each record is one resource and its config data';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_resource`
--

LOCK TABLES `mdl_resource` WRITE;
/*!40000 ALTER TABLE `mdl_resource` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_resource_old`
--

DROP TABLE IF EXISTS `mdl_resource_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_resource_old` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(30) NOT NULL DEFAULT '',
  `reference` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `alltext` longtext NOT NULL,
  `popup` longtext NOT NULL,
  `options` varchar(255) NOT NULL DEFAULT '',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `oldid` bigint(10) NOT NULL,
  `cmid` bigint(10) DEFAULT NULL,
  `newmodule` varchar(50) DEFAULT NULL,
  `newid` bigint(10) DEFAULT NULL,
  `migrated` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_resoold_old_uix` (`oldid`),
  KEY `mdl_resoold_cmi_ix` (`cmid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='backup of all old resource instances from 1.9';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_resource_old`
--

LOCK TABLES `mdl_resource_old` WRITE;
/*!40000 ALTER TABLE `mdl_resource_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_resource_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_role`
--

DROP TABLE IF EXISTS `mdl_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_role` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `shortname` varchar(100) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  `archetype` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_role_sor_uix` (`sortorder`),
  UNIQUE KEY `mdl_role_sho_uix` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='moodle roles';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_role`
--

LOCK TABLES `mdl_role` WRITE;
/*!40000 ALTER TABLE `mdl_role` DISABLE KEYS */;
INSERT INTO `mdl_role` VALUES (1,'','manager','',1,'manager'),(2,'','coursecreator','',2,'coursecreator'),(3,'','editingteacher','',3,'editingteacher'),(4,'','teacher','',4,'teacher'),(5,'','student','',5,'student'),(6,'','guest','',6,'guest'),(7,'','user','',7,'user'),(8,'','frontpage','',8,'frontpage'),(9,'deleteme','delete','<p>test role</p>',9,'');
/*!40000 ALTER TABLE `mdl_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_role_allow_assign`
--

DROP TABLE IF EXISTS `mdl_role_allow_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_role_allow_assign` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `allowassign` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolealloassi_rolall_uix` (`roleid`,`allowassign`),
  KEY `mdl_rolealloassi_rol_ix` (`roleid`),
  KEY `mdl_rolealloassi_all_ix` (`allowassign`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='this defines what role can assign what role';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_role_allow_assign`
--

LOCK TABLES `mdl_role_allow_assign` WRITE;
/*!40000 ALTER TABLE `mdl_role_allow_assign` DISABLE KEYS */;
INSERT INTO `mdl_role_allow_assign` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,3,4),(7,3,5);
/*!40000 ALTER TABLE `mdl_role_allow_assign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_role_allow_override`
--

DROP TABLE IF EXISTS `mdl_role_allow_override`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_role_allow_override` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `allowoverride` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolealloover_rolall_uix` (`roleid`,`allowoverride`),
  KEY `mdl_rolealloover_rol_ix` (`roleid`),
  KEY `mdl_rolealloover_all_ix` (`allowoverride`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='this defines what role can override what role';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_role_allow_override`
--

LOCK TABLES `mdl_role_allow_override` WRITE;
/*!40000 ALTER TABLE `mdl_role_allow_override` DISABLE KEYS */;
INSERT INTO `mdl_role_allow_override` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,3,4),(10,3,5),(11,3,6);
/*!40000 ALTER TABLE `mdl_role_allow_override` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_role_allow_switch`
--

DROP TABLE IF EXISTS `mdl_role_allow_switch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_role_allow_switch` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `roleid` bigint(10) NOT NULL,
  `allowswitch` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolealloswit_rolall_uix` (`roleid`,`allowswitch`),
  KEY `mdl_rolealloswit_rol_ix` (`roleid`),
  KEY `mdl_rolealloswit_all_ix` (`allowswitch`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='This table stores which which other roles a user is allowed ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_role_allow_switch`
--

LOCK TABLES `mdl_role_allow_switch` WRITE;
/*!40000 ALTER TABLE `mdl_role_allow_switch` DISABLE KEYS */;
INSERT INTO `mdl_role_allow_switch` VALUES (1,1,3),(2,1,4),(3,1,5),(4,1,6),(5,3,4),(6,3,5),(7,3,6),(8,4,5),(9,4,6);
/*!40000 ALTER TABLE `mdl_role_allow_switch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_role_assignments`
--

DROP TABLE IF EXISTS `mdl_role_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_role_assignments` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `contextid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `modifierid` bigint(10) NOT NULL DEFAULT '0',
  `component` varchar(100) NOT NULL DEFAULT '',
  `itemid` bigint(10) NOT NULL DEFAULT '0',
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_roleassi_sor_ix` (`sortorder`),
  KEY `mdl_roleassi_rolcon_ix` (`roleid`,`contextid`),
  KEY `mdl_roleassi_useconrol_ix` (`userid`,`contextid`,`roleid`),
  KEY `mdl_roleassi_comiteuse_ix` (`component`,`itemid`,`userid`),
  KEY `mdl_roleassi_rol_ix` (`roleid`),
  KEY `mdl_roleassi_con_ix` (`contextid`),
  KEY `mdl_roleassi_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='assigning roles in different context';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_role_assignments`
--

LOCK TABLES `mdl_role_assignments` WRITE;
/*!40000 ALTER TABLE `mdl_role_assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_role_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_role_capabilities`
--

DROP TABLE IF EXISTS `mdl_role_capabilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_role_capabilities` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `contextid` bigint(10) NOT NULL DEFAULT '0',
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `capability` varchar(255) NOT NULL DEFAULT '',
  `permission` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `modifierid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolecapa_rolconcap_uix` (`roleid`,`contextid`,`capability`),
  KEY `mdl_rolecapa_rol_ix` (`roleid`),
  KEY `mdl_rolecapa_con_ix` (`contextid`),
  KEY `mdl_rolecapa_mod_ix` (`modifierid`),
  KEY `mdl_rolecapa_cap_ix` (`capability`)
) ENGINE=InnoDB AUTO_INCREMENT=1120 DEFAULT CHARSET=utf8 COMMENT='permission has to be signed, overriding a capability for a p';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_role_capabilities`
--

LOCK TABLES `mdl_role_capabilities` WRITE;
/*!40000 ALTER TABLE `mdl_role_capabilities` DISABLE KEYS */;
INSERT INTO `mdl_role_capabilities` VALUES (1,1,1,'moodle/site:readallmessages',1,1390419893,0),(2,1,3,'moodle/site:readallmessages',1,1390419894,0),(3,1,1,'moodle/site:sendmessage',1,1390419895,0),(4,1,7,'moodle/site:sendmessage',1,1390419895,0),(5,1,1,'moodle/site:approvecourse',1,1390419895,0),(6,1,3,'moodle/backup:backupcourse',1,1390419896,0),(7,1,1,'moodle/backup:backupcourse',1,1390419896,0),(8,1,3,'moodle/backup:backupsection',1,1390419896,0),(9,1,1,'moodle/backup:backupsection',1,1390419896,0),(10,1,3,'moodle/backup:backupactivity',1,1390419896,0),(11,1,1,'moodle/backup:backupactivity',1,1390419897,0),(12,1,3,'moodle/backup:backuptargethub',1,1390419897,0),(13,1,1,'moodle/backup:backuptargethub',1,1390419897,0),(14,1,3,'moodle/backup:backuptargetimport',1,1390419897,0),(15,1,1,'moodle/backup:backuptargetimport',1,1390419897,0),(16,1,3,'moodle/backup:downloadfile',1,1390419897,0),(17,1,1,'moodle/backup:downloadfile',1,1390419897,0),(18,1,3,'moodle/backup:configure',1,1390419897,0),(19,1,1,'moodle/backup:configure',1,1390419897,0),(20,1,1,'moodle/backup:userinfo',1,1390419897,0),(21,1,1,'moodle/backup:anonymise',1,1390419898,0),(22,1,3,'moodle/restore:restorecourse',1,1390419898,0),(23,1,1,'moodle/restore:restorecourse',1,1390419898,0),(24,1,3,'moodle/restore:restoresection',1,1390419898,0),(25,1,1,'moodle/restore:restoresection',1,1390419899,0),(26,1,3,'moodle/restore:restoreactivity',1,1390419899,0),(27,1,1,'moodle/restore:restoreactivity',1,1390419899,0),(28,1,3,'moodle/restore:restoretargethub',1,1390419900,0),(29,1,1,'moodle/restore:restoretargethub',1,1390419900,0),(30,1,3,'moodle/restore:restoretargetimport',1,1390419900,0),(31,1,1,'moodle/restore:restoretargetimport',1,1390419901,0),(32,1,3,'moodle/restore:uploadfile',1,1390419901,0),(33,1,1,'moodle/restore:uploadfile',1,1390419901,0),(34,1,3,'moodle/restore:configure',1,1390419901,0),(35,1,1,'moodle/restore:configure',1,1390419901,0),(36,1,2,'moodle/restore:rolldates',1,1390419902,0),(37,1,1,'moodle/restore:rolldates',1,1390419902,0),(38,1,1,'moodle/restore:userinfo',1,1390419902,0),(39,1,1,'moodle/restore:createuser',1,1390419902,0),(40,1,3,'moodle/site:manageblocks',1,1390419903,0),(41,1,1,'moodle/site:manageblocks',1,1390419903,0),(42,1,4,'moodle/site:accessallgroups',1,1390419903,0),(43,1,3,'moodle/site:accessallgroups',1,1390419904,0),(44,1,1,'moodle/site:accessallgroups',1,1390419904,0),(45,1,4,'moodle/site:viewfullnames',1,1390419904,0),(46,1,3,'moodle/site:viewfullnames',1,1390419904,0),(47,1,1,'moodle/site:viewfullnames',1,1390419904,0),(48,1,4,'moodle/site:viewuseridentity',1,1390419905,0),(49,1,3,'moodle/site:viewuseridentity',1,1390419905,0),(50,1,1,'moodle/site:viewuseridentity',1,1390419905,0),(51,1,4,'moodle/site:viewreports',1,1390419907,0),(52,1,3,'moodle/site:viewreports',1,1390419907,0),(53,1,1,'moodle/site:viewreports',1,1390419908,0),(54,1,3,'moodle/site:trustcontent',1,1390419908,0),(55,1,1,'moodle/site:trustcontent',1,1390419908,0),(56,1,1,'moodle/site:uploadusers',1,1390419908,0),(57,1,3,'moodle/filter:manage',1,1390419908,0),(58,1,1,'moodle/filter:manage',1,1390419909,0),(59,1,1,'moodle/user:create',1,1390419909,0),(60,1,1,'moodle/user:delete',1,1390419909,0),(61,1,1,'moodle/user:update',1,1390419909,0),(62,1,6,'moodle/user:viewdetails',1,1390419909,0),(63,1,5,'moodle/user:viewdetails',1,1390419909,0),(64,1,4,'moodle/user:viewdetails',1,1390419909,0),(65,1,3,'moodle/user:viewdetails',1,1390419909,0),(66,1,1,'moodle/user:viewdetails',1,1390419909,0),(67,1,1,'moodle/user:viewalldetails',1,1390419909,0),(68,1,4,'moodle/user:viewhiddendetails',1,1390419909,0),(69,1,3,'moodle/user:viewhiddendetails',1,1390419910,0),(70,1,1,'moodle/user:viewhiddendetails',1,1390419910,0),(71,1,1,'moodle/user:loginas',1,1390419910,0),(72,1,1,'moodle/user:managesyspages',1,1390419910,0),(73,1,7,'moodle/user:manageownblocks',1,1390419910,0),(74,1,7,'moodle/user:manageownfiles',1,1390419910,0),(75,1,1,'moodle/my:configsyspages',1,1390419910,0),(76,1,3,'moodle/role:assign',1,1390419911,0),(77,1,1,'moodle/role:assign',1,1390419911,0),(78,1,4,'moodle/role:review',1,1390419911,0),(79,1,3,'moodle/role:review',1,1390419911,0),(80,1,1,'moodle/role:review',1,1390419911,0),(81,1,1,'moodle/role:override',1,1390419911,0),(82,1,3,'moodle/role:safeoverride',1,1390419911,0),(83,1,1,'moodle/role:manage',1,1390419912,0),(84,1,3,'moodle/role:switchroles',1,1390419913,0),(85,1,1,'moodle/role:switchroles',1,1390419913,0),(86,1,1,'moodle/category:manage',1,1390419913,0),(87,1,2,'moodle/category:viewhiddencategories',1,1390419914,0),(88,1,1,'moodle/category:viewhiddencategories',1,1390419914,0),(89,1,1,'moodle/cohort:manage',1,1390419914,0),(90,1,1,'moodle/cohort:assign',1,1390419914,0),(91,1,3,'moodle/cohort:view',1,1390419915,0),(92,1,1,'moodle/cohort:view',1,1390419915,0),(93,1,2,'moodle/course:create',1,1390419915,0),(94,1,1,'moodle/course:create',1,1390419915,0),(95,1,7,'moodle/course:request',1,1390419915,0),(96,1,1,'moodle/course:delete',1,1390419916,0),(97,1,3,'moodle/course:update',1,1390419916,0),(98,1,1,'moodle/course:update',1,1390419917,0),(99,1,1,'moodle/course:view',1,1390419917,0),(100,1,3,'moodle/course:enrolreview',1,1390419918,0),(101,1,1,'moodle/course:enrolreview',1,1390419918,0),(102,1,3,'moodle/course:enrolconfig',1,1390419919,0),(103,1,1,'moodle/course:enrolconfig',1,1390419919,0),(104,1,4,'moodle/course:bulkmessaging',1,1390419920,0),(105,1,3,'moodle/course:bulkmessaging',1,1390419920,0),(106,1,1,'moodle/course:bulkmessaging',1,1390419920,0),(107,1,4,'moodle/course:viewhiddenuserfields',1,1390419920,0),(108,1,3,'moodle/course:viewhiddenuserfields',1,1390419921,0),(109,1,1,'moodle/course:viewhiddenuserfields',1,1390419921,0),(110,1,2,'moodle/course:viewhiddencourses',1,1390419921,0),(111,1,4,'moodle/course:viewhiddencourses',1,1390419921,0),(112,1,3,'moodle/course:viewhiddencourses',1,1390419921,0),(113,1,1,'moodle/course:viewhiddencourses',1,1390419921,0),(114,1,3,'moodle/course:visibility',1,1390419921,0),(115,1,1,'moodle/course:visibility',1,1390419922,0),(116,1,3,'moodle/course:managefiles',1,1390419922,0),(117,1,1,'moodle/course:managefiles',1,1390419922,0),(118,1,3,'moodle/course:manageactivities',1,1390419923,0),(119,1,1,'moodle/course:manageactivities',1,1390419923,0),(120,1,3,'moodle/course:activityvisibility',1,1390419923,0),(121,1,1,'moodle/course:activityvisibility',1,1390419923,0),(122,1,4,'moodle/course:viewhiddenactivities',1,1390419924,0),(123,1,3,'moodle/course:viewhiddenactivities',1,1390419924,0),(124,1,1,'moodle/course:viewhiddenactivities',1,1390419924,0),(125,1,5,'moodle/course:viewparticipants',1,1390419925,0),(126,1,4,'moodle/course:viewparticipants',1,1390419925,0),(127,1,3,'moodle/course:viewparticipants',1,1390419925,0),(128,1,1,'moodle/course:viewparticipants',1,1390419925,0),(129,1,3,'moodle/course:changefullname',1,1390419925,0),(130,1,1,'moodle/course:changefullname',1,1390419925,0),(131,1,3,'moodle/course:changeshortname',1,1390419926,0),(132,1,1,'moodle/course:changeshortname',1,1390419926,0),(133,1,3,'moodle/course:changeidnumber',1,1390419927,0),(134,1,1,'moodle/course:changeidnumber',1,1390419927,0),(135,1,3,'moodle/course:changecategory',1,1390419927,0),(136,1,1,'moodle/course:changecategory',1,1390419927,0),(137,1,3,'moodle/course:changesummary',1,1390419928,0),(138,1,1,'moodle/course:changesummary',1,1390419928,0),(139,1,1,'moodle/site:viewparticipants',1,1390419928,0),(140,1,5,'moodle/course:isincompletionreports',1,1390419929,0),(141,1,5,'moodle/course:viewscales',1,1390419929,0),(142,1,4,'moodle/course:viewscales',1,1390419929,0),(143,1,3,'moodle/course:viewscales',1,1390419929,0),(144,1,1,'moodle/course:viewscales',1,1390419929,0),(145,1,3,'moodle/course:managescales',1,1390419930,0),(146,1,1,'moodle/course:managescales',1,1390419930,0),(147,1,3,'moodle/course:managegroups',1,1390419931,0),(148,1,1,'moodle/course:managegroups',1,1390419931,0),(149,1,3,'moodle/course:reset',1,1390419931,0),(150,1,1,'moodle/course:reset',1,1390419931,0),(151,1,3,'moodle/course:viewsuspendedusers',1,1390419931,0),(152,1,1,'moodle/course:viewsuspendedusers',1,1390419931,0),(153,1,6,'moodle/blog:view',1,1390419932,0),(154,1,7,'moodle/blog:view',1,1390419932,0),(155,1,5,'moodle/blog:view',1,1390419932,0),(156,1,4,'moodle/blog:view',1,1390419932,0),(157,1,3,'moodle/blog:view',1,1390419933,0),(158,1,1,'moodle/blog:view',1,1390419933,0),(159,1,6,'moodle/blog:search',1,1390419933,0),(160,1,7,'moodle/blog:search',1,1390419933,0),(161,1,5,'moodle/blog:search',1,1390419933,0),(162,1,4,'moodle/blog:search',1,1390419933,0),(163,1,3,'moodle/blog:search',1,1390419934,0),(164,1,1,'moodle/blog:search',1,1390419934,0),(165,1,1,'moodle/blog:viewdrafts',1,1390419934,0),(166,1,7,'moodle/blog:create',1,1390419935,0),(167,1,1,'moodle/blog:create',1,1390419935,0),(168,1,4,'moodle/blog:manageentries',1,1390419935,0),(169,1,3,'moodle/blog:manageentries',1,1390419935,0),(170,1,1,'moodle/blog:manageentries',1,1390419935,0),(171,1,5,'moodle/blog:manageexternal',1,1390419935,0),(172,1,7,'moodle/blog:manageexternal',1,1390419935,0),(173,1,4,'moodle/blog:manageexternal',1,1390419936,0),(174,1,3,'moodle/blog:manageexternal',1,1390419936,0),(175,1,1,'moodle/blog:manageexternal',1,1390419936,0),(176,1,7,'moodle/calendar:manageownentries',1,1390419937,0),(177,1,1,'moodle/calendar:manageownentries',1,1390419937,0),(178,1,4,'moodle/calendar:managegroupentries',1,1390419937,0),(179,1,3,'moodle/calendar:managegroupentries',1,1390419937,0),(180,1,1,'moodle/calendar:managegroupentries',1,1390419938,0),(181,1,4,'moodle/calendar:manageentries',1,1390419938,0),(182,1,3,'moodle/calendar:manageentries',1,1390419938,0),(183,1,1,'moodle/calendar:manageentries',1,1390419939,0),(184,1,1,'moodle/user:editprofile',1,1390419939,0),(185,1,6,'moodle/user:editownprofile',-1000,1390419939,0),(186,1,7,'moodle/user:editownprofile',1,1390419939,0),(187,1,1,'moodle/user:editownprofile',1,1390419940,0),(188,1,6,'moodle/user:changeownpassword',-1000,1390419940,0),(189,1,7,'moodle/user:changeownpassword',1,1390419940,0),(190,1,1,'moodle/user:changeownpassword',1,1390419940,0),(191,1,5,'moodle/user:readuserposts',1,1390419940,0),(192,1,4,'moodle/user:readuserposts',1,1390419941,0),(193,1,3,'moodle/user:readuserposts',1,1390419941,0),(194,1,1,'moodle/user:readuserposts',1,1390419942,0),(195,1,5,'moodle/user:readuserblogs',1,1390419943,0),(196,1,4,'moodle/user:readuserblogs',1,1390419943,0),(197,1,3,'moodle/user:readuserblogs',1,1390419943,0),(198,1,1,'moodle/user:readuserblogs',1,1390419944,0),(199,1,1,'moodle/user:editmessageprofile',1,1390419944,0),(200,1,6,'moodle/user:editownmessageprofile',-1000,1390419944,0),(201,1,7,'moodle/user:editownmessageprofile',1,1390419945,0),(202,1,1,'moodle/user:editownmessageprofile',1,1390419945,0),(203,1,3,'moodle/question:managecategory',1,1390419945,0),(204,1,1,'moodle/question:managecategory',1,1390419945,0),(205,1,3,'moodle/question:add',1,1390419945,0),(206,1,1,'moodle/question:add',1,1390419945,0),(207,1,3,'moodle/question:editmine',1,1390419946,0),(208,1,1,'moodle/question:editmine',1,1390419946,0),(209,1,3,'moodle/question:editall',1,1390419946,0),(210,1,1,'moodle/question:editall',1,1390419946,0),(211,1,3,'moodle/question:viewmine',1,1390419947,0),(212,1,1,'moodle/question:viewmine',1,1390419947,0),(213,1,3,'moodle/question:viewall',1,1390419947,0),(214,1,1,'moodle/question:viewall',1,1390419948,0),(215,1,3,'moodle/question:usemine',1,1390419948,0),(216,1,1,'moodle/question:usemine',1,1390419948,0),(217,1,3,'moodle/question:useall',1,1390419949,0),(218,1,1,'moodle/question:useall',1,1390419949,0),(219,1,3,'moodle/question:movemine',1,1390419949,0),(220,1,1,'moodle/question:movemine',1,1390419949,0),(221,1,3,'moodle/question:moveall',1,1390419949,0),(222,1,1,'moodle/question:moveall',1,1390419949,0),(223,1,1,'moodle/question:config',1,1390419950,0),(224,1,5,'moodle/question:flag',1,1390419950,0),(225,1,4,'moodle/question:flag',1,1390419950,0),(226,1,3,'moodle/question:flag',1,1390419950,0),(227,1,1,'moodle/question:flag',1,1390419950,0),(228,1,4,'moodle/site:doclinks',1,1390419951,0),(229,1,3,'moodle/site:doclinks',1,1390419951,0),(230,1,1,'moodle/site:doclinks',1,1390419951,0),(231,1,3,'moodle/course:sectionvisibility',1,1390419951,0),(232,1,1,'moodle/course:sectionvisibility',1,1390419951,0),(233,1,3,'moodle/course:useremail',1,1390419951,0),(234,1,1,'moodle/course:useremail',1,1390419951,0),(235,1,3,'moodle/course:viewhiddensections',1,1390419951,0),(236,1,1,'moodle/course:viewhiddensections',1,1390419952,0),(237,1,3,'moodle/course:setcurrentsection',1,1390419952,0),(238,1,1,'moodle/course:setcurrentsection',1,1390419952,0),(239,1,3,'moodle/course:movesections',1,1390419954,0),(240,1,1,'moodle/course:movesections',1,1390419954,0),(241,1,4,'moodle/grade:viewall',1,1390419954,0),(242,1,3,'moodle/grade:viewall',1,1390419954,0),(243,1,1,'moodle/grade:viewall',1,1390419954,0),(244,1,5,'moodle/grade:view',1,1390419955,0),(245,1,4,'moodle/grade:viewhidden',1,1390419955,0),(246,1,3,'moodle/grade:viewhidden',1,1390419955,0),(247,1,1,'moodle/grade:viewhidden',1,1390419955,0),(248,1,3,'moodle/grade:import',1,1390419955,0),(249,1,1,'moodle/grade:import',1,1390419955,0),(250,1,4,'moodle/grade:export',1,1390419955,0),(251,1,3,'moodle/grade:export',1,1390419955,0),(252,1,1,'moodle/grade:export',1,1390419955,0),(253,1,3,'moodle/grade:manage',1,1390419956,0),(254,1,1,'moodle/grade:manage',1,1390419956,0),(255,1,3,'moodle/grade:edit',1,1390419956,0),(256,1,1,'moodle/grade:edit',1,1390419956,0),(257,1,3,'moodle/grade:managegradingforms',1,1390419957,0),(258,1,1,'moodle/grade:managegradingforms',1,1390419957,0),(259,1,1,'moodle/grade:sharegradingforms',1,1390419957,0),(260,1,1,'moodle/grade:managesharedforms',1,1390419957,0),(261,1,3,'moodle/grade:manageoutcomes',1,1390419957,0),(262,1,1,'moodle/grade:manageoutcomes',1,1390419957,0),(263,1,3,'moodle/grade:manageletters',1,1390419958,0),(264,1,1,'moodle/grade:manageletters',1,1390419958,0),(265,1,3,'moodle/grade:hide',1,1390419958,0),(266,1,1,'moodle/grade:hide',1,1390419959,0),(267,1,3,'moodle/grade:lock',1,1390419959,0),(268,1,1,'moodle/grade:lock',1,1390419959,0),(269,1,3,'moodle/grade:unlock',1,1390419959,0),(270,1,1,'moodle/grade:unlock',1,1390419959,0),(271,1,7,'moodle/my:manageblocks',1,1390419960,0),(272,1,4,'moodle/notes:view',1,1390419960,0),(273,1,3,'moodle/notes:view',1,1390419960,0),(274,1,1,'moodle/notes:view',1,1390419960,0),(275,1,4,'moodle/notes:manage',1,1390419960,0),(276,1,3,'moodle/notes:manage',1,1390419960,0),(277,1,1,'moodle/notes:manage',1,1390419960,0),(278,1,4,'moodle/tag:manage',1,1390419961,0),(279,1,3,'moodle/tag:manage',1,1390419961,0),(280,1,1,'moodle/tag:manage',1,1390419961,0),(281,1,1,'moodle/tag:create',1,1390419961,0),(282,1,7,'moodle/tag:create',1,1390419961,0),(283,1,1,'moodle/tag:edit',1,1390419962,0),(284,1,7,'moodle/tag:edit',1,1390419962,0),(285,1,1,'moodle/tag:flag',1,1390419962,0),(286,1,7,'moodle/tag:flag',1,1390419962,0),(287,1,4,'moodle/tag:editblocks',1,1390419962,0),(288,1,3,'moodle/tag:editblocks',1,1390419962,0),(289,1,1,'moodle/tag:editblocks',1,1390419963,0),(290,1,6,'moodle/block:view',1,1390419963,0),(291,1,7,'moodle/block:view',1,1390419963,0),(292,1,5,'moodle/block:view',1,1390419963,0),(293,1,4,'moodle/block:view',1,1390419963,0),(294,1,3,'moodle/block:view',1,1390419963,0),(295,1,3,'moodle/block:edit',1,1390419964,0),(296,1,1,'moodle/block:edit',1,1390419964,0),(297,1,7,'moodle/portfolio:export',1,1390419964,0),(298,1,5,'moodle/portfolio:export',1,1390419965,0),(299,1,4,'moodle/portfolio:export',1,1390419966,0),(300,1,3,'moodle/portfolio:export',1,1390419966,0),(301,1,8,'moodle/comment:view',1,1390419966,0),(302,1,6,'moodle/comment:view',1,1390419967,0),(303,1,7,'moodle/comment:view',1,1390419967,0),(304,1,5,'moodle/comment:view',1,1390419967,0),(305,1,4,'moodle/comment:view',1,1390419967,0),(306,1,3,'moodle/comment:view',1,1390419967,0),(307,1,1,'moodle/comment:view',1,1390419968,0),(308,1,7,'moodle/comment:post',1,1390419968,0),(309,1,5,'moodle/comment:post',1,1390419969,0),(310,1,4,'moodle/comment:post',1,1390419969,0),(311,1,3,'moodle/comment:post',1,1390419969,0),(312,1,1,'moodle/comment:post',1,1390419969,0),(313,1,3,'moodle/comment:delete',1,1390419969,0),(314,1,1,'moodle/comment:delete',1,1390419969,0),(315,1,1,'moodle/webservice:createtoken',1,1390419969,0),(316,1,7,'moodle/webservice:createmobiletoken',1,1390419970,0),(317,1,7,'moodle/rating:view',1,1390419970,0),(318,1,5,'moodle/rating:view',1,1390419970,0),(319,1,4,'moodle/rating:view',1,1390419970,0),(320,1,3,'moodle/rating:view',1,1390419970,0),(321,1,1,'moodle/rating:view',1,1390419970,0),(322,1,7,'moodle/rating:viewany',1,1390419971,0),(323,1,5,'moodle/rating:viewany',1,1390419971,0),(324,1,4,'moodle/rating:viewany',1,1390419971,0),(325,1,3,'moodle/rating:viewany',1,1390419971,0),(326,1,1,'moodle/rating:viewany',1,1390419971,0),(327,1,7,'moodle/rating:viewall',1,1390419971,0),(328,1,5,'moodle/rating:viewall',1,1390419972,0),(329,1,4,'moodle/rating:viewall',1,1390419972,0),(330,1,3,'moodle/rating:viewall',1,1390419972,0),(331,1,1,'moodle/rating:viewall',1,1390419972,0),(332,1,7,'moodle/rating:rate',1,1390419972,0),(333,1,5,'moodle/rating:rate',1,1390419972,0),(334,1,4,'moodle/rating:rate',1,1390419972,0),(335,1,3,'moodle/rating:rate',1,1390419973,0),(336,1,1,'moodle/rating:rate',1,1390419973,0),(337,1,1,'moodle/course:publish',1,1390419973,0),(338,1,4,'moodle/course:markcomplete',1,1390419973,0),(339,1,3,'moodle/course:markcomplete',1,1390419974,0),(340,1,1,'moodle/course:markcomplete',1,1390419974,0),(341,1,1,'moodle/community:add',1,1390419974,0),(342,1,4,'moodle/community:add',1,1390419974,0),(343,1,3,'moodle/community:add',1,1390419975,0),(344,1,1,'moodle/community:download',1,1390419975,0),(345,1,3,'moodle/community:download',1,1390419975,0),(346,1,1,'moodle/badges:manageglobalsettings',1,1390419975,0),(347,1,7,'moodle/badges:viewbadges',1,1390419976,0),(348,1,7,'moodle/badges:manageownbadges',1,1390419977,0),(349,1,7,'moodle/badges:viewotherbadges',1,1390419978,0),(350,1,7,'moodle/badges:earnbadge',1,1390419979,0),(351,1,1,'moodle/badges:createbadge',1,1390419979,0),(352,1,3,'moodle/badges:createbadge',1,1390419979,0),(353,1,1,'moodle/badges:deletebadge',1,1390419979,0),(354,1,3,'moodle/badges:deletebadge',1,1390419979,0),(355,1,1,'moodle/badges:configuredetails',1,1390419980,0),(356,1,3,'moodle/badges:configuredetails',1,1390419980,0),(357,1,1,'moodle/badges:configurecriteria',1,1390419980,0),(358,1,3,'moodle/badges:configurecriteria',1,1390419980,0),(359,1,1,'moodle/badges:configuremessages',1,1390419981,0),(360,1,3,'moodle/badges:configuremessages',1,1390419981,0),(361,1,1,'moodle/badges:awardbadge',1,1390419982,0),(362,1,4,'moodle/badges:awardbadge',1,1390419982,0),(363,1,3,'moodle/badges:awardbadge',1,1390419982,0),(364,1,1,'moodle/badges:viewawarded',1,1390419982,0),(365,1,4,'moodle/badges:viewawarded',1,1390419982,0),(366,1,3,'moodle/badges:viewawarded',1,1390419983,0),(367,1,6,'mod/assign:view',1,1390420210,0),(368,1,5,'mod/assign:view',1,1390420210,0),(369,1,4,'mod/assign:view',1,1390420211,0),(370,1,3,'mod/assign:view',1,1390420211,0),(371,1,1,'mod/assign:view',1,1390420211,0),(372,1,5,'mod/assign:submit',1,1390420211,0),(373,1,4,'mod/assign:grade',1,1390420211,0),(374,1,3,'mod/assign:grade',1,1390420211,0),(375,1,1,'mod/assign:grade',1,1390420211,0),(376,1,4,'mod/assign:exportownsubmission',1,1390420211,0),(377,1,3,'mod/assign:exportownsubmission',1,1390420211,0),(378,1,1,'mod/assign:exportownsubmission',1,1390420211,0),(379,1,5,'mod/assign:exportownsubmission',1,1390420212,0),(380,1,3,'mod/assign:addinstance',1,1390420213,0),(381,1,1,'mod/assign:addinstance',1,1390420213,0),(382,1,4,'mod/assign:grantextension',1,1390420214,0),(383,1,3,'mod/assign:grantextension',1,1390420214,0),(384,1,1,'mod/assign:grantextension',1,1390420214,0),(385,1,3,'mod/assign:revealidentities',1,1390420214,0),(386,1,1,'mod/assign:revealidentities',1,1390420214,0),(387,1,3,'mod/assign:reviewgrades',1,1390420214,0),(388,1,1,'mod/assign:reviewgrades',1,1390420215,0),(389,1,3,'mod/assign:releasegrades',1,1390420215,0),(390,1,1,'mod/assign:releasegrades',1,1390420215,0),(391,1,3,'mod/assign:managegrades',1,1390420216,0),(392,1,1,'mod/assign:managegrades',1,1390420216,0),(393,1,3,'mod/assign:manageallocations',1,1390420216,0),(394,1,1,'mod/assign:manageallocations',1,1390420216,0),(395,1,6,'mod/assignment:view',1,1390420229,0),(396,1,5,'mod/assignment:view',1,1390420230,0),(397,1,4,'mod/assignment:view',1,1390420230,0),(398,1,3,'mod/assignment:view',1,1390420230,0),(399,1,1,'mod/assignment:view',1,1390420230,0),(400,1,3,'mod/assignment:addinstance',1,1390420230,0),(401,1,1,'mod/assignment:addinstance',1,1390420231,0),(402,1,5,'mod/assignment:submit',1,1390420231,0),(403,1,4,'mod/assignment:grade',1,1390420231,0),(404,1,3,'mod/assignment:grade',1,1390420231,0),(405,1,1,'mod/assignment:grade',1,1390420232,0),(406,1,4,'mod/assignment:exportownsubmission',1,1390420232,0),(407,1,3,'mod/assignment:exportownsubmission',1,1390420232,0),(408,1,1,'mod/assignment:exportownsubmission',1,1390420232,0),(409,1,5,'mod/assignment:exportownsubmission',1,1390420232,0),(410,1,3,'mod/book:addinstance',1,1390420236,0),(411,1,1,'mod/book:addinstance',1,1390420236,0),(412,1,6,'mod/book:read',1,1390420237,0),(413,1,8,'mod/book:read',1,1390420238,0),(414,1,5,'mod/book:read',1,1390420238,0),(415,1,4,'mod/book:read',1,1390420238,0),(416,1,3,'mod/book:read',1,1390420238,0),(417,1,1,'mod/book:read',1,1390420239,0),(418,1,4,'mod/book:viewhiddenchapters',1,1390420239,0),(419,1,3,'mod/book:viewhiddenchapters',1,1390420239,0),(420,1,1,'mod/book:viewhiddenchapters',1,1390420239,0),(421,1,3,'mod/book:edit',1,1390420239,0),(422,1,1,'mod/book:edit',1,1390420240,0),(423,1,3,'mod/chat:addinstance',1,1390420251,0),(424,1,1,'mod/chat:addinstance',1,1390420251,0),(425,1,5,'mod/chat:chat',1,1390420252,0),(426,1,4,'mod/chat:chat',1,1390420252,0),(427,1,3,'mod/chat:chat',1,1390420252,0),(428,1,1,'mod/chat:chat',1,1390420252,0),(429,1,5,'mod/chat:readlog',1,1390420252,0),(430,1,4,'mod/chat:readlog',1,1390420252,0),(431,1,3,'mod/chat:readlog',1,1390420253,0),(432,1,1,'mod/chat:readlog',1,1390420253,0),(433,1,4,'mod/chat:deletelog',1,1390420253,0),(434,1,3,'mod/chat:deletelog',1,1390420253,0),(435,1,1,'mod/chat:deletelog',1,1390420253,0),(436,1,4,'mod/chat:exportparticipatedsession',1,1390420254,0),(437,1,3,'mod/chat:exportparticipatedsession',1,1390420254,0),(438,1,1,'mod/chat:exportparticipatedsession',1,1390420254,0),(439,1,4,'mod/chat:exportsession',1,1390420254,0),(440,1,3,'mod/chat:exportsession',1,1390420254,0),(441,1,1,'mod/chat:exportsession',1,1390420255,0),(442,1,3,'mod/choice:addinstance',1,1390420260,0),(443,1,1,'mod/choice:addinstance',1,1390420260,0),(444,1,5,'mod/choice:choose',1,1390420261,0),(445,1,4,'mod/choice:choose',1,1390420261,0),(446,1,3,'mod/choice:choose',1,1390420261,0),(447,1,4,'mod/choice:readresponses',1,1390420262,0),(448,1,3,'mod/choice:readresponses',1,1390420262,0),(449,1,1,'mod/choice:readresponses',1,1390420263,0),(450,1,4,'mod/choice:deleteresponses',1,1390420264,0),(451,1,3,'mod/choice:deleteresponses',1,1390420264,0),(452,1,1,'mod/choice:deleteresponses',1,1390420264,0),(453,1,4,'mod/choice:downloadresponses',1,1390420265,0),(454,1,3,'mod/choice:downloadresponses',1,1390420265,0),(455,1,1,'mod/choice:downloadresponses',1,1390420265,0),(456,1,3,'mod/data:addinstance',1,1390420273,0),(457,1,1,'mod/data:addinstance',1,1390420273,0),(458,1,8,'mod/data:viewentry',1,1390420274,0),(459,1,6,'mod/data:viewentry',1,1390420274,0),(460,1,5,'mod/data:viewentry',1,1390420275,0),(461,1,4,'mod/data:viewentry',1,1390420275,0),(462,1,3,'mod/data:viewentry',1,1390420275,0),(463,1,1,'mod/data:viewentry',1,1390420275,0),(464,1,5,'mod/data:writeentry',1,1390420275,0),(465,1,4,'mod/data:writeentry',1,1390420276,0),(466,1,3,'mod/data:writeentry',1,1390420276,0),(467,1,1,'mod/data:writeentry',1,1390420276,0),(468,1,5,'mod/data:comment',1,1390420276,0),(469,1,4,'mod/data:comment',1,1390420277,0),(470,1,3,'mod/data:comment',1,1390420277,0),(471,1,1,'mod/data:comment',1,1390420277,0),(472,1,4,'mod/data:rate',1,1390420277,0),(473,1,3,'mod/data:rate',1,1390420277,0),(474,1,1,'mod/data:rate',1,1390420278,0),(475,1,4,'mod/data:viewrating',1,1390420278,0),(476,1,3,'mod/data:viewrating',1,1390420279,0),(477,1,1,'mod/data:viewrating',1,1390420279,0),(478,1,4,'mod/data:viewanyrating',1,1390420279,0),(479,1,3,'mod/data:viewanyrating',1,1390420279,0),(480,1,1,'mod/data:viewanyrating',1,1390420279,0),(481,1,4,'mod/data:viewallratings',1,1390420279,0),(482,1,3,'mod/data:viewallratings',1,1390420279,0),(483,1,1,'mod/data:viewallratings',1,1390420279,0),(484,1,4,'mod/data:approve',1,1390420279,0),(485,1,3,'mod/data:approve',1,1390420279,0),(486,1,1,'mod/data:approve',1,1390420280,0),(487,1,4,'mod/data:manageentries',1,1390420280,0),(488,1,3,'mod/data:manageentries',1,1390420280,0),(489,1,1,'mod/data:manageentries',1,1390420280,0),(490,1,4,'mod/data:managecomments',1,1390420281,0),(491,1,3,'mod/data:managecomments',1,1390420281,0),(492,1,1,'mod/data:managecomments',1,1390420281,0),(493,1,3,'mod/data:managetemplates',1,1390420281,0),(494,1,1,'mod/data:managetemplates',1,1390420282,0),(495,1,4,'mod/data:viewalluserpresets',1,1390420282,0),(496,1,3,'mod/data:viewalluserpresets',1,1390420282,0),(497,1,1,'mod/data:viewalluserpresets',1,1390420282,0),(498,1,1,'mod/data:manageuserpresets',1,1390420282,0),(499,1,1,'mod/data:exportentry',1,1390420283,0),(500,1,4,'mod/data:exportentry',1,1390420283,0),(501,1,3,'mod/data:exportentry',1,1390420283,0),(502,1,1,'mod/data:exportownentry',1,1390420284,0),(503,1,4,'mod/data:exportownentry',1,1390420284,0),(504,1,3,'mod/data:exportownentry',1,1390420284,0),(505,1,5,'mod/data:exportownentry',1,1390420284,0),(506,1,1,'mod/data:exportallentries',1,1390420284,0),(507,1,4,'mod/data:exportallentries',1,1390420284,0),(508,1,3,'mod/data:exportallentries',1,1390420284,0),(509,1,1,'mod/data:exportuserinfo',1,1390420285,0),(510,1,4,'mod/data:exportuserinfo',1,1390420285,0),(511,1,3,'mod/data:exportuserinfo',1,1390420286,0),(512,1,3,'mod/feedback:addinstance',1,1390420299,0),(513,1,1,'mod/feedback:addinstance',1,1390420300,0),(514,1,6,'mod/feedback:view',1,1390420300,0),(515,1,5,'mod/feedback:view',1,1390420300,0),(516,1,4,'mod/feedback:view',1,1390420300,0),(517,1,3,'mod/feedback:view',1,1390420300,0),(518,1,1,'mod/feedback:view',1,1390420300,0),(519,1,5,'mod/feedback:complete',1,1390420301,0),(520,1,5,'mod/feedback:viewanalysepage',1,1390420301,0),(521,1,3,'mod/feedback:viewanalysepage',1,1390420301,0),(522,1,1,'mod/feedback:viewanalysepage',1,1390420301,0),(523,1,3,'mod/feedback:deletesubmissions',1,1390420301,0),(524,1,1,'mod/feedback:deletesubmissions',1,1390420301,0),(525,1,1,'mod/feedback:mapcourse',1,1390420301,0),(526,1,3,'mod/feedback:edititems',1,1390420302,0),(527,1,1,'mod/feedback:edititems',1,1390420302,0),(528,1,3,'mod/feedback:createprivatetemplate',1,1390420302,0),(529,1,1,'mod/feedback:createprivatetemplate',1,1390420302,0),(530,1,3,'mod/feedback:createpublictemplate',1,1390420302,0),(531,1,1,'mod/feedback:createpublictemplate',1,1390420302,0),(532,1,3,'mod/feedback:deletetemplate',1,1390420303,0),(533,1,1,'mod/feedback:deletetemplate',1,1390420303,0),(534,1,4,'mod/feedback:viewreports',1,1390420303,0),(535,1,3,'mod/feedback:viewreports',1,1390420303,0),(536,1,1,'mod/feedback:viewreports',1,1390420303,0),(537,1,4,'mod/feedback:receivemail',1,1390420303,0),(538,1,3,'mod/feedback:receivemail',1,1390420303,0),(539,1,3,'mod/folder:addinstance',1,1390420306,0),(540,1,1,'mod/folder:addinstance',1,1390420306,0),(541,1,6,'mod/folder:view',1,1390420307,0),(542,1,7,'mod/folder:view',1,1390420307,0),(543,1,3,'mod/folder:managefiles',1,1390420307,0),(544,1,3,'mod/forum:addinstance',1,1390420319,0),(545,1,1,'mod/forum:addinstance',1,1390420319,0),(546,1,8,'mod/forum:viewdiscussion',1,1390420320,0),(547,1,6,'mod/forum:viewdiscussion',1,1390420321,0),(548,1,5,'mod/forum:viewdiscussion',1,1390420321,0),(549,1,4,'mod/forum:viewdiscussion',1,1390420322,0),(550,1,3,'mod/forum:viewdiscussion',1,1390420322,0),(551,1,1,'mod/forum:viewdiscussion',1,1390420322,0),(552,1,4,'mod/forum:viewhiddentimedposts',1,1390420322,0),(553,1,3,'mod/forum:viewhiddentimedposts',1,1390420322,0),(554,1,1,'mod/forum:viewhiddentimedposts',1,1390420322,0),(555,1,5,'mod/forum:startdiscussion',1,1390420322,0),(556,1,4,'mod/forum:startdiscussion',1,1390420322,0),(557,1,3,'mod/forum:startdiscussion',1,1390420323,0),(558,1,1,'mod/forum:startdiscussion',1,1390420323,0),(559,1,5,'mod/forum:replypost',1,1390420323,0),(560,1,4,'mod/forum:replypost',1,1390420323,0),(561,1,3,'mod/forum:replypost',1,1390420323,0),(562,1,1,'mod/forum:replypost',1,1390420324,0),(563,1,4,'mod/forum:addnews',1,1390420324,0),(564,1,3,'mod/forum:addnews',1,1390420324,0),(565,1,1,'mod/forum:addnews',1,1390420325,0),(566,1,4,'mod/forum:replynews',1,1390420325,0),(567,1,3,'mod/forum:replynews',1,1390420325,0),(568,1,1,'mod/forum:replynews',1,1390420325,0),(569,1,5,'mod/forum:viewrating',1,1390420326,0),(570,1,4,'mod/forum:viewrating',1,1390420326,0),(571,1,3,'mod/forum:viewrating',1,1390420326,0),(572,1,1,'mod/forum:viewrating',1,1390420326,0),(573,1,4,'mod/forum:viewanyrating',1,1390420326,0),(574,1,3,'mod/forum:viewanyrating',1,1390420326,0),(575,1,1,'mod/forum:viewanyrating',1,1390420326,0),(576,1,4,'mod/forum:viewallratings',1,1390420326,0),(577,1,3,'mod/forum:viewallratings',1,1390420327,0),(578,1,1,'mod/forum:viewallratings',1,1390420327,0),(579,1,4,'mod/forum:rate',1,1390420327,0),(580,1,3,'mod/forum:rate',1,1390420327,0),(581,1,1,'mod/forum:rate',1,1390420327,0),(582,1,5,'mod/forum:createattachment',1,1390420327,0),(583,1,4,'mod/forum:createattachment',1,1390420327,0),(584,1,3,'mod/forum:createattachment',1,1390420327,0),(585,1,1,'mod/forum:createattachment',1,1390420327,0),(586,1,5,'mod/forum:deleteownpost',1,1390420327,0),(587,1,4,'mod/forum:deleteownpost',1,1390420327,0),(588,1,3,'mod/forum:deleteownpost',1,1390420327,0),(589,1,1,'mod/forum:deleteownpost',1,1390420328,0),(590,1,4,'mod/forum:deleteanypost',1,1390420328,0),(591,1,3,'mod/forum:deleteanypost',1,1390420328,0),(592,1,1,'mod/forum:deleteanypost',1,1390420328,0),(593,1,4,'mod/forum:splitdiscussions',1,1390420328,0),(594,1,3,'mod/forum:splitdiscussions',1,1390420328,0),(595,1,1,'mod/forum:splitdiscussions',1,1390420329,0),(596,1,4,'mod/forum:movediscussions',1,1390420329,0),(597,1,3,'mod/forum:movediscussions',1,1390420329,0),(598,1,1,'mod/forum:movediscussions',1,1390420329,0),(599,1,4,'mod/forum:editanypost',1,1390420330,0),(600,1,3,'mod/forum:editanypost',1,1390420330,0),(601,1,1,'mod/forum:editanypost',1,1390420330,0),(602,1,4,'mod/forum:viewqandawithoutposting',1,1390420330,0),(603,1,3,'mod/forum:viewqandawithoutposting',1,1390420330,0),(604,1,1,'mod/forum:viewqandawithoutposting',1,1390420331,0),(605,1,4,'mod/forum:viewsubscribers',1,1390420331,0),(606,1,3,'mod/forum:viewsubscribers',1,1390420331,0),(607,1,1,'mod/forum:viewsubscribers',1,1390420331,0),(608,1,4,'mod/forum:managesubscriptions',1,1390420331,0),(609,1,3,'mod/forum:managesubscriptions',1,1390420331,0),(610,1,1,'mod/forum:managesubscriptions',1,1390420332,0),(611,1,4,'mod/forum:postwithoutthrottling',1,1390420333,0),(612,1,3,'mod/forum:postwithoutthrottling',1,1390420333,0),(613,1,1,'mod/forum:postwithoutthrottling',1,1390420334,0),(614,1,4,'mod/forum:exportdiscussion',1,1390420334,0),(615,1,3,'mod/forum:exportdiscussion',1,1390420334,0),(616,1,1,'mod/forum:exportdiscussion',1,1390420334,0),(617,1,4,'mod/forum:exportpost',1,1390420335,0),(618,1,3,'mod/forum:exportpost',1,1390420335,0),(619,1,1,'mod/forum:exportpost',1,1390420335,0),(620,1,4,'mod/forum:exportownpost',1,1390420335,0),(621,1,3,'mod/forum:exportownpost',1,1390420335,0),(622,1,1,'mod/forum:exportownpost',1,1390420335,0),(623,1,5,'mod/forum:exportownpost',1,1390420335,0),(624,1,4,'mod/forum:addquestion',1,1390420336,0),(625,1,3,'mod/forum:addquestion',1,1390420336,0),(626,1,1,'mod/forum:addquestion',1,1390420336,0),(627,1,5,'mod/forum:allowforcesubscribe',1,1390420336,0),(628,1,4,'mod/forum:allowforcesubscribe',1,1390420337,0),(629,1,3,'mod/forum:allowforcesubscribe',1,1390420337,0),(630,1,8,'mod/forum:allowforcesubscribe',1,1390420337,0),(631,1,3,'mod/glossary:addinstance',1,1390420346,0),(632,1,1,'mod/glossary:addinstance',1,1390420346,0),(633,1,8,'mod/glossary:view',1,1390420346,0),(634,1,6,'mod/glossary:view',1,1390420346,0),(635,1,5,'mod/glossary:view',1,1390420346,0),(636,1,4,'mod/glossary:view',1,1390420347,0),(637,1,3,'mod/glossary:view',1,1390420347,0),(638,1,1,'mod/glossary:view',1,1390420347,0),(639,1,5,'mod/glossary:write',1,1390420347,0),(640,1,4,'mod/glossary:write',1,1390420347,0),(641,1,3,'mod/glossary:write',1,1390420347,0),(642,1,1,'mod/glossary:write',1,1390420347,0),(643,1,4,'mod/glossary:manageentries',1,1390420347,0),(644,1,3,'mod/glossary:manageentries',1,1390420348,0),(645,1,1,'mod/glossary:manageentries',1,1390420348,0),(646,1,4,'mod/glossary:managecategories',1,1390420348,0),(647,1,3,'mod/glossary:managecategories',1,1390420348,0),(648,1,1,'mod/glossary:managecategories',1,1390420349,0),(649,1,5,'mod/glossary:comment',1,1390420349,0),(650,1,4,'mod/glossary:comment',1,1390420349,0),(651,1,3,'mod/glossary:comment',1,1390420349,0),(652,1,1,'mod/glossary:comment',1,1390420349,0),(653,1,4,'mod/glossary:managecomments',1,1390420349,0),(654,1,3,'mod/glossary:managecomments',1,1390420349,0),(655,1,1,'mod/glossary:managecomments',1,1390420349,0),(656,1,4,'mod/glossary:import',1,1390420349,0),(657,1,3,'mod/glossary:import',1,1390420349,0),(658,1,1,'mod/glossary:import',1,1390420349,0),(659,1,4,'mod/glossary:export',1,1390420350,0),(660,1,3,'mod/glossary:export',1,1390420350,0),(661,1,1,'mod/glossary:export',1,1390420350,0),(662,1,4,'mod/glossary:approve',1,1390420350,0),(663,1,3,'mod/glossary:approve',1,1390420350,0),(664,1,1,'mod/glossary:approve',1,1390420351,0),(665,1,4,'mod/glossary:rate',1,1390420351,0),(666,1,3,'mod/glossary:rate',1,1390420351,0),(667,1,1,'mod/glossary:rate',1,1390420351,0),(668,1,4,'mod/glossary:viewrating',1,1390420351,0),(669,1,3,'mod/glossary:viewrating',1,1390420351,0),(670,1,1,'mod/glossary:viewrating',1,1390420352,0),(671,1,4,'mod/glossary:viewanyrating',1,1390420352,0),(672,1,3,'mod/glossary:viewanyrating',1,1390420352,0),(673,1,1,'mod/glossary:viewanyrating',1,1390420352,0),(674,1,4,'mod/glossary:viewallratings',1,1390420352,0),(675,1,3,'mod/glossary:viewallratings',1,1390420352,0),(676,1,1,'mod/glossary:viewallratings',1,1390420352,0),(677,1,4,'mod/glossary:exportentry',1,1390420352,0),(678,1,3,'mod/glossary:exportentry',1,1390420352,0),(679,1,1,'mod/glossary:exportentry',1,1390420352,0),(680,1,4,'mod/glossary:exportownentry',1,1390420353,0),(681,1,3,'mod/glossary:exportownentry',1,1390420353,0),(682,1,1,'mod/glossary:exportownentry',1,1390420353,0),(683,1,5,'mod/glossary:exportownentry',1,1390420353,0),(684,1,6,'mod/imscp:view',1,1390420358,0),(685,1,7,'mod/imscp:view',1,1390420358,0),(686,1,3,'mod/imscp:addinstance',1,1390420359,0),(687,1,1,'mod/imscp:addinstance',1,1390420359,0),(688,1,3,'mod/label:addinstance',1,1390420362,0),(689,1,1,'mod/label:addinstance',1,1390420362,0),(690,1,3,'mod/lesson:addinstance',1,1390420369,0),(691,1,1,'mod/lesson:addinstance',1,1390420369,0),(692,1,3,'mod/lesson:edit',1,1390420370,0),(693,1,1,'mod/lesson:edit',1,1390420370,0),(694,1,4,'mod/lesson:manage',1,1390420371,0),(695,1,3,'mod/lesson:manage',1,1390420371,0),(696,1,1,'mod/lesson:manage',1,1390420371,0),(697,1,5,'mod/lti:view',1,1390420376,0),(698,1,4,'mod/lti:view',1,1390420376,0),(699,1,3,'mod/lti:view',1,1390420376,0),(700,1,1,'mod/lti:view',1,1390420377,0),(701,1,3,'mod/lti:addinstance',1,1390420377,0),(702,1,1,'mod/lti:addinstance',1,1390420377,0),(703,1,4,'mod/lti:grade',1,1390420377,0),(704,1,3,'mod/lti:grade',1,1390420377,0),(705,1,1,'mod/lti:grade',1,1390420377,0),(706,1,4,'mod/lti:manage',1,1390420377,0),(707,1,3,'mod/lti:manage',1,1390420378,0),(708,1,1,'mod/lti:manage',1,1390420378,0),(709,1,3,'mod/lti:addcoursetool',1,1390420378,0),(710,1,1,'mod/lti:addcoursetool',1,1390420378,0),(711,1,3,'mod/lti:requesttooladd',1,1390420378,0),(712,1,1,'mod/lti:requesttooladd',1,1390420378,0),(713,1,6,'mod/page:view',1,1390420383,0),(714,1,7,'mod/page:view',1,1390420383,0),(715,1,3,'mod/page:addinstance',1,1390420384,0),(716,1,1,'mod/page:addinstance',1,1390420384,0),(717,1,6,'mod/quiz:view',1,1390420391,0),(718,1,5,'mod/quiz:view',1,1390420392,0),(719,1,4,'mod/quiz:view',1,1390420392,0),(720,1,3,'mod/quiz:view',1,1390420392,0),(721,1,1,'mod/quiz:view',1,1390420392,0),(722,1,3,'mod/quiz:addinstance',1,1390420393,0),(723,1,1,'mod/quiz:addinstance',1,1390420394,0),(724,1,5,'mod/quiz:attempt',1,1390420394,0),(725,1,5,'mod/quiz:reviewmyattempts',1,1390420395,0),(726,1,3,'mod/quiz:manage',1,1390420395,0),(727,1,1,'mod/quiz:manage',1,1390420395,0),(728,1,3,'mod/quiz:manageoverrides',1,1390420395,0),(729,1,1,'mod/quiz:manageoverrides',1,1390420396,0),(730,1,4,'mod/quiz:preview',1,1390420396,0),(731,1,3,'mod/quiz:preview',1,1390420396,0),(732,1,1,'mod/quiz:preview',1,1390420396,0),(733,1,4,'mod/quiz:grade',1,1390420396,0),(734,1,3,'mod/quiz:grade',1,1390420397,0),(735,1,1,'mod/quiz:grade',1,1390420397,0),(736,1,4,'mod/quiz:regrade',1,1390420397,0),(737,1,3,'mod/quiz:regrade',1,1390420397,0),(738,1,1,'mod/quiz:regrade',1,1390420397,0),(739,1,4,'mod/quiz:viewreports',1,1390420398,0),(740,1,3,'mod/quiz:viewreports',1,1390420398,0),(741,1,1,'mod/quiz:viewreports',1,1390420398,0),(742,1,3,'mod/quiz:deleteattempts',1,1390420399,0),(743,1,1,'mod/quiz:deleteattempts',1,1390420399,0),(744,1,6,'mod/resource:view',1,1390420404,0),(745,1,7,'mod/resource:view',1,1390420404,0),(746,1,3,'mod/resource:addinstance',1,1390420404,0),(747,1,1,'mod/resource:addinstance',1,1390420405,0),(748,1,3,'mod/scorm:addinstance',1,1390420418,0),(749,1,1,'mod/scorm:addinstance',1,1390420418,0),(750,1,4,'mod/scorm:viewreport',1,1390420419,0),(751,1,3,'mod/scorm:viewreport',1,1390420419,0),(752,1,1,'mod/scorm:viewreport',1,1390420419,0),(753,1,5,'mod/scorm:skipview',1,1390420419,0),(754,1,5,'mod/scorm:savetrack',1,1390420420,0),(755,1,4,'mod/scorm:savetrack',1,1390420420,0),(756,1,3,'mod/scorm:savetrack',1,1390420420,0),(757,1,1,'mod/scorm:savetrack',1,1390420420,0),(758,1,5,'mod/scorm:viewscores',1,1390420421,0),(759,1,4,'mod/scorm:viewscores',1,1390420421,0),(760,1,3,'mod/scorm:viewscores',1,1390420421,0),(761,1,1,'mod/scorm:viewscores',1,1390420421,0),(762,1,4,'mod/scorm:deleteresponses',1,1390420422,0),(763,1,3,'mod/scorm:deleteresponses',1,1390420422,0),(764,1,1,'mod/scorm:deleteresponses',1,1390420422,0),(765,1,3,'mod/survey:addinstance',1,1390420444,0),(766,1,1,'mod/survey:addinstance',1,1390420445,0),(767,1,5,'mod/survey:participate',1,1390420445,0),(768,1,4,'mod/survey:participate',1,1390420445,0),(769,1,3,'mod/survey:participate',1,1390420445,0),(770,1,1,'mod/survey:participate',1,1390420445,0),(771,1,4,'mod/survey:readresponses',1,1390420445,0),(772,1,3,'mod/survey:readresponses',1,1390420445,0),(773,1,1,'mod/survey:readresponses',1,1390420446,0),(774,1,4,'mod/survey:download',1,1390420446,0),(775,1,3,'mod/survey:download',1,1390420446,0),(776,1,1,'mod/survey:download',1,1390420446,0),(777,1,6,'mod/url:view',1,1390420449,0),(778,1,7,'mod/url:view',1,1390420450,0),(779,1,3,'mod/url:addinstance',1,1390420450,0),(780,1,1,'mod/url:addinstance',1,1390420450,0),(781,1,3,'mod/wiki:addinstance',1,1390420460,0),(782,1,1,'mod/wiki:addinstance',1,1390420460,0),(783,1,6,'mod/wiki:viewpage',1,1390420460,0),(784,1,5,'mod/wiki:viewpage',1,1390420460,0),(785,1,4,'mod/wiki:viewpage',1,1390420460,0),(786,1,3,'mod/wiki:viewpage',1,1390420460,0),(787,1,1,'mod/wiki:viewpage',1,1390420460,0),(788,1,5,'mod/wiki:editpage',1,1390420460,0),(789,1,4,'mod/wiki:editpage',1,1390420461,0),(790,1,3,'mod/wiki:editpage',1,1390420461,0),(791,1,1,'mod/wiki:editpage',1,1390420461,0),(792,1,5,'mod/wiki:createpage',1,1390420461,0),(793,1,4,'mod/wiki:createpage',1,1390420462,0),(794,1,3,'mod/wiki:createpage',1,1390420462,0),(795,1,1,'mod/wiki:createpage',1,1390420462,0),(796,1,5,'mod/wiki:viewcomment',1,1390420462,0),(797,1,4,'mod/wiki:viewcomment',1,1390420462,0),(798,1,3,'mod/wiki:viewcomment',1,1390420462,0),(799,1,1,'mod/wiki:viewcomment',1,1390420462,0),(800,1,5,'mod/wiki:editcomment',1,1390420463,0),(801,1,4,'mod/wiki:editcomment',1,1390420463,0),(802,1,3,'mod/wiki:editcomment',1,1390420463,0),(803,1,1,'mod/wiki:editcomment',1,1390420463,0),(804,1,4,'mod/wiki:managecomment',1,1390420463,0),(805,1,3,'mod/wiki:managecomment',1,1390420463,0),(806,1,1,'mod/wiki:managecomment',1,1390420463,0),(807,1,4,'mod/wiki:managefiles',1,1390420464,0),(808,1,3,'mod/wiki:managefiles',1,1390420464,0),(809,1,1,'mod/wiki:managefiles',1,1390420464,0),(810,1,4,'mod/wiki:overridelock',1,1390420464,0),(811,1,3,'mod/wiki:overridelock',1,1390420464,0),(812,1,1,'mod/wiki:overridelock',1,1390420464,0),(813,1,4,'mod/wiki:managewiki',1,1390420467,0),(814,1,3,'mod/wiki:managewiki',1,1390420467,0),(815,1,1,'mod/wiki:managewiki',1,1390420468,0),(816,1,6,'mod/workshop:view',1,1390420484,0),(817,1,5,'mod/workshop:view',1,1390420484,0),(818,1,4,'mod/workshop:view',1,1390420485,0),(819,1,3,'mod/workshop:view',1,1390420485,0),(820,1,1,'mod/workshop:view',1,1390420485,0),(821,1,3,'mod/workshop:addinstance',1,1390420485,0),(822,1,1,'mod/workshop:addinstance',1,1390420485,0),(823,1,4,'mod/workshop:switchphase',1,1390420485,0),(824,1,3,'mod/workshop:switchphase',1,1390420485,0),(825,1,1,'mod/workshop:switchphase',1,1390420485,0),(826,1,3,'mod/workshop:editdimensions',1,1390420486,0),(827,1,1,'mod/workshop:editdimensions',1,1390420486,0),(828,1,5,'mod/workshop:submit',1,1390420486,0),(829,1,5,'mod/workshop:peerassess',1,1390420486,0),(830,1,4,'mod/workshop:manageexamples',1,1390420487,0),(831,1,3,'mod/workshop:manageexamples',1,1390420487,0),(832,1,1,'mod/workshop:manageexamples',1,1390420487,0),(833,1,4,'mod/workshop:allocate',1,1390420487,0),(834,1,3,'mod/workshop:allocate',1,1390420488,0),(835,1,1,'mod/workshop:allocate',1,1390420488,0),(836,1,4,'mod/workshop:publishsubmissions',1,1390420488,0),(837,1,3,'mod/workshop:publishsubmissions',1,1390420489,0),(838,1,1,'mod/workshop:publishsubmissions',1,1390420490,0),(839,1,5,'mod/workshop:viewauthornames',1,1390420491,0),(840,1,4,'mod/workshop:viewauthornames',1,1390420491,0),(841,1,3,'mod/workshop:viewauthornames',1,1390420492,0),(842,1,1,'mod/workshop:viewauthornames',1,1390420492,0),(843,1,4,'mod/workshop:viewreviewernames',1,1390420492,0),(844,1,3,'mod/workshop:viewreviewernames',1,1390420492,0),(845,1,1,'mod/workshop:viewreviewernames',1,1390420492,0),(846,1,4,'mod/workshop:viewallsubmissions',1,1390420492,0),(847,1,3,'mod/workshop:viewallsubmissions',1,1390420493,0),(848,1,1,'mod/workshop:viewallsubmissions',1,1390420493,0),(849,1,5,'mod/workshop:viewpublishedsubmissions',1,1390420493,0),(850,1,4,'mod/workshop:viewpublishedsubmissions',1,1390420493,0),(851,1,3,'mod/workshop:viewpublishedsubmissions',1,1390420493,0),(852,1,1,'mod/workshop:viewpublishedsubmissions',1,1390420494,0),(853,1,5,'mod/workshop:viewauthorpublished',1,1390420494,0),(854,1,4,'mod/workshop:viewauthorpublished',1,1390420494,0),(855,1,3,'mod/workshop:viewauthorpublished',1,1390420494,0),(856,1,1,'mod/workshop:viewauthorpublished',1,1390420494,0),(857,1,4,'mod/workshop:viewallassessments',1,1390420495,0),(858,1,3,'mod/workshop:viewallassessments',1,1390420495,0),(859,1,1,'mod/workshop:viewallassessments',1,1390420495,0),(860,1,4,'mod/workshop:overridegrades',1,1390420495,0),(861,1,3,'mod/workshop:overridegrades',1,1390420495,0),(862,1,1,'mod/workshop:overridegrades',1,1390420495,0),(863,1,4,'mod/workshop:ignoredeadlines',1,1390420495,0),(864,1,3,'mod/workshop:ignoredeadlines',1,1390420496,0),(865,1,1,'mod/workshop:ignoredeadlines',1,1390420496,0),(866,1,3,'enrol/cohort:config',1,1390420532,0),(867,1,1,'enrol/cohort:config',1,1390420532,0),(868,1,1,'enrol/cohort:unenrol',1,1390420532,0),(869,1,1,'enrol/database:unenrol',1,1390420534,0),(870,1,1,'enrol/guest:config',1,1390420538,0),(871,1,3,'enrol/guest:config',1,1390420538,0),(872,1,1,'enrol/ldap:manage',1,1390420543,0),(873,1,1,'enrol/manual:config',1,1390420545,0),(874,1,1,'enrol/manual:enrol',1,1390420545,0),(875,1,3,'enrol/manual:enrol',1,1390420545,0),(876,1,1,'enrol/manual:manage',1,1390420546,0),(877,1,3,'enrol/manual:manage',1,1390420546,0),(878,1,1,'enrol/manual:unenrol',1,1390420546,0),(879,1,3,'enrol/manual:unenrol',1,1390420547,0),(880,1,1,'enrol/meta:config',1,1390420548,0),(881,1,3,'enrol/meta:config',1,1390420548,0),(882,1,1,'enrol/meta:selectaslinked',1,1390420548,0),(883,1,1,'enrol/meta:unenrol',1,1390420549,0),(884,1,1,'enrol/paypal:config',1,1390420559,0),(885,1,1,'enrol/paypal:manage',1,1390420560,0),(886,1,3,'enrol/paypal:manage',1,1390420560,0),(887,1,1,'enrol/paypal:unenrol',1,1390420560,0),(888,1,3,'enrol/self:config',1,1390420562,0),(889,1,1,'enrol/self:config',1,1390420562,0),(890,1,3,'enrol/self:manage',1,1390420562,0),(891,1,1,'enrol/self:manage',1,1390420562,0),(892,1,5,'enrol/self:unenrolself',1,1390420563,0),(893,1,3,'enrol/self:unenrol',1,1390420563,0),(894,1,1,'enrol/self:unenrol',1,1390420563,0),(895,1,3,'block/activity_modules:addinstance',1,1390420570,0),(896,1,1,'block/activity_modules:addinstance',1,1390420570,0),(897,1,7,'block/admin_bookmarks:myaddinstance',1,1390420570,0),(898,1,3,'block/admin_bookmarks:addinstance',1,1390420570,0),(899,1,1,'block/admin_bookmarks:addinstance',1,1390420571,0),(900,1,3,'block/badges:addinstance',1,1390420572,0),(901,1,1,'block/badges:addinstance',1,1390420572,0),(902,1,7,'block/badges:myaddinstance',1,1390420572,0),(903,1,3,'block/blog_menu:addinstance',1,1390420573,0),(904,1,1,'block/blog_menu:addinstance',1,1390420573,0),(905,1,3,'block/blog_recent:addinstance',1,1390420575,0),(906,1,1,'block/blog_recent:addinstance',1,1390420575,0),(907,1,3,'block/blog_tags:addinstance',1,1390420576,0),(908,1,1,'block/blog_tags:addinstance',1,1390420576,0),(909,1,7,'block/calendar_month:myaddinstance',1,1390420577,0),(910,1,3,'block/calendar_month:addinstance',1,1390420577,0),(911,1,1,'block/calendar_month:addinstance',1,1390420579,0),(912,1,7,'block/calendar_upcoming:myaddinstance',1,1390420580,0),(913,1,3,'block/calendar_upcoming:addinstance',1,1390420580,0),(914,1,1,'block/calendar_upcoming:addinstance',1,1390420581,0),(915,1,7,'block/comments:myaddinstance',1,1390420582,0),(916,1,3,'block/comments:addinstance',1,1390420582,0),(917,1,1,'block/comments:addinstance',1,1390420582,0),(918,1,7,'block/community:myaddinstance',1,1390420584,0),(919,1,3,'block/community:addinstance',1,1390420585,0),(920,1,1,'block/community:addinstance',1,1390420585,0),(921,1,3,'block/completionstatus:addinstance',1,1390420586,0),(922,1,1,'block/completionstatus:addinstance',1,1390420586,0),(923,1,7,'block/course_list:myaddinstance',1,1390420587,0),(924,1,3,'block/course_list:addinstance',1,1390420588,0),(925,1,1,'block/course_list:addinstance',1,1390420588,0),(926,1,7,'block/course_overview:myaddinstance',1,1390420591,0),(927,1,3,'block/course_overview:addinstance',1,1390420592,0),(928,1,1,'block/course_overview:addinstance',1,1390420592,0),(929,1,3,'block/course_summary:addinstance',1,1390420594,0),(930,1,1,'block/course_summary:addinstance',1,1390420594,0),(931,1,3,'block/feedback:addinstance',1,1390420595,0),(932,1,1,'block/feedback:addinstance',1,1390420595,0),(933,1,7,'block/glossary_random:myaddinstance',1,1390420596,0),(934,1,3,'block/glossary_random:addinstance',1,1390420596,0),(935,1,1,'block/glossary_random:addinstance',1,1390420596,0),(936,1,7,'block/html:myaddinstance',1,1390420597,0),(937,1,3,'block/html:addinstance',1,1390420597,0),(938,1,1,'block/html:addinstance',1,1390420597,0),(939,1,3,'block/login:addinstance',1,1390420598,0),(940,1,1,'block/login:addinstance',1,1390420598,0),(941,1,7,'block/mentees:myaddinstance',1,1390420600,0),(942,1,3,'block/mentees:addinstance',1,1390420600,0),(943,1,1,'block/mentees:addinstance',1,1390420601,0),(944,1,7,'block/messages:myaddinstance',1,1390420601,0),(945,1,3,'block/messages:addinstance',1,1390420602,0),(946,1,1,'block/messages:addinstance',1,1390420603,0),(947,1,7,'block/mnet_hosts:myaddinstance',1,1390420605,0),(948,1,3,'block/mnet_hosts:addinstance',1,1390420605,0),(949,1,1,'block/mnet_hosts:addinstance',1,1390420605,0),(950,1,7,'block/myprofile:myaddinstance',1,1390420607,0),(951,1,3,'block/myprofile:addinstance',1,1390420607,0),(952,1,1,'block/myprofile:addinstance',1,1390420607,0),(953,1,7,'block/navigation:myaddinstance',1,1390420608,0),(954,1,3,'block/navigation:addinstance',1,1390420608,0),(955,1,1,'block/navigation:addinstance',1,1390420608,0),(956,1,7,'block/news_items:myaddinstance',1,1390420610,0),(957,1,3,'block/news_items:addinstance',1,1390420610,0),(958,1,1,'block/news_items:addinstance',1,1390420610,0),(959,1,7,'block/online_users:myaddinstance',1,1390420611,0),(960,1,3,'block/online_users:addinstance',1,1390420612,0),(961,1,1,'block/online_users:addinstance',1,1390420612,0),(962,1,7,'block/online_users:viewlist',1,1390420613,0),(963,1,6,'block/online_users:viewlist',1,1390420613,0),(964,1,5,'block/online_users:viewlist',1,1390420614,0),(965,1,4,'block/online_users:viewlist',1,1390420614,0),(966,1,3,'block/online_users:viewlist',1,1390420614,0),(967,1,1,'block/online_users:viewlist',1,1390420614,0),(968,1,3,'block/participants:addinstance',1,1390420615,0),(969,1,1,'block/participants:addinstance',1,1390420615,0),(970,1,7,'block/private_files:myaddinstance',1,1390420616,0),(971,1,3,'block/private_files:addinstance',1,1390420617,0),(972,1,1,'block/private_files:addinstance',1,1390420617,0),(973,1,3,'block/quiz_results:addinstance',1,1390420618,0),(974,1,1,'block/quiz_results:addinstance',1,1390420618,0),(975,1,3,'block/recent_activity:addinstance',1,1390420619,0),(976,1,1,'block/recent_activity:addinstance',1,1390420619,0),(977,1,7,'block/rss_client:myaddinstance',1,1390420620,0),(978,1,3,'block/rss_client:addinstance',1,1390420621,0),(979,1,1,'block/rss_client:addinstance',1,1390420621,0),(980,1,4,'block/rss_client:manageownfeeds',1,1390420621,0),(981,1,3,'block/rss_client:manageownfeeds',1,1390420621,0),(982,1,1,'block/rss_client:manageownfeeds',1,1390420621,0),(983,1,1,'block/rss_client:manageanyfeeds',1,1390420621,0),(984,1,3,'block/search_forums:addinstance',1,1390420623,0),(985,1,1,'block/search_forums:addinstance',1,1390420623,0),(986,1,3,'block/section_links:addinstance',1,1390420624,0),(987,1,1,'block/section_links:addinstance',1,1390420624,0),(988,1,3,'block/selfcompletion:addinstance',1,1390420625,0),(989,1,1,'block/selfcompletion:addinstance',1,1390420625,0),(990,1,7,'block/settings:myaddinstance',1,1390420628,0),(991,1,3,'block/settings:addinstance',1,1390420628,0),(992,1,1,'block/settings:addinstance',1,1390420628,0),(993,1,3,'block/site_main_menu:addinstance',1,1390420629,0),(994,1,1,'block/site_main_menu:addinstance',1,1390420629,0),(995,1,3,'block/social_activities:addinstance',1,1390420630,0),(996,1,1,'block/social_activities:addinstance',1,1390420630,0),(997,1,3,'block/tag_flickr:addinstance',1,1390420631,0),(998,1,1,'block/tag_flickr:addinstance',1,1390420631,0),(999,1,3,'block/tag_youtube:addinstance',1,1390420632,0),(1000,1,1,'block/tag_youtube:addinstance',1,1390420633,0),(1001,1,7,'block/tags:myaddinstance',1,1390420634,0),(1002,1,3,'block/tags:addinstance',1,1390420634,0),(1003,1,1,'block/tags:addinstance',1,1390420634,0),(1004,1,4,'report/completion:view',1,1390420667,0),(1005,1,3,'report/completion:view',1,1390420667,0),(1006,1,1,'report/completion:view',1,1390420667,0),(1007,1,4,'report/courseoverview:view',1,1390420669,0),(1008,1,3,'report/courseoverview:view',1,1390420669,0),(1009,1,1,'report/courseoverview:view',1,1390420669,0),(1010,1,4,'report/log:view',1,1390420671,0),(1011,1,3,'report/log:view',1,1390420671,0),(1012,1,1,'report/log:view',1,1390420671,0),(1013,1,4,'report/log:viewtoday',1,1390420671,0),(1014,1,3,'report/log:viewtoday',1,1390420672,0),(1015,1,1,'report/log:viewtoday',1,1390420672,0),(1016,1,4,'report/loglive:view',1,1390420673,0),(1017,1,3,'report/loglive:view',1,1390420673,0),(1018,1,1,'report/loglive:view',1,1390420673,0),(1019,1,4,'report/outline:view',1,1390420677,0),(1020,1,3,'report/outline:view',1,1390420677,0),(1021,1,1,'report/outline:view',1,1390420678,0),(1022,1,4,'report/participation:view',1,1390420679,0),(1023,1,3,'report/participation:view',1,1390420680,0),(1024,1,1,'report/participation:view',1,1390420680,0),(1025,1,1,'report/performance:view',1,1390420681,0),(1026,1,4,'report/progress:view',1,1390420682,0),(1027,1,3,'report/progress:view',1,1390420682,0),(1028,1,1,'report/progress:view',1,1390420683,0),(1029,1,1,'report/security:view',1,1390420686,0),(1030,1,4,'report/stats:view',1,1390420688,0),(1031,1,3,'report/stats:view',1,1390420688,0),(1032,1,1,'report/stats:view',1,1390420688,0),(1033,1,4,'gradeexport/ods:view',1,1390420690,0),(1034,1,3,'gradeexport/ods:view',1,1390420691,0),(1035,1,1,'gradeexport/ods:view',1,1390420691,0),(1036,1,1,'gradeexport/ods:publish',1,1390420691,0),(1037,1,4,'gradeexport/txt:view',1,1390420693,0),(1038,1,3,'gradeexport/txt:view',1,1390420693,0),(1039,1,1,'gradeexport/txt:view',1,1390420693,0),(1040,1,1,'gradeexport/txt:publish',1,1390420693,0),(1041,1,4,'gradeexport/xls:view',1,1390420695,0),(1042,1,3,'gradeexport/xls:view',1,1390420695,0),(1043,1,1,'gradeexport/xls:view',1,1390420695,0),(1044,1,1,'gradeexport/xls:publish',1,1390420695,0),(1045,1,4,'gradeexport/xml:view',1,1390420696,0),(1046,1,3,'gradeexport/xml:view',1,1390420696,0),(1047,1,1,'gradeexport/xml:view',1,1390420697,0),(1048,1,1,'gradeexport/xml:publish',1,1390420697,0),(1049,1,3,'gradeimport/csv:view',1,1390420698,0),(1050,1,1,'gradeimport/csv:view',1,1390420698,0),(1051,1,3,'gradeimport/xml:view',1,1390420701,0),(1052,1,1,'gradeimport/xml:view',1,1390420701,0),(1053,1,1,'gradeimport/xml:publish',1,1390420701,0),(1054,1,4,'gradereport/grader:view',1,1390420703,0),(1055,1,3,'gradereport/grader:view',1,1390420703,0),(1056,1,1,'gradereport/grader:view',1,1390420703,0),(1057,1,4,'gradereport/outcomes:view',1,1390420705,0),(1058,1,3,'gradereport/outcomes:view',1,1390420705,0),(1059,1,1,'gradereport/outcomes:view',1,1390420705,0),(1060,1,5,'gradereport/overview:view',1,1390420706,0),(1061,1,1,'gradereport/overview:view',1,1390420706,0),(1062,1,5,'gradereport/user:view',1,1390420707,0),(1063,1,4,'gradereport/user:view',1,1390420707,0),(1064,1,3,'gradereport/user:view',1,1390420707,0),(1065,1,1,'gradereport/user:view',1,1390420707,0),(1066,1,7,'repository/alfresco:view',1,1390420728,0),(1067,1,7,'repository/areafiles:view',1,1390420729,0),(1068,1,7,'repository/boxnet:view',1,1390420730,0),(1069,1,2,'repository/coursefiles:view',1,1390420731,0),(1070,1,4,'repository/coursefiles:view',1,1390420732,0),(1071,1,3,'repository/coursefiles:view',1,1390420732,0),(1072,1,1,'repository/coursefiles:view',1,1390420732,0),(1073,1,7,'repository/dropbox:view',1,1390420733,0),(1074,1,7,'repository/equella:view',1,1390420734,0),(1075,1,2,'repository/filesystem:view',1,1390420735,0),(1076,1,4,'repository/filesystem:view',1,1390420735,0),(1077,1,3,'repository/filesystem:view',1,1390420735,0),(1078,1,1,'repository/filesystem:view',1,1390420736,0),(1079,1,7,'repository/flickr:view',1,1390420739,0),(1080,1,7,'repository/flickr_public:view',1,1390420740,0),(1081,1,7,'repository/googledocs:view',1,1390420741,0),(1082,1,2,'repository/local:view',1,1390420742,0),(1083,1,4,'repository/local:view',1,1390420742,0),(1084,1,3,'repository/local:view',1,1390420743,0),(1085,1,1,'repository/local:view',1,1390420743,0),(1086,1,7,'repository/merlot:view',1,1390420744,0),(1087,1,7,'repository/picasa:view',1,1390420746,0),(1088,1,7,'repository/recent:view',1,1390420748,0),(1089,1,7,'repository/s3:view',1,1390420748,0),(1090,1,7,'repository/skydrive:view',1,1390420752,0),(1091,1,7,'repository/upload:view',1,1390420755,0),(1092,1,7,'repository/url:view',1,1390420756,0),(1093,1,7,'repository/user:view',1,1390420758,0),(1094,1,2,'repository/webdav:view',1,1390420760,0),(1095,1,4,'repository/webdav:view',1,1390420760,0),(1096,1,3,'repository/webdav:view',1,1390420760,0),(1097,1,1,'repository/webdav:view',1,1390420760,0),(1098,1,7,'repository/wikimedia:view',1,1390420762,0),(1099,1,7,'repository/youtube:view',1,1390420766,0),(1100,1,1,'tool/customlang:view',1,1390420798,0),(1101,1,1,'tool/customlang:edit',1,1390420799,0),(1102,1,1,'tool/uploaduser:uploaduserpictures',1,1390420812,0),(1103,1,3,'booktool/importhtml:import',1,1390420862,0),(1104,1,1,'booktool/importhtml:import',1,1390420862,0),(1105,1,6,'booktool/print:print',1,1390420865,0),(1106,1,8,'booktool/print:print',1,1390420865,0),(1107,1,5,'booktool/print:print',1,1390420865,0),(1108,1,4,'booktool/print:print',1,1390420865,0),(1109,1,3,'booktool/print:print',1,1390420866,0),(1110,1,1,'booktool/print:print',1,1390420866,0),(1111,1,4,'quiz/grading:viewstudentnames',1,1390420882,0),(1112,1,3,'quiz/grading:viewstudentnames',1,1390420882,0),(1113,1,1,'quiz/grading:viewstudentnames',1,1390420882,0),(1114,1,4,'quiz/grading:viewidnumber',1,1390420883,0),(1115,1,3,'quiz/grading:viewidnumber',1,1390420883,0),(1116,1,1,'quiz/grading:viewidnumber',1,1390420884,0),(1117,1,4,'quiz/statistics:view',1,1390420893,0),(1118,1,3,'quiz/statistics:view',1,1390420893,0),(1119,1,1,'quiz/statistics:view',1,1390420894,0);
/*!40000 ALTER TABLE `mdl_role_capabilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_role_context_levels`
--

DROP TABLE IF EXISTS `mdl_role_context_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_role_context_levels` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `roleid` bigint(10) NOT NULL,
  `contextlevel` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolecontleve_conrol_uix` (`contextlevel`,`roleid`),
  KEY `mdl_rolecontleve_rol_ix` (`roleid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='Lists which roles can be assigned at which context levels. T';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_role_context_levels`
--

LOCK TABLES `mdl_role_context_levels` WRITE;
/*!40000 ALTER TABLE `mdl_role_context_levels` DISABLE KEYS */;
INSERT INTO `mdl_role_context_levels` VALUES (1,1,10),(4,2,10),(2,1,40),(5,2,40),(3,1,50),(6,3,50),(8,4,50),(10,5,50),(7,3,70),(9,4,70),(11,5,70);
/*!40000 ALTER TABLE `mdl_role_context_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_role_names`
--

DROP TABLE IF EXISTS `mdl_role_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_role_names` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `contextid` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolename_rolcon_uix` (`roleid`,`contextid`),
  KEY `mdl_rolename_rol_ix` (`roleid`),
  KEY `mdl_rolename_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='role names in native strings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_role_names`
--

LOCK TABLES `mdl_role_names` WRITE;
/*!40000 ALTER TABLE `mdl_role_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_role_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_role_sortorder`
--

DROP TABLE IF EXISTS `mdl_role_sortorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_role_sortorder` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL,
  `roleid` bigint(10) NOT NULL,
  `contextid` bigint(10) NOT NULL,
  `sortoder` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_rolesort_userolcon_uix` (`userid`,`roleid`,`contextid`),
  KEY `mdl_rolesort_use_ix` (`userid`),
  KEY `mdl_rolesort_rol_ix` (`roleid`),
  KEY `mdl_rolesort_con_ix` (`contextid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='sort order of course managers in a course';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_role_sortorder`
--

LOCK TABLES `mdl_role_sortorder` WRITE;
/*!40000 ALTER TABLE `mdl_role_sortorder` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_role_sortorder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scale`
--

DROP TABLE IF EXISTS `mdl_scale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scale` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `scale` longtext NOT NULL,
  `description` longtext NOT NULL,
  `descriptionformat` tinyint(2) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_scal_cou_ix` (`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines grading scales';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scale`
--

LOCK TABLES `mdl_scale` WRITE;
/*!40000 ALTER TABLE `mdl_scale` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scale_history`
--

DROP TABLE IF EXISTS `mdl_scale_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scale_history` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `action` bigint(10) NOT NULL DEFAULT '0',
  `oldid` bigint(10) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `timemodified` bigint(10) DEFAULT NULL,
  `loggeduser` bigint(10) DEFAULT NULL,
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `scale` longtext NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_scalhist_act_ix` (`action`),
  KEY `mdl_scalhist_old_ix` (`oldid`),
  KEY `mdl_scalhist_cou_ix` (`courseid`),
  KEY `mdl_scalhist_log_ix` (`loggeduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='History table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scale_history`
--

LOCK TABLES `mdl_scale_history` WRITE;
/*!40000 ALTER TABLE `mdl_scale_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scale_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm`
--

DROP TABLE IF EXISTS `mdl_scorm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `scormtype` varchar(50) NOT NULL DEFAULT 'local',
  `reference` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `version` varchar(9) NOT NULL DEFAULT '',
  `maxgrade` double NOT NULL DEFAULT '0',
  `grademethod` tinyint(2) NOT NULL DEFAULT '0',
  `whatgrade` bigint(10) NOT NULL DEFAULT '0',
  `maxattempt` bigint(10) NOT NULL DEFAULT '1',
  `forcecompleted` tinyint(1) NOT NULL DEFAULT '1',
  `forcenewattempt` tinyint(1) NOT NULL DEFAULT '0',
  `lastattemptlock` tinyint(1) NOT NULL DEFAULT '0',
  `displayattemptstatus` tinyint(1) NOT NULL DEFAULT '1',
  `displaycoursestructure` tinyint(1) NOT NULL DEFAULT '1',
  `updatefreq` tinyint(1) NOT NULL DEFAULT '0',
  `sha1hash` varchar(40) DEFAULT NULL,
  `md5hash` varchar(32) NOT NULL DEFAULT '',
  `revision` bigint(10) NOT NULL DEFAULT '0',
  `launch` bigint(10) NOT NULL DEFAULT '0',
  `skipview` tinyint(1) NOT NULL DEFAULT '1',
  `hidebrowse` tinyint(1) NOT NULL DEFAULT '0',
  `hidetoc` tinyint(1) NOT NULL DEFAULT '0',
  `nav` tinyint(1) NOT NULL DEFAULT '1',
  `navpositionleft` bigint(10) DEFAULT '-100',
  `navpositiontop` bigint(10) DEFAULT '-100',
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  `popup` tinyint(1) NOT NULL DEFAULT '0',
  `options` varchar(255) NOT NULL DEFAULT '',
  `width` bigint(10) NOT NULL DEFAULT '100',
  `height` bigint(10) NOT NULL DEFAULT '600',
  `timeopen` bigint(10) NOT NULL DEFAULT '0',
  `timeclose` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `completionstatusrequired` tinyint(1) DEFAULT NULL,
  `completionscorerequired` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_scor_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='each table is one SCORM module and its configuration';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm`
--

LOCK TABLES `mdl_scorm` WRITE;
/*!40000 ALTER TABLE `mdl_scorm` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm_aicc_session`
--

DROP TABLE IF EXISTS `mdl_scorm_aicc_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm_aicc_session` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `scormid` bigint(10) NOT NULL DEFAULT '0',
  `hacpsession` varchar(255) NOT NULL DEFAULT '',
  `scoid` bigint(10) DEFAULT '0',
  `scormmode` varchar(50) DEFAULT NULL,
  `scormstatus` varchar(255) DEFAULT NULL,
  `attempt` bigint(10) DEFAULT NULL,
  `lessonstatus` varchar(255) DEFAULT NULL,
  `sessiontime` varchar(255) DEFAULT NULL,
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_scoraiccsess_sco_ix` (`scormid`),
  KEY `mdl_scoraiccsess_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Used by AICC HACP to store session information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm_aicc_session`
--

LOCK TABLES `mdl_scorm_aicc_session` WRITE;
/*!40000 ALTER TABLE `mdl_scorm_aicc_session` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm_aicc_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm_scoes`
--

DROP TABLE IF EXISTS `mdl_scorm_scoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm_scoes` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `scorm` bigint(10) NOT NULL DEFAULT '0',
  `manifest` varchar(255) NOT NULL DEFAULT '',
  `organization` varchar(255) NOT NULL DEFAULT '',
  `parent` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `launch` longtext NOT NULL,
  `scormtype` varchar(5) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_scorscoe_sco_ix` (`scorm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='each SCO part of the SCORM module';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm_scoes`
--

LOCK TABLES `mdl_scorm_scoes` WRITE;
/*!40000 ALTER TABLE `mdl_scorm_scoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm_scoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm_scoes_data`
--

DROP TABLE IF EXISTS `mdl_scorm_scoes_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm_scoes_data` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `scoid` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_scorscoedata_sco_ix` (`scoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains variable data get from packages';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm_scoes_data`
--

LOCK TABLES `mdl_scorm_scoes_data` WRITE;
/*!40000 ALTER TABLE `mdl_scorm_scoes_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm_scoes_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm_scoes_track`
--

DROP TABLE IF EXISTS `mdl_scorm_scoes_track`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm_scoes_track` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `scormid` bigint(10) NOT NULL DEFAULT '0',
  `scoid` bigint(10) NOT NULL DEFAULT '0',
  `attempt` bigint(10) NOT NULL DEFAULT '1',
  `element` varchar(255) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorscoetrac_usescosco_uix` (`userid`,`scormid`,`scoid`,`attempt`,`element`),
  KEY `mdl_scorscoetrac_use_ix` (`userid`),
  KEY `mdl_scorscoetrac_ele_ix` (`element`),
  KEY `mdl_scorscoetrac_sco_ix` (`scormid`),
  KEY `mdl_scorscoetrac_sco2_ix` (`scoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='to track SCOes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm_scoes_track`
--

LOCK TABLES `mdl_scorm_scoes_track` WRITE;
/*!40000 ALTER TABLE `mdl_scorm_scoes_track` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm_scoes_track` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm_seq_mapinfo`
--

DROP TABLE IF EXISTS `mdl_scorm_seq_mapinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm_seq_mapinfo` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `scoid` bigint(10) NOT NULL DEFAULT '0',
  `objectiveid` bigint(10) NOT NULL DEFAULT '0',
  `targetobjectiveid` bigint(10) NOT NULL DEFAULT '0',
  `readsatisfiedstatus` tinyint(1) NOT NULL DEFAULT '1',
  `readnormalizedmeasure` tinyint(1) NOT NULL DEFAULT '1',
  `writesatisfiedstatus` tinyint(1) NOT NULL DEFAULT '0',
  `writenormalizedmeasure` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqmapi_scoidobj_uix` (`scoid`,`id`,`objectiveid`),
  KEY `mdl_scorseqmapi_sco_ix` (`scoid`),
  KEY `mdl_scorseqmapi_obj_ix` (`objectiveid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SCORM2004 objective mapinfo description';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm_seq_mapinfo`
--

LOCK TABLES `mdl_scorm_seq_mapinfo` WRITE;
/*!40000 ALTER TABLE `mdl_scorm_seq_mapinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm_seq_mapinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm_seq_objective`
--

DROP TABLE IF EXISTS `mdl_scorm_seq_objective`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm_seq_objective` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `scoid` bigint(10) NOT NULL DEFAULT '0',
  `primaryobj` tinyint(1) NOT NULL DEFAULT '0',
  `objectiveid` varchar(255) NOT NULL DEFAULT '',
  `satisfiedbymeasure` tinyint(1) NOT NULL DEFAULT '1',
  `minnormalizedmeasure` float(11,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqobje_scoid_uix` (`scoid`,`id`),
  KEY `mdl_scorseqobje_sco_ix` (`scoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SCORM2004 objective description';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm_seq_objective`
--

LOCK TABLES `mdl_scorm_seq_objective` WRITE;
/*!40000 ALTER TABLE `mdl_scorm_seq_objective` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm_seq_objective` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm_seq_rolluprule`
--

DROP TABLE IF EXISTS `mdl_scorm_seq_rolluprule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm_seq_rolluprule` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `scoid` bigint(10) NOT NULL DEFAULT '0',
  `childactivityset` varchar(15) NOT NULL DEFAULT '',
  `minimumcount` bigint(10) NOT NULL DEFAULT '0',
  `minimumpercent` float(11,4) NOT NULL DEFAULT '0.0000',
  `conditioncombination` varchar(3) NOT NULL DEFAULT 'all',
  `action` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqroll_scoid_uix` (`scoid`,`id`),
  KEY `mdl_scorseqroll_sco_ix` (`scoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SCORM2004 sequencing rule';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm_seq_rolluprule`
--

LOCK TABLES `mdl_scorm_seq_rolluprule` WRITE;
/*!40000 ALTER TABLE `mdl_scorm_seq_rolluprule` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm_seq_rolluprule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm_seq_rolluprulecond`
--

DROP TABLE IF EXISTS `mdl_scorm_seq_rolluprulecond`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm_seq_rolluprulecond` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `scoid` bigint(10) NOT NULL DEFAULT '0',
  `rollupruleid` bigint(10) NOT NULL DEFAULT '0',
  `operator` varchar(5) NOT NULL DEFAULT 'noOp',
  `cond` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqroll_scorolid_uix` (`scoid`,`rollupruleid`,`id`),
  KEY `mdl_scorseqroll_sco2_ix` (`scoid`),
  KEY `mdl_scorseqroll_rol_ix` (`rollupruleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SCORM2004 sequencing rule';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm_seq_rolluprulecond`
--

LOCK TABLES `mdl_scorm_seq_rolluprulecond` WRITE;
/*!40000 ALTER TABLE `mdl_scorm_seq_rolluprulecond` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm_seq_rolluprulecond` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm_seq_rulecond`
--

DROP TABLE IF EXISTS `mdl_scorm_seq_rulecond`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm_seq_rulecond` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `scoid` bigint(10) NOT NULL DEFAULT '0',
  `ruleconditionsid` bigint(10) NOT NULL DEFAULT '0',
  `refrencedobjective` varchar(255) NOT NULL DEFAULT '',
  `measurethreshold` float(11,4) NOT NULL DEFAULT '0.0000',
  `operator` varchar(5) NOT NULL DEFAULT 'noOp',
  `cond` varchar(30) NOT NULL DEFAULT 'always',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqrule_idscorul_uix` (`id`,`scoid`,`ruleconditionsid`),
  KEY `mdl_scorseqrule_sco2_ix` (`scoid`),
  KEY `mdl_scorseqrule_rul_ix` (`ruleconditionsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SCORM2004 rule condition';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm_seq_rulecond`
--

LOCK TABLES `mdl_scorm_seq_rulecond` WRITE;
/*!40000 ALTER TABLE `mdl_scorm_seq_rulecond` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm_seq_rulecond` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_scorm_seq_ruleconds`
--

DROP TABLE IF EXISTS `mdl_scorm_seq_ruleconds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_scorm_seq_ruleconds` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `scoid` bigint(10) NOT NULL DEFAULT '0',
  `conditioncombination` varchar(3) NOT NULL DEFAULT 'all',
  `ruletype` tinyint(2) NOT NULL DEFAULT '0',
  `action` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_scorseqrule_scoid_uix` (`scoid`,`id`),
  KEY `mdl_scorseqrule_sco_ix` (`scoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SCORM2004 rule conditions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_scorm_seq_ruleconds`
--

LOCK TABLES `mdl_scorm_seq_ruleconds` WRITE;
/*!40000 ALTER TABLE `mdl_scorm_seq_ruleconds` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_scorm_seq_ruleconds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_sessions`
--

DROP TABLE IF EXISTS `mdl_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_sessions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `state` bigint(10) NOT NULL DEFAULT '0',
  `sid` varchar(128) NOT NULL DEFAULT '',
  `userid` bigint(10) NOT NULL,
  `sessdata` longtext,
  `timecreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  `firstip` varchar(45) DEFAULT NULL,
  `lastip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_sess_sid_uix` (`sid`),
  KEY `mdl_sess_sta_ix` (`state`),
  KEY `mdl_sess_tim_ix` (`timecreated`),
  KEY `mdl_sess_tim2_ix` (`timemodified`),
  KEY `mdl_sess_use_ix` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='Database based session storage - now recommended';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_sessions`
--

LOCK TABLES `mdl_sessions` WRITE;
/*!40000 ALTER TABLE `mdl_sessions` DISABLE KEYS */;
INSERT INTO `mdl_sessions` VALUES (8,0,'f8m8oedo7grt10ue0rf7h9pg43',2,NULL,1390504554,1390505836,'127.0.0.1','127.0.0.1'),(10,0,'iirm6bhnfingbdp59vvnc6ulh3',2,NULL,1390841746,1390841822,'127.0.0.1','127.0.0.1'),(12,0,'tukpsvvr6mnqdbknfuik85b5o7',2,NULL,1390925719,1390925784,'127.0.0.1','127.0.0.1');
/*!40000 ALTER TABLE `mdl_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_stats_daily`
--

DROP TABLE IF EXISTS `mdl_stats_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_stats_daily` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `timeend` bigint(10) NOT NULL DEFAULT '0',
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `stattype` varchar(20) NOT NULL DEFAULT 'activity',
  `stat1` bigint(10) NOT NULL DEFAULT '0',
  `stat2` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_statdail_cou_ix` (`courseid`),
  KEY `mdl_statdail_tim_ix` (`timeend`),
  KEY `mdl_statdail_rol_ix` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='to accumulate daily stats';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_stats_daily`
--

LOCK TABLES `mdl_stats_daily` WRITE;
/*!40000 ALTER TABLE `mdl_stats_daily` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_stats_daily` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_stats_monthly`
--

DROP TABLE IF EXISTS `mdl_stats_monthly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_stats_monthly` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `timeend` bigint(10) NOT NULL DEFAULT '0',
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `stattype` varchar(20) NOT NULL DEFAULT 'activity',
  `stat1` bigint(10) NOT NULL DEFAULT '0',
  `stat2` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_statmont_cou_ix` (`courseid`),
  KEY `mdl_statmont_tim_ix` (`timeend`),
  KEY `mdl_statmont_rol_ix` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='To accumulate monthly stats';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_stats_monthly`
--

LOCK TABLES `mdl_stats_monthly` WRITE;
/*!40000 ALTER TABLE `mdl_stats_monthly` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_stats_monthly` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_stats_user_daily`
--

DROP TABLE IF EXISTS `mdl_stats_user_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_stats_user_daily` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `timeend` bigint(10) NOT NULL DEFAULT '0',
  `statsreads` bigint(10) NOT NULL DEFAULT '0',
  `statswrites` bigint(10) NOT NULL DEFAULT '0',
  `stattype` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_statuserdail_cou_ix` (`courseid`),
  KEY `mdl_statuserdail_use_ix` (`userid`),
  KEY `mdl_statuserdail_rol_ix` (`roleid`),
  KEY `mdl_statuserdail_tim_ix` (`timeend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='To accumulate daily stats per course/user';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_stats_user_daily`
--

LOCK TABLES `mdl_stats_user_daily` WRITE;
/*!40000 ALTER TABLE `mdl_stats_user_daily` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_stats_user_daily` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_stats_user_monthly`
--

DROP TABLE IF EXISTS `mdl_stats_user_monthly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_stats_user_monthly` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `timeend` bigint(10) NOT NULL DEFAULT '0',
  `statsreads` bigint(10) NOT NULL DEFAULT '0',
  `statswrites` bigint(10) NOT NULL DEFAULT '0',
  `stattype` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_statusermont_cou_ix` (`courseid`),
  KEY `mdl_statusermont_use_ix` (`userid`),
  KEY `mdl_statusermont_rol_ix` (`roleid`),
  KEY `mdl_statusermont_tim_ix` (`timeend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='To accumulate monthly stats per course/user';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_stats_user_monthly`
--

LOCK TABLES `mdl_stats_user_monthly` WRITE;
/*!40000 ALTER TABLE `mdl_stats_user_monthly` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_stats_user_monthly` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_stats_user_weekly`
--

DROP TABLE IF EXISTS `mdl_stats_user_weekly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_stats_user_weekly` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `timeend` bigint(10) NOT NULL DEFAULT '0',
  `statsreads` bigint(10) NOT NULL DEFAULT '0',
  `statswrites` bigint(10) NOT NULL DEFAULT '0',
  `stattype` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_statuserweek_cou_ix` (`courseid`),
  KEY `mdl_statuserweek_use_ix` (`userid`),
  KEY `mdl_statuserweek_rol_ix` (`roleid`),
  KEY `mdl_statuserweek_tim_ix` (`timeend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='To accumulate weekly stats per course/user';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_stats_user_weekly`
--

LOCK TABLES `mdl_stats_user_weekly` WRITE;
/*!40000 ALTER TABLE `mdl_stats_user_weekly` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_stats_user_weekly` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_stats_weekly`
--

DROP TABLE IF EXISTS `mdl_stats_weekly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_stats_weekly` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `timeend` bigint(10) NOT NULL DEFAULT '0',
  `roleid` bigint(10) NOT NULL DEFAULT '0',
  `stattype` varchar(20) NOT NULL DEFAULT 'activity',
  `stat1` bigint(10) NOT NULL DEFAULT '0',
  `stat2` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_statweek_cou_ix` (`courseid`),
  KEY `mdl_statweek_tim_ix` (`timeend`),
  KEY `mdl_statweek_rol_ix` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='To accumulate weekly stats';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_stats_weekly`
--

LOCK TABLES `mdl_stats_weekly` WRITE;
/*!40000 ALTER TABLE `mdl_stats_weekly` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_stats_weekly` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_survey`
--

DROP TABLE IF EXISTS `mdl_survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_survey` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `template` bigint(10) NOT NULL DEFAULT '0',
  `days` mediumint(6) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext NOT NULL,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `questions` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_surv_cou_ix` (`course`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Each record is one SURVEY module with its configuration';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_survey`
--

LOCK TABLES `mdl_survey` WRITE;
/*!40000 ALTER TABLE `mdl_survey` DISABLE KEYS */;
INSERT INTO `mdl_survey` VALUES (1,0,0,0,985017600,985017600,'collesaname','collesaintro',0,'25,26,27,28,29,30,43,44'),(2,0,0,0,985017600,985017600,'collespname','collespintro',0,'31,32,33,34,35,36,43,44'),(3,0,0,0,985017600,985017600,'collesapname','collesapintro',0,'37,38,39,40,41,42,43,44'),(4,0,0,0,985017600,985017600,'attlsname','attlsintro',0,'65,67,68'),(5,0,0,0,985017600,985017600,'ciqname','ciqintro',0,'69,70,71,72,73');
/*!40000 ALTER TABLE `mdl_survey` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_survey_analysis`
--

DROP TABLE IF EXISTS `mdl_survey_analysis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_survey_analysis` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `survey` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_survanal_use_ix` (`userid`),
  KEY `mdl_survanal_sur_ix` (`survey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='text about each survey submission';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_survey_analysis`
--

LOCK TABLES `mdl_survey_analysis` WRITE;
/*!40000 ALTER TABLE `mdl_survey_analysis` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_survey_analysis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_survey_answers`
--

DROP TABLE IF EXISTS `mdl_survey_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_survey_answers` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `survey` bigint(10) NOT NULL DEFAULT '0',
  `question` bigint(10) NOT NULL DEFAULT '0',
  `time` bigint(10) NOT NULL DEFAULT '0',
  `answer1` longtext NOT NULL,
  `answer2` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_survansw_use_ix` (`userid`),
  KEY `mdl_survansw_sur_ix` (`survey`),
  KEY `mdl_survansw_que_ix` (`question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='the answers to each questions filled by the users';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_survey_answers`
--

LOCK TABLES `mdl_survey_answers` WRITE;
/*!40000 ALTER TABLE `mdl_survey_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_survey_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_survey_questions`
--

DROP TABLE IF EXISTS `mdl_survey_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_survey_questions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) NOT NULL DEFAULT '',
  `shorttext` varchar(30) NOT NULL DEFAULT '',
  `multi` varchar(100) NOT NULL DEFAULT '',
  `intro` varchar(50) NOT NULL DEFAULT '',
  `type` smallint(3) NOT NULL DEFAULT '0',
  `options` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COMMENT='the questions conforming one survey';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_survey_questions`
--

LOCK TABLES `mdl_survey_questions` WRITE;
/*!40000 ALTER TABLE `mdl_survey_questions` DISABLE KEYS */;
INSERT INTO `mdl_survey_questions` VALUES (1,'colles1','colles1short','','',1,'scaletimes5'),(2,'colles2','colles2short','','',1,'scaletimes5'),(3,'colles3','colles3short','','',1,'scaletimes5'),(4,'colles4','colles4short','','',1,'scaletimes5'),(5,'colles5','colles5short','','',1,'scaletimes5'),(6,'colles6','colles6short','','',1,'scaletimes5'),(7,'colles7','colles7short','','',1,'scaletimes5'),(8,'colles8','colles8short','','',1,'scaletimes5'),(9,'colles9','colles9short','','',1,'scaletimes5'),(10,'colles10','colles10short','','',1,'scaletimes5'),(11,'colles11','colles11short','','',1,'scaletimes5'),(12,'colles12','colles12short','','',1,'scaletimes5'),(13,'colles13','colles13short','','',1,'scaletimes5'),(14,'colles14','colles14short','','',1,'scaletimes5'),(15,'colles15','colles15short','','',1,'scaletimes5'),(16,'colles16','colles16short','','',1,'scaletimes5'),(17,'colles17','colles17short','','',1,'scaletimes5'),(18,'colles18','colles18short','','',1,'scaletimes5'),(19,'colles19','colles19short','','',1,'scaletimes5'),(20,'colles20','colles20short','','',1,'scaletimes5'),(21,'colles21','colles21short','','',1,'scaletimes5'),(22,'colles22','colles22short','','',1,'scaletimes5'),(23,'colles23','colles23short','','',1,'scaletimes5'),(24,'colles24','colles24short','','',1,'scaletimes5'),(25,'collesm1','collesm1short','1,2,3,4','collesmintro',1,'scaletimes5'),(26,'collesm2','collesm2short','5,6,7,8','collesmintro',1,'scaletimes5'),(27,'collesm3','collesm3short','9,10,11,12','collesmintro',1,'scaletimes5'),(28,'collesm4','collesm4short','13,14,15,16','collesmintro',1,'scaletimes5'),(29,'collesm5','collesm5short','17,18,19,20','collesmintro',1,'scaletimes5'),(30,'collesm6','collesm6short','21,22,23,24','collesmintro',1,'scaletimes5'),(31,'collesm1','collesm1short','1,2,3,4','collesmintro',2,'scaletimes5'),(32,'collesm2','collesm2short','5,6,7,8','collesmintro',2,'scaletimes5'),(33,'collesm3','collesm3short','9,10,11,12','collesmintro',2,'scaletimes5'),(34,'collesm4','collesm4short','13,14,15,16','collesmintro',2,'scaletimes5'),(35,'collesm5','collesm5short','17,18,19,20','collesmintro',2,'scaletimes5'),(36,'collesm6','collesm6short','21,22,23,24','collesmintro',2,'scaletimes5'),(37,'collesm1','collesm1short','1,2,3,4','collesmintro',3,'scaletimes5'),(38,'collesm2','collesm2short','5,6,7,8','collesmintro',3,'scaletimes5'),(39,'collesm3','collesm3short','9,10,11,12','collesmintro',3,'scaletimes5'),(40,'collesm4','collesm4short','13,14,15,16','collesmintro',3,'scaletimes5'),(41,'collesm5','collesm5short','17,18,19,20','collesmintro',3,'scaletimes5'),(42,'collesm6','collesm6short','21,22,23,24','collesmintro',3,'scaletimes5'),(43,'howlong','','','',1,'howlongoptions'),(44,'othercomments','','','',0,NULL),(45,'attls1','attls1short','','',1,'scaleagree5'),(46,'attls2','attls2short','','',1,'scaleagree5'),(47,'attls3','attls3short','','',1,'scaleagree5'),(48,'attls4','attls4short','','',1,'scaleagree5'),(49,'attls5','attls5short','','',1,'scaleagree5'),(50,'attls6','attls6short','','',1,'scaleagree5'),(51,'attls7','attls7short','','',1,'scaleagree5'),(52,'attls8','attls8short','','',1,'scaleagree5'),(53,'attls9','attls9short','','',1,'scaleagree5'),(54,'attls10','attls10short','','',1,'scaleagree5'),(55,'attls11','attls11short','','',1,'scaleagree5'),(56,'attls12','attls12short','','',1,'scaleagree5'),(57,'attls13','attls13short','','',1,'scaleagree5'),(58,'attls14','attls14short','','',1,'scaleagree5'),(59,'attls15','attls15short','','',1,'scaleagree5'),(60,'attls16','attls16short','','',1,'scaleagree5'),(61,'attls17','attls17short','','',1,'scaleagree5'),(62,'attls18','attls18short','','',1,'scaleagree5'),(63,'attls19','attls19short','','',1,'scaleagree5'),(64,'attls20','attls20short','','',1,'scaleagree5'),(65,'attlsm1','attlsm1','45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64','attlsmintro',1,'scaleagree5'),(66,'-','-','-','-',0,'-'),(67,'attlsm2','attlsm2','63,62,59,57,55,49,52,50,48,47','attlsmintro',-1,'scaleagree5'),(68,'attlsm3','attlsm3','46,54,45,51,60,53,56,58,61,64','attlsmintro',-1,'scaleagree5'),(69,'ciq1','ciq1short','','',0,NULL),(70,'ciq2','ciq2short','','',0,NULL),(71,'ciq3','ciq3short','','',0,NULL),(72,'ciq4','ciq4short','','',0,NULL),(73,'ciq5','ciq5short','','',0,NULL);
/*!40000 ALTER TABLE `mdl_survey_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_tag`
--

DROP TABLE IF EXISTS `mdl_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_tag` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `rawname` varchar(255) NOT NULL DEFAULT '',
  `tagtype` varchar(255) DEFAULT NULL,
  `description` longtext,
  `descriptionformat` tinyint(2) NOT NULL DEFAULT '0',
  `flag` smallint(4) DEFAULT '0',
  `timemodified` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_tag_nam_uix` (`name`),
  UNIQUE KEY `mdl_tag_idnam_uix` (`id`,`name`),
  KEY `mdl_tag_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tag table - this generic table will replace the old "tags" t';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_tag`
--

LOCK TABLES `mdl_tag` WRITE;
/*!40000 ALTER TABLE `mdl_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_tag_correlation`
--

DROP TABLE IF EXISTS `mdl_tag_correlation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_tag_correlation` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `tagid` bigint(10) NOT NULL,
  `correlatedtags` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_tagcorr_tag_ix` (`tagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The rationale for the ''tag_correlation'' table is performance';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_tag_correlation`
--

LOCK TABLES `mdl_tag_correlation` WRITE;
/*!40000 ALTER TABLE `mdl_tag_correlation` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_tag_correlation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_tag_instance`
--

DROP TABLE IF EXISTS `mdl_tag_instance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_tag_instance` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `tagid` bigint(10) NOT NULL,
  `itemtype` varchar(255) NOT NULL DEFAULT '',
  `itemid` bigint(10) NOT NULL,
  `tiuserid` bigint(10) NOT NULL DEFAULT '0',
  `ordering` bigint(10) DEFAULT NULL,
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_taginst_iteitetagtiu_uix` (`itemtype`,`itemid`,`tagid`,`tiuserid`),
  KEY `mdl_taginst_tag_ix` (`tagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='tag_instance table holds the information of associations bet';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_tag_instance`
--

LOCK TABLES `mdl_tag_instance` WRITE;
/*!40000 ALTER TABLE `mdl_tag_instance` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_tag_instance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_timezone`
--

DROP TABLE IF EXISTS `mdl_timezone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_timezone` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `year` bigint(11) NOT NULL DEFAULT '0',
  `tzrule` varchar(20) NOT NULL DEFAULT '',
  `gmtoff` bigint(11) NOT NULL DEFAULT '0',
  `dstoff` bigint(11) NOT NULL DEFAULT '0',
  `dst_month` tinyint(2) NOT NULL DEFAULT '0',
  `dst_startday` smallint(3) NOT NULL DEFAULT '0',
  `dst_weekday` smallint(3) NOT NULL DEFAULT '0',
  `dst_skipweeks` smallint(3) NOT NULL DEFAULT '0',
  `dst_time` varchar(6) NOT NULL DEFAULT '00:00',
  `std_month` tinyint(2) NOT NULL DEFAULT '0',
  `std_startday` smallint(3) NOT NULL DEFAULT '0',
  `std_weekday` smallint(3) NOT NULL DEFAULT '0',
  `std_skipweeks` smallint(3) NOT NULL DEFAULT '0',
  `std_time` varchar(6) NOT NULL DEFAULT '00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Rules for calculating local wall clock time for users';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_timezone`
--

LOCK TABLES `mdl_timezone` WRITE;
/*!40000 ALTER TABLE `mdl_timezone` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_timezone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_tool_customlang`
--

DROP TABLE IF EXISTS `mdl_tool_customlang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_tool_customlang` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `lang` varchar(20) NOT NULL DEFAULT '',
  `componentid` bigint(10) NOT NULL,
  `stringid` varchar(255) NOT NULL DEFAULT '',
  `original` longtext NOT NULL,
  `master` longtext,
  `local` longtext,
  `timemodified` bigint(10) NOT NULL,
  `timecustomized` bigint(10) DEFAULT NULL,
  `outdated` smallint(3) DEFAULT '0',
  `modified` smallint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_toolcust_lancomstr_uix` (`lang`,`componentid`,`stringid`),
  KEY `mdl_toolcust_com_ix` (`componentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains the working checkout of all strings and their custo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_tool_customlang`
--

LOCK TABLES `mdl_tool_customlang` WRITE;
/*!40000 ALTER TABLE `mdl_tool_customlang` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_tool_customlang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_tool_customlang_components`
--

DROP TABLE IF EXISTS `mdl_tool_customlang_components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_tool_customlang_components` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains the list of all installed plugins that provide thei';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_tool_customlang_components`
--

LOCK TABLES `mdl_tool_customlang_components` WRITE;
/*!40000 ALTER TABLE `mdl_tool_customlang_components` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_tool_customlang_components` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_upgrade_log`
--

DROP TABLE IF EXISTS `mdl_upgrade_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_upgrade_log` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `type` bigint(10) NOT NULL,
  `plugin` varchar(100) DEFAULT NULL,
  `version` varchar(100) DEFAULT NULL,
  `targetversion` varchar(100) DEFAULT NULL,
  `info` varchar(255) NOT NULL DEFAULT '',
  `details` longtext,
  `backtrace` longtext,
  `userid` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_upgrlog_tim_ix` (`timemodified`),
  KEY `mdl_upgrlog_typtim_ix` (`type`,`timemodified`),
  KEY `mdl_upgrlog_use_ix` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=969 DEFAULT CHARSET=utf8 COMMENT='Upgrade logging';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_upgrade_log`
--

LOCK TABLES `mdl_upgrade_log` WRITE;
/*!40000 ALTER TABLE `mdl_upgrade_log` DISABLE KEYS */;
INSERT INTO `mdl_upgrade_log` VALUES (1,0,'core','2013111801.01','2013111801.01','Upgrade savepoint reached',NULL,'',0,1390419995),(2,0,'core','2013111801.01','2013111801.01','Core installed',NULL,'',0,1390420175),(3,0,'qtype_calculated',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420178),(4,0,'qtype_calculated','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420181),(5,0,'qtype_calculated','2013110500','2013110500','Plugin installed',NULL,'',0,1390420181),(6,0,'qtype_calculatedmulti',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420182),(7,0,'qtype_calculatedmulti','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420182),(8,0,'qtype_calculatedmulti','2013110500','2013110500','Plugin installed',NULL,'',0,1390420182),(9,0,'qtype_calculatedsimple',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420182),(10,0,'qtype_calculatedsimple','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420182),(11,0,'qtype_calculatedsimple','2013110500','2013110500','Plugin installed',NULL,'',0,1390420183),(12,0,'qtype_description',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420183),(13,0,'qtype_description','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420184),(14,0,'qtype_description','2013110500','2013110500','Plugin installed',NULL,'',0,1390420184),(15,0,'qtype_essay',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420184),(16,0,'qtype_essay','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420185),(17,0,'qtype_essay','2013110500','2013110500','Plugin installed',NULL,'',0,1390420185),(18,0,'qtype_match',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420185),(19,0,'qtype_match','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420186),(20,0,'qtype_match','2013110500','2013110500','Plugin installed',NULL,'',0,1390420187),(21,0,'qtype_missingtype',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420187),(22,0,'qtype_missingtype','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420188),(23,0,'qtype_missingtype','2013110500','2013110500','Plugin installed',NULL,'',0,1390420189),(24,0,'qtype_multianswer',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420190),(25,0,'qtype_multianswer','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420191),(26,0,'qtype_multianswer','2013110500','2013110500','Plugin installed',NULL,'',0,1390420192),(27,0,'qtype_multichoice',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420192),(28,0,'qtype_multichoice','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420192),(29,0,'qtype_multichoice','2013110500','2013110500','Plugin installed',NULL,'',0,1390420193),(30,0,'qtype_numerical',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420193),(31,0,'qtype_numerical','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420195),(32,0,'qtype_numerical','2013110500','2013110500','Plugin installed',NULL,'',0,1390420196),(33,0,'qtype_random',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420196),(34,0,'qtype_random','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420196),(35,0,'qtype_random','2013110500','2013110500','Plugin installed',NULL,'',0,1390420196),(36,0,'qtype_randomsamatch',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420197),(37,0,'qtype_randomsamatch','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420197),(38,0,'qtype_randomsamatch','2013110500','2013110500','Plugin installed',NULL,'',0,1390420198),(39,0,'qtype_shortanswer',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420198),(40,0,'qtype_shortanswer','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420198),(41,0,'qtype_shortanswer','2013110500','2013110500','Plugin installed',NULL,'',0,1390420199),(42,0,'qtype_truefalse',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420199),(43,0,'qtype_truefalse','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420200),(44,0,'qtype_truefalse','2013110500','2013110500','Plugin installed',NULL,'',0,1390420201),(45,0,'mod_assign',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420201),(46,0,'mod_assign','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420209),(47,0,'mod_assign','2013110500','2013110500','Plugin installed',NULL,'',0,1390420222),(48,0,'mod_assignment',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420223),(49,0,'mod_assignment','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420228),(50,0,'mod_assignment','2013110500','2013110500','Plugin installed',NULL,'',0,1390420234),(51,0,'mod_book',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420234),(52,0,'mod_book','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420235),(53,0,'mod_book','2013110500','2013110500','Plugin installed',NULL,'',0,1390420241),(54,0,'mod_chat',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420241),(55,0,'mod_chat','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420250),(56,0,'mod_chat','2013110500','2013110500','Plugin installed',NULL,'',0,1390420255),(57,0,'mod_choice',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420255),(58,0,'mod_choice','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420260),(59,0,'mod_choice','2013110500','2013110500','Plugin installed',NULL,'',0,1390420267),(60,0,'mod_data',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420267),(61,0,'mod_data','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420273),(62,0,'mod_data','2013110500','2013110500','Plugin installed',NULL,'',0,1390420287),(63,0,'mod_feedback',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420287),(64,0,'mod_feedback','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420299),(65,0,'mod_feedback','2013110500','2013110500','Plugin installed',NULL,'',0,1390420304),(66,0,'mod_folder',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420304),(67,0,'mod_folder','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420305),(68,0,'mod_folder','2013110500','2013110500','Plugin installed',NULL,'',0,1390420308),(69,0,'mod_forum',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420309),(70,0,'mod_forum','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420318),(71,0,'mod_forum','2013110500','2013110500','Plugin installed',NULL,'',0,1390420340),(72,0,'mod_glossary',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420340),(73,0,'mod_glossary','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420346),(74,0,'mod_glossary','2013110500','2013110500','Plugin installed',NULL,'',0,1390420355),(75,0,'mod_imscp',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420355),(76,0,'mod_imscp','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420356),(77,0,'mod_imscp','2013110500','2013110500','Plugin installed',NULL,'',0,1390420360),(78,0,'mod_label',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420360),(79,0,'mod_label','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420361),(80,0,'mod_label','2013110500','2013110500','Plugin installed',NULL,'',0,1390420362),(81,0,'mod_lesson',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420363),(82,0,'mod_lesson','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420368),(83,0,'mod_lesson','2013110500','2013110500','Plugin installed',NULL,'',0,1390420372),(84,0,'mod_lti',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420372),(85,0,'mod_lti','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420375),(86,0,'mod_lti','2013110500','2013110500','Plugin installed',NULL,'',0,1390420380),(87,0,'mod_page',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420380),(88,0,'mod_page','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420383),(89,0,'mod_page','2013110500','2013110500','Plugin installed',NULL,'',0,1390420385),(90,0,'mod_quiz',NULL,'2013110501','Starting plugin installation',NULL,'',0,1390420385),(91,0,'mod_quiz','2013110501','2013110501','Upgrade savepoint reached',NULL,'',0,1390420390),(92,0,'mod_quiz','2013110501','2013110501','Plugin installed',NULL,'',0,1390420402),(93,0,'mod_resource',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420402),(94,0,'mod_resource','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420404),(95,0,'mod_resource','2013110500','2013110500','Plugin installed',NULL,'',0,1390420406),(96,0,'mod_scorm',NULL,'2013110501','Starting plugin installation',NULL,'',0,1390420406),(97,0,'mod_scorm','2013110501','2013110501','Upgrade savepoint reached',NULL,'',0,1390420415),(98,0,'mod_scorm','2013110501','2013110501','Plugin installed',NULL,'',0,1390420423),(99,0,'mod_survey',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420424),(100,0,'mod_survey','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420428),(101,0,'mod_survey','2013110500','2013110500','Plugin installed',NULL,'',0,1390420447),(102,0,'mod_url',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420448),(103,0,'mod_url','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420449),(104,0,'mod_url','2013110500','2013110500','Plugin installed',NULL,'',0,1390420451),(105,0,'mod_wiki',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420451),(106,0,'mod_wiki','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420459),(107,0,'mod_wiki','2013110500','2013110500','Plugin installed',NULL,'',0,1390420468),(108,0,'mod_workshop',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420468),(109,0,'mod_workshop','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420484),(110,0,'mod_workshop','2013110500','2013110500','Plugin installed',NULL,'',0,1390420497),(111,0,'auth_cas',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420498),(112,0,'auth_cas','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420498),(113,0,'auth_cas','2013110500','2013110500','Plugin installed',NULL,'',0,1390420500),(114,0,'auth_db',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420500),(115,0,'auth_db','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420500),(116,0,'auth_db','2013110500','2013110500','Plugin installed',NULL,'',0,1390420501),(117,0,'auth_email',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420501),(118,0,'auth_email','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420501),(119,0,'auth_email','2013110500','2013110500','Plugin installed',NULL,'',0,1390420502),(120,0,'auth_fc',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420502),(121,0,'auth_fc','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420503),(122,0,'auth_fc','2013110500','2013110500','Plugin installed',NULL,'',0,1390420504),(123,0,'auth_imap',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420505),(124,0,'auth_imap','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420505),(125,0,'auth_imap','2013110500','2013110500','Plugin installed',NULL,'',0,1390420506),(126,0,'auth_ldap',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420506),(127,0,'auth_ldap','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420507),(128,0,'auth_ldap','2013110500','2013110500','Plugin installed',NULL,'',0,1390420507),(129,0,'auth_manual',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420507),(130,0,'auth_manual','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420507),(131,0,'auth_manual','2013110500','2013110500','Plugin installed',NULL,'',0,1390420508),(132,0,'auth_mnet',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420508),(133,0,'auth_mnet','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420508),(134,0,'auth_mnet','2013110500','2013110500','Plugin installed',NULL,'',0,1390420518),(135,0,'auth_nntp',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420519),(136,0,'auth_nntp','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420519),(137,0,'auth_nntp','2013110500','2013110500','Plugin installed',NULL,'',0,1390420520),(138,0,'auth_nologin',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420520),(139,0,'auth_nologin','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420520),(140,0,'auth_nologin','2013110500','2013110500','Plugin installed',NULL,'',0,1390420520),(141,0,'auth_none',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420521),(142,0,'auth_none','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420521),(143,0,'auth_none','2013110500','2013110500','Plugin installed',NULL,'',0,1390420521),(144,0,'auth_pam',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420521),(145,0,'auth_pam','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420521),(146,0,'auth_pam','2013110500','2013110500','Plugin installed',NULL,'',0,1390420522),(147,0,'auth_pop3',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420522),(148,0,'auth_pop3','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420522),(149,0,'auth_pop3','2013110500','2013110500','Plugin installed',NULL,'',0,1390420523),(150,0,'auth_radius',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420523),(151,0,'auth_radius','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420524),(152,0,'auth_radius','2013110500','2013110500','Plugin installed',NULL,'',0,1390420525),(153,0,'auth_shibboleth',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420525),(154,0,'auth_shibboleth','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420525),(155,0,'auth_shibboleth','2013110500','2013110500','Plugin installed',NULL,'',0,1390420526),(156,0,'auth_webservice',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420526),(157,0,'auth_webservice','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420527),(158,0,'auth_webservice','2013110500','2013110500','Plugin installed',NULL,'',0,1390420528),(159,0,'calendartype_gregorian',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420529),(160,0,'calendartype_gregorian','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420529),(161,0,'calendartype_gregorian','2013110500','2013110500','Plugin installed',NULL,'',0,1390420530),(162,0,'enrol_category',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420530),(163,0,'enrol_category','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420530),(164,0,'enrol_category','2013110500','2013110500','Plugin installed',NULL,'',0,1390420531),(165,0,'enrol_cohort',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420531),(166,0,'enrol_cohort','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420532),(167,0,'enrol_cohort','2013110500','2013110500','Plugin installed',NULL,'',0,1390420532),(168,0,'enrol_database',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420533),(169,0,'enrol_database','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420533),(170,0,'enrol_database','2013110500','2013110500','Plugin installed',NULL,'',0,1390420534),(171,0,'enrol_flatfile',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420534),(172,0,'enrol_flatfile','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420535),(173,0,'enrol_flatfile','2013110500','2013110500','Plugin installed',NULL,'',0,1390420537),(174,0,'enrol_guest',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420538),(175,0,'enrol_guest','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420538),(176,0,'enrol_guest','2013110500','2013110500','Plugin installed',NULL,'',0,1390420538),(177,0,'enrol_imsenterprise',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420539),(178,0,'enrol_imsenterprise','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420539),(179,0,'enrol_imsenterprise','2013110500','2013110500','Plugin installed',NULL,'',0,1390420541),(180,0,'enrol_ldap',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420542),(181,0,'enrol_ldap','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420542),(182,0,'enrol_ldap','2013110500','2013110500','Plugin installed',NULL,'',0,1390420543),(183,0,'enrol_manual',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420544),(184,0,'enrol_manual','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420544),(185,0,'enrol_manual','2013110500','2013110500','Plugin installed',NULL,'',0,1390420547),(186,0,'enrol_meta',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420547),(187,0,'enrol_meta','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420547),(188,0,'enrol_meta','2013110500','2013110500','Plugin installed',NULL,'',0,1390420549),(189,0,'enrol_mnet',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420549),(190,0,'enrol_mnet','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420549),(191,0,'enrol_mnet','2013110500','2013110500','Plugin installed',NULL,'',0,1390420557),(192,0,'enrol_paypal',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420558),(193,0,'enrol_paypal','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420558),(194,0,'enrol_paypal','2013110500','2013110500','Plugin installed',NULL,'',0,1390420561),(195,0,'enrol_self',NULL,'2013110501','Starting plugin installation',NULL,'',0,1390420561),(196,0,'enrol_self','2013110501','2013110501','Upgrade savepoint reached',NULL,'',0,1390420561),(197,0,'enrol_self','2013110501','2013110501','Plugin installed',NULL,'',0,1390420563),(198,0,'message_email',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420563),(199,0,'message_email','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420564),(200,0,'message_email','2013110500','2013110500','Plugin installed',NULL,'',0,1390420566),(201,0,'message_jabber',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420567),(202,0,'message_jabber','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420567),(203,0,'message_jabber','2013110500','2013110500','Plugin installed',NULL,'',0,1390420568),(204,0,'message_popup',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420568),(205,0,'message_popup','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420568),(206,0,'message_popup','2013110500','2013110500','Plugin installed',NULL,'',0,1390420569),(207,0,'block_activity_modules',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420569),(208,0,'block_activity_modules','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420569),(209,0,'block_activity_modules','2013110500','2013110500','Plugin installed',NULL,'',0,1390420570),(210,0,'block_admin_bookmarks',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420570),(211,0,'block_admin_bookmarks','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420570),(212,0,'block_admin_bookmarks','2013110500','2013110500','Plugin installed',NULL,'',0,1390420571),(213,0,'block_badges',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420571),(214,0,'block_badges','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420572),(215,0,'block_badges','2013110500','2013110500','Plugin installed',NULL,'',0,1390420573),(216,0,'block_blog_menu',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420573),(217,0,'block_blog_menu','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420573),(218,0,'block_blog_menu','2013110500','2013110500','Plugin installed',NULL,'',0,1390420573),(219,0,'block_blog_recent',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420574),(220,0,'block_blog_recent','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420574),(221,0,'block_blog_recent','2013110500','2013110500','Plugin installed',NULL,'',0,1390420575),(222,0,'block_blog_tags',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420576),(223,0,'block_blog_tags','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420576),(224,0,'block_blog_tags','2013110500','2013110500','Plugin installed',NULL,'',0,1390420576),(225,0,'block_calendar_month',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420576),(226,0,'block_calendar_month','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420576),(227,0,'block_calendar_month','2013110500','2013110500','Plugin installed',NULL,'',0,1390420579),(228,0,'block_calendar_upcoming',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420579),(229,0,'block_calendar_upcoming','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420580),(230,0,'block_calendar_upcoming','2013110500','2013110500','Plugin installed',NULL,'',0,1390420581),(231,0,'block_comments',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420581),(232,0,'block_comments','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420582),(233,0,'block_comments','2013110500','2013110500','Plugin installed',NULL,'',0,1390420583),(234,0,'block_community',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420583),(235,0,'block_community','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420584),(236,0,'block_community','2013110500','2013110500','Plugin installed',NULL,'',0,1390420585),(237,0,'block_completionstatus',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420585),(238,0,'block_completionstatus','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420585),(239,0,'block_completionstatus','2013110500','2013110500','Plugin installed',NULL,'',0,1390420587),(240,0,'block_course_list',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420587),(241,0,'block_course_list','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420587),(242,0,'block_course_list','2013110500','2013110500','Plugin installed',NULL,'',0,1390420588),(243,0,'block_course_overview',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420589),(244,0,'block_course_overview','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420589),(245,0,'block_course_overview','2013110500','2013110500','Plugin installed',NULL,'',0,1390420593),(246,0,'block_course_summary',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420593),(247,0,'block_course_summary','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420593),(248,0,'block_course_summary','2013110500','2013110500','Plugin installed',NULL,'',0,1390420594),(249,0,'block_feedback',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420594),(250,0,'block_feedback','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420595),(251,0,'block_feedback','2013110500','2013110500','Plugin installed',NULL,'',0,1390420596),(252,0,'block_glossary_random',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420596),(253,0,'block_glossary_random','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420596),(254,0,'block_glossary_random','2013110500','2013110500','Plugin installed',NULL,'',0,1390420597),(255,0,'block_html',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420597),(256,0,'block_html','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420597),(257,0,'block_html','2013110500','2013110500','Plugin installed',NULL,'',0,1390420597),(258,0,'block_login',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420597),(259,0,'block_login','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420598),(260,0,'block_login','2013110500','2013110500','Plugin installed',NULL,'',0,1390420599),(261,0,'block_mentees',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420599),(262,0,'block_mentees','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420599),(263,0,'block_mentees','2013110500','2013110500','Plugin installed',NULL,'',0,1390420601),(264,0,'block_messages',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420601),(265,0,'block_messages','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420601),(266,0,'block_messages','2013110500','2013110500','Plugin installed',NULL,'',0,1390420604),(267,0,'block_mnet_hosts',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420604),(268,0,'block_mnet_hosts','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420604),(269,0,'block_mnet_hosts','2013110500','2013110500','Plugin installed',NULL,'',0,1390420605),(270,0,'block_myprofile',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420605),(271,0,'block_myprofile','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420607),(272,0,'block_myprofile','2013110500','2013110500','Plugin installed',NULL,'',0,1390420608),(273,0,'block_navigation',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420608),(274,0,'block_navigation','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420608),(275,0,'block_navigation','2013110500','2013110500','Plugin installed',NULL,'',0,1390420608),(276,0,'block_news_items',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420609),(277,0,'block_news_items','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420609),(278,0,'block_news_items','2013110500','2013110500','Plugin installed',NULL,'',0,1390420611),(279,0,'block_online_users',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420611),(280,0,'block_online_users','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420611),(281,0,'block_online_users','2013110500','2013110500','Plugin installed',NULL,'',0,1390420614),(282,0,'block_participants',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420614),(283,0,'block_participants','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420615),(284,0,'block_participants','2013110500','2013110500','Plugin installed',NULL,'',0,1390420615),(285,0,'block_private_files',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420616),(286,0,'block_private_files','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420616),(287,0,'block_private_files','2013110500','2013110500','Plugin installed',NULL,'',0,1390420617),(288,0,'block_quiz_results',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420617),(289,0,'block_quiz_results','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420617),(290,0,'block_quiz_results','2013110500','2013110500','Plugin installed',NULL,'',0,1390420618),(291,0,'block_recent_activity',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420618),(292,0,'block_recent_activity','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420618),(293,0,'block_recent_activity','2013110500','2013110500','Plugin installed',NULL,'',0,1390420619),(294,0,'block_rss_client',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420619),(295,0,'block_rss_client','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420620),(296,0,'block_rss_client','2013110500','2013110500','Plugin installed',NULL,'',0,1390420622),(297,0,'block_search_forums',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420622),(298,0,'block_search_forums','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420623),(299,0,'block_search_forums','2013110500','2013110500','Plugin installed',NULL,'',0,1390420623),(300,0,'block_section_links',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420623),(301,0,'block_section_links','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420624),(302,0,'block_section_links','2013110500','2013110500','Plugin installed',NULL,'',0,1390420624),(303,0,'block_selfcompletion',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420624),(304,0,'block_selfcompletion','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420625),(305,0,'block_selfcompletion','2013110500','2013110500','Plugin installed',NULL,'',0,1390420625),(306,0,'block_settings',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420626),(307,0,'block_settings','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420627),(308,0,'block_settings','2013110500','2013110500','Plugin installed',NULL,'',0,1390420628),(309,0,'block_site_main_menu',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420629),(310,0,'block_site_main_menu','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420629),(311,0,'block_site_main_menu','2013110500','2013110500','Plugin installed',NULL,'',0,1390420629),(312,0,'block_social_activities',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420630),(313,0,'block_social_activities','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420630),(314,0,'block_social_activities','2013110500','2013110500','Plugin installed',NULL,'',0,1390420630),(315,0,'block_tag_flickr',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420631),(316,0,'block_tag_flickr','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420631),(317,0,'block_tag_flickr','2013110500','2013110500','Plugin installed',NULL,'',0,1390420631),(318,0,'block_tag_youtube',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420631),(319,0,'block_tag_youtube','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420632),(320,0,'block_tag_youtube','2013110500','2013110500','Plugin installed',NULL,'',0,1390420633),(321,0,'block_tags',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420633),(322,0,'block_tags','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420633),(323,0,'block_tags','2013110500','2013110500','Plugin installed',NULL,'',0,1390420634),(324,0,'filter_activitynames',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420640),(325,0,'filter_activitynames','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420640),(326,0,'filter_activitynames','2013110500','2013110500','Plugin installed',NULL,'',0,1390420641),(327,0,'filter_algebra',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420641),(328,0,'filter_algebra','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420641),(329,0,'filter_algebra','2013110500','2013110500','Plugin installed',NULL,'',0,1390420642),(330,0,'filter_censor',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420643),(331,0,'filter_censor','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420643),(332,0,'filter_censor','2013110500','2013110500','Plugin installed',NULL,'',0,1390420643),(333,0,'filter_data',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420643),(334,0,'filter_data','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420643),(335,0,'filter_data','2013110500','2013110500','Plugin installed',NULL,'',0,1390420644),(336,0,'filter_emailprotect',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420645),(337,0,'filter_emailprotect','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420645),(338,0,'filter_emailprotect','2013110500','2013110500','Plugin installed',NULL,'',0,1390420645),(339,0,'filter_emoticon',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420646),(340,0,'filter_emoticon','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420646),(341,0,'filter_emoticon','2013110500','2013110500','Plugin installed',NULL,'',0,1390420646),(342,0,'filter_glossary',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420646),(343,0,'filter_glossary','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420646),(344,0,'filter_glossary','2013110500','2013110500','Plugin installed',NULL,'',0,1390420647),(345,0,'filter_mediaplugin',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420647),(346,0,'filter_mediaplugin','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420647),(347,0,'filter_mediaplugin','2013110500','2013110500','Plugin installed',NULL,'',0,1390420648),(348,0,'filter_multilang',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420648),(349,0,'filter_multilang','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420648),(350,0,'filter_multilang','2013110500','2013110500','Plugin installed',NULL,'',0,1390420648),(351,0,'filter_tex',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420649),(352,0,'filter_tex','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420650),(353,0,'filter_tex','2013110500','2013110500','Plugin installed',NULL,'',0,1390420651),(354,0,'filter_tidy',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420651),(355,0,'filter_tidy','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420652),(356,0,'filter_tidy','2013110500','2013110500','Plugin installed',NULL,'',0,1390420652),(357,0,'filter_urltolink',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420653),(358,0,'filter_urltolink','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420653),(359,0,'filter_urltolink','2013110500','2013110500','Plugin installed',NULL,'',0,1390420653),(360,0,'editor_textarea',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420653),(361,0,'editor_textarea','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420654),(362,0,'editor_textarea','2013110500','2013110500','Plugin installed',NULL,'',0,1390420654),(363,0,'editor_tinymce',NULL,'2013110600','Starting plugin installation',NULL,'',0,1390420654),(364,0,'editor_tinymce','2013110600','2013110600','Upgrade savepoint reached',NULL,'',0,1390420655),(365,0,'editor_tinymce','2013110600','2013110600','Plugin installed',NULL,'',0,1390420655),(366,0,'format_singleactivity',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420655),(367,0,'format_singleactivity','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420655),(368,0,'format_singleactivity','2013110500','2013110500','Plugin installed',NULL,'',0,1390420656),(369,0,'format_social',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420656),(370,0,'format_social','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420656),(371,0,'format_social','2013110500','2013110500','Plugin installed',NULL,'',0,1390420656),(372,0,'format_topics',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420657),(373,0,'format_topics','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420657),(374,0,'format_topics','2013110500','2013110500','Plugin installed',NULL,'',0,1390420657),(375,0,'format_weeks',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420657),(376,0,'format_weeks','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420657),(377,0,'format_weeks','2013110500','2013110500','Plugin installed',NULL,'',0,1390420658),(378,0,'profilefield_checkbox',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420658),(379,0,'profilefield_checkbox','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420659),(380,0,'profilefield_checkbox','2013110500','2013110500','Plugin installed',NULL,'',0,1390420660),(381,0,'profilefield_datetime',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420660),(382,0,'profilefield_datetime','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420660),(383,0,'profilefield_datetime','2013110500','2013110500','Plugin installed',NULL,'',0,1390420661),(384,0,'profilefield_menu',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420661),(385,0,'profilefield_menu','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420661),(386,0,'profilefield_menu','2013110500','2013110500','Plugin installed',NULL,'',0,1390420663),(387,0,'profilefield_text',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420663),(388,0,'profilefield_text','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420663),(389,0,'profilefield_text','2013110500','2013110500','Plugin installed',NULL,'',0,1390420664),(390,0,'profilefield_textarea',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420664),(391,0,'profilefield_textarea','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420664),(392,0,'profilefield_textarea','2013110500','2013110500','Plugin installed',NULL,'',0,1390420665),(393,0,'report_backups',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420665),(394,0,'report_backups','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420665),(395,0,'report_backups','2013110500','2013110500','Plugin installed',NULL,'',0,1390420665),(396,0,'report_completion',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420666),(397,0,'report_completion','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420666),(398,0,'report_completion','2013110500','2013110500','Plugin installed',NULL,'',0,1390420667),(399,0,'report_configlog',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420667),(400,0,'report_configlog','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420667),(401,0,'report_configlog','2013110500','2013110500','Plugin installed',NULL,'',0,1390420668),(402,0,'report_courseoverview',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420668),(403,0,'report_courseoverview','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420668),(404,0,'report_courseoverview','2013110500','2013110500','Plugin installed',NULL,'',0,1390420670),(405,0,'report_log',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420670),(406,0,'report_log','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420670),(407,0,'report_log','2013110500','2013110500','Plugin installed',NULL,'',0,1390420672),(408,0,'report_loglive',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420673),(409,0,'report_loglive','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420673),(410,0,'report_loglive','2013110500','2013110500','Plugin installed',NULL,'',0,1390420674),(411,0,'report_outline',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420675),(412,0,'report_outline','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420676),(413,0,'report_outline','2013110500','2013110500','Plugin installed',NULL,'',0,1390420678),(414,0,'report_participation',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420678),(415,0,'report_participation','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420678),(416,0,'report_participation','2013110500','2013110500','Plugin installed',NULL,'',0,1390420680),(417,0,'report_performance',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420680),(418,0,'report_performance','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420680),(419,0,'report_performance','2013110500','2013110500','Plugin installed',NULL,'',0,1390420681),(420,0,'report_progress',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420681),(421,0,'report_progress','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420681),(422,0,'report_progress','2013110500','2013110500','Plugin installed',NULL,'',0,1390420683),(423,0,'report_questioninstances',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420683),(424,0,'report_questioninstances','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420684),(425,0,'report_questioninstances','2013110500','2013110500','Plugin installed',NULL,'',0,1390420684),(426,0,'report_security',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420684),(427,0,'report_security','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420685),(428,0,'report_security','2013110500','2013110500','Plugin installed',NULL,'',0,1390420686),(429,0,'report_stats',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420686),(430,0,'report_stats','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420687),(431,0,'report_stats','2013110500','2013110500','Plugin installed',NULL,'',0,1390420689),(432,0,'gradeexport_ods',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420689),(433,0,'gradeexport_ods','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420690),(434,0,'gradeexport_ods','2013110500','2013110500','Plugin installed',NULL,'',0,1390420692),(435,0,'gradeexport_txt',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420692),(436,0,'gradeexport_txt','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420692),(437,0,'gradeexport_txt','2013110500','2013110500','Plugin installed',NULL,'',0,1390420694),(438,0,'gradeexport_xls',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420694),(439,0,'gradeexport_xls','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420694),(440,0,'gradeexport_xls','2013110500','2013110500','Plugin installed',NULL,'',0,1390420696),(441,0,'gradeexport_xml',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420696),(442,0,'gradeexport_xml','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420696),(443,0,'gradeexport_xml','2013110500','2013110500','Plugin installed',NULL,'',0,1390420697),(444,0,'gradeimport_csv',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420697),(445,0,'gradeimport_csv','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420698),(446,0,'gradeimport_csv','2013110500','2013110500','Plugin installed',NULL,'',0,1390420700),(447,0,'gradeimport_xml',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420700),(448,0,'gradeimport_xml','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420701),(449,0,'gradeimport_xml','2013110500','2013110500','Plugin installed',NULL,'',0,1390420702),(450,0,'gradereport_grader',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420702),(451,0,'gradereport_grader','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420702),(452,0,'gradereport_grader','2013110500','2013110500','Plugin installed',NULL,'',0,1390420704),(453,0,'gradereport_outcomes',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420704),(454,0,'gradereport_outcomes','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420704),(455,0,'gradereport_outcomes','2013110500','2013110500','Plugin installed',NULL,'',0,1390420705),(456,0,'gradereport_overview',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420705),(457,0,'gradereport_overview','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420705),(458,0,'gradereport_overview','2013110500','2013110500','Plugin installed',NULL,'',0,1390420706),(459,0,'gradereport_user',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420706),(460,0,'gradereport_user','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420706),(461,0,'gradereport_user','2013110500','2013110500','Plugin installed',NULL,'',0,1390420708),(462,0,'gradingform_guide',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420708),(463,0,'gradingform_guide','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420713),(464,0,'gradingform_guide','2013110500','2013110500','Plugin installed',NULL,'',0,1390420713),(465,0,'gradingform_rubric',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420713),(466,0,'gradingform_rubric','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420717),(467,0,'gradingform_rubric','2013110500','2013110500','Plugin installed',NULL,'',0,1390420718),(468,0,'mnetservice_enrol',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420718),(469,0,'mnetservice_enrol','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420720),(470,0,'mnetservice_enrol','2013110500','2013110500','Plugin installed',NULL,'',0,1390420720),(471,0,'webservice_amf',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420720),(472,0,'webservice_amf','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420721),(473,0,'webservice_amf','2013110500','2013110500','Plugin installed',NULL,'',0,1390420721),(474,0,'webservice_rest',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420721),(475,0,'webservice_rest','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420722),(476,0,'webservice_rest','2013110500','2013110500','Plugin installed',NULL,'',0,1390420722),(477,0,'webservice_soap',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420722),(478,0,'webservice_soap','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420723),(479,0,'webservice_soap','2013110500','2013110500','Plugin installed',NULL,'',0,1390420726),(480,0,'webservice_xmlrpc',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420727),(481,0,'webservice_xmlrpc','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420727),(482,0,'webservice_xmlrpc','2013110500','2013110500','Plugin installed',NULL,'',0,1390420727),(483,0,'repository_alfresco',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420727),(484,0,'repository_alfresco','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420728),(485,0,'repository_alfresco','2013110500','2013110500','Plugin installed',NULL,'',0,1390420728),(486,0,'repository_areafiles',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420728),(487,0,'repository_areafiles','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420728),(488,0,'repository_areafiles','2013110500','2013110500','Plugin installed',NULL,'',0,1390420730),(489,0,'repository_boxnet',NULL,'2013110700','Starting plugin installation',NULL,'',0,1390420730),(490,0,'repository_boxnet','2013110700','2013110700','Upgrade savepoint reached',NULL,'',0,1390420730),(491,0,'repository_boxnet','2013110700','2013110700','Plugin installed',NULL,'',0,1390420730),(492,0,'repository_coursefiles',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420731),(493,0,'repository_coursefiles','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420731),(494,0,'repository_coursefiles','2013110500','2013110500','Plugin installed',NULL,'',0,1390420732),(495,0,'repository_dropbox',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420732),(496,0,'repository_dropbox','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420733),(497,0,'repository_dropbox','2013110500','2013110500','Plugin installed',NULL,'',0,1390420734),(498,0,'repository_equella',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420734),(499,0,'repository_equella','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420734),(500,0,'repository_equella','2013110500','2013110500','Plugin installed',NULL,'',0,1390420734),(501,0,'repository_filesystem',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420735),(502,0,'repository_filesystem','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420735),(503,0,'repository_filesystem','2013110500','2013110500','Plugin installed',NULL,'',0,1390420736),(504,0,'repository_flickr',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420737),(505,0,'repository_flickr','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420738),(506,0,'repository_flickr','2013110500','2013110500','Plugin installed',NULL,'',0,1390420739),(507,0,'repository_flickr_public',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420739),(508,0,'repository_flickr_public','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420740),(509,0,'repository_flickr_public','2013110500','2013110500','Plugin installed',NULL,'',0,1390420740),(510,0,'repository_googledocs',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420740),(511,0,'repository_googledocs','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420740),(512,0,'repository_googledocs','2013110500','2013110500','Plugin installed',NULL,'',0,1390420741),(513,0,'repository_local',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420741),(514,0,'repository_local','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420741),(515,0,'repository_local','2013110500','2013110500','Plugin installed',NULL,'',0,1390420743),(516,0,'repository_merlot',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420743),(517,0,'repository_merlot','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420743),(518,0,'repository_merlot','2013110500','2013110500','Plugin installed',NULL,'',0,1390420744),(519,0,'repository_picasa',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420744),(520,0,'repository_picasa','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420744),(521,0,'repository_picasa','2013110500','2013110500','Plugin installed',NULL,'',0,1390420746),(522,0,'repository_recent',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420746),(523,0,'repository_recent','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420746),(524,0,'repository_recent','2013110500','2013110500','Plugin installed',NULL,'',0,1390420748),(525,0,'repository_s3',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420748),(526,0,'repository_s3','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420748),(527,0,'repository_s3','2013110500','2013110500','Plugin installed',NULL,'',0,1390420749),(528,0,'repository_skydrive',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420750),(529,0,'repository_skydrive','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420752),(530,0,'repository_skydrive','2013110500','2013110500','Plugin installed',NULL,'',0,1390420753),(531,0,'repository_upload',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420753),(532,0,'repository_upload','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420753),(533,0,'repository_upload','2013110500','2013110500','Plugin installed',NULL,'',0,1390420755),(534,0,'repository_url',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420755),(535,0,'repository_url','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420755),(536,0,'repository_url','2013110500','2013110500','Plugin installed',NULL,'',0,1390420757),(537,0,'repository_user',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420757),(538,0,'repository_user','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420757),(539,0,'repository_user','2013110500','2013110500','Plugin installed',NULL,'',0,1390420759),(540,0,'repository_webdav',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420759),(541,0,'repository_webdav','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420759),(542,0,'repository_webdav','2013110500','2013110500','Plugin installed',NULL,'',0,1390420760),(543,0,'repository_wikimedia',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420760),(544,0,'repository_wikimedia','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420760),(545,0,'repository_wikimedia','2013110500','2013110500','Plugin installed',NULL,'',0,1390420764),(546,0,'repository_youtube',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420764),(547,0,'repository_youtube','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420765),(548,0,'repository_youtube','2013110500','2013110500','Plugin installed',NULL,'',0,1390420766),(549,0,'portfolio_boxnet',NULL,'2013110602','Starting plugin installation',NULL,'',0,1390420766),(550,0,'portfolio_boxnet','2013110602','2013110602','Upgrade savepoint reached',NULL,'',0,1390420766),(551,0,'portfolio_boxnet','2013110602','2013110602','Plugin installed',NULL,'',0,1390420767),(552,0,'portfolio_download',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420767),(553,0,'portfolio_download','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420767),(554,0,'portfolio_download','2013110500','2013110500','Plugin installed',NULL,'',0,1390420767),(555,0,'portfolio_flickr',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420768),(556,0,'portfolio_flickr','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420768),(557,0,'portfolio_flickr','2013110500','2013110500','Plugin installed',NULL,'',0,1390420769),(558,0,'portfolio_googledocs',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420769),(559,0,'portfolio_googledocs','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420769),(560,0,'portfolio_googledocs','2013110500','2013110500','Plugin installed',NULL,'',0,1390420769),(561,0,'portfolio_mahara',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420770),(562,0,'portfolio_mahara','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420770),(563,0,'portfolio_mahara','2013110500','2013110500','Plugin installed',NULL,'',0,1390420772),(564,0,'portfolio_picasa',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420772),(565,0,'portfolio_picasa','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420772),(566,0,'portfolio_picasa','2013110500','2013110500','Plugin installed',NULL,'',0,1390420773),(567,0,'qbehaviour_adaptive',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420773),(568,0,'qbehaviour_adaptive','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420773),(569,0,'qbehaviour_adaptive','2013110500','2013110500','Plugin installed',NULL,'',0,1390420773),(570,0,'qbehaviour_adaptivenopenalty',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420773),(571,0,'qbehaviour_adaptivenopenalty','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420774),(572,0,'qbehaviour_adaptivenopenalty','2013110500','2013110500','Plugin installed',NULL,'',0,1390420775),(573,0,'qbehaviour_deferredcbm',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420776),(574,0,'qbehaviour_deferredcbm','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420777),(575,0,'qbehaviour_deferredcbm','2013110500','2013110500','Plugin installed',NULL,'',0,1390420777),(576,0,'qbehaviour_deferredfeedback',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420777),(577,0,'qbehaviour_deferredfeedback','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420777),(578,0,'qbehaviour_deferredfeedback','2013110500','2013110500','Plugin installed',NULL,'',0,1390420778),(579,0,'qbehaviour_immediatecbm',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420778),(580,0,'qbehaviour_immediatecbm','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420778),(581,0,'qbehaviour_immediatecbm','2013110500','2013110500','Plugin installed',NULL,'',0,1390420778),(582,0,'qbehaviour_immediatefeedback',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420778),(583,0,'qbehaviour_immediatefeedback','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420779),(584,0,'qbehaviour_immediatefeedback','2013110500','2013110500','Plugin installed',NULL,'',0,1390420779),(585,0,'qbehaviour_informationitem',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420779),(586,0,'qbehaviour_informationitem','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420779),(587,0,'qbehaviour_informationitem','2013110500','2013110500','Plugin installed',NULL,'',0,1390420780),(588,0,'qbehaviour_interactive',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420780),(589,0,'qbehaviour_interactive','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420781),(590,0,'qbehaviour_interactive','2013110500','2013110500','Plugin installed',NULL,'',0,1390420781),(591,0,'qbehaviour_interactivecountback',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420781),(592,0,'qbehaviour_interactivecountback','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420782),(593,0,'qbehaviour_interactivecountback','2013110500','2013110500','Plugin installed',NULL,'',0,1390420782),(594,0,'qbehaviour_manualgraded',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420782),(595,0,'qbehaviour_manualgraded','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420782),(596,0,'qbehaviour_manualgraded','2013110500','2013110500','Plugin installed',NULL,'',0,1390420783),(597,0,'qbehaviour_missing',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420783),(598,0,'qbehaviour_missing','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420784),(599,0,'qbehaviour_missing','2013110500','2013110500','Plugin installed',NULL,'',0,1390420784),(600,0,'qformat_aiken',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420784),(601,0,'qformat_aiken','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420785),(602,0,'qformat_aiken','2013110500','2013110500','Plugin installed',NULL,'',0,1390420785),(603,0,'qformat_blackboard_six',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420785),(604,0,'qformat_blackboard_six','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420785),(605,0,'qformat_blackboard_six','2013110500','2013110500','Plugin installed',NULL,'',0,1390420785),(606,0,'qformat_examview',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420785),(607,0,'qformat_examview','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420786),(608,0,'qformat_examview','2013110500','2013110500','Plugin installed',NULL,'',0,1390420786),(609,0,'qformat_gift',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420786),(610,0,'qformat_gift','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420786),(611,0,'qformat_gift','2013110500','2013110500','Plugin installed',NULL,'',0,1390420786),(612,0,'qformat_learnwise',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420786),(613,0,'qformat_learnwise','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420787),(614,0,'qformat_learnwise','2013110500','2013110500','Plugin installed',NULL,'',0,1390420787),(615,0,'qformat_missingword',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420787),(616,0,'qformat_missingword','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420787),(617,0,'qformat_missingword','2013110500','2013110500','Plugin installed',NULL,'',0,1390420789),(618,0,'qformat_multianswer',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420789),(619,0,'qformat_multianswer','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420790),(620,0,'qformat_multianswer','2013110500','2013110500','Plugin installed',NULL,'',0,1390420791),(621,0,'qformat_webct',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420791),(622,0,'qformat_webct','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420791),(623,0,'qformat_webct','2013110500','2013110500','Plugin installed',NULL,'',0,1390420792),(624,0,'qformat_xhtml',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420792),(625,0,'qformat_xhtml','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420792),(626,0,'qformat_xhtml','2013110500','2013110500','Plugin installed',NULL,'',0,1390420793),(627,0,'qformat_xml',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420793),(628,0,'qformat_xml','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420793),(629,0,'qformat_xml','2013110500','2013110500','Plugin installed',NULL,'',0,1390420794),(630,0,'tool_assignmentupgrade',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420794),(631,0,'tool_assignmentupgrade','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420794),(632,0,'tool_assignmentupgrade','2013110500','2013110500','Plugin installed',NULL,'',0,1390420795),(633,0,'tool_behat',NULL,'2013110501','Starting plugin installation',NULL,'',0,1390420795),(634,0,'tool_behat','2013110501','2013110501','Upgrade savepoint reached',NULL,'',0,1390420795),(635,0,'tool_behat','2013110501','2013110501','Plugin installed',NULL,'',0,1390420796),(636,0,'tool_capability',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420796),(637,0,'tool_capability','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420796),(638,0,'tool_capability','2013110500','2013110500','Plugin installed',NULL,'',0,1390420796),(639,0,'tool_customlang',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420796),(640,0,'tool_customlang','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420797),(641,0,'tool_customlang','2013110500','2013110500','Plugin installed',NULL,'',0,1390420799),(642,0,'tool_dbtransfer',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420799),(643,0,'tool_dbtransfer','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420799),(644,0,'tool_dbtransfer','2013110500','2013110500','Plugin installed',NULL,'',0,1390420800),(645,0,'tool_generator',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420800),(646,0,'tool_generator','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420801),(647,0,'tool_generator','2013110500','2013110500','Plugin installed',NULL,'',0,1390420802),(648,0,'tool_health',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420802),(649,0,'tool_health','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420802),(650,0,'tool_health','2013110500','2013110500','Plugin installed',NULL,'',0,1390420802),(651,0,'tool_innodb',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420802),(652,0,'tool_innodb','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420802),(653,0,'tool_innodb','2013110500','2013110500','Plugin installed',NULL,'',0,1390420803),(654,0,'tool_installaddon',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420803),(655,0,'tool_installaddon','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420803),(656,0,'tool_installaddon','2013110500','2013110500','Plugin installed',NULL,'',0,1390420803),(657,0,'tool_langimport',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420803),(658,0,'tool_langimport','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420804),(659,0,'tool_langimport','2013110500','2013110500','Plugin installed',NULL,'',0,1390420804),(660,0,'tool_multilangupgrade',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420804),(661,0,'tool_multilangupgrade','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420804),(662,0,'tool_multilangupgrade','2013110500','2013110500','Plugin installed',NULL,'',0,1390420805),(663,0,'tool_phpunit',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420805),(664,0,'tool_phpunit','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420805),(665,0,'tool_phpunit','2013110500','2013110500','Plugin installed',NULL,'',0,1390420805),(666,0,'tool_profiling',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420805),(667,0,'tool_profiling','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420806),(668,0,'tool_profiling','2013110500','2013110500','Plugin installed',NULL,'',0,1390420806),(669,0,'tool_qeupgradehelper',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420806),(670,0,'tool_qeupgradehelper','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420806),(671,0,'tool_qeupgradehelper','2013110500','2013110500','Plugin installed',NULL,'',0,1390420808),(672,0,'tool_replace',NULL,'2013110501','Starting plugin installation',NULL,'',0,1390420808),(673,0,'tool_replace','2013110501','2013110501','Upgrade savepoint reached',NULL,'',0,1390420808),(674,0,'tool_replace','2013110501','2013110501','Plugin installed',NULL,'',0,1390420808),(675,0,'tool_spamcleaner',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420808),(676,0,'tool_spamcleaner','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420808),(677,0,'tool_spamcleaner','2013110500','2013110500','Plugin installed',NULL,'',0,1390420808),(678,0,'tool_timezoneimport',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420809),(679,0,'tool_timezoneimport','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420809),(680,0,'tool_timezoneimport','2013110500','2013110500','Plugin installed',NULL,'',0,1390420809),(681,0,'tool_unsuproles',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420809),(682,0,'tool_unsuproles','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420810),(683,0,'tool_unsuproles','2013110500','2013110500','Plugin installed',NULL,'',0,1390420810),(684,0,'tool_uploadcourse',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420810),(685,0,'tool_uploadcourse','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420810),(686,0,'tool_uploadcourse','2013110500','2013110500','Plugin installed',NULL,'',0,1390420811),(687,0,'tool_uploaduser',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420811),(688,0,'tool_uploaduser','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420811),(689,0,'tool_uploaduser','2013110500','2013110500','Plugin installed',NULL,'',0,1390420812),(690,0,'tool_xmldb',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420812),(691,0,'tool_xmldb','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420814),(692,0,'tool_xmldb','2013110500','2013110500','Plugin installed',NULL,'',0,1390420815),(693,0,'cachestore_file',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420815),(694,0,'cachestore_file','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420815),(695,0,'cachestore_file','2013110500','2013110500','Plugin installed',NULL,'',0,1390420815),(696,0,'cachestore_memcache',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420816),(697,0,'cachestore_memcache','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420816),(698,0,'cachestore_memcache','2013110500','2013110500','Plugin installed',NULL,'',0,1390420816),(699,0,'cachestore_memcached',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420817),(700,0,'cachestore_memcached','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420817),(701,0,'cachestore_memcached','2013110500','2013110500','Plugin installed',NULL,'',0,1390420817),(702,0,'cachestore_mongodb',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420817),(703,0,'cachestore_mongodb','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420817),(704,0,'cachestore_mongodb','2013110500','2013110500','Plugin installed',NULL,'',0,1390420818),(705,0,'cachestore_session',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420818),(706,0,'cachestore_session','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420818),(707,0,'cachestore_session','2013110500','2013110500','Plugin installed',NULL,'',0,1390420819),(708,0,'cachestore_static',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420819),(709,0,'cachestore_static','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420819),(710,0,'cachestore_static','2013110500','2013110500','Plugin installed',NULL,'',0,1390420819),(711,0,'cachelock_file',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420819),(712,0,'cachelock_file','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420820),(713,0,'cachelock_file','2013110500','2013110500','Plugin installed',NULL,'',0,1390420820),(714,0,'theme_afterburner',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420820),(715,0,'theme_afterburner','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420820),(716,0,'theme_afterburner','2013110500','2013110500','Plugin installed',NULL,'',0,1390420821),(717,0,'theme_anomaly',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420821),(718,0,'theme_anomaly','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420821),(719,0,'theme_anomaly','2013110500','2013110500','Plugin installed',NULL,'',0,1390420821),(720,0,'theme_arialist',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420821),(721,0,'theme_arialist','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420822),(722,0,'theme_arialist','2013110500','2013110500','Plugin installed',NULL,'',0,1390420822),(723,0,'theme_base',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420822),(724,0,'theme_base','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420822),(725,0,'theme_base','2013110500','2013110500','Plugin installed',NULL,'',0,1390420822),(726,0,'theme_binarius',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420822),(727,0,'theme_binarius','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420823),(728,0,'theme_binarius','2013110500','2013110500','Plugin installed',NULL,'',0,1390420823),(729,0,'theme_bootstrapbase',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420823),(730,0,'theme_bootstrapbase','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420823),(731,0,'theme_bootstrapbase','2013110500','2013110500','Plugin installed',NULL,'',0,1390420824),(732,0,'theme_boxxie',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420824),(733,0,'theme_boxxie','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420825),(734,0,'theme_boxxie','2013110500','2013110500','Plugin installed',NULL,'',0,1390420827),(735,0,'theme_brick',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420827),(736,0,'theme_brick','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420827),(737,0,'theme_brick','2013110500','2013110500','Plugin installed',NULL,'',0,1390420827),(738,0,'theme_canvas',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420827),(739,0,'theme_canvas','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420828),(740,0,'theme_canvas','2013110500','2013110500','Plugin installed',NULL,'',0,1390420828),(741,0,'theme_clean',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420828),(742,0,'theme_clean','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420828),(743,0,'theme_clean','2013110500','2013110500','Plugin installed',NULL,'',0,1390420829),(744,0,'theme_formal_white',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420829),(745,0,'theme_formal_white','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420829),(746,0,'theme_formal_white','2013110500','2013110500','Plugin installed',NULL,'',0,1390420830),(747,0,'theme_formfactor',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420830),(748,0,'theme_formfactor','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420831),(749,0,'theme_formfactor','2013110500','2013110500','Plugin installed',NULL,'',0,1390420831),(750,0,'theme_fusion',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420831),(751,0,'theme_fusion','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420831),(752,0,'theme_fusion','2013110500','2013110500','Plugin installed',NULL,'',0,1390420832),(753,0,'theme_leatherbound',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420832),(754,0,'theme_leatherbound','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420832),(755,0,'theme_leatherbound','2013110500','2013110500','Plugin installed',NULL,'',0,1390420832),(756,0,'theme_magazine',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420832),(757,0,'theme_magazine','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420833),(758,0,'theme_magazine','2013110500','2013110500','Plugin installed',NULL,'',0,1390420833),(759,0,'theme_nimble',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420833),(760,0,'theme_nimble','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420834),(761,0,'theme_nimble','2013110500','2013110500','Plugin installed',NULL,'',0,1390420834),(762,0,'theme_nonzero',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420835),(763,0,'theme_nonzero','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420835),(764,0,'theme_nonzero','2013110500','2013110500','Plugin installed',NULL,'',0,1390420835),(765,0,'theme_overlay',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420835),(766,0,'theme_overlay','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420835),(767,0,'theme_overlay','2013110500','2013110500','Plugin installed',NULL,'',0,1390420836),(768,0,'theme_serenity',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420836),(769,0,'theme_serenity','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420836),(770,0,'theme_serenity','2013110500','2013110500','Plugin installed',NULL,'',0,1390420836),(771,0,'theme_sky_high',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420837),(772,0,'theme_sky_high','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420837),(773,0,'theme_sky_high','2013110500','2013110500','Plugin installed',NULL,'',0,1390420837),(774,0,'theme_splash',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420837),(775,0,'theme_splash','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420838),(776,0,'theme_splash','2013110500','2013110500','Plugin installed',NULL,'',0,1390420839),(777,0,'theme_standard',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420840),(778,0,'theme_standard','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420840),(779,0,'theme_standard','2013110500','2013110500','Plugin installed',NULL,'',0,1390420840),(780,0,'theme_standardold',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420840),(781,0,'theme_standardold','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420840),(782,0,'theme_standardold','2013110500','2013110500','Plugin installed',NULL,'',0,1390420841),(783,0,'assignsubmission_comments',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420841),(784,0,'assignsubmission_comments','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420841),(785,0,'assignsubmission_comments','2013110500','2013110500','Plugin installed',NULL,'',0,1390420842),(786,0,'assignsubmission_file',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420842),(787,0,'assignsubmission_file','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420843),(788,0,'assignsubmission_file','2013110500','2013110500','Plugin installed',NULL,'',0,1390420843),(789,0,'assignsubmission_onlinetext',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420843),(790,0,'assignsubmission_onlinetext','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420844),(791,0,'assignsubmission_onlinetext','2013110500','2013110500','Plugin installed',NULL,'',0,1390420845),(792,0,'assignfeedback_comments',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420845),(793,0,'assignfeedback_comments','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420846),(794,0,'assignfeedback_comments','2013110500','2013110500','Plugin installed',NULL,'',0,1390420847),(795,0,'assignfeedback_editpdf',NULL,'2013110800','Starting plugin installation',NULL,'',0,1390420848),(796,0,'assignfeedback_editpdf','2013110800','2013110800','Upgrade savepoint reached',NULL,'',0,1390420852),(797,0,'assignfeedback_editpdf','2013110800','2013110800','Plugin installed',NULL,'',0,1390420854),(798,0,'assignfeedback_file',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420854),(799,0,'assignfeedback_file','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420856),(800,0,'assignfeedback_file','2013110500','2013110500','Plugin installed',NULL,'',0,1390420857),(801,0,'assignfeedback_offline',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420857),(802,0,'assignfeedback_offline','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420858),(803,0,'assignfeedback_offline','2013110500','2013110500','Plugin installed',NULL,'',0,1390420858),(804,0,'assignment_offline',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420858),(805,0,'assignment_offline','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420858),(806,0,'assignment_offline','2013110500','2013110500','Plugin installed',NULL,'',0,1390420858),(807,0,'assignment_online',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420859),(808,0,'assignment_online','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420859),(809,0,'assignment_online','2013110500','2013110500','Plugin installed',NULL,'',0,1390420859),(810,0,'assignment_upload',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420860),(811,0,'assignment_upload','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420860),(812,0,'assignment_upload','2013110500','2013110500','Plugin installed',NULL,'',0,1390420860),(813,0,'assignment_uploadsingle',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420860),(814,0,'assignment_uploadsingle','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420861),(815,0,'assignment_uploadsingle','2013110500','2013110500','Plugin installed',NULL,'',0,1390420861),(816,0,'booktool_exportimscp',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420861),(817,0,'booktool_exportimscp','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420861),(818,0,'booktool_exportimscp','2013110500','2013110500','Plugin installed',NULL,'',0,1390420862),(819,0,'booktool_importhtml',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420862),(820,0,'booktool_importhtml','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420862),(821,0,'booktool_importhtml','2013110500','2013110500','Plugin installed',NULL,'',0,1390420862),(822,0,'booktool_print',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420863),(823,0,'booktool_print','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420864),(824,0,'booktool_print','2013110500','2013110500','Plugin installed',NULL,'',0,1390420866),(825,0,'datafield_checkbox',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420866),(826,0,'datafield_checkbox','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420866),(827,0,'datafield_checkbox','2013110500','2013110500','Plugin installed',NULL,'',0,1390420867),(828,0,'datafield_date',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420867),(829,0,'datafield_date','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420868),(830,0,'datafield_date','2013110500','2013110500','Plugin installed',NULL,'',0,1390420868),(831,0,'datafield_file',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420868),(832,0,'datafield_file','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420868),(833,0,'datafield_file','2013110500','2013110500','Plugin installed',NULL,'',0,1390420869),(834,0,'datafield_latlong',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420869),(835,0,'datafield_latlong','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420869),(836,0,'datafield_latlong','2013110500','2013110500','Plugin installed',NULL,'',0,1390420869),(837,0,'datafield_menu',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420870),(838,0,'datafield_menu','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420870),(839,0,'datafield_menu','2013110500','2013110500','Plugin installed',NULL,'',0,1390420871),(840,0,'datafield_multimenu',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420871),(841,0,'datafield_multimenu','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420871),(842,0,'datafield_multimenu','2013110500','2013110500','Plugin installed',NULL,'',0,1390420871),(843,0,'datafield_number',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420872),(844,0,'datafield_number','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420872),(845,0,'datafield_number','2013110500','2013110500','Plugin installed',NULL,'',0,1390420873),(846,0,'datafield_picture',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420873),(847,0,'datafield_picture','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420874),(848,0,'datafield_picture','2013110500','2013110500','Plugin installed',NULL,'',0,1390420874),(849,0,'datafield_radiobutton',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420874),(850,0,'datafield_radiobutton','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420875),(851,0,'datafield_radiobutton','2013110500','2013110500','Plugin installed',NULL,'',0,1390420877),(852,0,'datafield_text',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420877),(853,0,'datafield_text','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420877),(854,0,'datafield_text','2013110500','2013110500','Plugin installed',NULL,'',0,1390420877),(855,0,'datafield_textarea',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420878),(856,0,'datafield_textarea','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420878),(857,0,'datafield_textarea','2013110500','2013110500','Plugin installed',NULL,'',0,1390420879),(858,0,'datafield_url',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420879),(859,0,'datafield_url','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420879),(860,0,'datafield_url','2013110500','2013110500','Plugin installed',NULL,'',0,1390420880),(861,0,'datapreset_imagegallery',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420880),(862,0,'datapreset_imagegallery','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420880),(863,0,'datapreset_imagegallery','2013110500','2013110500','Plugin installed',NULL,'',0,1390420880),(864,0,'quiz_grading',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420881),(865,0,'quiz_grading','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420881),(866,0,'quiz_grading','2013110500','2013110500','Plugin installed',NULL,'',0,1390420884),(867,0,'quiz_overview',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420884),(868,0,'quiz_overview','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420884),(869,0,'quiz_overview','2013110500','2013110500','Plugin installed',NULL,'',0,1390420885),(870,0,'quiz_responses',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420886),(871,0,'quiz_responses','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420886),(872,0,'quiz_responses','2013110500','2013110500','Plugin installed',NULL,'',0,1390420888),(873,0,'quiz_statistics',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420889),(874,0,'quiz_statistics','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420892),(875,0,'quiz_statistics','2013110500','2013110500','Plugin installed',NULL,'',0,1390420895),(876,0,'quizaccess_delaybetweenattempts',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420895),(877,0,'quizaccess_delaybetweenattempts','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420895),(878,0,'quizaccess_delaybetweenattempts','2013110500','2013110500','Plugin installed',NULL,'',0,1390420896),(879,0,'quizaccess_ipaddress',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420896),(880,0,'quizaccess_ipaddress','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420896),(881,0,'quizaccess_ipaddress','2013110500','2013110500','Plugin installed',NULL,'',0,1390420897),(882,0,'quizaccess_numattempts',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420897),(883,0,'quizaccess_numattempts','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420897),(884,0,'quizaccess_numattempts','2013110500','2013110500','Plugin installed',NULL,'',0,1390420898),(885,0,'quizaccess_openclosedate',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420898),(886,0,'quizaccess_openclosedate','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420898),(887,0,'quizaccess_openclosedate','2013110500','2013110500','Plugin installed',NULL,'',0,1390420898),(888,0,'quizaccess_password',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420898),(889,0,'quizaccess_password','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420899),(890,0,'quizaccess_password','2013110500','2013110500','Plugin installed',NULL,'',0,1390420899),(891,0,'quizaccess_safebrowser',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420900),(892,0,'quizaccess_safebrowser','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420900),(893,0,'quizaccess_safebrowser','2013110500','2013110500','Plugin installed',NULL,'',0,1390420901),(894,0,'quizaccess_securewindow',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420901),(895,0,'quizaccess_securewindow','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420902),(896,0,'quizaccess_securewindow','2013110500','2013110500','Plugin installed',NULL,'',0,1390420904),(897,0,'quizaccess_timelimit',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420904),(898,0,'quizaccess_timelimit','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420905),(899,0,'quizaccess_timelimit','2013110500','2013110500','Plugin installed',NULL,'',0,1390420905),(900,0,'scormreport_basic',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420905),(901,0,'scormreport_basic','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420906),(902,0,'scormreport_basic','2013110500','2013110500','Plugin installed',NULL,'',0,1390420906),(903,0,'scormreport_graphs',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420906),(904,0,'scormreport_graphs','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420906),(905,0,'scormreport_graphs','2013110500','2013110500','Plugin installed',NULL,'',0,1390420907),(906,0,'scormreport_interactions',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420907),(907,0,'scormreport_interactions','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420907),(908,0,'scormreport_interactions','2013110500','2013110500','Plugin installed',NULL,'',0,1390420908),(909,0,'scormreport_objectives',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420908),(910,0,'scormreport_objectives','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420908),(911,0,'scormreport_objectives','2013110500','2013110500','Plugin installed',NULL,'',0,1390420908),(912,0,'workshopform_accumulative',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420909),(913,0,'workshopform_accumulative','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420910),(914,0,'workshopform_accumulative','2013110500','2013110500','Plugin installed',NULL,'',0,1390420911),(915,0,'workshopform_comments',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420911),(916,0,'workshopform_comments','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420912),(917,0,'workshopform_comments','2013110500','2013110500','Plugin installed',NULL,'',0,1390420913),(918,0,'workshopform_numerrors',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420914),(919,0,'workshopform_numerrors','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420918),(920,0,'workshopform_numerrors','2013110500','2013110500','Plugin installed',NULL,'',0,1390420918),(921,0,'workshopform_rubric',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420918),(922,0,'workshopform_rubric','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420921),(923,0,'workshopform_rubric','2013110500','2013110500','Plugin installed',NULL,'',0,1390420922),(924,0,'workshopallocation_manual',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420922),(925,0,'workshopallocation_manual','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420922),(926,0,'workshopallocation_manual','2013110500','2013110500','Plugin installed',NULL,'',0,1390420923),(927,0,'workshopallocation_random',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420923),(928,0,'workshopallocation_random','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420923),(929,0,'workshopallocation_random','2013110500','2013110500','Plugin installed',NULL,'',0,1390420923),(930,0,'workshopallocation_scheduled',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420923),(931,0,'workshopallocation_scheduled','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420924),(932,0,'workshopallocation_scheduled','2013110500','2013110500','Plugin installed',NULL,'',0,1390420925),(933,0,'workshopeval_best',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420926),(934,0,'workshopeval_best','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420926),(935,0,'workshopeval_best','2013110500','2013110500','Plugin installed',NULL,'',0,1390420927),(936,0,'tinymce_ctrlhelp',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420928),(937,0,'tinymce_ctrlhelp','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420929),(938,0,'tinymce_ctrlhelp','2013110500','2013110500','Plugin installed',NULL,'',0,1390420930),(939,0,'tinymce_dragmath',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420930),(940,0,'tinymce_dragmath','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420930),(941,0,'tinymce_dragmath','2013110500','2013110500','Plugin installed',NULL,'',0,1390420931),(942,0,'tinymce_managefiles',NULL,'2014010800','Starting plugin installation',NULL,'',0,1390420931),(943,0,'tinymce_managefiles','2014010800','2014010800','Upgrade savepoint reached',NULL,'',0,1390420931),(944,0,'tinymce_managefiles','2014010800','2014010800','Plugin installed',NULL,'',0,1390420932),(945,0,'tinymce_moodleemoticon',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420932),(946,0,'tinymce_moodleemoticon','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420932),(947,0,'tinymce_moodleemoticon','2013110500','2013110500','Plugin installed',NULL,'',0,1390420933),(948,0,'tinymce_moodleimage',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420933),(949,0,'tinymce_moodleimage','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420933),(950,0,'tinymce_moodleimage','2013110500','2013110500','Plugin installed',NULL,'',0,1390420934),(951,0,'tinymce_moodlemedia',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420934),(952,0,'tinymce_moodlemedia','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420934),(953,0,'tinymce_moodlemedia','2013110500','2013110500','Plugin installed',NULL,'',0,1390420935),(954,0,'tinymce_moodlenolink',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420935),(955,0,'tinymce_moodlenolink','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420935),(956,0,'tinymce_moodlenolink','2013110500','2013110500','Plugin installed',NULL,'',0,1390420935),(957,0,'tinymce_pdw',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420936),(958,0,'tinymce_pdw','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420936),(959,0,'tinymce_pdw','2013110500','2013110500','Plugin installed',NULL,'',0,1390420936),(960,0,'tinymce_spellchecker',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420936),(961,0,'tinymce_spellchecker','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420937),(962,0,'tinymce_spellchecker','2013110500','2013110500','Plugin installed',NULL,'',0,1390420938),(963,0,'tinymce_wrap',NULL,'2013110500','Starting plugin installation',NULL,'',0,1390420938),(964,0,'tinymce_wrap','2013110500','2013110500','Upgrade savepoint reached',NULL,'',0,1390420938),(965,0,'tinymce_wrap','2013110500','2013110500','Plugin installed',NULL,'',0,1390420940),(966,0,'qformat_wordtable',NULL,'2014010201','Starting plugin installation',NULL,'',2,1390505187),(967,0,'qformat_wordtable','2014010201','2014010201','Upgrade savepoint reached',NULL,'',2,1390505187),(968,0,'qformat_wordtable','2014010201','2014010201','Plugin installed',NULL,'',2,1390505188);
/*!40000 ALTER TABLE `mdl_upgrade_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_url`
--

DROP TABLE IF EXISTS `mdl_url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_url` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `externalurl` longtext NOT NULL,
  `display` smallint(4) NOT NULL DEFAULT '0',
  `displayoptions` longtext,
  `parameters` longtext,
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_url_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='each record is one url resource';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_url`
--

LOCK TABLES `mdl_url` WRITE;
/*!40000 ALTER TABLE `mdl_url` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_url` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_user`
--

DROP TABLE IF EXISTS `mdl_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_user` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `auth` varchar(20) NOT NULL DEFAULT 'manual',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `policyagreed` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `mnethostid` bigint(10) NOT NULL DEFAULT '0',
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `idnumber` varchar(255) NOT NULL DEFAULT '',
  `firstname` varchar(100) NOT NULL DEFAULT '',
  `lastname` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `emailstop` tinyint(1) NOT NULL DEFAULT '0',
  `icq` varchar(15) NOT NULL DEFAULT '',
  `skype` varchar(50) NOT NULL DEFAULT '',
  `yahoo` varchar(50) NOT NULL DEFAULT '',
  `aim` varchar(50) NOT NULL DEFAULT '',
  `msn` varchar(50) NOT NULL DEFAULT '',
  `phone1` varchar(20) NOT NULL DEFAULT '',
  `phone2` varchar(20) NOT NULL DEFAULT '',
  `institution` varchar(255) NOT NULL DEFAULT '',
  `department` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(120) NOT NULL DEFAULT '',
  `country` varchar(2) NOT NULL DEFAULT '',
  `lang` varchar(30) NOT NULL DEFAULT 'en',
  `calendartype` varchar(30) NOT NULL DEFAULT 'gregorian',
  `theme` varchar(50) NOT NULL DEFAULT '',
  `timezone` varchar(100) NOT NULL DEFAULT '99',
  `firstaccess` bigint(10) NOT NULL DEFAULT '0',
  `lastaccess` bigint(10) NOT NULL DEFAULT '0',
  `lastlogin` bigint(10) NOT NULL DEFAULT '0',
  `currentlogin` bigint(10) NOT NULL DEFAULT '0',
  `lastip` varchar(45) NOT NULL DEFAULT '',
  `secret` varchar(15) NOT NULL DEFAULT '',
  `picture` bigint(10) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  `description` longtext,
  `descriptionformat` tinyint(2) NOT NULL DEFAULT '1',
  `mailformat` tinyint(1) NOT NULL DEFAULT '1',
  `maildigest` tinyint(1) NOT NULL DEFAULT '0',
  `maildisplay` tinyint(2) NOT NULL DEFAULT '2',
  `autosubscribe` tinyint(1) NOT NULL DEFAULT '1',
  `trackforums` tinyint(1) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `trustbitmask` bigint(10) NOT NULL DEFAULT '0',
  `imagealt` varchar(255) DEFAULT NULL,
  `lastnamephonetic` varchar(255) DEFAULT NULL,
  `firstnamephonetic` varchar(255) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `alternatename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_user_mneuse_uix` (`mnethostid`,`username`),
  KEY `mdl_user_del_ix` (`deleted`),
  KEY `mdl_user_con_ix` (`confirmed`),
  KEY `mdl_user_fir_ix` (`firstname`),
  KEY `mdl_user_las_ix` (`lastname`),
  KEY `mdl_user_cit_ix` (`city`),
  KEY `mdl_user_cou_ix` (`country`),
  KEY `mdl_user_las2_ix` (`lastaccess`),
  KEY `mdl_user_ema_ix` (`email`),
  KEY `mdl_user_aut_ix` (`auth`),
  KEY `mdl_user_idn_ix` (`idnumber`),
  KEY `mdl_user_fir2_ix` (`firstnamephonetic`),
  KEY `mdl_user_las3_ix` (`lastnamephonetic`),
  KEY `mdl_user_mid_ix` (`middlename`),
  KEY `mdl_user_alt_ix` (`alternatename`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='One record for each person';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_user`
--

LOCK TABLES `mdl_user` WRITE;
/*!40000 ALTER TABLE `mdl_user` DISABLE KEYS */;
INSERT INTO `mdl_user` VALUES (1,'manual',1,0,0,0,1,'guest','$2y$10$S9dwcztQ045NBbcrulflUOFqDiCGyHMk0gJO3UQmdrCYjrmgWCkEW','','Guest user',' ','root@localhost',0,'','','','','','','','','','','','','en','gregorian','','99',0,0,0,0,'','',0,'','This user is a special user that allows read-only access to some courses.',1,1,0,2,1,0,0,1390419889,0,NULL,NULL,NULL,NULL,NULL),(2,'manual',1,0,0,0,1,'admin','$2y$10$WGwkuL/rrU/JJARPLFIFvu7Fn.Wj/79mYJ4xBZKXIO6slIkHztlVa','','Admin','User','admin@example.com',0,'','','','','','','','','','','','','en','gregorian','','99',1390421786,1390925785,1390841746,1390925719,'127.0.0.1','',13,'','',1,1,0,1,1,0,0,1390925792,0,'','','','',''),(3,'manual',1,0,1,0,1,'testuser@moodle.org.1390487566','$2y$10$UKZBIFwiYXa0VTTwzDNYbOgizy3CowiNxuNjr/iVRADLsV27bAVm2','','testuser','testuser','5d9c68c6c50ed3d02a2fcf54f63993b6',0,'','','','','','','','','','','Szczecin','PL','en','gregorian','','99',0,0,0,0,'','',0,'',NULL,1,1,0,2,1,0,1390422944,1390487566,0,NULL,NULL,NULL,NULL,NULL),(5,'manual',1,0,0,0,1,'testuser','$2y$10$MQdO5RYnPnje2oNv9GgQOO6h1fvp8KZd9aqbr6y5EgU8HPrQ10wqu','','test','user','test@example.com',0,'','','','','','','','','','','Neverland','PL','en','gregorian','','99',0,0,0,0,'','',0,'',NULL,1,1,0,2,1,0,1390505783,1390505783,0,NULL,NULL,NULL,NULL,NULL),(6,'manual',1,0,0,0,1,'testteacher','$2y$10$08/lt87ErFs0UugD7zl.yuMI5pxTNy1jwgTrO3ar4kDXG/c2seDMa','','test','teacher','teacher@example.com',0,'','','','','','','','','','','Scienceville','PL','en','gregorian','','99',0,0,0,0,'','',0,'',NULL,1,1,0,2,1,0,1390505922,1390505922,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `mdl_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_user_devices`
--

DROP TABLE IF EXISTS `mdl_user_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_user_devices` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `appid` varchar(128) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `model` varchar(32) NOT NULL DEFAULT '',
  `platform` varchar(32) NOT NULL DEFAULT '',
  `version` varchar(32) NOT NULL DEFAULT '',
  `pushid` varchar(255) NOT NULL DEFAULT '',
  `uuid` varchar(255) NOT NULL DEFAULT '',
  `timecreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_userdevi_pususe_uix` (`pushid`,`userid`),
  UNIQUE KEY `mdl_userdevi_puspla_uix` (`pushid`,`platform`),
  KEY `mdl_userdevi_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table stores user''s mobile devices information in order';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_user_devices`
--

LOCK TABLES `mdl_user_devices` WRITE;
/*!40000 ALTER TABLE `mdl_user_devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_user_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_user_enrolments`
--

DROP TABLE IF EXISTS `mdl_user_enrolments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_user_enrolments` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `status` bigint(10) NOT NULL DEFAULT '0',
  `enrolid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `timestart` bigint(10) NOT NULL DEFAULT '0',
  `timeend` bigint(10) NOT NULL DEFAULT '2147483647',
  `modifierid` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_userenro_enruse_uix` (`enrolid`,`userid`),
  KEY `mdl_userenro_enr_ix` (`enrolid`),
  KEY `mdl_userenro_use_ix` (`userid`),
  KEY `mdl_userenro_mod_ix` (`modifierid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users participating in courses (aka enrolled users) - everyb';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_user_enrolments`
--

LOCK TABLES `mdl_user_enrolments` WRITE;
/*!40000 ALTER TABLE `mdl_user_enrolments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_user_enrolments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_user_info_category`
--

DROP TABLE IF EXISTS `mdl_user_info_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_user_info_category` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customisable fields categories';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_user_info_category`
--

LOCK TABLES `mdl_user_info_category` WRITE;
/*!40000 ALTER TABLE `mdl_user_info_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_user_info_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_user_info_data`
--

DROP TABLE IF EXISTS `mdl_user_info_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_user_info_data` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `fieldid` bigint(10) NOT NULL DEFAULT '0',
  `data` longtext NOT NULL,
  `dataformat` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_userinfodata_usefie_ix` (`userid`,`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Data for the customisable user fields';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_user_info_data`
--

LOCK TABLES `mdl_user_info_data` WRITE;
/*!40000 ALTER TABLE `mdl_user_info_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_user_info_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_user_info_field`
--

DROP TABLE IF EXISTS `mdl_user_info_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_user_info_field` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `shortname` varchar(255) NOT NULL DEFAULT 'shortname',
  `name` longtext NOT NULL,
  `datatype` varchar(255) NOT NULL DEFAULT '',
  `description` longtext,
  `descriptionformat` tinyint(2) NOT NULL DEFAULT '0',
  `categoryid` bigint(10) NOT NULL DEFAULT '0',
  `sortorder` bigint(10) NOT NULL DEFAULT '0',
  `required` tinyint(2) NOT NULL DEFAULT '0',
  `locked` tinyint(2) NOT NULL DEFAULT '0',
  `visible` smallint(4) NOT NULL DEFAULT '0',
  `forceunique` tinyint(2) NOT NULL DEFAULT '0',
  `signup` tinyint(2) NOT NULL DEFAULT '0',
  `defaultdata` longtext,
  `defaultdataformat` tinyint(2) NOT NULL DEFAULT '0',
  `param1` longtext,
  `param2` longtext,
  `param3` longtext,
  `param4` longtext,
  `param5` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customisable user profile fields';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_user_info_field`
--

LOCK TABLES `mdl_user_info_field` WRITE;
/*!40000 ALTER TABLE `mdl_user_info_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_user_info_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_user_lastaccess`
--

DROP TABLE IF EXISTS `mdl_user_lastaccess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_user_lastaccess` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `courseid` bigint(10) NOT NULL DEFAULT '0',
  `timeaccess` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_userlast_usecou_uix` (`userid`,`courseid`),
  KEY `mdl_userlast_use_ix` (`userid`),
  KEY `mdl_userlast_cou_ix` (`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='To keep track of course page access times, used in online pa';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_user_lastaccess`
--

LOCK TABLES `mdl_user_lastaccess` WRITE;
/*!40000 ALTER TABLE `mdl_user_lastaccess` DISABLE KEYS */;
INSERT INTO `mdl_user_lastaccess` VALUES (1,2,2,1390925785);
/*!40000 ALTER TABLE `mdl_user_lastaccess` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_user_password_resets`
--

DROP TABLE IF EXISTS `mdl_user_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_user_password_resets` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL,
  `timerequested` bigint(10) NOT NULL,
  `timererequested` bigint(10) NOT NULL DEFAULT '0',
  `token` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mdl_userpassrese_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table tracking password reset confirmation tokens';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_user_password_resets`
--

LOCK TABLES `mdl_user_password_resets` WRITE;
/*!40000 ALTER TABLE `mdl_user_password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_user_password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_user_preferences`
--

DROP TABLE IF EXISTS `mdl_user_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_user_preferences` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(1333) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_userpref_usenam_uix` (`userid`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Allows modules to store arbitrary user preferences';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_user_preferences`
--

LOCK TABLES `mdl_user_preferences` WRITE;
/*!40000 ALTER TABLE `mdl_user_preferences` DISABLE KEYS */;
INSERT INTO `mdl_user_preferences` VALUES (1,2,'htmleditor',''),(2,2,'email_bounce_count','0'),(3,2,'email_send_count','0'),(4,2,'filepicker_recentrepository','4'),(5,2,'filepicker_recentlicense','public');
/*!40000 ALTER TABLE `mdl_user_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_user_private_key`
--

DROP TABLE IF EXISTS `mdl_user_private_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_user_private_key` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `script` varchar(128) NOT NULL DEFAULT '',
  `value` varchar(128) NOT NULL DEFAULT '',
  `userid` bigint(10) NOT NULL,
  `instance` bigint(10) DEFAULT NULL,
  `iprestriction` varchar(255) DEFAULT NULL,
  `validuntil` bigint(10) DEFAULT NULL,
  `timecreated` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_userprivkey_scrval_ix` (`script`,`value`),
  KEY `mdl_userprivkey_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='access keys used in cookieless scripts - rss, etc.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_user_private_key`
--

LOCK TABLES `mdl_user_private_key` WRITE;
/*!40000 ALTER TABLE `mdl_user_private_key` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_user_private_key` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_webdav_locks`
--

DROP TABLE IF EXISTS `mdl_webdav_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_webdav_locks` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `expiry` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `recursive` tinyint(1) NOT NULL DEFAULT '0',
  `exclusivelock` tinyint(1) NOT NULL DEFAULT '0',
  `created` bigint(10) NOT NULL DEFAULT '0',
  `modified` bigint(10) NOT NULL DEFAULT '0',
  `owner` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_webdlock_tok_uix` (`token`),
  KEY `mdl_webdlock_pat_ix` (`path`),
  KEY `mdl_webdlock_exp_ix` (`expiry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Resource locks for WebDAV users';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_webdav_locks`
--

LOCK TABLES `mdl_webdav_locks` WRITE;
/*!40000 ALTER TABLE `mdl_webdav_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_webdav_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_wiki`
--

DROP TABLE IF EXISTS `mdl_wiki`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_wiki` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT 'Wiki',
  `intro` longtext,
  `introformat` smallint(4) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `firstpagetitle` varchar(255) NOT NULL DEFAULT 'First Page',
  `wikimode` varchar(20) NOT NULL DEFAULT 'collaborative',
  `defaultformat` varchar(20) NOT NULL DEFAULT 'creole',
  `forceformat` tinyint(1) NOT NULL DEFAULT '1',
  `editbegin` bigint(10) NOT NULL DEFAULT '0',
  `editend` bigint(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_wiki_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores Wiki activity configuration';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_wiki`
--

LOCK TABLES `mdl_wiki` WRITE;
/*!40000 ALTER TABLE `mdl_wiki` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_wiki` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_wiki_links`
--

DROP TABLE IF EXISTS `mdl_wiki_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_wiki_links` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `subwikiid` bigint(10) NOT NULL DEFAULT '0',
  `frompageid` bigint(10) NOT NULL DEFAULT '0',
  `topageid` bigint(10) NOT NULL DEFAULT '0',
  `tomissingpage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_wikilink_fro_ix` (`frompageid`),
  KEY `mdl_wikilink_sub_ix` (`subwikiid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Page wiki links';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_wiki_links`
--

LOCK TABLES `mdl_wiki_links` WRITE;
/*!40000 ALTER TABLE `mdl_wiki_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_wiki_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_wiki_locks`
--

DROP TABLE IF EXISTS `mdl_wiki_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_wiki_locks` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `pageid` bigint(10) NOT NULL DEFAULT '0',
  `sectionname` varchar(255) DEFAULT NULL,
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `lockedat` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Manages page locks';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_wiki_locks`
--

LOCK TABLES `mdl_wiki_locks` WRITE;
/*!40000 ALTER TABLE `mdl_wiki_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_wiki_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_wiki_pages`
--

DROP TABLE IF EXISTS `mdl_wiki_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_wiki_pages` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `subwikiid` bigint(10) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT 'title',
  `cachedcontent` longtext NOT NULL,
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `timerendered` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `pageviews` bigint(10) NOT NULL DEFAULT '0',
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_wikipage_subtituse_uix` (`subwikiid`,`title`,`userid`),
  KEY `mdl_wikipage_sub_ix` (`subwikiid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores wiki pages';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_wiki_pages`
--

LOCK TABLES `mdl_wiki_pages` WRITE;
/*!40000 ALTER TABLE `mdl_wiki_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_wiki_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_wiki_subwikis`
--

DROP TABLE IF EXISTS `mdl_wiki_subwikis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_wiki_subwikis` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `wikiid` bigint(10) NOT NULL DEFAULT '0',
  `groupid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_wikisubw_wikgrouse_uix` (`wikiid`,`groupid`,`userid`),
  KEY `mdl_wikisubw_wik_ix` (`wikiid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores subwiki instances';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_wiki_subwikis`
--

LOCK TABLES `mdl_wiki_subwikis` WRITE;
/*!40000 ALTER TABLE `mdl_wiki_subwikis` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_wiki_subwikis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_wiki_synonyms`
--

DROP TABLE IF EXISTS `mdl_wiki_synonyms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_wiki_synonyms` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `subwikiid` bigint(10) NOT NULL DEFAULT '0',
  `pageid` bigint(10) NOT NULL DEFAULT '0',
  `pagesynonym` varchar(255) NOT NULL DEFAULT 'Pagesynonym',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_wikisyno_pagpag_uix` (`pageid`,`pagesynonym`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores wiki pages synonyms';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_wiki_synonyms`
--

LOCK TABLES `mdl_wiki_synonyms` WRITE;
/*!40000 ALTER TABLE `mdl_wiki_synonyms` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_wiki_synonyms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_wiki_versions`
--

DROP TABLE IF EXISTS `mdl_wiki_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_wiki_versions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `pageid` bigint(10) NOT NULL DEFAULT '0',
  `content` longtext NOT NULL,
  `contentformat` varchar(20) NOT NULL DEFAULT 'creole',
  `version` mediumint(5) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_wikivers_pag_ix` (`pageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores wiki page history';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_wiki_versions`
--

LOCK TABLES `mdl_wiki_versions` WRITE;
/*!40000 ALTER TABLE `mdl_wiki_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_wiki_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop`
--

DROP TABLE IF EXISTS `mdl_workshop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `intro` longtext,
  `introformat` smallint(3) NOT NULL DEFAULT '0',
  `instructauthors` longtext,
  `instructauthorsformat` smallint(3) NOT NULL DEFAULT '0',
  `instructreviewers` longtext,
  `instructreviewersformat` smallint(3) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL,
  `phase` smallint(3) DEFAULT '0',
  `useexamples` tinyint(2) DEFAULT '0',
  `usepeerassessment` tinyint(2) DEFAULT '0',
  `useselfassessment` tinyint(2) DEFAULT '0',
  `grade` decimal(10,5) DEFAULT '80.00000',
  `gradinggrade` decimal(10,5) DEFAULT '20.00000',
  `strategy` varchar(30) NOT NULL DEFAULT '',
  `evaluation` varchar(30) NOT NULL DEFAULT '',
  `gradedecimals` smallint(3) DEFAULT '0',
  `nattachments` smallint(3) DEFAULT '0',
  `latesubmissions` tinyint(2) DEFAULT '0',
  `maxbytes` bigint(10) DEFAULT '100000',
  `examplesmode` smallint(3) DEFAULT '0',
  `submissionstart` bigint(10) DEFAULT '0',
  `submissionend` bigint(10) DEFAULT '0',
  `assessmentstart` bigint(10) DEFAULT '0',
  `assessmentend` bigint(10) DEFAULT '0',
  `phaseswitchassessment` tinyint(2) NOT NULL DEFAULT '0',
  `conclusion` longtext,
  `conclusionformat` smallint(3) NOT NULL DEFAULT '1',
  `overallfeedbackmode` smallint(3) DEFAULT '1',
  `overallfeedbackfiles` smallint(3) DEFAULT '0',
  `overallfeedbackmaxbytes` bigint(10) DEFAULT '100000',
  PRIMARY KEY (`id`),
  KEY `mdl_work_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table keeps information about the module instances and ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop`
--

LOCK TABLES `mdl_workshop` WRITE;
/*!40000 ALTER TABLE `mdl_workshop` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_aggregations`
--

DROP TABLE IF EXISTS `mdl_workshop_aggregations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_aggregations` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `gradinggrade` decimal(10,5) DEFAULT NULL,
  `timegraded` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_workaggr_woruse_uix` (`workshopid`,`userid`),
  KEY `mdl_workaggr_wor_ix` (`workshopid`),
  KEY `mdl_workaggr_use_ix` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Aggregated grades for assessment are stored here. The aggreg';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_aggregations`
--

LOCK TABLES `mdl_workshop_aggregations` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_aggregations` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_aggregations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_assessments`
--

DROP TABLE IF EXISTS `mdl_workshop_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_assessments` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `submissionid` bigint(10) NOT NULL,
  `reviewerid` bigint(10) NOT NULL,
  `weight` bigint(10) NOT NULL DEFAULT '1',
  `timecreated` bigint(10) DEFAULT '0',
  `timemodified` bigint(10) DEFAULT '0',
  `grade` decimal(10,5) DEFAULT NULL,
  `gradinggrade` decimal(10,5) DEFAULT NULL,
  `gradinggradeover` decimal(10,5) DEFAULT NULL,
  `gradinggradeoverby` bigint(10) DEFAULT NULL,
  `feedbackauthor` longtext,
  `feedbackauthorformat` smallint(3) DEFAULT '0',
  `feedbackauthorattachment` smallint(3) DEFAULT '0',
  `feedbackreviewer` longtext,
  `feedbackreviewerformat` smallint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_workasse_sub_ix` (`submissionid`),
  KEY `mdl_workasse_gra_ix` (`gradinggradeoverby`),
  KEY `mdl_workasse_rev_ix` (`reviewerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Info about the made assessment and automatically calculated ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_assessments`
--

LOCK TABLES `mdl_workshop_assessments` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_assessments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_assessments_old`
--

DROP TABLE IF EXISTS `mdl_workshop_assessments_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_assessments_old` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL DEFAULT '0',
  `submissionid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timegraded` bigint(10) NOT NULL DEFAULT '0',
  `timeagreed` bigint(10) NOT NULL DEFAULT '0',
  `grade` double NOT NULL DEFAULT '0',
  `gradinggrade` smallint(3) NOT NULL DEFAULT '0',
  `teachergraded` smallint(3) NOT NULL DEFAULT '0',
  `mailed` smallint(3) NOT NULL DEFAULT '0',
  `resubmission` smallint(3) NOT NULL DEFAULT '0',
  `donotuse` smallint(3) NOT NULL DEFAULT '0',
  `generalcomment` longtext,
  `teachercomment` longtext,
  `newplugin` varchar(28) DEFAULT NULL,
  `newid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_workasseold_use_ix` (`userid`),
  KEY `mdl_workasseold_mai_ix` (`mailed`),
  KEY `mdl_workasseold_wor_ix` (`workshopid`),
  KEY `mdl_workasseold_sub_ix` (`submissionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Legacy workshop_assessments table to be dropped later in Moo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_assessments_old`
--

LOCK TABLES `mdl_workshop_assessments_old` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_assessments_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_assessments_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_comments_old`
--

DROP TABLE IF EXISTS `mdl_workshop_comments_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_comments_old` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL DEFAULT '0',
  `assessmentid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `mailed` tinyint(2) NOT NULL DEFAULT '0',
  `comments` longtext NOT NULL,
  `newplugin` varchar(28) DEFAULT NULL,
  `newid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_workcommold_use_ix` (`userid`),
  KEY `mdl_workcommold_mai_ix` (`mailed`),
  KEY `mdl_workcommold_wor_ix` (`workshopid`),
  KEY `mdl_workcommold_ass_ix` (`assessmentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Legacy workshop_comments table to be dropped later in Moodle';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_comments_old`
--

LOCK TABLES `mdl_workshop_comments_old` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_comments_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_comments_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_elements_old`
--

DROP TABLE IF EXISTS `mdl_workshop_elements_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_elements_old` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL DEFAULT '0',
  `elementno` smallint(3) NOT NULL DEFAULT '0',
  `description` longtext NOT NULL,
  `scale` smallint(3) NOT NULL DEFAULT '0',
  `maxscore` smallint(3) NOT NULL DEFAULT '1',
  `weight` smallint(3) NOT NULL DEFAULT '11',
  `stddev` double NOT NULL DEFAULT '0',
  `totalassessments` bigint(10) NOT NULL DEFAULT '0',
  `newplugin` varchar(28) DEFAULT NULL,
  `newid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_workelemold_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Legacy workshop_elements table to be dropped later in Moodle';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_elements_old`
--

LOCK TABLES `mdl_workshop_elements_old` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_elements_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_elements_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_grades`
--

DROP TABLE IF EXISTS `mdl_workshop_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_grades` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assessmentid` bigint(10) NOT NULL,
  `strategy` varchar(30) NOT NULL DEFAULT '',
  `dimensionid` bigint(10) NOT NULL,
  `grade` decimal(10,5) NOT NULL,
  `peercomment` longtext,
  `peercommentformat` smallint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_workgrad_assstrdim_uix` (`assessmentid`,`strategy`,`dimensionid`),
  KEY `mdl_workgrad_ass_ix` (`assessmentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='How the reviewers filled-up the grading forms, given grades ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_grades`
--

LOCK TABLES `mdl_workshop_grades` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_grades_old`
--

DROP TABLE IF EXISTS `mdl_workshop_grades_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_grades_old` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL DEFAULT '0',
  `assessmentid` bigint(10) NOT NULL DEFAULT '0',
  `elementno` bigint(10) NOT NULL DEFAULT '0',
  `feedback` longtext NOT NULL,
  `grade` smallint(3) NOT NULL DEFAULT '0',
  `newplugin` varchar(28) DEFAULT NULL,
  `newid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_workgradold_wor_ix` (`workshopid`),
  KEY `mdl_workgradold_ass_ix` (`assessmentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Legacy workshop_grades table to be dropped later in Moodle 2';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_grades_old`
--

LOCK TABLES `mdl_workshop_grades_old` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_grades_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_grades_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_old`
--

DROP TABLE IF EXISTS `mdl_workshop_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_old` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course` bigint(10) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `wtype` smallint(3) NOT NULL DEFAULT '0',
  `nelements` smallint(3) NOT NULL DEFAULT '1',
  `nattachments` smallint(3) NOT NULL DEFAULT '0',
  `phase` tinyint(2) NOT NULL DEFAULT '0',
  `format` tinyint(2) NOT NULL DEFAULT '0',
  `gradingstrategy` tinyint(2) NOT NULL DEFAULT '1',
  `resubmit` tinyint(2) NOT NULL DEFAULT '0',
  `agreeassessments` tinyint(2) NOT NULL DEFAULT '0',
  `hidegrades` tinyint(2) NOT NULL DEFAULT '0',
  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
  `includeself` tinyint(2) NOT NULL DEFAULT '0',
  `maxbytes` bigint(10) NOT NULL DEFAULT '100000',
  `submissionstart` bigint(10) NOT NULL DEFAULT '0',
  `assessmentstart` bigint(10) NOT NULL DEFAULT '0',
  `submissionend` bigint(10) NOT NULL DEFAULT '0',
  `assessmentend` bigint(10) NOT NULL DEFAULT '0',
  `releasegrades` bigint(10) NOT NULL DEFAULT '0',
  `grade` smallint(3) NOT NULL DEFAULT '0',
  `gradinggrade` smallint(3) NOT NULL DEFAULT '0',
  `ntassessments` smallint(3) NOT NULL DEFAULT '0',
  `assessmentcomps` smallint(3) NOT NULL DEFAULT '2',
  `nsassessments` smallint(3) NOT NULL DEFAULT '0',
  `overallocation` smallint(3) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `teacherweight` smallint(3) NOT NULL DEFAULT '1',
  `showleaguetable` smallint(3) NOT NULL DEFAULT '0',
  `usepassword` smallint(3) NOT NULL DEFAULT '0',
  `password` varchar(32) NOT NULL DEFAULT '',
  `newplugin` varchar(28) DEFAULT NULL,
  `newid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_workold_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Legacy workshop table to be dropped later in Moodle 2.x';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_old`
--

LOCK TABLES `mdl_workshop_old` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_rubrics_old`
--

DROP TABLE IF EXISTS `mdl_workshop_rubrics_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_rubrics_old` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL DEFAULT '0',
  `elementno` bigint(10) NOT NULL DEFAULT '0',
  `rubricno` smallint(3) NOT NULL DEFAULT '0',
  `description` longtext NOT NULL,
  `newplugin` varchar(28) DEFAULT NULL,
  `newid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_workrubrold_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Legacy workshop_rubrics table to be dropped later in Moodle ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_rubrics_old`
--

LOCK TABLES `mdl_workshop_rubrics_old` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_rubrics_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_rubrics_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_stockcomments_old`
--

DROP TABLE IF EXISTS `mdl_workshop_stockcomments_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_stockcomments_old` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL DEFAULT '0',
  `elementno` bigint(10) NOT NULL DEFAULT '0',
  `comments` longtext NOT NULL,
  `newplugin` varchar(28) DEFAULT NULL,
  `newid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_workstocold_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Legacy workshop_stockcomments table to be dropped later in M';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_stockcomments_old`
--

LOCK TABLES `mdl_workshop_stockcomments_old` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_stockcomments_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_stockcomments_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_submissions`
--

DROP TABLE IF EXISTS `mdl_workshop_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_submissions` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL,
  `example` tinyint(2) DEFAULT '0',
  `authorid` bigint(10) NOT NULL,
  `timecreated` bigint(10) NOT NULL,
  `timemodified` bigint(10) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext,
  `contentformat` smallint(3) NOT NULL DEFAULT '0',
  `contenttrust` smallint(3) NOT NULL DEFAULT '0',
  `attachment` tinyint(2) DEFAULT '0',
  `grade` decimal(10,5) DEFAULT NULL,
  `gradeover` decimal(10,5) DEFAULT NULL,
  `gradeoverby` bigint(10) DEFAULT NULL,
  `feedbackauthor` longtext,
  `feedbackauthorformat` smallint(3) DEFAULT '0',
  `timegraded` bigint(10) DEFAULT NULL,
  `published` tinyint(2) DEFAULT '0',
  `late` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_worksubm_wor_ix` (`workshopid`),
  KEY `mdl_worksubm_gra_ix` (`gradeoverby`),
  KEY `mdl_worksubm_aut_ix` (`authorid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Info about the submission and the aggregation of the grade f';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_submissions`
--

LOCK TABLES `mdl_workshop_submissions` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshop_submissions_old`
--

DROP TABLE IF EXISTS `mdl_workshop_submissions_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshop_submissions_old` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL DEFAULT '0',
  `userid` bigint(10) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `mailed` tinyint(2) NOT NULL DEFAULT '0',
  `description` longtext NOT NULL,
  `gradinggrade` smallint(3) NOT NULL DEFAULT '0',
  `finalgrade` smallint(3) NOT NULL DEFAULT '0',
  `late` smallint(3) NOT NULL DEFAULT '0',
  `nassessments` bigint(10) NOT NULL DEFAULT '0',
  `newplugin` varchar(28) DEFAULT NULL,
  `newid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mdl_worksubmold_use_ix` (`userid`),
  KEY `mdl_worksubmold_mai_ix` (`mailed`),
  KEY `mdl_worksubmold_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Legacy workshop_submissions table to be dropped later in Moo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshop_submissions_old`
--

LOCK TABLES `mdl_workshop_submissions_old` WRITE;
/*!40000 ALTER TABLE `mdl_workshop_submissions_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshop_submissions_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshopallocation_scheduled`
--

DROP TABLE IF EXISTS `mdl_workshopallocation_scheduled`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshopallocation_scheduled` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL,
  `enabled` tinyint(2) NOT NULL DEFAULT '0',
  `submissionend` bigint(10) NOT NULL,
  `timeallocated` bigint(10) DEFAULT NULL,
  `settings` longtext,
  `resultstatus` bigint(10) DEFAULT NULL,
  `resultmessage` varchar(1333) DEFAULT NULL,
  `resultlog` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_worksche_wor_uix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the allocation settings for the scheduled allocator';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshopallocation_scheduled`
--

LOCK TABLES `mdl_workshopallocation_scheduled` WRITE;
/*!40000 ALTER TABLE `mdl_workshopallocation_scheduled` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshopallocation_scheduled` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshopeval_best_settings`
--

DROP TABLE IF EXISTS `mdl_workshopeval_best_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshopeval_best_settings` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL,
  `comparison` smallint(3) DEFAULT '5',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_workbestsett_wor_uix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Settings for the grading evaluation subplugin Comparison wit';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshopeval_best_settings`
--

LOCK TABLES `mdl_workshopeval_best_settings` WRITE;
/*!40000 ALTER TABLE `mdl_workshopeval_best_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshopeval_best_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshopform_accumulative`
--

DROP TABLE IF EXISTS `mdl_workshopform_accumulative`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshopform_accumulative` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL,
  `sort` bigint(10) DEFAULT '0',
  `description` longtext,
  `descriptionformat` smallint(3) DEFAULT '0',
  `grade` bigint(10) NOT NULL,
  `weight` mediumint(5) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_workaccu_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The assessment dimensions definitions of Accumulative gradin';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshopform_accumulative`
--

LOCK TABLES `mdl_workshopform_accumulative` WRITE;
/*!40000 ALTER TABLE `mdl_workshopform_accumulative` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshopform_accumulative` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshopform_comments`
--

DROP TABLE IF EXISTS `mdl_workshopform_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshopform_comments` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL,
  `sort` bigint(10) DEFAULT '0',
  `description` longtext,
  `descriptionformat` smallint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_workcomm_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The assessment dimensions definitions of Comments strategy f';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshopform_comments`
--

LOCK TABLES `mdl_workshopform_comments` WRITE;
/*!40000 ALTER TABLE `mdl_workshopform_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshopform_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshopform_numerrors`
--

DROP TABLE IF EXISTS `mdl_workshopform_numerrors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshopform_numerrors` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL,
  `sort` bigint(10) DEFAULT '0',
  `description` longtext,
  `descriptionformat` smallint(3) DEFAULT '0',
  `descriptiontrust` bigint(10) DEFAULT NULL,
  `grade0` varchar(50) DEFAULT NULL,
  `grade1` varchar(50) DEFAULT NULL,
  `weight` mediumint(5) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `mdl_worknume_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The assessment dimensions definitions of Number of errors gr';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshopform_numerrors`
--

LOCK TABLES `mdl_workshopform_numerrors` WRITE;
/*!40000 ALTER TABLE `mdl_workshopform_numerrors` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshopform_numerrors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshopform_numerrors_map`
--

DROP TABLE IF EXISTS `mdl_workshopform_numerrors_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshopform_numerrors_map` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL,
  `nonegative` bigint(10) NOT NULL,
  `grade` decimal(10,5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_worknumemap_wornon_uix` (`workshopid`,`nonegative`),
  KEY `mdl_worknumemap_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This maps the number of errors to a percentual grade for sub';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshopform_numerrors_map`
--

LOCK TABLES `mdl_workshopform_numerrors_map` WRITE;
/*!40000 ALTER TABLE `mdl_workshopform_numerrors_map` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshopform_numerrors_map` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshopform_rubric`
--

DROP TABLE IF EXISTS `mdl_workshopform_rubric`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshopform_rubric` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL,
  `sort` bigint(10) DEFAULT '0',
  `description` longtext,
  `descriptionformat` smallint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_workrubr_wor_ix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The assessment dimensions definitions of Rubric grading stra';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshopform_rubric`
--

LOCK TABLES `mdl_workshopform_rubric` WRITE;
/*!40000 ALTER TABLE `mdl_workshopform_rubric` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshopform_rubric` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshopform_rubric_config`
--

DROP TABLE IF EXISTS `mdl_workshopform_rubric_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshopform_rubric_config` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `workshopid` bigint(10) NOT NULL,
  `layout` varchar(30) DEFAULT 'list',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_workrubrconf_wor_uix` (`workshopid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Configuration table for the Rubric grading strategy';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshopform_rubric_config`
--

LOCK TABLES `mdl_workshopform_rubric_config` WRITE;
/*!40000 ALTER TABLE `mdl_workshopform_rubric_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshopform_rubric_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mdl_workshopform_rubric_levels`
--

DROP TABLE IF EXISTS `mdl_workshopform_rubric_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mdl_workshopform_rubric_levels` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `dimensionid` bigint(10) NOT NULL,
  `grade` decimal(10,5) NOT NULL,
  `definition` longtext,
  `definitionformat` smallint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_workrubrleve_dim_ix` (`dimensionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The definition of rubric rating scales';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mdl_workshopform_rubric_levels`
--

LOCK TABLES `mdl_workshopform_rubric_levels` WRITE;
/*!40000 ALTER TABLE `mdl_workshopform_rubric_levels` DISABLE KEYS */;
/*!40000 ALTER TABLE `mdl_workshopform_rubric_levels` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-28 17:19:09
