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
{block name=title}
    {l s='Connect to your existing Remarkety account' mod='remarkety'}
{/block}

{block name=form_content}
    <form id="banner-form" method="post">';
        <div class="banner-optin vertical">
            <div class="form-group">
                <input id="remarkety_username" name="remarkety_username" type="text" class="form-control"
                       placeholder="{l s='Email' mod='remarkety'}"
                       value="{$remarkety_username|escape:'htmlall':'UTF-8'}">
            </div>
            <div class="form-group">
                <input id="remarkety_password" name="remarkety_password" type="password" class="form-control"
                       placeholder="{l s='Password' mod='remarkety'}">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default btn-submit" style="padding-left: 6px; padding-right: 6px;"
                        name="remarkety_submit_test_connection">{l s='Connect your store' mod='remarkety'}</button>
                <button type="submit" class="btn-link" style="background-color: unset; padding: 0 0 0 10px;
			vertical-align: bottom;"
                        name="remarkety_submit_no_account">{l s='I don\'t have an account' mod='remarkety'}</button>
            </div>
        </div>
    </form>
{/block}