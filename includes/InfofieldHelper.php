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

require_once dirname(__FILE__) . '/../classes/MetaModel.php';

trait infofieldHelper
{
    private function infofield_meta_update()
    {
                $meta_object = new MetaModel(null, $inf_id, $obj->id);

                if (isset($meta_object->id)) {
                    if ($field_type == 5 || $field_type == 9) {
                        $done_upload = $this->inf_upload_files($inf_id, $obj->id, $_FILES, $parent_type, $field_type);

                        if (!$done_upload) {
                            continue;
                        }
                        $meta_object->meta_data = json_encode($done_upload);
                    } else {
                        $meta_object->meta_data = $data['inf_metafield_' . $inf_id];

                        if ($field_type == 6) {
                            $meta_object->meta_data = json_encode($meta_object->meta_data);
                        }
                    }
                    $meta_object->update();
                } else {
                    $meta_object->id_infofields = $inf_id;
                    $meta_object->parent_item_id = $obj->id;
                    if ($field_type == 5 || $field_type == 9) {
                        $done_upload = $this->inf_upload_files($inf_id, $obj->id, $_FILES, $parent_type, $field_type);

                        if (!$done_upload) {
                            continue;
                        }
                        $meta_object->meta_data = json_encode($done_upload);
                    } else {
                        $meta_object->meta_data = $data['inf_metafield_' . $inf_id];
                        if ($field_type == 6) {
                            $meta_object->meta_data = json_encode($meta_object->meta_data);
                        }
                    }

                    $meta_object->add();
                }
            }
    }
    private function inf_upload_files($inf_id, $obj_id, $files, $parent_type, $field_type)
    {
        $ogName = $files[$parent_type]['name']['inf_metafield_' . $inf_id];
        $tmp_name = $files[$parent_type]['tmp_name']['inf_metafield_' . $inf_id];
        $err_code = $files[$parent_type]['error']['inf_metafield_' . $inf_id];
        $uploadDir = _PS_IMG_DIR_ . 'infofield/';
        $ext = pathinfo($ogName, PATHINFO_EXTENSION);
        $newFileName = 'inf_img_' . $parent_type . '_' . $obj_id . '_' . $inf_id;
        $uploadFile = $newFileName . '.' . $ext;
        if ($field_type == 9) {
            $newFileName = $ogName;
            $uploadFile = $ogName;
        }
        $id_lang = $this->context->language->id;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if ($err_code !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!move_uploaded_file($tmp_name, $uploadDir . $uploadFile)) {
            return false;
        }

        if ($field_type == 5) {
            $fieldsmodel = new FieldsModel();
            $fields = $fieldsmodel->get_infofield_by_id($inf_id, $id_lang);
            $imgSizeData = [
                [
                    'name' => 'backend_default',
                    'width' => 125,
                    'height' => 125,
                ],
                [
                    'name' => 'custom_default',
                    'width' => $fields[0]['img_width'],
                    'height' => $fields[0]['img_height'],
                ]
            ];
            foreach ($imgSizeData as $imgSize) {
                $newWidth = (int)$imgSize['width'];
                $newHeight = (int)$imgSize['height'];
                $resized = ImageManager::resize(
                    $uploadDir . $uploadFile,
                    _PS_IMG_DIR_ . 'infofield/' . $newFileName . '_' . $imgSize['name'] . '.' . $ext,
                    $newWidth,
                    $newHeight
                );

                if (!$resized) {
                    continue;
                }
            }
        }
        return ['file' => $newFileName, 'ext' => $ext];
    }
}
