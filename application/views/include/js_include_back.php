
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>plugins/colorbox/jquery.colorbox.js"></script>

<script>
    	var $colorbox = $.noConflict();
		  $colorbox(".image_preview_colorbox").colorbox();
		 // $colorbox(".youtube").colorbox({iframe:true, innerWidth:300, innerHeight:400});
</script>



<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<!-- jEasy Grid -->
<!-- ================ -->
<script type="text/javascript" src="<?php echo base_url();?>plugins/grid/jquery.easyui.min.js"></script>
<!-- Load Language -->
<?php $jui_language_name=$this->language;?>
<script type="text/javascript" src="<?php echo base_url();?>plugins/grid/locale/<?php echo $jui_language_name;?>.js"></script>


<!--Multiselect plugin-->
<script type="text/javascript" src="<?php echo base_url();?>plugins/multiselect/multiple-select.js"></script>



<!--Jquery Date Time Picker -->

<script type="text/javascript" src="<?php echo base_url();?>plugins/datetimepickerjquery/jquery.datetimepicker.js"></script>
<!-- <script type="text/javascript" src="<?php echo base_url();?>plugins/datetimepickerjquery/jquery.datetimepicker.full.min.js"></script> -->

<!-- Emoji Library-->
<script src="<?php echo base_url();?>plugins/emoji/dist/emojionearea.js" type="text/javascript"></script>

<!-- RTL Support -->
<?php 
// if($this->config->item('language')=="arabic") 
if($this->is_rtl) 
  { ?>    
    <link href="<?php echo base_url();?>plugins/grid/easyui-rtl.css" rel="stylesheet" type="text/css" /> 
    <script type="text/javascript" src="<?php echo base_url();?>plugins/grid/easyui-rtl.js"></script>
  <?php
  } ?>
<!-- ================ -->
<!-- jEasy Grid -->


<script>
    	var $j= jQuery.noConflict();
</script> 

<!-- jQuery 2.1.4 -->
<script src="<?php echo base_url();?>plugins/jQuery/jQuery-2.1.4.min.js"></script>

<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script type="text/javascript">
$.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.2 JS -->
<script src="<?php echo base_url();?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="<?php echo base_url();?>plugins/morris/morris.min.js" type="text/javascript"></script>

<!-- Sparkline -->
<script src="<?php echo base_url();?>plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
<!-- jvectormap -->
<script src="<?php echo base_url();?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo base_url();?>plugins/knob/jquery.knob.js" type="text/javascript"></script>

<!-- char.js -->
<script src="<?php echo base_url();?>plugins/chartjs/Chart.js" type="text/javascript"></script>

<!-- js for ajax multiselect -->
<link rel="stylesheet" href="<?php echo base_url();?>plugins/multiselect_tokenize/jquery.tokenize.css" type="text/css" />
<script src="<?php echo base_url();?>plugins/multiselect_tokenize/jquery.tokenize.js" type="text/javascript"></script>

<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>


<script src="<?php echo base_url();?>plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- datepicker -->
<script src="<?php echo base_url();?>plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo base_url();?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
<!-- Slimscroll -->
<script src="<?php echo base_url();?>plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- FastClick -->
<script src="<?php echo base_url();?>plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url();?>js/app.min.js" type="text/javascript"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo base_url();?>js/pages/dashboard.js" type="text/javascript"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url();?>js/demo.js" type="text/javascript"></script>
<!-- added 20/9/2015 -->
<!-- <script src="<?php echo base_url();?>plugins/ckeditor/ckeditor.js" type="text/javascript"></script> -->
<script src="<?php echo base_url();?>js/common.js" type="text/javascript"></script>

<script src="<?php echo base_url();?>plugins/xregexp/xregexp.js" type="text/javascript"></script>


<!-- scrollbar -->
<script src="<?php echo base_url();?>plugins/scrollbar/jquery.mCustomScrollbar.concat.min.js" type="text/javascript"></script>


<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> -->

<!-- for tab -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> -->
<!--<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-ui.min.js"></script>-->



