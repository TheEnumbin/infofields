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

use PrestaShopBundle\Form\Admin\Type\CustomContentType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\File;

class InfofieldBuilder
{
    private $fields;
    private $metas;

    public function __construct($form_for, $item_id)
    {
        $fieldsmodel = new FieldsModel();
        $this->fields = $fieldsmodel->get_infofield_by_parent_item($form_for);
        $metamodel = new MetaModel();
        $this->metas = $metamodel->get_meta_by_parent($item_id, $this->fields, null, true);
    }

    public function get_fields()
    {
        return $this->fields;
    }

    public function get_metas()
    {
        return $this->metas;
    }

    public function inf_build_form($formBuilder, $fields, $metas)
    {
        $inf_ids = [];
        $file_fileds = [5, 9];
        foreach ($fields as $field) {
            $field_params = $this->inf_get_field_params($field);
            $inf_ids[$field['id_infofields']] = $field['field_type'];
            $data = $this->inf_prepare_data($metas[$field['id_infofields']], $field['field_type']);
            if (!in_array($field['field_type'], $file_fileds)) {
                $field_params['params']['data'] = $data;
            }
            if ($field_params['has_translator']) {
                $formBuilder
                ->add(
                    'inf_metafield_' . $field['id_infofields'],
                    TranslatableType::class,
                    $field_params['params'],
                );
            } else {
                $formBuilder
                    ->add(
                        'inf_metafield_' . $field['id_infofields'],
                        $field_params['classtype'],
                        $field_params['params'],
                    );
                if ($field['field_type'] == 5) {
                    if ($data != '') {
                        $data = _PS_IMG_ . 'infofield/' . $data;
                        $formBuilder->add('image_preview', CustomContentType::class, [
                            'template' => '@Modules/infofields/views/templates/admin/file_display.html.twig',
                            'data' => [
                                'imageUrl' => $data,
                            ],
                        ]);
                    }
                } elseif ($field['field_type'] == 9) {
                    if ($data != '') {
                        $data = _PS_IMG_ . 'infofield/' . $data;
                        $formBuilder->add('file_preview', CustomContentType::class, [
                            'template' => '@Modules/infofields/views/templates/admin/file_display.html.twig',
                            'data' => [
                                'fileUrl' => $data,
                            ],
                        ]);
                    }
                }
            }
        }
        $inf_ids = array_unique($inf_ids);
        $inf_ids = json_encode($inf_ids);
        $formBuilder
        ->add(
            'inf_infofield_ids',
            HiddenType::class,
            [
                'data' => $inf_ids,
            ]
        );
    }

    public function inf_get_field_params($field)
    {
        $return_arr = null;

        switch ($field['field_type']) {
            case 1:
                $return_arr['params'] = [
                    'type' => TextType::class,
                ];
                $return_arr['has_translator'] = true;
                break;
            case 2:
                $return_arr['params'] = [
                    'type' => FormattedTextareaType::class,
                ];
                $return_arr['has_translator'] = true;
                break;
            case 3:
                $return_arr['params'] = [
                    'type' => TextareaType::class,
                ];
                $return_arr['has_translator'] = true;
                break;
            case 4:
                $return_arr['classtype'] = SwitchType::class;
                $return_arr['has_translator'] = false;
                break;
            case 5:
                $return_arr['classtype'] = FileType::class;
                $return_arr['has_translator'] = false;
                $return_arr['data_class'] = null; // Important to handle File objects
                $return_arr['attr'] = [
                    'accept' => 'gif,jpg,jpeg,jpe,png',
                ];
                break;
            case 9:
                $return_arr['classtype'] = FileType::class;
                $return_arr['has_translator'] = false;
                $return_arr['data_class'] = null; // Important to handle File objects
                $return_arr['attr'] = [
                    'accept' => 'pdf,doc',
                ];
                break;
            case 6:
                $return_arr['params'] = [
                    'format' => 'yyyy-MM-dd',
                    'input' => 'string',
                ];
                $return_arr['classtype'] = DateType::class;
                $return_arr['has_translator'] = false;
                break;
            case 7:
                $return_arr['classtype'] = CheckboxType::class;
                $return_arr['has_translator'] = false;
                $return_arr['attr'] = [
                    'material_design' => true,
                ];
                break;
            case 8:
                $return_arr['classtype'] = ChoiceType::class;
                $return_arr['has_translator'] = false;
                $available_values = explode(',', $field['available_values']);
                $return_arr['params']['choices']['Select An Item'] = 0;

                foreach ($available_values as $available_value) {
                    $key_value = explode(':', $available_value);

                    if (isset($key_value[1])) {
                        $label = $key_value[1];
                        $key = $key_value[0];
                    } else {
                        $label = $key_value[0];
                        $key = str_replace(' ', '', strtolower($label));
                    }
                    if ($key != '') {
                        $return_arr['params']['choices'][$label] = $key;
                    }
                }
                break;
        }
        $return_arr['params']['required'] = false;
        $return_arr['params']['label'] = $field['field_name'];

        return $return_arr;
    }

    public function inf_prepare_data($data, $field_type)
    {
        switch ($field_type) {
            case 1:
            case 2:
            case 3:
                break;
            case 5:
            case 9:
                $data = array_pop($data);
                $data = json_decode($data, true);
                if (is_array($data)) {
                    if ($field_type == 5) {
                        $data = $data['file'] . '_backend_default' . '.' . $data['ext'];
                    } else {
                        $data = $data['file'];
                    }
                } else {
                    $data = '';
                }
                break;
            case 4:
            case 8:
                $data = array_pop($data);
                break;
            case 6:
                $data = array_pop($data);
                $data = json_decode($data, true);
                if (is_array($data)) {
                    $flag = 0;
                    if ($data['year'] != '' && $data['month'] != '' && $data['day'] != '') {
                        $data = implode('-', $data);
                    } else {
                        $data = '';
                    }
                } else {
                    $data = '';
                }
                break;
            case 7:
                $data = array_pop($data);
                if ($data == 1) {
                    $data = true;
                } else {
                    $data = false;
                }

                break;
        }
        return $data;
    }
}
