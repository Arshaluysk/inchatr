<?php $this->load->view('admin/theme/message'); ?>
<?php $is_demo=$this->is_demo; ?>
<?php $is_admin=($this->session->userdata('user_type') == "Admin")?1:0; ?>
<style type="text/css">
    .button{
        margin-top: 10px;
    }
    .datagrid-body
    {
      overflow: hidden !important; 
    }
</style>
<!-- Main content -->
<section class="content-header">
    <h1 class = 'text-info'> <i class="fa fa fa-th-large"></i> <?php echo $this->lang->line('Auto Comment Template Manager'); ?> </h1>
</section>
<section class="content">  
    <div class="row" >
        <div class="col-xs-12 table-responsive">
            <div class="grid_container" style="min-width:700px; height:657px;">
                <table 
                id="tt"
                class="easyui-datagrid" 
                url="<?php echo base_url()."facebook_ex_auto_comment/template_manager_data/"; ?>" 
                pagination="true" 
                rownumbers="true" 
                toolbar="#tb" 
                pageSize="10" 
                pageList="[5,10,15,20,50,100,500,1000]"  
                fit= "true" 
                fitColumns= "true" 
                nowrap= "true" 
                view= "detailview"
                idField="id"
                >
                    <!-- url is the link to controller function to load grid data -->
                    <thead>
                        <tr>
                           
                            <th width="50%" align="left" field="template_name"><?php echo $this->lang->line("Template Name"); ?></th>
                            <th width="50%" field="action" sortable="true"><?php echo $this->lang->line("Action")?></th>
                        </tr>
                    </thead>
                </table>                        
            </div>

            <div id="tb" style="padding:3px">
              
                <button class="btn btn-primary" type="button" name="add" id="add" style="color: white;"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Create new template'); ?></button> 

                <div id="dynamic_field_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="post" id="add_name">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 style="text-align: center;" class="modal-title"><i class="fa fa-th-large"></i> <?php echo $this->lang->line('Please Give The Following Information For Post Auto Comment'); ?></h4>
                                </div>
                                <div class="modal-body">

                                    <label>
                                        <i class="fa fa-th-large"></i>
                                         <?php echo $this->lang->line('Template Name'); ?>
                                    </label>
                                    <div class="form-group">
                                        <input type="text" name="template_name" id="name" class="form-control" placeholder="<?php echo $this->lang->line('Your Template Name'); ?>" />
                                    </div>
                                    <div class="form-group" id="dynamic_field">


                                       
                                        
                                    </div>

                                    <button style="font-size: 10px; text-align:center;" type="button" name="add_more" id="add_more" class="btn btn-outline-primary add_more_edit"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('add more'); ?></button>
                                    <button style="font-size: 10px;text-align:center;" type="button" id="add_more_new" class="btn btn-outline-primary add_more_new">
                                        <i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('add more'); ?>
                                    </button>
                                    <div class="smallspace clearfix"></div>
                                    <div class="col-xs-12 text-center" id="response_status"></div>
                                </div>
                                <div class="modal-footer" style="margin-top: 10px;">
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                    <input type="hidden" name="action" id="action" value="insert" />
                                     
                                    
                                    <!-- <input type="submit" name="submit" id="submit" class="btn btn-primary" value="Submit" /> -->
                                    <button type="submit" name="submit" id="submit" class="btn btn-primary btn-lg"><i class='fa fa-save'></i> <?php echo $this->lang->line('Save'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div> 

                <form class="form-inline" style="margin-top:20px">

                    <div class="form-group">
                        <input id="template_name" name="template_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Template Name'); ?>">
                    </div>

                    <button class='btn btn-info'  onclick="doSearch(event)"><i class="fa fa-search"></i> <?php echo $this->lang->line("search");?></button>               
                </form> 

            </div>  

        </div>
    </div>   
</section>


<script>

    var base_url="<?php echo site_url(); ?>";
    var is_demo='<?php echo $is_demo;?>'  
    var is_admin='<?php echo $is_admin;?>'  

    function doSearch(event)
    {
        event.preventDefault(); 
        $j('#tt').datagrid('load',{
            template_name   :     $j('#template_name').val(),                  
            is_searched      :      1
        });
    }


</script>

<?php 
    
    $Youdidntprovideallinformation = $this->lang->line("you didn't provide template information.");
   
    $Youdidntselectanyoption = $this->lang->line("you didn\'t select any option.");

    $Youdidntprovideallcomment = $this->lang->line("You didn\'t provide comment information ");

    $Doyouwanttodeletethisrecordfromdatabase = $this->lang->line("do you want to delete this record from database?");
    $AutoComment = $this->lang->line("auto comment");
    $remove = $this->lang->line("remove");
    $AddComments = $this->lang->line("add comments");
?>

<script>
$(document).ready(function(){



    var count = 10;
    var wrapper= $('#dynamic_field');
    var add_button_edit      = $(".add_more_edit");
    var add_button = $("#add_more_new");
    var x=1;
    var AutoComment = "<?php echo $AutoComment; ?>";
    var remove = "<?php echo $remove; ?>";
    var AddComments = "<?php echo $AddComments; ?>";

    function add_dynamic_input_field(x)
    {     

        output = '<div style="margin-top:50px;border:1px solid #ccc;" class="form-group">';
        output += '<h4 class="modal-title text-center" style="margin: 0px !important;padding:10px 0;font-size:13px; text-align:center;"><i class="fa fa-comments"></i> '+AutoComment+'</h4> <textarea type="text" name="auto_reply_comment_text[]" id="auto_reply_comment_text_'+x+'" class="form-control name_list" style="height:70px;width:100%;border:none !important;border-top:1px solid #ccc !important;background:#fcfcfc;border-radius:0 !important;" placeholder="'+AddComments+'"></textarea>';
        output += '<a href="#" style="margin-top:10px;font-size:10px;text-align:center;" class="btn btn-outline-danger remove_field pull-right"><i class="fa fa-remove"></i> '+remove+'</a></div>';
        $(wrapper).append(output);

    }

    $('#add').click(function(){
        add_dynamic_input_field(x);
        $(".add_more_edit").hide();
        $('#action').val("insert");
        $('#submit').val('<?php echo $this->lang->line("submit"); ?>');
        $('#dynamic_field_modal').modal('show');

         $j("#auto_reply_comment_text_"+x).emojioneArea({
                autocomplete: false,
                pickerPosition: "bottom"
        });
    });

    $(add_button).on('click', function(e){
        e.preventDefault();
        if(x<count){
            x++;
            add_dynamic_input_field(x);

             $j("#auto_reply_comment_text_"+x).emojioneArea({
                autocomplete: false,
                pickerPosition: "bottom"
            });
            
        }
       
        
      

    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    });

    $('#add_name').on('submit', function(event){
        event.preventDefault();

        $("#response_status").html('');

        var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
        var name = $("#name").val().trim();

        if(name == ''){
            alertify.alert('<?php echo $this->lang->line("Alert")?>',Youdidntprovideallinformation,function(){});
            return false;
        }
        var form_data = $(this).serialize();

        var Youdidntprovideallcomment = "<?php echo $Youdidntprovideallcomment; ?>";
        var auto_reply_comment_text = $('#auto_reply_comment_text').val();
        if(auto_reply_comment_text == ''){
            alertify.alert('<?php echo $this->lang->line("Alert")?>',Youdidntprovideallcomment,function(){});
            return false;
        }
        var action = $('#action').val();


        $.ajax({
            url:base_url+"facebook_ex_auto_comment/create_template_action",
            method:"POST",
            data:form_data,
            success:function(data)
            {
                if(action == 'insert')
                {
                    alertify.success('<?php echo $this->lang->line("your data has been successfully inseret into the database."); ?>');
                    window.location.reload();
                }

                if(action == 'edit')
                {
                    
                   alertify.success('<?php echo $this->lang->line("your data has been successfully editted from the database."); ?>');
                   window.location.reload();

                }
                
                $('#add_name')[0].reset();
                $('#dynamic_field_modal').modal('hide');
      
            }
        });
      
    });

    $(document).on('click', '.edit', function(){
        var id = $(this).attr("id");
        var x;
        $("#add_more_new").hide();

        $.ajax({
            url:base_url+"Facebook_ex_auto_comment/ajaxselect",
            method:"POST",
            data:{id:id},
            dataType:"JSON",
            success:function(data)
            {
                $('#name').val(data.template_name);
                $('#dynamic_field').html(data.auto_reply_comment_text);
                $('#action').val('edit');
                $('.modal-title').html("<i class='fa fa-comments'></i> <?php echo $this->lang->line('auto comment'); ?>");
                $('.modal-header .modal-title').html("<i class='fa fa-comments'></i> <?php echo $this->lang->line('Please Give The Following Information For Post Auto Comment'); ?>");
                $('#submit').val("<?php echo $this->lang->line('update'); ?>");
                $('#hidden_id').val(id);
                $('#dynamic_field_modal').modal('show');

                x=data.x;

                for(var k=1; k<=x;k++){

                      $j("#auto_reply_comment_text_"+k).emojioneArea({
                            autocomplete: false,
                            pickerPosition: "bottom"
                        });
                }
                
                $count=10
               
                $(add_button_edit).on('click', function(e){
                    e.preventDefault();
                    if(x<count){
                        x++;
                        add_dynamic_input_field(x);

                        $j("#auto_reply_comment_text_"+x).emojioneArea({
                            autocomplete: false,
                            pickerPosition: "bottom"
                        });
                        
                    }
                   
                    
                  

                });


          
            }
        });
    });



    var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $Doyouwanttodeletethisrecordfromdatabase; ?>";
    $(document.body).on('click','.delete',function(){
        var id = $(this).attr("id");
        if(is_demo=='1' && is_admin=='1')
        {
            alertify.alert('<?php echo $this->lang->line("Alert");?>','You can not delete admin account templates.',function(){ });
            return;
        }
        alertify.confirm('<?php echo $this->lang->line("are you sure");?>',Doyouwanttodeletethisrecordfromdatabase, 
            function(){ 
                $.ajax({
                    type:'POST' ,
                    url: base_url+"facebook_ex_auto_comment/delete_comment",
                    data: {id:id},
                    // async: false,
                    success:function(response){
                        $j('#tt').datagrid('reload');
                        alertify.success('<?php echo $this->lang->line("your data has been successfully deleted from the database."); ?>');
                    }

                });
            },
            function(){     
        });
    });

    $(document).on('click','.close',function(){

        location.reload();

    });


});
</script>


<div class="modal fade" id="delete_template_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><?php  echo $this->lang->line("Template Delete Confirmation") ?></h4>
            </div>
            <div class="modal-body" id="delete_template_modal_body">                

            </div>
        </div>
    </div>
</div>
