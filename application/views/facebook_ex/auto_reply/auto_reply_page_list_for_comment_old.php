<?php 
	$this->load->view("include/upload_js"); 
	if(ultraresponse_addon_module_exist())	$commnet_hide_delete_addon = 1;
	else $commnet_hide_delete_addon = 0;

	if(addon_exist(201,"commenttagmachine")) $comment_tag_machine_addon = 1;
	else $comment_tag_machine_addon = 0;			
?>
<style type="text/css">
	::placeholder {
	  color: white !important;
	}
</style>

<style>
.smallspace{padding: 10px 0;}
.lead_first_name,.lead_last_name,.lead_tag_name{background: #fff !important;}
.ajax-file-upload-statusbar{width: 100% !important;}
	hr{
	   margin-top: 10px;
	}

	.custom-top-margin{
	  margin-top: 20px;
	}

	.sync_page_style{
	   margin-top: 8px;
	}
	/* .wrapper,.content-wrapper{background: #fafafa !important;} */
	.well{background: #fff;}
	.box-shadow
	{
	  -webkit-box-shadow: 0px 2px 14px -3px rgba(0,0,0,0.75);
	    -moz-box-shadow: 0px 2px 14px -3px rgba(0,0,0,0.75);
	    box-shadow: 0px 2px 14px -3px rgba(0,0,0,0.75);
	    border-bottom: 4px solid orange;
	}

	.info-box-icon {
	    border-top-left-radius: 2px;
	    border-top-right-radius: 0;
	    border-bottom-right-radius: 0;
	    border-bottom-left-radius: 2px;
	    display: block;
	    float: left;
	    height: 66px;
	    width: 50px;
	    text-align: center;
	    font-size: 30px;
	    line-height: 66px;
	    background: rgba(0,0,0,0.2);
	}

	.info-box {
	    display: block;
	    min-height: 67px;
	    background: #fff;
	    width: 100%;
	    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
	    border-radius: 2px;
	    margin-bottom: 5px;
	}
	.info-box-content
	{
		margin-left: 50px;
	}
</style>


<?php if(empty($page_info)){ ?>
     
<div class="well_border_left">
	<h4 class="text-center"> <i class="fa fa-facebook-official"></i> <?php echo $this->lang->line("you have no page in facebook");?><h4>
</div>

<?php }else{ ?>
   
<div class="well well_border_left">
	<h4 class="text-center blue"> <i class="fa fa-mail-reply-all"></i> <?php echo $this->lang->line("Auto Reply : Page List");?><h4>
</div>

<div class="row" style="padding:0 15px;">
	<?php $i=0; foreach($page_info as $value) : ?>
	<div class="col-xs-12 col-sm-12 col-md-6">
      <div class="box box-shadow box-solid" style="margin-top: 10px;">
        <div class="box-header with-border text-center">
          <h3 class="box-title blue"><?php echo $value['page_name']; ?></h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body" style="padding-top:20px">
          <div class="col-xs-12">
            <div class="row">
              <?php $profile_picture=$value['page_profile']; ?>
             
              <div class="col-xs-12 col-md-8">
                <div class="info-box">
                  <span class="info-box-icon bg-blue" style="background: <?php echo $THEMECOLORCODE;?> !important;"><i class="fa fa-mail-reply-all"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text" style="font-weight: normal; font-size: 12px;"><b><?php echo $this->lang->line("total auto comment enabled post");?></b></span><hr style="margin-bottom:2px;">
                    <span class="info-box-number">
                      <?php 
                      	echo number_format($value['auto_reply_enabled_post']);
                      ?>
                    </span>
                  </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->

                <div class="info-box">
                  <span class="info-box-icon bg-blue" style="background: <?php echo $THEMECOLORCODE;?> !important;"><i class="fa fa-send"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text" style="font-weight: normal; font-size: 12px;"><b><?php echo $this->lang->line("total auto comment sent");?></b></span><hr style="margin-bottom:2px;">
                    <span class="info-box-number">
                      <?php
                      	echo number_format($value['autoreply_count']);
                      ?>
                    </span>
                  </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
              	<button style="margin-top: 4px;border:1px solid #ccc;" class="pull-left manual_auto_reply btn btn-default btn-sm" page_name="<?php echo $value['page_name']; ?>" page_table_id="<?php echo $value['id']; ?>"><i class="fa fa-check-circle green"></i> <small><?php echo $this->lang->line("enable reply by post id") ?></small></button>
              	<button style="margin-top: 4px;border:1px solid #ccc;" class="pull-right manual_edit_reply btn btn-default btn-sm" page_name="<?php echo $value['page_name']; ?>" page_table_id="<?php echo $value['id']; ?>"><i class="fa fa-edit orange"></i> <small><?php echo $this->lang->line("edit reply by post id") ?></small></button>
              </div>    

              <div class="text-center col-xs-12 col-md-4">
                <img src="<?php echo $profile_picture;?>" alt="" style='height:80px;width:80px;' class="img-thumbnail" >
                <br>
                <small class="">
                    <?php 
	                    echo "<small>".$this->lang->line('last auto comment sent')."</small><br/>";
	                    if($value['last_reply_time']!="0000-00-00 00:00:00") echo "<span style='font-weight:normal;' class='label label-default'><i class='fa fa-clock-o'></i>  ".date("jS M, Y H:i:s a",strtotime($value['last_reply_time']))."<span>";
	                    else echo "<span style='font-weight:normal;' class='label label-default'><i class='fa fa-clock-o'></i>  {$this->lang->line('not replied yet')}</span>";
	                ?>
                  </small>
                
              	<a style="display: block; margin-top: 28px;border:1px solid #ccc;" target="_blank" href="<?php echo base_url('facebook_ex_auto_comment/auto_reply_report').'/'.$value['id']; ?>" class="btn btn-outline-default btn-sm view_repo"><i class="fa fa-list blue"></i> <small><?php echo $this->lang->line("View report") ?></small></a>
             
              </div>              
            </div><!-- /.row -->
           
            <div class="row">             
              <div class="col-xs-12" style='margin-top:10px;'> 
              	<button class="btn btn-outline-success get_post btn-block" style="margin-top:10px;"  table_id="<?php echo $value['id']; ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("get latest posts & enable auto comment") ?></button>
              </div> 
              
              <!-- <div class="col-xs-12 col-md-6 clearfix">
              	<small class="pull-right">
                    <?php 
	                    echo "<small>".$this->lang->line('last auto reply sent')."</small><br/>";
	                    if($value['last_reply_time']!="0000-00-00 00:00:00") echo "<span style='font-weight:normal;' class='label label-default'><i class='fa fa-clock-o'></i>  ".date("jS M, Y H:i:s a",strtotime($value['last_reply_time']))."<span>";
	                    else echo "<span style='font-weight:normal;' class='label label-warning'><i class='fa fa-clock-o'></i>  {$this->lang->line('not replied yet')}</span>";
	                ?>
                  </small>
              </div> --> 
            </div>
          </div>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
	<?php   
	$i++;
	if($i%2 == 0)
		echo "</div><div class='row' style='padding:0 15px;'>";
	endforeach;
	?>
</div>
<?php } ?>

<?php 
	
	$Youdidntprovideallinformation = $this->lang->line("you didn\'t provide all information.");
	$Pleaseprovidepostid = $this->lang->line("please provide post id.");
	$Youdidntselectanytemplate = $this->lang->line("you didn\'t select any template.");
	$Youdidntselectanyoptionyet = $this->lang->line("you didn\'t select any option yet.");
	$Youdidntselectanyoption = $this->lang->line("you didn\'t select any option.");
	
	$AlreadyEnabled = $this->lang->line("already enabled");
	$ThispostIDisnotfoundindatabaseorthispostIDisnotassociatedwiththepageyouareworking = $this->lang->line("This post ID is not found in database or this post ID is not associated with the page you are working.");
	$EnableAutoReply = $this->lang->line("enable auto reply");
	$TypeAutoCampaignname = $this->lang->line("You didn\'t Type auto campaign name");
	$YouDidnotchosescheduleType = $this->lang->line("You didn\'t choose any schedule type");
	$YouDidnotchosescheduletime = $this->lang->line("You didn\'t select any schedule time");
	$YouDidnotchosescheduletimezone = $this->lang->line("You didn\'t select any time zone");
	$YoudidnotSelectPerodicTime = $this->lang->line("You didn\'t select any periodic time");
	$YoudidnotSelectCampaignStartTime = $this->lang->line("You didn\'t choose campaign start time");
	$YoudidnotSelectCampaignEndTime = $this->lang->line("You didn\'t choose campaign end time");
	// $YoudidnotSelectAutoCommentType = $this->lang->line("You didn\'t select Auto comment type");




 ?>

<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();	    
	});
	$j(document).ready(function(){

		var base_url = "<?php echo base_url(); ?>";
		var content_counter = 1;
		var edit_content_counter = 1;

		$('[data-toggle="popover"]').popover(); 
		$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});


		// enable and edit auto reply by post id
		$(".manual_auto_reply").click(function(){
			var page_name = $(this).attr('page_name');
			var page_table_id = $(this).attr('page_table_id');
			var EnableAutoReply = "<i class='fa fa-check-circle'></i> <?php echo $EnableAutoReply; ?>";
			$("#manual_reply_error").html('');
			$("#manual_page_name").html(page_name);
			$("#manual_table_id").val(page_table_id);
			$("#manual_post_id").val('');
			// #manual_auto_reply is the id for (enable auto reply button of modal)
			$("#manual_auto_reply").attr('page_table_id',page_table_id);
			$("#manual_auto_reply").attr('post_id','');

			$("#manual_auto_reply").hide();
			$("#check_post_id").show();

			$("#manual_auto_reply").removeClass('btn-danger').addClass('btn-primary').html(EnableAutoReply);
			$("#manual_reply_by_post").addClass('modal');
			$("#manual_reply_by_post").modal();
		});

		$("#check_post_id").click(function(){
			$("#manual_reply_error").html('');		
			var post_id = $("#manual_post_id").val();
			var page_table_id = $("#manual_table_id").val();
			if(post_id=="")
			{
				alertify.alert('<?php echo $this->lang->line("Alert")?>','<?php echo $this->lang->line("Please provide a post ID");?>',function(){});
				return false;
			}
			$.ajax({
			  type:'POST' ,
			  url:"<?php echo site_url();?>facebook_ex_auto_comment/checking_post_id",
			  data:{page_table_id:page_table_id,post_id:post_id},
			  dataType:'JSON',
			  success:function(response){
			  	if(response.error == 'yes')
			  		$("#manual_reply_error").html("<div class='alert alert-danger text-center'><i class='fa fa-close'></i> "+response.error_msg+"</div>");
			  	else
			  	{
				  	$("#manual_auto_reply").attr('post_id',post_id);
				  	$("#manual_auto_reply").attr('manual_enable','yes');
				  	$("#check_post_id").hide();
				  	$("#manual_auto_reply").show();
			  	}
			  }
			});
		});

		$(".manual_edit_reply").click(function(){
			var page_name = $(this).attr('page_name');
			var page_table_id = $(this).attr('page_table_id');
			$("#manual_edit_page_name").html(page_name);
			$("#manual_edit_table_id").val(page_table_id);
			$("#manual_edit_error").html('');
			$("#manual_edit_post_id").val('');
			$("#manual_edit_reply_by_post").addClass('modal');
			$("#manual_edit_reply_by_post").modal();
		});

		$("#manual_edit_post_id").keyup(function(){
			$("#manual_edit_auto_reply").hide();
			$("#manual_edit_error").html('');
			var post_id = $("#manual_edit_post_id").val();
			var page_table_id = $("#manual_edit_table_id").val();

			var ThispostIDisnotfoundindatabaseorthispostIDisnotassociatedwiththepageyouareworking = "<?php echo $ThispostIDisnotfoundindatabaseorthispostIDisnotassociatedwiththepageyouareworking; ?>";
			$.ajax({
			  type:'POST' ,
			  url:"<?php echo site_url();?>facebook_ex_auto_comment/get_tableid_by_postid",
			  data:{page_table_id:page_table_id,post_id:post_id},
			  dataType:'JSON',
			  success:function(response){
			  	if(response.error == 'yes')
			  	{
			  		$("#manual_edit_error").html("<div class='alert alert-danger text-center'><i class='fa fa-close'></i> "+ThispostIDisnotfoundindatabaseorthispostIDisnotassociatedwiththepageyouareworking+"</div>");
			  		$("#manual_edit_auto_reply").addClass('disabled');
			  	}
			  	else
				{
					$("#manual_edit_auto_reply").attr('table_id',response.table_id);
					$("#manual_edit_auto_reply").removeClass('disabled');
				}
			  	
			  	$("#manual_edit_auto_reply").show();
			  }
			});

		});
		// end of enable and edit auto reply by post id



		$(".get_post").click(function(){
			var table_id = $(this).attr('table_id');
			var loading = '<img src="'+base_url+'assets/pre-loader/full-screenshots.gif" style="width:300px" class="center-block">';
			$("#post_synch_modal_body").html(loading);
		  	$("#post_synch_modal").modal();
			$.ajax({
			  type:'POST' ,
			  url:"<?php echo site_url();?>facebook_ex_auto_comment/import_latest_post",
			  data:{table_id:table_id},
			  dataType:'JSON',
			  success:function(response){
			  	  $("#page_name_div").html(response.page_name);
			  	  $("#post_synch_modal_body").html(response.message);
			  }
			});

		});
		

		$(document.body).on('click','.enable_auto_commnet',function(){
		
			/** emoji load for offensive private reply  **/
			
		$j("#private_message_offensive_words").emojioneArea({
        	autocomplete: false,
			pickerPosition: "bottom"
     	 });
		 
		
			var page_table_id = $(this).attr('page_table_id');
			var post_id = $(this).attr('post_id');
			var manual_enable = $(this).attr('manual_enable');
			var Pleaseprovidepostid = "<?php echo $Pleaseprovidepostid; ?>";

			if(typeof(post_id) === 'undefined' || post_id == '')
			{
				alertify.alert('<?php echo $this->lang->line("Alert")?>',Pleaseprovidepostid,function(){});
				return false;
			}

			$("#auto_reply_page_id").val(page_table_id);
			$("#auto_reply_post_id").val(post_id);
			$("#manual_enable").val(manual_enable);
			$(".message").val('');
			$(".filter_word").val('');
			for(var i=2;i<=10;i++)
			{
				$("#filter_div_"+i).hide();
			}
			content_counter = 1;
			$("#content_counter").val(content_counter);
			$("#add_more_button").show();

			$("#response_status").html('');

			$("#auto_reply_message_modal").addClass("modal");
			$("#auto_reply_message_modal").modal();

			$("#manual_reply_by_post").removeClass('modal');
		});
		


		$("#content_counter").val(content_counter);
		$(document.body).on('click','#add_more_button',function(){
			content_counter++;
			if(content_counter == 10)
				$("#add_more_button").hide();
			$("#content_counter").val(content_counter);

			$("#filter_div_"+content_counter).show();
			
			/** Load Emoji For Filter Word when click on add more button **/
			
			$j("#filter_message_"+content_counter).emojioneArea({
        		autocomplete: false,
				pickerPosition: "bottom"
     	 	});
			
			$j("#comment_reply_msg_"+content_counter).emojioneArea({
        		autocomplete: false,
				pickerPosition: "bottom"
     	 	});
			

		});


		$(document.body).on('change','input[name=message_type]',function(){    
        	if($("input[name=message_type]:checked").val()=="generic")
        	{
        		$("#generic_message_div").show();
        		$("#filter_message_div").hide();
				
				/*** Load Emoji for generic message when clicked ***/
				
				$j("#generic_message, #generic_message_private").emojioneArea({
	        		autocomplete: false,
					pickerPosition: "bottom"
	     		 });
		 
				
        	}
        	else 
        	{
        		$("#generic_message_div").hide();
        		$("#filter_message_div").show();
				
				/*** Load Emoji When Filter word click , by defualt first textarea are loaded & No match found field****/
				
				$j("#comment_reply_msg_1, #filter_message_1, #nofilter_word_found_text, #nofilter_word_found_text_private").emojioneArea({
	        		autocomplete: false,
					pickerPosition: "bottom"
	     	 	});
				
        	}
        });

        
        $(document.body).on('click','.lead_first_name',function(){	
			
			var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
				
		    var txtToAdd = " #LEAD_USER_FIRST_NAME# ";
		    var new_text = textAreaTxt + txtToAdd;
		   	$(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
		   	$(this).parent().next().next().next().children('.emojionearea-editor').click();
			
			
		});

		$(document.body).on('click','.lead_last_name',function(){

        	var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
				
		    var txtToAdd = " #LEAD_USER_LAST_NAME# ";
			var new_text = textAreaTxt + txtToAdd;
		   $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
		   $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
		   
		});

		$(document.body).on('click','.lead_tag_name',function(){

	    	var textAreaTxt = $(this).parent().next().next().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
				
				
		    var txtToAdd = " #TAG_USER# ";
			var new_text = textAreaTxt + txtToAdd;
		    $(this).parent().next().next().next().next().next().children('.emojionearea-editor').html(new_text);
		    $(this).parent().next().next().next().next().next().children('.emojionearea-editor').click();
		});



		$(document.body).on('click','#save_button',function(){
			var post_id = $("#auto_reply_post_id").val();
			var Youdidntselectanytemplate = "<?php echo $Youdidntselectanytemplate; ?>";
			var Youdidntselectanyoptionyet = "<?php echo $Youdidntselectanyoptionyet; ?>";
			var TypeAutoCampaignname = "<?php echo $TypeAutoCampaignname; ?>";
			var YouDidnotchosescheduleType = "<?php echo $YouDidnotchosescheduleType; ?>";
			var YouDidnotchosescheduletime = "<?php echo $YouDidnotchosescheduletime; ?>";
			var YouDidnotchosescheduletimezone = "<?php echo $YouDidnotchosescheduletimezone; ?>";
			var YoudidnotSelectPerodicTime = "<?php echo $YoudidnotSelectPerodicTime; ?>";
			var YoudidnotSelectCampaignStartTime = "<?php echo $YoudidnotSelectCampaignStartTime; ?>";
			var YoudidnotSelectCampaignEndTime = "<?php echo $YoudidnotSelectCampaignEndTime; ?>";
			// var YoudidnotSelectAutoCommentType = "<?php echo $YoudidnotSelectAutoCommentType; ?>";

			var schedule_type = $("input[name=schedule_type]:checked").val();
			//var auto_comment_type = $("input[name=auto_comment_type]:checked").val();
			var schedule_time =$("#schedule_time").val();
			var time_zone = $("#time_zone").val();
			var periodic_time = $("#periodic_time").val();
            var campaign_start_time = $("#campaign_start_time").val();
			var campaign_end_time = $("#campaign_end_time").val();
			var auto_comment_template_id = $("#auto_comment_template_id").val();
			var auto_campaign_name = $("#auto_campaign_name").val().trim();

			if (typeof(schedule_type)==='undefined')
			{
				alertify.alert('<?php echo $this->lang->line("Alert")?>',Youdidntselectanyoptionyet,function(){});
				return false;
			}			


            
            if(schedule_type == "onetime")
            {
            	if(schedule_time == ''){
            		alertify.alert('<?php echo $this->lang->line("Alert")?>',YouDidnotchosescheduletime,function(){});
            		return false;
            	}				
            	if(time_zone == ''){
            		alertify.alert('<?php echo $this->lang->line("Alert")?>',YouDidnotchosescheduletimezone,function(){});
            		return false;
            	}
            }
            if(schedule_type == "periodic")
            {
            	if(periodic_time == ''){
            		alertify.alert('<?php echo $this->lang->line("Alert")?>',YoudidnotSelectPerodicTime,function(){});
            		return false;
            	}				
            	if(campaign_start_time == ''){
            		alertify.alert('<?php echo $this->lang->line("Alert")?>',YoudidnotSelectCampaignStartTime,function(){});
            		return false;
            	}			
            	if(campaign_end_time == ''){
            		alertify.alert('<?php echo $this->lang->line("Alert")?>',YoudidnotSelectCampaignEndTime,function(){});
            		return false;
            	}

            
            }


         

           if(auto_comment_template_id == 0){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',Youdidntselectanytemplate,function(){});
				return false;
			}
			if(auto_campaign_name == ''){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',TypeAutoCampaignname,function(){});
				return false;
			}	

			if(schedule_type == ''){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',YouDidnotchosescheduleType,function(){});
				return false;
			}
            var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
			$("#response_status").html(loading);

			var queryString = new FormData($("#auto_reply_info_form")[0]);
			var AlreadyEnabled = "<?php echo $AlreadyEnabled; ?>";
		    $.ajax({
		    	type:'POST' ,
		    	url: base_url+"facebook_ex_auto_comment/ajax_autocomment_reply_submit",
		    	data: queryString,
		    	dataType : 'JSON',
		    	// async: false,
		    	cache: false,
		    	contentType: false,
		    	processData: false,
		    	success:function(response){
		         	if(response.status=="1")
			        {
			         	$("#response_status").html(response.message);
						$("button[post_id="+post_id+"]").removeClass('btn-success').addClass('btn-danger').html(AlreadyEnabled);
			        }
			        else
			        {
			         	$("#response_status").html(response.message);
			        }
		    	}

		    });

		});

		

		$(document.body).on('click','#modal_close',function(){
			var manual_post_id = $("#manual_post_id").val();
			if(manual_post_id != '')
			{
				$("#auto_reply_message_modal").modal("hide");
				$("#manual_reply_by_post").modal("hide");
				$("#manual_post_id").val('');
			}
			else
				$("#auto_reply_message_modal").removeClass("modal");
		});

		$(document.body).on('click','#edit_modal_close',function(){        	
			// $("#edit_auto_reply_message_modal").removeClass("modal");
			var manual_post_id = $("#manual_edit_post_id").val();
			if(manual_post_id != '')
			{
				$("#edit_auto_reply_message_modal").modal("hide");
				$("#manual_edit_reply_by_post").modal("hide");
				$("#manual_edit_post_id").val('');
			}
			else
				$("#edit_auto_reply_message_modal").removeClass("modal");
		});


		$('#post_synch_modal').on('hidden.bs.modal', function () { 
			location.reload();
		});


		$(document.body).on('click','.edit_reply_info',function(){

			// if($("#manual_edit_post_id").val()=="")
			// {
			// 	alertify.alert('<?php echo $this->lang->line("Alert")?>','<?php echo $this->lang->line("Please provide a post ID");?>',function(){});
			// 	return false;
			// }
			
			

			$("#manual_edit_reply_by_post").removeClass('modal');
			$("#edit_auto_reply_message_modal").addClass("modal");
			$("#edit_response_status").html("");


			var table_id = $(this).attr('table_id');
			$.ajax({
			  type:'POST' ,
			  url:"<?php echo site_url();?>facebook_ex_auto_comment/ajax_edit_reply_info",
			  data:{table_id:table_id},
			  dataType:'JSON',
			  success:function(response){

			    $("#edit_auto_reply_page_id").val(response.edit_auto_reply_page_id);
			    $("#edit_auto_reply_post_id").val(response.edit_auto_reply_post_id);
			  	$("#edit_campaign_name").val(response.edit_campaign_name);

                if(response.edit_schedule_type == 'onetime')
                {
                    
                	$("#edit_schedule_type_o").attr('checked',true);
                	$(".schedule_block_item_o").show();
                	$(".schedule_block_item_new_p").hide();
                	
                	$("#edit_schedule_time_o").val(response.edit_schedule_time_o);
                	$("#edit_time_zone_o").val(response.edit_time_zone_o);

                }
                if(response.edit_schedule_type == 'periodic')
                {
                    
                	$("#edit_schedule_type_p").attr('checked',true);
                	$(".schedule_block_item_new_p").show();
                	$(".schedule_block_item_o").hide();
                	$("#edit_periodic_time").val(response.edit_periodic_time);
                	$("#edit_campaign_start_time").val(response.edit_campaign_start_time);
                	$("#edit_campaign_end_time").val(response.edit_campaign_end_time);

                }

                if(response.edit_auto_comment_type=='random')
                {
                	$("#edit_random").attr('checked',true);

                }
                if(response.edit_auto_comment_type =='serially')
                {
                	$("#edit_serially").attr('checked',true);
                }

         
                	
              $("#edit_auto_comment_template_id").val(response.edit_auto_comment_template_id);
              $("#edit_auto_reply_message_modal").modal();
			  }
			});
			
			

			
			setTimeout(function(){
			
				$(".previewLoader").hide();
				
			},5000);
			
			
			
		});


		$(document.body).on('click','#edit_add_more_button',function(){
			if(edit_content_counter == 11)
				$("#edit_add_more_button").hide();
			$("#edit_content_counter").val(edit_content_counter);

			$("#edit_filter_div_"+edit_content_counter).show();
			
			/** Load Emoji For Filter Word when click on add more button during Edit**/
			
			$j("#edit_filter_message_"+edit_content_counter).emojioneArea({
        		autocomplete: false,
				pickerPosition: "bottom"
     	 	});
			
			$j("#edit_comment_reply_msg_"+edit_content_counter).emojioneArea({
        		autocomplete: false,
				pickerPosition: "bottom"
     	 	});
			
			edit_content_counter++;
			
		});
		

		$(document.body).on('click','#edit_save_button',function(){
			var post_id = $("#edit_auto_reply_post_id").val();
			var edit_campaign_name = $("#edit_campaign_name").val();
			var edit_schedule_type = $("input[name=edit_schedule_type]:checked").val();
			var edit_schedule_time_o = $("#edit_schedule_time_o").val();
			var edit_time_zone_o = $("#edit_time_zone_o").val();
			var edit_periodic_time = $("#edit_periodic_time").val();
			var edit_campaign_start_time = $("#edit_campaign_start_time").val();
			var edit_campaign_end_time = $("#edit_campaign_end_time").val();
			var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
			var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
			var YouDidnotchosescheduletime = "<?php echo $YouDidnotchosescheduletime; ?>";
			var YouDidnotchosescheduletimezone = "<?php echo $YouDidnotchosescheduletimezone; ?>";
			var YoudidnotSelectPerodicTime = "<?php echo $YoudidnotSelectPerodicTime; ?>";
			var YoudidnotSelectCampaignStartTime = "<?php echo $YoudidnotSelectCampaignStartTime; ?>";
			var YoudidnotSelectCampaignEndTime = "<?php echo $YoudidnotSelectCampaignEndTime; ?>";

			if (typeof(edit_schedule_type)==='undefined')
			{
				alertify.alert('<?php echo $this->lang->line("Alert")?>',Youdidntselectanyoption,function(){});
				return false;
			}
			if(edit_schedule_type == "onetime")
			{
				if(edit_schedule_time_o == ''){
					alertify.alert('<?php echo $this->lang->line("Alert")?>',YouDidnotchosescheduletime,function(){});
					return false;
				}				
				if(edit_time_zone_o == ''){
					alertify.alert('<?php echo $this->lang->line("Alert")?>',YouDidnotchosescheduletimezone,function(){});
					return false;
				}
			}
			if(edit_schedule_type == "periodic")
			{
				if(edit_periodic_time == ''){
					alertify.alert('<?php echo $this->lang->line("Alert")?>',YoudidnotSelectPerodicTime,function(){});
					return false;
				}				
				if(edit_campaign_start_time == ''){
					alertify.alert('<?php echo $this->lang->line("Alert")?>',YoudidnotSelectCampaignStartTime,function(){});
					return false;
				}			
				if(edit_campaign_end_time == ''){
					alertify.alert('<?php echo $this->lang->line("Alert")?>',YoudidnotSelectCampaignEndTime,function(){});
					return false;
				}
			}

			if(edit_campaign_name == ''){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',Youdidntprovideallinformation,function(){});
				return false;
			}

			var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
			$("#edit_response_status").html(loading);

			var queryString = new FormData($("#edit_auto_reply_info_form")[0]);
		    $.ajax({
		    	type:'POST' ,
		    	url: base_url+"facebook_ex_auto_comment/ajax_update_autoreply_submit",
		    	data: queryString,
		    	dataType : 'JSON',
		    	// async: false,
		    	cache: false,
		    	contentType: false,
		    	processData: false,
		    	success:function(response){
		         	if(response.status=="1")
			        {
			         	$("#edit_response_status").html(response.message);
			        }
			        else
			        {
			         	$("#edit_response_status").html(response.message);
			        }
		    	}

		    });

		});


		$(document.body).on('change','input[name=edit_message_type]',function(){    
        	if($("input[name=edit_message_type]:checked").val()=="generic")
        	{
        		$("#edit_generic_message_div").show();
        		$("#edit_filter_message_div").hide();
				
				$j("#edit_generic_message, #edit_generic_message_private").emojioneArea({
	        		autocomplete: false,
					pickerPosition: "bottom"
	     		 });
        	}
        	else 
        	{
        		$("#edit_generic_message_div").hide();
        		$("#edit_filter_message_div").show();
				
				
				/*** Load Emoji When Filter word click during Edit , by defualt first textarea are loaded & No match found field****/
				
				$j("#edit_comment_reply_msg_1, #edit_filter_message_1, #edit_nofilter_word_found_text, #edit_nofilter_word_found_text_private").emojioneArea({
	        		autocomplete: false,
					pickerPosition: "bottom"
	     	 	});
        	}
        });

		
	});
