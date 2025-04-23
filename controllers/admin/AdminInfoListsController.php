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

require_once dirname(__FILE__) . '/../../classes/FieldsModel.php';

class AdminInfoListsController extends ModuleAdminController
{
    public $module;

    public function __construct()
    {
        $this->module = 'infofields';
        $this->bootstrap = true;
        $this->table = 'infofields';
        $this->identifier = 'id_infofields';
        $this->className = 'FieldsModel';
        $this->lang = true;
        $this->deleted = false;
        $this->context = Context::getContext();
        $this->_orderBy = 'id_infofields';
        $this->_orderWay = 'asc';
        parent::__construct();
        $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'infofields_shop i_shop ON (a.id_infofields = i_shop.id_infofields)';
        $this->_where = 'AND i_shop.id_shop = ' . (int) $this->context->shop->id . ' AND (a.group_id IS NULL or a.group_id = 0)';
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = [
            'id_infofields' => [
                'title' => $this->module->l('ID'),
                'align' => 'center',
                'type' => 'integer',
            ],
            'field_name' => [
                'title' => $this->module->l('Name'),
                'align' => 'center',
                'type' => 'string',
            ],
            'parent_item' => [
                'title' => $this->module->l('Parent Item'),
                'align' => 'center',
                'type' => 'string',
                'callback' => 'getParentName',
            ],
            'field_type' => [
                'title' => $this->module->l('Field Type'),
                'align' => 'center',
                'type' => 'string',
                'callback' => 'getFieldtypeName',
            ],
        ];
        $advertise = $this->advertise_template();

        return parent::renderList() . $advertise;
    }

