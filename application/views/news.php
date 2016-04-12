<div class="content_block">
 <!-- top_title -->
 <div class="top_title">
  <div class="wraper">
   <h2>法律法规 </h2>
   <ul>
    <li><a href="#">法律法规</a></li>
<!--     <li><a href="#">{category}</a></li> -->
   </ul>
  </div>
 </div>
 <!-- /top_title -->
 <div class="wraper">
  <!-- blog entries full -->
  <div class="blog_entries blog_entries_full">
  
  {news}
  
   <div class="post">
    <div class="desc">
     <h4><a href="<?=site_url('index/newsdetail/{id}')?>">{title}</a></h4>
      <p>{content}    [{create_date}]</p>
      <a href="<?=site_url('index/newsdetail/{id}')?>" class="read_more btn_col">更多</a>
     </div>   
    </div> 
    {/news}
      
   </div>
   <!-- pager_nav -->
   <div class="pager_nav">
    <a href="<?=site_url('index/news')?>/{category}?page={lastpage}">上一页</a><span>{page}</span><a href="<?=site_url('index/news')?>/{category}?page={nextpage}">下一页</a>
   </div>
   <!-- /pager_nav -->
  </div>
  <!-- /blog entries full -->
 </div>
</div>
