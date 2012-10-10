<?php
define ('ROOT_PATH', '../');
include (ROOT_PATH.'birding/db_config.php');

$selected_bird = isset($_GET['id'])?$_GET['id']:0;
$page = isset($_GET['page'])?$_GET['page']:0;
$order_by = isset($_GET['order_by'])?$_GET['order_by']:0;
$or = isset($_GET['or'])?$_GET['or']:'ds';
$records_per_page = 12;
        
$total_items = 0;   
$result = $sql_db->getRow('SELECT COUNT(*) total FROM birds_index');
if ($sql_db->getNumberOfReturnedRows()) {
   $total_items = $result->total;       
}  else {
   die('configure database!');   
}
$highest_allowed_num = 0;
$result_highest_allowed_num = $sql_db->getRow('SELECT MAX(bird_id) total FROM birds_index'); 
if ($sql_db->getNumberOfReturnedRows()) {
   $highest_allowed_num = $result_highest_allowed_num->total;       
}  else {
   die('configure database!');   
}
//make sure there is nothing hinky!
if ($selected_bird > $highest_allowed_num || $selected_bird < 0) { 
   $selected_bird = 0;
}

$index_result = array();
if ($selected_bird == 0)
{
   // show index
   $total_pages = floor($total_items / $records_per_page);
   if ($page > $total_pages) { $page = 0; }
   $start_record = $page * $records_per_page;   
   $previous_page = $page - 1;
   $next_page = $page + 1;
   switch ($order_by) {
      case 'name':
         $order_by_db = "ORDER BY birds_index.common_name";
         break;
      case 'sc_name':
         $order_by_db = "ORDER BY birds_index.scientific_name";
         break;
      case 'date':
         $order_by_db = "ORDER BY birds_index.date"; 
         break;
      case 'location':
         $order_by_db = "ORDER BY birds_index.location"; 
         break;
      default:
         $order_by_db = "ORDER BY birds_index.bird_id"; 
   }
  
  $or_method = 'DESC';
   switch ($or) {
      case 'as':
         $or_method = "ASC";
         break;
      case 'ds':
      default:
         $or_method = "DESC";
         break;
   }
   
   $query = "
      SELECT 
         birds_index.bird_id id,
         birds_index.common_name,
         birds_index.scientific_name,
         birds_index.date,
         birds_index.notes,
         birds_index.location, 
         birds_index.image_name,
         birds_index.image_thumbnail_name          
      FROM 
         birds_index 
      $order_by_db $or_method
      LIMIT $start_record, $records_per_page";

   $index_result = $sql_db->getRows($query);
   if (!$sql_db->getNumberOfReturnedRows()) {                                           
      die('configure database!');   
   }   
   
} else {                
   //show detail item
   $query = "
      SELECT 
         birds_index.bird_id id,
         birds_index.common_name,
         birds_index.scientific_name,
         birds_index.date,
         birds_index.location,
         birds_index.notes,
         birds_index.wiki_entry,
         birds_index.image_name,
         birds_index.image_thumbnail_name          
      FROM 
         birds_index 
      WHERE
         birds_index.bird_id = $selected_bird";
      
   $bird_result = $sql_db->getRow($query);
   if (!$sql_db->getNumberOfReturnedRows()) {                                           
      die('configure database!');   
   } 
   $query = "
      SELECT     
         birds_alt_images.image_name,
         birds_alt_images.image_thumbnail_name          
      FROM 
         birds_alt_images
      WHERE
         birds_alt_images.bird_id = $selected_bird";
      
   $bird_alt_images_result = $sql_db->getRows($query);  
}
$title = 'Rod Simpson Photography - Birding Life List Gallery';
$description = "Rod Simpson's Birding Life List Gallery";
$keywords = "Rod Simpson, Birding, Birding Life List, Gallery, Photography, Bird Photography";
if ($selected_bird > 0) {
   $title = 'Rod Simpson Photography - '. $bird_result->common_name. ' ('.$bird_result->scientific_name.')';
   $description = 'Details for '. $bird_result->common_name. ' ('.$bird_result->scientific_name.'), from Rod Simpson Photography';
   $keywords = $bird_result->common_name.",".$bird_result->scientific_name.", Rod Simpson, Birding, Birding Life List, Gallery, Photography, Bird Photography";   
}      


