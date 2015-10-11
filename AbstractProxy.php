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
abstract class RemarketyProxyAbstractProxy
{

    /**
     * The store ID
     */
    protected $store_id = null;
    protected $api_key = null;
    protected $base_url = null;

    /**
     * The server URL for the requst
     */
    protected $url = null;

    protected $client = null;

    protected $is_post = false;

    public function __construct($base_url)
    {
        $this->base_url = $base_url;
    }

    /**
     *
     * Initialize the proxy. Get the URL of the store's platform
     * from, the database according to the store ID.
     */
    public function init()
    {
        $this->initUrl($this->base_url);

        return true;
    }

    public function initUrl($base_url)
    {
        $this->url = $base_url;
    }

    /**
     *
     * @param array $action (action name, action value)
     */
    abstract public function setAction($action);

    /**
     *
     * @param array $action (param name, param value)
     */
    abstract public function setParams($params);

    /**
     *
     * @param mixed $client
     */
    protected function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * @return mixed
     */
    protected function getClient()
    {
        //$this->client is an object and returend by reference by default
        return $this->client;
    }

    public function setPost($is_post)
    {
        $this->is_post = $is_post;
    }

    public function isPost()
    {
        return $this->is_post;
    }
}
