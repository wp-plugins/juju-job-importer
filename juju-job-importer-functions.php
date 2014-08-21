<?php
if (!function_exists('juju_job_importer_formate_date')):
function juju_job_importer_formate_date($raw_date,$format='%d %b %Y  %I:%M %p')
{
 if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) 
  return false;
 $year = (int)substr($raw_date, 0, 4);
 $month = (int)substr($raw_date, 5, 2);
 $day = (int)substr($raw_date, 8, 2);
 $hour = (int)substr($raw_date, 11, 2);
 $minute = (int)substr($raw_date, 14, 2);
 $second = (int)substr($raw_date, 17, 2);
 return strftime($format, mktime($hour,$minute,$second,$month,$day,$year));
}
endif;
///////////
if (!function_exists('juju_job_importer_draw_pull_down_menu')):
function juju_job_importer_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) 
{
 $field = '<select name="' . htmlspecialchars($name) . '"';
 if ($parameters!='') 
  $field .= ' ' . $parameters;
 $field .= '>';
 for ($i=0, $n=sizeof($values); $i<$n; $i++) 
 {
  $field .= '<option value="' . htmlspecialchars($values[$i]['id']) . '"';
  if(is_array($default))
  {
   if(in_array($values[$i]['id'],$default))
   {
    $field .= ' SELECTED';
   }
  }
  else
  {
   if($default==$values[$i]['id'])
   {
    $field .= ' SELECTED';
   }
  }
  $field .= '>' . htmlspecialchars($values[$i]['text'],ENT_QUOTES) . '</option>';
 }
 $field .= '</select>';
 if ($required == true) $field .= '&nbsp;<span class="inputRequirement">*</span>';;
  return $field;
}
endif;
/////////////
if (!function_exists('juju_job_importer_job_type_drop_down')):
function juju_job_importer_job_type_drop_down($name='job_type',$parameters='',$selected="",$header="",$header_value="")
{
 $required_list = array();
 if($header!='')
 $required_list[0] =array('id'=>$header_value,'text'=>$header);
 $required_list[] =array('id'=>'contract','text'=>'Contract');
 $required_list[] =array('id'=>'fulltime','text'=>'Full-time');
 $required_list[] =array('id'=>'parttime','text'=>'Part-time');
 $required_list[] =array('id'=>'internship','text'=>'Internship');
 $required_list[] =array('id'=>'temporary','text'=>'Temporary');
 return juju_job_importer_draw_pull_down_menu($name, $required_list, $selected,$parameters);
}
endif;
///////////
if (!function_exists('juju_job_importer_country_drop_down')):
function juju_job_importer_country_drop_down($name='country',$parameters='',$selected="",$header="",$header_value="")
{
 $country_list = array();
 if($header!='')
 $country_list[0] =array('id'=>$header_value,'text'=>$header);
 $country_list1 =juju_job_importer_country_list();
 $country_list=array_merge($country_list,$country_list1);
 return juju_job_importer_draw_pull_down_menu($name, $country_list, $selected,$parameters);
}
endif;
/////////////////
if (!function_exists('juju_job_importer_get_category_list')) :
function juju_job_importer_get_category_list($parent=0,$level=0)
{
 $args = array(
	'type'                     => 'post',
	'child_of'                 => 0,
	'parent'                   => $parent,
	'orderby'                  => 'name',
	'order'                    => 'ASC',
	'hide_empty'               => 0,
	'hierarchical'             => 1,
	'exclude'                  => '',
	'include'                  => '',
	'number'                   => '',
	'taxonomy'                 => 'category',
	'pad_counts'               => false );
 $list_set =array();
 $categories = get_categories( $args );
 foreach($categories as $category)
 {
  $list_set[]=array('id'=>$category->term_id,'text'=>$category->name,'level'=>$level);
  $list= juju_job_importer_get_category_list( $category->term_id ,($level+1));
  if(is_array($list) && count($list)>0)
  $list_set =array_merge($list_set,$list);
 }
 return $list_set;
}
endif;
///////////
if (!function_exists('juju_job_importer_get_category_drop_down')):
function juju_job_importer_get_category_drop_down($name='category',$parameters='',$selected="",$header="",$header_value="",$hierarchical=true)
{
 $cat_list =array();
 if($header!='')
 $cat_list[0] =array('id'=>$header_value,'text'=>$header);
 $cat_list= array_merge($cat_list,juju_job_importer_get_category_list());
 if($hierarchical)
 foreach($cat_list as $key =>$value)
 {
  $cat_list[$key]['text_pad'] = str_repeat(' - ',$value['level']);
 }
 return juju_job_importer_draw_pull_down_menu($name, $cat_list, $selected,$parameters);
}
endif;
////////
if (!function_exists('juju_job_importer_next_runtime')):
function juju_job_importer_next_runtime($occurrence,$occurrence_type,$start_date)
{
 $end_date   = current_time('mysql');
 $occurrence = (int) $occurrence;
 $year    = substr($start_date,0,4);
 $month   = substr($start_date,5,2);
 $day     = substr($start_date,8,2);
 $hour    = substr($start_date,11,2);
 $minutes = substr($start_date,14,2);
 $seconds = substr($start_date,17,2);
 if(!checkdate ( (int)$month, (int) $day, (int) $year))
 $start_date ='0000-00-00 00:00:00';
 switch($occurrence_type)
 {
  case "hour":
   if($start_date=="" ||$start_date=='0000-00-00 00:00:00')
    $end_date=date("Y-m-d H:i:s",mktime(date('H')+$occurrence,date('i'),date('s'),date('m'),date('d'),date('Y')));
   else
    $end_date=date("Y-m-d H:i:s",mktime($hour+$occurrence,$minutes,$seconds,$month,$day,$year));
   break;
  case "day":
   if($start_date=="" ||$start_date=='0000-00-00 00:00:00')
    $end_date=date("Y-m-d H:i:s",mktime(date('H'),date('i'),date('s'),date('m'),date('d')+$occurrence,date('Y')));
   else
    $end_date=date("Y-m-d H:i:s",mktime($hour,$minutes,$seconds,$month,$day+$occurrence,$year));
   break;
  case "week":
   if($start_date=="" ||$start_date=='0000-00-00 00:00:00')
    $end_date=date("Y-m-d H:i:s",mktime(date('H'),date('i'),date('s'),date('m'),date('d')+($occurrence*7),date('Y')));
   else
    $end_date=date("Y-m-d H:i:s",mktime($hour,$minutes,$seconds,$month,$day+($occurrence*7),$year));
   break;
 }
 return $end_date;
}
endif;
////////
if (!function_exists('juju_job_importer_feed_import')):
function juju_job_importer_feed_import($feed_ids='',$current_time='')
{
 global $wpdb;
 $juju_job_importer_dbtable = $wpdb->base_prefix . "juju_job_importer";	
 $add_whereClause='';
 
 if($feed_ids!='')
 $add_whereClause.=" and  y.feed_id in (".$feed_ids.") ";

 if($current_time!='')
 $add_whereClause.=" and  y.next_activate <= '".$wpdb->escape($current_time)."'";
 
 $query = "select y.* from ".$juju_job_importer_dbtable."  as y  where y.status='active' $add_whereClause   order by y.feed_id";
 $records = $wpdb->get_results($query,'ARRAY_A');
 foreach ($records as $row)
 {
  $blog_id          = wp_filter_nohtml_kses($row['blog_id']);
  $switch = false;
  if (function_exists('is_multisite') && is_multisite())
  {
   if ( get_current_blog_id() != $blog_id ) {
    $switch = true;
    switch_to_blog( $blog_id );
   }
  }
  $publisher_id     = wp_filter_nohtml_kses($row['publisher_id']);
  $feed_keyword     = wp_filter_nohtml_kses($row['feed_keyword']);
  $feed_category    = wp_filter_nohtml_kses($row['feed_category']);
  $feed_location    = wp_filter_nohtml_kses($row['feed_location']);
  $max_feed         = wp_filter_nohtml_kses($row['max_feed']);
  $wp_category      = wp_filter_nohtml_kses($row['wp_category']);
  $occurrence       = wp_filter_nohtml_kses($row['occurrence']);
  $occurrence_type  = wp_filter_nohtml_kses($row['occurrence_type']);
  $publish_status   = wp_filter_nohtml_kses($row['publish_status']);
  $display_template = stripslashes_deep($row['template_format']);
  $feed_id          = wp_filter_nohtml_kses($row['feed_id']);
  $import_items     = wp_filter_nohtml_kses($row['import_items']);
  $import_count     = 0;
  $page=1;
  if($import_items>0)
  {
   $page= floor($import_items / 10);
   if($page==0)
   $page=1;
  }
  $feed_parameter  = array('partnerid'=>$publisher_id,'feed_keyword'=>$feed_keyword,'feed_location'=>$feed_location,'feed_category'=>$feed_category,'sort_by'=>$sort_by,'ipaddress'=>juju_job_importer_user_ip_address(),'useragent'=>'Mozilla/%2F4.0%28Firefox%29');
  $api_url         = jujuImporterApiUrl($feed_parameter);
  $content         = juju_job_importer_readFeeds($api_url);

  $total_records = count($content);
  if($total_records >0 && is_array($content))
  for($i=0;$i<$total_records && $import_count<$max_feed ;$i++)
  {
   $error          = false;
   $item_id        = wp_filter_nohtml_kses($content[$i]['id']);
   $item_title     = wp_filter_nohtml_kses($content[$i]['title']);
   $item_url       = wp_filter_nohtml_kses($content[$i]['url']);
   $item_description = stripslashes($content[$i]['description']);
   $item_city      = wp_filter_nohtml_kses($content[$i]['city']);
   $item_state     = wp_filter_nohtml_kses($content[$i]['state']);
   $item_country   = wp_filter_nohtml_kses($content[$i]['country']);
   $item_zip_code  = wp_filter_nohtml_kses($content[$i]['zip']);
   $item_company   = wp_filter_nohtml_kses($content[$i]['company']);
   
   if(strlen($item_title)<=0)
   {
    $error=true;
   }
   elseif($result1=$wpdb->get_var($wpdb->prepare("select post_title FROM $wpdb->posts 	WHERE post_title = '%s'",$item_title)))
   {
    $error=true;
   }  
   if(!$error)
   {
    $sql_data_array=array();
    $sql_data_array=array(
                          'post_title'    => $item_title,
                          'post_content'  => $item_description,
                          'post_author'   => 1,
                          'post_status' => $publish_status,
                         );
    ////////////////////////////////////////////
   
    
    $template_format=$display_template;
    if($template_format!='')
    {
	 $template_format = nl2br($template_format);
     
     
     if($item_company!='')
      $template_format = str_replace("{job_company}",'<span class="feed_company">'.$item_company.'</span>' ,$template_format);
     else
      $template_format = str_replace("{job_company}",'' ,$template_format);

     if($item_city!='')
      $template_format = str_replace("{job_city}",'<span class="feed_city">'.$item_city.'</span>' ,$template_format);
     else
      $template_format = str_replace("{job_city}",'' ,$template_format);

     if($item_state!='')
      $template_format = str_replace("{job_state}",'<span class="feed_state">'.$item_state.'</span>' ,$template_format);
     else
      $template_format = str_replace("{job_state}",'' ,$template_format);

 	 if($item_country!='')
      $template_format = str_replace("{job_country}",'<span class="feed_country">'.$item_country.'</span>' ,$template_format);
     else
      $template_format = str_replace("{job_country}",'' ,$template_format);


	 if($item_category!='')
      $template_format = str_replace("{job_category}",'<span class="feed_category">'.$item_category.'</span>' ,$template_format);
     else
      $template_format = str_replace("{job_category}",'' ,$template_format);

	 if($item_zip_code!='')
      $template_format = str_replace("{job_zip_code}",'<span class="feed_zip_code">'.$item_zip_code.'</span>' ,$template_format);
     else
      $template_format = str_replace("{job_zip_code}",'' ,$template_format);


	 $template_format = str_replace("{job_description}",$item_description ,$template_format);

     if($item_url!='')
     {
      $template_format = str_replace("{job_detail_url}","<span class='feed_url'>".$item_url."</span>" ,$template_format); 
      $template_format = str_replace("{job_detail_link}","<span class='feed_url_link'><a href='".$item_url."' target='_blank'>".$item_url."</a></span>" ,$template_format); 
      $template_format = str_replace("{job_detail_more_link}","<span class='feed_url'><a href='".$item_url."' target='_blank'>More >></a></span>" ,$template_format); 
     }
    }
    else
    $template_format =$item_description;
    //echo $template_format ;die();
    $sql_data_array['post_content'] = $template_format;
    ///////////////////////////////////////////
    $now=current_time('mysql');
    $sql_data_array['post_date']=$now;
    $sql_data_array['post_date_gmt']=current_time('mysql',1);
    if($post_ID = wp_insert_post($sql_data_array))
    {
	 wp_set_post_terms( $post_ID, $wp_category, 'category');
     update_post_meta($post_ID,'source','juju');	
 	 if($item_city!='')
     update_post_meta($post_ID,'job_city',$item_city);	
	 $import_count=$import_count+1;
     $import_items=$import_items+1;
    }
   }
  }
  $next_activate = juju_job_importer_next_runtime($occurrence,$occurrence_type,current_time('mysql'));
  $query="update ".$juju_job_importer_dbtable." set  last_import='".$import_count."',import_items='".$import_items."',last_active='".current_time('mysql')."',next_activate='".$next_activate."'  where feed_id='".$feed_id."'"; 
  $results = $wpdb->query($query);
  if($switch)
  restore_current_blog();
 }
}
endif;
///////
if (!function_exists('juju_job_importer_readFeeds')):
function juju_job_importer_readFeeds($url)
{
 $juju_content=array();
 if (function_exists('curl_init') )
 {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 60);
  $data = curl_exec($ch);
  curl_close($ch);
  if($data)
  {
   $parsed_xml = simplexml_load_string($data);
   $i=0;
   if($parsed_xml->channel->item)
   foreach($parsed_xml->channel->item as $current)
   {
     $jobkey    =  wp_filter_nohtml_kses($current->jobkey); 
     $jobtitle  =  wp_filter_nohtml_kses($current->title);	 
     $company   =  wp_filter_nohtml_kses($current->company); 
     $zip       =  wp_filter_nohtml_kses($current->zip); 
     $city      =  wp_filter_nohtml_kses($current->city); 
     $state     =  wp_filter_nohtml_kses($current->state); 
     $country   =  wp_filter_nohtml_kses($current->country); 
     $description =  wp_filter_nohtml_kses($current->description); 
     $url       =  wp_filter_nohtml_kses(trim($current->link)); 

     $juju_content[$i]=array(
                        'title'       => $jobtitle,
                        'company'     => $company,
                        'city'        => $city,
                        'state'       => $state,
                        'country'     => $country,
                        'zip'         => $zip,
                        'description' => $description,
                        'url'          => $url,
                       );
     $i++;
    }
   if($parsed_xml->channel->sponsoreditem)
   foreach($parsed_xml->channel->sponsoreditem as $current)
   {
     $jobkey    =  wp_filter_nohtml_kses($current->jobkey); 
     $jobtitle  =  wp_filter_nohtml_kses($current->title);	 
     $company   =  wp_filter_nohtml_kses($current->company); 
     $zip       =  wp_filter_nohtml_kses($current->zip); 
     $city      =  wp_filter_nohtml_kses($current->city); 
     $state     =  wp_filter_nohtml_kses($current->state); 
     $country   =  wp_filter_nohtml_kses($current->country); 
     $description =  wp_filter_nohtml_kses($current->description); 
     $url       =  wp_filter_nohtml_kses(trim($current->link)); 

     $juju_content[$i]=array(
                        'title'       => $jobtitle,
                        'company'     => $company,
                        'city'        => $city,
                        'state'       => $state,
                        'country'     => $country,
                        'zip'         => $zip,
                        'description' => $description,
                        'url'          => $url,
                       );
     $i++;
    }
   }
  }
  //echo "<pre>";  print_r($juju_content);  echo "</pre>";  die();
 return $juju_content;
}
endif;
if (!function_exists('juju_job_importer_user_ip_address')):
function juju_job_importer_user_ip_address() 
{
 if (isset($_SERVER)) 
 {
  if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
  {
   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } 
  elseif (isset($_SERVER['HTTP_CLIENT_IP'])) 
  {
   $ip = $_SERVER['HTTP_CLIENT_IP'];
  } 
  else 
  {
   $ip = $_SERVER['REMOTE_ADDR'];
  }
 } 
 else 
 {
  if (getenv('HTTP_X_FORWARDED_FOR')) 
  {
   $ip = getenv('HTTP_X_FORWARDED_FOR');
  } 
  elseif (getenv('HTTP_CLIENT_IP')) 
  {
   $ip = getenv('HTTP_CLIENT_IP');
  } 
  else 
  {
   $ip = getenv('REMOTE_ADDR');
  }
 }
 return $ip;
}
endif;
if (!function_exists('jujuImporterApiUrl')) :
function jujuImporterApiUrl($parameters=array())
{
 $partnerid      = wp_filter_nohtml_kses($parameters['partnerid']);
 $feed_keyword   = wp_filter_nohtml_kses($parameters['feed_keyword']);
 $feed_location  = wp_filter_nohtml_kses($parameters['feed_location']);
 $feed_category  = wp_filter_nohtml_kses($parameters['feed_category']);
 $sort_by        = wp_filter_nohtml_kses($parameters['sort_by']);
 $ipaddress      = wp_filter_nohtml_kses($parameters['ipaddress']);
 $useragent      = wp_filter_nohtml_kses($parameters['useragent']);
 $occurrence     = wp_filter_nohtml_kses($parameters['occurrence']);
 $occurrence_type= wp_filter_nohtml_kses($parameters['occurrence_type']);
 $page=1;	
 
 $api_url ='http://api.juju.com/jobs';
 $url_parameter=array();
 $url_parameter['partnerid']=$partnerid;

 if($page>0 )
 $url_parameter['page']=$page;
 else
 $url_parameter['page']=1;

 if($feed_keyword!='')
 $url_parameter['k']=trim($feed_keyword);

 if($feed_location!='')
 $url_parameter['l']=trim($feed_location);

 if($feed_category!='')
 $url_parameter['c']=trim($feed_category);

 if($useragent!='')
 $url_parameter['useragent']=trim($useragent);

 if($ipaddress!='')
 $url_parameter['ipaddress']=trim($ipaddress);

 $url_parameter['highlight']=0;

 foreach ($url_parameter as $name=>$value) 
 {
  $name = str_replace("%7E", "~", rawurlencode($name));
  $value = str_replace("%7E", "~", rawurlencode($value));
  $juju_query[] = $name."=".$value;
 }
 $url_parameter= implode("&", $juju_query);
 $api_url  = $api_url."?".$url_parameter;
 return $api_url; 
}
endif;
if (!function_exists('jujuCategoryDropDown')):
function jujuCategoryDropDown($name='category',$parameters='',$selected="",$header="",$header_value="")
{
 $cat_list =array();
 if($header!='')
 $cat_list[0] =array('id'=>$header_value,'text'=>$header);
 $cat_list= array_merge($cat_list,getJujuCategory());
 if($hierarchical)
 foreach($cat_list as $key =>$value)
 {
  $cat_list[$key]['text_pad'] = str_repeat(' - ',$value['level']);
 }
 return juju_job_importer_draw_pull_down_menu($name, $cat_list, $selected,$parameters);
}
endif;

