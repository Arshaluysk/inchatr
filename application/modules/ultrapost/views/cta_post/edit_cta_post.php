<?php 
	$this->load->view("include/upload_js"); 

	$image_upload_limit = 1; 
	if($this->config->item('facebook_poster_image_upload_limit') != '')
	$image_upload_limit = $this->config->item('facebook_poster_image_upload_limit'); 

	$video_upload_limit = 10; 
	if($this->config->item('facebook_poster_video_upload_limit') != '')
	$video_upload_limit = $this->config->item('facebook_poster_video_upload_limit');
?>

<img src="<?php echo base_url('assets/pre-loader/Fading squares2.gif');?>" class="center-block previewLoader" style="margin-top:20px;margin-bottom:10px;display:none">

<div class="row padding-20" style="padding-top: 5px;">
	<div class="col-xs-12 col-md-6 padding-5">
		<div class="box box-primary">
			<div class="box-header ui-sortable-handle" style="cursor: move;">
				<i class="fa fa-edit"></i>
				<h3 class="box-title"><?php echo $this->lang->line('CTA (Call to Action) Poster'); ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body">
				<form action="#" enctype="multipart/form-data" id="cta_poster_form" method="post">
					<input type="hidden" value="<?php echo $all_data[0]["id"];?>" name="id">
					<input type="hidden" value="<?php echo $all_data[0]["user_id"];?>" name="user_id">
					<input type="hidden" value="<?php echo $all_data[0]["facebook_rx_fb_user_info_id"];?>" name="facebook_rx_fb_user_info_id">
					<div class="form-group">
						<label><?php echo $this->lang->line('Campaign Name'); ?></label>
						<input type="input" class="form-control"  name="campaign_name" id="campaign_name" value="<?php if(set_value('campaign_name')) echo set_value('campaign_name');else {if(isset($all_data[0]['campaign_name'])) echo $all_data[0]['campaign_name'];}?>">
					</div>

					<div class="form-group">
						<label><?php echo $this->lang->line('Message'); ?></label>
						<a href="#" data-placement="right"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("support Spintax"); ?>, Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
						<textarea class="form-control" name="message" id="message" placeholder="<?php echo $this->lang->line('Type your message here...'); ?>"><?php if(isset($all_data[0]['message'])) echo $all_data[0]['message'];?></textarea>
					</div>
					
					<div class="form-group">
						<label><?php echo $this->lang->line('Paste link'); ?></label>
						<input class="form-control" name="link" id="link"  type="text" value="<?php if(set_value('link')) echo set_value('link');else {if(isset($all_data[0]['link'])) echo $all_data[0]['link'];}?>">
					</div>
					<div class="form-group hidden">
						<label><?php echo $this->lang->line('Preview image URL'); ?></label>
						<input class="form-control" name="link_preview_image" id="link_preview_image" type="text"> 
					</div>					
					<div class="form-group hidden">      
                         <div id="link_preview_upload"><?php echo $this->lang->line('Upload');?></div>                              
                        <br/>
                    </div>
					<div class="form-group hidden">
						<label><?php echo $this->lang->line('Title'); ?></label>
						<input class="form-control" name="link_caption" id="link_caption" type="text"> 
					</div>	
					<div class="form-group hidden">
						<label><?php echo $this->lang->line('Description'); ?></label>
						<textarea class="form-control" name="link_description" id="link_description"></textarea>
					</div>

					<div class="form-group">
						<label><?php echo $this->lang->line('CTA button type'); ?></label>
						<?php echo form_dropdown("cta_type",$cta_dropdown,$all_data[0]["cta_type"],"class='form-control' id='cta_type'");?>
					</div>

					<div class="form-group cta_value_container_div">
						<label><?php echo $this->lang->line('CTA button action link'); ?></label>
						<input type="input" class="form-control"  name="cta_value" id="cta_value">
					</div>

						 <?php 
						 	$facebook_rx_fb_user_info_id=isset($fb_user_info[0]["id"]) ? $fb_user_info[0]["id"] : 0; 
						 	$facebook_rx_fb_user_info_name=isset($fb_user_info[0]["name"]) ? $fb_user_info[0]["name"] : ""; 
						 	$facebook_rx_fb_user_info_access_token=isset($fb_user_info[0]["access_token"]) ? $fb_user_info[0]["access_token"] : ""; 
						 ?>

						 <?php if ($is_all_posted == 1): ?>
						 	<style type="text/css">
						 		.hide_hoye_za_page{
						 			display: none;
						 		}
						 		.hide_hoye_za_template{
						 			display: none;
						 		}
					
						 		.hide_hoye_za_schedule{
						 			display: none;	
						 		}
						 	</style>
						 <?php endif; ?>
					
					<div class="form-group col-xs-12 col-md-6 hide_hoye_za_page" style="padding: 0 1px 0 0 ;">
						<label><?php echo $this->lang->line('Post to pages'); ?></label>
						<select multiple="multiple"  class="form-control" id="post_to_pages" name="post_to_pages[]">	



						 <?php
							    foreach($fb_page_info as $key=>$val)
							    {   
							        $id=$val['id'];
							        $page_name=$val['page_name'];

							        $page_ids = explode(',',$all_data[0]['page_ids']);

							        if(in_array($id, $page_ids))
							            echo "<option value='{$id}' selected>{$page_name}</option>";
							        else
							            echo "<option value='{$id}'>{$page_name}</option>";
							        

							    }
						 ?> 					
						</select>
					</div>	

					<div class="form-group col-xs-12 col-md-6 hide_hoye_za_template" style="padding: 0;">
						<label><?php echo $this->lang->line('Auto Reply Template'); ?></label>
						<select  class="form-control" id="auto_reply_template" name="auto_reply_template">
						<?php
							echo "<option value='0'>{$this->lang->line('Please select a template')}</option>";
							foreach($auto_reply_template as $key=>$val)
							{
								$id=$val['id'];
								$group_name=$val['ultrapost_campaign_name'];
								if($id == $all_data[0]['ultrapost_auto_reply_table_id'])
									echo "<option value='{$id}' selected>{$group_name}</option>";
								else
									echo "<option value='{$id}'>{$group_name}</option>";
							}
						 ?>
						</select>
					</div>
					
					<div class="clearfix"></div><br>	

					<div class="form-group hide_hoye_za_schedule">
						<label><?php echo $this->lang->line('Schedule'); ?></label><br/>
						<input name="schedule_type" value="later" id="schedule_later" checked type="radio"> <?php echo $this->lang->line('Later'); ?> 
					</div>

					<div class="form-group schedule_block_item schedule_block_item col-xs-12 col-md-6 hide_hoye_za_schedule">
						<label><?php echo $this->lang->line('Schedule time'); ?></label>
						<input placeholder="Time"  name="schedule_time" id="schedule_time" class="form-control datepicker" type="text"  value="<?php if(set_value('schedule_time')) echo set_value('schedule_time');else {if(isset($all_data[0]['schedule_time'])) echo $all_data[0]['schedule_time'];}?>"/>
					</div>

					<div class="form-group schedule_block_item schedule_block_item col-xs-12 col-md-6 hide_hoye_za_schedule">
						<label><?php echo $this->lang->line('Time zone'); ?></label>
						<?php
						$time_zone[''] = 'Please Select';
						echo form_dropdown('time_zone',$time_zone,$all_data[0]['time_zone'],' class="form-control" id="time_zone" required'); 
						?>
					</div>

					<div class="clearfix"></div><br>

					<div class="input-group col-xs-12 col-md-12 hide_hoye_za_schedule" style="padding: 0 1px 0 0 ;">
					  <span class="input-group-addon"><?php echo $this->lang->line('repost this post'); ?></span>
					  <input type="number" class="form-control" value="<?php if(isset($all_data[0]['repeat_times'])) echo $all_data[0]['repeat_times']; ?>" name="repeat_times" aria-describedby="basic-addon2">
					  <span class="input-group-addon" id="basic-addon2"><?php echo $this->lang->line('times') ?></span>
					</div>
					<br>
					<div class="form-group hide_hoye_za_schedule">
						<label><?php echo $this->lang->line('time interval'); ?></label>
						<?php
						
							$time_interval[''] =$this->lang->line('Please Select Periodic Time Schedule');
							echo form_dropdown('time_interval',$time_interval,$all_data[0]['time_interval'],' class="form-control" id="time_interval" required');
						?>
					</div>
			</div>

			<div class="clearfix"></div>
			<div class="box-footer clearfix" style="text-align: left !important;">
				<button class="btn btn-primary btn-lg btn-block" submit_type="text_submit" id="submit_post" name="submit_post" type="button"><i class="fa fa-send"></i> <?php echo $this->lang->line('Submit Post'); ?> </button>
			</div>
			</form>
		
		</div>
	</div>  <!-- end of col-6 left part -->

	<div class="col-xs-12 col-md-6 padding-5">
		<div class="box box-primary">
			<div class="box-header ui-sortable-handle" style="cursor: move;">
				<i class="fa fa-facebook-official"></i>
				<h3 class="box-title"><?php echo $this->lang->line('Preview'); ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body preview">					
				<?php $profile_picture="https://graph.facebook.com/me/picture?access_token={$facebook_rx_fb_user_info_access_token}&width=150&height=150"; ?>				
				<img src="<?php echo $profile_picture;?>" class="preview_cover_img inline pull-left text-center" alt="X">
				<span class="preview_page"><?php echo $facebook_rx_fb_user_info_name;?></span><br/>
				<span class="preview_page_sm"><?php echo $this->lang->line('Now'); ?> <?php echo isset($app_info[0]['app_name']) ? $app_info[0]['app_name'] : $this->config->item("product_short_name");?></span><br/><br/>	
				<span class="preview_message"><br/></span>	

				<img src="<?php echo base_url('assets/images/demo_image.png');?>" class="preview_img" alt="No Image Preview">		
				<div class="preview_og_info clearfix">
					<div class="preview_og_info_title inline-block"></div>
					<div class="preview_og_info_desc inline-block">							
					</div>
					<div class="preview_og_info_link inline-block pull-left">							
					</div>
					<div class="button_container"><a class="cta-btn btn btn-sm btn-default pull-right"><?php echo $this->lang->line('Message Page'); ?></a></div>
				</div>

			</div>
		</div>
	</div> <!-- end of col-6 right part -->

