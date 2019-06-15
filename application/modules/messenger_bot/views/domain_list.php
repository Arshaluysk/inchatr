<?php $this->load->view('admin/theme/message'); ?>
<style>
  .datagrid-body
  {
    overflow: hidden !important; 
  }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class='fa fa-check-circle'></i> <?php echo $this->lang->line('Whitelisted Domains'); ?>  </h1>

</section>

<!-- Main content -->
<section class="content">  
  <div class="row" >
    <div class="col-xs-12 table-responsive">      
      <div class="grid_container" style="min-width:700px; height:659px;">
            <table 
            id="tt"  
            class="easyui-datagrid" 
            url="<?php echo base_url()."messenger_bot/domain_whitelist_data"; ?>" 

            pagination="true" 
            rownumbers="true" 
            toolbar="#tb" 
            pageSize="10" 
            pageList="[10,15,20,50,100,500]"  
            fit= "true" 
            fitColumns= "true" 
            nowrap= "true" 
            view= "detailview"
            idField="id"
            >

            <!-- url is the link to controller function to load grid data -->
            
                <thead>
                    <tr>
                        <!-- <th field="id"  checkbox="true"></th> -->
                        <th style="width: 30%;" align="left" field="account_name"><?php echo $this->lang->line('FB Account'); ?></th>                  
                        <th style="width: 30%;" align="left" field="page_name" formatter="pagename"><?php echo $this->lang->line('Page Name'); ?></th>
                        <th style="width: 10%;" align="left" field="count"><?php echo $this->lang->line('Domain Count'); ?></th>                   
                        <th style="width: 30%;" field="view" formatter='action_column'><?php echo $this->lang->line('actions'); ?></th>
                    </tr>
                </thead>
            </table>                        
      </div>
  
       <div id="tb" style="padding:3px">


            <a class="btn btn-primary" id="add_new_domain" data-toggle="modal" href='#add_new_domain_modal'><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add Domain");?></a>
            
            <form class="form-inline" style="margin-top:20px">
                <div class="form-group">
                    <input id="search_domain" name="search_domain" value="<?php echo $this->session->userdata('messenger_bot_whitelist_domain'); ?>" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Domain'); ?>">
                </div>  
                <div class="form-group">
                    <input id="search_page" name="search_page" value="<?php echo $this->session->userdata('messenger_bot_whitelist_page'); ?>" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Page Name'); ?>">
                </div>                   
                <button class='btn btn-info'  onclick="doSearch(event)"><i class="fa fa-search"></i> <?php echo $this->lang->line('Search'); ?></button>     
                      
            </form> 

        </div>        
    </div>
  </div>   
</section>

<?php     
    $doyouwanttodeletethiscontact = $this->lang->line("do you want to delete this domain?");
    $pleasewait = $this->lang->line("please wait");
    $somethingwentwrongpleasetryagain = $this->lang->line("something went wrong, please try again.");
    $adddomain = $this->lang->line("Add Domain");
    $domainlist = $this->lang->line("Domain List");
    $deleted = $this->lang->line("Deleted");
    $somethingismissing = $this->lang->line("something is missing");
?>


