<header class="header">
	<a class="logo" href="">大状</a>
	<nav>
		<!-- top menu -->
		<ul>
			<li><a href="<?=site_url('index/index')?>">首页</a></li>

			<li><a href="<?=site_url('index/service')?>">服务</a></li>
			<li><a href="">法律法规</a>
				<ul>
					{categorys}
					<li><a href="<?=site_url('index/news')?>/{id}">{name}<span></span></a></li>
					{/categorys}
				</ul></li>
			<ul>
				<li><a href="<?=site_url('index/about')?>">关于我们</a></li>
				<li><a href="<?=site_url('index/contact')?>">联系我们</a></li>
			</ul>
			<li><a href="<?=site_url('index/usercenter')?>">个人中心</a></li>
			<!-- /top menu -->
		</ul>
	</nav>
</header>
