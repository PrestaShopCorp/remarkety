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
class Remarkety extends Module
{

    const FORM_CREATE_ACCOUNT  = 1;
    const FORM_TEST_CONNECTION = 2;
    const FORM_PATCH_FILE      = 3;


    private $store_url = '';
    private $module_url = '';
    private $ver16or_higher = true;
    private $schema = '';

    public function __construct()
    {
        $this->name = 'remarkety';
        $this->tab = 'emailing';
        $this->version = '1.1.0';
        $this->author = 'Remarkety';
        $this->need_instance = 1;
        //$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->module_key = '32c0888d9bb9a87bb2d236f84d029db8';
        $this->schema = parse_url(_PS_BASE_URL_, PHP_URL_SCHEME).'://';

        parent::__construct();

        $this->displayName = $this->l('Remarkety - Email Marketing For PrestaShop');
        $this->description = $this->l('Targeted email marketing based on shopping history');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $account_creted = $this->getAccountCreatedConfiguration();
        if (!$account_creted) {
            $this->warning =
                $this->l('Please finish configuration to connect your store to a Remarkety account');
        }
        Configuration::updateValue('REMARKETY_KEY', $this->getRemarketyKey());

        $this->store_url = _PS_BASE_URL_.__PS_BASE_URI__;
        $this->module_url = Tools::getProtocol(Tools::usingSecureMode()).$_SERVER['HTTP_HOST'].$this->getPathUri();
        if (version_compare(_PS_VERSION_, '1.6') < 0) {
            $this->ver16or_higher = false;
        }

        require_once _PS_MODULE_DIR_.$this->name.'/AbstractProxy.php';
        require_once _PS_MODULE_DIR_.$this->name.'/SimpleRestProxy.php';
        require_once _PS_MODULE_DIR_.$this->name.'/RemarketyConfiguration.php';
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->setAccountCreatedConfiguration(0) ||
            !Configuration::updateValue('REMARKETY_ACCOUNT_USERNAME', Configuration::get('PS_SHOP_EMAIL'))
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        $remarkety_username = Configuration::get('REMARKETY_ACCOUNT_USERNAME');
        $context = Shop::getContext();
        if ((Shop::isFeatureActive() && $context == Shop::CONTEXT_ALL) || !Shop::isFeatureActive()) {
            if (!parent::uninstall()) {
                return false;
            }
        }

        if ((Shop::isFeatureActive() && $context == Shop::CONTEXT_ALL)) {
            //for each store delete key and call remarkety
            $keys = $this->getAllRemarketyKeys();
            $shop_keys = $this->getShopsKeys();
            foreach ($keys as $remarkety_key) {
                //key exist, delete it
                $account_id = $this->getKeyAccountId($remarkety_key);
                $ws_key = new WebserviceKey();
                $ws_key->key = $remarkety_key;
                $ws_key->id = $account_id;
                $ws_key->delete();
            }
            $shops = Shop::getShops();
            foreach ($shops as $shop) {
                $domain = $this->schema.$shop['domain'];
                $url = RemarketyConfiguration::REMARKETY_BASE_URL.
                    RemarketyConfiguration::REMARKETY_UNINSTALL_ENDPOINT;
                $params = $this->getBaseParams();
                $params['key'] = $shop_keys[$shop['id_shop']];
                $params['email'] = $remarkety_username;
                $params['domain'] = $domain;
                $message = '';
                $this->callRemarketyApp($url, $params, $message);
            }
            Configuration::deleteByName('REMARKETY_ACCOUNT_CREATED');
            Configuration::deleteByName('REMARKETY_ACCOUNT_USERNAME');
            Configuration::deleteByName('REMARKETY_KEY');
            Configuration::deleteByName('REMARKETY_DONT_SHOW_PATCH_FORM');
            return true;
        }

        //a single store
        $account_creted = $this->getAccountCreatedConfiguration();
        if (!$account_creted) {
            return true;
        }

        $domain = $this->schema.$this->context->shop->domain.$this->context->shop->physical_uri;
        //disconnect only current shop, do not delete the module itself
        $remarkety_key = Configuration::get('REMARKETY_KEY');
        if (!empty($remarkety_key)) {
            //key exist, delete it
            $account_id = $this->getKeyAccountId($remarkety_key);
            $ws_key = new WebserviceKey();
            $ws_key->key = $remarkety_key;
            $ws_key->id = $account_id;
            $ws_key->delete();

            $url = RemarketyConfiguration::REMARKETY_BASE_URL.RemarketyConfiguration::REMARKETY_UNINSTALL_ENDPOINT;
            $params = $this->getBaseParams();
            $params['key'] = $remarkety_key;
            $params['email'] = $remarkety_username;
            $params['domain'] = $domain;
            $message = '';
            $this->callRemarketyApp($url, $params, $message);
        }
        $this->setAccountCreatedConfiguration(0);
        if (!Shop::isFeatureActive()) {
            Configuration::deleteByName('REMARKETY_ACCOUNT_CREATED');
            Configuration::deleteByName('REMARKETY_ACCOUNT_USERNAME');
            Configuration::deleteByName('REMARKETY_KEY');
            Configuration::deleteByName('REMARKETY_DONT_SHOW_PATCH_FORM');
        }
        return true;
    }

