<?php
$passPhrase = 'PASSPHRASE';
$data = [
    'merchant_id' => 'YOUR_MERCHANT_ID',
    'merchant_key' => 'YOUR_MECHANT_KEY',
    'return_url' => 'https://yourlink.com/return.php',
    'cancel_url' => 'https://yourlink.com/cancel.php',
    'notify_url' => 'https://yourlink.com/notify.php',
    'name_first' => 'FIRSTNAME_OF_SENDER',
    'name_last'  => 'LASTNAME_OF_SENDER',
    'email_address' => 'SENDER_EMAIL',
    'm_payment_id' => 'UNIQUE_PAYMENT_ID',
    'amount' => '500',
    'item_name' => 'Order#123',
];

/**
 * @param array $data
 * @param null $passPhrase
 * @return string
 */

function generateSignature($data, $passPhrase = null) {
    // Create parameter string
    $pfOutput = '';
    foreach( $data as $key => $val ) {
        if($val !== '') {
            $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
        }
    }
    // Remove last ampersand
    $getString = substr( $pfOutput, 0, -1 );
    if( $passPhrase !== null ) {
        $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
    }
    return md5( $getString );
} 

function dataToString($dataArray) {
  // Create parameter string
    $pfOutput = '';
    foreach( $dataArray as $key => $val ) {
        if($val !== '') {
            $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
        }
    }
    // Remove last ampersand
    return substr( $pfOutput, 0, -1 );
}

function generatePaymentIdentifier($pfParamString, $pfProxy = null) {
    // Use cURL (if available)
    if( in_array( 'curl', get_loaded_extensions(), true ) ) {
        // Variable initialization
      
        //$url = 'https://sandbox.payfast.co.za/onsite/process';
        $url = 'https://payfast.co.za/onsite/process';
      
        // Create default cURL object
        $ch = curl_init();

        // Set cURL options - Use curl_setopt for greater PHP compatibility
        // Base settings
        curl_setopt( $ch, CURLOPT_USERAGENT, NULL );  // Set user agent
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );      // Return output as string rather than outputting it
        curl_setopt( $ch, CURLOPT_HEADER, false );             // Don't include header in output
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );

        // Standard settings
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $pfParamString );
        if( !empty( $pfProxy ) )
            curl_setopt( $ch, CURLOPT_PROXY, $pfProxy );

        // Execute cURL
        $response = curl_exec( $ch );
        curl_close( $ch );
        echo $response;
        $rsp = json_decode($response, true);
        if ($rsp['uuid']) {
            return $rsp['uuid'];
        }
    }
    return null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayFast Integration</title>
    <script src="https://www.payfast.co.za/onsite/engine.js"></script>
</head>
<body>
    <h1>PayFast Integration Example</h1>
    <form action="https://yourdomain.co.za" method="post">
        
        <input type="submit" name= "paynow" value="Pay Now">
    </form>

<?php

if(isset($_POST['paynow'])){

$data["signature"] = generateSignature($data, $passPhrase);

// Convert the data array to a string
$pfParamString = dataToString($data);

// Generate payment identifier
$identifier = generatePaymentIdentifier($pfParamString);

if($identifier !== null){

   echo '<script type="text/javascript">window.payfast_do_onsite_payment({"uuid":"'.$identifier.'"});</script>';
}

}
?>

</body>
</html>
