<?php $this->load->view("include/upload_js"); ?>
<div class="row padding-20">
	<div class="col-xs-12 col-md-7 padding-10">
		<div class="box box-primary">
			<div class="box-header ui-sortable-handle  text-center" style="cursor: move;margin-bottom: 0px;">
				<i class="fa fa-paper-plane"></i>
				<h3 class="box-title"><?php echo $this->lang->line("messanger AD JSON script"); ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body" style="padding:25px !important;">
				<form action="#" enctype="multipart/form-data" id="inbox_json_form" method="post">
					<div class="row">	
						<div class="col-xs-12">
							<div class="form-group">
								<label><?php echo $this->lang->line("upload image"); ?> [jpg,png]</label>
								<div id="image_url"><?php echo $this->lang->line("upload"); ?></div>
							</div>
						</div>					
						<div class="form-group col-xs-12">
							<label>
								<?php echo $this->lang->line("image url"); ?> *
							</label>
							<input class="form-control" name="image_url_link" id="image_url_link" type="text" placeholder="<?php echo $this->lang->line('Upload an image or put an image url directly here');?>"> 
						</div>								
						
					</div>

					<br>
					<br>
					
					<div class="row">						
						<div class="form-group col-xs-12 col-md-6">
							<label>
								<?php echo $this->lang->line("title"); ?>*
							</label>
							<input class="form-control" name="message_title" id="message_title" type="text" placeholder="<?php echo $this->lang->line("welcome") ?>"> 
						</div>
						<div class="form-group col-xs-12 col-md-6">
							<label>
								<?php echo $this->lang->line("sub-title"); ?>*
							</label>
							<input class="form-control" name="message_subtitle" id="message_subtitle" type="text" placeholder="<?php echo $this->lang->line("subtitle") ?>"> 
						</div>
					</div>


					<div class="row">						
						<div class="form-group col-xs-12">
							<label>
								<?php echo $this->lang->line("website url"); ?>*
							</label>
							<input class="form-control" name="website_url" id="website_url" type="text" placeholder="http://example.com/page1"> 
						</div>
					</div>

					<div class="row">	
						<div class="form-group col-xs-12 col-md-6">
							<label>
								<?php echo $this->lang->line("view website button text"); ?>*
							</label>
							<input class="form-control" name="website_button_text" id="website_button_text" type="text" placeholder="<?php echo $this->lang->line("view website") ?>"> 
						</div>
						<div class="form-group col-xs-12 col-md-6">
							<label>
								<?php echo $this->lang->line("start chatting button text"); ?>*
							</label>
							<input class="form-control" name="start_chat_button_text" id="start_chat_button_text" type="text" placeholder="<?php echo $this->lang->line("Start Chatting") ?>"> 
						</div>
					</div>

					<div class="row">	
						<div class="form-group col-xs-12 col-md-6">
							<label>
								<?php echo $this->lang->line("quick reply button 1 text"); ?>*
							</label>
							<input class="form-control" name="reply_1_button_text" id="reply_1_button_text" type="text" placeholder="<?php echo $this->lang->line("ok, thanks") ?>"> 
						</div>
						<div class="form-group col-xs-12 col-md-6">
							<label>
								<?php echo $this->lang->line("quick reply button 2 text"); ?>*
							</label>
							<input class="form-control" name="reply_2_button_text" id="reply_2_button_text" type="text" placeholder="<?php echo $this->lang->line("no, thanks") ?>"> 
						</div>
					</div>
				    <div class="clearfix"></div>
					<br/>
					<div class="box-footer clearfix" style="border-top:none !important;">
						<!-- <div class="col-xs-12"> -->
							<button style='width:100%;margin-bottom:10px;' class="btn btn-primary center-block btn-lg" id="get_json_code" name="get_json_code" type="button"><i class="fa fa-get-pocket"></i> <?php echo $this->lang->line("get JSON code"); ?></button>
						<!-- </div> -->
					</div>

				</form>
			</div>
			
		</div>
	</div>  <!-- end of col-6 left part -->


	<div class="col-xs-12 col-md-5 padding-10">
		<div class="box box-primary">
			<div class="box-header ui-sortable-handle  text-center" style="cursor: move;margin-bottom: 0px;">
				<i class="fa fa-facebook-official"></i>
				<h3 class="box-title"><?php echo $this->lang->line("inbox preview"); ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body preview">	

				
				
				<div class="chat_box">
					<div class="chat_header">
						<span class='pull-left' id="page_name"><?php echo $this->lang->line("page name"); ?></span>
						<span class='pull-right'> <i class="fa fa-cog"></i> <i class="fa fa-remove"></i> </span>
					</div>
					<div class="chat_body">
						<img id="page_thumb" class="pull-left" src="<?php echo base_url("assets/images/chat_box_thumb.png");?>">
						<span id="json_thumb_container" class="pull-left">						
							<span class="clearfix"></span>
							<img id="json_page_thumb" style="width:200px;height:140px;" class="img-responsive" src="<?php echo base_url("assets/images/chat_box_thumb3.png");?>">
							<div id="json_level1" class='level_class'>
								<div id="json_message_title"><?php echo $this->lang->line("message title"); ?></div>
								<div id="json_message_subtitle"><?php echo $this->lang->line("message subtitle"); ?></div>
							</div>
							<div id="json_level2" class='level_class text-center'>
								<a id="view_website" href=""><?php echo $this->lang->line("view website"); ?></a>
							</div>
							<div id="json_level3" class='level_class text-center'>
								<a id="start_chatting" href=""><?php echo $this->lang->line("start chatting"); ?></a>
							</div>
						</span>
						<div class="clearfix"></div>		
						<br/>					 
						<center id="json_level4" class='pull-right'>
							<a id="reply1" class="btn btn-default btn-sm" href=""><?php echo $this->lang->line("reply1"); ?></a>
							<a id="reply2" class="btn btn-default btn-sm" href=""><?php echo $this->lang->line("reply2"); ?></a>
						</center>
					</div>
					<div class="chat_footer">
						<img src="<?php echo base_url("assets/images/chat_box.png");?>" class="img-responsive">
					</div>
				</div>
			</div>			
		</div>		
	</div> <!-- end of col-6 right part -->

