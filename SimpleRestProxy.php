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
class RemarketyProxySimpleRestProxy extends RemarketyProxyAbstractProxy
{

    public function init()
    {
        $b_res = parent::init();
        if ($b_res === true) {
            try {
                $client = array();
                $this->setClient($client);
            } catch (Exception $e) {
                $b_res = false;
            }
        }
        return $b_res;
    }

    public function setAction($action)
    {
        $client = $this->getClient();
        foreach ($action as $key => $val) {
            $client['action'][$key] = $val;
        }
        //re-set is needed since in this case $this->client is array which returns
        //from $this->getClient() by value.
        $this->setClient($client);
        return $this;
    }

    public function setParams($params)
    {
        $client = $this->getClient();
        foreach ($params as $key => $val) {
            $client['params'][$key] = $val;
        }
        //re-set is needed since in this case $this->client is array which returns
        //from $this->getClient() by value.
        $this->setClient($client);
        return $this;
    }

    public function execute($request_type = 'json')
    {
        $client = $this->getClient();
        $url = $this->getUrl();
        $req_params = '';
        $rec_action = '';
        if (isset($client['action'])) {
            foreach ($client['action'] as $key => $val) {
                $rec_action .= '/'.$key.'/'.urlencode($val);
            }
        }
        if (isset($client['params'])) {
            foreach ($client['params'] as $key => $val) {
                if ($val instanceof \DateTime) {
                    $val = $val->format('Y-m-d H:i:s');
                }
                $req_params .= '&'.$key.'='.urlencode($val);
            }
            if (!empty($req_params)) {
                $req_params = preg_replace('/^&/', '?', $req_params);
            }
        }

        $complete_url = $url.$rec_action.$req_params;
        $ch = curl_init();
// 		$headers = array(
// 				"Accept: */*",
// 				"Accept-Encoding: gzip, deflate, sdch",
// 				"Accept-Language: en-US,en;q=0.8,he;q=0.6",
// 				"Connection: keep-alive",
// 				"Cache-Control: no-cache",
// 		);
// 		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $complete_url);
        curl_setopt(
            $ch,
            CURLOPT_USERAGENT,
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36'
        );
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CAINFO, _PS_MODULE_DIR_.'remarkety/cacert.pem');

        if ($this->isPost()) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req_params);
        }

        $result = curl_exec($ch);
        $response_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Clean the result
        if ($request_type == 'json') {
            // Remove any preceeding and trailing characters
            $orig = $result;
            $result = preg_replace('/^[^\{]*/', '', $result);
            $result = preg_replace('/[^}]*$/', '', $result);
            // Make sure we got JSON back, otherwise we have a problem
            $json = Tools::jsonDecode($result);
            if ($json === null) {
                return $this->handleSyncError($complete_url, $response_code, $orig, $ch, 'Got bad or empty JSON');
            }
// 			if ($orig != $result)
// 			{
// 				//log?
// 			}
        }
        curl_close($ch);
        return $json;
    }

    private function handleSyncError($complete_url, $response_code, $result, $ch, $msg)
    {
        $curl_error = curl_error($ch);
        $curl_error .= '';
        //log?
        $log = $complete_url.', '.$response_code.', '.$result.', '.$msg;
        $log .= '';
        curl_close($ch);
        return false;
    }
}
