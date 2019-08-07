<?php include('pay.php'); ?>
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
</div>