<!--Table Sorter-->

<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>-->

<link rel="stylesheet" href="<?php echo base_url();?>plugins/datatables/jquery.dataTables.css" type="text/css" />
<script src="<?php echo base_url();?>plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>

<link rel="stylesheet" href="<?php echo base_url();?>plugins/datatables/dataTables.bootstrap.css" type="text/css" />
<script src="<?php echo base_url();?>plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

<!-- searchable select single -->
<!-- https://www.phpgang.com/how-to-create-html-select-box-with-searching-support-using-jquery_648.html -->
<!-- <link href="<?php echo base_url('plugins/select_search/select2.css');?>" rel="stylesheet"/>
<script src="<?php echo base_url('plugins/select_search/select2.js');?>"></script> -->

<script src="<?php echo base_url();?>plugins/summernote/summernote.js" type="text/javascript"></script>


<!-- alerfify https://alertifyjs.com/guide.html -->
<script src="<?php echo base_url('plugins/alertifyjs/alertify.min.js')?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/alertifyjs/css/alertify.min.css')?>" />
<link rel="stylesheet" href="<?php echo base_url('plugins/alertifyjs/css/themes/default.min.css')?>" />
<!-- alerfify -->

<!-- beautiful select css is loaded in css include back for priority issue--> 
<script src="<?php echo base_url('plugins/select_search/select2.js')?>"></script>

<!-- progress scroll -->
<script src="<?php echo base_url();?>plugins/scrollbar/prognroll.js"></script>



<script>
// grid formatter
function status(value,row,index)
{   
    if(value=="1") return "<label class='label label-light'><i class='fa fa-check-circle green'></i> " + "<?php echo $this->lang->line('Active');?>" + "</label>";            
    else return "<label class='label label-light'>" + "<i class='fa fa-remove red'></i> <?php echo $this->lang->line('Inactive');?>"  + "</label>";            
}   
function yes_no(value,row,index)
{   
    if(value=="1" || value=="yes") return "<label class='label label-light'><i class='fa fa-check-circle green'></i> " + "<?php echo $this->lang->line('Yes');?>" + "</label>";           
    else return "<label class='label label-light'>" + "<i class='fa fa-remove red'></i> <?php echo $this->lang->line('No');?>" + "</label>";            
}   

function yes_no_email(value,row,index)
{   
    if(value=="1") return "<label class='label label-light'><i class='fa fa-check-circle green'></i> " + "<?php echo $this->lang->line('Successful');?>" + "</label>";            
    else return "<label class='label label-light'><i class='fa fa-remove red'></i> " + "<?php echo $this->lang->line('Failed');?>" + "</label>";            
}   
function ucwords_js (str) 
{
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}
function yes_no_sms(value,row,index)
{   
    if(value=="1" || value=='Sent') return "<label class='label label-light'><i class='fa fa-check-circle green'></i> " + "<?php echo $this->lang->line('Successful');?>" + "</label>";            
    else 
    {
      if(value=="" || value=="0") value="Unknown Cause";
      value=ucwords_js(value);
      return "<label class='label label-light' title='"+value+"' style='cursor:pointer;'><i class='fa fa-remove red'></i> " + "<?php echo $this->lang->line('Failed');?>" + "</label>";   
    }        
}  

function attachment_download(value,row,index)
{   
    if (typeof(row)==='undefined') row = "";
    if (typeof(index)==='undefined') index = "";

    if(value && value!="0") return "<a class='label label-success' href='<?php echo base_url();?>home/attachment_downloader/"+value+"' target='_BLANK' title='Download'>"+value+"</a>";
    else return  "<label class='label label-warning' title='No attachment'>" + "<?php echo $this->lang->line('No attachment');?>" + "</label>";      
}  


function message_formatter(value,row,index)
{
    var newval;
    var recval=String(row.message);
    if(recval.length>=33) 
    {   
        newval=recval.substring(0, 30);
        newval=newval+"...";
    }
    else newval=recval;
    return newval;
}
//  grid formatter 


