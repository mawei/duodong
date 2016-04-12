<!DOCTYPE html>
<html>
	{head}
	<body>
		<!--  Navigation Bar -->
		{header}
		<!-- Main Page -->
		
		
	<div class="container-fluid noPadding">
		<div class="row">
	 		 <div class="col-xs-11 col-md-11" style="text-align: right">
	 		 		<a href="<?php echo site_url('index/loginout') ?>"><font color="red">退出</font></a>
	  		 </div>
	   </div>
	
		<div class="container text-left">

		<div class="list-group">
		{weihus}
			<a href="#" class="list-group-item">
		    <h4 class="list-group-item-heading">{itemname}</h4>
		    <p>数量：{quantity} 价格：{price} 总价：{total} </p>
	   		<p>店铺：{shop}</p>
	   		</a>
		{/weihus}
		</div>

	<div class="row">
	  <div class="col-xs-6 col-md-3">
	    <a href="#" class="thumbnail">
	      <img src="<?=$insurances['image']?>" alt="...">
	    </a>
	            <h4>保单照片</h4>
	  </div><div class="col-xs-6 col-md-3">
	    <a href="#" class="thumbnail">
	      <img src="<?=$insurances['ID_front_image']?>" alt="...">
	    </a>
	            <h4>身份证正面</h4>
	  </div><div class="col-xs-6 col-md-3">
	    <a href="#" class="thumbnail">
	      <img src="<?=$insurances['ID_back_image']?>" alt="...">
	    </a>
	            <h4>身份证反面</h4>
	  </div><div class="col-xs-6 col-md-3">
	    <a href="#" class="thumbnail">
	      <img src="<?=$insurances['bank_image']?>" alt="...">
	    </a>
	    <h4>银行理赔卡照片</h4>
	  </div>
	</div>
	
	</div>
	</div>
			<!-- Footer -->
	</body>
</html>