</script>


<div class="modal fade" id="post_synch_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-portrait"></i> <?php echo $this->lang->line("latest post for page") ?> - <span id="page_name_div"></span></h4>
            </div>
            <div class="modal-body text-center" id="post_synch_modal_body">                

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg"  style="min-width: 70%;max-width: 100%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id='modal_close' class="close">&times;</button>
                <h4 class="modal-title text-center"><?php echo $this->lang->line("Please give the following information for post auto comment") ?></h4>
            </div>
            <form action="#" id="auto_reply_info_form" method="post">
	            <input type="hidden" name="auto_reply_page_id" id="auto_reply_page_id" value="">
	            <input type="hidden" name="auto_reply_post_id" id="auto_reply_post_id" value="">
	            <input type="hidden" name="manual_enable" id="manual_enable" value="">
            <div class="modal-body" id="auto_reply_message_modal_body">  
            	<!-- comment hide and delete section -->
            	<div class="row" style="padding: 10px 20px 10px 20px;">
					<div class="col-xs-12">
                        <div class="form-group col-xs-12 col-md-12" style="padding: 0;">
							<label><?php echo $this->lang->line('') ?></label>
							<label>
								<i class="fa fa-th-large"></i> <?php echo $this->lang->line('Auto Comment Campaign Name'); ?> <span class="red">*</span> 
								<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Select Template From Template Manager'); ?>" data-original-title="Message"><i class="fa fa-info-circle"></i> </a>
							</label>
							<br>
							<select  class="form-control" id="auto_comment_template_id" name="auto_comment_template_id">
							<?php
								echo "<option value='0'>{$this->lang->line('Please select a template')}</option>";
								foreach($auto_comment_template as $key=>$val)
								{
									$id=$val['id'];
									$group_name=$val['template_name'];
									echo "<option value='{$id}'>{$group_name}</option>";
								}
							 ?>
							</select>
					    </div>
					</div>
					<br>
					<div class="col-xs-12">

						<div class="form-group">
							
							<label>
								<i class="fas fa-comment"></i> <?php echo $this->lang->line('Auto Comment Type'); ?> <span class="red">*</span> 
								<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Select Schedule type For Auto Comment'); ?>" data-original-title="Message"><i class="fa fa-info-circle"></i> </a>
							</label>
							<br>
							<input name="auto_comment_type" value="random" id="random" type="radio"> <?php echo $this->lang->line('Random');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input name="auto_comment_type" value="serially" id="serially" type="radio"> <?php echo $this->lang->line('Serially');?>
						</div>

                   </div>
                   <br>

					<div class="col-xs-12">

						<div class="form-group">
							
							<label>
								<i class="fas fa-clock"></i> <?php echo $this->lang->line('Schedule Type'); ?> <span class="red">*</span> 
								<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Select Schedule Time For Auto Comment reply'); ?>" data-original-title="Message"><i class="fa fa-info-circle"></i> </a>
							</label>
							<br>
							<input name="schedule_type" value="onetime" id="schedule_now" type="radio"> <?php echo $this->lang->line('One Time');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input name="schedule_type" value="periodic" id="schedule_later" type="radio"> <?php echo $this->lang->line('Periodic');?>
						</div>

						<div class="form-group schedule_block_item col-xs-12 col-md-6">
							<label><?php echo $this->lang->line('Schedule time'); ?></label>
							<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="schedule_time" id="schedule_time" class="form-control datepicker" type="text"/>
						</div>

						<div class="form-group schedule_block_item col-xs-12 col-md-6">
							<label><?php echo $this->lang->line('Time zone'); ?></label>
							<?php
							$time_zone[''] =$this->lang->line('Please Select');
							echo form_dropdown('time_zone',$time_zone,set_value('time_zone'),' class="form-control" id="time_zone" required');
							?>
						</div>

						<div class="form-group schedule_block_item_new col-xs-12 col-md-12">

							<label><?php echo $this->lang->line('Periodic Schedule time'); ?></label>
							<?php
							$periodic_time[''] =$this->lang->line('Please Select Periodic Time Schedule');
							echo form_dropdown('periodic_time',$periodic_time,set_value('periodic_time'),' class="form-control" id="periodic_time" required');
							?>
						</div>

						<div class="form-group schedule_block_item_new col-xs-12 col-md-6">
							<label><?php echo $this->lang->line('Campaign Start time'); ?></label>
							<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="campaign_start_time" id="campaign_start_time" class="form-control datepicker" type="text"/>
						</div>						
						<div class="form-group schedule_block_item_new col-xs-12 col-md-6">
							<label><?php echo $this->lang->line('Campaign End time'); ?></label>
							<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="campaign_end_time" id="campaign_end_time" class="form-control datepicker" type="text"/>
						</div>

		
						
					</div>

					<div class="col-xs-12" style="margin-top: 15px;">
						<div class="form-group">
							<label>
								<i class="fas fa-monument"></i> <?php echo $this->lang->line('Auto comment campaign name') ?> <span class="red">*</span> 
								<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="Write your auto reply campaign name here" data-original-title="Message"><i class="fa fa-info-circle"></i> </a>
							</label>
							<br>
							<input class="form-control" type="text" name="auto_campaign_name" id="auto_campaign_name" placeholder="Write your auto reply campaign name here">
						</div>
					</div>

					<br/><br/>
				
	
				</div>  
				<!-- end of comment hide and delete section -->

				<div class="row" style="padding: 10px 20px 10px 20px;">
				


					<div class="smallspace clearfix"></div>

			

		
			
				</div>
				<div class="col-xs-12 text-center" id="response_status"></div>
            </div>
            </form>
            <div class="clearfix"></div>
            <div class="modal-footer text-center" style="padding-right:35px;">                
				<button class="btn btn-lg btn-primary" id="save_button"><i class='fa fa-save'></i> <?php echo $this->lang->line("save") ?></button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="edit_auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="min-width: 70%;max-width: 100%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id='edit_modal_close' class="close">&times;</button>
                <h4 class="modal-title text-center"><?php echo $this->lang->line("Please give the following information for post auto comment") ?></h4>
            </div>
            <form action="#" id="edit_auto_reply_info_form" method="post">
	            <input type="hidden" name="edit_auto_reply_page_id" id="edit_auto_reply_page_id" value="">
	            <input type="hidden" name="edit_auto_reply_post_id" id="edit_auto_reply_post_id" value="">
            <div class="modal-body" id="edit_auto_reply_message_modal_body">   
			
			<img src="<?php echo base_url('assets/pre-loader/Fading squares2.gif');?>" class="center-block previewLoader" style="margin-bottom:0;">
			
			

				            	<div class="row" style="padding: 10px 20px 10px 20px;">
									<div class="col-xs-12">
				                        <div class="form-group col-xs-12 col-md-12" style="padding: 0;">
											<label><?php echo $this->lang->line('') ?></label>
											<label>
												<i class="fa fa-th-large"></i> <?php echo $this->lang->line('Auto Comment Template'); ?> <span class="red">*</span> 
												<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Select Template From Template Manager'); ?>" data-original-title="Message"><i class="fa fa-info-circle"></i> </a>
											</label>
											<br>
											<select  class="form-control" id="edit_auto_comment_template_id" name="edit_auto_comment_template_id">
											<?php
												echo "<option value='0'>{$this->lang->line('Please select a template')}</option>";
												foreach($auto_comment_template as $key=>$val)
												{
													$id=$val['id'];
													$group_name=$val['template_name'];
													echo "<option value='{$id}'>{$group_name}</option>";
												}
											 ?>
											</select>
									    </div>
									</div>
									<br>
									<div class="col-xs-12">

										<div class="form-group">
											
											<label>
												<i class="fas fa-comment"></i> <?php echo $this->lang->line('Auto Comment Type'); ?> <span class="red">*</span> 
												<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Select Schedule type For Auto Comment'); ?>" data-original-title="Message"><i class="fa fa-info-circle"></i> </a>
											</label>
											<br>
											<input name="edit_auto_comment_type" value="random" id="edit_random" type="radio"> <?php echo $this->lang->line('Random');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input name="edit_auto_comment_type" value="serially" id="edit_serially" type="radio"> <?php echo $this->lang->line('Serially');?>
										</div>

				                   </div>
				                   <br>
									<div class="col-xs-12">

										<div class="form-group">
											
											<label>
												<i class="fas fa-clock"></i> <?php echo $this->lang->line('Schedule Type'); ?> <span class="red">*</span> 
												<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Select Schedule Time For Auto Comment reply'); ?>" data-original-title="Message"><i class="fa fa-info-circle"></i> </a>
											</label>
											<br>
											<input name="edit_schedule_type" value="onetime" id="edit_schedule_type_o" type="radio"> <?php echo $this->lang->line('One Time');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input name="edit_schedule_type" value="periodic" id="edit_schedule_type_p" type="radio"> <?php echo $this->lang->line('Periodic');?>
										</div>

										<div class="form-group schedule_block_item_o col-xs-12 col-md-6">
											<label><?php echo $this->lang->line('Schedule time'); ?></label>
											<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_schedule_time_o" id="edit_schedule_time_o" class="form-control datepicker" type="text"/>
										</div>

										<div class="form-group schedule_block_item_o col-xs-12 col-md-6">
											<label><?php echo $this->lang->line('Time zone'); ?></label>
											<?php
											$time_zone[''] =$this->lang->line('Please Select');
											echo form_dropdown('edit_time_zone_o',$time_zone,set_value('time_zone'),' class="form-control" id="edit_time_zone_o" required');
											?>
										</div>

										<div class="form-group schedule_block_item_new_p col-xs-12 col-md-12">

											<label><?php echo $this->lang->line('Periodic Schedule time'); ?></label>
											<?php
											$periodic_time[''] =$this->lang->line('Please Select Periodic Time Schedule');
											echo form_dropdown('edit_periodic_time',$periodic_time,set_value('edit_periodic_time'),' class="form-control" id="edit_periodic_time" required');
											?>
										</div>

										<div class="form-group schedule_block_item_new_p col-xs-12 col-md-6">
											<label><?php echo $this->lang->line('Campaign Start time'); ?></label>
											<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_campaign_start_time" id="edit_campaign_start_time" class="form-control datepicker" type="text"/>
										</div>						
										<div class="form-group schedule_block_item_new_p col-xs-12 col-md-6">
											<label><?php echo $this->lang->line('Campaign End time'); ?></label>
											<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_campaign_end_time" id="edit_campaign_end_time" class="form-control datepicker" type="text"/>
										</div>

						
										
									</div>

									<div class="col-xs-12" style="margin-top: 15px;">
										<div class="form-group">
											<label>
												<i class="fas fa-monument"></i> Auto reply campaign name <span class="red">*</span> 
												<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="Write your auto reply campaign name here" data-original-title="Message"><i class="fa fa-info-circle"></i> </a>
											</label>
											<br>
											<input class="form-control"type="text" name="edit_campaign_name" id="edit_campaign_name" placeholder="Write your auto reply campaign name here">
										</div>
									</div>

									<br/><br/>
								
					
								</div>  
				<div class="row" style="padding: 10px 20px 10px 20px;">
					<!-- added by mostofa on 26-04-2017 -->

					<div class="smallspace clearfix"></div>





				</div>
				<div class="col-xs-12 text-center" id="edit_response_status"></div>
            </div>
            </form>
            <div class="clearfix"></div>
            <div class="modal-footer text-center" style="padding-right:35px;">                
				<button class="btn btn-lg btn-primary" id="edit_save_button"><i class='fa fa-save'></i> <?php echo $this->lang->line("save") ?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="manual_reply_by_post" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line("please provide a post id of page") ?> (<span id="manual_page_name"></span>)</h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12" id="waiting_div"></div>
                    <div class="col-xs-12 col-md-8 col-md-offset-2">
                        <form>
                            <div class="form-group">
                              <label for="manual_post_id"><?php echo $this->lang->line("post id") ?> :</label>
                              <input type="text" class="form-control" id="manual_post_id" placeholder="<?php echo $this->lang->line("please give a post id") ?>" value="">
                              <input type="hidden" id="manual_table_id">
                            </div>
                            <div class="text-center" id="manual_reply_error"></div>
                         
                            <div class="form-group text-center">
                              <button type="button" class="btn btn-primary enable_auto_commnet" id="manual_auto_reply"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("enable auto reply") ?></button>
                            </div>
                         </form>
                         <div class="form-group text-center">
                            <button type="button" class="btn btn-outline-warning" id="check_post_id"><i class="fa fa-check"></i> <?php echo $this->lang->line("check existance") ?></button>
                         </div>
                        
                    </div>                    
                </div>               
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="manual_edit_reply_by_post" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line("please provide a post id of page") ?> (<span id="manual_edit_page_name"></span>)</h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12" id="waiting_div"></div>
                    <div class="col-xs-12 col-md-8 col-md-offset-2">
                        <form>
                            <div class="form-group">
                              <label for="manual_post_id"><?php echo $this->lang->line("post id") ?> :</label>
                              <input type="text" class="form-control" id="manual_edit_post_id" placeholder="<?php echo $this->lang->line("please give a post id") ?>" value="">
                              <input type="hidden" id="manual_edit_table_id">
                            </div>
                            <div class="text-center" id="manual_edit_error"></div>                           
                        </form>
                        <div class="form-group text-center" style="margin-top: 15px;">
                           <button type="button" class="btn btn-outline-warning edit_reply_info" id="manual_edit_auto_reply"><i class="fa fa-edit"></i> <?php echo $this->lang->line("edit auto reply") ?></button>
                        </div>
                        
                    </div>                    
                </div>               
            </div>
        </div>
    </div>