// Code that uses other library's $ can follow here.
$j("document").ready(function(){

  $("#auto_reply_template,#email_sending_option,#language,#theme,#theme_front,#time_zone,#display_landing_page,#force_https,#autoreply_renew_access,#backup_mode,#developer_access,#field-status,#tb select,#tb2 select,#subscribed,#cta_type,#add_new_domain_page,#enbale_type_on,.social_sharing_design select").select2(); 

  //crud birthday schedule
  var temp="<?php echo $this->uri->segment(2);?>";
});

function goBack(link,insert_or_update,add_base_url) //used to go back to list as crud
{
  
  // insert_or_update does not have any effect from v6.0
  if (typeof(insert_or_update)==='undefined') insert_or_update = 0;
  if (typeof(add_base_url)==='undefined') add_base_url = 1;

  var mes='';
  // if(insert_or_update==0)
  // mes="<?php echo $this->lang->line('the data you had insert may not be saved.\\nare you sure you want to go back to list?') ?>";
  // else
  mes="<?php echo $this->lang->line('the data you had change may not be saved.\\nare you sure you want to go back to list?') ?>";
  alertify.confirm('<?php echo $this->lang->line("are you sure");?>',mes, 
  function(){ 
    if(add_base_url==1)
    link="<?php echo site_url();?>"+link;
    window.location.assign(link);
  },
  function(){     
  });
}

function delete_crud_row(link) // crud row delete function has been moved here to implement alertify
{
  var mes='<?php echo $this->lang->line("are you sure that you want to delete this record?");?>';  
  alertify.confirm('<?php echo $this->lang->line("are you sure");?>',mes, 
  function(){ 
    window.location.href = link;
  },
  function(){     
  });
}

// grid delete function is now handles here to implement alertify, it also used for some other places to confirm


// Code that uses other library's $ can follow here.


$j('document').ready(function() {
 // replace admin and member string to 
 var replace_dropdown='<select class="chosen-select" name="user_type" id="field-user_type"><option value=""></option><option value="Member">'+'<?php echo $this->lang->line("member user"); ?>'+'</option><option value="Admin">'+'<?php echo $this->lang->line("admin user"); ?>'+'</option></select>';  
 $("#user_type_input_box").html(replace_dropdown);
 
});

</script>


<script type="text/javascript">
  $j(document).ready(function() {
    $(document.body).on('click','.are_you_sure',function(e){
      e.preventDefault();
      var link = $(this).attr("href");
      var mes='<?php echo $this->lang->line("are you sure that you want to delete this record?");?>';  
      alertify.confirm('<?php echo $this->lang->line("are you sure");?>',mes, 
      function(){ 
        window.location.href = link;
      },
      function(){     
      });
    });
  });
</script>

<script type="text/javascript">
  $j(document).ready(function() {
    $("#language_change").change(function(){
      var language=$(this).val();
      $("#language_label").html("Loading Language...");
      $.ajax({
        url: '<?php echo site_url("home/language_changer");?>',
        type: 'POST',
        data: {language:language},
        success:function(response){
            $("#language_label").html("Language");
            location.reload(); 
        }
      })
      
    });
  });
</script>


<script type="text/javascript">
  $j(document).ready(function() {
    $("#fb_rx_account_switch").change(function(){
      var id=$(this).val();
      $.ajax({
        url: '<?php echo site_url("facebook_rx_account_import/fb_rx_account_switch");?>',
        type: 'POST',
        data: {id:id},
        success:function(response){
            location.reload(); 
        }
      })
      
    });
  });
</script>

<script type="text/javascript">
  $j(document).ready(function() {
    $("#vidcaster_fb_rx_account_switch").change(function(){
      var id=$(this).val();
      $.ajax({
        url: '<?php echo site_url("vidcasterlive/fb_rx_account_switch");?>',
        type: 'POST',
        data: {id:id},
        success:function(response){
            location.reload(); 
        }
      })
      
    });
  });
</script>


