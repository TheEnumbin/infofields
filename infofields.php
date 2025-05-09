<?php

/**
 * 2007-2022 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2022 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/FieldsModel.php';
require_once dirname(__FILE__) . '/classes/MetaModel.php';
require_once dirname(__FILE__) . '/includes/InfofieldBuilder.php';
require_once dirname(__FILE__) . '/includes/InfofieldHelper.php';

class Infofields extends Module
{
    use infofieldHelper;
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'infofields';
        $this->version = '1.3.2';
        $this->tab = 'pricing_promotion';
        $this->author = 'TheEnumbin';
        $this->need_instance = 0;
        $this->module_key = '573462b9857b4ede3a667c2b6d431205';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Info Fields: Create Custom Advanced Meta Fields');
        $this->description = $this->l('Add extra fields to your Products, Categories, Customers, Pages.');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->define_constants();
    }

    private function define_constants()
    {
        if (!defined('INFOFIELDS_CLASSES_PATH')) {
            define('INFOFIELDS_CLASSES_PATH', _PS_MODULE_DIR_ . 'infofields/classes/');
        }
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        $date = date('Y-m-d');

        include _PS_MODULE_DIR_ . $this->name . '/sql/install.php';
        include _PS_MODULE_DIR_ . $this->name . '/sql/install_tabs.php';

        return parent::install()
            && $this->registerHook('actionCategoryFormBuilderModifier')
            && $this->registerHook('actionCmsPageFormBuilderModifier')
            && $this->registerHook('actionCustomerFormBuilderModifier')
            && $this->registerHook('actionObjectCmsUpdateAfter')
            && $this->registerHook('actionObjectCategoryUpdateAfter')
            && $this->registerHook('actionObjectCustomerUpdateAfter')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayAdminProductsExtra')
            && $this->registerHook('displayInfofield');
    }

    /**
     * This methos is called when uninstalling the module.
     */
    public function uninstall()
    {
        include _PS_MODULE_DIR_ . $this->name . '/sql/uninstall.php';
        include _PS_MODULE_DIR_ . $this->name . '/sql/uninstall_tabs.php';
        Configuration::deleteByName('INFOFIELDS_PRD_ORIENTATION');
        Configuration::deleteByName('INFOFIELDS_PRD_BACK_COLOR');
        Configuration::deleteByName('INFOFIELDS_PRD_FONT_SIZE');
        Configuration::deleteByName('INFOFIELDS_PRD_PADDING');
        Configuration::deleteByName('INFOFIELDS_PRD_FONT_COLOR');
        Configuration::deleteByName('INFOFIELDS_CTG_ORIENTATION');
        Configuration::deleteByName('INFOFIELDS_CTG_BACK_COLOR');
        Configuration::deleteByName('INFOFIELDS_CTG_FONT_SIZE');
        Configuration::deleteByName('INFOFIELDS_CTG_PADDING');
        Configuration::deleteByName('INFOFIELDS_CTG_FONT_COLOR');
        Configuration::deleteByName('INFOFIELDS_CUST_ORIENTATION');
        Configuration::deleteByName('INFOFIELDS_CUST_BACK_COLOR');
        Configuration::deleteByName('INFOFIELDS_CUST_FONT_SIZE');
        Configuration::deleteByName('INFOFIELDS_CUST_PADDING');
        Configuration::deleteByName('INFOFIELDS_CUST_FONT_COLOR');
        Configuration::deleteByName('INFOFIELDS_CMS_ORIENTATION');
        Configuration::deleteByName('INFOFIELDS_CMS_BACK_COLOR');
        Configuration::deleteByName('INFOFIELDS_CMS_FONT_SIZE');
        Configuration::deleteByName('INFOFIELDS_CMS_PADDING');
        Configuration::deleteByName('INFOFIELDS_CMS_FONT_COLOR');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';
        if (((bool) Tools::isSubmit('submitInfofieldsModule')) == true) {
            $this->postProcess();
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        $advertise = $this->advertise_template();

        return $output . $this->renderForm() . $advertise;
    }

    protected function advertise_template()
    {
        // Fetch and render the template file
        $this->context->smarty->assign('module_dir', $this->_path);
        return $this->context->smarty->fetch('module:infofields/views/templates/admin/advertise_template.tpl');
    }
    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitInfofieldsModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $this->context->smarty->assign('import_txt', $this->l('Import Now!!!'));
        $this->context->smarty->assign('stop_import', $this->l('Stop'));
        $this->context->smarty->assign('field_sample_url', $this->_path . '/data/fields_sample.csv');
        $this->context->smarty->assign('meta_sample_url', $this->_path . '/data/meta_sample.csv');
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    // Import/Export Fields
                    [
                        'type' => 'file',
                        'label' => $this->l('Custom Fields CSV'),
                        'name' => 'INFOFIELDS_CSV_IMPORT',
                        'desc' => $this->l('Upload a CSV file to import custom fields.'),
                        'form_group_class' => 'inf-csv-input',
                        'tab' => 'import_export',
                    ],
                    [
                        'type' => 'hidden',
                        'name' => 'INFOFIELDS_CSV_FIELD_TYPE',
                        'form_group_class' => 'inf-csv-type',
                        'tab' => 'import_export',
                    ],
                    [
                        'type' => 'html',
                        'label' => '',
                        'name' => 'INFOFIELDS_IMPORT_BTNS',
                        'html_content' => $this->context->smarty->fetch($this->local_path . 'views/templates/admin/import_button.tpl'),
                        'form_group_class' => 'inf-csv-bt',
                        'tab' => 'import_export',
                    ],
                    [
                        'type' => 'html',
                        'name' => 'INFOFIELDS_DIVIDER_HTML',
                        'html_content' => $this->context->smarty->fetch($this->local_path . 'views/templates/admin/divider.tpl'),
                        'tab' => 'import_export',
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Identify Product By'),
                        'name' => 'INFOFIELDS_PRD_BY_TYPE',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'id',
                                    'name' => $this->l('Product ID'),
                                ],
                                [
                                    'id' => 'reference',
                                    'name' => $this->l('Reference Key/Id'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'form_group_class' => 'inf-prd-identifier',
                        'desc' => $this->l('Select How You Want to Import Product Data. With Reference Id or Product Id'),
                        'tab' => 'import_export',
                    ],
                    [
                        'type' => 'file',
                        'label' => $this->l('Meta CSV'),
                        'name' => 'INFOFIELDS_META_CSV',
                        'desc' => $this->l('CSV for product custom data for fields'),
                        'form_group_class' => 'inf-csv-input',
                        'tab' => 'import_export',
                    ],
                    [
                        'type' => 'hidden',
                        'name' => 'INFOFIELDS_CSV_META_TYPE',
                        'form_group_class' => 'inf-csv-type',
                        'tab' => 'import_export',
                    ],
                    [
                        'type' => 'html',
                        'label' => '',
                        'name' => 'INFOFIELDS_IMPORT_BTNS',
                        'html_content' => $this->context->smarty->fetch($this->local_path . 'views/templates/admin/import_button.tpl'),
                        'form_group_class' => 'inf-csv-bt',
                        'tab' => 'import_export',
                    ],
                    [
                        'type' => 'html',
                        'name' => 'INFOFIELDS_DIVIDER_HTML',
                        'html_content' => $this->context->smarty->fetch($this->local_path . 'views/templates/admin/divider.tpl'),
                        'tab' => 'import_export',
                    ],
                    [
                        'type' => 'html',
                        'label' => 'Download Sample CSV Files',
                        'name' => 'INFOFIELDS_IMPORT_SAMPLES',
                        'html_content' => $this->context->smarty->fetch($this->local_path . 'views/templates/admin/import_sample.tpl'),
                        'tab' => 'import_export',
                    ],
                    // Product Design
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show meta as extra tab'),
                        'name' => 'INFOFIELDS_PRD_EXTRATAB_HOOK',
                        'values' => [
                            [
                                'id' => 'enable',
                                'value' => true,
                                'label' => $this->l('Enable'),
                            ],
                            [
                                'id' => 'disable',
                                'value' => false,
                                'label' => $this->l('Disable'),
                            ],
                        ],
                        'tab' => 'peoduct_design',
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Label and Meta Orientation'),
                        'name' => 'INFOFIELDS_PRD_ORIENTATION',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'column',
                                    'name' => $this->l('Stacked'),
                                ],
                                [
                                    'id' => 'row',
                                    'name' => $this->l('Inline'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'tab' => 'peoduct_design',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background Color'),
                        'name' => 'INFOFIELDS_PRD_BACK_COLOR',
                        'tab' => 'peoduct_design',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Text Color'),
                        'name' => 'INFOFIELDS_PRD_FONT_COLOR',
                        'tab' => 'peoduct_design',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your font size like "12px"'),
                        'name' => 'INFOFIELDS_PRD_FONT_SIZE',
                        'label' => $this->l('Font Size'),
                        'tab' => 'peoduct_design',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your padding like "6px"'),
                        'name' => 'INFOFIELDS_PRD_PADDING',
                        'label' => $this->l('Padding'),
                        'tab' => 'peoduct_design',
                    ],
                    // Category Design
                    [
                        'type' => 'select',
                        'label' => $this->l('Label and Meta Orientation'),
                        'name' => 'INFOFIELDS_CTG_ORIENTATION',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'row',
                                    'name' => $this->l('Inline'),
                                ],
                                [
                                    'id' => 'column',
                                    'name' => $this->l('Stacked'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'tab' => 'category_design',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background Color'),
                        'name' => 'INFOFIELDS_CTG_BACK_COLOR',
                        'tab' => 'category_design',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Text Color'),
                        'name' => 'INFOFIELDS_CTG_FONT_COLOR',
                        'tab' => 'category_design',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your font size like "12px"'),
                        'name' => 'INFOFIELDS_CTG_FONT_SIZE',
                        'label' => $this->l('Font Size'),
                        'tab' => 'category_design',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your padding like "6px"'),
                        'name' => 'INFOFIELDS_CTG_PADDING',
                        'label' => $this->l('Padding'),
                        'tab' => 'category_design',
                    ],
                    // Customer Design Fields
                    [
                        'type' => 'select',
                        'label' => $this->l('Label and Meta Orientation'),
                        'name' => 'INFOFIELDS_CUST_ORIENTATION',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'row',
                                    'name' => $this->l('Inline'),
                                ],
                                [
                                    'id' => 'column',
                                    'name' => $this->l('Stacked'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'tab' => 'customer_design',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background Color'),
                        'name' => 'INFOFIELDS_CUST_BACK_COLOR',
                        'tab' => 'customer_design',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Text Color'),
                        'name' => 'INFOFIELDS_CUST_FONT_COLOR',
                        'tab' => 'customer_design',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your font size like "12px"'),
                        'name' => 'INFOFIELDS_CUST_FONT_SIZE',
                        'label' => $this->l('Font Size'),
                        'tab' => 'customer_design',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your padding like "6px"'),
                        'name' => 'INFOFIELDS_CUST_PADDING',
                        'label' => $this->l('Padding'),
                        'tab' => 'customer_design',
                    ],
                    // CMS Design Fields
                    [
                        'type' => 'select',
                        'label' => $this->l('Label and Meta Orientation'),
                        'name' => 'INFOFIELDS_CMS_ORIENTATION',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'row',
                                    'name' => $this->l('Inline'),
                                ],
                                [
                                    'id' => 'column',
                                    'name' => $this->l('Stacked'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'tab' => 'customer_design',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background Color'),
                        'name' => 'INFOFIELDS_CMS_BACK_COLOR',
                        'tab' => 'cms_design',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Text Color'),
                        'name' => 'INFOFIELDS_CMS_FONT_COLOR',
                        'tab' => 'cms_design',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your font size like "12px"'),
                        'name' => 'INFOFIELDS_CMS_FONT_SIZE',
                        'label' => $this->l('Font Size'),
                        'tab' => 'cms_design',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your padding like "6px"'),
                        'name' => 'INFOFIELDS_CMS_PADDING',
                        'label' => $this->l('Padding'),
                        'tab' => 'cms_design',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Reset Fields Data'),
                        'name' => 'INFOFIELDS_RESET_FIELDDATA',
                        'values' => [
                            [
                                'id' => 'yes',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'no',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'action',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Reset All Meta Data'),
                        'name' => 'INFOFIELDS_RESET_METADATA',
                        'values' => [
                            [
                                'id' => 'yes',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'no',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'action',
                    ],
                ],
                'tabs' => [
                    'import_export' => 'Import',
                    'peoduct_design' => 'Product Meta Settings',
                    'category_design' => 'Category Meta Settings',
                    'customer_design' => 'Customer Meta Settings',
                    'cms_design' => 'Cms Meta Settings',
                    'action' => 'Actions',
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $ret_arr = [
            'INFOFIELDS_PRD_EXTRATAB_HOOK' => Configuration::get('INFOFIELDS_PRD_EXTRATAB_HOOK', false),
            'INFOFIELDS_PRD_ORIENTATION' => Configuration::get('INFOFIELDS_PRD_ORIENTATION', 'row'),
            'INFOFIELDS_PRD_BY_TYPE' => Configuration::get('INFOFIELDS_PRD_BY_TYPE', 'id'),
            'INFOFIELDS_PRD_BACK_COLOR' => Configuration::get('INFOFIELDS_PRD_BACK_COLOR', ''),
            'INFOFIELDS_PRD_FONT_COLOR' => Configuration::get('INFOFIELDS_PRD_FONT_COLOR', ''),
            'INFOFIELDS_PRD_FONT_SIZE' => Configuration::get('INFOFIELDS_PRD_FONT_SIZE', ''),
            'INFOFIELDS_PRD_PADDING' => Configuration::get('INFOFIELDS_PRD_PADDING', ''),
            'INFOFIELDS_CTG_ORIENTATION' => Configuration::get('INFOFIELDS_CTG_ORIENTATION', 'row'),
            'INFOFIELDS_CTG_BACK_COLOR' => Configuration::get('INFOFIELDS_CTG_BACK_COLOR', ''),
            'INFOFIELDS_CTG_FONT_COLOR' => Configuration::get('INFOFIELDS_CTG_FONT_COLOR', ''),
            'INFOFIELDS_CTG_FONT_SIZE' => Configuration::get('INFOFIELDS_CTG_FONT_SIZE', ''),
            'INFOFIELDS_CTG_PADDING' => Configuration::get('INFOFIELDS_CTG_PADDING', ''),
            'INFOFIELDS_CUST_ORIENTATION' => Configuration::get('INFOFIELDS_CUST_ORIENTATION', 'row'),
            'INFOFIELDS_CUST_BACK_COLOR' => Configuration::get('INFOFIELDS_CUST_BACK_COLOR', ''),
            'INFOFIELDS_CUST_FONT_COLOR' => Configuration::get('INFOFIELDS_CUST_FONT_COLOR', ''),
            'INFOFIELDS_CUST_FONT_SIZE' => Configuration::get('INFOFIELDS_CUST_FONT_SIZE', ''),
            'INFOFIELDS_CUST_PADDING' => Configuration::get('INFOFIELDS_CUST_PADDING', ''),
            'INFOFIELDS_CMS_ORIENTATION' => Configuration::get('INFOFIELDS_CMS_ORIENTATION', 'row'),
            'INFOFIELDS_CMS_BACK_COLOR' => Configuration::get('INFOFIELDS_CMS_BACK_COLOR', ''),
            'INFOFIELDS_CMS_FONT_COLOR' => Configuration::get('INFOFIELDS_CMS_FONT_COLOR', ''),
            'INFOFIELDS_CMS_FONT_SIZE' => Configuration::get('INFOFIELDS_CMS_FONT_SIZE', ''),
            'INFOFIELDS_CMS_PADDING' => Configuration::get('INFOFIELDS_CMS_PADDING', ''),
            'INFOFIELDS_RESET_FIELDDATA' => false,
            'INFOFIELDS_RESET_METADATA' => false,
            'INFOFIELDS_CSV_FIELD_TYPE' => '5',
            'INFOFIELDS_CSV_META_TYPE' => '2',
            // 'INFOFIELDS_CSV_PRD_TYPE' => '2',
            // 'INFOFIELDS_CSV_CTG_TYPE' => '1',
            // 'INFOFIELDS_CSV_CST_TYPE' => '3',
            // 'INFOFIELDS_CSV_CMS_TYPE' => '4',
        ];

        return $ret_arr;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        $lang_id = $this->context->language->id;

        foreach (array_keys($form_values) as $key) {
            if ($key == 'INFOFIELDS_PRD_EXTRATAB_HOOK') {
                if (Tools::getValue($key) == true) {
                    if (!$this->isRegisteredInHook('displayProductExtraContent')) {
                        $this->registerHook('displayProductExtraContent');
                    }
                } else {
                    if ($this->isRegisteredInHook('displayProductExtraContent')) {
                        $this->unregisterHook('displayProductExtraContent');
                    }
                }
            } elseif ($key == 'INFOFIELDS_RESET_FIELDDATA') {
                if (Tools::getValue($key)) {
                    Db::getInstance()->execute('SET FOREIGN_KEY_CHECKS = 0');
                    Db::getInstance()->execute('TRUNCATE `' . _DB_PREFIX_ . 'infofields_shop`');
                    Db::getInstance()->execute('TRUNCATE `' . _DB_PREFIX_ . 'infofields_lang`');
                    Db::getInstance()->execute('TRUNCATE `' . _DB_PREFIX_ . 'infofields`');
                    Db::getInstance()->execute('SET FOREIGN_KEY_CHECKS = 1');
                }
            } elseif ($key == 'INFOFIELDS_RESET_METADATA') {
                if (Tools::getValue($key)) {
                    Db::getInstance()->execute('SET FOREIGN_KEY_CHECKS = 0');
                    Db::getInstance()->execute('TRUNCATE `' . _DB_PREFIX_ . 'infofields_meta_lang`');
                    Db::getInstance()->execute('TRUNCATE `' . _DB_PREFIX_ . 'infofields_meta`');
                    Db::getInstance()->execute('SET FOREIGN_KEY_CHECKS = 1');
                }
            }
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $infofields_prd_alignment = 'center';
        $infofields_prd_orientation = Configuration::get('INFOFIELDS_PRD_ORIENTATION', 'row');
        $infofields_prd_back_color = Configuration::get('INFOFIELDS_PRD_BACK_COLOR', '');
        $infofields_prd_font_color = Configuration::get('INFOFIELDS_PRD_FONT_COLOR', '');
        $infofields_prd_font_size = Configuration::get('INFOFIELDS_PRD_FONT_SIZE', '');
        $infofields_prd_padding = Configuration::get('INFOFIELDS_PRD_PADDING', '');
        if ($infofields_prd_orientation == 'column') {
            $infofields_prd_alignment = 'flex-start';
        }

        $infofields_ctg_alignment = 'center';
        $infofields_ctg_orientation = Configuration::get('INFOFIELDS_CTG_ORIENTATION', 'row');
        $infofields_ctg_back_color = Configuration::get('INFOFIELDS_CTG_BACK_COLOR', '');
        $infofields_ctg_font_color = Configuration::get('INFOFIELDS_CTG_FONT_COLOR', '');
        $infofields_ctg_font_size = Configuration::get('INFOFIELDS_CTG_FONT_SIZE', '');
        $infofields_ctg_padding = Configuration::get('INFOFIELDS_CTG_PADDING', '');
        if ($infofields_ctg_orientation == 'column') {
            $infofields_ctg_alignment = 'flex-start';
        }

        $infofields_cust_alignment = 'center';
        $infofields_cust_orientation = Configuration::get('INFOFIELDS_CUST_ORIENTATION', '');
        $infofields_cust_back_color = Configuration::get('INFOFIELDS_CUST_BACK_COLOR', '');
        $infofields_cust_font_color = Configuration::get('INFOFIELDS_CUST_FONT_COLOR', '');
        $infofields_cust_font_size = Configuration::get('INFOFIELDS_CUST_FONT_SIZE', '');
        $infofields_cust_padding = Configuration::get('INFOFIELDS_CUST_PADDING', '');
        if ($infofields_cust_orientation == 'column') {
            $infofields_cust_alignment = 'flex-start';
        }

        $infofields_cms_alignment = 'center';
        $infofields_cms_orientation = Configuration::get('INFOFIELDS_CMS_ORIENTATION', '');
        $infofields_cms_back_color = Configuration::get('INFOFIELDS_CMS_BACK_COLOR', '');
        $infofields_cms_font_color = Configuration::get('INFOFIELDS_CMS_FONT_COLOR', '');
        $infofields_cms_font_size = Configuration::get('INFOFIELDS_CMS_FONT_SIZE', '');
        $infofields_cms_padding = Configuration::get('INFOFIELDS_CMS_PADDING', '');
        if ($infofields_cms_orientation == 'column') {
            $infofields_cms_alignment = 'flex-start';
        }
        $gen_css = '.infofield-wrapper .infofield-product-meta{
                        flex-direction: ' . $infofields_prd_orientation . ';
                        align-items: ' . $infofields_prd_alignment . ';
                        padding: ' . $infofields_prd_padding . ' !important;
                        font-size: ' . $infofields_prd_font_size . ' !important;
                        color: ' . $infofields_prd_font_color . ' !important;
                        background: ' . $infofields_prd_back_color . ' !important;
                    }
                    .infofield-wrapper .infofield-category-meta{
                        flex-direction: ' . $infofields_ctg_orientation . ';
                        align-items: ' . $infofields_ctg_alignment . ';
                        padding: ' . $infofields_ctg_padding . ' !important;
                        font-size: ' . $infofields_ctg_font_size . ' !important;
                        color: ' . $infofields_ctg_font_color . ' !important;
                        background: ' . $infofields_ctg_back_color . ' !important;
                    }
                    .infofield-wrapper .infofield-customer-meta{
                        flex-direction: ' . $infofields_cust_orientation . ';
                        align-items: ' . $infofields_cust_alignment . ';
                        padding: ' . $infofields_cust_padding . ' !important;
                        font-size: ' . $infofields_cust_font_size . ' !important;
                        color: ' . $infofields_cust_font_color . ' !important;
                        background: ' . $infofields_cust_back_color . ' !important;
                    }
                    .infofield-wrapper .infofield-cms-meta{
                        flex-direction: ' . $infofields_cms_orientation . ';
                        align-items: ' . $infofields_cms_alignment . ';
                        padding: ' . $infofields_cms_padding . ' !important;
                        font-size: ' . $infofields_cms_font_size . ' !important;
                        color: ' . $infofields_cms_font_color . ' !important;
                        background: ' . $infofields_cms_back_color . ' !important;
                    }
                    ';
        $this->generateCustomCSS($gen_css);
        $this->_clearCache('*');
    }

    /**
     * Generate the custom CSS securely using allowlist
     *
     * @param string $css_content The raw CSS content to save
     * @return bool
     * @throws Exception
     */
    public function generateCustomCSS($css_content)
    {
        $base_path = _PS_MODULE_DIR_ . $this->name . '/views/css/';
        $file_name = 'front_generated.css';

        // Validate the directory path using an allowlist
        $allowed_files = ['front_generated.css']; // Define allowed filenames
        if (!in_array($file_name, $allowed_files, true)) {
            throw new Exception('Invalid file name.');
        }

        // Validate and sanitize the CSS content
        $sanitized_css = $this->sanitizeCssContent($css_content);

        // Ensure the file path is within the allowed directory
        $css_path = realpath($base_path . $file_name);
        if (strpos($css_path, realpath($base_path)) !== 0) {
            throw new Exception('Invalid file path.');
        }

        // Save the CSS file
        return file_put_contents($css_path, $sanitized_css) !== false;
    }

    /**
     * Sanitize the CSS content
     *
     * @param string $css_content
     * @return string
     */
    private function sanitizeCssContent($css_content)
    {
        $css_content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $css_content);

        return $css_content;
    }

    public function inf_update_object($obj, $data, $parent_type)
    {
        $inf_ids = $data['inf_infofield_ids'];
        $inf_ids = json_decode($inf_ids, true);

        if (!empty($inf_ids)) {
            foreach ($inf_ids as $inf_id => $field_type) {
                $meta_updated = $this->infofield_meta_update($inf_id, $obj->id, $field_type, $parent_type, $data);

                if (!$meta_updated) {
                    continue;
                }
            }
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        $controller = Tools::getValue('controller');

        if ($controller == 'AdminProducts' || $controller == 'AdminInfoLists' || $controller == 'AdminCmsContent' || $controller == 'AdminCustomers' || $controller == 'AdminCategories' || Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            $this->context->controller->addJS($this->_path . 'views/js/admin.js');
            $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
            $defaultLanguage = new Language($defaultLangId);
            Media::addJsDef([
                'infofields_ajax_url' => $this->context->link->getAdminLink('AdminAjaxInfofields'),
                'infofields_def_iso_code' => $defaultLanguage->iso_code,
            ]);
        }
    }

    /**
     * Shows Price History List in Admin Product Page
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = $params['id_product'];
        $parent_item = 2;
        $languages = Language::getLanguages(false);
        $langs = [];
        foreach ($languages as $lang) {
            $langs[$lang['id_lang']] = $lang['iso_code'];
        }
        $fieldsmodel = new FieldsModel();
        $fields = $fieldsmodel->get_infofield_by_parent_item($parent_item);

        $metamodel = new MetaModel();
        $metas = $metamodel->get_meta_by_parent($id_product, $fields);
        $this->context->smarty->assign([
            'id_lang' => $this->context->language->id,
            'infofields' => $fields,
            'infometas' => $metas,
            'id_prd' => $id_product,
            'langs' => $langs,
            'img_dir' => _PS_IMG_,
        ]);
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/fields_form.tpl');

        return $output;
    }

    public function hookActionCategoryFormBuilderModifier(array $params)
    {
        $id_category = $params['id'];
        $builder = new InfofieldBuilder(1, $id_category);
        $builder->inf_build_form($params['form_builder'], $builder->get_fields(), $builder->get_metas());
    }

    public function hookActionCmsPageFormBuilderModifier(array $params)
    {
        $id_cms = $params['id'];
        $builder = new InfofieldBuilder(3, $id_cms);
        $builder->inf_build_form($params['form_builder'], $builder->get_fields(), $builder->get_metas());
    }

    public function hookActionCustomerFormBuilderModifier(array $params)
    {
        $id_customer = $params['id'];
        $builder = new InfofieldBuilder(4, $id_customer);
        $builder->inf_build_form($params['form_builder'], $builder->get_fields(), $builder->get_metas());
    }

    public function hookActionObjectCategoryUpdateAfter($params)
    {
        $data = Tools::getValue('category');
        $category_obj = $params['object'];
        $this->inf_update_object($category_obj, $data, 'category');
    }

    public function hookActionObjectCmsUpdateAfter($params)
    {
        $data = Tools::getValue('cms_page');
        $cms_obj = $params['object'];
        $this->inf_update_object($cms_obj, $data, 'cms');
    }

    public function hookActionObjectCustomerUpdateAfter($params)
    {
        $data = Tools::getValue('customer');
        $customer_obj = $params['object'];
        $this->inf_update_object($customer_obj, $data, 'customer');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . '/views/css/front_generated.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
    }

    /**
     * Call back function for the  hook DisplayInfofield
     */
    public function hookDisplayInfofield($params)
    {
        $inf_ids = $params['id_infofields'];
        $item_id = $params['item_id'];
        $width = 0;
        $height = 0;
        if (isset($params['width'])) {
            $width = $params['width'];
        }
        if (isset($params['height'])) {
            $height = $params['height'];
        }
        $id_lang = $this->context->language->id;
        $fields = [];
        $index = 0;
        if (is_array($inf_ids)) {
            // If have multiple ids
        } else {
            $fieldsmodel = new FieldsModel();
            $fields = $fieldsmodel->get_infofield_by_id($inf_ids, $id_lang);
        }
        $metamodel = new MetaModel();
        $metas = $metamodel->get_meta_by_parent($item_id, $fields, $id_lang);
        $this->context->smarty->assign([
            'infofields' => $fields,
            'infofields_metas' => $metas,
            'lang_id' => $id_lang,
            'hook_width' => $width,
            'hook_height' => $height,
            'img_dir' => _PS_IMG_ . 'infofield/',
        ]);
        $output = $this->context->smarty->fetch('module:infofields/views/templates/front/infofield.tpl');
        echo $output;
    }

    /**
     * HookdisplayProductExtraContent hook callback for the hook "displayProductExtraContent"
     */
    public function hookDisplayProductExtraContent($params)
    {
        $lang_id = Context::getContext()->language->id;
        $fieldsmodel = new FieldsModel();
        $fields = $fieldsmodel->get_infofield_by_parent_item(2, $lang_id, true);
        $id_product = Tools::getValue('id_product');
        $metamodel = new MetaModel();
        $metas = $metamodel->get_meta_by_parent($id_product, $fields, $lang_id);
        $array = [];
        foreach ($fields as $field) {
            if (!empty($metas[$field['id_infofields']][$lang_id])) {
                if ($metas[$field['id_infofields']][$lang_id]['meta_data'] != '') {
                    if ($field['field_type'] == '5') {
                        $this->context->smarty->assign('img_dir', _PS_IMG_ . 'infofield/');
                        $this->context->smarty->assign('infofield', $field);
                        $this->context->smarty->assign('infometa', $metas[$field['id_infofields']][$lang_id]['meta_data']);
                        $content = $this->context->smarty->fetch('module:infofields/views/templates/front/infofield_image.tpl');
                    } elseif ($field['field_type'] == '9') {
                        $this->context->smarty->assign('img_dir', _PS_IMG_ . 'infofield/');
                        $this->context->smarty->assign('infometa', $metas[$field['id_infofields']][$lang_id]['meta_data']);
                        $content = $this->context->smarty->fetch('module:infofields/views/templates/front/infofield_file.tpl');
                    } elseif ($field['field_type'] == '10') {
                        $this->context->smarty->assign('infofield', $field);
                        $this->context->smarty->assign('infofield', $field);
                        $this->context->smarty->assign('infometa', $metas[$field['id_infofields']][$lang_id]['meta_data']);
                        $content = $this->context->smarty->fetch('module:infofields/views/templates/front/infofield_video.tpl');
                    } else {
                        $content = $metas[$field['id_infofields']][$lang_id]['meta_data'];
                    }
                    $array[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())
                        ->setTitle($field['field_name'])
                        ->setContent($content);
                } elseif ($field['global_meta_data'] != '') {
                    $content = $field['global_meta_data'];
                    $array[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())
                        ->setTitle($field['field_name'])
                        ->setContent($content);
                }
            } elseif ($field['global_meta_data'] != '') {
                $content = $field['global_meta_data'];
                $array[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())
                    ->setTitle($field['field_name'])
                    ->setContent($content);
            }
        }
        return $array;
    }

    public function hookDisplayFooter()
    {
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/front/infofield_footer.tpl');
        echo $output;
    }
}
