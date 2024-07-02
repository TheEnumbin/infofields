{*
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
*}
<div class="infofield-wrapper">
    {foreach from=$infofields item=infofield}
        {assign var="now" value=$smarty.now}
        {if $infofield.start_date == "0000-00-00 00:00:00" || $infofield.start_date == ""}
            {assign var="start" value=0}
        {else}
            {assign var="start" value=strtotime($infofield.start_date)}
        {/if}
        {if $infofield.end_date == "0000-00-00 00:00:00" || $infofield.end_date == ""}
            {assign var="end" value=strtotime('+20 day')}
        {else}
            {assign var="end" value=strtotime($infofield.end_date)}
        {/if}
        {if $start <= $now && $now <= $end}
            {if isset($infofields_metas[$infofield.id_infofields])}
                {assign var="infometa" value=$infofields_metas[$infofield.id_infofields]}
                {if $infofield.parent_item == 1}
                    {assign var="parent_item" value="category"}
                {elseif $infofield.parent_item == 2}
                    {assign var="parent_item" value="product"}
                {elseif $infofield.parent_item == 3}
                    {assign var="parent_item" value="cms"}
                {elseif $infofield.parent_item == 4}
                    {assign var="parent_item" value="customer"}
                {/if}
                {if $infometa[$lang_id] != false}
                    <div class="infofield-meta-item infofield-{$parent_item}-meta">
                        {if $infofield.with_field_name != false}
                            <h3 class="infofield-meta-label">{$infofield.field_name}</h3>
                        {/if}
                        {if $infofield.field_type == 7 || $infofield.field_type == 4}
                            {if $infometa[$lang_id].meta_data == 1}
                                <span class="inf-done-sign">&#10004;</span>
                            {else}
                                <span class="inf-not-done-sign">&#10006;</span>
                            {/if}
                        {elseif $infofield.field_type == 8 }
                            {assign var="available_values" value=","|explode:$infofield.available_values}
                            {foreach from=$available_values item=available_value}
                                {assign var="key_value" value=":"|explode:$available_value}
                                {if isset($key_value[1])}
                                    {assign var="label" value=$key_value[1]}
                                    {assign var="key" value=$key_value[0]}
                                {else}
                                    {assign var="label" value=$key_value[0]}
                                    {assign var="key" value=$key_value[0]}
                                {/if}
                                {if $key == $infometa[$lang_id].meta_data }
                                    {$label}
                                {/if}
                            {/foreach}
                        {elseif $infofield.field_type == 6 }
                            {assign var="date_value" value=$infometa[$lang_id].meta_data|json_decode:true}
                            {$date_value.year}-{$date_value.month}-{$date_value.day}
                        {else}
                            {$infometa[$lang_id].meta_data nofilter}
                        {/if}
                    </div>
                {elseif $infofield.global_meta_data != ""}
                    <div class="infofield-meta-item infofield-{$parent_item}-meta">
                        {if $infofield.with_field_name != false}
                            <h3 class="infofield-meta-label">{$infofield.field_name}</h3>
                        {/if}
                        {if $infofield.field_type == 7 || $infofield.field_type == 4}
                            {if $infofield.global_meta_data|strip_tags:true == 1}
                                <span class="inf-done-sign">&#10004;</span>
                            {else}
                                <span class="inf-not-done-sign">&#10006;</span>
                            {/if}
                        {else}
                            {$infofield.global_meta_data nofilter}
                        {/if}
                    </div>
                {/if}
            {/if}
        {/if}
    {/foreach}
</div>