<?php if($this->is_rtl){ ?>

<script type="text/javascript">

$j('document').ready(function() {
  $('*').each(function() {  
      if(!isRTL($(this).text())){
       $(this).addClass('ltr');
    }
  });
});
  
  
  function isInt(value) {

      var er = /^-?[0-9]+$/;
  
      return er.test(value);
  }

  
  function isRTL(str) {
  
      var isArabic = XRegExp('[\\p{Arabic}]');
      var partArabic = 0;
      var rtlIndex = 0;
    
    /**This for check if any of the text is numberic then don't make it RTL***/
    var is_int=0;
    
      var isRTL = false;
  
      for(i=0;i<str.length;i++){
          if(isArabic.test(str[i]))
              partArabic++;
        
      if(isInt(str[i])){
        is_int=1;
      }
        
      }
    
    /**if any character is arabic and also check if no integer there , then it is RTL**/
      if(partArabic > 0 && is_int==0) {
          isRTL = true;
      }
      return isRTL;
  }
  
</script>

<?php  } ?>



<!-- BOSS FILE MANAGER CODE STARTS -->
<script type="text/javascript">
  var base_url = "<?php echo base_url(); ?>";
  $j("document").ready(function(){   

      $(document.body).on('click','.boss-file-manager-btn',function(){  

        var modal_id = $(this).attr("modal-id");

        $("#"+modal_id+" .modal-body").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
        $("#"+modal_id).modal();

        var loc = $(this).attr("data-location");
        var preview = $(this).attr("data-preview");
        var allowed = $(this).attr("data-allowed");     
        var data_id = $(this).attr("id"); 
        var src = $(this).attr("data-fill");
        var data_type = $(this).attr("data-type");


        if(src=="" || data_type=="" || data_id=="" || loc=="") 
        {
          alert("File Manager was not configured properly");
          return;
        }  

            var video_loc= "";
            if(data_type=="video")
            {
                video_loc = $(this).attr("video-location");               
                if(video_loc=="") 
                {
                    alert("File Manager was not configured properly");
                    return;
                }
            }   

         $.ajax({
            url:base_url+'home/load_files',
            type:'POST',
            data:{loc:loc,allowed:allowed,data_id:data_id,video_loc:video_loc,data_type:data_type},
            success:function(response){
              $("#"+modal_id+" .modal-body").html(response);      
            }        
          }); 
      });


        $(document.body).on('click','.delete-boss-file',function(){ 

            var file_path = $(this).prev().attr("data-path");
            var thumb_path = $(this).prev().attr("thumb-path");
            $(this).parent().hide();                 

            alertify.confirm('<?php echo $this->lang->line("confirm");?>','<?php echo $this->lang->line("are you sure");?>', 
            function(){ 
               $.ajax({
                url:base_url+'home/delete_files',
                type:'POST',
                data:{file_path:file_path,thumb_path:thumb_path},
                success:function(response){
                }        
              });
            },
            function(){     
            });


        });

      $(document.body).on('click','.BossFileManager',function(){ 
        var btn_id= $(this).attr("data-id");        
        if(btn_id=="") 
        {
          alert("File Manager was not configured properly");
          return;
        }

        var src = $("#"+btn_id).attr("data-fill");
        var data_put_full_path = $("#"+btn_id).attr("data-put-full-path");
        var data_preview_only_message = $("#"+btn_id).attr("data-preview-only-message");
        var data_type = $("#"+btn_id).attr("data-type");

        if(src=="" || data_type=="") 
        {
          alert("File Manager was not configured properly");
          return;
        }

            var video_loc= "";
            if(data_type=="video")
            {
                video_loc = $("#"+btn_id).attr("video-location");
                if(video_loc=="") 
                {
                    alert("File Manager was not configured properly");
                    return;
                }
            }

        var file_path = $(this).attr("data-path");
        file_path = base_url+file_path;
        var file_name_only= $(this).attr("only-name");

        if(data_put_full_path=="1")
        $("#"+src).val(file_path);
        else $("#"+src).val(file_name_only);


        var height='auto';
        var width='100%';
        height = $("#"+btn_id).attr("data-preview-height");
        width = $("#"+btn_id).attr("data-preview-width");

        var preview = $("#"+btn_id).attr("data-preview");
        
        if(typeof(preview)==='undefined')
        {
          alert("success but not displayed");
        }
        else
        {
          var preview_html="<div class='alert alert-success text-center' style='margin:10px 0;'><i class='fa fa-check-circle'></i> <b>"+file_name_only+ "</b> has been selected successfully from library.</div>";
          
          if(data_preview_only_message=="0")
          {
            if(data_type=="image") preview_html +="<img class='img-thumbnail' style='display:block;width:"+width+";height:"+height+";margin:10px 0' src='"+base_url+file_path+"'>";
            else if(data_type=="video") preview_html +='<video style="display:block;width:'+width+';height:'+height+';border:1px solid #ccc;margin:10px 0" controls><source src="'+base_url+file_path+'">Your browser does not support the video tag.</video>';
            else if(data_type=="audio") preview_html +='<audio style="display:block;width:'+width+';border:1px solid #ccc;margin:10px 0" controls><source src="'+base_url+file_path+'">Your browser does not support the audio tag.</audio>';
          }
          $("#"+preview).html(preview_html);
        }

        var modal_id = $("#"+btn_id).attr("modal-id");
        $("#"+modal_id).modal('toggle');

        
      });

    });
