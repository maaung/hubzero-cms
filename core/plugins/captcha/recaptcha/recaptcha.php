<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

/**
 * Recaptcha Plugin.
 *
 * Based on the official recaptcha library( https://developers.google.com/recaptcha/docs/php )
 */
class plgCaptchaRecaptcha extends \Hubzero\Plugin\Plugin
{
	/**
	 * Path to JS library needed for ReCAPTCHA to display
	 *
	 * [!] Must be served over HTTPS
	 * 
	 * @var  string
	 */
	private static $_jsUrl = 'https://www.google.com/recaptcha/api.js';

	/**
	 * Path to JS fallback library needed for ReCAPTCHA to display when JS is disabled
	 *
	 * [!] Must be served over HTTPS
	 * 
	 * @var  string
	 */
	private static $_jsFallbackUrl = 'https://www.google.com/recaptcha/api/fallback?k=';

	/**
	 * ReCAPTCHA verification url
	 * 
	 * @var  string
	 */
	private static $_verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?';

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Initialise the captcha
	 *
	 * @param   string   $id  The id of the field.
	 * @return  boolean  True on success, false otherwise
	 * @since   2.5
	 */
	public function onInit($id = 'dynamic_recaptcha_1')
	{
		if (!$this->params->get('public') || !$this->params->get('private'))
		{
			throw new Exception(Lang::txt('PLG_CAPTCHA_RECAPTCHA_ERROR_NO_PUBLIC_KEY'));
		}

		return true;
	}

	/**
	 * Gets the challenge HTML
	 *
	 * @param   string  $name   The name of the field. Not Used.
	 * @param   string  $id     The id of the field.
	 * @param   string  $class  The class of the field. This should be passed as 'class="required"'.
	 * @return  string
	 */
	public function onDisplay($name = null, $id = 'dynamic_recaptcha_1', $class = '')
	{
		try
		{
			$this->onInit($id);
		}
		catch (Exception $e)
		{
			return '<p class="error">' . Lang::txt('PLG_CAPTCHA_RECAPTCHA_API_NEEDED') . '</p>';
		}

		Document::addStyleDeclaration('
			noscript .g-recaptcha-ns {
				width: 302px;
				height: 352px;
			}
			noscript .g-recaptcha-inner {
				width: 302px;
				height: 352px;
				position: relative;
			}
			noscript .g-recaptcha-challenge {
				width: 302px;
				height: 352px;
				position: absolute;
			}
			noscript .g-recaptcha-challenge iframe {
				width: 302px;
				height:352px;
				border-style: none;
			}
			noscript .g-recaptcha-response-wrap {
				width: 250px;
				height: 80px;
				position: absolute;
				border-style: none;
				bottom: 21px;
				left: 25px;
				margin: 0px;
				padding: 0px;
				right: 25px;
			}
			noscript .g-recaptcha-response {
				width: 250px;
				height: 80px;
				border: 1px solid #c1c1c1;
				margin: 0px;
				padding: 0px;
				resize: none;
			}
		');

		// recaptcha html structure
		// this has support for users with js off
		$html  = '<div class="form-group">';
		$html .= '<label class="">&nbsp;</label><div class="field-wrap">';
		$html .= '<div class="g-recaptcha" id="' . $id . '" data-type="' . $this->params->get('type', 'image') . '" data-theme="' . $this->params->get('theme', 'light') . '" data-sitekey="' . $this->params->get('public') . '"></div>
					<noscript>
					  <div class="g-recaptcha-ns">
					    <div class="g-recaptcha-inner">
					      <div class="g-recaptcha-challenge">
					        <iframe src="' . static::$_jsFallbackUrl . $this->params->get('public') . '" frameborder="0" scrolling="no" title="' . Lang::txt('PLG_CAPTCHA_RECAPTCHA_TITLE') . '">
					        </iframe>
					      </div>
					      <div class="g-recaptcha-response-wrap">
					        <textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" value="">
					        </textarea>
					      </div>
					    </div>
					  </div>
					</noscript>
					<script type="text/javascript" src="' . static::$_jsUrl . '?hl=' . $this->params->get('language', 'en') . '" async defer></script>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Calls an HTTP POST function to verify if the user's guess was correct
	 *
	 * @param   string   $code  Answer provided by user. Not needed for the Recaptcha implementation
	 * @return  boolean  True if valid CAPTCHA response
	 */
	public function onCheckAnswer($code = null)
	{
		// get request params
		$response = Request::getString('g-recaptcha-response', null);
		$remoteIp = Request::ip();

		// Discard empty solution submissions
		if ($response == null || strlen($response) == 0)
		{
			$this->setError('missing-input');
			return false;
		}

		// perform a get request to the verify server with the needed data
		$verificationResponse = $this->_submitHttpGet(static::$_verifyUrl, array(
			'secret'   => $this->params->get('private'),
			'remoteip' => $remoteIp,
			'response' => $response
		));

		// json decode response
		$verificationResponse = json_decode($verificationResponse);

		// something went wrong
		if ($verificationResponse->success !== true)
		{
			if (isset($verificationResponse->{'error-codes'}))
			{
				$this->setError($verificationResponse->{'error-codes'});
			}
			return false;
		}

		// success
		return true;
	}

	/**
	 * Submits an HTTP GET to a reCAPTCHA server.
	 *
	 * @param   string  $url   url path to recaptcha server.
	 * @param   array   $data  array of parameters to be sent.
	 * @return  array   response
	 */
	private function _submitHttpGet($url, $data)
	{
		return file_get_contents($url . $this->_encodeQS($data));
	}

	/**
	 * Encodes the given data into a query string format
	 *
	 * @param   array   $data  Array of string elements to be encoded
	 * @return  string  Encoded request
	 */
	private function _encodeQs($data)
	{
		$req = '';
		foreach ($data as $key => $value)
		{
			$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
		}

		// Cut the last '&'
		$req = substr($req, 0, strlen($req)-1);
		return $req;
	}
}
