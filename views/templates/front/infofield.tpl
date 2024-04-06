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
<div class="infofield-wrapper">
    {foreach from=$infofields item=infofield}
        {if isset($infofields_metas[$infofield.id_infofields])}
            {assign var="infometa" value=$infofields_metas[$infofield.id_infofields]}
            {if $infometa[$lang_id] != false}
                <div class="infofield-meta-item">
                    {if $infofield.with_field_name != false}
                        {$infofield.field_name}
                    {/if}
                    {$infometa[$lang_id].meta_data nofilter}
                </div>
            {/if}
        {/if}

    {/foreach}
</div>