    public function renderForm()
    {
        $obj = $this->loadObject(true);

        if (!$obj) {
            return;
        }
        $id_infofields = Tools::getValue('id_infofields');
        $languages = Language::getLanguages(false);
        $parents = $this->getParentItems();
        $field_types = $this->getFieldTypes($obj->parent_item);
        $parent_arr = [];
        $field_types_arr = [];

        foreach ($parents as $id => $parent) {
            $parent_arr[] = [
                'id' => $id,
                'name' => $this->l($parent),
            ];
        }
        foreach ($field_types as $id => $field_type) {
            $field_types_arr[] = [
                'id' => $id,
                'name' => $this->l($field_type),
            ];
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Info Field Form'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Field Name'),
                    'name' => 'field_name',
                    'id' => 'field_name',
                    'size' => 60,
                    'required' => true,
                    'desc' => $this->l('This will appear as the field name on your form.'),
                    'lang' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Global Metadata'),
                    'name' => 'global_meta_data',
                    'lang' => true,
                    'rows' => 10,
                    'cols' => 62,
                    'class' => 'rte',
                    'autoload_rte' => true,
                    'desc' => $this->l('This will act as global meta data. Specific meta will overwrite it. Put 0 or 1 for Checkbox & Switch field.'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Parent Item'),
                    'name' => 'parent_item',
                    'options' => [
                        'query' => $parent_arr,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'form_group_class' => 'inf-parent-item-form',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Field Type'),
                    'name' => 'field_type',
                    'options' => [
                        'query' => $field_types_arr,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'form_group_class' => 'inf-field-type-form',
                    'desc' => $this->l('Rich textfield only works for Products and Category.'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Available Items'),
                    'name' => 'available_values',
                    'lang' => true,
                    'rows' => 10,
                    'cols' => 62,
                    'desc' => $this->l('Only for Select, Radio, Checkboxes types. [Example Format for Select and Radio: key1:label1,key2:label2] [Example Format for Checkboxes: label1,label2]'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Width'),
                    'name' => 'img_width',
                    'id' => 'img_width',
                    'size' => 20,
                    'desc' => $this->l('Only for image and video fields. Example: 20px'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Height'),
                    'name' => 'img_height',
                    'id' => 'img_height',
                    'size' => 20,
                    'desc' => $this->l('Only for image and video fields. Example: 20px'),
                ],
                [
                    'label' => $this->l('Start Date'),
                    'type' => 'date',
                    'name' => 'start_date',
                ],
                [
                    'label' => $this->l('End Date'),
                    'type' => 'date',
                    'name' => 'end_date',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Show Meta In'),
                    'name' => 'show_meta_in',
                    'options' => [
                        'query' => [
                            [
                                'id' => 'popup',
                                'name' => $this->l('In PopUp'),
                            ],
                            [
                                'id' => 'embed',
                                'name' => $this->l('Iframe Embed'),
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Show with field name'),
                    'name' => 'with_field_name',
                    'required' => false,
                    'class' => 't',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                    'hint' => $this->l('Show with the field name on frontend ex: Field name: Value'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Show as a Tab on Products'),
                    'name' => 'as_product_tab',
                    'required' => false,
                    'class' => 't',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                    'hint' => $this->l('Only works for products'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Shortcode'),
                    'name' => 'shortcode',
                    'id' => 'shortcode',
                    'size' => 60,
                    'desc' => $this->l('Use this in your tpl file to show the meta info.'),
                    'disabled' => true,
                    'class' => 'text-danger infofield-shortcode-class',
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save'),
            ],
        ];

        if ($id_infofields && $id_infofields > 0) {
            $id = $obj->id_infofields;
            $settings = json_decode($obj->settings, true);
            $parent = strtolower($this->getParentName($obj->parent_item));
            $this->fields_value['shortcode'] = $this->getShortcode($id, $parent);
            $this->fields_value['show_meta_in'] = $settings['show_meta_in'];
        }
        return parent::renderForm();
    }

    public function processSave()
    {
        if (Tools::isSubmit('submitAddinfofields')) {
            $id_infofields = (int) Tools::getValue('id_infofields');
            $fields = new FieldsModel($id_infofields);
            $defaultLangId = (int) Context::getContext()->language->id;
            $languages = Language::getLanguages();
            $defaultFieldName = Tools::getValue('field_name_' . $defaultLangId);

            // Check if all field names are empty
            $allFieldNamesEmpty = true;
            foreach ($languages as $lang) {
                $field_name = Tools::getValue('field_name_' . $lang['id_lang']);
                if (!empty($field_name)) {
                    $allFieldNamesEmpty = false;
                    break;
                }
            }

            if ($allFieldNamesEmpty) {
                $this->errors[] = $this->l('Field name cannot be empty for all languages.');
                return false;
            }
            foreach ($languages as $lang) {
                $field_name = Tools::getValue('field_name_' . $lang['id_lang']);
                if (empty($field_name)) {
                    $fields->field_name[$lang['id_lang']] = $defaultFieldName;
                } else {
                    $fields->field_name[$lang['id_lang']] = $field_name;
                }
                $fields->global_meta_data[$lang['id_lang']] = Tools::getValue('global_meta_data_' . $lang['id_lang']);
                $fields->available_values[$lang['id_lang']] = Tools::getValue('available_values_' . $lang['id_lang']);
            }

            // Settings
            $settings = [];
            $show_in_meta = Tools::getValue('show_meta_in');
            $settings['show_meta_in'] = $show_in_meta;
            $settings = json_encode($settings);
            $fields->settings = $settings;

            // Other fields
            $fields->parent_item = (int) Tools::getValue('parent_item');
            $fields->field_type = (int) Tools::getValue('field_type');
            $fields->start_date = Tools::getValue('start_date');
            $fields->end_date = Tools::getValue('end_date');
            $fields->with_field_name = (bool) Tools::getValue('with_field_name');
            $fields->img_width = Tools::getValue('img_width');
            $fields->img_height = Tools::getValue('img_height');
            $fields->as_product_tab = (bool) Tools::getValue('as_product_tab');

            // Validate dates
            if (strtotime($fields->start_date) > strtotime($fields->end_date)) {
                $this->errors[] = $this->l('Start date cannot be later than end date.');
                return false;
            }

            // Save or update fields
            if ($fields->id) {
                // Update existing fields
                if ($fields->update()) {
                    $this->confirmations[] = $this->l('Fields updated successfully.');
                    return true;
                } else {
                    $this->errors[] = $this->l('Failed to update fields.');
                }
            } else {
                // Add new fields
                if ($fields->add()) {
                    $this->confirmations[] = $this->l('Fields added successfully.');
                    return true;
                } else {
                    $this->errors[] = $this->l('Failed to add fields.');
                }
            }
        }

        return false;
    }

    public function postProcess()
    {
        if ($this->processSave()) {
            return true;
        } else {
            return parent::postProcess();
        }

        return false;
    }

    public function getShortcode($id, $parent)
    {
        $parent_obj = '';

        switch ($parent) {
            case 'product':
                $parent_obj = '$product.id';
                break;
            case 'cms page':
                $parent_obj = '$cms.id';
                break;
            case 'category':
                $parent_obj = '$category.id';
                break;
            case 'customer':
                $parent_obj = '$customer.id';
                break;
            default:
                $parent_obj = 'unknown';
        }

        return "{hook h='displayInfofield' id_infofields=$id item_id=$parent_obj}";
    }

    public function getParentName($key)
    {
        $parents = $this->getParentItems();
        return $parents[$key];
    }

    public function getFieldtypeName($key)
    {
        $field_types = $this->getFieldTypes();
        return $field_types[$key];
    }

    public function getParentItems()
    {
        return [
            2 => 'Product',
            1 => 'Category',
            3 => 'CMS Page',
            4 => 'Customer',
        ];
    }

    public function getFieldTypes($parent_item = 0)
    {
        $defaults = [
            1 => 'Text Field',
            2 => 'Rich Text Field',
            3 => 'Textarea',
            4 => 'Switch',
            5 => 'Image',
            10 => 'Videos',
            9 => 'File',
            6 => 'Date',
            7 => 'Checkboxes',
            8 => 'Select',
        ];

        if ($parent_item == 0 || $parent_item == 2) {
            $return_fields = $defaults + [
                11 => 'GPSR Fields',
                12 => 'FAQ Fields',
            ];

            return $return_fields;
        } else {
            return $defaults;
        }
        return [];
    }

    protected function advertise_template()
    {
        // Fetch and render the template file
        $this->context->smarty->assign('module_dir', $this->module->getPathUri());
        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/advertise_template.tpl');
    }
}
