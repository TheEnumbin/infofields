/**
* 2007-2023 PrestaShop
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
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$(document).ready(function() {

    const $toogle_langs = ($locale, $old_locale) => {
        $('.inf-meta-form-wrapper-' + $locale).toggleClass('hidden-field')
        $('.inf-meta-form-wrapper-' + $old_locale).toggleClass('hidden-field')
        $(".inf_lang_iso").val($locale);
    }

    $(document).on('click', '.js-locale-item', function(){
        let $old_locale = $(".inf_lang_iso").val().trim();
        let $this = $(this);
        let $locale = $this.data('locale').trim();
        $toogle_langs($locale, $old_locale);
    });

    $(document).on('click', '#saveInfoMeta', function(){
        let $iso_code = $(".js-locale-btn").html().trim();
        let $prd_id = $('.inf_input_prd').val();
        let $this = $(this);
        let $wrapper = $this.parent('.inf-meta-form-wrapper');
        let $infofield_id = $wrapper.find('.inf_input_id').val();
        let $value = $wrapper.find('#inf_metafield_' + $infofield_id + '_' + $iso_code).val();
        $.ajax({
            type: 'POST',
            url: infofields_ajax_url,
            dataType: 'html',
            data: {
                controller : 'AdminAjaxInfofields',
                action : 'SaveInfometa',
                iso_code : $iso_code,
                inf_id : $infofield_id,
                prd_id : $prd_id,
                inf_value : $value,
                ajax : true
            },
            success : function(data) {

            }
        });
    });
});
