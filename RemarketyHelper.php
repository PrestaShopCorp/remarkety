<?php
/**
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
 * @author    Interamind Ltd <support@remarkety.com>
 * @copyright 2015-2022 Interamind Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
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
 * @author    Interamind Ltd <support@remarkety.com>
 * @copyright 2015-2022 Interamind Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class RemarketyHelper
{

    public static function handleVoucherFromUrl($cart_controller)
    {
        if (CartRule::isFeatureActive()) {
            if (Tools::isSubmit('submitAddDiscount')) {
                $context = Context::getContext();
                if (!($code = trim(Tools::getValue('discount_name')))) {
                    $cart_controller->errors[] = Tools::displayError('You must enter a voucher code.');
                } elseif (!Validate::isCleanHtml($code)) {
                    $cart_controller->errors[] = Tools::displayError('The voucher code is invalid.');
                } else {
                    if (($cart_rule = new CartRule(CartRule::getIdByCode($code))) &&
                        Validate::isLoadedObject($cart_rule)
                    ) {
                        $context->cart->addcart_rule($cart_rule->id);

                        if (!$cart_rule->checkValidity($context, false, true)) {
                            $context->cart->addcart_rule($cart_rule->id);
                        }
                    } else {
                        $cart_controller->errors[] = Tools::displayError('This voucher does not exists.');
                    }
                }
                $context->smarty->assign(array(
                    'errors' => $cart_controller->errors,
                    'discount_name' => Tools::safeOutput($code)
                ));
            }

        }
    }
}
