<!DOCTYPE html>
<head>
	<title>完善资料</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/templatemo_style.css')?>"/>
	{head}
</head>
<body class="templatemo-bg-gray">
	<div class="container">
		<div class="col-md-12">
			<h1 class="margin-bottom-15">完善资料</h1>
			<form class="form-horizontal templatemo-container templatemo-login-form-1 margin-bottom-30" role="form" method="post" action="<?php echo site_url("index/complete_profile");?>" data-ajax="false">				
		        <div class="form-group">
		          <div class="col-xs-12">		            
		            <div class="control-wrapper">
		            	<label for="carmodel" class="control-label fa-label"><i class="fa fa-user fa-medium"></i></label>
		            	<input type="text" class="form-control" id="carmodel" name="carmodel" placeholder="车型">
		            </div>		            	            
		          </div>              
		        </div>
		        <div class="form-group">
		          <div class="col-md-12">
		          	<div class="control-wrapper">
		            	<label for="wheel" class="control-label fa-label"><i class="fa fa-lock fa-medium"></i></label>
		            	<input type="text" class="form-control" id="wheel" name="wheel" placeholder="车轮">
		            </div>
		          </div>
		        </div>
		        <div class="form-group">
		          <div class="col-md-12">
		          	<div class="control-wrapper">
		          		<input type="submit" value="完成" class="btn btn-info">
 		          	</div>
		          </div>
		        </div>
		      </form>
		</div>
	</div>
</body>
</html>