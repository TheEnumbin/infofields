{*
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
*}
<div class="prextrameta-wrapper">
    <div class="prextrameta-sec prextrameta-header">
        <input type="hidden" id="prd_id" name="prd_id" value="{$omnibus_prd_id}">
        <select class="prextrameta-lang-changer" name="prextrameta_lang_changer" id="prextrameta_lang_changer">
            {foreach from=$omnibus_langs item=omnibus_lang}
                {if $omnibus_lang.id_lang == $omnibus_curr_lang}
                    <option selected="selected" value="{$omnibus_lang.id_lang}">{$omnibus_lang.name}</option>
                {else}
                    <option value="{$omnibus_lang.id_lang}">{$omnibus_lang.name}</option>
                {/if}
            {/foreach}
        </select>
    </div>
    <div class="prextrameta-sec prextrameta-meta-section">
        <div><h2>Pricing Extra Metadata Section</h3></div>
        <div class="prextrameta_meta_table">
            <table id="prextrameta_meta_table">
                <tr>
                    <th>Meta Data</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
                {if $omnibus_meta}
                    <tr class="prextrameta-meta_datam" id="prextrameta_meta_{$omnibus_meta.id_prextrameta}">
                        <td>{$omnibus_meta.meta_data}</td>
                        <td>{$omnibus_meta.start_date}</td>
                        <td>{$omnibus_meta.end_date}</td>
                    </tr>
                {/if}
            </table>
        </div>
        <div class="prextrameta-meta-add">
            <label><h3>Input Your Extra Meta Data Here</h3></label>
            {if $omnibus_meta}
                <input type="hidden" id="prextrameta_extra_meta_id" name="prextrameta_extra_meta_id" value="{$omnibus_meta.id_prextrameta}">
            {else}
                <input type="hidden" id="prextrameta_extra_meta_id" name="prextrameta_extra_meta_id" value="0">
            {/if}
            <input type="text" id="prextrameta_extra_meta" name="extra_meta" value="{$omnibus_meta.id_prextrameta}">
            <div class="prextrameta-meta-dates">
                <div class="prextrameta-meta-date-item">
                    <label>Start date for showing this meta</label>
                    <input class="prextrameta-custom-field" type="date" id="prextrameta_mdate_start" name="prextrameta_mdate_start">
                </div>
                <div class="prextrameta-meta-date-item">
                    <label>End date for showing this meta</label>
                    <input class="prextrameta-custom-field" type="date" id="prextrameta_mdate_end" name="prextrameta_mdate_end">
                </div>
                <div class="prextrameta-meta-date-hint">
                    <span>Keep these empty to show the meta always!!!</span>
                </div>
            </div>
            <button id="prextrameta_meta_add" class="btn btn-primary" type="button">Submit</button>
        </div>
    </div>
    <div class="prextrameta-sec prextrameta-price-history">
        <div><h2>Omnibus Pricing Section</h3></div>
        <table id="prextrameta_history_table">
            <tr>
                <th>Date</th>
                <th>Price Amount</th>
                <th>Price Type</th>
                <th>Action</th>
            </tr>
            {foreach from=$omnibus_prices item=omnibus_price}
                <tr class="prextrameta-history-datam" id="prextrameta_history_{$omnibus_price.id}">
                    <td>{$omnibus_price.date}</td>
                    <td>{$omnibus_price.price}</td>
                    <td>{$omnibus_price.promotext}</td>
                    <td><button  class="prextrameta_history_delete btn btn-danger" type="button" value="{$omnibus_price.id}">Delete</button></td>
                </tr>
            {/foreach}
        </table>
    </div>
    <div class="prextrameta-sec prextrameta-custom-section">
        <h3>Add Your Custom Price</h3>
        <div class="prextrameta-custom-fields">
            <input class="prextrameta-custom-field" type="number" id="price_amount" name="price_amount">
            <input class="prextrameta-custom-field" type="date" id="promodate" name="promodate">
            <select class="prextrameta-custom-field promnibus-promo" name="price_type" id="price_type">
                <option>Select Price Type</option>
                <option value="1">Promotional</option>
                <option value="0">Normal</option>
            </select>
            <button id="prextrameta_custom_price_add" class="btn btn-primary" type="button">Add</button>
        </div>
    </div>
</div>
