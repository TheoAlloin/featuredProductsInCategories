<?php
/**
 * 2007-2017 PrestaShop
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
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/models/FPCAssociation.php');

class FeaturedProductsInCategories extends Module
{
    
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'featuredProductsInCategories';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'JL Consulting Web';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Featured Products in Categories');
        $this->description = $this->l('This module allows you to highlight products in categories by putting them at the top of the page.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module ?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('FPC_BLOCK_TITLE', '');
        Configuration::updateValue('FPC_PRODUCTS_ORDER', 1);
        Configuration::updateValue('FPC_BLOCK_POSITION', 1);
        Configuration::updateValue('FPC_BLOCK_SLIDER', 0);

        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionProductDelete') &&
            $this->registerHook('actionProductUpdate') &&
            $this->registerHook('displayLeftColumn') &&
            $this->registerHook('displayTop') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('displayProductTabContent') &&
            $this->registerHook('displayTopColumn');
    }

    public function uninstall()
    {

        Configuration::deleteByName('FPC_BLOCK_TITLE');
        Configuration::deleteByName('FPC_PRODUCTS_ORDER');
        Configuration::deleteByName('FPC_BLOCK_POSITION');
        Configuration::deleteByName('FPC_BLOCK_SLIDER');

        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If submit categories in admin product tab
         */
        if (Tools::isSubmit('submitFeaturedProducts')) {
            if (Tools::getValue('categoryBox') && ValidateCore::isArrayWithIds(Tools::getValue('categoryBox')) && (int)Tools::getValue('current_product') ) {
                $this->_isSubmitFeaturedProductsPostProcess();
            } elseif ((int)Tools::getValue('current_product')) {
                $error = $this->l('You must check one or more categories');
                $this->redirectAdminTab((int)Tools::getValue('id_product'));
                $this->hookDisplayAdminProductsExtra($error);
            } else {
                echo 'fatal error'; die();
            }
        }

        /**
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitFeaturedProductsInCategoriesModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
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
        $helper->submit_action = 'submitFeaturedProductsInCategoriesModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-tag"></i>',
                        'desc' => $this->l('Choose the name of the block'),
                        'hint' => $this->l('Leave empty to use the name of the category'),
                        'name' => 'FPC_BLOCK_TITLE',
                        'label' => $this->l('Block label'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Order products by'),
                        'desc' => $this->l('Choose display order of products'),
                        'name' => 'FPC_PRODUCTS_ORDER',
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => 'Product id'
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => 'Random'
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => 'Last modified date'
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => 'Creation date'
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Block position'),
                        'desc' => $this->l('Choose the position of the block'),
                        'name' => 'FPC_BLOCK_POSITION',
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => 'Top of page'
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => 'Left column'
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Use a slider for featured products'),
                        'desc' => $this->l(''),
                        'name' => 'FPC_BLOCK_SLIDER',
                        'required' => true,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yep')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Nope')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'FPC_BLOCK_TITLE' => Configuration::get('FPC_BLOCK_TITLE'),
            'FPC_PRODUCTS_ORDER' => Configuration::get('FPC_PRODUCTS_ORDER'),
            'FPC_BLOCK_POSITION' => Configuration::get('FPC_BLOCK_POSITION'),
            'FPC_BLOCK_SLIDER' => Configuration::get('FPC_BLOCK_SLIDER'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
            $this->context->controller->addJS(_PS_BO_ALL_THEMES_DIR_.'default/js/tree.js');

        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookActionProductDelete()
    {
        /* Place your code here. */
    }

    public function hookActionProductUpdate()
    {
        /* Place your code here. */
    }

    public function hookDisplayLeftColumn()
    {
        /* Place your code here. */
    }

    public function hookDisplayTop()
    {
        /* Place your code here. */
    }

    public function hookDisplayTopColumn()
    {
        /* Place your code here. */
    }

    /**
     * Product admin tab
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = Tools::getValue('id_product');
        $root = Category::getRootCategory();
        $submitFormRouting = 'index.php?controller=AdminModules&configure=featuredProductsInCategories&tab_module=front_office_features&module_name=featuredProductsInCategories&token=' . Tools::getAdminTokenLite('AdminModules');

        $id_categories_enabled = array(3, 4, 5);
        $id_categories_disabled = array(8);
        
        if ($params) {
            $error = $params;
        }
        
//        $selected_cat = array($root->id);
//        $categories = array();
//        $categories = $this->getSelectedCategory($id_product);

        $tree = new HelperTreeCategories('associated-categories-tree', 'Associated categories');
        $tree->setUseCheckBox(true)
            ->setFullTree(true)
            ->setAttribute('is_category_filter', $root->id)
            ->setRootCategory($root->id)
            ->setHeaderTemplate('tree_associated_header.tpl')
            ->setSelectedCategories($id_categories_enabled)
            ->setDisabledCategories($id_categories_disabled)
            ->setUseSearch(true);
        $this->context->smarty->assign(array(
            'categories_tree' => $tree->render(),
            'submitFormRouting' => $submitFormRouting,
            'current_product' => $id_product,
            'error' => $error
        ));
        
        return $this -> display(__FILE__, 'views/templates/admin/displayAdminProductsExtra.tpl');
    }

    private function getSelectedCategory($id_product)
    {
        $default_category = $this->context->cookie->id_category_products_filter ? $this->context->cookie->id_category_products_filter : Context::getContext()->shop->id_category;
        $selected_cat = Category::getCategoryInformations(Tools::getValue('categoryBox', array($default_category)), $this->default_form_language);

        foreach ($selected_cat as $key => $category) {
            $categories[] = $key;
        }
        return $categories;
    }
    /**
     * Send categories postProcess
     */
    private function _isSubmitFeaturedProductsPostProcess()
    {
        $id_categories = Tools::getValue('categoryBox');
        $id_product = Tools::getValue('current_product');
        $errors = array();
        
        if ($id_categories && (int)$id_product) {
            FPCAssociation::deleteAllAssociationsByProductId($id_product);
            if (!FPCAssociation::addAssociations($id_product, $id_categories)) {
                $errors[] = $this->l('Can\'t add associations');
            }
        } else {
            $errors[] = $this->l('Can\'t retrive id_categories sends or current id_product');
        }
        
        /***** display errors if needed *****/
        if (!count($errors)) {
            return true;
        } else {
            return $errors;
        }
    }
    
    private function redirectAdminTab($id_product) {
        $url = 'index.php?controller=AdminProducts&token='.Tools::getAdminTokenLite('AdminProducts') . '&id_product=' . (int)$id_product . '&action=ModuleFeaturedProductsInCategories&updateproduct';
        var_dump(Tools::redirectAdmin($url));exit;
    }
}