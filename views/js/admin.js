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
    $(document).on('change', '#pricingomnibus_lang_changer', function(){
        var $val = $(this).val();
        var $prdid = $('#prd_id').val();
        $.ajax({
            type: 'POST',
            url: promnibus_ajax_url,
            dataType: 'html',
            data: {
                controller : 'AdminAjaxPromnibus',
                action : 'PricingomnibusChangeLang',
                prdid : $prdid,
                langid : $val,
                shopid : promnibus_shop_id,
                ajax : true
            },
            success : function(data) {
                var $data = JSON.parse(data);
                if(typeof $data.success !== 'undefined' && $data.success){
                    console.log($data.omnibus_meta);
                    $('#pricingomnibus_meta_table').find(".pricingomnibus-meta_datam").remove();
                    if($data.omnibus_meta.length === 0){
                        $('#promnibus_extra_meta_id').val(0);
                    }else{
                        $('#pricingomnibus_meta_table').append('<tr class="pricingomnibus-meta_datam" id="pricingomnibus_history_' + $data.omnibus_meta.id_prextrameta + '">' 
                            + '<td>' + $data.omnibus_meta.meta_data + '</td><td>' + $data.omnibus_meta.start_date + '</td><td>' + $data.omnibus_meta.end_date + '</td>'
                            + '</tr>');
                        $('#promnibus_extra_meta_id').val($data.omnibus_meta.id_prextrameta);
                    }
                    
                    $('#pricingomnibus_history_table').find(".pricingomnibus-history-datam").remove();
                    $.each( $data.omnibus_prices, function( key, value ) {
                        $('#pricingomnibus_history_table').append('<tr class="pricingomnibus-history-datam" id="pricingomnibus_history_' + value.id + '">' 
                        + '<td>' + value.date + '</td><td>' + value.price + '</td><td>' + value.promotext + '</td>'
                        + '<td><button  class="pricingomnibus_history_delete btn btn-danger" type="button" value="' + value.id + '">Delete</button></td>'
                        + '</tr>');
                    });
                }
            }
        });
    });
    $(document).on('click', '#promnibus_meta_add', function(){
        var $extra_meta_id = $('#promnibus_extra_meta_id').val();
        var $prdid = $('#prd_id').val();
        var $extrameta = $('#promnibus_extra_meta').val();
        var $start_date = $('#promnibus_mdate_start').val();
        var $end_date = $('#promnibus_mdate_end').val();
        var $langid = $('#pricingomnibus_lang_changer').find(":selected").val();
        $.ajax({
            type: 'POST',
            url: promnibus_ajax_url,
            dataType: 'html',
            data: {
                controller : 'AdminAjaxPromnibus',
                action : 'AddExtraMeta',
                exmtid : $extra_meta_id,
                prdid : $prdid,
                meta : $extrameta,
                s_date : $start_date,
                e_date : $end_date,
                langid : $langid,
                shopid : promnibus_shop_id,
                ajax : true
            },
            success : function(data) {
                var $data = JSON.parse(data);
                if(typeof $data.success !== 'undefined' && $data.success){
                    $('#pricingomnibus_meta_' + $extra_meta_id).remove();
                    $('#pricingomnibus_meta_table').append('<tr class="pricingomnibus-meta_datam" id="pricingomnibus_meta_' + $data.id_inserted + '">' 
                    + '<td>' + $data.meta_data + '</td><td>' + $data.start_date + '</td><td>' + $data.end_date + '</td>'
                    + '</tr>');
                    $('#promnibus_extra_meta').val("");
                    $('#promnibus_mdate_start').val("");
                    $('#promnibus_mdate_end').val("");
                    $('#promnibus_extra_meta_id').val($data.id_inserted);
                    $('.promnibus_meta_notice').html($data.msg)
                }
            }
        });
    });
    $(document).on('click', '#promnibus_custom_price_add', function(){
        var $prdid = $('#prd_id').val();
        var $price = $('#price_amount').val();
        var $price_type = $('#price_type').val();
        var $promodate = $('#promodate').val();
        var $langid = $('#pricingomnibus_lang_changer').find(":selected").val();
        $.ajax({
            type: 'POST',
            url: promnibus_ajax_url,
            dataType: 'html',
            data: {
                controller : 'AdminAjaxPromnibus',
                action : 'AddCustomPrice',
                prdid : $prdid,
                price : $price,
                pricetype : $price_type,
                promodate : $promodate,
                langid : $langid,
                shopid : promnibus_shop_id,
                ajax : true
            },
            success : function(data) {
                var $data = JSON.parse(data);
                if(typeof $data.success !== 'undefined' && $data.success){
                    $('#pricingomnibus_history_table').append('<tr class="pricingomnibus-history-datam"  id="pricingomnibus_history_' + $data.id_inserted + '">' 
                    + '<td>' + $data.date + '</td><td>' + $data.price + '</td><td>' + $data.promo + '</td>'
                    + '</tr>');
                    $('#price_amount').val("");
                    $('#promodate').val("");
                    $('#price_type').prop('selectedIndex',0);
                }
            }
        });
    });
    $(document).on('click', '.pricingomnibus_history_delete', function(){
        var $val = $(this).val();
        $.ajax({
            type: 'POST',
            url: promnibus_ajax_url,
            dataType: 'html',
            data: {
                controller : 'AdminAjaxPromnibus',
                action : 'DeleteCustomPrice',
                pricing_id : $val,
                ajax : true
            },
            success : function(data) {
                var $data = JSON.parse(data);
                if(typeof $data.success !== 'undefined' && $data.success){
                    $('#pricingomnibus_history_' + $val).remove();
                }
            }
        });
    });
});
