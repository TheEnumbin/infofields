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
require_once dirname(__FILE__) . '/../../includes/InfofieldDb.php';
require_once dirname(__FILE__) . '/../../includes/InfofieldHelper.php';

class AdminAjaxInfofieldsController extends ModuleAdminController
{
    use InfofieldHelper;

    public function ajaxProcessSaveInfometa()
    {
        $iso_code = trim(Tools::getValue('iso_code'));
        $inf_id = (int) trim(Tools::getValue('inf_id'));
        $prd_id = (int) trim(Tools::getValue('prd_id'));
        $inf_value = trim(Tools::getValue('inf_value'));
        $inf_type = (int) trim(Tools::getValue('inf_type'));
        $languages = Language::getLanguages(false);
        $lang_id = Context::getContext()->language->id;

        foreach ($languages as $language) {
            if ($language['iso_code'] == $iso_code) {
                $lang_id = (int) $language['id_lang'];
            }
        }
        $response = $this->infofield_meta_update($inf_id, $prd_id, $inf_type, 'product', $inf_value, true);
        $response['img_dir'] = _PS_IMG_;
        if ($response) {
            echo json_encode($response);
            exit;
        }

        echo json_encode(['success' => false]);
        exit;
    }

    public function ajaxProcessDeleteFileImg()
    {
        $inf_id = (int) trim(Tools::getValue('inf_id'));
        $item_id = (int) trim(Tools::getValue('item_id'));
        $field_type = trim(Tools::getValue('field_type'));
        $lang_id = Context::getContext()->language->id;
        $object = new MetaModel(null, $inf_id, $item_id);
        $deleted = true;

        if (isset($object->id)) {
            if (!empty($object->meta_data)) {
                $meta_data = json_decode(array_pop($object->meta_data), true);

                if ($field_type == 'file') {
                    $file = $meta_data['file'];
                    $total_path = _PS_IMG_DIR_ . '/infofield/';
                    $allowed_extensions = ['pdf', 'doc', 'docx', 'txt'];
                    $this->inf_unlink($total_path, $file, $allowed_extensions);
                } else {
                    $file = $meta_data['file'] . '.' . $meta_data['ext'];
                    $backend_file = $meta_data['file'] . '_backend_default.' . $meta_data['ext'];
                    $custom_file = $meta_data['file'] . '_custom_default.' . $meta_data['ext'];
                    $total_path = _PS_IMG_DIR_ . '/infofield/';
                    $allowed_extensions = ['gif', 'jpg', 'jpeg', 'jpe', 'png'];
                    $this->inf_unlink($total_path, $file, $allowed_extensions);
                    $this->inf_unlink($total_path, $backend_file, $allowed_extensions);
                    $this->inf_unlink($total_path, $custom_file, $allowed_extensions);
                }
            }
            $object->delete();
            $deleted = 1;
        }
        if ($deleted) {
            echo json_encode(['deleted' => true]);
        } else {
            echo json_encode(['deleted' => false]);
        }
        exit;
    }

    public function ajaxProcessImportCSV()
    {
        $file = $_FILES['csv_file'];
        $offset = trim(Tools::getValue('offset'));
        $csv_type = trim(Tools::getValue('csv_type'));
        $continue_import = 1;
        $chunkSize = 100;
        $inf_db = new InfofieldDB();

        if ($file['error'] !== UPLOAD_ERR_OK) {
            die(json_encode(array('error' => 'File upload failed')));
        }
        $handle = fopen($file['tmp_name'], 'r');

        if (!$handle) {
            die(json_encode(array('error' => 'Unable to open CSV file')));
        }

        if ($offset == 0) {
            $latest_id = $inf_db->inf_get_last_id();
        }
        fseek($handle, $offset);
        $processed_rows = 0;
        $lastrow = [];
        $insert_values_str = [];

        while (($row = fgetcsv($handle)) !== false && $processed_rows < $chunkSize) {

            if ($processed_rows > 0) {
                $insert_values_str[] = $this->process_csv_row($row, $csv_type);
                $lastrow[] = $row;
            }
            $processed_rows++;
        }

        if ($csv_type == 5) {
            $inf_db->insert_infofields($insert_values_str);
        }

        if (empty($lastrow)) {
            $continue_import = false;
            $this->finish_processing_import($csv_type);
        }

        // Get the current file pointer position
        $currentOffset = ftell($handle);
        $isFinished = feof($handle);
        fclose($handle);

        die(json_encode(array(
            'offset' => $currentOffset,
            'is_finished' => $isFinished,
            'last_row' => $lastrow,
            'continue' => $continue_import,
        )));
    }

    private function inf_unlink($total_path, $file, $allowlist)
    {
        $file_extension = pathinfo($file, PATHINFO_EXTENSION);

        if (in_array(strtolower($file_extension), $allowlist, true)) {
            $full_path = realpath($total_path . $file);

            // Validate that the full path exists and is within the intended directory
            if ($full_path && strpos($full_path, realpath($total_path)) === 0) {
                // Delete the file
                if (file_exists($full_path)) {
                    unlink($full_path);
                }
            } else {
                // Log an error or handle the invalid path case
                error_log('Invalid file path: ' . $full_path);
            }
        } else {
            // Log an error or handle the invalid file type case
            error_log('Attempt to delete an unauthorized file type: ' . $file);
        }
    }
}
