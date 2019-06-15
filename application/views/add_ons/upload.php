<?php $this->load->view("include/upload_js"); ?>
<div class="container-fluid ">
  <div class="row">
    <div class='well well_border_left text-center' style="border-radius: 0;background: #fff;">
      <h4><i class='fa fa-cloud-upload'></i> <?php echo $this->lang->line("upload new add-on"); ?></h4> 
    </div>

    <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2">
      <div class="box box-primary">
        <div class="box-header ui-sortable-handle" style="cursor: move;">
          <i class="fa fa-cloud-upload blue"></i>
          <h3 class="box-title blue" title="" style="font-size:15px;margin-top:15px;">
            <?php echo $this->lang->line("browse add-on zip file"); ?> *
          </h3>
        </div>
        <div class="box-body">           

          <div class="form-group">    
            <div id="addon_url_upload"><?php echo $this->lang->line('Upload');?></div>
          </div>
          <div class="row" style="background: #fff; width: 100%; margin: auto; box-shadow: 0px 0px 16px -2px rgba(143,141,143,0.61) !important;">
            <h4 style="text-align: center;">Current server settings</h4><hr>           
            <div class="col-xs-12 col-md-6">
              <?php 
                if(function_exists('ini_get'))
                {
                  $make_dir = (!function_exists('mkdir')) ? "Not working":"Working";
                  $zip_archive = (!class_exists('ZipArchive')) ? "Not Enabled":"Enabled";
                  echo "<ul>";
                  echo "<li><b>mkdir() function</b> : ".$make_dir."</li>"; 
                  echo "<li><b>ZipArchive</b> : ".$zip_archive."</li>";  
                  echo "<li><b>max_input_time</b> : ".ini_get('max_input_time')."</li>";         
                  echo "</ul>";
                }
              ?>
            </div>
            <div class="col-xs-12 col-md-6">
              <?php 
                if(function_exists('ini_get'))
                {
                  echo "<ul>";
                  echo "<li><b>post_max_size</b> : ".ini_get('post_max_size')."</li>"; 
                  echo "<li><b>upload_max_filesize</b> : ".ini_get('upload_max_filesize')."</li>";          
                  echo "</ul>";
                }
              ?>
            </div>
            <div class="col-xs-12">              
              <p>If you face problem during upload, make sure server file upload limit is large enough and application/modules folder has write permission.</p>
            </div>
          </div><br/>
          <div class="well orange text-justify" style="padding:10px"><?php echo $this->lang->line('after you upload add-on file you will be taken to add-on activation page, you need to active the add-on there.');?><?php echo $this->lang->line('if you are having trouble in file upload with our uploader then you can simply uplaod add-on zip file to application/modules folder, unzip itand activate it from add-on list.');?></div>
        </div>

      </div>     
    </div>
  </div>    
</div>

    



<script>
  var base_url = "<?php echo base_url(); ?>";
  $j("document").ready(function(){

    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;}); 

    $("#addon_url_upload").uploadFile({
        url:base_url+"addons/upload_addon_zip",
        fileName:"myfile",
        maxFileSize:100*1024*1024,
        showPreview:false,
        returnType: "json",
        dragDrop: true,
        showDelete: true,
        multiple:false,
        maxFileCount:1, 
        showDelete:false,
        acceptFiles:".zip",
        deleteCallback: function (data, pd) {
            var delete_url="<?php echo site_url('addons/delete_uploaded_zip');?>";
              $.post(delete_url, {op: "delete",name: data},
                  function (resp,textStatus, jqXHR) {                         
                  });
           
         },
         onSuccess:function(files,data,xhr,pd)
           {
               var data_modified = data;
               window.location.assign(base_url+'addons/lists'); 
           }
    });
  });
</script>


<style>
  .ajax-upload-dragdrop,.ajax-file-upload-statusbar{width:100% !important;}
  .full {
    width: 100%;
    float: left;
    margin: 0;
    padding: 0;
  }
  .short-info {
    border: 1px solid #e8edef;
  }

  .short-info p {
    display: inline-block;
    width: 48%;
    border-right: 1px solid #e8edef;
    font-size: 11px;
    margin: 0;
    text-align: center;
    padding: 10px 0;
    line-height: 26px;
  }


  .short-info p:last-child {
    border-right: 0px solid #e8edef;
  } 

  .short-info p span {
    font-weight: bold;
    font-size: 25px;
  } 
  .custom_progress {
    height: 2px;
    margin-top: 0px;
    margin-bottom: 10px;
    overflow: hidden;
    background-color: #f5f5f5;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
  }
  .custom_progress_bar {
    float: left;
    width: 0;
    height: 100%;
    font-size: 4px;
    line-height: 6px;
    color: #fff;
    text-align: center;
    background-color: #337ab7;
    -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);
    -webkit-transition: width .6s ease;
    -o-transition: width .6s ease;
    transition: width .6s ease;
  }
  .existing_account {
    margin: 10px 0 0;
    font-size: 16px;
    font-weight: bold;
    font-style: italic;
  }
  .account_list{
    padding-left: 5%;
  }
  .individual_account_name{
    font-weight: bold;
    font-size: 14px;
  }
  .padded_ul{
    padding-left: 10%;
  }
</style>


<style>
  .margin_top{
    margin-top:20px;
  }

  .padding{
    padding:15px;
  } 

  .count_text{
    margin: 0px;
    padding: 0px;
    color: orange;
  }
  .cover_image{
    height: 200px;
    width: 200px;
  }

  h4.pagination_link
  {
    font-size: 12px;
    text-align: center;
    font-weight: normal;
    margin-top: 12px;
  }

  h4.pagination_link a
  {
    padding: 4px 7px 4px 7px;
    background: #238db6;
    color:#fff;
    border:1px solid #238db6;
    font-style: normal;
    text-decoration: none;
  }

  h4.pagination_link strong
  {
    padding: 4px 7px 4px 7px;
    background: #E95903;
    color:#fff;
    border:1px solid #E95903;
    font-style: normal;
  }

  h4.pagination_link a:hover
  {
    background: #77a2cc;
    border:1px solid #77a2cc; 
    color: #fff;
  }

</style>
