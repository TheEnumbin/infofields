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
        $this->lang = false;
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
                'type' => 'integer'
            ],
            'field_name' => [
                'title' => $this->module->l('Name'),
                'align' => 'center',
                'type' => 'string'
            ],
            'parent_item' => [
                'title' => $this->module->l('Parent Item'),
                'align' => 'center',
                'type' => 'string'
            ],
            'field_type' => [
                'title' => $this->module->l('Field Type'),
                'align' => 'center',
                'type' => 'string'
            ],
        ];

        return parent::renderList();
    }

    public function renderForm()
	{

		$languages = Language::getLanguages(false);

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
                        'query' => [
                            [
                                'id' => 1,
                                'name' => $this->l('Category'),
                            ],
                            [
                                'id' => 2,
                                'name' => $this->l('Product'),
                            ],
                            [
                                'id' => 3,
                                'name' => $this->l('CMS Page'),
                            ],
                            [
                                'id' => 4,
                                'name' => $this->l('Customer'),
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Field Type'),
                    'name' => 'field_type',
                    'options' => [
                        'query' => [
                            [
                                'id' => 1,
                                'name' => $this->l('Text Field'),
                            ],
                            [
                                'id' => 2,
                                'name' => $this->l('Rich Text Field'),
                            ],
                            [
                                'id' => 3,
                                'name' => $this->l('Image'),
                            ],
                            [
                                'id' => 4,
                                'name' => $this->l('File'),
                            ],
                        ],
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
            ],
            'submit' => [
                'title' => $this->module->l('Save'),
            ]
        ];
		return parent::renderForm();
	}
}
