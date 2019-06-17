<div class="side-nav">
    <div class="side-nav-inner">
        <div class="side-nav-logo">
            <a href="index.html">
                <div class="logo logo-dark" style="background-image: url('<?php echo base_url();?>new_assets/images/new_logo/logo.png')"></div>
                <div class="logo logo-white" style="background-image: url('<?php echo base_url();?>new_assets/images/new_logo/logo-white.png')"></div>
            </a>
            <div class="mobile-toggle side-nav-toggle">
                <a href="">
                    <i class="ti-arrow-circle-left"></i>
                </a>
            </div>
        </div>
        <ul class="side-nav-menu scrollable">
        <?php
          $all_links=array();

          foreach($menus as $single_menu) 
          {
              if($single_menu['id']==2 && $this->config->item('backup_mode')==='0' && $this->session->userdata('user_type')=='Member') continue; // static condition not to show app settings to memeber if backup mode = 0              
              
              if($single_menu['serial'] == '14' && $this->session->userdata('license_type') != 'double') continue;
              if($single_menu['serial']=='14' && $this->config->item('enable_support')=='0' && $this->session->userdata('user_type')=='Member') continue; // if support desk enable in config is '0'

              $only_admin = $single_menu['only_admin'];
              $only_member = $single_menu['only_member']; 
              $module_access = explode(',', $single_menu['module_access']);
              $module_access = array_filter($module_access);

              $extraText = $single_menu['add_ons_id']!='0' && $this->is_demo=='1' ? 'Addon' : '' ;

              if($single_menu['is_external']=='1') $site_url1=""; else $site_url1=site_url(); // if external link then no need to add site_url()
              if($single_menu['is_external']=='1') $parent_newtab=" target='_BLANK'"; else $parent_newtab=''; // if external link then open in new tab

              array_push($all_links, $site_url1.$single_menu['url']);  

              if(isset($menu_child_1_map[$single_menu['id']]) && count($menu_child_1_map[$single_menu['id']]) > 0)
              { 
                $menu_html = "<li class='nav-item dropdown'> <a class='dropdown-toggle' href='javascript:void(0);'> <span class='icon-holder'> <i class='".$single_menu['icon']."'></i> </span> <span class='title'>". $this->lang->line($single_menu['name']).$extraText."</span> <span class='arrow'><i class='ti-angle-right'></i> </span> </a> <ul class='dropdown-menu'>";
                foreach($menu_child_1_map[$single_menu['id']] as $single_child_menu)
                {
                    $only_admin2 = $single_child_menu['only_admin'];
                    $only_member2 = $single_child_menu['only_member']; 

                    if($single_child_menu['url'] == 'admin/activity_log' && $this->session->userdata('license_type') != 'double') continue;

                    //if($single_child_menu['url']=="messenger_bot/facebook_config" && $bot_backup_mode=='0' && $this->session->userdata('user_type')=='Member') continue; // static condition not to show app settings to memeber if backup mode = 0

                    if($single_child_menu['url']=="pageresponse/facebook_config" && $pageresponse_backup_mode=='0' && $this->session->userdata('user_type')=='Member') continue; // static condition not to show app settings to memeber if backup mode = 0

                    if($single_child_menu['url']=="instagram_reply/facebook_config" && $pageresponse_backup_mode=='0' && $this->session->userdata('user_type')=='Member')continue; // static condition not to show app settings to memeber if backup mode = 0

                    if(($only_admin2 == '1' && $this->session->userdata('user_type') == 'Member') || ($only_member2 == '1' && $this->session->userdata('user_type') == 'Admin')) 
                    continue;

                    if($this->session->userdata('user_type')=='Member' && $single_child_menu['url'] == 'messenger_bot/cron_job') continue;
                    if($this->session->userdata('user_type')=='Member' && $single_child_menu['url'] == 'messenger_bot/configuration') continue;  

                    if($single_child_menu['is_external']=='1') $site_url2=""; else $site_url2=site_url(); // if external link then no need to add site_url()
                    if($single_child_menu['is_external']=='1') $child_newtab=" target='_BLANK'"; else $child_newtab=''; // if external link then open in new tab

                    array_push($all_links, $site_url2.$single_child_menu['url']);

                    if(isset($menu_child_2_map[$single_child_menu['id']]) && count($menu_child_2_map[$single_child_menu['id']]) > 0)
                    { 
                    $menu_html .= "<li class='nav-item dropdown'><a class='dropdown-toggle' href='javascript:void(0);'> <span class='icon-holder'> <i class=".$single_child_menu['icon']."></i> </span> <span class='title'>". $this->lang->line($single_child_menu['name'])."</span> <span class='arrow'><i class='ti-angle-right'></i> </span> </a> <ul class='dropdown-menu'>";

                      foreach($menu_child_2_map[$single_child_menu['id']] as $single_child_menu_2)
                      { 
                        $only_admin3 = $single_child_menu_2['only_admin'];
                        $only_member3 = $single_child_menu_2['only_member'];
                        if(($only_admin3 == '1' && $this->session->userdata('user_type') == 'Member') || ($only_member3 == '1' && $this->session->userdata('user_type') == 'Admin'))
                          continue;
                        if($single_child_menu_2['is_external']=='1') $site_url3=""; else $site_url3=site_url(); // if external link then no need to add site_url()
                        if($single_child_menu_2['is_external']=='1') $child2_newtab=" target='_BLANK'"; else $child2_newtab=''; // if external link then open in new tab   

                        $menu_html .= "<li class='nav-item'><a href='".$site_url2.$single_child_menu_2['url']."'><span class='icon-holder'> <i class=".$single_child_menu_2['icon']."></i> </span>". $this->lang->line($single_child_menu_2['name']). "</a>";
                        array_push($all_links, $site_url3.$single_child_menu_2['url']);
                      }
                      $menu_html .= "</ul>";
                        $menu_html .= "</li>";
         
                    } else {
                        $menu_html .= "<li class='nav-item'><a href='".$site_url2.$single_child_menu['url']."'><span class='icon-holder'> <i class=".$single_child_menu['icon']."></i> </span>".$this->lang->line($single_child_menu['name']). "</a>";
                    }
                }
                    $menu_html .= "</ul>";
                $menu_html .= "</li>";

              }
              else
              {
                $menu_html = "<li class='nav-item'> <a  href='".$site_url1.$single_menu['url']."'> <span class='icon-holder'> <i class=".$single_menu['icon']."></i> </span> <span class='title'>". $this->lang->line($single_menu['name']).$extraText."</span> </a>";
              }

              $menu_html .= "</li>";
              if($only_admin == '1') 
              {
                if($this->session->userdata('user_type') == 'Admin') 
                echo $menu_html;
              }
              else if($only_member == '1') 
              {
                if($this->session->userdata('user_type') == 'Member') 
                echo $menu_html;
              } 
              else 
              {
                if($this->session->userdata("user_type")=="Admin" || empty($module_access) || count(array_intersect($this->module_access, $module_access))>0 ) 
                echo $menu_html;
              } 
        ?>

        <?php 
          }
        ?>

     <li style="margin-bottom:200px">&nbsp;</li>
   </ul>
    </div>
