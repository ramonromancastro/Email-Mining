CREATE DATABASE IF NOT EXISTS `emailmining` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `emailmining`;

DROP TABLE IF EXISTS `emails`;
CREATE TABLE `emails` (
  `id_emails` bigint(20) NOT NULL,
  `source` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `service` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `error` tinyint(4) NOT NULL DEFAULT '0',
  `source_uid` bigint(20) NOT NULL,
  `service_uid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL DEFAULT '0',
  `acknowledge` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS `text_uid`;
CREATE TABLE `text_uid` (
  `id_text_uid` bigint(20) NOT NULL,
  `text` varchar(255) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


ALTER TABLE `emails`
  ADD PRIMARY KEY (`id_emails`),
  ADD UNIQUE KEY `UID` (`uid`),
  ADD KEY `SOURCE` (`source`),
  ADD KEY `SERVICE` (`service`),
  ADD KEY `STATUS` (`status`),
  ADD KEY `ERROR` (`error`),
  ADD KEY `SOURCE_UID` (`source_uid`),
  ADD KEY `SERVICE_UID` (`service_uid`),
  ADD KEY `ACKNOWLEDGE` (`acknowledge`);

ALTER TABLE `text_uid`
  ADD PRIMARY KEY (`id_text_uid`),
  ADD UNIQUE KEY `id_text_uid_UNIQUE` (`id_text_uid`),
  ADD UNIQUE KEY `text_UNIQUE` (`text`),
  ADD KEY `TEXT` (`text`);


ALTER TABLE `emails`
  MODIFY `id_emails` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `text_uid`
  MODIFY `id_text_uid` bigint(20) NOT NULL AUTO_INCREMENT;