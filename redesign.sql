-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 01 月 14 日 10:53
-- 服务器版本: 5.5.20
-- PHP 版本: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `redesign`
--

-- --------------------------------------------------------

--
-- 表的结构 `rs_relation`
--

CREATE TABLE IF NOT EXISTS `rs_relation` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `website_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `role_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `rs_relation`
--

INSERT INTO `rs_relation` (`id`, `website_id`, `user_id`, `role_id`) VALUES
(1, 1, 1, 1),
(2, 1, 3, 5),
(3, 1, 4, 4);

-- --------------------------------------------------------

--
-- 表的结构 `rs_role`
--

CREATE TABLE IF NOT EXISTS `rs_role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) NOT NULL,
  `purview` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- 转存表中的数据 `rs_role`
--

INSERT INTO `rs_role` (`id`, `role_name`, `purview`) VALUES
(1, 'all', -1),
(2, 'create', 8),
(3, 'retrieve', 4),
(4, 'retrieve', 4),
(5, 'update', 2),
(6, 'delete', 1),
(7, 'x', 0),
(8, 'test', 0),
(9, 'test1', 8),
(10, 'e', 0);

-- --------------------------------------------------------

--
-- 表的结构 `rs_translation`
--

CREATE TABLE IF NOT EXISTS `rs_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `en` text NOT NULL,
  `de` text NOT NULL,
  `nl` text NOT NULL,
  `fr` text NOT NULL,
  `remarks` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `website_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6070 ;

--
-- 转存表中的数据 `rs_translation`
--

INSERT INTO `rs_translation` (`id`, `en`, `de`, `nl`, `fr`, `remarks`, `status`, `website_id`) VALUES
(6042, '%s Items', '%s Artikel', '%s Artikel(en)', '', '', 1, 1),
(6043, '(Shift-)Click or drag to change value', '(Umschalt-)Klick oder ziehen, um Wert zu ?ndern', '(Shift-)Klik of sleep om de waarde te veranderen', '', '', 1, 1),
(6044, '* Required Fields', '* Pflichtfelder', '* Verplichte velden', '', '', 1, 1),
(6045, '*Pflichtfelder', '*Pflichtfelder', '*Verplichte velden', '', '', 1, 1),
(6046, '#NAME?', ' =- Klicken Sie auf einen der Zeitwerte, um den Wert zu erh?hen', ' =- Klik op een van de tijdgedeeltes om deze te verhogen', '', '', 1, 1),
(6047, '#NAME?', ' =- Halten Sie einen der Buttons gedr', ' =- Houd de muisknop op een van de bovenstaande knoppen om sneller te selecteren', '', '', 1, 1),
(6048, '- No Cities -', ' - keine St?dte -', '- Geen Steden -', '', '', 1, 1),
(6049, '- No Store Details -', ' - keine Filial-Details -', '- Geen Winkel Details -', '', '', 1, 1),
(6050, '- Use the %s buttons to select month', ' - Nutzen Sie die %s Buttons, um einen Monat auszuw?hlen', '- Gebruik de %s knoppen om de maand te selecteren', '', '', 1, 1),
(6051, '%s Items', '%s Artikel', '%s Artikel(en)', '', '', 1, 1),
(6052, '(Shift-)Click or drag to change value', '(Umschalt-)Klick oder ziehen, um Wert zu ?ndern', '(Shift-)Klik of sleep om de waarde te veranderen', '', '', 1, 1),
(6053, '* Required Fields', '* Pflichtfelder', '* Verplichte velden', '', '', 1, 1),
(6054, '*Pflichtfelder', '*Pflichtfelder', '*Verplichte velden', '', '', 1, 1),
(6055, '#NAME?', ' =- Klicken Sie auf einen der Zeitwerte, um den Wert zu erh?hen', ' =- Klik op een van de tijdgedeeltes om deze te verhogen', '', '', 1, 1),
(6056, '#NAME?', ' =- Halten Sie einen der Buttons gedr', ' =- Houd de muisknop op een van de bovenstaande knoppen om sneller te selecteren', '', '', 1, 1),
(6057, '- No Cities -', ' - keine St?dte -', '- Geen Steden -', '', '', 1, 1),
(6058, '- No Store Details -', ' - keine Filial-Details -', '- Geen Winkel Details -', '', '', 1, 1),
(6059, '- Use the %s buttons to select month', ' - Nutzen Sie die %s Buttons, um einen Monat auszuw?hlen', '- Gebruik de %s knoppen om de maand te selecteren', '', '', 1, 1),
(6060, '%s Items', '%s Artikel', '%s Artikel(en)', '', '', 1, 1),
(6061, '(Shift-)Click or drag to change value', '(Umschalt-)Klick oder ziehen, um Wert zu ?ndern', '(Shift-)Klik of sleep om de waarde te veranderen', '', '', 1, 1),
(6062, '* Required Fields', '* Pflichtfelder', '* Verplichte velden', '', '', 1, 1),
(6063, '*Pflichtfelder', '*Pflichtfelder', '*Verplichte velden', '', '', 1, 1),
(6064, '#NAME?', ' =- Klicken Sie auf einen der Zeitwerte, um den Wert zu erh?hen', ' =- Klik op een van de tijdgedeeltes om deze te verhogen', '', '', 1, 1),
(6065, '#NAME?', ' =- Halten Sie einen der Buttons gedr', ' =- Houd de muisknop op een van de bovenstaande knoppen om sneller te selecteren', '', '', 1, 1),
(6066, '- No Cities -', ' - keine St?dte -', '- Geen Steden -', '', '', 1, 1),
(6067, '- No Store Details -', ' - keine Filial-Details -', '- Geen Winkel Details -', '', '', 1, 1),
(6068, '- Use the %s buttons to select month', ' - Nutzen Sie die %s Buttons, um einen Monat auszuw?hlen', '- Gebruik de %s knoppen om de maand te selecteren', '', '', 1, 1),
(6069, 'd', '', '', '', '', 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `rs_translation_image`
--

CREATE TABLE IF NOT EXISTS `rs_translation_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(10) NOT NULL DEFAULT '0',
  `image_name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=298 ;

--
-- 转存表中的数据 `rs_translation_image`
--

INSERT INTO `rs_translation_image` (`id`, `lang_id`, `image_name`, `status`) VALUES
(279, 4464, 'mm_54ae56166038c.png', 0),
(278, 3371, 'mm_54ae2f642c3d2.png', 1),
(276, 2284, 'mm_54ae26e078611.png', 1),
(281, 4464, 'mm_54ae5cd499411.png', 0),
(282, 4465, 'mm_54ae5d36612ce.png', 0),
(283, 4465, 'mm_54ae5de1d4c95.png', 0),
(284, 4465, 'mm_54ae5de4b6392.png', 0),
(285, 4464, 'mm_54ae5fc55d2af.png', 0),
(286, 4464, 'mm_54ae5fc8eac78.png', 0),
(287, 4466, 'mm_54af37eb4d00f.png', 1),
(288, 4710, 'mm_54af583a07b62.png', 0),
(289, 4712, 'mm_54af6971c3f2c.gif', 0),
(290, 0, 'mm_54af6f639ff36.jpg', 0),
(291, 0, '', 0),
(292, 0, 'mm_54af72d74d730.gif', 0),
(293, 0, '', 0),
(294, 0, '', 0),
(295, 0, 'mm_54af7561dd7d5.gif', 0),
(296, 4715, 'mm_54b36fb96358b.gif', 0),
(297, 0, 'mm_54b4e1bfef247.jpg', 0);

-- --------------------------------------------------------

--
-- 表的结构 `rs_user`
--

CREATE TABLE IF NOT EXISTS `rs_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `allow` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `rs_user`
--

INSERT INTO `rs_user` (`id`, `username`, `password`, `allow`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1),
(3, '123456', 'e10adc3949ba59abbe56e057f20f883e', 0),
(4, 'qwert', 'a384b6463fc216a5f8ecb6670f86456a', 1);

-- --------------------------------------------------------

--
-- 表的结构 `rs_website`
--

CREATE TABLE IF NOT EXISTS `rs_website` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `website_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `rs_website`
--

INSERT INTO `rs_website` (`id`, `website_name`) VALUES
(1, 'Default');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
