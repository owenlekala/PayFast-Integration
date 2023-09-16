<?php

function generateSignature($data, $passPhrase = null) {
  
    $pfOutput = '';
    foreach ($data as $key => $val) {
        if ($val !== '') {
            $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
        }
    }
    // Remove last ampersand
    $getString = substr($pfOutput, 0, -1);
    if ($passPhrase !== null) {
        $getString .= '&passphrase=' . urlencode(trim($passPhrase));
    }
    return md5($getString);
}

$cartTotal = 10.00; //amount needs to be sent here
$passPhrase = 'PASSPHRASE';
$data = array(
    'merchant_id' => 'YOUR_MERCHANT_ID',
    'merchant_key' => 'YOUR_MECHANT_KEY',
    'return_url' => 'https://yourlink.com/return.php',
    'cancel_url' => 'https://yourlink.com/cancel.php',
    'notify_url' => 'https://yourlink.com/notify.php',
    'name_first' => 'FIRSTNAME_OF_SENDER',
    'name_last'  => 'LASTNAME_OF_SENDER',
    'email_address' => 'SENDER_EMAIL',
    'm_payment_id' => 'UNIQUE_PAYMENT_ID',
    'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),//do not change
    'item_name' => 'ORDER_NAME',
);
$signature = generateSignature($data, $passPhrase);
$data['signature'] = $signature;

// If in testing mode make use of either sandbox.payfast.co.za or www.payfast.co.za
$testingMode = false; //if you are testing this in sandbox, change to "true"
$pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
$htmlForm = '<form action="https://' . $pfHost . '/eng/process" method="post">';
foreach ($data as $name => $value) {
    $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
}
$htmlForm .= '<input type="submit" name="paynow" value="Pay Now"></form>';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayFast Integration</title>
</head>
<body>
    <h1>PayFast Integration Example</h1>
    
    <?php echo $htmlForm; ?>

</body>
</html>