</div>

<!-- comment hide and delete section -->
<div class="modal fade" id="modal-live-video-library"  data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header clearfix">
        <a class="pull-right" id="filemanager_close" style="font-size: 14px; color: white; cursor: pointer;" >&times;</a>
        <h4 class="modal-title"><i class="fa fa-file-video-o"></i> <?php echo $this->lang->line("filemanager Library") ?></h4>
      </div>
      <div class="modal-body">
        
      </div>
    </div>
  </div>
</div>

<script>



$(document.body).on('change','input[name=schedule_type]',function(){
	if($("input[name=schedule_type]:checked").val()=="onetime")
	{
		$(".schedule_block_item").show();
		$(".schedule_block_item_new").hide();
		$("#periodic_time").val("");
		$("#campaign_start_time").val("");
		$("#campaign_end_time").val("");
	}
	else
	{
		$("#schedule_time").val("");
		$("#time_zone").val("");
		$(".schedule_block_item_new").show();
		$(".schedule_block_item").hide();
	}
});

$(document.body).on('change','input[name=schedule_type]',function(){
	if($("input[name=schedule_type]:checked").val()=="onetime")
	{
		$(".schedule_block_item").show();
		$(".schedule_block_item_new").hide();
		$("#periodic_time").val("");
		$("#campaign_start_time").val("");
		$("#campaign_end_time").val("");
	}
	else
	{
		$("#schedule_time").val("");
		$("#time_zone").val("");
		$(".schedule_block_item_new").show();
		$(".schedule_block_item").hide();
	}
});


$(document.body).on('change','input[name=edit_schedule_type]',function(){
	if($("input[name=edit_schedule_type]:checked").val()=="onetime")
	{
		$(".schedule_block_item_o").show();
		$(".schedule_block_item_new_p").hide();
		$("#periodic_time_p").val("");
		$("#campaign_start_time_p").val("");
		$("#campaign_end_time_p").val("");
	}
	else
	{
		$("#schedule_time_o").val("");
		$("#time_zone_o").val("");
		$(".schedule_block_item_new_p").show();
		$(".schedule_block_item_o").hide();
	}
});


$(document).ready(function(){
     $(".schedule_block_item").hide();
     $(".schedule_block_item_new").hide();

    var today = new Date();
    var next_date = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
    $j('.datepicker').datetimepicker({
    	theme:'light',
    	format:'Y-m-d H:i:s',
    	formatDate:'Y-m-d H:i:s',
    	minDate: today,
    	maxDate: next_date
    })

});
</script>
<!-- comment hide and delete section -->

<style type="text/css">.ajax-upload-dragdrop{width:100% !important;}</style>