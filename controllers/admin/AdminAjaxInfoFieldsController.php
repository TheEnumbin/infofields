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
require_once dirname(__FILE__) . '/../../classes/MetaModel.php';

class AdminAjaxInfofieldsController extends ModuleAdminController
{
    public function ajaxProcessSaveInfometa()
    {
        $iso_code = trim(Tools::getValue('iso_code'));
        $inf_id = (int) trim(Tools::getValue('inf_id'));
        $prd_id = (int) trim(Tools::getValue('prd_id'));
        $inf_value = trim(Tools::getValue('inf_value'));
        $inf_type = (int) trim(Tools::getValue('inf_type'));
        $languages = Language::getLanguages(false);
        $lang_id = Context::getContext()->language->id;
        echo '<pre>';
        print_r($inf_type);
        echo '</pre>';
        echo __FILE__ . ' : ' . __LINE__;
        die(__FILE__ . ' : ' . __LINE__);

        foreach ($languages as $language) {
            if ($language['iso_code'] == $iso_code) {
                $lang_id = (int) $language['id_lang'];
            }
        }
        $object = new MetaModel(null, $inf_id, $prd_id);

        if (isset($object->id)) {
            if ($inf_type == 6) {
                $inf_value = json_encode($inf_value);
            }
            $object->meta_data[$lang_id] = $inf_value;
            $object->update();
        } else {
            $object->id_infofields = $inf_id;
            $object->parent_item_id = $prd_id;
            if ($inf_type == 6) {
                $inf_value = json_encode($inf_value);
            }
            $object->meta_data[$lang_id] = $inf_value;
            $object->add();
        }
        exit;
    }

    public function ajaxProcessDeleteFileImg()
    {
        $inf_id = (int) trim(Tools::getValue('inf_id'));
        $item_id = (int) trim(Tools::getValue('item_id'));
        $field_type = trim(Tools::getValue('field_type'));
        $lang_id = Context::getContext()->language->id;
        $object = new MetaModel(null, $inf_id, $item_id);
        $deleted = 0;

        if (isset($object->id)) {
            if (!empty($object->meta_data)) {
                $meta_data = json_decode(array_pop($object->meta_data), true);
                if ($field_type == 'file') {
                    $file = $meta_data['file'];
                } else {
                    $file = $meta_data['file'] . '.' . $meta_data['ext'];
                }
                $total_path = _PS_IMG_DIR_ . 'infofield/' . $file;
                unlink($total_path);
            }
            $object->delete();
            $delete = 1;
        }
        if ($delete) {
            echo json_encode(['deleted' => true]);
        } else {
            echo json_encode(['deleted' => false]);
        }
        exit;
    }
}
