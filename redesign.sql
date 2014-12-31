-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 12 月 31 日 10:05
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
-- 表的结构 `rs_translation`
--

CREATE TABLE IF NOT EXISTS `rs_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `en` text NOT NULL,
  `de` text NOT NULL,
  `nl` text NOT NULL,
  `fr` text NOT NULL,
  `remarks` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=157 ;

--
-- 转存表中的数据 `rs_translation`
--

INSERT INTO `rs_translation` (`id`, `en`, `de`, `nl`, `fr`, `remarks`) VALUES
(155, 'available', 'verfügbar', 'beschikbaar', 'w', 'd'),
(137, 'Action', 'Aktion', 'Handeling', 'Action', ''),
(135, '3: Shipping', '3: Versand', '3: Verzenden', 'Transport maritime', ''),
(156, 'Welcome', 'Willkommen', 'Welkom', '', 'test');

-- --------------------------------------------------------

--
-- 表的结构 `rs_translation_image`
--

CREATE TABLE IF NOT EXISTS `rs_translation_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(10) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=60 ;

--
-- 转存表中的数据 `rs_translation_image`
--

INSERT INTO `rs_translation_image` (`id`, `lang_id`, `image_name`) VALUES
(3, 0, ''),
(2, 0, 'mm_54a1001f9dd57.png'),
(4, 0, 'mm_54a1003bb9164.png'),
(5, 0, 'mm_54a10089cb7c8.gif'),
(6, 0, 'mm_54a1009992566.png'),
(7, 0, 'mm_54a10135eabf9.gif'),
(8, 0, 'mm_54a101f4b69e0.png'),
(9, 0, 'mm_54a1028069743.gif'),
(10, 1, 'mm_54a102a567865.gif'),
(11, 7, 'mm_54a102ae24662.gif'),
(14, 136, 'mm_54a2107bb3ee8.png'),
(15, 138, 'mm_54a214c673882.jpg'),
(16, 138, 'mm_54a21503abb8b.gif'),
(17, 138, 'mm_54a2152cea1dd.png'),
(18, 139, 'mm_54a21569c6440.jpg'),
(19, 139, 'mm_54a2172fcb86f.jpg'),
(20, 139, 'mm_54a2177fdd1f4.png'),
(21, 139, 'mm_54a2179052ccb.jpg'),
(22, 139, 'mm_54a2244cc91b1.gif'),
(23, 139, 'mm_54a22467a12e5.png'),
(24, 145, 'mm_54a2398ec3b30.png'),
(25, 145, 'mm_54a239b4aff33.jpg'),
(26, 145, 'mm_54a239db7da6a.gif'),
(27, 145, 'mm_54a239fb183b8.gif'),
(28, 145, 'mm_54a23a02ebf12.png'),
(29, 140, 'mm_54a24811875e1.png'),
(30, 140, 'mm_54a248155043a.jpg'),
(31, 136, 'mm_54a38ecfdd3bc.jpg'),
(55, 137, 'mm_54a3ae9d80d88.png'),
(57, 135, 'mm_54a3aee02d321.jpg'),
(53, 137, 'mm_54a3ad710c475.jpg'),
(59, 135, 'mm_54a3aee7b4dec.jpg'),
(58, 135, 'mm_54a3aee472a9b.png');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
