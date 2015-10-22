{*
 * 2015-2022 Interamind Ltd
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
 * Do not edit or add to this file if you wish to upgrade Remarkety module to newer
 * versions in the future. If you wish to customize Remarekty module for your
 * needs please contact Remarkety support
 *
 *  @author Interamind Ltd <support@remarkety.com>
 *  @copyright  2015-2022 Interamind Ltd
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *}
{extends file="./form-layout.tpl"}

{block name=form_content}
    <form id="banner-form" method="post">
        <div class="banner-optin vertical">
            <div style="color: white">
                <p style="font-weight: bold">
                    {l s='We have detected some PrestaShop issues that might prevent Remarkety
        from connecting to your store:' mod='remarkety'}
                </p>
                <ul style="margin-bottom: 15px;">
                    {foreach from=$problems item=problem}
                        {if $problem='patchOrder'}
                            <div class="form-group">


                                <li> <span style="color: white;">{l s='A known bug in PrestaShop Orders web service.' mod='remarkety'}
                                        <a href="https://remarkety.zendesk.com/entries/79231695-PrestaShop-connection-problems"
                                           target="_blank"
                                           style="margin-left: 10px;">{l s='Read more...' mod='remarkety'}</a>
                                </li>

                            </div>
                        {/if}
                    {/foreach}
                </ul>
            </div>
            <div class="checkbox">
                <label>
                    <input id="remarkety_dont_show" name="remarkety_dont_show" type="checkbox"/>
                    <span style="color: white;">{l s='Do not show again' mod='remarkety'}</span>
                </label>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default btn-submit" style="padding-left: 6px; padding-right: 6px;"
                        name="remarkety_submit_patch_files">{l s='Fix Problems' mod='remarkety'}</button>
            </div>
        </div>
    </form>
{/block}