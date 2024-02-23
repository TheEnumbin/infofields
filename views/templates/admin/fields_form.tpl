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
{$infofields|print_r }
<div>
    {foreach from=$infofields item=infofield}
        <div class="inf-meta-form-wrapper">
            <h3>{$infofield.field_name}</h3>
            <div class="inf-meta-form-group form-group">
            {if $infofield.field_type == 1}
                <div class="input-group locale-input-group js-locale-input-group d-flex" tabindex="1">
                    <div class="js-locale-input" style="flex-grow: 1;">
                        <div class="input-group js-text-with-length-counter">
                            <input type="text" id="inf_input_{$infofield.id_infofields}" name="inf_input_{$infofield.id_infofields}" class="inf-meta-input form-control">
                        </div>
                    </div>
                </div>
            {else}

            {/if}
            </div>
            <button id="saveInfoMeta" type="button">Save</button>
        </div>
    {/foreach}
</div>