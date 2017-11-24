{extends file="helpers/form/form.tpl"}
    {block name="input"}
    {if $input.type == 'text' || $input.type == 'tags'}
            {if isset($input.lang) AND $input.lang}
            {if $languages|count > 1}
            <div class="form-group">
            {/if}
            {foreach $languages as $language}
                    {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                    {if $languages|count > 1}
                    <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                            <div class="col-lg-9">
                    {/if}
                                    {if $input.type == 'tags'}
                                            {literal}
                                                    <script type="text/javascript">
                                                            $().ready(function () {
                                                                    var input_id = '{/literal}{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}{literal}';
                                                                    $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'});
                                                                    $({/literal}'#{$table}{literal}_form').submit( function() {
                                                                            $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                                                    });
                                                            });
                                                    </script>
                                            {/literal}
                                    {/if}
                                    {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                    <div class="input-group{if isset($input.class)} {$input.class}{/if}">
                                    {/if}
                                    {if isset($input.maxchar) && $input.maxchar}
                                    <span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
                                            <span class="text-count-down">{$input.maxchar|intval}</span>
                                    </span>
                                    {/if}
                                    {if isset($input.prefix)}
                                            <span class="input-group-addon">
                                              {$input.prefix}
                                            </span>
                                            {/if}
                                    <input type="text"
                                            id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"
                                            name="{$input.name}_{$language.id_lang}"
                                            class="{if isset($input.class)}{$input.class}{/if}{if $input.type == 'tags'} tagify{/if}"
                                            value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                            onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
                                            {if isset($input.size)} size="{$input.size}"{/if}
                                            {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                            {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                            {if isset($input.required) && $input.required} required="required" {/if}
                                            {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder}"{/if} />
                                            {if isset($input.suffix)}
                                            <span class="input-group-addon">
                                              {$input.suffix}
                                            </span>
                                            {/if}
                                    {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                    </div>
                                    {/if}
                    {if $languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                            {$language.iso_code}
                                            <i class="icon-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                            {foreach from=$languages item=language}
                                            <li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
                                            {/foreach}
                                    </ul>
                            </div>
                    </div>
                    {/if}
            {/foreach}
            {if isset($input.maxchar) && $input.maxchar}
            <script type="text/javascript">
            $(document).ready(function(){
            {foreach from=$languages item=language}
                    countDown($("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), $("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
            {/foreach}
            });
            </script>
            {/if}
            {if $languages|count > 1}
            </div>
            {/if}
            {else}
                    {if $input.type == 'tags'}
                            {literal}
                            <script type="text/javascript">
                                    $().ready(function () {
                                            var input_id = '{/literal}{if isset($input.id)}{$input.id}{else}{$input.name}{/if}{literal}';
                                            $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag'}{literal}'});
                                            $({/literal}'#{$table}{literal}_form').submit( function() {
                                                    $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                            });
                                    });
                            </script>
                            {/literal}
                    {/if}
                    {assign var='value_text' value=$fields_value[$input.name]}
                    {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                    <div class="input-group{if isset($input.class)} {$input.class}{/if}">
                    {/if}
                    {if isset($input.maxchar) && $input.maxchar}
                    <span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|intval}</span></span>
                    {/if}
                    {if isset($input.prefix)}
                    <span class="input-group-addon">
                      {$input.prefix}
                    </span>
                    {/if}
                    <input type="text"
                            name="{$input.name}"
                            id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
                            value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                            class="{if isset($input.class)}{$input.class}{/if}{if $input.type == 'tags'} tagify{/if}"
                            {if isset($input.size)} size="{$input.size}"{/if}
                            {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                            {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                            {if isset($input.required) && $input.required } required="required" {/if}
                            {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder}"{/if}
                            />
                    {if isset($input.suffix)}
                    <span class="input-group-addon">
                      {$input.suffix}
                    </span>
                    {/if}

                    {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                    </div>
                    {/if}
                    {if isset($input.maxchar) && $input.maxchar}
                    <script type="text/javascript">
                    $(document).ready(function(){
                            countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
                    });
                    </script>
                    {/if}
            {/if}
    {elseif $input.type == 'textbutton'}
            {assign var='value_text' value=$fields_value[$input.name]}
            <div class="row">
                    <div class="col-lg-9">
                    {if isset($input.maxchar)}
                    <div class="input-group">
                            <span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon">
                                    <span class="text-count-down">{$input.maxchar|intval}</span>
                            </span>
                    {/if}
                    <input type="text"
                            name="{$input.name}"
                            id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
                            value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                            class="{if isset($input.class)}{$input.class}{/if}{if $input.type == 'tags'} tagify{/if}"
                            {if isset($input.size)} size="{$input.size}"{/if}
                            {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                            {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                            {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder}"{/if}
                            />
                    {if isset($input.suffix)}{$input.suffix}{/if}
                    {if isset($input.maxchar) && $input.maxchar}
                    </div>
                    {/if}
                    </div>
                    <div class="col-lg-2">
                            <button type="button" class="btn btn-default{if isset($input.button.attributes['class'])} {$input.button.attributes['class']}{/if}{if isset($input.button.class)} {$input.button.class}{/if}"
                                    {foreach from=$input.button.attributes key=name item=value}
                                            {if $name|lower != 'class'}
                                             {$name|escape:'html':'UTF-8'}="{$value|escape:'html':'UTF-8'}"
                                            {/if}
                                    {/foreach} >
                                    {$input.button.label}
                            </button>
                    </div>
            </div>
            {if isset($input.maxchar) && $input.maxchar}
            <script type="text/javascript">
                    $(document).ready(function() {
                            countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
                    });
            </script>
            {/if}
    {elseif $input.type == 'swap'}
            <div class="form-group">
                    <div class="col-lg-9">
                            <div class="form-control-static row">
                                    <div class="col-xs-6">
                                            <select {if isset($input.size)}size="{$input.size|escape:'html':'utf-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'utf-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'utf-8'}{/if}" id="availableSwap" name="{$input.name|escape:'html':'utf-8'}_available[]" multiple="multiple">
                                            {foreach $input.options.query AS $option}
                                                    {if is_object($option)}
                                                            {if !in_array($option->$input.options.id, $fields_value[$input.name])}
                                                                    <option value="{$option->$input.options.id}">{$option->$input.options.name}</option>
                                                            {/if}
                                                    {elseif $option == "-"}
                                                            <option value="">-</option>
                                                    {else}
                                                            {if !in_array($option[$input.options.id], $fields_value[$input.name])}
                                                                    <option value="{$option[$input.options.id]}">{$option[$input.options.name]}</option>
                                                            {/if}
                                                    {/if}
                                            {/foreach}
                                            </select>
                                            <a href="#" id="addSwap" class="btn btn-default btn-block">{l s='Add'} <i class="icon-arrow-right"></i></a>
                                    </div>
                                    <div class="col-xs-6">
                                            <select {if isset($input.size)}size="{$input.size|escape:'html':'utf-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'utf-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'utf-8'}{/if}" id="selectedSwap" name="{$input.name|escape:'html':'utf-8'}_selected[]" multiple="multiple">
                                            {foreach $input.options.query AS $option}
                                                    {if is_object($option)}
                                                            {if in_array($option->$input.options.id, $fields_value[$input.name])}
                                                                    <option value="{$option->$input.options.id}">{$option->$input.options.name}</option>
                                                            {/if}
                                                    {elseif $option == "-"}
                                                            <option value="">-</option>
                                                    {else}
                                                            {if in_array($option[$input.options.id], $fields_value[$input.name])}
                                                                    <option value="{$option[$input.options.id]}">{$option[$input.options.name]}</option>
                                                            {/if}
                                                    {/if}
                                            {/foreach}
                                            </select>
                                            <a href="#" id="removeSwap" class="btn btn-default btn-block"><i class="icon-arrow-left"></i> {l s='Remove'}</a>
                                    </div>
                            </div>
                    </div>
            </div>
    {elseif $input.type == 'select'}
            {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
                    {$input.empty_message}
                    {$input.required = false}
                    {$input.desc = null}
            {else}
                    <select name="{$input.name|escape:'html':'utf-8'}{if isset($input.multiple) && $input.multiple}[]{/if}"
                                    class="{if isset($input.class)}{$input.class|escape:'html':'utf-8'}{/if} fixed-width-xl"
                                    id="{if isset($input.id)}{$input.id|escape:'html':'utf-8'}{else}{$input.name|escape:'html':'utf-8'}{/if}"
                                    {if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
                                    {if isset($input.size)} size="{$input.size|escape:'html':'utf-8'}"{/if}
                                    {if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'utf-8'}"{/if}
                                    {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}>
                            {if isset($input.options.default)}
                                    <option value="{$input.options.default.value|escape:'html':'utf-8'}">{$input.options.default.label|escape:'html':'utf-8'}</option>
                            {/if}
                            {if isset($input.options.optiongroup)}
                                    {foreach $input.options.optiongroup.query AS $optiongroup}
                                            <optgroup label="{$optiongroup[$input.options.optiongroup.label]}">
                                                    {foreach $optiongroup[$input.options.options.query] as $option}
                                                            <option value="{$option[$input.options.options.id]}"
                                                                    {if isset($input.multiple)}
                                                                            {foreach $fields_value[$input.name] as $field_value}
                                                                                    {if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
                                                                            {/foreach}
                                                                    {else}
                                                                            {if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
                                                                    {/if}
                                                            >{$option[$input.options.options.name]}</option>
                                                    {/foreach}
                                            </optgroup>
                                    {/foreach}
                            {else}
                                    {foreach $input.options.query AS $option}
                                            {if is_object($option)}
                                                    <option value="{$option->$input.options.id}"
                                                            {if isset($input.multiple)}
                                                                    {foreach $fields_value[$input.name] as $field_value}
                                                                            {if $field_value == $option->$input.options.id}
                                                                                    selected="selected"
                                                                            {/if}
                                                                    {/foreach}
                                                            {else}
                                                                    {if $fields_value[$input.name] == $option->$input.options.id}
                                                                            selected="selected"
                                                                    {/if}
                                                            {/if}
                                                    >{$option->$input.options.name}</option>
                                            {elseif $option == "-"}
                                                    <option value="">-</option>
                                            {else}
                                                    <option value="{$option[$input.options.id]}"
                                                            {if isset($input.multiple)}
                                                                    {foreach $fields_value[$input.name] as $field_value}
                                                                            {if $field_value == $option[$input.options.id]}
                                                                                    selected="selected"
                                                                            {/if}
                                                                    {/foreach}
                                                            {else}
                                                                    {if $fields_value[$input.name] == $option[$input.options.id]}
                                                                            selected="selected"
                                                                    {/if}
                                                            {/if}
                                                    >{$option[$input.options.name]}</option>

                                            {/if}
                                    {/foreach}
                            {/if}
                    </select>
            {/if}
    {elseif $input.type == 'radio'}
            {foreach $input.values as $value}
                    <div class="radio {if isset($input.class)}{$input.class}{/if}">
                            {strip}
                            <label>
                            <input type="radio"	name="{$input.name}" id="{$value.id}" value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
                                    {$value.label}
                            </label>
                            {/strip}
                    </div>
                    {if isset($value.p) && $value.p}<p class="help-block">{$value.p}</p>{/if}
            {/foreach}
    {elseif $input.type == 'switch'}
            <span class="switch prestashop-switch fixed-width-lg">
                    {foreach $input.values as $value}
                    <input type="radio" name="{$input.name}"{if $value.value == 1} id="{$input.name}_on"{else} id="{$input.name}_off"{/if} value="{$value.value}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
                    {strip}
                    <label {if $value.value == 1} for="{$input.name}_on"{else} for="{$input.name}_off"{/if}>
                            {if $value.value == 1}
                                    {l s='Yes'}
                            {else}
                                    {l s='No'}
                            {/if}
                    </label>
                    {/strip}
                    {/foreach}
                    <a class="slide-button btn"></a>
            </span>
    {elseif $input.type == 'textarea'}
            {if isset($input.maxchar) && $input.maxchar}<div class="input-group">{/if}
            {assign var=use_textarea_autosize value=true}
            {if isset($input.lang) AND $input.lang}
                    {foreach $languages as $language}
                            {if $languages|count > 1}
                            <div class="form-group translatable-field lang-{$language.id_lang}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                                    <div class="col-lg-9">
                            {/if}
                                            {if isset($input.maxchar) && $input.maxchar}
                                                    <span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
                                                            <span class="text-count-down">{$input.maxchar|intval}</span>
                                                    </span>
                                            {/if}
                                            <textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name}_{$language.id_lang}" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_{$language.id_lang}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
                            {if $languages|count > 1}
                                    </div>
                                    <div class="col-lg-2">
                                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                    {$language.iso_code}
                                                    <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                    {foreach from=$languages item=language}
                                                    <li>
                                                            <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                                    </li>
                                                    {/foreach}
                                            </ul>
                                    </div>
                            </div>
                            {/if}
                    {/foreach}
                    {if isset($input.maxchar) && $input.maxchar}
                            <script type="text/javascript">
                            $(document).ready(function(){
                            {foreach from=$languages item=language}
                                    countDown($("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), $("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
                            {/foreach}
                            });
                            </script>
                    {/if}
            {else}
                    {if isset($input.maxchar) && $input.maxchar}
                            <span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
                                    <span class="text-count-down">{$input.maxchar|intval}</span>
                            </span>
                    {/if}
                    <textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name}" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}" {if isset($input.cols)}cols="{$input.cols}"{/if} {if isset($input.rows)}rows="{$input.rows}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
                    {if isset($input.maxchar) && $input.maxchar}
                            <script type="text/javascript">
                            $(document).ready(function(){
                                    countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
                            });
                            </script>
                    {/if}
            {/if}
            {if isset($input.maxchar) && $input.maxchar}</div>{/if}
    {elseif $input.type == 'checkbox'}
            {if isset($input.expand)}
                    <a class="btn btn-default show_checkbox{if strtolower($input.expand.default) == 'hide'} hidden{/if}" href="#">
                            <i class="icon-{$input.expand.show.icon}"></i>
                            {$input.expand.show.text}
                            {if isset($input.expand.print_total) && $input.expand.print_total > 0}
                                    <span class="badge">{$input.expand.print_total}</span>
                            {/if}
                    </a>
                    <a class="btn btn-default hide_checkbox{if strtolower($input.expand.default) == 'show'} hidden{/if}" href="#">
                            <i class="icon-{$input.expand.hide.icon}"></i>
                            {$input.expand.hide.text}
                            {if isset($input.expand.print_total) && $input.expand.print_total > 0}
                                    <span class="badge">{$input.expand.print_total}</span>
                            {/if}
                    </a>
            {/if}
            {foreach $input.values.query as $value}
                    {assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
                    <div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">
                            {strip}
                                    <label for="{$id_checkbox}">
                                            <input type="checkbox" name="{$id_checkbox}" id="{$id_checkbox}" class="{if isset($input.class)}{$input.class}{/if}"{if isset($value.val)} value="{$value.val|escape:'html':'UTF-8'}"{/if}{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]} checked="checked"{/if} />
                                            {$value[$input.values.name]}
                                    </label>
                            {/strip}
                    </div>
            {/foreach}
    {elseif $input.type == 'change-password'}
            <div class="row">
                    <div class="col-lg-12">
                            <button type="button" id="{$input.name}-btn-change" class="btn btn-default">
                                    <i class="icon-lock"></i>
                                    {l s='Change password...'}
                            </button>
                            <div id="{$input.name}-change-container" class="form-password-change well hide">
                                    <div class="form-group">
                                            <label for="old_passwd" class="control-label col-lg-2 required">
                                                    {l s='Current password'}
                                            </label>
                                            <div class="col-lg-10">
                                                    <div class="input-group fixed-width-lg">
                                                            <span class="input-group-addon">
                                                                    <i class="icon-unlock"></i>
                                                            </span>
                                                            <input type="password" id="old_passwd" name="old_passwd" class="form-control" value="" required="required" autocomplete="off">
                                                    </div>
                                            </div>
                                    </div>
                                    <hr />
                                    <div class="form-group">
                                            <label for="{$input.name}" class="required control-label col-lg-2">
                                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Password should be at least 8 characters long.'}">
                                                            {l s='New password'}
                                                    </span>
                                            </label>
                                            <div class="col-lg-9">
                                                    <div class="input-group fixed-width-lg">
                                                            <span class="input-group-addon">
                                                                    <i class="icon-key"></i>
                                                            </span>
                                                            <input type="password" id="{$input.name}" name="{$input.name}" class="{if isset($input.class)}{$input.class}{/if}" value="" required="required" autocomplete="off"/>
                                                    </div>
                                                    <span id="{$input.name}-output"></span>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                            <label for="{$input.name}2" class="required control-label col-lg-2">
                                                    {l s='Confirm password'}
                                            </label>
                                            <div class="col-lg-4">
                                                    <div class="input-group fixed-width-lg">
                                                            <span class="input-group-addon">
                                                                    <i class="icon-key"></i>
                                                            </span>
                                                            <input type="password" id="{$input.name}2" name="{$input.name}2" class="{if isset($input.class)}{$input.class}{/if}" value="" autocomplete="off"/>
                                                    </div>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                            <div class="col-lg-10 col-lg-offset-2">
                                                    <input type="text" class="form-control fixed-width-md pull-left" id="{$input.name}-generate-field" disabled="disabled">
                                                    <button type="button" id="{$input.name}-generate-btn" class="btn btn-default">
                                                            <i class="icon-random"></i>
                                                            {l s='Generate password'}
                                                    </button>
                                            </div>
                                    </div>
                                    <div class="row">
                                            <div class="col-lg-12">
                                                    <button type="button" id="{$input.name}-cancel-btn" class="btn btn-default">
                                                            <i class="icon-remove"></i>
                                                            {l s='Cancel'}
                                                    </button>
                                            </div>
                                    </div>
                            </div>
                    </div>
            </div>
            <script>
                    $(function(){
                            var $oldPwd = $('#old_passwd');
                            var $passwordField = $('#{$input.name}');
                            var $output = $('#{$input.name}-output');
                            var $generateBtn = $('#{$input.name}-generate-btn');
                            var $generateField = $('#{$input.name}-generate-field');
                            var $cancelBtn = $('#{$input.name}-cancel-btn');
                            var feedback = [
                                    { badge: 'text-danger', text: '{l s="Invalid" js=1}' },
                                    { badge: 'text-warning', text: '{l s="Okay" js=1}' },
                                    { badge: 'text-success', text: '{l s="Good" js=1}' },
                                    { badge: 'text-success', text: '{l s="Fabulous" js=1}' }
                            ];
                            $.passy.requirements.length.min = 8;
                            $.passy.requirements.characters = 'DIGIT';
                            $passwordField.passy(function(strength, valid) {
                                    $output.text(feedback[strength].text);
                                    $output.removeClass('text-danger').removeClass('text-warning').removeClass('text-success');
                                    $output.addClass(feedback[strength].badge);
                                    if (valid){
                                            $output.show();
                                    }
                                    else {
                                            $output.hide();
                                    }
                            });
                            var $container = $('#{$input.name}-change-container');
                            var $changeBtn = $('#{$input.name}-btn-change');
                            var $confirmPwd = $('#{$input.name}2');
                            $changeBtn.on('click',function(){
                                    $container.removeClass('hide');
                                    $changeBtn.addClass('hide');
                            });
                            $generateBtn.click(function() {
                                    $generateField.passy( 'generate', 8 );
                                    var generatedPassword = $generateField.val();
                                    $passwordField.val(generatedPassword);
                                    $confirmPwd.val(generatedPassword);
                            });
                            $cancelBtn.on('click',function() {
                                    $container.find("input").val("");
                                    $container.addClass('hide');
                                    $changeBtn.removeClass('hide');
                            });
                            $.validator.addMethod('password_same', function(value, element) {
                                    return $passwordField.val() == $confirmPwd.val();
                            }, '{l s="Invalid password confirmation" js=1}');
                            $('#employee_form').validate({
                                    rules: {
                                            "email": {
                                                    email: true
                                            },
                                            "{$input.name}" : {
                                                    minlength: 8
                                            },
                                            "{$input.name}2": {
                                                    password_same: true
                                            },
                                            "old_passwd" : {},
                                    },
                                    // override jquery validate plugin defaults for bootstrap 3
                                    highlight: function(element) {
                                            $(element).closest('.form-group').addClass('has-error');
                                    },
                                    unhighlight: function(element) {
                                            $(element).closest('.form-group').removeClass('has-error');
                                    },
                                    errorElement: 'span',
                                    errorClass: 'help-block',
                                    errorPlacement: function(error, element) {
                                            if(element.parent('.input-group').length) {
                                                    error.insertAfter(element.parent());
                                            } else {
                                                    error.insertAfter(element);
                                            }
                                    }
                            });
                    });
            </script>
    {elseif $input.type == 'password'}
            <div class="input-group fixed-width-lg">
                    <span class="input-group-addon">
                            <i class="icon-key"></i>
                    </span>
                    <input type="password"
                            id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
                            name="{$input.name}"
                            class="{if isset($input.class)}{$input.class}{/if}"
                            value=""
                            {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if}
                            {if isset($input.required) && $input.required } required="required" {/if} />
            </div>

    {elseif $input.type == 'birthday'}
    <div class="form-group">
            {foreach $input.options as $key => $select}
            <div class="col-lg-2">
                    <select name="{$key}" class="fixed-width-lg{if isset($input.class)} {$input.class}{/if}">
                            <option value="">-</option>
                            {if $key == 'months'}
                                    {*
                                            This comment is useful to the translator tools /!\ do not remove them
                                            {l s='January'}
                                            {l s='February'}
                                            {l s='March'}
                                            {l s='April'}
                                            {l s='May'}
                                            {l s='June'}
                                            {l s='July'}
                                            {l s='August'}
                                            {l s='September'}
                                            {l s='October'}
                                            {l s='November'}
                                            {l s='December'}
                                    *}
                                    {foreach $select as $k => $v}
                                            <option value="{$k}" {if $k == $fields_value[$key]}selected="selected"{/if}>{l s=$v}</option>
                                    {/foreach}
                            {else}
                                    {foreach $select as $v}
                                            <option value="{$v}" {if $v == $fields_value[$key]}selected="selected"{/if}>{$v}</option>
                                    {/foreach}
                            {/if}
                    </select>
            </div>
            {/foreach}
    </div>
    {elseif $input.type == 'group'}
            {assign var=groups value=$input.values}
            {include file='helpers/form/form_group.tpl'}
    {elseif $input.type == 'shop'}
            {$input.html}
    {elseif $input.type == 'categories'}
            {eval var='{$categories_tree_'|cat:$input.name|cat:'}'}
    {elseif $input.type == 'file'}
            {$input.file}
    {elseif $input.type == 'categories_select'}
            {$input.category_tree}
    {elseif $input.type == 'asso_shop' && isset($asso_shop) && $asso_shop}
            {$asso_shop}
    {elseif $input.type == 'color'}
    <div class="form-group">
            <div class="col-lg-2">
                    <div class="row">
                            <div class="input-group">
                                    <input type="color"
                                    data-hex="true"
                                    {if isset($input.class)} class="{$input.class}"
                                    {else} class="color mColorPickerInput"{/if}
                                    name="{$input.name}"
                                    value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                            </div>
                    </div>
            </div>
    </div>
    {elseif $input.type == 'date'}
            <div class="row">
                    <div class="input-group col-lg-4">
                            <input
                                    id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
                                    type="text"
                                    data-hex="true"
                                    {if isset($input.class)} class="{$input.class}"
                                    {else}class="datepicker"{/if}
                                    name="{$input.name}"
                                    value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                            <span class="input-group-addon">
                                    <i class="icon-calendar-empty"></i>
                            </span>
                    </div>
            </div>
    {elseif $input.type == 'datetime'}
            <div class="row">
                    <div class="input-group col-lg-4">
                            <input
                                    id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
                                    type="text"
                                    data-hex="true"
                                    {if isset($input.class)} class="{$input.class}"
                                    {else} class="datetimepicker"{/if}
                                    name="{$input.name}"
                                    value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                            <span class="input-group-addon">
                                    <i class="icon-calendar-empty"></i>
                            </span>
                    </div>
            </div>
    {elseif $input.type == 'free'}
            {$fields_value[$input.name]}
    {elseif $input.type == 'html'}
            {if isset($input.html_content)}
                    {$input.html_content}
            {else}
                    {$input.name}
            {/if}
    {/if}
{/block}{* end block input *}
{$smarty.block.parent}
