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
        $configuration = $this->assignProductConfiguration($id_product);
        
        $this->renderTree($configuration['id_categories_enabled'], $configuration['id_categories_disabled']);
        $this->_isSubmitFeaturedProductsPostProcess();

        return $this -> display(__FILE__, 'views/templates/admin/displayAdminProductsExtra.tpl');
    }

    public function renderTree($id_categories_enabled, $id_categories_disabled)
    {
        $root = Category::getRootCategory();

        $tree = new HelperTreeCategories('associated-categories-tree', 'Associated categories');
        $tree->setUseCheckBox(true)
            ->setFullTree(true)
            ->setAttribute('is_category_filter', $root->id)
            ->setRootCategory($root->id)
            ->setHeaderTemplate('tree_associated_header.tpl')
            ->setSelectedCategories($id_categories_enabled)
            ->setDisabledCategories($id_categories_disabled)
            ->setUseSearch(true)
            ->setInputName('categoryBox2');

        
        $this->context->smarty->assign('categories_tree', $tree->render());
    }
    /*
     * if product have configuration registered
     */
    private function assignProductConfiguration( $id_product )
    {
        $id_categories_enabled = [];
        $id_categories_disabled = [];
        $product = new Product($id_product);
        $category = new CategoryCore();

        $params = array('id_product' => $id_product);
        
        /*** get categories enabled for render tree ***/
        $list_configuration = FPCAssociation::getList($params);
        $id_parent_categories_enabled = [];
        
        foreach ($list_configuration as $configuration)
        {
            if ($configuration['id_category']) {
                array_push($id_categories_enabled, (int)$configuration['id_category']);

                $a_Category = new CategoryCore($configuration['id_category']);
                $categories_parent = $a_Category->getParentsCategories();
                
                /* add parents of current category for be enabled  */
                foreach ($categories_parent as $category_parent) {
                    array_push($id_parent_categories_enabled, (int)$category_parent['id_category']);
                }
            }
        }

        /*** get categories disabled for render tree ***/
        $id_categories_enabled_formated = '';
        
        /* format for FPCAssociation::getCustomCategories($sql_filter) */
        foreach($id_categories_enabled as $id_category_enabled) {
            if( !next( $id_categories_enabled ) ) {
                $id_categories_enabled_formated .= ($id_category_enabled);
            } else {
                $id_categories_enabled_formated .= ($id_category_enabled . ',' );
            }
        }
        
        /* get all category not in enabled categories */
        $sql_filter = 'AND c.id_category NOT IN ( ' . $id_categories_enabled_formated . ' ) AND c.id_category != 1';
        $list_categories = FPCAssociation::getCustomIDCategories(false, false, false, $sql_filter);
        
        foreach ($list_categories as $category) {
            array_push($id_categories_disabled, (int)$category['id_category']);
        }
        
        $id_categories_disabled = array_diff($id_categories_disabled, $id_parent_categories_enabled);
        /* supprime des categories desactivées les catégories auxquelles sont associé le produit courant */
        $product_categories_associated = $product->getCategories();
        $id_categories_disabled = array_diff($id_categories_disabled, $product_categories_associated);

        $result = [
            'id_categories_enabled' => $id_categories_enabled,
            'id_categories_disabled' => $id_categories_disabled
        ];
        
        return $result;
    }
    
    /**
     * Send categories postProcess
     */
    public function _isSubmitFeaturedProductsPostProcess()
    {
        /**
         * If submit categories in admin product tab
         */
        if (Tools::isSubmit('submitFeaturedProducts')) {
            if (Tools::getValue('categoryBox2') && ValidateCore::isArrayWithIds(Tools::getValue('categoryBox2'))) {
                $id_categories = Tools::getValue('categoryBox2');
                $id_product = Tools::getValue('id_product');
                $product = new Product($id_product);
                $id_lang = $this->context->language->id;
                $categories_associated = [];
                $errors = [];

                if ($id_categories && (int)$id_product) {
                    FPCAssociation::deleteAllAssociationsByProductId($id_product);
                    if (FPCAssociation::addAssociations($id_product, $id_categories)) {
                        $this -> context -> smarty -> assign('confirmation', 'ok');
                    }
                } else {
                    $errors[] = $this->l('Can\'t retrieved id_categories sends or current id_product');
                }
            }
        }
        
        /***** display errors if needed *****/
        if (!empty($errors) && count($errors)) {
            $this -> context -> smarty -> assign('error', $errors);
        }
    }
}