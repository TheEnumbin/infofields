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
    public function inf_get_last_id($csv_type)
    {
        if ($csv_type == 5) {
            $latest_id = Db::getInstance()->getValue('
                SELECT MAX(id_infofields) AS latest_id FROM ' . _DB_PREFIX_ . 'infofields
            ');
        } else {
            $latest_id = Db::getInstance()->getValue('
                SELECT MAX(id_infofields_meta) AS latest_id FROM ' . _DB_PREFIX_ . 'infofields_meta
            ');
        }

        if ($latest_id == null) {
            $latest_id = 0;
        }

        return $latest_id;
    }

    public function insert_infofields($values)
    {
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'infofields` (
            `parent_item`, `field_type`, `start_date`, `end_date`,
            `with_field_name`, `as_product_tab`, `img_width`, `img_height`
            ) VALUES'
            . implode(',', $values);

        if (Db::getInstance()->execute($query)) {
            return Db::getInstance()->Insert_ID();
        }

        return false;
    }

    public function insert_infofields_lang($values)
    {
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'infofields_lang` (
            `id_infofields`, `id_lang`, `field_name`, `global_meta_data`, `available_values`
            ) VALUES '
            . implode(',', $values);
        if (Db::getInstance()->execute($query)) {
            return true;
        }

        return false;
    }

    public function insert_infofields_shop($starting_id)
    {
        $shop_id = Context::getContext()->shop->id;
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'infofields_shop` (
            `id_infofields`, `id_shop`
            ) 
            SELECT id_infofields, ' . $shop_id . ' FROM `' . _DB_PREFIX_ . 'infofields` WHERE  id_infofields > ' . (int) $starting_id . '
            ';

        if (Db::getInstance()->execute($query)) {
            return true;
        }

        return false;
    }

    public function insert_infofields_meta($values)
    {
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'infofields_meta` (
            `id_infofields`, `parent_item_id`
            ) VALUES '
            . implode(',', $values);

        if (Db::getInstance()->execute($query)) {
            return Db::getInstance()->Insert_ID();
        }

        return false;
    }

    public function insert_infofields_meta_lang($values)
    {
        $query = '
            INSERT INTO `' . _DB_PREFIX_ . 'infofields_meta_lang` (
            `id_infofields_meta`, `id_lang`, `meta_data`
            ) VALUES '
            . implode(',', $values);

        if (Db::getInstance()->execute($query)) {
            return true;
        }

        return false;
    }
}
