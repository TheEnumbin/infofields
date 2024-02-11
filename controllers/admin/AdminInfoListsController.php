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

require_once(dirname(__FILE__) . '/../../classes/FieldsModel.php');

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

        return parent::renderList();
    }

    public function renderForm()
	{
        $obj = $this->loadObject(true);
        if (!$obj) {
            return;
        }
		$languages = Language::getLanguages(false);
        $parents = $this->getParentItems();
        $field_types = $this->getFieldTypes();
        $parent_arr = [];
        $field_types_arr = [];
        foreach ($parents as $id => $parent) {
            $parent_arr[] = [
                'id' => $id,
                'name' => $parent,
            ];
        }
        foreach ($field_types as $id => $field_type) {
            $field_types_arr[] = [
                'id' => $id,
                'name' => $field_type,
            ];
        }

		$this->fields_form = [
			'legend' => [
				'title' => $this->l('Info Field Form'),
            ],
			'input'  => [
				[
					'type'     => 'text',
					'label'    => $this->l('Field Name'),
					'name'     => 'field_name',
					'id'       => 'field_name',
					'size'     => 60,
					'required' => true,
					'desc'     => $this->l('This will appear as the field name on your form.'),
					'lang'     => true,
                ],
				[
					'type'         => 'textarea',
					'label'        => $this->l('Global Metadata'),
					'name'         => 'global_meta_data',
					'lang'         => true,
					'rows'         => 10,
					'cols'         => 62,
					'class'        => 'rte',
					'autoload_rte' => true,
					'desc'         => $this->l('This will act as global meta data. Specific meta will overwrite it.'),
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
					'type'     => 'text',
					'label'    => $this->l('Shortcode'),
					'name'     => 'shortcode',
					'id'       => 'shortcode',
					'size'     => 60,
					'desc'     => $this->l('Use this in your tpl file to show the meta info.'),
					'disabled'  => true,
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save'),
            ]
        ];

        if (isset($_GET['id_infofields']) && $_GET['id_infofields'] > 0) {
            $lang_id = Context::getContext()->language->id;
            $alias = ucfirst(str_replace(' ', '', $obj->field_name[$lang_id]));
            $id = $obj->id_infofields;
            $this->fields_value['shortcode'] = "{hook h='displayInfofield$alias id_infofields=$id'}";
        }
		return parent::renderForm();
	}

    public function getParentName($key) {
        $parents = $this->getParentItems();
        return $parents[$key];
    }

    public function getFieldtypeName($key) {
        $field_types = $this->getFieldTypes();
        return $field_types[$key];
    }

    public function getParentItems() {
        return [
            1 => $this->module->l('Category'),
            2 => $this->module->l('Product'),
            3 => $this->module->l('CMS Page'),
            4 => $this->module->l('Customer'),
        ];
    }

    public function getFieldTypes() {
        return [
            1 => $this->module->l('Text Field'),
            2 => $this->module->l('Rich Text Field'),
            3 => $this->module->l('Image'),
            4 => $this->module->l('File'),
        ];
    }
}
