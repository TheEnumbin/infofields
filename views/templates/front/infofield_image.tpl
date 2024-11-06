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
    <div class="image-wrapper">
        {assign var="img_size" value=""}
        {if isset($infofield.img_height) && isset($infofield.img_width)}
            {assign var="img_size" value="_custom_default"}
        {/if}
        {assign var="img_data" value=$infometa[$lang_id].meta_data|json_decode:true}
        <img src="{$img_dir}{$img_data.file}{$img_size}.{$img_data.ext}" alt="Uploaded Image" />
    </div>
</div>