</div>

<?php 
$all_links=array_unique($all_links);
$unsetkey = array_search (base_url().'#', $all_links); 
if($unsetkey!=FALSE)
unset($all_links[$unsetkey]); // removing links without a real url

/* 
links that are not in database [custom link = sibebar parent]
No need to add a custom link if it's parent is controller/index
*/
$custom_links=array
(
  base_url("admin/change_user_password")=>base_url("admin/user_management"),
  base_url("admin/user_log")=>base_url("admin/user_management"),
  base_url("payment/add_package")=>base_url("payment/package_settings"),
  base_url("payment/update_package")=>base_url("payment/package_settings"),
  base_url("payment/details_package")=>base_url("payment/package_settings"),
  base_url("announcement/add")=>base_url("announcement/full_list"),
  base_url("announcement/edit")=>base_url("announcement/full_list"),
  base_url("announcement/details")=>base_url("announcement/full_list"),
  base_url("facebook_ex_import_lead/update_contact")=>base_url("facebook_ex_import_lead/contact_list"),
  base_url("facebook_ex_campaign/edit_multipage_campaign")=>base_url("facebook_ex_campaign/campaign_report"),
  base_url("facebook_ex_campaign/edit_multigroup_campaign")=>base_url("facebook_ex_campaign/campaign_report"),
  base_url("facebook_ex_campaign/edit_custom_campaign")=>base_url("facebook_ex_campaign/campaign_report"),
  base_url("ultrapost/text_image_link_video_poster")=>base_url("ultrapost/text_image_link_video"),
  base_url("ultrapost/text_image_link_video_edit_auto_post")=>base_url("ultrapost/text_image_link_video"),
  base_url("ultrapost/offer_poster")=>base_url("ultrapost/offer_post"),
  base_url("ultrapost/offer_post_report")=>base_url("ultrapost/offer_post"),
  base_url("ultrapost/edit_offer_post_campaign")=>base_url("ultrapost/offer_post"),
  base_url("ultrapost/edit_cta_post")=>base_url("ultrapost/cta_post"),
  base_url("ultrapost/cta_poster")=>base_url("ultrapost/cta_post"),
  base_url("ultrapost/carousel_slider_poster")=>base_url("ultrapost/carousel_slider_post"),
  base_url("ultrapost/edit_carousel_slider")=>base_url("ultrapost/carousel_slider_post"),
  base_url("messenger_bot/fb_login")=>base_url("messenger_bot/facebook_config"),
  base_url("messenger_bot/bot_settings")=>base_url("messenger_bot/bot_list"),
  base_url("messenger_bot/edit_bot")=>base_url("messenger_bot/bot_list"),
  base_url("messenger_bot/persistent_menu_list")=>base_url("messenger_bot/bot_list"),
  base_url("messenger_bot/create_persistent_menu")=>base_url("messenger_bot/bot_list"),
  base_url("messenger_bot/edit_template")=>base_url("messenger_bot/template_manager"),
  base_url("pageresponse/fb_login")=>base_url("pageresponse/facebook_config"),
  base_url("pageresponse/page_like_share_report")=>base_url("pageresponse/like_share_list"),
  base_url("pageresponse/page_response_report")=>base_url("pageresponse/page_list"),
  base_url("commenttagmachine/edit_bulk_tag_campaign")=>base_url("commenttagmachine/bulk_tag_campaign_list"),
  base_url("commenttagmachine/edit_bulk_comment_reply_campaign")=>base_url("commenttagmachine/bulk_comment_reply_campaign_list"),
  base_url("messenger_engagement/checkbox_plugin_add")=>base_url("messenger_engagement/checkbox_plugin_list"),
  base_url("messenger_engagement/send_to_messenger_add")=>base_url("messenger_engagement/send_to_messenger_list"),
  base_url("messenger_engagement/mme_link_add")=>base_url("messenger_engagement/mme_link_list"),
  base_url("messenger_engagement/messenger_codes_add")=>base_url("messenger_engagement/messenger_codes_list"),
  base_url("messenger_engagement/add_domain")=>base_url("messenger_engagement/plugin_list"),
  base_url("messenger_engagement/checkbox_plugin_edit")=>base_url("messenger_engagement/checkbox_plugin_list"),
  base_url("messenger_engagement/send_to_messenger_edit")=>base_url("messenger_engagement/send_to_messenger_list"),
  base_url("messenger_engagement/mme_link_edit")=>base_url("messenger_engagement/mme_link_list"),
  base_url("messenger_engagement/messenger_codes_edit")=>base_url("messenger_engagement/messenger_codes_list"),
  base_url("messenger_engagement/edit_domain")=>base_url("messenger_engagement/plugin_list"),
  base_url("messenger_broadcaster/update_contact")=>base_url("messenger_broadcaster/contact_list"),
  base_url("messenger_broadcaster/quick_bulk_broadcast_add")=>base_url("messenger_broadcaster/quick_bulk_broadcast_report"),
  base_url("messenger_broadcaster/quick_bulk_broadcast_edit")=>base_url("messenger_broadcaster/quick_bulk_broadcast_report"),
  base_url("messenger_broadcaster/subscriber_bulk_broadcast_add")=>base_url("messenger_broadcaster/subscriber_bulk_broadcast_report"),
  base_url("messenger_broadcaster/subscriber_bulk_broadcast_edit")=>base_url("messenger_broadcaster/subscriber_bulk_broadcast_report"),
  base_url("drip_messaging/edit_campaign")=>base_url("drip_messaging/messaging_report"),
  base_url("instagram_reply/fb_login")=>base_url("instagram_reply/facebook_config"),
  base_url("instagram_reply/user_insight")=>base_url("instagram_reply/account_import"),
  base_url("instagram_reply/full_account_report")=>base_url("instagram_reply/autoreply"),
  base_url("instagram_reply/mentions_report")=>base_url("instagram_reply/autoreply"),
  base_url("instagram_reply/auto_reply_report")=>base_url("instagram_reply/autoreply"),
  base_url("instagram_reply/business_discovery_dashboard")=>base_url("instagram_reply/business_accounts"),
  base_url("instagram_reply/hash_tag_report")=>base_url("instagram_reply/hash_tag_search"),
  base_url("comboposter/add_pinterest_settings/add")=>base_url("comboposter/pinterest_settings"),
  base_url("comboposter/update_pinterest_config")=>base_url("comboposter/pinterest_settings"),
  base_url("comboposter/pinterest_login_button")=>base_url("comboposter/pinterest_settings"),
  base_url("vidcasterlive/fb_login")=>base_url("vidcasterlive/facebook_rx_config"),
  base_url("vidcasterlive/clone_live_scheduler")=>base_url("vidcasterlive/live_scheduler_list"),
  base_url("vidcasterlive/edit_live_scheduler")=>base_url("vidcasterlive/live_scheduler_list"),
  base_url("vidcasterlive/video_analytics_display")=>base_url("vidcasterlive/live_scheduler_list"),
  base_url("simplesupport/create_category")=>base_url("simplesupport/support_category"),
  base_url("simplesupport/edit_support_category")=>base_url("simplesupport/support_category"),
  base_url("simplesupport/reply_support")=>base_url("simplesupport/all_ticket"),
  base_url("messenger_bot_analytics/result")=>base_url("messenger_bot/bot_list"),
  base_url("addons/upload")=>base_url("addons/lists")
);
$custom_links_assoc_str="{";
$loop=0;
foreach ($custom_links as $key => $value) 
{
  $loop++;
  array_push($all_links, $key); // adding custom urls in all urls array

  /* making associative link -> parent array for js, js dont support special chars */
  $custom_links_assoc_str.=str_replace(array('/',':','-','.'), array('FORWARDSLASHES','COLONS','DASHES','DOTS'), $key).":'".$value."'";
  if($loop!=count($custom_links)) $custom_links_assoc_str.=',';
}
$custom_links_assoc_str.="}";
// echo "<pre style='padding-left:300px;'>";
// print_r($all_links);
// echo "</pre>"; 
?>