?>   
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
<title><?=$title;?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
<meta name="description" content="<?=$description;?>" />
<meta name="keywords" content="<?=$keywords;?>" />
<meta name="robots" content="index,follow,noodp,noydir" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="/css/styles.css" type="text/css" />
<link rel="stylesheet" href="/css/styles_old.css" type="text/css" />
<link rel="alternate" type="application/atom+xml" href="/atom.xml" title="Rod Simpson's blog">
<!--[if (gte IE 6)&(lte IE 8)]>
<script type="text/javascript" src="/js/html5.js"></script>
<link rel="stylesheet" href="/css/ie.css" type="text/css" />
<![endif]-->
<script type="text/javascript" src="//use.typekit.net/mhm2zyq.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<script type="text/javascript" src="/js/jquery-1.8.0.min.js"></script>
</head>
<body>
<header class="header_bg clearfix">
   <div class="container clearfix bottom_rule">
      <div class="logo">
         <span class="logo1"><a href="/index.html">ROD</a></span>
         <span class="logo2"><a href="/index.html">SIMPSON</a></span>
      </div>
      <nav class="main-menu">
         <ul class="sf-menu">
            <li><a href="/index.html">MAIN</a></li>
            <li><a href="/pages/artist/photography_gallery.html">PHOTOGRAPHER</a></li>
            <li><a href="/pages/writer/writing.html">WRITER</a></li>
            <li><a href="/pages/developer/resume.html">DEVELOPER</a></li>
            <li><a href="/blog">BLOG</a></li>
            <li><a href="/pages/personal/me.html">ME</a></li>
            <li><a href="/pages/personal/contact.html">CONTACT</a></li>
            <li style="margin-right: 0px;"><a href="/atom.xml"><img src="/images/rss.png" class="rssfeed"></a></li>
         </ul>
      </nav>
      <div class="clear padding5"></div>
   </div>
</header>

 <!-- Content -->
<section class="container clearfix">
   <div class="clear padding25"></div>
   <div class="gallery_listing">
<?php


