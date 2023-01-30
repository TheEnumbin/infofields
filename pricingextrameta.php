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

class Pricingextrameta extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'pricingextrameta';
        $this->version = '1.0.0';
        $this->tab = 'pricing_promotion';
        $this->author = 'TheEnumbin';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Pricing Extra Meta');
        $this->description = $this->l('Share extra information about your product pricing.');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => '1.7.8.7'];
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        $date = date('Y-m-d');
        Configuration::updateValue('PREXTRAMETA_SHOW_ON', 'discounted');
        Configuration::updateValue('PREXTRAMETA_SHOW_ON_CATELOGUE', true);
        Configuration::updateValue('PREXTRAMETA_AUTO_DELETE_OLD', false);
        Configuration::updateValue('PREXTRAMETA_SHOW_IF_CURRENT', true);
        Configuration::updateValue('PREXTRAMETA_NOTICE_STYLE', 'before_after');
        Configuration::updateValue('PREXTRAMETA_POSITION', 'after_price');
        Configuration::updateValue('PREXTRAMETA_BACK_COLOR', '#b3a700');
        Configuration::updateValue('PREXTRAMETA_FONT_COLOR', '#ffffff');
        Configuration::updateValue('PREXTRAMETA_DELETE_DATE', $date);

        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            Configuration::updateValue('PREXTRAMETA_TEXT_MINI_' . $lang['id_lang'], 'Lowest price within 30 days');
            Configuration::updateValue('PREXTRAMETA_TEXT_' . $lang['id_lang'], 'Lowest price within 30 days before promotion.');
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminAjaxPrextrameta';
        $tab->name = [];
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = 'Pricing Omnibus Ajax';
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;
        $tab->add();

        include _PS_MODULE_DIR_ . $this->name . '/sql/install.php';

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('displayProductPriceBlock');
    }

    /**
     * This methos is called when uninstalling the module.
     */
    public function uninstall()
    {
        include _PS_MODULE_DIR_ . $this->name . '/sql/uninstall.php';
        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            Configuration::deleteByName('PREXTRAMETA_TEXT_MINI_' . $lang['id_lang']);
            Configuration::deleteByName('PREXTRAMETA_TEXT_' . $lang['id_lang']);
        }
        
        Configuration::deleteByName('PREXTRAMETA_SHOW_ON');
        Configuration::deleteByName('PREXTRAMETA_SHOW_ON_CATELOGUE');
        Configuration::deleteByName('PREXTRAMETA_AUTO_DELETE_OLD');
        Configuration::deleteByName('PREXTRAMETA_SHOW_IF_CURRENT');
        Configuration::deleteByName('PREXTRAMETA_NOTICE_STYLE');
        Configuration::deleteByName('PREXTRAMETA_POSITION');
        Configuration::deleteByName('PREXTRAMETA_BACK_COLOR');
        Configuration::deleteByName('PREXTRAMETA_FONT_SIZE');
        Configuration::deleteByName('PREXTRAMETA_PADDING');
        Configuration::deleteByName('PREXTRAMETA_FONT_COLOR');
        Configuration::deleteByName('PREXTRAMETA_DELETE_OLD');
        Configuration::deleteByName('PREXTRAMETA_DELETE_DATE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (((bool) Tools::isSubmit('submitPrextrametaModule')) == true) {
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
        $helper->submit_action = 'submitPrextrametaModule';
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
                        'label' => $this->l('Show Notice On'),
                        'name' => 'PREXTRAMETA_SHOW_ON',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'all_prds',
                                    'name' => $this->l('All Products'),
                                ],
                                [
                                    'id' => 'discounted',
                                    'name' => $this->l('Only Discounted Products'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show notice if current price is the lowest.'),
                        'name' => 'PREXTRAMETA_SHOW_IF_CURRENT',
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
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Select Notice Text Style'),
                        'name' => 'PREXTRAMETA_NOTICE_STYLE',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'before_after',
                                    'name' => $this->l('Notice Text _ Price'),
                                ],
                                [
                                    'id' => 'after_before',
                                    'name' => $this->l('Price _ Notice Text'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Automatically delete 30 days older data'),
                        'name' => 'PREXTRAMETA_AUTO_DELETE_OLD',
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
                        'tab' => 'general',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show omnibus directive notice on product catalog?'),
                        'name' => 'PREXTRAMETA_SHOW_ON_CATELOGUE',
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
                        'tab' => 'content_list_tab',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Text to show where you show the lowest price in last 30 days.'),
                        'name' => 'PREXTRAMETA_TEXT_MINI',
                        'label' => $this->l('Omni Directive Text'),
                        'tab' => 'content_list_tab',
                        'lang' => true,
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Text to show where you show the lowest price in last 30 days.'),
                        'name' => 'PREXTRAMETA_TEXT',
                        'label' => $this->l('Omni Directive Text'),
                        'tab' => 'content_tab',
                        'lang' => true,
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Select Notice Position'),
                        'name' => 'PREXTRAMETA_POSITION',
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
                        'name' => 'PREXTRAMETA_BACK_COLOR',
                        'tab' => 'design_tab',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Text Color'),
                        'name' => 'PREXTRAMETA_FONT_COLOR',
                        'tab' => 'design_tab',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your font size like "12px"'),
                        'name' => 'PREXTRAMETA_FONT_SIZE',
                        'label' => $this->l('Font Size'),
                        'tab' => 'design_tab',
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Put your padding like "6px"'),
                        'name' => 'PREXTRAMETA_PADDING',
                        'label' => $this->l('Padding'),
                        'tab' => 'design_tab',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Delete Data Before 30 Days?'),
                        'name' => 'PREXTRAMETA_DELETE_OLD',
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
                        'tab' => 'action_tab',
                    ],
                ],
                'tabs' => [
                    'general' => 'General',
                    'content_list_tab' => 'Content (Product Catalog)',
                    'content_tab' => 'Content (Single Product)',
                    'design_tab' => 'Design',
                    'action_tab' => 'Action',
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
            'PREXTRAMETA_SHOW_ON' => Configuration::get('PREXTRAMETA_SHOW_ON', 'discounted'),
            'PREXTRAMETA_SHOW_ON_CATELOGUE' => Configuration::get('PREXTRAMETA_SHOW_ON_CATELOGUE', true),
            'PREXTRAMETA_SHOW_IF_CURRENT' => Configuration::get('PREXTRAMETA_SHOW_IF_CURRENT', true),
            'PREXTRAMETA_AUTO_DELETE_OLD' => Configuration::get('PREXTRAMETA_AUTO_DELETE_OLD', false),
            'PREXTRAMETA_POSITION' => Configuration::get('PREXTRAMETA_POSITION', 'after_price'),
            'PREXTRAMETA_NOTICE_STYLE' => Configuration::get('PREXTRAMETA_NOTICE_STYLE', 'before_after'),
            'PREXTRAMETA_BACK_COLOR' => Configuration::get('PREXTRAMETA_BACK_COLOR', '#b3a700'),
            'PREXTRAMETA_FONT_COLOR' => Configuration::get('PREXTRAMETA_FONT_COLOR', '#ffffff'),
            'PREXTRAMETA_FONT_SIZE' => Configuration::get('PREXTRAMETA_FONT_SIZE', '12px'),
            'PREXTRAMETA_PADDING' => Configuration::get('PREXTRAMETA_PADDING', '6px'),
            'PREXTRAMETA_DELETE_OLD' => false,
        ];
        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $ret_arr['PREXTRAMETA_TEXT'][$lang['id_lang']] = Configuration::get('PREXTRAMETA_TEXT_' . $lang['id_lang'], 'Lowest price within 30 days before promotion');
            $ret_arr['PREXTRAMETA_TEXT_MINI'][$lang['id_lang']] = Configuration::get('PREXTRAMETA_TEXT_MINI_' . $lang['id_lang'], 'Lowest price within 30 days');
        }

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
            if ($key == 'PREXTRAMETA_POSITION') {
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
            } elseif ($key == 'PREXTRAMETA_DELETE_OLD') {
                if (Tools::getValue($key)) {
                    $date = date('Y-m-d');
                    $date_range = date('Y-m-d', strtotime('-31 days'));

                    Db::getInstance()->execute(
                        'DELETE FROM `' . _DB_PREFIX_ . 'prextrameta_products` oc
                        WHERE oc.date < "' . $date_range . '"'
                    );
                    Configuration::updateValue('PREXTRAMETA_DELETE_DATE', $date);
                }
            } elseif($key == ('PREXTRAMETA_TEXT_MINI') || $key == ('PREXTRAMETA_TEXT')){
                
                $languages = Language::getLanguages(false);

                foreach ($languages as $lang) {
                    Configuration::updateValue($key . '_' . $lang['id_lang'], Tools::getValue($key . '_' . $lang['id_lang']));
                }
            } else{
                Configuration::updateValue($key, Tools::getValue($key));
            }   
        }

        $prextrameta_back_color = Configuration::get('PREXTRAMETA_BACK_COLOR', '#b3a700');
        $prextrameta_font_color = Configuration::get('PREXTRAMETA_FONT_COLOR', '#ffffff');
        $prextrameta_font_size = Configuration::get('PREXTRAMETA_FONT_SIZE', '12px');
        $prextrameta_padding = Configuration::get('PREXTRAMETA_PADDING', '6px');
        $gen_css = '.prextrameta-notice{
                        padding: ' . $prextrameta_padding . ' !important;
                        font-size: ' . $prextrameta_font_size . ' !important;
                        color: ' . $prextrameta_font_color . ' !important;
                        background: ' . $prextrameta_back_color . ' !important;
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
        $lang_id = $this->context->language->id;
        $shop_id = $this->context->shop->id;
        Media::addJsDef([
            'prextrameta_ajax_url' => $this->context->link->getAdminLink('AdminAjaxPrextrameta'),
            'prextrameta_shop_id' => $shop_id,
            'prextrameta_lang_id' => $lang_id,
        ]);
        $omni_auto_del = Configuration::get('PREXTRAMETA_AUTO_DELETE_OLD', false);

        if($omni_auto_del){
            $date = date('Y-m-d');
            $date_range = date('Y-m-d', strtotime('-31 days'));
            $prextrameta_delete_date = Configuration::get('PREXTRAMETA_DELETE_DATE');
    
            if ($prextrameta_delete_date == $date_range) {
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'prextrameta_products` oc
                    WHERE oc.date < "' . $date_range . '"'
                );
                Configuration::updateValue('PREXTRAMETA_DELETE_DATE', $date);
            }
        }
    }

    /**
     * Shows Price History List in Admin Product Page
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = $params['id_product'];

        $lang_id = $this->context->language->id;
        $shop_id = $this->context->shop->id;
        $omnibus_meta = false;
        $mresults = Db::getInstance()->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'pricing_extrameta` pemt
            WHERE pemt.`lang_id` = ' . (int) $lang_id . ' AND' . ' pemt.`shop_id` = ' . (int) $shop_id . ' AND pemt.`product_id` = ' . (int) $id_product,
            true
        );

        if(isset($mresults) && !empty($mresults)){
            $omnibus_meta = array_pop($mresults);
        }

        $results = Db::getInstance()->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'prextrameta_products` oc
            WHERE oc.`lang_id` = ' . (int) $lang_id . ' AND' . ' oc.`shop_id` = ' . (int) $shop_id . ' AND oc.`product_id` = ' . (int) $id_product . ' ORDER BY date DESC',
            true
        );
        $omnibus_prices = array();

        foreach($results as $result){
            $omnibus_prices[$result['id_prextrameta']]['id'] = $result['id_prextrameta'];
            $omnibus_prices[$result['id_prextrameta']]['date'] = $result['date'];
            $omnibus_prices[$result['id_prextrameta']]['price'] = $this->context->getCurrentLocale()->formatPrice($result['price'], $this->context->currency->iso_code);
            $omnibus_prices[$result['id_prextrameta']]['promotext'] = 'Normal Price';

            if($result['promo']){
                $omnibus_prices[$result['id_prextrameta']]['promotext'] = 'Promotional Price';
            }
        }
        $languages = Language::getLanguages(false);
        $this->context->smarty->assign([
            'omnibus_meta' => $omnibus_meta,
            'omnibus_prices' => $omnibus_prices,
            'omnibus_prd_id' => $id_product,
            'omnibus_langs' => $languages,
            'omnibus_curr_lang' => $lang_id,
        ]);
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/price_history.tpl');

        return $output;
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
     * Call back function for the  hook DisplayProductPriceBlock
     */
    public function hookDisplayProductPriceBlock($params)
    {
        $product = $params['product'];
        $on_catlg = Configuration::get('PREXTRAMETA_SHOW_ON_CATELOGUE', true);
        $prextrameta_price = $this->prextrameta_init($product, $on_catlg);

        if ($prextrameta_price) {
            $controller = Tools::getValue('controller');
    
            if($controller == 'product'){
                $prextrameta_pos = Configuration::get('PREXTRAMETA_POSITION', 'after_price');

                if ($params['type'] == $prextrameta_pos) {
                    $this->prextrameta_show_notice($prextrameta_price);
                }
            }else{

                if($on_catlg && $params['type'] == 'unit_price'){
                    $this->prextrameta_show_notice($prextrameta_price, '_mini');
                }
            }
        }
    }

    /**
     * Call back function for the  hook DisplayFooterProduct
     */
    public function hookDisplayFooterProduct($params)
    {
        $product = $params['product'];
        $prextrameta_price = $this->prextrameta_init($product);

        if ($prextrameta_price) {
            $this->prextrameta_show_notice($prextrameta_price);
        }
    }

    /**
     * Call back function for the  hook DisplayProductButtons
     */
    public function hookDisplayProductButtons($params)
    {
        $product = $params['product'];
        $prextrameta_price = $this->prextrameta_init($product);

        if ($prextrameta_price) {
            $this->prextrameta_show_notice($prextrameta_price);
        }
    }

    /**
     * Returns the Omnibus Price if poduct has promotion
     */
    private function prextrameta_init($product, $on_catlg = true)
    {
        $controller = Tools::getValue('controller');
        $show_on = Configuration::get('PREXTRAMETA_SHOW_ON', 'discounted');

        if ($controller != 'product' && !$on_catlg) {
            return;
        }

        if (!$product->has_discount && $show_on == 'discounted') {
            return;
        }
        $price_amount = $product->price_amount;
        $existing = $this->prextrameta_check_existance($product->id, $price_amount, $product->id_product_attribute);

        if (empty($existing)) {
            $this->prextrameta_insert_data($product->id, $price_amount, $product->has_discount, $product->id_product_attribute);
        }
        $omnibus_price = $this->prextrameta_get_price($product->id, $price_amount, $product->id_product_attribute);

        if ($omnibus_price) {
            $prextrametae_formatted_price = $this->context->getCurrentLocale()->formatPrice($omnibus_price, $this->context->currency->iso_code);

            return $prextrametae_formatted_price;
        } else {
            $omni_if_current = Configuration::get('PREXTRAMETA_SHOW_IF_CURRENT', true);

            if ($omni_if_current) {
                return $product->price;
            }

            return false;
        }

        return false;
    }

    /**
     * Check if price is alredy available for the product
     */
    private function prextrameta_check_existance($prd_id, $price, $id_attr = 0)
    {
        $lang_id = $this->context->language->id;
        $shop_id = $this->context->shop->id;
        $attr_q = '';

        if($id_attr){
            $attr_q = ' AND oc.`id_product_attribute` = ' . $id_attr;
        }

        $results = Db::getInstance()->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'prextrameta_products` oc
            WHERE oc.`lang_id` = ' . (int) $lang_id . ' AND oc.`shop_id` = ' . (int) $shop_id . '
            AND oc.`product_id` = ' . (int) $prd_id . ' AND oc.`price` = ' . $price . $attr_q
        );

        return $results;
    }

    /**
     * Insert the minimum price to the table
     */
    private function prextrameta_insert_data($prd_id, $price, $discounted, $id_attr = 0)
    {
        $lang_id = $this->context->language->id;
        $shop_id = $this->context->shop->id;
        $date = date('Y-m-d');
        $promo = 0;

        if($discounted){
            $promo = 1;
        }

        $result = Db::getInstance()->insert('prextrameta_products', [
            'product_id' => (int) $prd_id,
            'id_product_attribute' => $id_attr,
            'price' => $price,
            'promo' => $promo,
            'date' => $date,
            'shop_id' => (int) $shop_id,
            'lang_id' => (int) $lang_id,
        ]);
    }

    /**
     * Gets the minimum price within 30 days.
     */
    private function prextrameta_get_price($id, $price_amount, $id_attr = 0)
    {
        $lang_id = $this->context->language->id;
        $shop_id = $this->context->shop->id;
        $attr_q = '';
        if($id_attr){
            $attr_q = ' AND oc.`id_product_attribute` = ' . $id_attr;
        }
        $date = date('Y-m-d');
        $date_range = date('Y-m-d', strtotime('-31 days'));
        $result = Db::getInstance()->getValue('SELECT MIN(price) as ' . $this->name . '_price FROM `' . _DB_PREFIX_ . 'prextrameta_products` oc 
        WHERE oc.`lang_id` = ' . (int) $lang_id . ' AND oc.`shop_id` = ' . (int) $shop_id . '
        AND oc.`product_id` = ' . (int) $id . ' AND oc.date > "' . $date_range . '" AND oc.price != "' . $price_amount . '"' . $attr_q);

        return $result;
    }

    /**
     * Shows the notice
     */
    private function prextrameta_show_notice($price, $controller = '')
    {
        $lang_id = $this->context->language->id;
        $mini_conf = strtoupper($controller);
        $prextrameta_text = Configuration::get('PREXTRAMETA_TEXT' . $mini_conf . '_' . $lang_id, 'Lowest price within 30 days before promotion.');
        $prextrameta_text_style = Configuration::get('PREXTRAMETA_NOTICE_STYLE', 'before_after');
        $this->context->smarty->assign([
            'prextrameta_text' => $prextrameta_text,
            'prextrameta_text_style' => $prextrameta_text_style,
            'prextrameta_price' => $price,
        ]);
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/front/omni_front' . $controller . '.tpl');

        echo $output;
    }
}
