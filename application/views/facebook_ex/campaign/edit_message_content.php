<div class="row padding-20">
	<div class="col-xs-12 col-md-7 padding-10">
		<div class="box box-primary">
			<div class="box-header ui-sortable-handle  text-center" style="cursor: move;margin-bottom: 0px;">
				<i class="fa fa-edit"></i>
				<h3 class="box-title"><?php echo $this->lang->line("edit").' '.$this->lang->line("message") ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body">
				<img class="wait_few_seconds center-block" src="<?php echo base_url("assets/pre-loader/Fading squares2.gif");?>" alt="">
				<form action="#" enctype="multipart/form-data" id="inbox_campaign_form" method="post">
					<input type="hidden" value="<?php echo $xdata[0]["id"];?>" class="form-control"  name="campaign_id" id="campaign_id">
					
					<div class="form-group">
						<label>
							<?php echo $this->lang->line("message") ?> *
							<a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("message") ?>" data-content="<?php echo $this->lang->line("Message may contain texts, urls and emotions.You can include #LEAD_USER_NAME# variable by clicking 'Include Lead User Name' button. The variable will be replaced by real names when we will send it. If you want to show links or youtube video links with preview, then you can use 'Paste URL' OR 'Paste Youtube Video URL' fields below. Remember that if you put url/link inside this message area, preview of 'Paste URL' OR 'Paste Youtube Video ID' will not work. Then, the first url inside this message area will be previewed."); ?> Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
						</label>
						<span class='pull-right'> 
							<a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user last name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
							<a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm' id="lead_last_name"><i class='fa fa-user'></i> <?php echo $this->lang->line("include") ?> "<?php echo $this->lang->line("last name") ?>"</a>
						</span>
						<span class='pull-right'> 
							<a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user first name") ?>"" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
							<a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm' id="lead_first_name"><i class='fa fa-user'></i> <?php echo $this->lang->line("include") ?> "<?php echo $this->lang->line("first name") ?>"</a>
						</span>
						<div class="clearfix"></div>
						<textarea class="form-control" name="message" id="message" placeholder="<?php echo $this->lang->line("type your message here...") ?>" style="height:170px;"><?php echo $xdata[0]["campaign_message"];?></textarea>
						
					</div>
					
					<div class="form-group">
						<label>
							<?php echo $this->lang->line("paste url") ?> <small>(<?php echo $this->lang->line("will be attached & previewed") ?>)</small>
							<a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("paste url") ?>" data-content="<?php echo $this->lang->line("Paste any url, make sure your url contains http:// or https://. This url will be attched after your message with preview.") ?>"><i class='fa fa-info-circle'></i> </a>
						</label>
						<input value="<?php echo $xdata[0]["attached_url"];?>" class="form-control" name="link" id="link"  type="text" placeholder="http://example.com">
					</div>	

					<div class="form-group text-center">
						<h4 style="margin:0" title="<?php echo $this->lang->line("eiher url or video will be previewed and attached at the bottom of message") ?>"><?php echo $this->lang->line("or") ?></h4>
					</div>	

					<div class="form-group">
						<label>
							<?php echo $this->lang->line("paste youtube video url") ?>  <small>(<?php echo $this->lang->line("will be attached & previewed") ?>)</small>
							<a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("paste youtube video url") ?>" data-content="<?php echo $this->lang->line("Paste any Youtube video URL, make sure your youtube url looks like https://www.youtube.com/watch?v=VIDEO_ID or https://youtu.be/VIDEO_ID. This video url will be attched after your message with preview.") ?>"><i class='fa fa-info-circle'></i> </a>
						</label>
						<input value="<?php echo $xdata[0]["attached_video"];?>" class="form-control" name="video_url" id="video_url" type="text" placeholder="https://www.youtube.com/watch?v=VIDEO_ID"> 
					</div>

					<br/>
					<img id="preview_loading" class="loading center-block" src="<?php echo base_url("assets/pre-loader/Fading squares2.gif");?>" alt="">
				    <div class="clearfix"></div>
					<br/>
					
					<div class="alert alert-danger text-center" id="alert_div" style="display: none; font-size: 600;"></div>
				
					<div class="clearfix"></div>
					<div class="box-footer clearfix" style="border:none !important;">
						<!-- <div class="col-xs-12"> -->
							<button style='width:100%;margin-bottom:10px;' class="btn btn-primary center-block btn-lg" id="submit_post" name="submit_post" type="button"><i class="fa fa-edit"></i> <?php echo $this->lang->line("edit") ?>  <?php echo $this->lang->line("message") ?> </button>
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
				<h3 class="box-title"><?php echo $this->lang->line("inbox preview") ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body preview">					
				<img class="wait_few_seconds center-block" src="<?php echo base_url("assets/pre-loader/Fading squares2.gif");?>" alt="">
				<div class="chat_box">
					<div class="chat_header">
						<span class='pull-left' id="page_name"><?php echo $this->lang->line("page name") ?></span>
						<span class='pull-right'> <i class="fa fa-cog"></i> <i class="fa fa-remove"></i> </span>
					</div>
					<div class="chat_body">
						<img id="page_thumb" class="pull-left" src="<?php echo base_url("assets/images/chat_box_thumb.png");?>">
						<span id="preview_message" class="pull-left"><span id="preview_message_plain"><?php echo $this->lang->line("your message goes here...") ?></span><span id="preview_message_link"></span></span>
						<div class="clearfix"></div>
						<div id="video_thumb" class="pull-left">
							<div id="video_embed">
								<!-- <iframe width="100%" height="100" src="https://www.youtube.com/embed/SP8o501ORJ4" frameborder="0" allowfullscreen></iframe> -->
							</div>
							<div id="video_info">								
								<div id="video_info_title"></div>
								<div id="video_info_description"></div>
								<div id="video_info_youtube">youtube.com</div>
							</div>
						</div>

						<div class="clearfix"></div>
						<div id="link_thumb" class="pull-left">
							<div class="col-xs-5" id="link_embed"></div>
							<div class="col-xs-7" id="link_info">
								<div id="link_info_title"></div>
								<div id="link_info_description"></div>
								<div id="link_info_website"></div>
							</div>	
						</div>

					</div>
					<div class="chat_footer">
						<img src="<?php echo base_url("assets/images/chat_box.png");?>" class="img-responsive">
					</div>
				</div>
			</div>			
		</div>

		<div class="box box-primary">
			<div class="box-header ui-sortable-handle  text-center" style="cursor: move;margin-bottom: 0px;">
				<i class="fa fa-cogs"></i>
				<h3 class="box-title"><?php echo $this->lang->line("send test message") ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body" id="test_msg_box_body">
				<div class="alert" id="test_send_modal_content">
					<form id="test_message_form">
						<img id="test_loading" class="loading center-block" src="<?php echo base_url("assets/pre-loader/Fading squares2.gif");?>" alt="">
						<h4><div id="test_message_response" class="table-responsive"></div></h4>
						<div class="form-group">
	                        <label class="text-center">
	                       		<?php echo $this->lang->line("choose up to 3 leads to test how it will look. Start typing, it's auto-complete.") ?>                       	
	                        </label>
	                        <select style="width:100px;"  name="test_send[]" id="test_send" multiple="multiple" class="tokenize-sample form-control test_send_autocomplete">                                     
	                        </select>
	                    </div>
	                    <div>
							<button class="btn btn-primary" id="submit_test_post" name="submit_test_post" type="button"><i class="fa fa-envelope"></i> <?php echo $this->lang->line("send test message") ?> </button>
						</div> 
					</form>
				</div>
			</div>
		</div>
		
	</div> <!-- end of col-6 right part -->

