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

class FieldsModel extends ObjectModel
{
    public $id_infofields;
    public $parent_item;
    public $field_type;
    public $start_date;
    public $end_date;
    public $field_name;
    public $global_meta_data;

    public static $definition = [
        'table'     => 'infofields',
        'primary'   => 'id_infofields',
        'multilang' => true,
        'fields'    => [
            'parent_item'            => [
                'type'     => self::TYPE_INT,
                'validate' => 'isunsignedInt',
            ],
            'field_type'            => [
                'type'     => self::TYPE_INT,
                'validate' => 'isunsignedInt',
            ],
            'start_date'          => [
                'type'     => self::TYPE_DATE,
                'validate' => 'isString',
            ],
            'end_date'         => [
                'type'     => self::TYPE_DATE,
                'validate' => 'isString',
            ],
            'field_name'         => [
                'type'     => self::TYPE_STRING,
                'validate' => 'isString',
                'lang'     => true,
                'required' => true,
            ],
            'with_field_name' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'global_meta_data'           => [
                'type'     => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
                'lang'     => true,
            ],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation('infofields', ['type' => 'shop']);
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function get_infofield_by_parent_item($p_item)
    {
        $lang_id = Context::getContext()->language->id;
        $shop_id = Context::getContext()->shop->id;

        // $results = Db::getInstance()->executeS(
        //     'SELECT *
        //     FROM `' . _DB_PREFIX_ . 'infofields` inf
        //     WHERE inf.parent_item = ' . (int) $p_item,
        //     true
        // );
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM ' . _DB_PREFIX_ . 'infofields inf
		LEFT JOIN ' . _DB_PREFIX_ . 'infofields_lang infl ON (inf.id_infofields = infl.id_infofields)
		' . Shop::addSqlAssociation('infofields', 'inf') . '
		WHERE inf.parent_item = ' . (int) $p_item, true);
        return $results;
    }

    public function get_infofield_by_id($id, $id_lang)
    {
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM ' . _DB_PREFIX_ . 'infofields inf
		LEFT JOIN ' . _DB_PREFIX_ . 'infofields_lang infl ON (inf.id_infofields = infl.id_infofields)
		' . Shop::addSqlAssociation('infofields', 'inf') . '
		WHERE inf.id_infofields = ' . (int) $id . ' AND infl.id_lang = ' . (int) $id_lang, true);
        return $results;
    }
}
