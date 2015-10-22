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
    {l s='Create your' mod='remarkety'}
    <strong>{l s='free' mod='remarkety'}</strong>
    {l s='account' mod='remarkety'}
{/block}
{block name=form_content}
    <form id="banner-form" method="post">
        <div class="banner-optin vertical">
            <div class="form-group">
                <input id="remarkety_firstname" name="remarkety_firstname" type="text" class="form-control"
                       placeholder="{l s='First Name' mod='remarkety'}"
                       value="{$remarkety_firstname|escape:'htmlall':'UTF-8'}">
            </div>
            <div class="form-group">
                <input id="remarkety_lastname" name="remarkety_lastname" type="text" class="form-control"
                       placeholder="{l s='Last Name' mod='remarkety'}"
                       value="{$remarkety_lastname|escape:'htmlall':'UTF-8'}">
            </div>
            <div class="form-group">
                <input name="remarkety_username" id="remarkety_username" name="remarkety_username" type="text"
                       class="form-control" placeholder="{l s='Email (username)' mod='remarkety'}"
                       value="{$remarkety_username|escape:'htmlall':'UTF-8'}">
            </div>
            <div class="form-group">
                <input id="remarkety_password" name="remarkety_password" type="password" class="form-control"
                       placeholder="{l s='Password' mod='remarkety'}">
            </div>
            <div class="form-group">
                <input id="remarkety_password2" name="remarkety_password2" type="password" class="form-control"
                       placeholder="{l s='Re-enter Password' mod='remarkety'}">
            </div>
            <div class="form-group">
                <input name="remarkety_phone" id="remarkety_phone" name="remarkety_phone" type="text"
                       class="form-control" placeholder="{l s='Phone number' mod='remarkety'}"
                       value="{$remarkety_phone|escape:'htmlall':'UTF-8'}">
            </div>
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input id="remarkety_terms" name="remarkety_terms" type="checkbox"
                               {if isset($remarkety_terms) && remarkety_terms == true}checked="checked"{/if}/>
                        <span style="color: white;">{l s='I agree to ' mod='remarkety'}</span>
                        <a target="_blank" href="http://www.remarkety.com/terms-of-service">
                            {l s='Terms of service' mod='remarkety'}
                        </a>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default btn-submit" name="remarkety_submit_create">
                    {l s='Start Your Account' mod='remarkety'}</button>
                <button type="submit" class="btn-link"
                        style="color:white; background-color: unset; padding: 0 0 0 10px;"
                        name="remarkety_submit_has_account">{l s='I already have an account' mod='remarkety'}</button>
            </div>
        </div>
    </form>
{/block}