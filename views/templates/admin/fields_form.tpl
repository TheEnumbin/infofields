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
<div class="inf-meta-wrapper">
    <input type="hidden" class="inf_lang_iso" value="{$langs[$id_lang]}">
    <input type="hidden" class="inf_input_prd" value="{$id_prd}">
    {foreach from=$infofields item=infofield}
        {assign var="hidden" value=''}
        {if $infofield.id_lang != $id_lang}
            {assign var="hidden" value='hidden-field'}
        {/if}
        {if $infometas[$infofield.id_infofields][$infofield.id_lang] == true}
            {assign var="infometa_value" value=$infometas[$infofield.id_infofields][$infofield.id_lang].meta_data}
        {else}
            {assign var="infometa_value" value=""}
        {/if}
        <div class="inf-meta-form-wrapper inf-meta-form-wrapper-{$langs[$infofield.id_lang]} {$hidden}">
            <input type="hidden" class="inf_input_id" value="{$infofield.id_infofields}">
            <input type="hidden" class="inf_input_type" value="{$infofield.field_type}">
            <h3>{$infofield.field_name}</h3>
            <div class="inf-meta-form-group form-group">
                {if $infofield.field_type == 1}
                    <div class="input-group">
                        <input type="text" id="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}"
                            name="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}"
                            class="inf-meta-field inf-meta-field-{$langs[$infofield.id_lang]} form-control"
                            value="{$infometa_value}">
                    </div>
                {elseif $infofield.field_type == 2}
                    <div class="input-group">
                        <textarea name="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}"
                            id="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}"
                            class="inf-meta-field inf-meta-field-{$langs[$infofield.id_lang]} form-control rte autoload_rte">{$infometa_value}</textarea>
                    </div>
                {elseif $infofield.field_type == 3}
                    <div class="input-group">
                        <textarea name="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}"
                            id="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}"
                            class="inf-meta-field inf-meta-field-{$langs[$infofield.id_lang]} form-control">{$infometa_value}</textarea>
                    </div>
                {elseif $infofield.field_type == 4}
                    <div class="input-group">
                        <span id="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}" class="ps-switch">
                            <input name="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}"
                                id="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}_0"
                                class="inf-meta-field inf-meta-field-{$langs[$infofield.id_lang]} form-control ps-switch"
                                name="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}" value="0"
                                type="radio" {if $infometa_value == 0} checked="" {/if}>
                            <label for="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}_0">No</label>
                            <input name="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}"
                                id="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}_1"
                                class="inf-meta-field inf-meta-field-{$langs[$infofield.id_lang]} form-control ps-switch"
                                name="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}" value="1"
                                type="radio" {if $infometa_value == 1} checked="" {/if}>
                            <label for="inf_metafield_{$infofield.id_infofields}_{$langs[$infofield.id_lang]}_1">Yes</label>
                            <span class="slide-button"></span>
                        </span>
                    </div>
                {/if}
            </div>
            <button id="saveInfoMeta" class="inf-meta-save-bt btn-primary btn" type="button">Save</button>
        </div>
    {/foreach}
</div>