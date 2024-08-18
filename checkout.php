<?php

namespace Midtrans;

require_once dirname(__FILE__) . '/midtrans/Midtrans.php';
require 'functions.php';

// Set your Merchant Server Key
Config::$serverKey = 'SB-Mid-server-sGKoECP1bTPyYFbruWktC5l-';
$clientKey = 'SB-Mid-client-f7B9jBRZfO2q1Zs4';
// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
// Config::$isProduction = true;
// Set sanitization on (default)
Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
Config::$is3ds = true;

// // Use new notification url(s) disregarding the settings on Midtrans Dashboard Portal (MAP)
// Config::$overrideNotifUrl = "http://localhost/sewa-lapangan-bulutangkis/handler.php";

$total = $_POST['total'];
$idsewa = $_POST['idsewa'];
$order_id = rand();

// required
$transaction_details = array(
    'order_id' => $order_id,
    'gross_amount' => $total, // no decimal allowed for creditcard
);

$enable_payments = array('credit_card','cimb_clicks','mandiri_clickpay','echannel');

$params = array(
    'transaction_details' => $transaction_details,
);

try {
    
    // Get Snap Payment Page URL
    $paymentUrl = Snap::createTransaction($params)->redirect_url;
    
    $data = array(
        'order_id' => $order_id,
        'idsewa' => $idsewa,
    );
    tambahBayar($data);
    
    // Redirect to Snap Payment Page
    header('Location: ' . $paymentUrl);
}
catch (\Exception $e) {
    echo $e->getMessage();
}

$snapToken = Snap::getSnapToken($transaction);