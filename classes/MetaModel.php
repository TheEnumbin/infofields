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

class MetaModel extends ObjectModel
{
    public $id_infofields_meta;
    public $id_infofields;
    public $parent_item_id;
    public $meta_data;

    public static $definition = [
		'table'     => 'infofields_meta',
		'primary'   => 'id_infofields_meta',
		'multilang' => true,
		'fields'    => [
			'id_infofields'            => [
				'type'     => self::TYPE_INT,
				'validate' => 'isunsignedInt',
			],
			'parent_item_id'            => [
				'type'     => self::TYPE_INT,
				'validate' => 'isunsignedInt',
			],
			'meta_data'           => [
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
				'lang'     => true,
			],
		],
	];

	// public function __construct($id = null, $id_lang = null, $id_shop = null)
	// {
	// 	// Shop::addTableAssociation('infofields_meta', ['type' => 'shop']);
	// 	parent::__construct($id, $id_lang, $id_shop);
	// }

	public function __construct($id = null, $id_infofields = null, $parent_item_id = null, $id_lang = null)
	{
		if ($id) {
            parent::__construct($id);
        } elseif ($id_infofields && $parent_item_id) {
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT *
				FROM `' . _DB_PREFIX_ . 'infofields_meta` inm
				WHERE `id_infofields` = \'' . (int) $id_infofields . '\' AND `parent_item_id` = ' . (int) $parent_item_id);

                if ($row) {
                    parent::__construct($row['id_infofields_meta']);
                } else {
                    parent::__construct();
                }
        }
	}

	public function get_meta_by_parent($parent_id, $parent_fields){
		if(empty($parent_fields)){
			return false;
		}
		$id_parents = [];
		foreach($parent_fields as $parent_field){
			$id_parents[] = $parent_field['id_infofields'];
		}
		$id_parents = implode(', ', $id_parents);
		$metas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("
		SELECT *
		FROM `" . _DB_PREFIX_ . "infofields_meta` infm LEFT JOIN `" . _DB_PREFIX_ . "infofields_meta_lang` infml ON (infm.id_infofields_meta = infml.id_infofields_meta)
		WHERE `id_infofields` IN ($id_parents) AND `parent_item_id` = " . (int) $parent_id);
		$return_arr = [];
		foreach ($metas as $meta) {
			$return_arr[$meta['id_infofields']][$meta['id_lang']] = $meta;
		}
		return $return_arr;
	}
}
