<?php
include("config.php");
$razorpayPaymentId = $_POST["razorpay_payment_id"];
$razorpaySignature = $_POST["razorpay_signature"];
$subscriptionId = $_POST["subscription_id"];
$rd = $_POST['redirect'];

$expectedSignature = hash_hmac('sha256', $razorpayPaymentId . '|' . $subscriptionId, $keySecret);

if ($expectedSignature === $razorpaySignature)
{
    echo "Payment is successful!";
    $user = wp_get_current_user();
    $user->add_role('paid_subscriber');
    wp_redirect($rd);
}
