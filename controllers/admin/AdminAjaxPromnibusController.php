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
class AdminAjaxPromnibusController extends ModuleAdminController
{
    public function ajaxProcessPricingomnibusChangeLang()
    {
        $lang_id = Tools::getValue('langid');
        $shop_id = Tools::getValue('shopid');
        $prd_id = Tools::getValue('prdid');
        $omnibus_meta = array();
        $mresults = Db::getInstance()->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'pricing_extrameta` pemt
            WHERE pemt.`lang_id` = ' . (int) $lang_id . ' AND' . ' pemt.`shop_id` = ' . (int) $shop_id . ' AND pemt.`product_id` = ' . (int) $prd_id,
            true
        );

        if(isset($mresults) && !empty($mresults)){
            $omnibus_meta = array_pop($mresults);
        }

        $results = Db::getInstance()->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'pricingomnibus_products` oc
            WHERE oc.`lang_id` = ' . (int) $lang_id . ' AND' . ' oc.`shop_id` = ' . (int) $shop_id . ' AND oc.`product_id` = ' . (int) $prd_id . ' ORDER BY date DESC',
            true
        );

        $omnibus_prices = array();

        foreach($results as $result){
            $omnibus_prices[$result['id_pricingomnibus']]['id'] = $result['id_pricingomnibus'];
            $omnibus_prices[$result['id_pricingomnibus']]['date'] = $result['date'];
            $omnibus_prices[$result['id_pricingomnibus']]['price'] = Context::getContext()->getCurrentLocale()->formatPrice($result['price'], Context::getContext()->currency->iso_code);
            $omnibus_prices[$result['id_pricingomnibus']]['promotext'] = 'Normal Price';

            if($result['promo']){
                $omnibus_prices[$result['id_pricingomnibus']]['promotext'] = 'Promotional Price';
            }
        }

        if(isset($omnibus_meta) || isset($omnibus_prices)){
            $returnarr = [
                'success' => true,
                'omnibus_meta' => $omnibus_meta,
                'omnibus_prices' => $omnibus_prices,
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
            'pricing_extrameta',
            '`id_prextrameta` = ' . (int) $exmtid
        );

        $result = Db::getInstance()->insert('pricing_extrameta', [
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

    /**
     * This function allow to delete users
     */
    public function ajaxProcessAddCustomPrice()
    {
        $prd_id = Tools::getValue('prdid');
        $price = Tools::getValue('price');
        $promodate = Tools::getValue('promodate');
        $pricetype = Tools::getValue('pricetype');
        $lang_id = Tools::getValue('langid');
        $shop_id = Tools::getValue('shopid');
        $promotext = "Normal Price";
        $promo = 0;

        if($pricetype){
            $promo = 1;
            $promotext = "Promotional Price";
        }

        $result = Db::getInstance()->insert('pricingomnibus_products', [
            'product_id' => (int) $prd_id,
            'id_product_attribute' => 0,
            'price' => $price,
            'promo' => $promo,
            'date' => $promodate,
            'shop_id' => (int) $shop_id,
            'lang_id' => (int) $lang_id,
        ]);
        $insert_id = Db::getInstance()->Insert_ID();
        $price_formatted = Context::getContext()->getCurrentLocale()->formatPrice($price, Context::getContext()->currency->iso_code);

        if($result){
            $returnarr = [
                'success' => true,
                'date' => $promodate,
                'price' => $price_formatted,
                'promo' => $promotext,
                'id_inserted' => $insert_id,
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

    public function ajaxProcessDeleteCustomPrice()
    {
        $pricing_id = Tools::getValue('pricing_id');

        $result = Db::getInstance()->delete(
            'pricingomnibus_products',
            '`id_pricingomnibus` = ' . (int) $pricing_id
        );
        
        if($result){
            $returnarr = [
                'success' => true,
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
}