<script> 
  
    var base_url="<?php echo site_url();?>";     
    var doyouwanttodeletethiscontact="<?php echo $doyouwanttodeletethiscontact;?>";     
    var pleasewait="<?php echo $pleasewait;?>";     
    var somethingwentwrongpleasetryagain="<?php echo $somethingwentwrongpleasetryagain;?>";     
    var adddomain="<?php echo $adddomain;?>";     
    var domainlist="<?php echo $domainlist;?>";    
    var deleted="<?php echo $deleted;?>";    
    var somethingismissing="<?php echo $somethingismissing;?>";    

    function action_column(value,row,index)
    {             
                      
        var str="";     
      
        str=str+"&nbsp;<a style='cursor:pointer' class='btn btn-outline-primary add_domain' title='"+adddomain+"' data-page='"+row.page_id+"'><i class='fa fa-plus-circle'></i></a>&nbsp;"; 
        str=str+"<a style='cursor:pointer' class='btn btn-outline-info domain_list' title='"+domainlist+"' data-account-name='"+row.account_name+"' data-page-name='"+row.page_name+"' data-page='"+row.page_id+"'><i class='fa fa-eye'></i></a>"; 
        
        return str;
    } 

    function pagename(value,row,index)
    {             
                      
        var str="<a target='_BLANK' href='https://facebook.com/"+row.fb_page_id+"'>"+value+"</i></a>";         
        return str;
    } 

    $j(document.body).on('click','.domain_list',function(){
      var page_id = $(this).attr('data-page');
      var page_name = $(this).attr('data-page-name');
      var account_name = $(this).attr('data-account-name');
      $("#response_div").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
      $("#domain_list_modal").modal(); 
      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>messenger_bot/domain_details",
        data:{page_id:page_id,page_name:page_name,account_name:account_name},
        success:function(response)
        { 
           $('#response_div').html(response);  
        }
      });

    }); 

    $j(document.body).on('click','.delete_domain',function(){
        var domain_id = $(this).attr('data-id');
        var col_id = $(this).attr('id');
        $(this).addClass('disabled');

        alertify.confirm('<?php echo $this->lang->line("are you sure");?>',doyouwanttodeletethiscontact, 
		  function(){ 
		    $.ajax({
	          type:'POST' ,
	          url:"<?php echo site_url();?>messenger_bot/delete_domain",
	          data:{domain_id:domain_id},
	          success:function(response)
	          { 
	              var deleted_html="<span class='label label-light'><i class='fa fa-check red'></i> "+deleted+"</span>";
	              if(response=='1')
	              $("#"+col_id).parent().html(deleted_html);
	              else
	              alertify.alert('<?php echo $this->lang->line("Alert"); ?>',somethingwentwrongpleasetryagain,function(){});
	          }
	        });
		  },
		  function(){     
		  });       

    }); 

    $j(document.body).on('click','#add_new_domain_submit',function(){
        var page_id = $("#add_new_domain_page").val();
        var domain_name = $("#add_new_domain_name").val();

        if(page_id=='' || domain_name=='')
        {
           alertify.alert('<?php echo $this->lang->line("Alert"); ?>',somethingismissing,function(){});
           return false;
        }

        $(this).addClass('disabled');
        $("#add_new_domain_response").removeClass('alert').removeClass('alert-success').removeClass('alert-danger').html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
        $.ajax({
          type:'POST' ,
          url:"<?php echo site_url();?>messenger_bot/add_domain",
          data:{page_id:page_id,domain_name:domain_name},
          dataType:'JSON',
          success:function(response)
          { 
            $('#add_new_domain_submit').removeClass('disabled');
            $("#add_new_domain_page").val('');
            $("#add_new_domain_name").val('');
            if(response.status=='1')
            $("#add_new_domain_response").addClass('alert alert-success').html(response.result);
            else
            $("#add_new_domain_response").addClass('alert alert-danger').html(response.result);
          }
        });

    });

    $j(document.body).on('click','.add_domain',function(){
        var page_id = $(this).attr('data-page');
        $("#add_new_domain_page").val(page_id);  
        $("#add_new_domain_modal").modal();   
    }); 

    $j("document").ready(function(){
      $('#add_new_domain_modal').on('hidden.bs.modal', function () { 
        location.reload();
      });
    });

       
   
    function doSearch(event)
    {
        event.preventDefault(); 
        $j('#tt').datagrid('load',{
          search_domain  :     $j('#search_domain').val(),         
          search_page  :     $j('#search_page').val(),        
          is_searched      :      1
        });


    }  

</script>


<div class="modal fade" id="domain_list_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-list-ol"></i> <?php echo $this->lang->line("Domain List");?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive" id="response_div" style="padding:0 20px;"></div>
                </div>               
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_new_domain_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> <?php echo $this->lang->line("Add Domain");?></h4>
            </div>
            <div class="modal-body">
                <div id="add_new_domain_response" class="text-center"></div>
                <div class="form-group col-xs-12" style="padding:0 5px 0 0">
                  <label><?php echo $this->lang->line("Page") ?> *</label>
                  <select class="form-control" id="add_new_domain_page" name="add_new_domain_page">
                    <?php 
                    foreach ($pagelist as $key => $value) 
                    {
                      echo "<option value=''>".$this->lang->line('Choose Page')."</option>";
                      echo '<optgroup label="'.addslashes($value['account_name']).'">';
                      foreach ($value['page_data'] as $key2 => $value2) 
                      {
                          echo "<option value='".$value2['page_id']."'>".$value2['page_name']."</option>";
                      }
                      echo '</optgroup>';
                    } ?>
                  </select>
                </div> 
                <div class="form-group col-xs-12" style="padding:0 5px 0 0">
                  <label><?php echo $this->lang->line("Domain") ?> *</label>
                  <input placeholder="http://xyz.com"  name="add_new_domain_name" id="add_new_domain_name" class="form-control" type="text"/>
                </div>        
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-lg btn-default pull-right" data-dismiss="modal"><?php echo $this->lang->line('close') ?></button>
              <button class="btn btn-primary btn-lg pull-left" name="add_new_domain_submit" id="add_new_domain_submit" type="button"><i class="fa fa-save"></i> <?php echo $this->lang->line('save');?></button>
            </div>
        </div>
    </div>
</div>






 
