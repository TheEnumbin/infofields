/**
* 2007-2024 PrestaShop
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
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$(document).ready(function () {

    const $toogle_langs = ($locale, $old_locale) => {
        $('.inf-meta-form-wrapper-' + $locale).toggleClass('hidden-field')
        $('.inf-meta-form-wrapper-' + $old_locale).toggleClass('hidden-field')
        $(".inf_lang_iso").val($locale);
    }

    $(document).on('click', '.js-locale-item', function () {
        let $old_locale = $(".inf_lang_iso").val().trim();
        let $this = $(this);
        let $locale = $this.data('locale').trim();
        $toogle_langs($locale, $old_locale);
    });

    $(document).on('click', '#saveInfoMeta', function () {
        let $iso_code = $(".js-locale-btn").html().trim();
        $iso_code = $iso_code.toLowerCase();
        let $prd_id = $('.inf_input_prd').val();
        let $this = $(this);
        let $wrapper = $this.parent('.inf-meta-form-wrapper');
        let $infofield_id = $wrapper.find('.inf_input_id').val();
        let $infofield_type = $wrapper.find('.inf_input_type').val();
        var $value = '';
        let dataarr = {}
        if ($infofield_type == "4") {
            $value = $wrapper.find("input[type='radio'][name='" + 'inf_metafield_' + $infofield_id + '_' + $iso_code + "']:checked").val();
        } else if ($infofield_type == "7") {
            if ($wrapper.find("input[type='checkbox'][name='" + 'inf_metafield_' + $infofield_id + '_' + $iso_code + "']").prop('checked')) {
                $value = 1;
            } else {
                $value = 0;
            }
        } else if ($infofield_type == "5") {
            var fileInput = $wrapper.find('#inf_metafield_' + $infofield_id + '_' + $iso_code)[0];
            var formData = new FormData();
            $value = fileInput.files[0];
            dataarr = formData;
            dataarr.append('inf_value', $value);
            dataarr.append('controller', 'AdminAjaxInfofields');
            dataarr.append('action', 'SaveInfometa');
            dataarr.append('iso_code', $iso_code);
            dataarr.append('inf_id', $infofield_id);
            dataarr.append('inf_type', $infofield_type);
            dataarr.append('prd_id', $prd_id);
            dataarr.append('ajax', true);
        } else {
            $value = $wrapper.find('#inf_metafield_' + $infofield_id + '_' + $iso_code).val();
        }
        console.log(typeof dataarr)
        if ($infofield_type != "5") {
            dataarr.controller = 'AdminAjaxInfofields';
            dataarr.action = 'SaveInfometa';
            dataarr.iso_code = $iso_code;
            dataarr.inf_id = $infofield_id;
            dataarr.inf_type = $infofield_type;
            dataarr.prd_id = $prd_id;
            dataarr.inf_value = $value;
            dataarr.ajax = true;
        }

        $.ajax({
            type: 'POST',
            url: infofields_ajax_url,
            dataType: 'html',  // Expect HTML response (or change to 'json' if backend returns JSON)
            data: dataarr,    // Send the FormData object (contains both the file and the other data)
            // contentType: true,  // Important: tells jQuery not to process content type automatically
            // processData: true,  // Important: tells jQuery not to process the data automatically
            success: function (response) {
                // Handle the success response
                // console.log('File and data uploaded successfully:', response);
            },
            error: function (error) {
                // Handle any errors
                // console.error('Error uploading file and data:', error);
            }
        });
        // $.ajax({
        //     type: 'POST',
        //     url: infofields_ajax_url,
        //     dataType: 'html',
        //     data: {
        //         controller: 'AdminAjaxInfofields',
        //         action: 'SaveInfometa',
        //         iso_code: $iso_code,
        //         inf_id: $infofield_id,
        //         inf_type: $infofield_type,
        //         prd_id: $prd_id,
        //         inf_value: $value,
        //         ajax: true
        //     },
        //     success: function (data) {

        //     }
        // });
    });
    $(document).on('click', '.inf-delete-btn', function (e) {
        e.preventDefault()
        const $this = $(this)
        $.ajax({
            type: 'POST',
            url: infofields_ajax_url,
            dataType: 'html',
            data: {
                controller: 'AdminAjaxInfofields',
                action: 'DeleteFileImg',
                inf_id: $this.data('inf_id'),
                item_id: $this.data('item_id'),
                field_type: $this.data('type'),
                ajax: true
            },
            success: function (data) {
                let response = JSON.parse(data);
                if (response.deleted == true) {
                    console.log($this.closest(".preview-wrapper"))
                    $this.closest(".preview-wrapper").html("")
                }
            }
        });
    });

});