<script type="text/javascript">

  var all_links_JS = [<?php echo '"'.implode('","', $all_links).'"' ?>]; // all urls includes database & custom urls
  var custom_links_JS= [<?php echo '"'.implode('","', array_keys($custom_links)).'"' ?>]; // only custom urls
  var custom_links_assoc_JS = <?php echo $custom_links_assoc_str?>; // custom urls associative array link -> parent

  var sideBarURL = window.location;
  sideBarURL=String(sideBarURL).trim();
  sideBarURL=sideBarURL.replace('#_=_',''); // redirct from facebook login return extra chars with url

  function removeUrlLastPart(the_url)   // function that remove last segment of a url
  {
      var theurl = String(the_url).split('/');
      theurl.pop();      
      var answer=theurl.join('/');
      return answer;
  }

  // get parent url of a custom url
  function matchCustomUrl(find)
  {
    var parentUrl='';
    var tempu1=find.replace(/\//g, 'FORWARDSLASHES'); // decoding special chars that was encoded to make js array
    tempu1=tempu1.replace(/:/g, 'COLONS');
    tempu1=tempu1.replace(/-/g, 'DASHES');
    tempu1=tempu1.replace(/\./g, 'DOTS');

    if(typeof(custom_links_assoc_JS[tempu1])!=='undefined')
    parentUrl=custom_links_assoc_JS[tempu1]; // getting parent value of custom link

    return parentUrl;
  }

  if(jQuery.inArray(sideBarURL, custom_links_JS) !== -1) // if the current link match custom urls
  {    
    sideBarURL=matchCustomUrl(sideBarURL);
  } 
  else if(jQuery.inArray(sideBarURL, all_links_JS) !== -1) // if the current link match known urls, this check is done later becuase all_links_JS also contains custom urls
  {
     sideBarURL=sideBarURL;
  }
  else // url does not match any of known urls
  {  
    var remove_times=1;
    var temp_URL=sideBarURL;
    var temp_URL2="";
    var tempu2="";
    while(true) // trying to match known urls by remove last part of url or adding /index at the last
    {
      temp_URL=removeUrlLastPart(temp_URL); // url may match after removing last
      temp_URL2=temp_URL+'/index'; // url may match after removing last part and adding /index

      if(jQuery.inArray(temp_URL, custom_links_JS) !== -1) // trimmed url match custom urls
      {
        sideBarURL=matchCustomUrl(temp_URL);
        break;
      }
      else if(jQuery.inArray(temp_URL, all_links_JS) !== -1) //trimmed url match known links
      {
        sideBarURL=temp_URL;
        break;
      }
      else // trimmed url does not match known urls, lets try extending url by adding /index
      {
        if(jQuery.inArray(temp_URL2, custom_links_JS) !== -1) // extended url match custom urls
        {
          sideBarURL=matchCustomUrl(temp_URL2);
          break;
        }
        else if(jQuery.inArray(temp_URL2, all_links_JS) !== -1)  // extended url match known urls
        {
          sideBarURL=temp_URL2;
          break;
        }
      }
      remove_times++;
      if(temp_URL.trim()=="") break;
    }    
  }
alert(111)
  $('.nav-item > a').filter(function() {
     return this.href == sideBarURL;
  }).parent().addClass('active');
  $('ul.dropdown-menu a').filter(function() {
     return this.href == sideBarURL;
  }).parentsUntil(".nav-item").addClass('active');
</script>