</script>


<!-- scrollbar -->
<!-- theme:"rounded-dark",
theme: "dark", "light",
theme: "light-2", "dark-2",
theme: "light-thick", "dark-thick",
theme: "light-thin", "dark-thin",
theme "rounded", "rounded-dark", "rounded-dots", "rounded-dots-dark",
theme "3d", "3d-dark", "3d-thick", "3d-thick-dark",
theme: "minimal", "minimal-dark",
theme "light-3", "dark-3",
theme "inset", "inset-dark", "inset-2", "inset-2-dark", "inset-3", "inset-3-dark", -->
<script>
  $j(document).ready(function() {
      $(".table-responsive").mCustomScrollbar({
          autoHideScrollbar:true,
          theme:"3d-dark",          
          axis: "x"
      });   
      $(".xscroll").mCustomScrollbar({
        autoHideScrollbar:true,
        theme:"3d-dark",
        axis: "x"
      });
      $(".yscroll").mCustomScrollbar({
        autoHideScrollbar:true,
        theme:"3d-dark"
      });
      $(".xyscroll").mCustomScrollbar({
        autoHideScrollbar:true,
        theme:"3d-dark",
        axis:"yx"
      });

      $(".video-widget-info").mCustomScrollbar({
        autoHideScrollbar:true,
        theme:"rounded-dark"
      });

       $(".account_list").mCustomScrollbar({
        autoHideScrollbar:true,
        theme:"rounded-dark"
      });

    $("body").prognroll({
      height: 5, //Progress bar height
      color: "<?php echo $THEMECOLORCODE; ?>", //Progress bar background color
      custom: false //If you make it true, you can add your custom div and see it's scroll progress on the page
    });
  });
 </script>

 <script>
  // $j(document).ready(function() {
  //     $(".text_second_level_account_list").mCustomScrollbar({
  //         autoHideScrollbar:true,
  //         theme:"rounded-dark"
  //     });   
  // });



  $j(document).ready(function() {
      $(".social_sharing_design").mCustomScrollbar({
          autoHideScrollbar:true,
          theme:"3d-dark"
      }); 

      $(document.body).on('click','.text_first_level_account_list .text_account_icon_image,.text_first_level_account_list .pretty_icon_account_name,.image_first_level_account_list .image_account_icon_image,.image_first_level_account_list .pretty_icon_account_name, .text_first_level_account_list .account_collapse_toggle_icon,.text_first_level_account_list .pretty_icon_account_name',function(){ 
        $(this).parent().next('.text_second_level_account_list,.image_second_level_account_list').toggle();
      });

  });


 </script>

