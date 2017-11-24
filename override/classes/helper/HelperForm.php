<?php
class HelperForm extends HelperFormCore
{
    public function generate()
    {
        $this->tpl = $this->createTemplate($this->base_tpl);
        if (is_null($this->submit_action)) {
            $this->submit_action = 'submitAdd'.$this->table;
        }
        $categories = true;
        $color = true;
        $date = true;
        $tinymce = true;
        $textarea_autosize = true;
        $file = true;
        foreach ($this->fields_form as $fieldset_key => &$fieldset) {
            if (isset($fieldset['form']['tabs'])) {
                $tabs[] = $fieldset['form']['tabs'];
            }
            if (isset($fieldset['form']['input'])) {
                foreach ($fieldset['form']['input'] as $key => &$params) {
                    // If the condition is not met, the field will not be displayed
                    if (isset($params['condition']) && !$params['condition']) {
                        unset($this->fields_form[$fieldset_key]['form']['input'][$key]);
                    }
                    switch ($params['type']) {
                        case 'categories':
                            if (!isset($params['tree']['id'])) {
                                throw new PrestaShopException('Id must be filled for categories tree');
                            }
                            $tree = new HelperTreeCategories($params['tree']['id'], isset($params['tree']['title']) ? $params['tree']['title'] : null);
                            if (isset($params['name'])) {
                                $tree->setInputName($params['name']);
                            }
                            if (isset($params['tree']['selected_categories'])) {
                                $tree->setSelectedCategories($params['tree']['selected_categories']);
                            }
                            if (isset($params['tree']['disabled_categories'])) {
                                $tree->setDisabledCategories($params['tree']['disabled_categories']);
                            }
                            if (isset($params['tree']['root_category'])) {
                                $tree->setRootCategory($params['tree']['root_category']);
                            }
                            if (isset($params['tree']['use_search'])) {
                                $tree->setUseSearch($params['tree']['use_search']);
                            }
                            if (isset($params['tree']['use_checkbox'])) {
                                $tree->setUseCheckBox($params['tree']['use_checkbox']);
                            }
                            if (isset($params['tree']['set_data'])) {
                                $tree->setData($params['tree']['set_data']);
                            }
                            $this->context->smarty->assign('categories_tree_'.$params['name'], $tree->render());
                            break;
                        case 'file':
                            $uploader = new HelperUploader();
                            $uploader->setId(isset($params['id'])?$params['id']:null);
                            $uploader->setName($params['name']);
                            $uploader->setUrl(isset($params['url'])?$params['url']:null);
                            $uploader->setMultiple(isset($params['multiple'])?$params['multiple']:false);
                            $uploader->setUseAjax(isset($params['ajax'])?$params['ajax']:false);
                            $uploader->setMaxFiles(isset($params['max_files'])?$params['max_files']:null);
                            if (isset($params['files']) && $params['files']) {
                                $uploader->setFiles($params['files']);
                            } elseif (isset($params['image']) && $params['image']) { // Use for retrocompatibility
                                $uploader->setFiles(array(
                                    0 => array(
                                        'type'       => HelperUploader::TYPE_IMAGE,
                                        'image'      => isset($params['image'])?$params['image']:null,
                                        'size'       => isset($params['size'])?$params['size']:null,
                                        'delete_url' => isset($params['delete_url'])?$params['delete_url']:null
                                    )
                                ));
                            }
                            if (isset($params['file']) && $params['file']) { // Use for retrocompatibility
                                $uploader->setFiles(array(
                                    0 => array(
                                        'type'       => HelperUploader::TYPE_FILE,
                                        'size'       => isset($params['size'])?$params['size']:null,
                                        'delete_url' => isset($params['delete_url'])?$params['delete_url']:null,
                                        'download_url' => isset($params['file'])?$params['file']:null
                                    )
                                ));
                            }
                            if (isset($params['thumb']) && $params['thumb']) { // Use for retrocompatibility
                                $uploader->setFiles(array(
                                    0 => array(
                                        'type'  => HelperUploader::TYPE_IMAGE,
                                        'image' => isset($params['thumb'])?'<img src="'.$params['thumb'].'" alt="'.(isset($params['title']) ? $params['title'] : '').'" title="'.(isset($params['title']) ? $params['title'] : '').'" />':null,
                                    )
                                ));
                            }
                            $uploader->setTitle(isset($params['title'])?$params['title']:null);
                            $params['file'] = $uploader->render();
                            break;
                        case 'color':
                            if ($color) {
                                // Added JS file
                                $this->context->controller->addJqueryPlugin('colorpicker');
                                $color = false;
                            }
                            break;
                        case 'date':
                            if ($date) {
                                $this->context->controller->addJqueryUI('ui.datepicker');
                                $date = false;
                            }
                            break;
                        case 'textarea':
                            if ($tinymce) {
                                $iso = $this->context->language->iso_code;
                                $this->tpl_vars['iso'] = file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en';
                                $this->tpl_vars['path_css'] = _THEME_CSS_DIR_;
                                $this->tpl_vars['ad'] = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
                                $this->tpl_vars['tinymce'] = true;
                                $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
                                $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
                                $tinymce = false;
                            }
                            if ($textarea_autosize) {
                                $this->context->controller->addJqueryPlugin('autosize');
                                $textarea_autosize = false;
                            }
                            break;
                        case 'shop':
                            $disable_shops = isset($params['disable_shared']) ? $params['disable_shared'] : false;
                            $params['html'] = $this->renderAssoShop($disable_shops);
                            if (Shop::getTotalShops(false) == 1) {
                                if ((isset($this->fields_form[$fieldset_key]['form']['force']) && !$this->fields_form[$fieldset_key]['form']['force']) || !isset($this->fields_form[$fieldset_key]['form']['force'])) {
                                    unset($this->fields_form[$fieldset_key]['form']['input'][$key]);
                                }
                            }
                            break;
                    }
                }
            }
        }
        $this->tpl->assign(array(
            'title' => $this->title,
            'toolbar_btn' => $this->toolbar_btn,
            'show_toolbar' => $this->show_toolbar,
            'toolbar_scroll' => $this->toolbar_scroll,
            'submit_action' => $this->submit_action,
            'firstCall' => $this->first_call,
            'current' => $this->currentIndex,
            'token' => $this->token,
            'table' => $this->table,
            'identifier' => $this->identifier,
            'name_controller' => $this->name_controller,
            'languages' => $this->languages,
            'current_id_lang' => $this->context->language->id,
            'defaultFormLanguage' => $this->default_form_language,
            'allowEmployeeFormLang' => $this->allow_employee_form_lang,
            'form_id' => $this->id,
            'tabs' => (isset($tabs)) ? $tabs : null,
            'fields' => $this->fields_form,
            'fields_value' => $this->fields_value,
            'required_fields' => $this->getFieldsRequired(),
            'vat_number' => Module::isInstalled('vatnumber') && file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php'),
            'module_dir' => _MODULE_DIR_,
            'base_url' => $this->context->shop->getBaseURL(),
            'contains_states' => (isset($this->fields_value['id_country']) && isset($this->fields_value['id_state'])) ? Country::containsStates($this->fields_value['id_country']) : null,
            'show_cancel_button' => $this->show_cancel_button,
            'back_url' => $this->back_url
        ));
        return parent::generate();
    }
}