<?php

/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2022 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'infofields` (
    `id_infofields` int(11) NOT NULL AUTO_INCREMENT,
    `parent_item` int(11),
    `field_type` int(11),
    `start_date` datetime,
    `end_date` datetime,
    `with_field_name` BOOLEAN,
    `as_product_tab` BOOLEAN,
    PRIMARY KEY  (`id_infofields`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'infofields_lang` (
    `id_infofields` int(11),
    `id_lang` int(11),
    `field_name` VARCHAR(255),
    `global_meta_data` longtext,
    `available_values` VARCHAR(255),
    PRIMARY KEY  (`id_infofields`, `id_lang`),
    FOREIGN KEY (`id_infofields`) REFERENCES `' . _DB_PREFIX_ . 'infofields`(id_infofields)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'infofields_shop` (
    `id_infofields` int(11),
    `id_shop` VARCHAR(255),
    PRIMARY KEY  (`id_infofields`, `id_shop`),
    FOREIGN KEY (`id_infofields`) REFERENCES `' . _DB_PREFIX_ . 'infofields`(id_infofields)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'infofields_groups` (
    `id_infofields_groups` int(11) NOT NULL AUTO_INCREMENT,
    `parent_item` int(11),
    `field_type` int(11),
    `start_date` datetime,
    `end_date` datetime,
    `with_field_name` BOOLEAN,
    `as_product_tab` BOOLEAN,
    PRIMARY KEY  (`id_infofields`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'infofields_meta` (
    `id_infofields_meta` int(11) NOT NULL AUTO_INCREMENT,
    `id_infofields` int(11),
    `parent_item_id` int(11),
    PRIMARY KEY  (`id_infofields_meta`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'infofields_meta_lang` (
    `id_infofields_meta` int(11),
    `id_lang` int(11),
    `meta_data` longtext,
    PRIMARY KEY  (`id_infofields_meta`, `id_lang`),
    FOREIGN KEY (`id_infofields_meta`) REFERENCES `' . _DB_PREFIX_ . 'infofields_meta`(id_infofields_meta)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
