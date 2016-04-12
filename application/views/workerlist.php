<!DOCTYPE html>
<html>
{head}
<body>
<div class="container">

<div data-role="page">
{header}
  <div data-role="content">
  		<div class="list-group">
		{records}
		  <a href="tel:{phone}" class="list-group-item">
		    <h4 class="list-group-item-heading">{name}</h4>
	    	<p>联系方式：{phone}</p>
		   </a>
		{/records}
		</div>
  </div>
  {footer}
</div>
</div>
</body>
</html>


