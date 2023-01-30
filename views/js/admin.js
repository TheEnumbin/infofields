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
    $(document).on('change', '#prextrameta_lang_changer', function(){
        var $val = $(this).val();
        var $prdid = $('#prd_id').val();
        $.ajax({
            type: 'POST',
            url: prextrameta_ajax_url,
            dataType: 'html',
            data: {
                controller : 'AdminAjaxPrextrameta',
                action : 'PrextrametaChangeLang',
                prdid : $prdid,
                langid : $val,
                shopid : prextrameta_shop_id,
                ajax : true
            },
            success : function(data) {
                var $data = JSON.parse(data);
                if(typeof $data.success !== 'undefined' && $data.success){
                    console.log($data.omnibus_meta);
                    $('#prextrameta_meta_table').find(".prextrameta-meta_datam").remove();
                    if($data.omnibus_meta.length === 0){
                        $('#prextrameta_extra_meta_id').val(0);
                    }else{
                        $('#prextrameta_meta_table').append('<tr class="prextrameta-meta_datam" id="prextrameta_history_' + $data.omnibus_meta.id_prextrameta + '">' 
                            + '<td>' + $data.omnibus_meta.meta_data + '</td><td>' + $data.omnibus_meta.start_date + '</td><td>' + $data.omnibus_meta.end_date + '</td>'
                            + '</tr>');
                        $('#prextrameta_extra_meta_id').val($data.omnibus_meta.id_prextrameta);
                    }
                    
                    $('#prextrameta_history_table').find(".prextrameta-history-datam").remove();
                    $.each( $data.omnibus_prices, function( key, value ) {
                        $('#prextrameta_history_table').append('<tr class="prextrameta-history-datam" id="prextrameta_history_' + value.id + '">' 
                        + '<td>' + value.date + '</td><td>' + value.price + '</td><td>' + value.promotext + '</td>'
                        + '<td><button  class="prextrameta_history_delete btn btn-danger" type="button" value="' + value.id + '">Delete</button></td>'
                        + '</tr>');
                    });
                }
            }
        });
    });
    $(document).on('click', '#prextrameta_meta_add', function(){
        var $extra_meta_id = $('#prextrameta_extra_meta_id').val();
        var $prdid = $('#prd_id').val();
        var $extrameta = $('#prextrameta_extra_meta').val();
        var $start_date = $('#prextrameta_mdate_start').val();
        var $end_date = $('#prextrameta_mdate_end').val();
        var $langid = $('#prextrameta_lang_changer').find(":selected").val();
        $.ajax({
            type: 'POST',
            url: prextrameta_ajax_url,
            dataType: 'html',
            data: {
                controller : 'AdminAjaxPrextrameta',
                action : 'AddExtraMeta',
                exmtid : $extra_meta_id,
                prdid : $prdid,
                meta : $extrameta,
                s_date : $start_date,
                e_date : $end_date,
                langid : $langid,
                shopid : prextrameta_shop_id,
                ajax : true
            },
            success : function(data) {
                var $data = JSON.parse(data);
                if(typeof $data.success !== 'undefined' && $data.success){
                    $('#prextrameta_meta_' + $extra_meta_id).remove();
                    $('#prextrameta_meta_table').append('<tr class="prextrameta-meta_datam" id="prextrameta_meta_' + $data.id_inserted + '">' 
                    + '<td>' + $data.meta_data + '</td><td>' + $data.start_date + '</td><td>' + $data.end_date + '</td>'
                    + '</tr>');
                    $('#prextrameta_extra_meta').val("");
                    $('#prextrameta_mdate_start').val("");
                    $('#prextrameta_mdate_end').val("");
                    $('#prextrameta_extra_meta_id').val($data.id_inserted);
                    $('.prextrameta_meta_notice').html($data.msg)
                }
            }
        });
    });
    $(document).on('click', '#prextrameta_custom_price_add', function(){
        var $prdid = $('#prd_id').val();
        var $price = $('#price_amount').val();
        var $price_type = $('#price_type').val();
        var $promodate = $('#promodate').val();
        var $langid = $('#prextrameta_lang_changer').find(":selected").val();
        $.ajax({
            type: 'POST',
            url: prextrameta_ajax_url,
            dataType: 'html',
            data: {
                controller : 'AdminAjaxPrextrameta',
                action : 'AddCustomPrice',
                prdid : $prdid,
                price : $price,
                pricetype : $price_type,
                promodate : $promodate,
                langid : $langid,
                shopid : prextrameta_shop_id,
                ajax : true
            },
            success : function(data) {
                var $data = JSON.parse(data);
                if(typeof $data.success !== 'undefined' && $data.success){
                    $('#prextrameta_history_table').append('<tr class="prextrameta-history-datam"  id="prextrameta_history_' + $data.id_inserted + '">' 
                    + '<td>' + $data.date + '</td><td>' + $data.price + '</td><td>' + $data.promo + '</td>'
                    + '</tr>');
                    $('#price_amount').val("");
                    $('#promodate').val("");
                    $('#price_type').prop('selectedIndex',0);
                }
            }
        });
    });
    $(document).on('click', '.prextrameta_history_delete', function(){
        var $val = $(this).val();
        $.ajax({
            type: 'POST',
            url: prextrameta_ajax_url,
            dataType: 'html',
            data: {
                controller : 'AdminAjaxPrextrameta',
                action : 'DeleteCustomPrice',
                pricing_id : $val,
                ajax : true
            },
            success : function(data) {
                var $data = JSON.parse(data);
                if(typeof $data.success !== 'undefined' && $data.success){
                    $('#prextrameta_history_' + $val).remove();
                }
            }
        });
    });
});
