<?php

/**
 * 2017-2022 PrestaShop
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
 * @author    MBE Worldwide
 * @copyright 2017-2023 MBE Worldwide
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of MBE Worldwide
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class InfofieldDB
{
    public function insert_infofields($row)
    {
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'infofields` (
            `parent_item`, `field_type`, `start_date`, `end_date`,
            `with_field_name`, `as_product_tab`, `img_width`, `img_height`
            ) VALUES (
            ' . pSQL($row[2]) . ',
            ' . pSQL($row[3]) . ',
            ' . (empty($row[4]) ? 'NULL' : "'" . pSQL($row[4]) . "'") . ',
            ' . (empty($row[5]) ? 'NULL' : "'" . pSQL($row[5]) . "'") . ',
            ' . (strtoupper($row[6]) === 'TRUE' ? 1 : 0) . ',
            ' . (strtoupper($row[7]) === 'TRUE' ? 1 : 0) . ',
            ' . (empty($row[8]) ? 'NULL' : (int) $row[8]) . ',
            ' . (empty($row[9]) ? 'NULL' : (int) $row[9]) . '
            )';

        if (Db::getInstance()->execute($query)) {
            return Db::getInstance()->Insert_ID();
        }

        return false;
    }

    public function insert_infofields_lang($row, $inf_id)
    {
        if (empty($row[0])) {
            return false;
        }
        $lang_id = Context::getContext()->language->id;
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'infofields_lang` (
            `id_infofields`, `id_lang`, `field_name`, `global_meta_data`, `available_values`
            ) VALUES (
            ' . pSQL($inf_id) . ',
            ' . pSQL($lang_id) . ',
            "' . pSQL($row[0]). '",
            ' . (empty($row[1]) ? "''" : "'" . pSQL($row[1]) . "'") . ',
            ' . (empty($row[4]) ? "''" : "'" . pSQL($row[4]) . "'") . '
            )';

        if (Db::getInstance()->execute($query)) {
            return true;
        }

        return false;
    }

    public function insert_infofields_shop()
    {
        $shop_id = Context::getContext()->shop->id;
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'infofields_shop` (
            `id_infofields`, `id_shop`
            ) 
            SELECT id_infofields, ' . $shop_id . ' FROM `' . _DB_PREFIX_ . 'infofields` 
            ';

        if (Db::getInstance()->execute($query)) {
            return true;
        }

        return false;
    }

    public function insert_infofields_meta($row)
    {
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'infofields_meta` (
            `id_infofields`, `parent_item_id`
            ) VALUES (
            ' . pSQL($row[0]) . ',
            ' . pSQL($row[1]) . '
            )';

        if (Db::getInstance()->execute($query)) {
            return Db::getInstance()->Insert_ID();
        }

        return false;
    }

    public function insert_infofields_meta_lang($row, $inf_meta_id)
    {
        if (empty($row[0])) {
            return false;
        }
        $lang_id = Context::getContext()->language->id;
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'infofields_meta_lang` (
            `id_infofields_meta`, `id_lang`, `meta_data`
            ) VALUES (
            ' . pSQL($inf_meta_id) . ',
            ' . pSQL($lang_id) . ',
            "' . pSQL($row[2]). '"
            )';

        if (Db::getInstance()->execute($query)) {
            return true;
        }

        return false;
    }
}
