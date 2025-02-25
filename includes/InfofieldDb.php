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
            ' . pSQL($row[0]) . ',
            ' . pSQL($row[1]) . ',
            ' . (empty($row[2]) ? 'NULL' : "'" . pSQL($row[2]) . "'") . ',
            ' . (empty($row[3]) ? 'NULL' : "'" . pSQL($row[3]) . "'") . ',
            ' . (strtoupper($row[4]) === 'TRUE' ? 1 : 0) . ',
            ' . (strtoupper($row[5]) === 'TRUE' ? 1 : 0) . ',
            ' . (empty($row[6]) ? 'NULL' : (int)$row[6]) . ',
            ' . (empty($row[7]) ? 'NULL' : (int)$row[7]) . '
            )';

        if (Db::getInstance()->execute($query)) {
            return Db::getInstance()->Insert_ID();
        }

        return false;
    }

    public function insert_infofields_lang($row)
    {
    }
}
