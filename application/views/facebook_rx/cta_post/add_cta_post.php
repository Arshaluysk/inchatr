<?php $this->load->view("include/upload_js"); ?>
<div class="row padding-20">
	<div class="col-xs-12 col-md-6 padding-5">
		<div class="box box-primary">
			<div class="box-header ui-sortable-handle" style="cursor: move;">
				<i class="fa fa-paper-plane "></i>
				<h3 class="box-title"><?php echo $this->lang->line("cta (call to action) poster") ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body">
				<form action="#" enctype="multipart/form-data" id="cta_poster_form" method="post">
					<div class="form-group">
						<label><?php echo $this->lang->line("campaign name") ?></label>
						<input type="input" class="form-control"  name="campaign_name" id="campaign_name">
					</div>

					<div class="form-group">
						<label><?php echo $this->lang->line("message") ?></label>
						<textarea class="form-control" name="message" id="message" placeholder="<?php echo $this->lang->line("type your message here...") ?>"></textarea>
					</div>
					
					<div class="form-group">
						<label><?php echo $this->lang->line("paste link") ?></label>
						<input class="form-control" name="link" id="link"  type="text">
					</div>
					<div class="form-group hidden">
						<label><?php echo $this->lang->line("preview image url") ?></label>
						<input class="form-control" name="link_preview_image" id="link_preview_image" type="text"> 
					</div>					
					<div class="form-group hidden">      
                         <div id="link_preview_upload"><?php echo $this->lang->line('upload');?></div>                              
                        <br/>
                    </div>
					<div class="form-group hidden">
						<label><?php echo $this->lang->line("title") ?></label>
						<input class="form-control" name="link_caption" id="link_caption" type="text"> 
					</div>	
					<div class="form-group hidden">
						<label><?php echo $this->lang->line("description") ?></label>
						<textarea class="form-control" name="link_description" id="link_description"></textarea>
					</div>

					<div class="form-group">
						<label><?php echo $this->lang->line("cta button type") ?></label>
						<?php echo form_dropdown("cta_type",$cta_dropdown,"MESSAGE_PAGE","class='form-control' id='cta_type'");?>
					</div>

					<div class="form-group cta_value_container_div">
						<label><?php echo $this->lang->line("cta button action link") ?></label>
						<input type="input" class="form-control"  name="cta_value" id="cta_value">
					</div>

						 <?php 
						 	$facebook_rx_fb_user_info_id=isset($fb_user_info[0]["id"]) ? $fb_user_info[0]["id"] : 0; 
						 	$facebook_rx_fb_user_info_name=isset($fb_user_info[0]["name"]) ? $fb_user_info[0]["name"] : ""; 
						 	$facebook_rx_fb_user_info_access_token=isset($fb_user_info[0]["access_token"]) ? $fb_user_info[0]["access_token"] : ""; 
						 ?>

					<div class="form-group">
						<label><?php echo $this->lang->line("post to pages") ?></label>
						<select multiple="multiple"  class="form-control" id="post_to_pages" name="post_to_pages[]">	
						<?php
							foreach($fb_page_info as $key=>$val)
							{	
								$id=$val['id'];
								$page_name=$val['page_name'];
								echo "<option value='{$id}'>{$page_name}</option>";								
							}
						 ?>						
						</select>
					</div>	


					<?php 
					 	if($this->session->userdata("user_type")=="Admin" || in_array(74,$this->module_access)) $like_comment_Share_reply_block_class=""; 
					 	else $like_comment_Share_reply_block_class="hidden";
					?>
					<div id="like_comment_Share_reply_block" style="margin-top:30px !important" class="<?php echo $like_comment_Share_reply_block_class;?>">
						<div class="form-group">
							<label><?php echo $this->lang->line("auto share this post") ?></label><br/>
							<input name="auto_share_post" value="1" id="auto_share_post_enable" type="radio"> <?php echo $this->lang->line("enable") ?>&nbsp;&nbsp;&nbsp;&nbsp;
							<input name="auto_share_post" value="0" id="auto_share_post_disable" type="radio" checked> <?php echo $this->lang->line("disable") ?>
						</div>

						<div class="form-group auto_share_post_block_item">
							<label><?php echo $this->lang->line("auto share to timeline") ?></label><br/>
							<input name="auto_share_to_profile" value="<?php echo $facebook_rx_fb_user_info_id;?>" id="auto_share_to_profile_yes"  type="radio"> <?php echo $this->lang->line("share to timeline") ?> (<?php echo $facebook_rx_fb_user_info_name;?>) &nbsp;&nbsp;&nbsp;&nbsp;
							<input name="auto_share_to_profile" value="No" id="auto_share_to_profile_no" type="radio" checked> No, don't share
						</div>		

						<div class="form-group auto_share_post_block_item">
							<label><?php echo $this->lang->line("auto share as pages") ?></label>
							<select multiple="multiple"  class="form-control" id="auto_share_this_post_by_pages" name="auto_share_this_post_by_pages[]">	
							<?php
								foreach($fb_page_info as $key=>$val)
								{	
									$id=$val['id'];
									$page_name=$val['page_name'];
									echo "<option value='{$id}'>{$page_name}</option>";								
								}
							 ?>						
							</select>
						</div>	
						<br/>	


						<div class="form-group">
							<label><?php echo $this->lang->line("Auto like this post as all other pages") ?></label><br/>
							<input name="auto_like_post" value="1" id="auto_like_post_enable" type="radio"> <?php echo $this->lang->line("enable") ?>&nbsp;&nbsp;&nbsp;&nbsp;
							<input name="auto_like_post" value="0" id="auto_like_post_disable" type="radio" checked> <?php echo $this->lang->line("disable") ?>
						</div>
						<br/>

						<div class="form-group">
							<label><?php echo $this->lang->line("auto private reply on user comments") ?></label><br/>
							<input name="auto_private_reply" value="1" id="auto_private_reply_enable"  type="radio"> <?php echo $this->lang->line("enable") ?>&nbsp;&nbsp;&nbsp;&nbsp;
							<input name="auto_private_reply" value="0" id="auto_private_reply_disable" type="radio" checked> <?php echo $this->lang->line("disable") ?>
						</div>

						<div class="form-group auto_reply_block_item">
							<label><?php echo $this->lang->line("private reply") ?></label>
							<textarea class="form-control" name="auto_private_reply_text" id="auto_private_reply_text"></textarea>
						</div>	
						<br/>

						<div class="form-group">
							<label><?php echo $this->lang->line("auto comment") ?></label><br/>
							<input name="auto_comment" value="1" id="auto_comment_enable"  type="radio"> <?php echo $this->lang->line("enable") ?>&nbsp;&nbsp;&nbsp;&nbsp;
							<input name="auto_comment" value="0" id="auto_comment_disbale" type="radio" checked> <?php echo $this->lang->line("disable") ?>
						</div>
						<div class="form-group auto_comment_block_item">
							<label><?php echo $this->lang->line("comment") ?></label>
							<textarea class="form-control" name="auto_comment_text" id="auto_comment_text"></textarea>
						</div>
						<br/>
					</div>
		
					

					<div class="form-group">
						<label><?php echo $this->lang->line("schedule") ?></label><br/>
						<input name="schedule_type" value="now" id="schedule_now" checked type="radio"> <?php echo $this->lang->line("now") ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input name="schedule_type" value="later" id="schedule_later" type="radio"> <?php echo $this->lang->line("later") ?> 
					</div>

					<div class="form-group schedule_block_item">
						<label><?php echo $this->lang->line("schedule time") ?></label>
						<input placeholder="<?php echo $this->lang->line("") ?>Time"  name="schedule_time" id="schedule_time" class="form-control datepicker" type="text"/>
					</div>

					<div class="form-group schedule_block_item">
						<label><?php echo $this->lang->line("time zone") ?></label>
						<?php
						$time_zone[''] = 'Please Select';
						echo form_dropdown('time_zone',$time_zone,set_value('time_zone'),' class="form-control" id="time_zone" required'); 
						?>
					</div>	

					<div class="box-footer clearfix">
						<button class="btn btn-primary" submit_type="text_submit" id="submit_post" name="submit_post" type="button"><i class="fa fa-send"></i> <?php echo $this->lang->line("submit post") ?> </button>
					</div>

				</form>
			</div>
			
		</div>
	</div>  <!-- end of col-6 left part -->

	<div class="col-xs-12 col-md-6 padding-5">
		<div class="box box-primary">
			<div class="box-header ui-sortable-handle" style="cursor: move;">
				<i class="fa fa-facebook-official"></i>
				<h3 class="box-title"><?php echo $this->lang->line("preview") ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body preview">	
				<?php $profile_picture="https://graph.facebook.com/me/picture?access_token={$facebook_rx_fb_user_info_access_token}&width=150&height=150"; ?>				
				<img src="<?php echo $profile_picture;?>" class="preview_cover_img inline pull-left text-center" alt="X">
				<span class="preview_page"><?php echo $facebook_rx_fb_user_info_name;?></span><br/>
				<span class="preview_page_sm">Now <?php echo isset($app_info[0]['app_name']) ? $app_info[0]['app_name'] : $this->config->item("product_short_name");?></span><br/><br/>	
				<span class="preview_message"><br/></span>	

				<img src="<?php echo base_url('assets/images/demo_post2.png');?>" class="preview_img" alt="No Image Preview">		
				<div class="preview_og_info clearfix">
					<div class="preview_og_info_title inline-block"></div>
					<div class="preview_og_info_desc inline-block">							
					</div>
					<div class="preview_og_info_link inline-block pull-left">							
					</div>
					<div class="button_container"><a class="cta-btn btn btn-sm btn-default pull-right"><?php echo $this->lang->line("message page") ?></a></div>
				</div>

			</div>
		</div>
	</div> <!-- end of col-6 right part -->