</div>

<?php 
	
	$listseemsargighlyrecommendforcreatecustomcampaign = $this->lang->line("The list seems large. We highly recommend to split your campaign with small campaign with 300 leads per campaign.For create custom campaign,");
	$weillubmitllleadfoendmessagethapphaacereveendessaghigolumeat = $this->lang->line("Anyway we will submit all leads for sending message. But it may happen that facebook prevent sending message to high volume at a time. Use dealy 10 or more for safety.");
	$gohere = $this->lang->line("go here");
	$sorryyourmonthlylimittosendmessageisexceeded = $this->lang->line("sorry, your monthly limit to send message is exceeded.");


	$pleasehooseanyleadtosendtestmessage = $this->lang->line("please choose any lead to send test message.");
	$pleaypessagsteurlideourlystemcannotendlanessage = $this->lang->line("Please type a message or paste url/video url. system can not send blank message.");
	$pleaseelectpagetoreateinboxampaign = $this->lang->line("please select pages to create inbox campaign.");
	$pleaseselectcheduleimeimezone = $this->lang->line("please select schedule time/time zone.");
	$campaignhavebeenupdatedsuccessfully = $this->lang->line("campaign have been updated successfully.");
	$seereport = $this->lang->line("see report");
	$urlisinvalid = $this->lang->line("url is invalid.");
	$youtubeurlisinvalid = $this->lang->line("youtube URL is invalid.");
	$pleaseselectpagestocreateinboxcampaign = $this->lang->line("Please select pages to create inbox campaign.");

 ?>

