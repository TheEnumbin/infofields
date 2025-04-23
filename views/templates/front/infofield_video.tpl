{*
* 2007-2025 PrestaShop
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
*  @copyright 2007-2025 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="video-container">
    {assign var="vid_data" value=$infometa}
    {assign var="vid_urls" value=$vid_data|trim}
    {assign var="vid_items" value=","|explode:$vid_urls}
    {foreach from=$vid_items item=vid_item}
        {assign var="field_settings" value=$infofield.settings|json_decode:true}
        {if isset($infofield.img_height) && isset($infofield.img_width)}
            {assign var="vid_height" value=$infofield.img_height}
            {assign var="vid_width" value=$infofield.img_width}
        {else}
            {assign var="vid_height" value="315"}
            {assign var="vid_width" value="560"}
        {/if}

        {if $field_settings["show_meta_in"] == 'popup'}
            <div class="inf-youtube-thumbnail" data-video-id="{$vid_item|replace:'https://www.youtube.com/watch?v=':''}">
                <img src="https://img.youtube.com/vi/{$vid_item|replace:'https://www.youtube.com/watch?v=':''}/hqdefault.jpg"
                    alt="Video cover">
                <span class="inf-play-btn"></span>
            </div>
        {else}
            <iframe width="{$vid_width}" height="{$vid_height}"
                src="https://www.youtube.com/embed/{$vid_item|replace:'https://www.youtube.com/watch?v=':''}"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        {/if}
    {/foreach}
</div>