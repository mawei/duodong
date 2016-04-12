<nav class="navbar navbar-default navbar-fixed-top noBorder" role="navigation">
			<div class="container">
				
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand brandStyle" href="#">泉林汽车</a>
				</div>
				
				<div class="collapse navbar-collapse noPadding"	id="myNavbar">
					<div class="navbar-right menustyle">
						<ul class="nav navbar-nav">
							<li><a href="site"><p>首页</p></a></li>
							<li><a href=""><p>爱车师傅</p></a></li>
							<li class="dropdown">
						        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">泉林师傅 <b class="caret"></b></a>
						        <ul class="dropdown-menu">
						          <li><a href="<?=site_url('site/workerlist')?>">师傅预约</a></li>
						          <li><a href="#">师傅在线</a></li>
						          <li><a href="#">师傅上门</a></li>
						          <li><a href="#">增值服务</a></li>
  					            </ul>
						    </li>
							
							<li class="dropdown">
						        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">泉林导航 <b class="caret"></b></a>
						        <ul class="dropdown-menu">
						          <li><a href="#">养车宝典</a></li>
						          <li><a href="#">加入我们</a></li>
						          <li><a href="#">去找我们</a></li>
						          <li class="divider"></li>
						          <li><a href="#">建议意见</a></li>
  					            </ul>
						    </li>
						    
						    <li class="dropdown">
						        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">我的资料 <b class="caret"></b></a>
						        <ul class="dropdown-menu">
						          <li><a href="<?=site_url('index/wheel')?>">轮胎</a></li>
						          <li><a href="<?=site_url('index/insurancelist')?>">保险</a></li>
						          <li><a href="<?=site_url('index/weihulist')?>">维护</a></li>
						          <li class="divider"></li>
								  <li><a href="<?php echo site_url('index/loginout') ?>"><p>退出</p></a></li>
						        </ul>
						    </li>
						</ul>
					</div>
				</div>
				
			</div>
		</nav>