<script>

 
	$j("document").ready(function(){
	
		var emoji_message_div =	$j("#message").emojioneArea({
        	autocomplete: false,
			pickerPosition: "bottom"
			//hideSource: false,
     	 });


		var base_url="<?php echo base_url();?>";


		$("#monthly_message_send_limit,#test_loading").hide();

		setTimeout(function() {
			$(".loading").hide();
			$(".wait_few_seconds").hide();
			$(".emojionearea-editor").blur();
			$("#message,#link,#video_url").blur();	
		}, 3000);

		$('[data-toggle="popover"]').popover(); 
		$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

		$(".overlay").hide();


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


        function message_change()
        {
        	var message=$("#message").val();
        	message=htmlspecialchars(message);
			message=message.replace(/[\r\n]/g, "<br />");

        	if( $("#preview_message_link").html() != "") message = message + "<br/>";

        	$("#preview_message").show();
        	$("#preview_message_plain").show();

        	var words = message.split(" ");    
    		var img;
    		var src;
    		for (var i = 0; i < words.length; i++) 
    		{
			    words[i] = words[i].replace(/"/g,""); // replce all " from message

			    if(typeof($(".emotion[eval=\""+words[i]+"\"]").attr("title"))==='undefined') continue;			    
			    
		    	src = $(".emotion[eval=\""+words[i]+"\"]").attr("title");	
		    	src =  "<?php echo base_url('assets/images/emotions-fb');?>/"+src+".gif";	    	
		    	img= "<img src='"+src+"'>";
		    	message = message.replace(words[i], img);			    
			}	

        	$("#preview_message_plain").html(message).text();
        	if(message=="" && $("#preview_message_link").html() == "") $("#preview_message").hide(); 
        }

		
		 $(document.body).on('keyup','.emojionearea-editor',message_change); 
	     $(document.body).on('blur','.emojionearea-editor',message_change);
		 

          $(document.body).on('click','#lead_first_name',function(){  
		    var textAreaTxt = $(".emojionearea-editor").html();
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
			
		    var txtToAdd = " #LEAD_USER_FIRST_NAME# ";
		    $(".emojionearea-editor").html(textAreaTxt + txtToAdd );
			$(".emojionearea-editor").click(); 
		    $("#preview_message_plain").html($("#message").val());
			
		});


		$(document.body).on('click','#lead_last_name',function(){  
		    var textAreaTxt = $(".emojionearea-editor").html();
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
				
		    var txtToAdd = " #LEAD_USER_LAST_NAME# ";
		    $(".emojionearea-editor").html(textAreaTxt + txtToAdd );

			$(".emojionearea-editor").click(); 
			$("#preview_message_plain").html($("#message").val());
			
		});
		
		

   
 		$(document.body).on('blur','#link',function(){  
        	var link=$("#link").val();  
        	var urlisinvalid = "<?php echo $urlisinvalid; ?>";  
        	
	        if(link!='')
	        {
	            $("#preview_loading").show();
	            $.ajax({
	            type:'POST' ,
	            url:"<?php echo site_url();?>facebook_ex_campaign/link_grabber",
	            data:{link:link},
	            dataType : 'JSON',
	            success:function(response){	 

	            	$("#preview_loading").hide();          		                
	             
               	 	if(response.status=='0')
               	 	{
               	 		alertify.alert('<?php echo $this->lang->line("Alert");?>','<?php echo $this->lang->line("URL is invalid.");?>',function(){ });
               	 		$("#link").val("");
               	 		$("#link_thumb").hide();
               	 		$("#preview_message").css("-webkit-border-radius","10px");       
            			$("#preview_message").css("-moz-border-radius","10px");       
            			$("#preview_message").css("border-radius","10px"); 
               	 	}
               	 	else
               	 	{
            			if(response.image=="") response.image= "<?php echo base_url('assets/images/chat_box_thumb2.png');?>";
            			$("#link_embed").html("<img src='"+response.image+"'>");
            			$("#link_info_title").html(response.title);
            			$("#link_info_description").html(response.description);
            			var link_author=link;
            			link_author = link_author.replace("http://", ""); 
	                	link_author = link_author.replace("https://", ""); 
	                	link_author = link_author.replace("www.", ""); 
	                	
	                	if($("#message").val() == "")
            			$("#preview_message_link").html("<a href='"+link+"' target='_BLANK'>"+link+"</a>").show();
            			else $("#preview_message_link").html("<br/><a href='"+link+"' target='_BLANK'>"+link+"</a>").show();
            			
            			$("#link_info_website").html(link_author);
            			$("#link_thumb").show(); 
            			$("#video_thumb").hide(); 
            			$("#video_url").val("");

            			if( $("#message").val() == "") 
            			$("#preview_message_plain").hide();            	
            			else 
            			{
            				$("#preview_message").css("-webkit-border-radius","10px 10px 10px 0");       
            				$("#preview_message").css("-moz-border-radius","10px 10px 10px 0");       
            				$("#preview_message").css("border-radius","10px 10px 10px 0");       
            			}
               	 	}
                             
	            }
	        }); 	            
	        }
	        else 
	       	{
	       		$("#link_thumb").hide(); 
	       		$("#preview_message_link").hide();
	       		$("#preview_message").css("-webkit-border-radius","10px");       
            	$("#preview_message").css("-moz-border-radius","10px");       
            	$("#preview_message").css("border-radius","10px"); 
	       	}     		      
            
        });


        $(document.body).on('blur','#video_url',function(){  
        	var link=$("#video_url").val();  
        	var youtubeurlisinvalid = "<?php echo $youtubeurlisinvalid; ?>";  
	        if(link!='')
	        {
	            $("#preview_loading").show();
	            $.ajax({
	            type:'POST' ,
	            url:"<?php echo site_url();?>facebook_ex_campaign/youtube_video_grabber",
	            data:{link:link},
	            dataType : 'JSON',
	            success:function(response){	           		                
	             	
	       			$("#preview_loading").hide();   
               	 	if(response.status=='0')
               	 	{
               	 		alertify.alert('<?php echo $this->lang->line("Alert");?>','<?php echo $this->lang->line("Youtube URL is invalid.");?>',function(){ });
               	 		$("#video_url").val("");
               	 		$("#video_thumb").hide();
               	 	}
               	 	else
               	 	{
            			$("#video_embed").html(response.video_embed);
            			$("#video_info_title").html(response.title);
            			$("#video_info_description").html(response.description);
            			
            			if($("#message").val() == "")
            			$("#preview_message_link").html("<a href='"+link+"' target='_BLANK'>"+link+"</a>").show();
            			else $("#preview_message_link").html("<br/><a href='"+link+"' target='_BLANK'>"+link+"</a>").show();
            			
            			$("#video_thumb").show(); 
            			$("#link_thumb").hide(); 
            			$("#link").val("");

            			if( $("#message").val() == "") 
            			$("#preview_message_plain").hide();
            				
            			else 
            			{
            				$("#preview_message").css("-webkit-border-radius","10px 10px 10px 0");       
            				$("#preview_message").css("-moz-border-radius","10px 10px 10px 0");       
            				$("#preview_message").css("border-radius","10px 10px 10px 0");       
            			}
               	 	}
                             
	            }
	        }); 	            
	        }	
	        else 
	       	{
	       		$("#video_thumb").hide(); 
	       		$("#preview_message_link").hide();
	       		$("#preview_message").css("-webkit-border-radius","10px");       
            	$("#preview_message").css("-moz-border-radius","10px");       
            	$("#preview_message").css("border-radius","10px"); 
	       	}  
            
        });

        $('.test_send_autocomplete').tokenize({
            datas: base_url+"facebook_ex_campaign/lead_autocomplete/0",
            maxElements : 3
        });


	    $(document.body).on('click','#submit_test_post',function(){ 
	    	var thread_ids = $('.test_send_autocomplete').tokenize().toArray();
	    	var pleasehooseanyleadtosendtestmessage = "<?php echo $pleasehooseanyleadtosendtestmessage; ?>";
	    	var pleaypessagsteurlideourlystemcannotendlanessage = "<?php echo $pleaypessagsteurlideourlystemcannotendlanessage; ?>";
	    	if(thread_ids.length==0) 
	    	{
	    		alertify.alert('<?php echo $this->lang->line("Alert");?>',pleasehooseanyleadtosendtestmessage,function(){ });
	    	 	return;
	    	}
	    	var message = $("#message").val();
	    	var link = $("#link").val();
	    	var video_url = $("#video_url").val();

	    	if(message=="" && link==""&&  video_url=="")
    		{
    			alertify.alert('<?php echo $this->lang->line("Alert");?>',pleaypessagsteurlideourlystemcannotendlanessage,function(){ });
    			return;
    		} 
    	    $("#test_loading").show();
    	    $("#submit_test_post").addClass("disabled");
	        $.ajax({
		       type:'POST' ,
		       url: base_url+"facebook_ex_campaign/send_test_message",
		       data: {message:message,link:link,video_url:video_url,thread_ids:thread_ids},
		       success:function(response)
		       {  	    	 			
	 			  $("#test_loading").hide();
	 			  $("#submit_test_post").removeClass("disabled");
	 			  $("#test_message_response").html(response);
		       	  
		       }
	      	});
	    });



	    $(document.body).on('click','#submit_post',function(){ 
       		var pleaypessagsteurlideourlystemcannotendlanessage = "<?php echo $pleaypessagsteurlideourlystemcannotendlanessage; ?>";
       		var pleaseelectpagetoreateinboxampaign = "<?php echo $pleaseelectpagetoreateinboxampaign; ?>";
       		var pleaseselectcheduleimeimezone = "<?php echo $pleaseselectcheduleimeimezone; ?>";
       		var campaignhavebeenupdatedsuccessfully = "<?php echo $campaignhavebeenupdatedsuccessfully; ?>";
       		var seereport = "<?php echo $seereport; ?>";
                  	
    		if($("#message").val()=="" && $("#link").val()==""&&  $("#video_url").val()=="")
    		{
    			alertify.alert('<?php echo $this->lang->line("Alert");?>',pleaypessagsteurlideourlystemcannotendlanessage,function(){ });
    			return;
    		}    
      
        	$("#response_modal_content").removeClass("alert-danger");
        	$("#response_modal_content").removeClass("alert-success");
        	var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
        	$("#response_modal_content").html(loading);

        	var report_link = base_url+"facebook_ex_campaign/campaign_report";
        	
        	var success_message = "<i class='fa fa-check-circle'></i> <?php echo $this->lang->line('Campaign have been submitted successfully.'); ?> <a href='"+report_link+"'><?php echo $this->lang->line('See report'); ?></a>";

        	$("#response_modal_content").removeClass("alert-danger");
         	$("#response_modal_content").addClass("alert-success");
         	$("#response_modal_content").html(success_message);
       	        	
		      var queryString = new FormData($("#inbox_campaign_form")[0]);
		      $.ajax({
			       type:'POST' ,
			       url: base_url+"facebook_ex_campaign/edit_message_content_action",
			       data: queryString,
			       cache: false,
			       contentType: false,
			       processData: false,
			       success:function(response)
			       {  
			       }
		      	});
		      $("#response_modal").modal();
		      $(this).addClass("disabled");

				// var delay=2000;
				// setTimeout(function() {
				// 	window.location.href=report_link;
				// }, delay);

        });

 		//  $('#response_modal').on('hidden.bs.modal', function () { 
		// var link=base_url+"facebook_ex_campaign/campaign_report";
		// window.location.assign(link); 
		// })



    });

</script>



<div class="modal fade" id="response_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title text-center"><?php echo $this->lang->line("campaign status") ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert text-center" id="response_modal_content">
					
				</div>
			</div>
		</div>
	</div>
</div>


<?php $this->load->view("facebook_ex/campaign/style");?>