</div>

<?php $this->load->view("facebook_ex/campaign/style");?>

<style type="text/css">
			#json_thumb_container
			{
				max-width: 185px;	
				margin-left: 10px;	
				border: 1px solid #ccc;
				-webkit-border-radius: 10px;		
				-moz-border-radius: 10px;		
				border-radius: 10px;
				overflow: hidden;	
				font-family: Arial;	
			}	

			#json_level4
			{
				width: 185px;	
			}	
			.level_class
			{
				padding: 10px 5px;
				border-top: 1px solid #ccc;
			}	
			#json_level1
			{
				padding-top: 2px;
			}
			#json_level2 a,#json_level3 a
			{
				font-weight: 600;
			}
			#json_message_title
			{
				font-size: 15px;
				font-weight: 700;
				color: #000;
			}
			#json_message_subtitle
			{
				font-size: 13px;
				color: #777;
			}
			#json_level4 a
			{
				color: #01A4E0;
				font-weight: 300;
				background: #fff;
				-webkit-border-radius: 15px;		
				-moz-border-radius: 15px;		
				border-radius: 15px;
				-webkit-box-shadow: none !important;
				-moz-box-shadow: none !important;
				box-shadow: none !important;

			}							
</style>		

<?php 
	$Pleaseprovidealltheinformation = $this->lang->line("please provide all the information");
 ?>