if ($selected_bird == 0)
{   
   $max_cols = 4;
   $curr_col = 1;
   ?>  
   <div class="alignleft"><span>Total Birds Sighted: <?=$total_items;?></span></div>
    
   <div class="alignleft">
   Current page: <?=$page+1;?> of <?=$total_pages+1;?> &nbsp;     
   <?php
   if ($previous_page >=0 ) {
   ?>
      <a href="<?=ROOT_PATH;?>birding/index.php?page=<?=$previous_page;?>&order_by=<?=$order_by;?>&or=<?=$or;?>" class="small_link_u"><< previous page</a> &nbsp;
   <?php
   } else {
   ?>

   <?php
   }
   ?>       
   &nbsp;   
   <?php
   if ($next_page <= $total_pages ) {
   ?>
      <a href="<?=ROOT_PATH;?>birding/index.php?page=<?=$next_page;?>&order_by=<?=$order_by;?>&or=<?=$or;?>" class="small_link_u">next page >></a> &nbsp;
   <?php
   } else {
      ?>

      <?php
   } 
   ?> 
   </div> 
   <div class="alignright">
      Order by: &nbsp;
      <a href="<?=ROOT_PATH;?>birding/index.php?page=0&order_by=name&or=<?=($or=='as')?'ds':'as';?>" class="small_link_u">common name</a> &nbsp;
      <a href="<?=ROOT_PATH;?>birding/index.php?page=0&order_by=sc_name&or=<?=($or=='as')?'ds':'as';?>" class="small_link_u">scientific name</a> &nbsp;
      <a href="<?=ROOT_PATH;?>birding/index.php?page=0&order_by=date&or=<?=($or=='as')?'ds':'as';?>" class="small_link_u">date</a> &nbsp;
      <a href="<?=ROOT_PATH;?>birding/index.php?page=0&order_by=location&or=<?=($or=='as')?'ds':'as';?>" class="small_link_u">location</a> &nbsp;
   </div>
   <div class="clear padding15"></div>
   <?php
   foreach($index_result as $bird) { 
      $class ="gallery_image_container";
      if ($curr_col % $max_cols == 0) {
         $class = 'gallery_image_container_last';
      }
      ?>
      <div class="<?=$class;?>">
         <a href="<?=ROOT_PATH;?>birding/index.php?id=<?=$bird->id;?>&page=<?=$page;?>&order_by=<?=$order_by;?>&or=<?=$or;?>">
            <img class="gallery_image" src="<?=$bird->image_thumbnail_name;?>" alt="<?=$bird->common_name;?>" />
         </a>
         <div class="bird_gallery_title">
            <a class="small_link" href="<?=ROOT_PATH;?>birding/index.php?id=<?=$bird->id;?>&page=<?=$page;?>&order_by=<?=$order_by;?>&or=<?=$or;?>">
               <?=$bird->common_name;?>
            </a>
            <br/>
            <a class="smaller_link" href="<?=ROOT_PATH;?>birding/index.php?id=<?=$bird->id;?>&page=<?=$page;?>&order_by=<?=$order_by;?>&or=<?=$or;?>">
               (<?=$bird->scientific_name;?>)
            </a>
            <br>
         </div>
      </div>
      <?php
      $curr_col++;
   }
} else if ($selected_bird > 0)  {
   
   ?>
     <div class="alignleft">
        <?php if ($selected_bird > 0)  { ?>
          &nbsp;  &nbsp;  &nbsp; <a href="<?=ROOT_PATH;?>birding/index.php?page=<?=$page;?>&order_by=<?=$order_by;?>&or=<?=$or;?>" class="small_link"><< Return To Birding Life List Index</a>
        <?php } ?>
     </div>

<div id="sub_content">    
   <div style="float: left;">     
      <img id="main_image" src="<?=$bird_result->image_name;?>" alt="Image of <?=$bird_result->common_name;?>">
      <br /> <br />  
      <?php 
      if (count($bird_alt_images_result) > 0) {
         ?>Alternate Images (hover over to view):<br /><?php
         $i=0;  
           
         foreach ($bird_alt_images_result as $alt_bird)
         {
            if ($i==3) {
               $i=0;
               ?><br /> <br /><?php
            }
            ?>
            <img width="195" src="<?=$alt_bird->image_name;?>" 
               onmouseover="document.images.main_image.src='<?=$alt_bird->image_name;?>';"
               onmouseout="document.images.main_image.src='<?=$bird_result->image_name;?>';"
            />
            &nbsp;
            <?php
            $i++;
         }
      }
   
      ?>
   </div>

   <div style="float: right; width: 350px; line-height: 16px;">      
      <b><?=$bird_result->common_name;?></b>
      <br><br>
      <b>Common Name:</b> <?=$bird_result->common_name;?><br>      
      <b>Scientific Name:</b> <?=$bird_result->scientific_name;?><br>
      <b>Date Sighted:</b> <?=$bird_result->date;?><br>
      <b>Location of sighting:</b> <?=$bird_result->location;?><br>
      <b>Notes:</b> <?=$bird_result->notes;?><br><br>
      Learn more about the <?=$bird_result->common_name;?> at the <a href="<?=$bird_result->wiki_entry;?>" target="_blank" class="small_download_link">wiki entry</a>.
         </tr>
      </table>
   </div>
</div>
<?php
}
?>
 </div>

</section>

<div class="clear padding60"></div>
<footer class="footer_bg_bottom" id="footer">
   <div class="footer_bottom container">
      <div class="menu">
         <ul class="sf-menu">
            <li><a href="/pages/artist/photography_gallery.html">Photographer</a></li>
            <li><a href="/pages/writer/writing.html">Writing</a></li>
            <li><a href="/pages/developer/resume.html">Developer</a></li>
            <li><a href="/blog">Blog</a></li>
            <li><a href="/pages/personal/me.html">Me</a></li>
            <li><a href="/pages/personal/contact.html">Contact</a></li>
         </ul>
      </div>
      <div class="clear padding20"></div>
         <p>
            &copy; 2012 All Rights Reserved. &nbsp; <a href="pages/artist/copyright.html">Copyright Notice</a>
         </p>
      </div>
      <div class="clear padding20"></div>
</footer>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19276682-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
