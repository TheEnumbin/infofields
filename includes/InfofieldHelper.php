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

require_once dirname(__FILE__) . '/InfofieldDb.php';
require_once dirname(__FILE__) . '/../classes/MetaModel.php';

trait infofieldHelper
{
    private function infofield_meta_update($inf_id, $obj_id, $field_type, $parent_type, $data, $ajax = false)
    {
        $meta_object = new MetaModel(null, $inf_id, $obj_id);
        $return_arr = [];
        if (isset($meta_object->id)) {
            if ($field_type == 5 || $field_type == 9) {
                $done_upload = $this->inf_upload_files($inf_id, $obj_id, $_FILES, $parent_type, $field_type, $ajax);

                if (!$done_upload) {
                    return false;
                }
                $return_arr['file'] = $done_upload;
                $meta_object->meta_data = json_encode($done_upload);
            } else {
                $meta_data = $data;
                if (!$ajax) {
                    $meta_data = $data['inf_metafield_' . $inf_id];
                }
                $meta_object->meta_data = $meta_data;

                if ($field_type == 6) {
                    $meta_object->meta_data = json_encode($meta_object->meta_data);
                }
            }
            $meta_object->update();
        } else {
            $meta_object->id_infofields = $inf_id;
            $meta_object->parent_item_id = $obj_id;
            if ($field_type == 5 || $field_type == 9) {
                $done_upload = $this->inf_upload_files($inf_id, $obj_id, $_FILES, $parent_type, $field_type, $ajax);

                if (!$done_upload) {
                    return false;
                }
                $return_arr['file'] = $done_upload;
                $meta_object->meta_data = json_encode($done_upload);
            } else {
                $meta_data = $data;

                if (!$ajax) {
                    $meta_data = $data['inf_metafield_' . $inf_id];
                }
                $meta_object->meta_data = $meta_data;
                if ($field_type == 6) {
                    $meta_object->meta_data = json_encode($meta_object->meta_data);
                }
            }

            $meta_object->add();
        }
        $return_arr['success'] = true;
        return $return_arr;
    }

    private function inf_upload_files($inf_id, $obj_id, $files, $parent_type, $field_type, $ajax = false)
    {
        if ($ajax) {
            $ogName = $files['inf_value']['name'];
            $tmp_name = $files['inf_value']['tmp_name'];
            $err_code = $files['inf_value']['error'];
        } else {
            $ogName = $files[$parent_type]['name']['inf_metafield_' . $inf_id];
            $tmp_name = $files[$parent_type]['tmp_name']['inf_metafield_' . $inf_id];
            $err_code = $files[$parent_type]['error']['inf_metafield_' . $inf_id];
        }

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
                ],
            ];
            foreach ($imgSizeData as $imgSize) {
                $newWidth = (int) $imgSize['width'];
                $newHeight = (int) $imgSize['height'];
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

    private function process_csv_row($row, $csv_type)
    {
        $inf_db = new InfofieldDB();

        if ($csv_type == 5) {
            $processed_row = $this->process_row_data($row, $csv_type);
            $inf_id = $inf_db->insert_infofields($processed_row);
            $done = $inf_db->insert_infofields_lang($row, $inf_id);

            return $done;
        }
    }

    private function finish_processing_import($csv_type)
    {
        $inf_db = new InfofieldDB();

        if ($csv_type == 5) {
            $done = $inf_db->insert_infofields_shop();

            return $done;
        }
    }

    private function count_csv_rows($filePath)
    {
        $handle = fopen($filePath, 'r');
        $rowCount = 0;
        while (fgetcsv($handle) !== false) {
            $rowCount++;
        }
        fclose($handle);
        return $rowCount;
    }

    private function process_row_data($row, $csv_type)
    {
        if ($csv_type == 5) {
            $row[2] = $this->inf_value_array('parent_item', $row[2]);
            $row[3] = $this->inf_value_array('field_type', $row[3]);
        }
        // echo '<pre>';
        // print_r($row);
        // echo '</pre>';
        // echo __FILE__ . ' : ' . __LINE__;
        // die(__FILE__ . ' : ' . __LINE__);
        return $row;
    }

    private function inf_table_heads($which, $of)
    {
        $table_heads = [
            'infofields' => [
                'main' => [
                    'parent_item',
                    'field_type',
                    'start_date',
                    'end_date',
                    'with_field_name',
                    'as_product_tab',
                    'img_width',
                    'img_height',
                ],
                'lang' => [
                    'id_infofields',
                    'id_lang',
                    'field_name',
                    'global_meta_data',
                    'available_values',
                ],
                'shop' => [
                    'id_infofields',
                    'id_shop',
                ],
            ],
            'meta' => [
                'main' => [
                    'id_infofields',
                    'parent_item_id',
                ],
                'lang' => [
                    'id_infofields_meta',
                    'id_lang',
                    'meta_data',
                ],
            ],
        ];

        return $table_heads[$of][$which];
    }

    private function inf_value_array($of, $which)
    {
        $value_arr = [
            'parent_item' => [
                'product' => 2,
                'customer' => 4,
                'category' => 1,
                'cms' => 3,
            ],
            'field_type' => [
                'textfield' => 1,
                'rte' => 2,
                'textarea' => 3,
                'switch' => 4,
                'image' => 5,
                'video' => 10,
                'file' => 9,
                'date' => 6,
                'checkboxes' => 7,
                'select' => 8,
            ],
        ];

        return $value_arr[$of][$which];
    }
}