<script>
 
	$j("document").ready(function(){

		var base_url="<?php echo base_url();?>";

		$('[data-toggle="popover"]').popover(); 
		$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

		$("#image_url").uploadFile({
			url:base_url+"facebook_ex_json_messanger/upload_image_only",
			fileName:"myfile",
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
	        maxFileCount:1,
			acceptFiles:".png,.jpg,.jpeg",
			deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('facebook_ex_json_messanger/delete_uploaded_file');?>";
                $.post(delete_url, {op: "delete",name: data},
                    function (resp,textStatus, jqXHR) { 
                    	$("#image_url_link").val("");
                    	$("#json_page_thumb").attr('src','');                   	                 
                    });
	           
	        },
	        onSuccess:function(files,data,xhr,pd)
	        {
	        	var data_modified = base_url+"upload/"+data;
	        	$("#image_url_link").val(data_modified);
	        	$("#json_page_thumb").attr('src',data_modified);	
	        }

		});

        $(document.body).on('blur','#image_url_link',function(){
        	var image_url_link = $('#image_url_link').val();
        	$("#json_page_thumb").attr('src',image_url_link);
        });

        $(document.body).on('keyup','#message_title',function(){
        	var message_title = $('#message_title').val();
        	$("#json_message_title").html(message_title);
        });

        $(document.body).on('keyup','#message_subtitle',function(){
        	var message_subtitle = $('#message_subtitle').val();
        	$("#json_message_subtitle").html(message_subtitle);
        });

        $(document.body).on('keyup','#website_button_text',function(){
        	var website_button_text = $('#website_button_text').val();
        	$("#view_website").html(website_button_text);
        });

        $(document.body).on('keyup','#start_chat_button_text',function(){
        	var start_chat_button_text = $('#start_chat_button_text').val();
        	$("#start_chatting").html(start_chat_button_text);
        });

        $(document.body).on('keyup','#reply_1_button_text',function(){
        	var reply_1_button_text = $('#reply_1_button_text').val();
        	$("#reply1").html(reply_1_button_text);
        });

        $(document.body).on('keyup','#reply_2_button_text',function(){
        	var reply_2_button_text = $('#reply_2_button_text').val();
        	$("#reply2").html(reply_2_button_text);
        });


        $(document.body).on('click','#get_json_code',function(){
        	var image_url_link = $("#image_url_link").val().trim();
        	var start_chat_button_text = $("#start_chat_button_text").val().trim();
        	var message_title = $("#message_title").val().trim();
        	var message_subtitle = $("#message_subtitle").val().trim();
        	var website_url = $("#website_url").val().trim();
        	var website_button_text = $("#website_button_text").val().trim();
        	var reply_1_button_text = $("#reply_1_button_text").val().trim();
        	var reply_2_button_text = $("#reply_2_button_text").val().trim();
        	var Pleaseprovidealltheinformation = "<?php echo $Pleaseprovidealltheinformation; ?>";
        	if(image_url_link=='' || start_chat_button_text=='' || message_title=='' || message_subtitle=='' || website_url=='' || website_button_text=='' || reply_1_button_text=='' || reply_2_button_text=='') 
        	{
        		alertify.alert('<?php echo $this->lang->line("Alert");?>',Pleaseprovidealltheinformation,function(){});
        		return false;
        	} 

         	$("#response_json_modal").modal();
        	var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
			$("#response_json_modal_content").html(loading);

        	var queryString = new FormData($("#inbox_json_form")[0]);
		    $.ajax({
		    	type:'POST' ,
		    	url: base_url+"facebook_ex_json_messanger/ajax_get_json_code",
		    	data: queryString,
		    	dataType : 'JSON',
		    	// async: false,
		    	cache: false,
		    	contentType: false,
		    	processData: false,
		    	success:function(response){	
		    		if(response.status == 'error')
		    		{
		    			var textarea = "<div class='alert alert-danger text-center'>"+response.message+"</div>";     	
			         	$("#response_json_modal_content").html(textarea);
		    		}
		    		else
		    		{
			    		var textarea = "<textarea class='form-control' rows='12'>"+response.message+"</textarea>";     	
			         	$("#response_json_modal_content").html(textarea);
		    		}
		    	}

		    });	    		      
            
        });


    });



</script>
<div class="modal fade" id="response_json_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title text-center"><?php echo $this->lang->line("please copy the following JSON code for farther use"); ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert text-center" id="response_json_modal_content" style="font-style: italic;">
					
				</div>
			</div>
		</div>
	</div>
</div>

