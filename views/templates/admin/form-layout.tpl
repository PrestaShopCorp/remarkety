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
<div class="remarkety_body">
    <section class="section bg-grey">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div style="width:200px; margin-left: auto; margin-right:auto;"><a target="_blank"
                                                                                       href="http://www.remarkety.com"><img
                                    src="{$module_url|escape:'htmlall':'UTF-8'}views/img/remarkety_logo_307x101.png"
                                    style="width:200px;"></a></div>
                    <div class="text-center">
                        <h1>{l s='World\'s Best Email Marketing for PrestaShop' mod='remarkety'}</h1>
                        <h4 style="display: inline-block;padding-bottom: 15px;border-bottom: 2px solid #DDD;">{l s='Increase sales by sending automated personal emails based on shopping behavior and purchase history.' mod='remarkety'}</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--Subscribe -->
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="headline">
                        <h1 style="border-bottom: none;">{block name=title}{/block}</h1>
                    </div>
                    {block name=form_content}{/block}
                </div>
                <div class="col-lg-5 col-md-5 col-sm-6 col-md-offset-1">
                    <div class="headline">
                        <h1 style="border-bottom: none;">{l s='Features and Benefits' mod='remarkety'}</h1>
                    </div>
                    <ul class="green-arrow">
                        <li>{l s='Automates email campaigns and customer segmentation' mod='remarkety'}</li>
                        <li>{l s='Increase customer\'s Life Time Value' mod='remarkety'}</li>
                        <li>{l s='Makes personal product recommendations' mod='remarkety'}</li>
                        <li>{l s='Recover Abandoned Carts' mod='remarkety'}</li>
                        <li>{l s='Win-back Inactive customers' mod='remarkety'}</li>
                        <li>{l s='Delivers emails triggered by customer activity' mod='remarkety'}</li>
                        <li>{l s='ROI dashboard tracks open, click and purchase rate' mod='remarkety'}</li>
                        <li>{l s='Marketing insights based on millions of shoppers' mod='remarkety'}</li>
                    </ul>
                </div>

            </div>

        </div>
    </section>
    <!--End Subscribe -->

    <!-- More Features -->
    <section id="more-features" class="section bg-grey">
        <div class="container">
            <div class="row">
                <!-- More Feature Column -->
                <div class="col-lg-4  col-md-4 col-sm-4 feature">
                    <i class="fa a fa-magic"></i><!-- Features Icon -->
                    <h4>Analyze and Recommend</h4><!-- Features Title -->
                    <div class="bubble"><!-- Features Details -->
                        <p>{l s='Remarkety automatically analyzes your data, and recommends which email campaigns to send. We will optimize your ongoing email campaigns and give you ideas for new ones.' mod='remarkety'}</p>

                    </div>
                </div>
                <!-- End More Feature Column -->
                <!-- More Feature Column -->
                <div class="col-lg-4  col-md-4 col-sm-4 feature">
                    <i class="fa fa-send"></i><!-- Features Icon -->
                    <h4>{l s='Send Effective Emails' mod='remarkety'}</h4><!-- Features Title -->
                    <div class="bubble"><!-- Features Details -->
                        <p>{l s='Trust Remarkety to deliver your email. Once you setup your campaigns, we will continuously send the right message, to the right person, at the right time, automatically' mod='remarkety'}</p>

                    </div>
                </div>
                <!-- End More Feature Column -->
                <!-- More Feature Column -->
                <div class="col-lg-4  col-md-4 col-sm-4 feature">
                    <i class="fa fa-money"></i><!-- Features Icon -->
                    <h4>{l s='Increase Sales' mod='remarkety'}</h4><!-- Features Title -->
                    <div class="bubble"><!-- Features Details -->
                        <p>{l s='The bottom line - Remarkety will increase your sales. Our dashboard is simple and intuitive. You can easily find the important metrics including real ROI, opens, clicks and purchases.' mod='remarkety'}</p>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End More Features -->

    <footer id="footer_remarkety" class="section nopadding-bottom">
        <div class="container">
            <div class="row">
                <!--footer_remarkety About Description -->
                <div class="col-md-3 col-sm-6">

                    <h4>{l s='Quick Links' mod='remarkety'}</h4>
                    <ul class="quick-links">
                        <li><a target="_blank"
                               href="http://www.remarkety.com/?utm_source=plugin&utm_medium=link&utm_campaign=prestashop-plugin">www.remarkety.com</a>
                        </li>
                        <li><a target="_blank"
                               href="https://app.remarkety.com/?utm_source=plugin&utm_medium=link&utm_campaign=prestashop-plugin">{l s='Sign in' mod='remarkety'}</a>
                        </li>
                        <li><a target="_blank"
                               href="https://remarkety.zendesk.com">{l s='Support' mod='remarkety'}</a>
                        </li>
                        <li><a target="_blank"
                               href="http://www.remarkety.com/blog?utm_source=plugin&utm_medium=link&utm_campaign=prestashop-plugin">{l s='Blog' mod='remarkety'}</a>
                        </li>
                        <li><a target="_blank"
                               href="http://www.remarkety.com/terms-of-service">{l s='Terms and Conditions' mod='remarkety'}</a>
                        </li>

                    </ul>

                </div>
                <!-- End footer_remarkety About Description -->

                <!-- Start Contact Details  -->
                <div class="col-md-5 col-sm-6">
                    <div class="contact-info">
                        <h4>{l s='Where to find us' mod='remarkety'}</h4>
                        <ul class="contact-list">
                            <li><i class="fa fa-map-marker"></i>379 West Broadway NY, NY 10012, USA</li>
                            <li><i class="fa fa-phone"></i>+1 (800) 570-7564</li>
                            <li><i class="fa fa-envelope-o"></i><a href="mailto:contact@simplesphere.net.com">support@remarkety.com</a>
                            </li>
                        </ul>
                    </div>
                    <!-- End Contact Details  -->

                </div>
                <div class="col-md-4 bottom-contact">
                    <div class="social">
                        <h4>{l s='Follow us' mod='remarkety'}</h4>
                        <!-- Start  Social Links -->
                        <ul class="social">
                            <li class="facebook"><a target="_blank" href="https://www.facebook.com/Remarkety"> <i
                                            class="fa fa-facebook"></i> </a></li>
                            <li class="twitter"><a target="_blank" href="https://twitter.com/remarkety"> <i
                                            class="fa fa-twitter"></i> </a></li>
                            <li class="google-plus"><a target="_blank"
                                                       href="https://plus.google.com/+Remarkety_plus/posts"> <i
                                            class="fa fa-google-plus"></i> </a></li>
                            <li class="linkedin"><a target="_blank" href="https://www.linkedin.com/company/interamind">
                                    <i class="fa fa-linkedin"></i> </a></li>
                        </ul>
                        <!-- End Social Links  -->
                    </div>


                </div>
            </div>
        </div>
        <div class="container">
            <div class="row footer_remarkety-bottom">
                <div class="col-lg-6 col-sm-6">
                    <p>&copy; Remarkety Inc. All Right Reserved</p>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <p class="copyright">{l s='Made with' mod='remarkety'} <i
                                class="fa fa-heart"></i> {l s='by' mod='remarkety'} <a
                                href="http://www.remarkety.com">{l s='Remarkety' mod='remarkety'}</a></p>
                </div>
            </div>
        </div>
    </footer>
</div>
