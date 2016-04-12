

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta name="generator" content="Bootply" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href="<?php echo base_url("assets/static/js/bootstrap/css/bootstrap.min.css");?>" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link href="<?php echo base_url("assets/static/css/styles.css");?>" rel="stylesheet">
		
		<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<?php foreach($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
		
	</head>
	<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">大状 后台管理</a>
        </div>
        <div class="navbar-collapse collapse">
<!--           <ul class="nav navbar-nav navbar-right"> -->
<!--             <li><a href="#">Dashboard</a></li> -->
<!--             <li><a href="#">Settings</a></li> -->
<!--             <li><a href="#">Profile</a></li> -->
<!--             <li><a href="#">Help</a></li> -->
<!--           </ul> -->
<!--           <form class="navbar-form navbar-right"> -->
<!--             <input type="text" class="form-control" placeholder="Search..."> -->
<!--           </form> -->
        </div>
      </div>
</nav>

<div class="container-fluid">
      
      <div class="row row-offcanvas row-offcanvas-left">
        
         <div class="col-sm-3 col-md-2 sidebar-offcanvas" id="sidebar" role="navigation">
           
            <ul class="nav nav-sidebar">
              <li class="active"><a href="#">Overview</a></li>
                <li><a href='<?php echo site_url('admin/user')?>'>用户管理</a></li>
				<li><a href='<?php echo site_url('admin/victim')?>'>受害者管理</a></li>
				<li><a href='<?php echo site_url('admin/lawyer')?>'>律师管理</a></li>
				<li><a href='<?php echo site_url('admin/cases')?>'>案件管理</a></li>
				<li><a href='<?php echo site_url('admin/quotation')?>'>报价管理</a></li>
				<li><a href='<?php echo site_url('admin/payment')?>'>付款管理</a></li>
				<li><a href='<?php echo site_url('admin/evidence')?>'>证据管理</a></li>
				<li><a href='<?php echo site_url('admin/config')?>'>配置管理</a></li>
				<li><a href='<?php echo site_url('admin/message')?>'>消息管理</a></li>
				<li><a href='<?php echo site_url('admin/select_case_to_lawyer')?>'>案件分配</a></li>
				<li><a href='<?php echo site_url('admin/news')?>'>网站新闻</a></li>
				<li><a href='<?php echo site_url('admin/newscategory')?>'>新闻分类</a></li>
												            </ul>
            <ul class="nav nav-sidebar">
            </ul>
            <ul class="nav nav-sidebar">
            </ul>
          
        </div><!--/span-->
        
        <div class="col-sm-9 col-md-10 main">
          
          <!--toggle sidebar button-->
          <p class="visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
          </p>
          
		  <h1 class="page-header">
            Dashboard
          </h1>

          <div class="table-responsive">
			<?php echo $output;?>
          </div>

      </div><!--/row-->
	</div>
</div><!--/.container-->

<footer>
  <p class="pull-right">©2014 Company</p>
</footer>
        
	<!-- script references -->
		<script src="<?php echo base_url("assets/static/js/scripts.js");?>"></script>
	</body>
</html>