    public function getContent()
    {
        $output = '';
        $params = $this->getBaseParams();
        //load bootstrap if version is lower than 1.5
        if (!$this->ver16or_higher) {
            if (isset ($this->context->controller)) {
                $this->context->controller->addCSS($this->_path.'views/css/bootstrap.min.css', 'all');
                $this->context->controller->addCSS($this->_path.'views/css/bootstrap-theme.min.css', 'all');
                $this->context->controller->addCSS($this->_path.'views/css/google-fonts.css', 'all');
                $this->context->controller->addCSS($this->_path.'views/css/font-awesome.min.css', 'all');
                $this->context->controller->addCSS($this->_path.'views/css/style.css', 'all');
            } else {
                echo '<link rel="stylesheet" href="'.$this->_path.'views/css/google-fonts.css" />';
                echo '<link rel="stylesheet" href="'.$this->_path.'views/css/font-awesome.min.css" />';
                echo '<link rel="stylesheet" href="'.$this->_path.'views/css/style.css"  />';
            }
        } else {
            $this->context->controller->addCSS($this->module_url.'views/css/google-fonts.css', 'all');
            $this->context->controller->addCSS($this->module_url.'views/css/font-awesome.min.css', 'all');
            $this->context->controller->addCSS($this->module_url.'views/css/style.css', 'all');
        }

        if (Tools::isSubmit('remarkety_submit_patch_files')) {
// 			$output = '';
            $dont_show = Tools::getValue('remarkety_dont_show', false) === 'on' ? true : false;
            $this->setDontShowPatchForm($dont_show);
            header('Refresh:0');
// 			$order_file_path = _PS_CLASS_DIR_.'/order/Order.php';
// 			if (is_writable($order_file_path))
// 			{
// 				$order_content = Tools::file_get_contents(_PS_CLASS_DIR_.'/order/Order.php');
// 				$order_content = str_replace(
// 					'public function getWsCurrentState($state)',
// 					'public function getWsCurrentState()',
// 					$order_content
// 				);
// 				$res = file_put_contents($order_file_path, $order_content);
// 				if ($res === false)
// 				{
// 					$output .= $this->displayError(
// 						$this->l('Cannot fix problem. Please follow these').
// 						' <a href="https://remarkety.zendesk.com/entries/79231695-PrestaShop-connection-problems"
//                          target="_blank">'.
// 						$this->l('instructions').'</a> '.$this->l('to fix it manually.')
// 					);
// 				}
// 				else
// 					header('Refresh:0');
// 			}
// 			else
// 			{
// 				$output .= $this->displayError(
// 					$this->l('Cannot fix problem. Please make sure that the file ').
// 					$order_file_path.' and its folder have proper write permission'
// 				);
// 			}
        } elseif ($this->showPatchForm() && file_exists(_PS_CLASS_DIR_.'/order/Order.php')) {
            $output = '';
            $order_content = Tools::file_get_contents(_PS_CLASS_DIR_.'/order/Order.php');
            if (strpos($order_content, 'public function getWsCurrentState($state)') !== false) {
                return $this->displayPatchView($output, array('patchOrder'));
            } else {
                $this->setDontShowPatchForm(true);
            }
        }

        $account_created = $this->getAccountCreatedConfiguration();
        if ($account_created) {
            return $this->displayMainView();
        }

        if (Tools::isSubmit('remarkety_submit_has_account')) {
            return $this->displayExistingAccountView();
        } elseif (Tools::isSubmit('remarkety_submit_no_account')) {
            return $this->displayCreateAccountView();
        } elseif (Tools::isSubmit('remarkety_submit_test_connection')) {
            $output = '';
            //validate request parameters
            $remarkety_username = (string)Tools::getValue('remarkety_username');
            $remarkety_password = (string)Tools::getValue('remarkety_password');
            if (!$remarkety_username
                || empty($remarkety_username)
                || !Validate::isEmail($remarkety_username)
            ) {
                $output .= $this->displayError($this->l('Remarkety username should be a valid email address'));
            } elseif (!$remarkety_password || empty($remarkety_password)) {
                $output .= $this->displayError($this->l('Please enter your password in both password fields,
                    and make sure they are identical'));
            } else {
                Configuration::updateValue('REMARKETY_ACCOUNT_USERNAME', $remarkety_username);

                $params['email'] = $remarkety_username;
                $params['password'] = $remarkety_password;
                $params['isNewUser'] = 'false';
                $params['acceptTerms'] = 'true';

                $message = $this->formPostProcess($params);
                if ($message === true) {
                    return $this->displayMainView();
                } else {
                    $output .= $message;
                }

            }
            return $this->displayExistingAccountView($output);
        } elseif (Tools::isSubmit('remarkety_submit_create')) {
            $remarkety_username = (string)Tools::getValue('remarkety_username');
            $remarkety_password = (string)Tools::getValue('remarkety_password');
            $remarkety_password2 = (string)Tools::getValue('remarkety_password2');
            $remarkety_first_name = (string)Tools::getValue('remarkety_firstname');
            $remarkety_last_name = (string)Tools::getValue('remarkety_lastname');
            $remarkety_phone = (string)Tools::getValue('remarkety_phone');
            $remarkety_terms = Tools::getValue('remarkety_terms', false) === 'on' ? true : false;

            if (!$remarkety_username
                || empty($remarkety_username)
                || !Validate::isEmail($remarkety_username)
            ) {
                $output .= $this->displayError($this->l('Remarkety username should be a valid email'));
            } elseif (!$remarkety_password
                || empty($remarkety_password)
                || !$remarkety_password2
                || empty($remarkety_password2)
                || $remarkety_password != $remarkety_password2
            ) {
                $output .= $this->displayError($this->l('Please enter your password in both password fields,
                    and make sure they are identical'));
            } elseif (!$remarkety_first_name
                || empty($remarkety_first_name)
                || !Validate::isGenericName($remarkety_first_name)
            ) {
                $output .= $this->displayError($this->l('First name contains invalid characters'));
            } elseif (!$remarkety_last_name
                || empty($remarkety_last_name)
                || !Validate::isGenericName($remarkety_last_name)
            ) {
                $output .= $this->displayError($this->l('Last name contains invalid characters'));
            } elseif (!$remarkety_phone
                || empty($remarkety_phone)
                || !Validate::isPhoneNumber($remarkety_phone)
            ) {
                $output .= $this->displayError($this->l('Please enter a valid phone number'));
            } elseif (!$remarkety_terms || empty($remarkety_terms)) {
                $output .= $this->displayError($this->l('You need to agree to terms of service in order to
                    create your Remarkety account'));
            } else {
                Configuration::updateValue('REMARKETY_ACCOUNT_USERNAME', $remarkety_username);
                $params['email'] = $remarkety_username;
                $params['password'] = $remarkety_password;
                $params['isNewUser'] = 'true';
                $params['acceptTerms'] = 'true';
                $params['firstName'] = $remarkety_first_name;
                $params['lastName'] = $remarkety_last_name;
                $params['phone'] = $remarkety_phone;

                $message = $this->formPostProcess($params);
                if ($message === true) {
                    return $this->displayMainView();
                } else {
                    $output .= $message;
                }
            }
        }

        $stores_connected = $this->getCountStoresConnected();
        if (Shop::isFeatureActive() && $stores_connected > 0) {
            return $this->displayExistingAccountView($output);
        } else {
            return $this->displayCreateAccountView($output);
        }

    }

    public function displayCreateAccountView($output = '')
    {
        $remarkety_username = (string)Tools::getValue('remarkety_username');
        $remarkety_firstname = (string)Tools::getValue('remarkety_firstname', '');
        $remarkety_lastname = (string)Tools::getValue('remarkety_lastname', '');
        $remarkety_phone = (string)Tools::getValue('remarkety_phone', '');
        $remarkety_terms = Tools::getValue('remarkety_terms', false) === 'on' ? true : false;

        $remarkety_username = !empty($remarkety_username) ?
            $remarkety_username : Configuration::get('REMARKETY_ACCOUNT_USERNAME');

        $smarty_vars = array(
            'remarkety_username' => $remarkety_username,
            'remarkety_firstname' => $remarkety_firstname,
            'remarkety_lastname' => $remarkety_lastname,
            'remarkety_phone' => $remarkety_phone,
            'remarkety_terms' => $remarkety_terms

        );
        return $this->displayTemplate($output, 'create-account', $smarty_vars);
    }

    public function displayExistingAccountView($output = '')
    {
        $smarty_vars = array(
            'remarkety_username' => Configuration::get('REMARKETY_ACCOUNT_USERNAME')
        );
        return $this->displayTemplate($output, 'existing-account', $smarty_vars);
    }

    public function displayPatchView($output, $problems)
    {
        $vars = array(
            'problems' => $problems
        );

        return $this->displayTemplate($output, 'patch', $vars);
    }

    protected function getRemarketyKey()
    {
        $shop_id = (int)$this->context->shop->id;
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT w.key
		FROM '._DB_PREFIX_.'webservice_account w
		INNER JOIN '._DB_PREFIX_.'webservice_account_shop ws ON w.id_webservice_account = ws.id_webservice_account
		AND ws.id_shop = '.(int)$shop_id.'
		WHERE w.description = \'Remarkety\'');
        return $res;
    }

    protected function getAllRemarketyKeys()
    {
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->query('
		SELECT w.key
		FROM '._DB_PREFIX_.'webservice_account w
		WHERE w.description = \'Remarkety\'');
        $keys = array();
        while ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($res)) {
            $keys[] = $row['key'];
        }
        return $keys;
    }

    protected function getShopsKeys()
    {
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->query('
		SELECT w.key, ws.id_shop
		FROM '._DB_PREFIX_.'webservice_account_shop ws
		INNER JOIN '._DB_PREFIX_.'webservice_account w ON w.id_webservice_account = ws.id_webservice_account
		WHERE w.description = \'Remarkety\'');
        $keys = array();
        while ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($res)) {
            $keys[$row['id_shop']] = $row['key'];
        }

        return $keys;
    }

    protected function getKeyAccountId($key)
    {
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT w.id_webservice_account
		FROM '._DB_PREFIX_.'webservice_account w
		WHERE w.key = \''.pSQL($key).'\'');

        return $res;
    }

    protected function getAllKeyAccountId($key)
    {
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->query('
		SELECT w.id_webservice_account
		FROM '._DB_PREFIX_.'webservice_account w
		WHERE w.key = \''.pSQL($key).'\'');
        $account_ids = array();
        while ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($res)) {
            $account_ids[] = $row['id_webservice_account'];
        }

        return $account_ids;
    }

    protected function createRemarketyKey()
    {
        $new_key = md5(uniqid());
        $ws_key = new WebserviceKey();
        $ws_key->key = $new_key;
        $ws_key->description = 'remarkety';
        $ws_key->add();
        $account_id = $this->getKeyAccountId($new_key);
        $permissions = Tools::jsonDecode('{"addresses":{"GET":"on"},"carriers":{"GET":"on"},"cart_rules":{"GET":"on"},
            "carts":{"GET":"on"},"categories":{"GET":"on"},"combinations":{"GET":"on"},"configurations":{"GET":"on"},
            "contacts":{"GET":"on"},"content_management_system":{"GET":"on"},"countries":{"GET":"on"},
            "currencies":{"GET":"on"},"customer_messages":{"GET":"on"},"customer_threads":{"GET":"on"},
            "customers":{"GET":"on"},"customizations":{"GET":"on"},"deliveries":{"GET":"on"},"employees":{"GET":"on"},
            "groups":{"GET":"on"},"guests":{"GET":"on"},"image_types":{"GET":"on"},"images":{"GET":"on"},
            "languages":{"GET":"on"},"manufacturers":{"GET":"on"},"order_carriers":{"GET":"on"},
            "order_details":{"GET":"on"},"order_discounts":{"GET":"on"},"order_histories":{"GET":"on"},
            "order_invoices":{"GET":"on"},"order_payments":{"GET":"on"},"order_slip":{"GET":"on"},
            "order_states":{"GET":"on"},"orders":{"GET":"on"},"price_ranges":{"GET":"on"},
            "product_customization_fields":{"GET":"on"},"product_feature_values":{"GET":"on"},
            "product_features":{"GET":"on"},"product_option_values":{"GET":"on"},"product_options":{"GET":"on"},
            "product_suppliers":{"GET":"on"},"products":{"GET":"on"},"search":{"GET":"on"},"shop_groups":{"GET":"on"},
            "shop_urls":{"GET":"on"},"shops":{"GET":"on"},"specific_price_rules":{"GET":"on"},
            "specific_prices":{"GET":"on"},"states":{"GET":"on"},"stock_availables":{"GET":"on"},
            "stock_movement_reasons":{"GET":"on"},"stock_movements":{"GET":"on"},"stocks":{"GET":"on"},
            "stores":{"GET":"on"},"suppliers":{"GET":"on"},"supply_order_details":{"GET":"on"},
            "supply_order_histories":{"GET":"on"},"supply_order_receipt_histories":{"GET":"on"},
            "supply_order_states":{"GET":"on"},"supply_orders":{"GET":"on"},"tags":{"GET":"on"},
            "tax_rule_groups":{"GET":"on"},"tax_rules":{"GET":"on"},"taxes":{"GET":"on"},
            "translated_configurations":{"GET":"on"},"warehouse_product_locations":{"GET":"on"},
            "warehouses":{"GET":"on"},"weight_ranges":{"GET":"on"},"zones":{"GET":"on"}}', true);
        WebserviceKey::setPermissionForAccount($account_id, (array)$permissions);
        Tools::generateHtaccess();
        Configuration::updateValue('PS_WEBSERVICE', 1);
        Configuration::updateValue('REMARKETY_KEY', $new_key);
        return true;
    }

    protected function callRemarketyApp($url, $params, &$message)
    {
        if (!function_exists('curl_init')) {
            $message = $this->l('Cannot connect to Remarkety. Your PHP should support cURL.');
            return false;
        }

        if (!isset($params['domain'])) {
            $params['domain'] = $this->store_url;
        }

        $host = gethostbyname(parse_url($params['domain'], PHP_URL_HOST).'.');
        if (stripos($params['domain'], 'localhost') !== false || $host == '127.0.0.1') {
            $message = $this->l('Cannot connect to Remarkety with localhost domain.');
            return false;
        }

        $params['platform'] = 'PRESTASHOP';
        $params['version'] = _PS_VERSION_;
        $remarket_proxy = new RemarketyProxySimpleRestProxy($url);
        $remarket_proxy->init();
        $remarket_proxy->setParams($params);
        return $remarket_proxy->execute();
    }

    protected function formPostProcess(&$params)
    {
        $remarkety_key = Configuration::get('REMARKETY_KEY');
        $message = '';
        $domain = $this->schema.$this->context->shop->domain.$this->context->shop->physical_uri;
        $shop_id = $this->context->shop->id;
        $params['domain'] = $domain;
        $params['shopId'] = $shop_id;
        if ($remarkety_key !== false) {
            //there is a key, test connection
            $params['key'] = $remarkety_key;
            $url = RemarketyConfiguration::REMARKETY_BASE_URL.RemarketyConfiguration::REMARKETY_INSTALL_ENDPOINT;
            $res = $this->callRemarketyApp($url, $params, $message);
            if ($res === false) {
                if (!empty($message)) {
                    return $this->displayError($message);
                } else {
                    return $this->displayError(
                        $this->l('A network error occurred while trying to connect to Remarkety.
                        Please contact our customer support: ')
                        .'<a href="mailto:support@remarkety.com" class="alert-link">support@remarkety.com</a>'
                    );
                }

            } elseif (is_object($res) && $res->status == 'ERROR') {
                return $this->displayError($this->translateRemarketyResponseMessage($res->code, $domain));
            } else {
                //Success -
                $this->setAccountCreatedConfiguration(1);
                return true;
            }
        } else {
            //Key not exists, create key
            $res = $this->createRemarketyKey();
            $remarkety_key = Configuration::get('REMARKETY_KEY');
            if ($res === false) {
                return $this->displayError($this->l('Technical error. Could not create webservice secured key on
                your PrestaShop admin'));
            } else {
                //	test connection
                $params['key'] = $remarkety_key;
                $url = RemarketyConfiguration::REMARKETY_BASE_URL.RemarketyConfiguration::REMARKETY_INSTALL_ENDPOINT;
                $res = $this->callRemarketyApp($url, $params, $message);
                if ($res === false) {
                    if (!empty($message)) {
                        return $this->displayError($message);
                    } else {
                        $error_msg =
                            $this->l('Connection to Remarkety failed, please contact customer support');
                        return $this->displayError($error_msg.': <a href="mailto:support@remarkety.com"
                        class="alert-link">support@remarkety.com</a>');
                    }
                } elseif (is_object($res) && $res->status == 'ERROR') {
                    return $this->displayError($this->translateRemarketyResponseMessage($res->code));
                } else {
                    //Success -
                    $this->setAccountCreatedConfiguration(1);
                    return true;
                }
            }
        }
    }

    protected function displayMainView($output = '')
    {
        $vars = array(
            'remarkety_url' => 'https://app.remarkety.com',
            'version' => $this->version,
            'shop_name' => $this->context->shop->name,
            'is_multistore' => Shop::isFeatureActive()
        );
        return $this->displayTemplate($output, 'main', $vars);
    }

    protected function displayTemplate($output, $template, $smarty_vars = array())
    {
        $base_vars = array(
            'module_url' => $this->module_url,
            'remarkety_logo_path' => $this->module_url.'views/img/remarkety_logo_600x200_transparent.gif',
        );
        $smarty = $this->context->smarty;
        $smarty->assign(
            array_merge($base_vars, $smarty_vars)
        );

        return $output.$this->display(__FILE__, "views/templates/admin/$template.tpl");
    }

    protected function translateRemarketyResponseMessage($response_code, $domain = "")
    {
        $message = '';
        switch ($response_code) {
            case 'FIELD_MISSING':
                $message = $this->l('A mandatory field is missing');
                break;
            case 'INVALID_FIELD_VALUE':
                $message = $this->l('A field contains an invalid value');
                break;
            case 'TERMS_NOT_ACCEPTED':
                $message = $this->l('You must accept the terms and conditions');
                break;
            case 'CONNECTION_FAILED':
                $message = $this->l('Remarkety could not connect back to your website
                to sync the data. Please make sure your website is publicly accessible from the Internet.
                We were trying to connect to: ')."<strong>$domain</strong>"
                ;
                break;
            case 'INTERNAL_ERROR':
                $message = $this->l('An internal error occured - please contact support');
                break;
            case 'NO_SUCH_USER':
                $message = $this->l('This user doesn\'t exist in Remarkety, or the password is invalid');
                break;
            case 'WRONG_PASSWORD':
                $message = $this->l('The password is incorrect');
                break;
            case 'INVALID_PASSWORD':
                $message = $this->l('This password is invalid - please choose a stronger one');
                break;
            case 'USER_ALREADY_EXISTS':
                $message = $this->l('This email is already registered');
                break;
            default:
                $message = $this->l('An unknown error was returned - please contact support')." ($response_code)";
        }

        return str_replace(
            '[COSTMER_SUPPORT_ADDRESS]',
            '<a href="mailto:support@remarkety.com" class="alert-link">support@remarkety.com</a>',
            $message
        );
    }

    private function getAccountCreatedConfiguration()
    {
        $shop_id = (int)$this->context->shop->id;
        $account_created = Configuration::getGlobalValue('REMARKETY_ACCOUNT_CREATED'); //this returns json
        $account_created = !empty($account_created) ? Tools::jsonDecode($account_created, true) : array();
        return isset($account_created[$shop_id]) ? $account_created[$shop_id] : 0;
    }

    private function setAccountCreatedConfiguration($value = 1)
    {
        $shop_id = (int)$this->context->shop->id;
        $account_created = Configuration::getGlobalValue('REMARKETY_ACCOUNT_CREATED'); //this returns json
        $account_created = !empty($account_created) ? Tools::jsonDecode($account_created, true) : array();
        $account_created[$shop_id] = $value;
        return Configuration::updateGlobalValue('REMARKETY_ACCOUNT_CREATED', Tools::jsonEncode($account_created));
    }

    private function getCountStoresConnected()
    {
        $account_created = Configuration::getGlobalValue('REMARKETY_ACCOUNT_CREATED'); //this returns json
        $account_created = !empty($account_created) ? Tools::jsonDecode($account_created, true) : array();
        $count_values = array_count_values($account_created);
        return isset($count_values['1']) ? $count_values['1'] : 0;
    }

    private function getBaseParams()
    {
        $params = array(
            'locale' => $this->getLocale(),
            'ip' => $this->getIp(),
            'timezone' => $this->getTimezone(),
            'pluginVersion' => RemarketyConfiguration::REMARKETY_PLUGIN_VERSION,
            'platformVersion' => $this->getPlatformVersion()
        );
        if (RemarketyConfiguration::REMARKETY_OEM) {
            $params['oem'] = RemarketyConfiguration::REMARKETY_OEM;
        }

        return $params;
    }

    private function getIp()
    {
        // This has security implications and need not be trusted - the client can set any IP they like
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = Tools::getRemoteAddr();
        }
        return $ip;
    }

    private function getLocale()
    {
        $language = (string)Configuration::get('PS_LOCALE_LANGUAGE');
        $country = (string)Configuration::get('PS_LOCALE_COUNTRY');
        return Tools::strtolower($language).'_'.Tools::strtoupper($country);
    }

    private function getTimezone()
    {
        $timezone = (string)Configuration::get('PS_TIMEZONE');
        return $timezone;
    }

    private function getPlatformVersion()
    {
        $timezone = (string)Configuration::get('PS_INSTALL_VERSION');
        return $timezone;
    }

    private function showPatchForm()
    {
        $dont_show = Configuration::getGlobalValue('REMARKETY_DONT_SHOW_PATCH_FORM');
        if (empty($dont_show)) {
            return true;
        }
        return false;
    }

    private function setDontShowPatchForm($value)
    {
        Configuration::updateGlobalValue('REMARKETY_DONT_SHOW_PATCH_FORM', $value);
    }
}