</div>

<?php 
	$Pleasepastealinktopost = $this->lang->line("please paste a link to post.");
	$Pleaseselecttabuttontypeandenterctabuttonactionlink = $this->lang->line("please select cta button type and enter cta button action link.");
	$Pleaseselectpagestopublishthispost = $this->lang->line("please select pages to publish this post.");
	$Pleaseselecttimelineorpagesforautosharing = $this->lang->line("please select timeline or page(s) for auto sharing.");
	$Pleasetypeprivatereplymessage = $this->lang->line("please type private reply message.");
	$Pleasetypeautocommentmessage = $this->lang->line("please type auto comment message.");
	$Pleaseselectscheduletimetimezone = $this->lang->line("please select schedule time/time zone.");

	$SubmitPost = $this->lang->line("submit post");
	$Processing = $this->lang->line("processing");
 ?>

<script>
	$j("document").ready(function(){

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

        $(".auto_share_post_block_item,.auto_reply_block_item,.auto_comment_block_item,.schedule_block_item,.cta_value_container_div").hide();
 
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
	        $.ajax({
	            type:'POST' ,
	            url:"<?php echo site_url();?>facebook_rx_cta_poster/meta_info_grabber",
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

	            	              
	            }
	        }); 
        });

        $(document.body).on('keyup','#message',function(){  
        	var message=$(this).val();
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

 	       

	    $("#link_preview_upload").uploadFile({
	        url:base_url+"facebook_rx_cta_poster/upload_link_preview",
	        fileName:"myfile",
	        maxFileSize:1*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1, 
	        acceptFiles:".png,.jpg,.jpeg",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('facebook_rx_cta_poster/delete_uploaded_file');?>";
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
          	 var Pleasepastealinktopost = "<?php echo $Pleasepastealinktopost; ?>";
          	 var Pleaseselecttabuttontypeandenterctabuttonactionlink = "<?php echo $Pleaseselecttabuttontypeandenterctabuttonactionlink; ?>";
          	 var Pleaseselectpagestopublishthispost = "<?php echo $Pleaseselectpagestopublishthispost; ?>";
          	 var Pleaseselecttimelineorpagesforautosharing = "<?php echo $Pleaseselecttimelineorpagesforautosharing; ?>";
          	 var Pleasetypeprivatereplymessage = "<?php echo $Pleasetypeprivatereplymessage; ?>";
          	 var Pleasetypeautocommentmessage = "<?php echo $Pleasetypeautocommentmessage; ?>";
          	 var Pleaseselectscheduletimetimezone = "<?php echo $Pleaseselectscheduletimetimezone; ?>";
    		if($("#link").val()=="")
    		{
    			alert(Pleasepastealinktopost);
    			return;
    		}

    		if($("#cta_value").val()=="" || $("#cta_type").val()=="")
    		{
    			alert(Pleaseselecttabuttontypeandenterctabuttonactionlink);
    			return;
    		}
    	
        	var post_to_pages = $("#post_to_pages").val();
        	if(post_to_pages==null)
        	{
        		alert(Pleaseselectpagestopublishthispost);
        		return;
        	}

          	var auto_share_post = $("input[name=auto_share_post]:checked").val();
        	var auto_share_this_post_by_pages = $("#auto_share_this_post_by_pages").val();
        	if((auto_share_post=='1' && auto_share_this_post_by_pages==null) && $("input[name=auto_share_to_profile]:checked").val() == "No")
        	{
        		alert(Pleaseselecttimelineorpagesforautosharing);
        		return;
        	}

        	var auto_private_reply = $("input[name=auto_private_reply]:checked").val();
        	var auto_private_reply_text = $("#auto_private_reply_text").val();
        	if(auto_private_reply=='1' && auto_private_reply_text=="")
        	{
        		alert(Pleasetypeprivatereplymessage);
        		return;
        	}

        	var auto_comment = $("input[name=auto_comment]:checked").val();
        	var auto_comment_text = $("#auto_comment_text").val();
        	if(auto_comment=='1' && auto_comment_text=="")
        	{
        		alert(Pleasetypeautocommentmessage);
        		return;
        	}

        	var schedule_type = $("input[name=schedule_type]:checked").val();
        	var schedule_time = $("#schedule_time").val();
        	var time_zone = $("#time_zone").val();
        	var Processing = "<?php echo $Processing; ?>";
        	if(schedule_type=='later' && (schedule_time=="" || time_zone==""))
        	{
        		alert(Pleaseselectscheduletimetimezone);
        		return;
        	}

        	$("#submit_post").html(Processing+'...');     	
        	$("#submit_post").addClass("disabled"); 
        	$("#response_modal_content").removeClass("alert-danger");
        	$("#response_modal_content").removeClass("alert-success");
        	var loading = '<img src="'+base_url+'assets/pre-loader/custom_lg.gif" class="center-block">';
        	$("#response_modal_content").html(loading);
        	$("#response_modal").modal();

		      var queryString = new FormData($("#cta_poster_form")[0]);
		      var SubmitPost = "<?php echo $SubmitPost; ?>";
		      $.ajax({
		       type:'POST' ,
		       url: base_url+"facebook_rx_cta_poster/add_cta_post_action",
		       data: queryString,
		       dataType : 'JSON',
		       // async: false,
		       cache: false,
		       contentType: false,
		       processData: false,
		       success:function(response)
		       {  		         
		       		$("#submit_post").removeClass("disabled");
		         	$("#submit_post").html('<i class="fa fa-send"></i> '+SubmitPost);    

		         	var report_link="<br/><a href='"+base_url+"facebook_rx_cta_poster/cta_post_list'>See Report</a>";

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
				<h4 class="modal-title"><?php echo $this->lang->line("auto post campaign status") ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert text-center" id="response_modal_content">
				</div>
			</div>
		</div>
	</div>
</div>



<style type="text/css" media="screen">
	.box-header{border-bottom:1px solid #ccc !important;margin-bottom:15px;}
	.box-primary{border:1px solid #ccc !important;}
	.box-footer{border-top:1px solid #ccc !important;}
	.padding-5{padding:5px;}
	.padding-20{padding:20px;}
	.box-header{color:#3C8DBC;}
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