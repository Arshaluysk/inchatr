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

                        $menu_html .= "<li class='nav-item'><a href='".$site_url2.$single_child_menu['url']."'><span class='icon-holder'> <i class=".$single_child_menu_2['icon']."></i> </span>". $this->lang->line($single_child_menu_2['name']). "</a>";
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