</div>
<script type="text/javascript">
	$('[data-toggle="popover"]').popover(); 
	$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
</script>

<script>

	$j("document").ready(function(){
		setTimeout(function() {

			$("#link").blur();

			var cta_type = "<?php echo $all_data[0]["cta_type"]?>";
			if(cta_type=="MESSAGE_PAGE" || cta_type=="LIKE_PAGE") 
        	$(".cta_value_container_div").hide();
        	else $(".cta_value_container_div").show();
        	cta_type=cta_type.replace(/_/g, " ");
        	cta_type=cta_type.toLowerCase();        	
        	$(".cta-btn").html(cta_type); 
        	$(".cta-btn").css("text-transform","capitalize"); 

			var auto_private_reply_con="<?php echo $all_data[0]["auto_private_reply"];?>";
			if(auto_private_reply_con == 1){
				$(".auto_reply_block_item").show();
			}else{
				$(".auto_reply_block_item").hide();
			}

			var auto_comment="<?php echo $all_data[0]["auto_comment"]?>";
			if(auto_comment == 1){
				$(".auto_comment_block_item").show();
			}else{
				$(".auto_comment_block_item").hide();
			}

			var auto_share_post="<?php echo $all_data[0]["auto_share_post"];?>";
			if(auto_share_post == 1){
				$(".auto_share_post_block_item").show();
			}else{
				$(".auto_share_post_block_item").hide();
			}
		}, 1000);
	});


	$j("document").ready(function(){
	
		var emoji_message_div =	$j("#message").emojioneArea({
	        	autocomplete: false,
				pickerPosition: "bottom",
				//hideSource: false,
     	 });

		var base_url="<?php echo base_url();?>";

		var today = new Date();
		var next_date = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
		$j('.datepicker').datetimepicker({
			theme:'light',
			format:'Y-m-d H:i:s',
			formatDate:'Y-m-d H:i:s',
			minDate: today,
			maxDate: next_date
		})	

		$j("#post_to_pages").multipleSelect({
            filter: true,
            multiple: true
        });	

        $j("#post_to_groups").multipleSelect({
            filter: true,
            multiple: true
        });	 
      
 		$j("#auto_share_this_post_by_pages").multipleSelect({
            filter: true,
            multiple: true
        });	

        $(".auto_share_post_block_item,.auto_reply_block_item,.auto_comment_block_item,.cta_value_container_div").hide();
 
        $(document.body).on('change','input[name=auto_share_post]',function(){    
        	if($("input[name=auto_share_post]:checked").val()=="1")
        	$(".auto_share_post_block_item").show();
        	else $(".auto_share_post_block_item").hide();
        });  

        $(document.body).on('change','input[name=auto_private_reply]',function(){    
        	if($("input[name=auto_private_reply]:checked").val()=="1")
        	$(".auto_reply_block_item").show();
        	else $(".auto_reply_block_item").hide();
        }); 

         $(document.body).on('change','input[name=auto_comment]',function(){    
        	if($("input[name=auto_comment]:checked").val()=="1")
        	$(".auto_comment_block_item").show();
        	else $(".auto_comment_block_item").hide();
        }); 

        $(document.body).on('change','input[name=schedule_type]',function(){    
        	if($("input[name=schedule_type]:checked").val()=="later")
        	$(".schedule_block_item").show();
        	else 
        	{
        		$("#schedule_time").val("");
        		$("#time_zone").val("");
        		$(".schedule_block_item").hide();
        	}
        }); 

        var message_pre=$("#message").val();
    	message_pre=message_pre.replace(/[\r\n]/g, "<br />");
    	if(message_pre!="")
    	{
    		message_pre=message_pre+"<br/><br/>";
    		$(".preview_message").html(message_pre);
    	}
    	    

        $(document.body).on('blur','#link',function(){        	
			var link=$("#link").val();
            if(link=='') return;
            $(".previewLoader").show();
	        $.ajax({
	            type:'POST' ,
	            url:"<?php echo site_url();?>ultrapost/cta_post_meta_info_grabber",
	            data:{link:link},
	            dataType:'JSON',
	            success:function(response){
	            		                
	                $("#link_preview_image").val(response.image);
	                $(".preview_img").attr("src",response.image);	

	                if(typeof(response.image)==='undefined' || response.image=="")
	                $(".preview_img").hide();
	                else $(".preview_img").show();	                

	                $("#link_caption").val(response.title);
	                $(".preview_og_info_title").html(response.title); 

	                $("#link_description").val(response.description);
	                $(".preview_og_info_desc").html(response.description);

	                var link_author=link;
	                var link_author = link_author.replace("http://", ""); 
	                var link_author = link_author.replace("https://", ""); 
	                if(typeof(response.image)!='undefined' && response.author!=="") link_author=link_author+" | "+response.author;

	                $(".preview_og_info_link").html(link_author);
	                $("#cta_value").val(link);

	                if(response.image==undefined || response.image=="")
	                $(".preview_img").hide();
	                else $(".preview_img").show();

	                 $(".previewLoader").hide();
	            	              
	            }
	        }); 
        });
		
		function htmlspecialchars(str) {
			 if (typeof(str) == "string") {
			  str = str.replace(/&/g, "&amp;"); /* must do &amp; first */
			  str = str.replace(/"/g, "&quot;");
			  str = str.replace(/'/g, "&#039;");
			  str = str.replace(/</g, "&lt;");
			  str = str.replace(/>/g, "&gt;");
			  }
			 return str;
		}
		
       
         $(document.body).on('keyup','.emojionearea-editor',function(){
		
        	var message=$("#message").val();
			message=htmlspecialchars(message);
			message=message.replace(/[\r\n]/g, "<br />");
			
        	if(message!="")
        	{
        		message=message+"<br/><br/>";
        		$(".preview_message").html(message);
        	}
			
        }); 

        $(document.body).on('blur','#link_preview_image',function(){  
        	var link=$("#link_preview_image").val(); 
            $(".preview_img").attr("src",link).show();	            
        	 
        }); 

         $(document.body).on('change','#cta_type',function(){  
        	var cta_type=$(this).val();

        	if(cta_type=="MESSAGE_PAGE" || cta_type=="LIKE_PAGE") 
        	$(".cta_value_container_div").hide();
        	else $(".cta_value_container_div").show();

        	cta_type=cta_type.replace(/_/g, " ");
        	cta_type=cta_type.toLowerCase();
        	
        	$(".cta-btn").html(cta_type); 
        	$(".cta-btn").css("text-transform","capitalize");           	 
        }); 

        $(document.body).on('keyup','#link_caption',function(){  
        	var link_caption=$("#link_caption").val();               
			$(".preview_og_info_title").html(link_caption);	 
			
        });  

        $(document.body).on('keyup','#link_description',function(){  
        	var link_description=$("#link_description").val();            
			$(".preview_og_info_desc").html(link_description);	 
			
        }); 

 	    var image_upload_limit = "<?php echo $image_upload_limit; ?>";
 	    var video_upload_limit = "<?php echo $video_upload_limit; ?>";

	    $("#link_preview_upload").uploadFile({
	        url:base_url+"ultrapost/cta_post_upload_link_preview",
	        fileName:"myfile",
	        maxFileSize:image_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1, 
	        acceptFiles:".png,.jpg,.jpeg",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('ultrapost/cta_post_delete_uploaded_file');?>";
                $.post(delete_url, {op: "delete",name: data},
                    function (resp,textStatus, jqXHR) {                         
                    });
	           
	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               var data_modified = base_url+"upload_caster/ctapost/"+data;
	               $("#link_preview_image").val(data_modified);	
	               $(".preview_img").attr("src",data_modified);	
	           }
	    });	
		


	     $(document.body).on('click','#submit_post',function(){ 
          	         
    		if($("#link").val()=="")
    		{
    			alertify.alert('<?php echo $this->lang->line("Alert");?>',"<?php echo $this->lang->line('Please paste a link to post.');?>",function(){ });
    			return;
    		}

    		if($("#cta_value").val()=="" || $("#cta_type").val()=="")
    		{
    			alertify.alert('<?php echo $this->lang->line("Alert");?>',"<?php echo $this->lang->line('Please select cta button type and enter cta button action link.');?>",function(){ });
    			return;
    		}
    

          	var auto_share_post = $("input[name=auto_share_post]:checked").val();
        	var auto_share_this_post_by_pages = $("#auto_share_this_post_by_pages").val();
        	if((auto_share_post=='1' && auto_share_this_post_by_pages==null) && $("input[name=auto_share_to_profile]:checked").val() == "No")
        	{
        		alertify.alert('<?php echo $this->lang->line("Alert");?>',"<?php echo $this->lang->line('Please select pages to publish this post.');?>",function(){ });
        		return;
        	}
        	
        	var schedule_type = $("input[name=schedule_type]:checked").val();
        	var schedule_time = $("#schedule_time").val();
        	var time_zone = $("#time_zone").val();
        	if(schedule_type=='later' && (schedule_time=="" || time_zone==""))
        	{
        		alertify.alert('<?php echo $this->lang->line("Alert");?>',"<?php echo $this->lang->line('Please select schedule time/time zone.');?>",function(){ });
        		return;
        	}

        	$("#submit_post").html('<?php echo $this->lang->line("Processing..."); ?>');    	
        	$("#submit_post").addClass("disabled"); 
        	$("#response_modal_content").removeClass("alert-danger");
        	$("#response_modal_content").removeClass("alert-success");
        	var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
        	$("#response_modal_content").html(loading);
        	$("#response_modal").modal();

		      var queryString = new FormData($("#cta_poster_form")[0]);
		      $.ajax({
		       type:'POST' ,
		       url: base_url+"ultrapost/edit_cta_post_action",
		       data: queryString,
		       dataType : 'JSON',
		       // async: false,
		       cache: false,
		       contentType: false,
		       processData: false,
		       success:function(response)
		       {  		         
		       		$("#submit_post").removeClass("disabled");
		         	$("#submit_post").html('<i class="fa fa-send"></i> <?php echo $this->lang->line("Submit Post"); ?>');    

		         	var report_link="<br/><a href='"+base_url+"ultrapost/cta_post'><?php echo $this->lang->line('Click here to see report'); ?></a>";

		         	if(response.status=="1")
			        {
			         	$("#response_modal_content").removeClass("alert-danger");
			         	$("#response_modal_content").addClass("alert-success");
			         	$("#response_modal_content").html(response.message+report_link);
			        }
			        else
			        {
			         	$("#response_modal_content").removeClass("alert-success");
			         	$("#response_modal_content").addClass("alert-danger");
			         	$("#response_modal_content").html(response.message+report_link);
			        }

		       }

		      });

        });



    });



</script>
<div class="modal fade" id="response_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $this->lang->line('Update Campaign Status'); ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert text-center" id="response_modal_content">
				</div>
			</div>
		</div>
	</div>
</div>



<style type="text/css" media="screen">
	/* .box-header{border-bottom:1px solid #ccc !important;margin-bottom:15px;} */
	/* .box-primary{border:1px solid #ccc !important;} */
	/* .box-footer{border-top:1px solid #ccc !important;} */
	.padding-5{padding:5px;}
	.padding-20{padding:20px;}
	.box-body,.box-footer{padding:20px;}
	.box-header{padding-left: 20px;}
	.preview
	{
		font-family: helvetica,​arial,​sans-serif;
		padding: 20px;
	}
	.preview_cover_img
	{
		width:45px;
		height:45px;
		border: .5px solid #ccc;
	}
	.preview_page
	{
		padding-left: 7px;
		color: #365899;
		font-weight: 700;
		font-size: 14px;
		cursor: pointer;
	}
	.preview_page_sm
	{
		padding-left: 7px;
		padding-top: 7px;
		color: #9197a3;
		font-size: 13px;
		font-weight: 300;
		cursor: pointer;
	}
	.preview_img
	{
		width:100%;
		border: 1px solid #ccc;
		border-bottom: none;
		cursor: pointer;
	}		
	.preview_og_info
	{
		border: 1px solid #ccc;
		box-shadow: 0px 0px 2px #ddd;
		-webkit-box-shadow: 0px 0px 2px #ddd;
		-moz-box-shadow: 0px 0px 2px #ddd;
		padding: 10px;
		cursor: pointer;

	}
	.preview_og_info_title
	{
		font-size: 23px;
		font-weight: 400;
		font-family: 'Times New Roman',helvetica,​arial;
		
	}
	.preview_og_info_desc
	{
		margin-top: 5px;
		font-size: 13px;
	}
	.preview_og_info_link
	{
		text-transform: uppercase;
		color: #9197a3;
		margin-top: 7px;
		font-size: 10px;
	}
	.ms-choice span
	{
		padding-top: 2px !important;
	}
	.hidden
	{
		display: none;
	}
	.btn-default
	{
		background: #fff;
		border-color: #ccc;
		border-radius: 2px;
		-moz-border-radius: 2px;
		-webkit-border-radius: 2px;
		padding: 3px 5px;
		color: #555;
	}
	.btn-default:hover
	{
		background: #eee;
		border-color: #ccc;
		color: #555;
	}
	.box-primary
	{
		-webkit-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
		-moz-box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
		box-shadow: 0px 2px 14px -5px rgba(0,0,0,0.75);
	}
	.content-wrapper{background: #fff;}
	.ajax-upload-dragdrop{width:100% !important;}
</style>