<?php
namespace Midtrans;

require_once dirname(__FILE__) . '/midtrans/Midtrans.php';
require 'functions.php';

$raw_post_data = file_get_contents('php://input');

// Parse the JSON data
$json_data = json_decode($raw_post_data, true);

Config::$isProduction = false;
Config::$serverKey = 'SB-Mid-server-sGKoECP1bTPyYFbruWktC5l-';

$notif = new Notification();

$transaction = $notif->transaction_status;
$type = $notif->payment_type;
$order_id = $notif->order_id;
$fraud = $notif->fraud_status;

$message = 'ok';

if ($transaction == 'capture') {
    // For credit card transaction, we need to check whether transaction is challenge by FDS or not
    // if ($type == 'credit_card') {
        if ($fraud == 'challenge') {
            // TODO set payment status in merchant's database to 'Challenge by FDS'
            // TODO merchant should decide whether this transaction is authorized or not in MAP
            $message = "Transaction order_id: " . $order_id ." is challenged by FDS";
        } else {
            // TODO set payment status in merchant's database to 'Success'
            $message = "Transaction order_id: " . $order_id ." successfully captured using " . $type;
        }
    // }
} elseif ($transaction == 'settlement') {
    // TODO set payment status in merchant's database to 'Settlement'
    $data = konfirmasiBayar($json_data);
    toLunas($data['idsewa']);
    $message = "Transaction order_id: " . $order_id1 ." successfully transfered using " . $type;
} elseif ($transaction == 'pending') {
    // TODO set payment status in merchant's database to 'Pending'
    $message = "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
} elseif ($transaction == 'deny') {
    // TODO set payment status in merchant's database to 'Denied'
    $message = "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
} elseif ($transaction == 'expire') {
    // TODO set payment status in merchant's database to 'expire'
    $data = konfirmasiBatalBayar($json_data);
    hapusPesan($data['idsewa']);
    $message = "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
} elseif ($transaction == 'cancel') {
    // TODO set payment status in merchant's database to 'Denied'
    $message = "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
}

// Send a 200 OK response to Midtrans to acknowledge the notification
header('HTTP/1.1 200 OK');

$filename = $order_id . ".txt";
$dirpath = 'log';
is_dir($dirpath) || mkdir($dirpath, 0777, true);

echo file_put_contents($dirpath . "/" . $filename, $message);