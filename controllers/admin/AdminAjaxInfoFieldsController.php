<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once(dirname(__FILE__) . '/../../classes/MetaModel.php');


class AdminAjaxInfofieldsController extends ModuleAdminController
{

    public function ajaxProcessSaveInfometa()
    {
        $lang_id = Context::getContext()->language->id;
        echo '<pre>';
        print_r($lang_id);
        echo '</pre>';
        echo __FILE__ . ' : ' . __LINE__;
        die(__FILE__ . ' : ' . __LINE__);
        $object = new MetaModel();
        $object->id_infofields = 1;
        $object->parent_item_id = 19;
        $object->meta_data[1] = "hello Single Meta";

        echo '<pre>';
        print_r($object);
        echo '</pre>';
        echo __FILE__ . ' : ' . __LINE__;

        $object->add();
        die(__FILE__ . ' : ' . __LINE__);
    }

    public function ajaxProcessPrextrametaChangeLang()
    {
        $lang_id = Tools::getValue('langid');
        $shop_id = Tools::getValue('shopid');
        $prd_id = Tools::getValue('prdid');
        $omnibus_meta = array();
        $mresults = Db::getInstance()->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'infofields` pemt
            WHERE pemt.`lang_id` = ' . (int) $lang_id . ' AND' . ' pemt.`shop_id` = ' . (int) $shop_id . ' AND pemt.`product_id` = ' . (int) $prd_id,
            true
        );

        if(isset($mresults) && !empty($mresults)){
            $omnibus_meta = array_pop($mresults);
        }

        if(isset($omnibus_meta)){
            $returnarr = [
                'success' => true,
                'omnibus_meta' => $omnibus_meta,
            ];
            echo json_encode($returnarr);
            die();
        }else{
            $returnarr = [
                'success' => false,
            ];
            echo json_encode($returnarr);
            die();
        }
    }

    public function ajaxProcessAddExtraMeta()
    {
        $exmtid = Tools::getValue('exmtid');
        $prd_id = Tools::getValue('prdid');
        $meta = Tools::getValue('meta');
        $s_date = Tools::getValue('s_date');
        $e_date = Tools::getValue('e_date');
        $lang_id = Tools::getValue('langid');
        $shop_id = Tools::getValue('shopid');

        $result = Db::getInstance()->delete(
            'infofields',
            '`id_infofields` = ' . (int) $exmtid
        );

        $result = Db::getInstance()->insert('infofields', [
            'product_id' => (int) $prd_id,
            'id_product_attribute' => 0,
            'meta_data' => $meta,
            'start_date' => $s_date,
            'end_date' => $e_date,
            'shop_id' => (int) $shop_id,
            'lang_id' => (int) $lang_id,
        ]);
        $insert_id = Db::getInstance()->Insert_ID();

        if($result){
            $returnarr = [
                'success' => true,
                'msg' => 'Meta Inserted Succesfully',
                'meta_data' => $meta,
                'start_date' => $s_date,
                'end_date' => $e_date,
                'id_inserted' => $insert_id,
            ];
            echo json_encode($returnarr);
            die();
        }else{
            $returnarr = [
                'success' => false,
                'msg' => 'Something Wrong. Try Again!!!',
            ];
            echo json_encode($returnarr);
            die();
        }
    }
}
