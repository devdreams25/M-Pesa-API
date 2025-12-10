<?php 
header('Content-Type: application/json');

$stkCallBackResponse = file_get_contents('php://input');
$logFile = 'Mpesastkresponse.json';
$log = fopen($logFile, 'a');
fwrite($log, $stkCallBackResponse);
fclose($log) ;

$data = json_decode($stkCallBackResponse);

$MerchantRequestID = $data->Body->stkCallback->MerchantRequestID;
$CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID;
$ResultCode = $data->Body->stkCallback->ResultCode;
$ResultDesc = $data->Body->stkCallback->ResultDesc;
$Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value;
$TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value;
$UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value;

// Check if the transaction was successful
if ($ResultCode == 0) {
    // Store the transaction details in the database
    // Here you can add code to update your database or perform other actions
    // For example, you can log the transaction details
    $transactionDetails = [
        'MerchantRequestID' => $MerchantRequestID,
        'CheckoutRequestID' => $CheckoutRequestID,
        'Amount' => $Amount,
        'TransactionId' => $TransactionId,
        'UserPhoneNumber' => $UserPhoneNumber,
        'Status' => 'Success'
    ];
    
    // Log successful transaction
    $successLogFile = 'SuccessfulTransactions.json';
    $successLog = fopen($successLogFile, 'a');
    fwrite($successLog, json_encode($transactionDetails) . PHP_EOL);
    fclose($successLog);
} else {
    // Log failed transaction
    $failedLogFile = 'FailedTransactions.json';
    $failedLog = fopen($failedLogFile, 'a');
    $failedTransactionDetails = [
        'MerchantRequestID' => $MerchantRequestID,
        'CheckoutRequestID' => $CheckoutRequestID,
        'ResultCode' => $ResultCode,
        'ResultDesc' => $ResultDesc,
        'Status' => 'Failed'
    ];
    fwrite($failedLog, json_encode($failedTransactionDetails) . PHP_EOL);
    fclose($failedLog);
}