if (!function_exists('getJujuCategory')) :
function getJujuCategory()
{
 return array(
	 array('id'=>'accounting','text'=>'accounting'),
	 array('id'=>'administrative-clerical','text'=>'administrative-clerical'),
	 array('id'=>'advertising-marketing-public-relations','text'=>'advertising-marketing-public-relations'),
	 array('id'=>'banking-mortgage','text'=>'banking-mortgage'),
	 array('id'=>'biotech-pharmaceutical','text'=>'biotech-pharmaceutical'),
	 array('id'=>'construction','text'=>'construction'),
	 array('id'=>'customer-service','text'=>'customer-service'),
	 array('id'=>'defense','text'=>'defense'),
	 array('id'=>'education','text'=>'education'),
	 array('id'=>'entry-level','text'=>'entry-level'),
	 array('id'=>'executive','text'=>'executive'),
	 array('id'=>'finance','text'=>'finance'),
	 array('id'=>'government','text'=>'government'),
	 array('id'=>'health-care','text'=>'health-care'),
	 array('id'=>'hospitality','text'=>'hospitality'),
	 array('id'=>'human-resources','text'=>'human-resources'),
	 array('id'=>'insurance','text'=>'insurance'),
	 array('id'=>'legal','text'=>'legal'),
	 array('id'=>'logistics-transportation','text'=>'logistics-transportation'),
	 array('id'=>'manufacturing-industrial','text'=>'manufacturing-industrial'),
	 array('id'=>'media','text'=>'media'),
	 array('id'=>'non-profit','text'=>'non-profit'),
	 array('id'=>'public-safety-security','text'=>'public-safety-security'),
	 array('id'=>'real-estate','text'=>'real-estate'),
	 array('id'=>'retail','text'=>'retail'),
	 array('id'=>'sales','text'=>'sales'),
	 array('id'=>'software-it','text'=>'software-it')
	 );
}
endif;
?>