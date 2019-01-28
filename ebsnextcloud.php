<?php


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related abilities and
 * settings.
 *
 * @see https://developers.whmcs.com/provisioning-modules/meta-data-params/
 *
 * @return array
 */
function ebsnextcloud_MetaData()
{
    return array(
        'DisplayName' => 'Nextcloud',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => true, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '80', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '443', // Default SSL Connection Port
        'ServiceSingleSignOnLabel' => 'Login to Nextcloud as User',
        'AdminSingleSignOnLabel' => 'Login to Nextcloud as Admin',
    );
}


function ebsnextcloud_ConfigOptions()
{
    return array(
        // a text field type allows for single line text input
        'Quota' => array(
            'Type' => 'text',
            'Size' => '25',
            'Default' => '1GB',
            'Description' => 'Enter quota for product (1GB, 5GB, 10GB ....)',
        ),
    );
}

function ebsnextcloud_CreateAccount(array $params)
{
    try {
     
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://'.$params['serverusername'].':'.$params['serverpassword'].'@'.$params['serverhostname'].'/ocs/v1.php/cloud/users',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                userid => $params['username'],
                password =>  $params['password']
            ),
            CURLOPT_HTTPHEADER => array(
                "OCS-APIRequest: true"
            )
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        
        //Update Nextcloud account with user email
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => 'https://'.$params['serverusername'].':'.$params['serverpassword'].'@'.$params['serverhostname'].'/ocs/v1.php/cloud/users/'.$params['username'],
            CURLOPT_POSTFIELDS => http_build_query(array(
                key     =>  'email',
                value   =>  $params['clientsdetails']['email']
            )),
            CURLOPT_HTTPHEADER => array(
                "OCS-APIRequest: true"
            ),
            CURLOPT_CONNECTTIMEOUT => "10"
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
        //Update Nextcloud account display name with user firstname
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => 'https://'.$params['serverusername'].':'.$params['serverpassword'].'@'.$params['serverhostname'].'/ocs/v1.php/cloud/users/'.$params['username'],
            CURLOPT_POSTFIELDS => http_build_query(array(
                key     =>  'displayname',
                value   =>  $params['clientsdetails']['firstname']. " ".$params['clientsdetails']['lastname']
            )),
            CURLOPT_HTTPHEADER => array(
                "OCS-APIRequest: true"
            ),
            CURLOPT_CONNECTTIMEOUT => "10"
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
        //Set Nextcloud quota
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => 'https://'.$params['serverusername'].':'.$params['serverpassword'].'@'.$params['serverhostname'].'/ocs/v1.php/cloud/users/'.$params['username'],
            CURLOPT_POSTFIELDS => http_build_query(array(
                key     =>  'quota',
                value   =>  $params['configoption1']
            )),
            CURLOPT_HTTPHEADER => array(
                "OCS-APIRequest: true"
            ),
            CURLOPT_CONNECTTIMEOUT => "10"
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ebsnextcloud_SuspendAccount(array $params)
{
    try {
        //Disable Nextcloud account
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => 'https://'.$params['serverusername'].':'.$params['serverpassword'].'@'.$params['serverhostname'].'/ocs/v1.php/cloud/users/'.$params['username'].'/disable',
            CURLOPT_HTTPHEADER => array(
                "OCS-APIRequest: true"
            ),
            CURLOPT_CONNECTTIMEOUT => "10"
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ebsnextcloud_UnsuspendAccount(array $params)
{
    try {
        //Disable Nextcloud account
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => 'https://'.$params['serverusername'].':'.$params['serverpassword'].'@'.$params['serverhostname'].'/ocs/v1.php/cloud/users/'.$params['username'].'/enable',
            CURLOPT_HTTPHEADER => array(
                "OCS-APIRequest: true"
            ),
            CURLOPT_CONNECTTIMEOUT => "10"
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ebsnextcloud_TerminateAccount(array $params)
{
    try {
        
        //Disable Nextcloud account
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_URL => 'https://'.$params['serverusername'].':'.$params['serverpassword'].'@'.$params['serverhostname'].'/ocs/v1.php/cloud/users/'.$params['username'],
            CURLOPT_HTTPHEADER => array(
                "OCS-APIRequest: true"
            ),
            CURLOPT_CONNECTTIMEOUT => "10"
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ebsnextcloud_ChangePassword(array $params)
{
    try {
        
        //Disable Nextcloud account
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => 'https://'.$params['serverusername'].':'.$params['serverpassword'].'@'.$params['serverhostname'].'/ocs/v1.php/cloud/users/'.$params['username'],
            CURLOPT_HTTPHEADER => array(
                "OCS-APIRequest: true"
            ),
            CURLOPT_POSTFIELDS => http_build_query(array(
                key     =>  'password',
                value   =>  $params['password']
            )),
            CURLOPT_CONNECTTIMEOUT => "10"
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ebsnextcloud_ChangePackage(array $params)
{
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'configoption1' => 'The new service disk space',
        //     'configoption3' => 'Whether or not to enable FTP',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ebsnextcloud_TestConnection(array $params)
{
    try {
        // Call the service's connection test function.

        $success = true;
        $errorMsg = '';
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        $success = false;
        $errorMsg = $e->getMessage();
    }

    return array(
        'success' => $success,
        'error' => $errorMsg,
    );
}
//
//function ebsnextcloud_AdminCustomButtonArray()
//{
//    return array(
//        "Button 1 Display Value" => "buttonOneFunction",
//        "Button 2 Display Value" => "buttonTwoFunction",
//    );
//}
//
//function ebsnextcloud_ClientAreaCustomButtonArray()
//{
//    return array(
//        "Action 1 Display Value" => "actionOneFunction",
//        "Action 2 Display Value" => "actionTwoFunction",
//    );
//}
//
//function ebsnextcloud_buttonOneFunction(array $params)
//{
//    try {
//        // Call the service's function, using the values provided by WHMCS in
//        // `$params`.
//    } catch (Exception $e) {
//        // Record the error in WHMCS's module log.
//        logModuleCall(
//            'provisioningmodule',
//            __FUNCTION__,
//            $params,
//            $e->getMessage(),
//            $e->getTraceAsString()
//        );
//
//        return $e->getMessage();
//    }
//
//    return 'success';
//}
//
//function ebsnextcloud_actionOneFunction(array $params)
//{
//    try {
//        // Call the service's function, using the values provided by WHMCS in
//        // `$params`.
//    } catch (Exception $e) {
//        // Record the error in WHMCS's module log.
//        logModuleCall(
//            'provisioningmodule',
//            __FUNCTION__,
//            $params,
//            $e->getMessage(),
//            $e->getTraceAsString()
//        );
//
//        return $e->getMessage();
//    }
//
//    return 'success';
//}
//
//function ebsnextcloud_AdminServicesTabFields(array $params)
//{
//    try {
//        // Call the service's function, using the values provided by WHMCS in
//        // `$params`.
//        $response = array();
//
//        // Return an array based on the function's response.
//        return array(
//            'Number of Apples' => (int) $response['numApples'],
//            'Number of Oranges' => (int) $response['numOranges'],
//            'Last Access Date' => date("Y-m-d H:i:s", $response['lastLoginTimestamp']),
//            'Something Editable' => '<input type="hidden" name="ebsnextcloud_original_uniquefieldname" '
//                . 'value="' . htmlspecialchars($response['textvalue']) . '" />'
//                . '<input type="text" name="ebsnextcloud_uniquefieldname"'
//                . 'value="' . htmlspecialchars($response['textvalue']) . '" />',
//        );
//    } catch (Exception $e) {
//        // Record the error in WHMCS's module log.
//        logModuleCall(
//            'provisioningmodule',
//            __FUNCTION__,
//            $params,
//            $e->getMessage(),
//            $e->getTraceAsString()
//        );
//
//        // In an error condition, simply return no additional fields to display.
//    }
//
//    return array();
//}
//
//function ebsnextcloud_AdminServicesTabFieldsSave(array $params)
//{
//    // Fetch form submission variables.
//    $originalFieldValue = isset($_REQUEST['ebsnextcloud_original_uniquefieldname'])
//        ? $_REQUEST['ebsnextcloud_original_uniquefieldname']
//        : '';
//
//    $newFieldValue = isset($_REQUEST['ebsnextcloud_uniquefieldname'])
//        ? $_REQUEST['ebsnextcloud_uniquefieldname']
//        : '';
//
//    // Look for a change in value to avoid making unnecessary service calls.
//    if ($originalFieldValue != $newFieldValue) {
//        try {
//            // Call the service's function, using the values provided by WHMCS
//            // in `$params`.
//        } catch (Exception $e) {
//            // Record the error in WHMCS's module log.
//            logModuleCall(
//                'provisioningmodule',
//                __FUNCTION__,
//                $params,
//                $e->getMessage(),
//                $e->getTraceAsString()
//            );
//
//            // Otherwise, error conditions are not supported in this operation.
//        }
//    }
//}

//function ebsnextcloud_ServiceSingleSignOn(array $params)
//{
//    try {
//        // Call the service's single sign-on token retrieval function, using the
//        // values provided by WHMCS in `$params`.
//        $response = array();
//
//        return array(
//            'success' => true,
//            'redirectTo' => $response['redirectUrl'],
//        );
//    } catch (Exception $e) {
//        // Record the error in WHMCS's module log.
//        logModuleCall(
//            'provisioningmodule',
//            __FUNCTION__,
//            $params,
//            $e->getMessage(),
//            $e->getTraceAsString()
//        );
//
//        return array(
//            'success' => false,
//            'errorMsg' => $e->getMessage(),
//        );
//    }
//}

function ebsnextcloud_AdminSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on admin token retrieval function,
        // using the values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

//function ebsnextcloud_ClientArea(array $params)
//{
//    // Determine the requested action and set service call parameters based on
//    // the action.
//    $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';
//
//    if ($requestedAction == 'manage') {
//        $serviceAction = 'get_usage';
//        $templateFile = 'templates/manage.tpl';
//    } else {
//        $serviceAction = 'get_stats';
//        $templateFile = 'templates/overview.tpl';
//    }
//
//    try {
//        // Call the service's function based on the request action, using the
//        // values provided by WHMCS in `$params`.
//        $response = array();
//
//        $extraVariable1 = 'abc';
//        $extraVariable2 = '123';
//
//        return array(
//            'tabOverviewReplacementTemplate' => $templateFile,
//            'templateVariables' => array(
//                'extraVariable1' => $extraVariable1,
//                'extraVariable2' => $extraVariable2,
//            ),
//        );
//    } catch (Exception $e) {
//        // Record the error in WHMCS's module log.
//        logModuleCall(
//            'provisioningmodule',
//            __FUNCTION__,
//            $params,
//            $e->getMessage(),
//            $e->getTraceAsString()
//        );
//
//        // In an error condition, display an error page.
//        return array(
//            'tabOverviewReplacementTemplate' => 'error.tpl',
//            'templateVariables' => array(
//                'usefulErrorHelper' => $e->getMessage(),
//            ),
//        );
//    }
//}
