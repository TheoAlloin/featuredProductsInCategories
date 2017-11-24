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
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */



var currentToken = "62b7dcb22b0637712c5bc9355ca531fd";
console.log(currentToken);

var treeClickFunc = function () {
    var newURL = window.location.protocol + "//" + window.location.host + window.location.pathname;
    var queryString = window.location.search.replace(/&id_category=[0-9]*/, "") + "&id_category=" + $(this).val();
    location.href = newURL + queryString; // hash part is dropped: window.location.hash
};

function addDefaultCategory(elem)
{
    $('select#id_category_default').append('<option value="' + elem.val() + '">' + (elem.val() != 1 ? elem.parent().find('label').html() : home) + '</option>');
    if ($('select#id_category_default option').length > 0)
    {
        $('select#id_category_default').closest('.form-group').show();
        $('#no_default_category').hide();
    }
}

function checkAllAssociatedCategories($tree)
{
    $tree.find(':input[type=checkbox]').each(function () {
        $(this).prop('checked', true);

        addDefaultCategory($(this));
        $(this).parent().addClass('tree-selected');
    });
}

function uncheckAllAssociatedCategories($tree)
{
    $tree.find(':input[type=checkbox]').each(function () {
        $(this).prop('checked', false);

        $('select#id_category_default option[value=' + $(this).val() + ']').remove();
        if ($('select#id_category_default option').length == 0)
        {
            $('select#id_category_default').closest('.form-group').hide();
            $('#no_default_category').show();
        }

        $(this).parent().removeClass('tree-selected');
    });
}
$('#associated-categories-tree-categories-search').bind('typeahead:selected', function (obj, datum) {
    var match = $('#associated-categories-tree').find(':input[value="' + datum.id_category + '"]').first();
    if (match.length)
    {
        match.each(function () {
            $(this).prop("checked", true);
            $(this).parent().addClass("tree-selected");
            $(this).parents('ul.tree').each(function () {
                $(this).show();
                $(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
            });
            addDefaultCategory($(this));
        }
        );
    } else
    {
        var selected = [];
        that = this;
        $('#associated-categories-tree').find('.tree-selected input').each(
                function ()
                {
                    selected.push($(this).val());
                }
        );

        $.get(
                'ajax-tab.php',
                {controller: 'AdminProducts', token: currentToken, action: 'getCategoryTree', fullTree: 1, selected: selected},
                function (content) {

                    $('#associated-categories-tree').html(content);
                    $('#associated-categories-tree').tree('init');
                    $('#associated-categories-tree').find(':input[value="' + datum.id_category + '"]').each(function () {
                        $(this).prop("checked", true);
                        $(this).parent().addClass("tree-selected");
                        $(this).parents('ul.tree').each(function () {
                            $(this).show();
                            $(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
                        });
                        full_loaded = true;
                    }
                    );
                }
        );
    }
});
function startTree() {
    if (typeof $.fn.tree === 'undefined') {
        setTimeout(startTree, 100);
        return;
    }

    $('#associated-categories-tree').tree('collapseAll');
    $('#associated-categories-tree').find(':input[type=radio]').click(treeClickFunc);

    $('#no_default_category').hide();
    var selected_categories = new Array("2", "3", "8", "11");

    if (selected_categories.length > 1)
        $('#expand-all-associated-categories-tree').hide();
    else
        $('#collapse-all-associated-categories-tree').hide();

    $('#associated-categories-tree').find(':input').each(function () {
        if ($.inArray($(this).val(), selected_categories) != -1)
        {
            $(this).prop("checked", true);
            $(this).parent().addClass("tree-selected");
            $(this).parents('ul.tree').each(function () {
                $(this).show();
                $(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
            });
        }
    });
}
startTree();
