<!-- 
<div class="container">
<div class="row">
<div class="col-sm-12">
<h2>Weekly Subscription</h2>
<br><br>
<div class="col-sm-4 col-lg-4 col-md-4">
<div class="thumbnail">
<img src="prod.gif" alt="">
<div class="caption">
<h4 class="pull-right">₹1.00</h4>
<h4><a href="#">Weekly</a></h4>
</div>
<form id="checkout-selection" action="pay.php" method="POST">
<input type="hidden" name="item_name" value="My Test Product">
<input type="hidden" name="item_description" value="Weekly Subscription">
<input type="hidden" name="item_number" value="3456">
<input type="hidden" name="amount" value="1.00">
<input type="hidden" name="address" value="ABCD Address">
<input type="hidden" name="currency" value="INR">
<input type="hidden" name="email" value="test@test.com">
<input type="hidden" name="contact" value="9999999999">
<input type="submit" class="btn btn-primary" value="Buy Now">
</form>
</div>
</div>
</div>
</div>
</div> -->

<?php

$curl = curl_init();
$plan = $_GET['plan'];
$plan_id = '';
$rd = $_GET['rd'];


$plan_id = 'plan_D2ZMOWnWxa2jt6';


curl_setopt_array($curl, array(
  CURLOPT_URL => "https://rzp_test_AqyE3JjZFOmNJZ:xtL2qf6lF0XO5zY8ijkXQYG6@api.razorpay.com/v1/subscriptions",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => 'plan_id='.$plan_id.'&total_count=12&customer_notify=1',
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/x-www-form-urlencoded",
    "postman-token: 67d92778-3ca8-ffb4-9680-c384d115f95a"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);


$id = '';

if ($err) {
  echo "cURL Error #:" . $err;
} else {
    $jsonArray = json_decode($response,true);
    $id = $jsonArray["id"];
}
?>

<div id = "order-table" class = "order-table" style = "width: 500px; height: 400px; border: 1px solid black; position: relative;">
<p> Order Summary</p>
<div style = "width: 100%; height: 50%; display: inline-block;">
<p> Khabar Lahariya Subscription</p><p>Rs. 200</p>
</div>
<p class="text-center py-3"><button id="turkbox" class="btn btn-primary"
 style="background-color: #4abba9 !important;">
Buy Subscriptioin</button></p>
</div>



<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<form name='razorpayform' action="wp-admin/admin-post.php" method="POST">
    <input type="hidden" name ="action" value="verify_payment">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
    <input type="hidden" name="subscription_id"  id="subscription_id" >
    <input type="hidden" name="redirect"  id="redirect" >
</form>

<script>
var id = "<?php echo $id?>";
var options = {
    "key": "rzp_test_AqyE3JjZFOmNJZ",
    "subscription_id": id,
    "name": "Khabar Lahariya ",
    "description": "Subscription",
    "handler": function (response){
       
    },
    
    "theme": {
        "color": "#00a1f1"
    }
};

options.handler = function (response){
    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
    document.getElementById('razorpay_signature').value = response.razorpay_signature;
    document.getElementById('subscription_id').value = id;
    document.getElementById('redirect').value = "<?php echo $rd ?>";
    document['razorpayform'].submit();
};

var turkbox= new Razorpay(options);

document.getElementById('turkbox').onclick = function(e){
    turkbox.open();
    e.preventDefault();
}
</script>



