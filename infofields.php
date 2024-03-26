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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_')) {
    exit;
}



require_once(dirname(__FILE__) . '/classes/FieldsModel.php');
require_once(dirname(__FILE__) . '/classes/MetaModel.php');
require_once(dirname(__FILE__) . '/includes/InfofieldBuilder.php');

class Infofields extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'infofields';
        $this->version = '1.0.0';
        $this->tab = 'pricing_promotion';
        $this->author = 'TheEnumbin';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Custom Info Fields: Create Advanced Meta Fields');
        $this->description = $this->l('Add extra fields to your Products, Categories, Customers, Pages.');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->define_constants();

        $parent_item = 3;
        $languages = Language::getLanguages(false);
        $langs = [];
        foreach ($languages as $lang) {
            $langs[$lang['id_lang']] = $lang['iso_code'];
        }
        $fieldsmodel = new FieldsModel();
        $fields = $fieldsmodel->get_infofield_by_parent_item($parent_item);
        $metamodel = new MetaModel();
        $metas = $metamodel->get_meta_by_parent(1, $fields);
        echo '<pre>';
        print_r($metas);
        echo '</pre>';
        echo __FILE__ . ' : ' . __LINE__;
        // $this->registerHook('actionObjectCmsUpdateAfter');
    }

    private function define_constants()
    {

        if(!defined('INFOFIELDS_CLASSES_PATH')) {
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
        Configuration::updateValue('INFOFIELDS_POSITION', 'after_price');
        Configuration::updateValue('INFOFIELDS_BACK_COLOR', '#b3a700');
        Configuration::updateValue('INFOFIELDS_FONT_COLOR', '#ffffff');

        include _PS_MODULE_DIR_ . $this->name . '/sql/install.php';
        include _PS_MODULE_DIR_ . $this->name . '/sql/install_tabs.php';

        return parent::install() &&
            $this->registerHook('actionCmsPageFormBuilderModifier') &&
            $this->registerHook('actionObjectCmsUpdateAfter') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('displayInfofield') &&
            $this->registerHook('displayProductPriceBlock');
    }

    /**
     * This methos is called when uninstalling the module.
     */
    public function uninstall()
    {
        include _PS_MODULE_DIR_ . $this->name . '/sql/uninstall.php';
        include _PS_MODULE_DIR_ . $this->name . '/sql/uninstall_tabs.php';

        Configuration::deleteByName('INFOFIELDS_POSITION');
        Configuration::deleteByName('INFOFIELDS_BACK_COLOR');
        Configuration::deleteByName('INFOFIELDS_FONT_SIZE');
        Configuration::deleteByName('INFOFIELDS_PADDING');
        Configuration::deleteByName('INFOFIELDS_FONT_COLOR');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (((bool) Tools::isSubmit('submitInfofieldsModule')) == true) {
            $this->postProcess();
        }

        return $this->renderForm();
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
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->l('Select Notice Position'),
                        'name' => 'INFOFIELDS_POSITION',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'after_price',
                                    'name' => $this->l('After Price'),
                                ],
                                [
                                    'id' => 'old_price',
                                    'name' => $this->l('Before Old Price'),
                                ],
                                [
                                    'id' => 'footer_product',
                                    'name' => $this->l('Footer Product'),
                                ],
                                [
                                    'id' => 'product_bts',
                                    'name' => $this->l('After Product Buttons'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'tab' => 'content_tab',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background Color'),
                        'name' => 'INFOFIELDS_BACK_COLOR',
                        'tab' => 'content_tab',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Text Color'),
                        'name' => 'INFOFIELDS_FONT_COLOR',
                        'tab' => 'content_tab',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your font size like "12px"'),
                        'name' => 'INFOFIELDS_FONT_SIZE',
                        'label' => $this->l('Font Size'),
                        'tab' => 'content_tab',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your padding like "6px"'),
                        'name' => 'INFOFIELDS_PADDING',
                        'label' => $this->l('Padding'),
                        'tab' => 'content_tab',
                    ],
                ],
                'tabs' => [
                    'content_tab' => 'Content & Design',
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
            'INFOFIELDS_POSITION' => Configuration::get('INFOFIELDS_POSITION', 'after_price'),
            'INFOFIELDS_BACK_COLOR' => Configuration::get('INFOFIELDS_BACK_COLOR', '#b3a700'),
            'INFOFIELDS_FONT_COLOR' => Configuration::get('INFOFIELDS_FONT_COLOR', '#ffffff'),
            'INFOFIELDS_FONT_SIZE' => Configuration::get('INFOFIELDS_FONT_SIZE', '12px'),
            'INFOFIELDS_PADDING' => Configuration::get('INFOFIELDS_PADDING', '6px'),
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
            if ($key == 'INFOFIELDS_POSITION') {
                if (Tools::getValue($key) == 'footer_product') {
                    $this->registerHook('displayFooterProduct');
                    $this->unregisterHook('displayProductButtons');
                    $this->unregisterHook('displayProductPriceBlock');
                } elseif (Tools::getValue($key) == 'product_bts') {
                    $this->registerHook('displayProductButtons');
                    $this->unregisterHook('displayFooterProduct');
                    $this->unregisterHook('displayProductPriceBlock');
                } else {
                    $this->registerHook('displayProductPriceBlock');
                    $this->unregisterHook('displayFooterProduct');
                    $this->unregisterHook('displayProductButtons');
                }
                Configuration::updateValue($key, Tools::getValue($key));
            } else {
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }

        $infofields_back_color = Configuration::get('INFOFIELDS_BACK_COLOR', '#b3a700');
        $infofields_font_color = Configuration::get('INFOFIELDS_FONT_COLOR', '#ffffff');
        $infofields_font_size = Configuration::get('INFOFIELDS_FONT_SIZE', '12px');
        $infofields_padding = Configuration::get('INFOFIELDS_PADDING', '6px');
        $gen_css = '.prextrameta-notice{
                        padding: ' . $infofields_padding . ' !important;
                        font-size: ' . $infofields_font_size . ' !important;
                        color: ' . $infofields_font_color . ' !important;
                        background: ' . $infofields_back_color . ' !important;
                    }';

        file_put_contents(_PS_MODULE_DIR_ . $this->name . '/views/css/front_generated.css', $gen_css);
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');
        Media::addJsDef([
            'infofields_ajax_url' => $this->context->link->getAdminLink('AdminAjaxInfofields'),
        ]);
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
        ]);
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/fields_form.tpl');

        return $output;
    }

    public function hookActionCmsPageFormBuilderModifier(array $params)
    {
        $id_cms = $params['id'];
        $parent_item = 3;
        $fieldsmodel = new FieldsModel();
        $fields = $fieldsmodel->get_infofield_by_parent_item($parent_item);
        $metamodel = new MetaModel();
        $metas = $metamodel->get_meta_by_parent($id_cms, $fields);
        $builder = new InfofieldBuilder();
        $builder->inf_build_form($params['form_builder'], $fields, $metas);
    }

    // public function hookActionObjectUpdateAfter($params)
    // {
    //     // echo '<pre>';
    //     // print_r($params['object']);
    //     // echo '</pre>';
    //     // echo __FILE__ . ' : ' . __LINE__;
    //     // // die(__FILE__ . ' : ' . __LINE__);
    // }

    public function hookActionObjectCmsUpdateAfter($params)
    {
        $data = Tools::getValue('cms_page');
        $inf_ids = $data['inf_infofield_ids'];
        $inf_ids = explode(",", $inf_ids);
        $cms_obj = $params['object'];

        if (!empty($inf_ids)) {
            foreach ($inf_ids as $inf_id) {
                $object = new MetaModel(null, $inf_id, $cms_obj->id);
                if(isset($object->id)) {
                    $object->meta_data = $data['inf_metafield_' . $inf_id];
                    $object->update();
                } else {
                    $object->id_infofields = $inf_id;
                    $object->parent_item_id = $cms_obj->id;
                    $object->meta_data = $data['inf_metafield_' . $inf_id];
                    $object->add();
                }
            }
        }
    }


    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path . '/views/css/front_generated.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    /**
     * Call back function for the  hook DisplayInfofield
     */
    public function hookDisplayInfofield($params)
    {
        $inf_ids = $params['id_infofields'];
        $prd_id = $params['product_id'];
        $id_lang = $this->context->language->id;
        $fields = [];
        $index = 0;
        if(is_array($inf_ids)) {

        } else {
            $fields[$index]['id_infofields'] = $inf_ids;
            $fields[$index]['id_lang'] = $id_lang;
        }
        $metamodel = new MetaModel();
        $metas = $metamodel->get_meta_by_parent($prd_id, $fields, $id_lang);

        $this->context->smarty->assign([
            'infofields_metas' => $metas,
            'lang_id' => $id_lang,
        ]);
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/front/infofield.tpl');

        echo $output;

    }

    /**
     * Call back function for the  hook DisplayProductPriceBlock
     */
    public function hookDisplayProductPriceBlock($params)
    {
        $product = $params['product'];
        $infofields_meta = $this->infofields_init($product);

        if ($infofields_meta) {
            $infofields_pos = Configuration::get('INFOFIELDS_POSITION', 'after_price');

            if ($params['type'] == $infofields_pos) {
                $this->infofields_show_notice($infofields_meta);
            }
        }
    }

    /**
     * Call back function for the  hook DisplayFooterProduct
     */
    public function hookDisplayFooterProduct($params)
    {
        $product = $params['product'];
        $infofields_meta = $this->infofields_init($product);

        if ($infofields_meta) {
            $this->infofields_show_notice($infofields_meta);
        }
    }

    /**
     * Call back function for the  hook DisplayProductButtons
     */
    public function hookDisplayProductButtons($params)
    {
        $product = $params['product'];
        $infofields_meta = $this->infofields_init($product);

        if ($infofields_meta) {
            $this->infofields_show_notice($infofields_meta);
        }
    }

    /**
     * Returns the Omnibus Price if poduct has promotion
     */
    private function infofields_init($product)
    {
        $controller = Tools::getValue('controller');

        $id_product = $product->id;

        $lang_id = $this->context->language->id;
        $shop_id = $this->context->shop->id;
        $infofields_meta = false;
        $mresults = Db::getInstance()->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'pricing_extrameta` pemt
            WHERE pemt.`lang_id` = ' . (int) $lang_id . ' AND' . ' pemt.`shop_id` = ' . (int) $shop_id . ' AND pemt.`product_id` = ' . (int) $id_product,
            true
        );

        if(isset($mresults) && !empty($mresults)) {
            $infofields_meta = array_pop($mresults);
            return $infofields_meta['meta_data'];
        }

        return false;
    }


    /**
     * Shows the notice
     */
    private function infofields_show_notice($infofields_text)
    {
        $lang_id = $this->context->language->id;

        $this->context->smarty->assign([
            'infofields_text' => $infofields_text,
        ]);
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/front/prextrameta.tpl');

        echo $output;
    }
}
