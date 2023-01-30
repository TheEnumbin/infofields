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
<div class="pricingomnibus-wrapper">
    <div class="pricingomnibus-sec pricingomnibus-header">
        <input type="hidden" id="prd_id" name="prd_id" value="{$omnibus_prd_id}">
        <select class="pricingomnibus-lang-changer" name="pricingomnibus_lang_changer" id="pricingomnibus_lang_changer">
            {foreach from=$omnibus_langs item=omnibus_lang}
                {if $omnibus_lang.id_lang == $omnibus_curr_lang}
                    <option selected="selected" value="{$omnibus_lang.id_lang}">{$omnibus_lang.name}</option>
                {else}
                    <option value="{$omnibus_lang.id_lang}">{$omnibus_lang.name}</option>
                {/if}
            {/foreach}
        </select>
    </div>
    <div class="pricingomnibus-sec pricingomnibus-meta-section">
        <div><h2>Pricing Extra Metadata Section</h3></div>
        <div class="pricingomnibus_meta_table">
            <table id="pricingomnibus_meta_table">
                <tr>
                    <th>Meta Data</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
                {if $omnibus_meta}
                    <tr class="pricingomnibus-meta_datam" id="pricingomnibus_meta_{$omnibus_meta.id_prextrameta}">
                        <td>{$omnibus_meta.meta_data}</td>
                        <td>{$omnibus_meta.start_date}</td>
                        <td>{$omnibus_meta.end_date}</td>
                    </tr>
                {/if}
            </table>
        </div>
        <div class="pricingomnibus-meta-add">
            <label><h3>Input Your Extra Meta Data Here</h3></label>
            {if $omnibus_meta}
                <input type="hidden" id="promnibus_extra_meta_id" name="promnibus_extra_meta_id" value="{$omnibus_meta.id_prextrameta}">
            {else}
                <input type="hidden" id="promnibus_extra_meta_id" name="promnibus_extra_meta_id" value="0">
            {/if}
            <input type="text" id="promnibus_extra_meta" name="extra_meta" value="{$omnibus_meta.id_prextrameta}">
            <div class="pricingomnibus-meta-dates">
                <div class="pricingomnibus-meta-date-item">
                    <label>Start date for showing this meta</label>
                    <input class="pricingomnibus-custom-field" type="date" id="promnibus_mdate_start" name="promnibus_mdate_start">
                </div>
                <div class="pricingomnibus-meta-date-item">
                    <label>End date for showing this meta</label>
                    <input class="pricingomnibus-custom-field" type="date" id="promnibus_mdate_end" name="promnibus_mdate_end">
                </div>
                <div class="pricingomnibus-meta-date-hint">
                    <span>Keep these empty to show the meta always!!!</span>
                </div>
            </div>
            <button id="promnibus_meta_add" class="btn btn-primary" type="button">Submit</button>
        </div>
    </div>
    <div class="pricingomnibus-sec pricingomnibus-price-history">
        <div><h2>Omnibus Pricing Section</h3></div>
        <table id="pricingomnibus_history_table">
            <tr>
                <th>Date</th>
                <th>Price Amount</th>
                <th>Price Type</th>
                <th>Action</th>
            </tr>
            {foreach from=$omnibus_prices item=omnibus_price}
                <tr class="pricingomnibus-history-datam" id="pricingomnibus_history_{$omnibus_price.id}">
                    <td>{$omnibus_price.date}</td>
                    <td>{$omnibus_price.price}</td>
                    <td>{$omnibus_price.promotext}</td>
                    <td><button  class="pricingomnibus_history_delete btn btn-danger" type="button" value="{$omnibus_price.id}">Delete</button></td>
                </tr>
            {/foreach}
        </table>
    </div>
    <div class="pricingomnibus-sec pricingomnibus-custom-section">
        <h3>Add Your Custom Price</h3>
        <div class="pricingomnibus-custom-fields">
            <input class="pricingomnibus-custom-field" type="number" id="price_amount" name="price_amount">
            <input class="pricingomnibus-custom-field" type="date" id="promodate" name="promodate">
            <select class="pricingomnibus-custom-field promnibus-promo" name="price_type" id="price_type">
                <option>Select Price Type</option>
                <option value="1">Promotional</option>
                <option value="0">Normal</option>
            </select>
            <button id="promnibus_custom_price_add" class="btn btn-primary" type="button">Add</button>
        </div>
    </div>
</div>
