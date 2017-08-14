<?php

add_shortcode('nuapay', 'np_form_creation');

function isValidMandateId($str) {
        if (preg_match("/[^A-Z a-z0-9\?\/\-\(\)\.\,\'\+\:]/", $str))
        {
                return false;
        }
        return true;
}

function isMandateIdTooLong($str) {
        $max_length = 35;
        $length = strlen($str);
        return $length > $max_length;
}

function np_form_creation($atts) {
	
	$a = shortcode_atts( array(
			'integration_type' => null, //'REDIRECT'/'OVERLAYE'
			'api_key' => null,//apiKey
			'scheme_type' => null,//merchantSchemeType
			'payment_type' => null,//paymentType
			'scheme_id' => null,//merchantSchemeId
			'creditor_iban' => null,//merchantIBAN
			//mandateUMR				- optional (generated by system)
			//debtorContractReference	- optional
	), $atts );

	$missing_args = check($a);
	
	//get the mandate id as a query param
    $queryParamMandateId = null;
    if ( isset ($_REQUEST['id'])) {
        $queryParamMandateId = $_REQUEST['id'];
        //url decode the parameter
        $queryParamMandateId = urldecode($queryParamMandateId);
        if(!isValidMandateId($queryParamMandateId)){
            NPUtils::render('error', array(
                'message' => NPUtils::i18('Invalid mandate id characters found.')
            ));
        }
        if(isMandateIdTooLong($queryParamMandateId)){
            NPUtils::render('error', array(
                'message' => NPUtils::i18('Invalid mandate id, too long.')
            ));
        }
    }
	
	if (isset($missing_args) && ($missing_args === true)) {
		NPUtils::render('error', array(
				'message' => NPUtils::i18('NUAPAY Plugin Not Configured Correctly contact support.')
		));
	} else if (in_array($a['integration_type'], array('REDIRECT', 'OVERLAY')) === false) {
		NPUtils::render('error', array(
				'message' => NPUtils::i18('NUAPAY Plugin - Not supported "%s" integration. Allowed only: "REDIRECT" or "OVERLAY".', array($a['integration_type']))
		));
	} else {
		
		$options = get_option('np_form_options');
		$generateTokerUrl = $options[NPSettings::API_URL];
		$emandateWebUrl = $options[NPSettings::EMANDATE_URL];
		
		$integration_type = $a['integration_type'];
		$api_key = $a['api_key'];
		$scheme_type = $a['scheme_type'];
		$payment_type = $a['payment_type'];
		$scheme_id = $a['scheme_id'];
		$creditor_iban = $a['creditor_iban'];
		
		$isOverlay = strcmp($integration_type, 'OVERLAY') == 0;
		
		$tokenRequest = array(
			'merchantDetails' => array(
				'creditorSchemeId' => $scheme_id,
				'schemeType' => $scheme_type,
				'mandateType' => $payment_type,
				'iban' => $creditor_iban,
				'mandateId' => $queryParamMandateId,
			),
		);
		
		$tokenResponse = wp_remote_post($generateTokerUrl, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($api_key . ':')
			),
			'body' => json_encode($tokenRequest),
			'cookies' => array()
		));
		
		$tokenResponseCode = wp_remote_retrieve_response_code($tokenResponse);
		
		$response = json_decode(wp_remote_retrieve_body($tokenResponse));
		
		if ($tokenResponseCode == 201 && !is_null($response->data && !is_null($response->data->token))) {
			
			$token = $response->data->token;
			$baseUrl = trim($emandateWebUrl, '/');
			
			if ($isOverlay) {

				$overlayUrl = $baseUrl . '/show?token=' . $token;
				
				wp_enqueue_script( 'np-js-plain-overlay', NUAPAY_PLUGIN_URL . 'overlay/plain-overlay.js' );
				wp_enqueue_style( 'np-css-plain-overlay', NUAPAY_PLUGIN_URL . 'overlay/plain-overlay.css' );
				wp_localize_script( 'np-js-plain-overlay', 'NP_Script_Params', array(
						'url' => $overlayUrl,
						'text' =>  array( 'subscribe' => NPUtils::i18('Sign'), ),
				) );
				
				$return_string = '<div class="np-overlay-mode-container">'.
				'<div id="np-overlay-button-container"></div>'.
				'<div id="np-overlay-modal">'.
				'<div class="np-modal-scrollable">'.
				'<div class="np-modal">'.
				'<div class="np-modal-body">'.
				'<div class="np-modal-body-content" id="np-overlay-modal-content"></div>'.
				'<div class="np-modal-bt-row">'.
				'<button type="button" id="np-overlay-modal-close">' . NPUtils::i18('Close') . '</button>'.
				'</div>'.
				'</div>'.
				'<div class="np-modal-footer"></div>'.
				'</div>'.
				'</div>'.
				'<div class="np-modal-backdrop"></div>'.
				'</div>'.
				'</div>';
				
				return $return_string;
				
			} else {
				
				$redirectUrl = $baseUrl . '/show';
				
				$return_string = '<div class="np-redirect-mode-container">'
						. '<form method="GET" action="'. $redirectUrl .'">'
						. '<input type="hidden" id="token" name="token" value="' . $token .'" />'
						. '<button type="submit" class="btn btn-primary">' . NPUtils::i18('Sign') . '</button>'
						. '</form>'
						. '</div>';
				
				return $return_string;
			}
			
		} else {
			
			$errorMessage = '';
			
			if (is_object($response) && !is_null($response->returnDescription)) {
				$errorMessage = $response->returnDescription . ' (' . $response->returnCode . ')';
			} else {
				if (is_wp_error($tokenResponse)) {
					$errorMessage = $tokenResponse->get_error_message();
				} else {
					$errorMessage = wp_remote_retrieve_response_message($tokenResponse);
				}
			}
			
			NPUtils::render('error', array(
					'message' => NPUtils::i18('Authentication error: %s', array($errorMessage))
			));
		}
		
	}
}