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
        const iso_local = $(".js-locale-btn");
        let $iso_code = infofields_def_iso_code;
        if (iso_local.length > 0) {
            $iso_code = $(".js-locale-btn").html().trim();
            $iso_code = $iso_code.toLowerCase();
        }
        let $prd_id = $('.inf_input_prd').val();
        let $this = $(this);
        let $wrapper = $this.parent('.inf-meta-form-wrapper');
        let $infofield_id = $wrapper.find('.inf_input_id').val();
        let $infofield_type = $wrapper.find('.inf_input_type').val();
        var $value = '';
        let dataarr = {}
        let contentType = 'application/x-www-form-urlencoded'
        let processData = true;
        if ($infofield_type == "4") {
            $value = $wrapper.find("input[type='radio'][name='" + 'inf_metafield_' + $infofield_id + '_' + $iso_code + "']:checked").val();
        } else if ($infofield_type == "7") {
            if ($wrapper.find("input[type='checkbox'][name='" + 'inf_metafield_' + $infofield_id + '_' + $iso_code + "']").prop('checked')) {
                $value = 1;
            } else {
                $value = 0;
            }
        } else if ($infofield_type == "5" || $infofield_type == "9") {
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
            contentType = false
            processData = false
        } else {
            $value = $wrapper.find('#inf_metafield_' + $infofield_id + '_' + $iso_code).val();
        }
        if ($infofield_type != "5" && $infofield_type != "9") {
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
            contentType: contentType,  // Important: tells jQuery not to process content type automatically
            processData: processData,  // Important: tells jQuery not to process the data automatically
            success: function (response) {
                const responseArr = JSON.parse(response)
                if (responseArr.success == true) {
                    if ($infofield_type == "5") {
                        $this.siblings('.inf-meta-form-group').find('.preview-wrapper').append(
                            `<figure class="figure">
                                        <img src="${responseArr.img_dir}/infofield/${responseArr.file.file}_backend_default.${responseArr.file.ext}"
                                            class="figure-img img-fluid img-thumbnail">
                                    </figure>
                                    <div class="d-block">
                                        <button class="btn btn-danger inf-delete-btn mt-2" data-inf_id="${$infofield_id}"
                                            data-item_id="${$prd_id}"
                                            data-file="${responseArr.img_dir}/infofield/${responseArr.file.file}_backend_default.${responseArr.file.ext}"
                                            data-type="image">Delete
                                            File</button>
                                        <img src="/path/to/loader.gif" class="loader" style="display:none;" alt="Loading">
                                    </div>`
                        )
                    } else if ($infofield_type == "9") {
                        $this.siblings('.inf-meta-form-group').find('.preview-wrapper').append(
                            `<div class="file-preview preview-wrapper" id="file-container">
                                        <a href="${responseArr.img_dir}/infofield/${responseArr.file.file}" target="_blank"
                                            class="download-link">
                                            Download File
                                        </a>
                                        <div>
                                            <button class="btn btn-danger inf-delete-btn mt-2" data-inf_id="${$infofield_id}"
                                            data-item_id="${$prd_id}"
                                            data-file="${responseArr.img_dir}/infofield/${responseArr.file.file}"
                                            data-type="file">Delete
                                            File</button>
                                            <img src="/path/to/loader.gif" class="loader" style="display:none;" alt="Loading">
                                        </div>
                                    </div>`
                        )
                    }
                }
            },
            error: function (error) {
            }
        });
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
    $(document).on('click', '.inf-import-bt', function (e) {
        e.preventDefault()
        const $this = $(this)
        const $import_element_type = $this.closest('.inf-csv-bt').prev('.inf-csv-type').find('input')
        const $import_element = $import_element_type.closest('.inf-csv-type').prev('.inf-csv-input').find('input')
        var fileInput = $import_element[0];
        if (!fileInput.files.length) {
            alert('Please select a CSV file.');
            return;
        }
        $this.siblings('.inf-import-stop').removeClass('hidden')
        inf_ajax_import(fileInput.files[0], $import_element_type.val(), 0)
    });

    function inf_ajax_import(file, type, offset) {
        let dataarr = {}
        var formData = new FormData();
        dataarr = formData;
        dataarr.append('csv_file', file);
        dataarr.append('csv_type', type);
        dataarr.append('offset', offset);
        dataarr.append('controller', 'AdminAjaxInfofields');
        dataarr.append('action', 'ImportCSV');
        dataarr.append('ajax', true);
        $.ajax({
            type: 'POST',
            url: infofields_ajax_url,
            dataType: 'html',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                const responseArr = JSON.parse(response)
                if (responseArr.continue == true) {
                    inf_ajax_import(file, type, responseArr.offset)
                } else {
                    $this.siblings('.inf-import-stop').addClass('hidden')
                }
            },
            error: function (error) {
            }
        });
    }
});
