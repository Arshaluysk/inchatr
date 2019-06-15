<style>
	.margined{padding:20px;background: #fbfbfb;}
	.refresh_button_holder
	{
		padding: 0px;
		margin-top: 5px; 
		margin-bottom:5px;
	}
	.header_title{
		margin: 0px;
		padding: 0px;
		color: #3C8DBC;
	}
	.right_content
	{
		float: right;
		margin-bottom: 10px;

	}
	.right_content_message
	{
		background: #0084FF;
		color: white;
		padding: 10px;
		margin: 0px;
		border-radius: 15px;
	}
	.right_content_name
	{
		color: gray;
		text-align: right;
		padding: 0px;
		padding-right: 10px;
		margin: 0px;
	}
	.right_content_date
	{
		color: gray;
		text-align: right;
		font-weight: 500;
		padding: 0px;
		padding-right: 10px;
		margin: 0px;
	}

	.left_content
	{
		float: left;
		margin-bottom: 10px;
	}
	.left_content_message
	{
		background: #F1F0F0;
		padding: 10px;
		margin: 0px;
		border-radius: 15px;
	}
	.left_content_name
	{
		color: gray;
		text-align: left;
		padding: 0px;
		padding-left: 10px;
		margin: 0px;
	}
	.left_content_date
	{
		color: gray;
		text-align: left;
		font-weight: 500;
		padding: 0px;
		padding-left: 10px;
		margin: 0px;
	}
</style>
<div class="row" style="padding: 10px; padding-top: 0px;">
	
	<div id="unread_message_div"></div>
	<br/>
	<div id="page_list_div"></div>

</div>

<div class="modal fade" id="conversation_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-wechat"></i> <span id="conversation_page_name"></span></h4>
            </div>
            <div class="modal-body" id="conversation_modal_body">                

            </div>
            <div class="modal-footer">
            	<input class="form-control" type="text" placeholder="<?php echo $this->lang->line('type your reply message here');?>" id="reply_message" name="reply_message">
            	<!-- <div class='text-left' id="emotion_container"><?php echo $emotion_list;?></div> -->
            	<br>
            	<button class="btn btn-primary btn-sm" id="final_reply_button" style="margin-top: 5px;"><i class="fa fa-send"></i> <?php echo $this->lang->line('send') ?></button>
            </div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();
	});

	$(document.body).on('click','.emotion',function(){  
    	var img_link = $(this).attr("src");
    	var eval = $(this).attr("eval");
    	var caretPos = $(this).parent().prev()[0].selectionStart;
    	var textAreaTxt = $(this).parent().prev().val();
    	var txtToAdd = " "+eval+" ";
    	// var new_text = textAreaTxt + txtToAdd;
    	$(this).parent().prev().val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
    });
	
	$j("document").ready(function(){
		var base_url="<?php echo base_url(); ?>";
		var loading = '<br><img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
		var loading2 = '<br><br><br><br><img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';

		$(document.body).on('click','.reply_button',function(){
			var thread_id = $(this).attr('thread_id');
			$("#final_reply_button").attr('thread_id',thread_id);
			var page_table_id = $(this).attr('page_table_id');
			$("#final_reply_button").attr('page_table_id',page_table_id);
			var page_name = $(this).attr('page_name');
			$("#conversation_modal").modal();
			$("#conversation_modal_body").html(loading);					
			$("#conversation_page_name").html(page_name);					
			$.ajax({
					url:base_url+'fb_msg_manager/get_post_conversation',
					type:'POST',
					data:{thread_id:thread_id,page_table_id:page_table_id},
					success:function(response){
						$("#conversation_modal_body").html(response);
						var element = document.getElementById("conversation_modal_body");
						element.scrollTop = element.scrollHeight;					
					}
					
				});
		});

		$(document.body).on('click','#final_reply_button',function(){
			var thread_id = $(this).attr('thread_id');
			var page_table_id = $(this).attr('page_table_id');
			var reply_message = $("#reply_message").val().trim();
			if(reply_message == '')
			{
				alertify.alert("<?php $this->lang->line('Alert'); ?>","<?php echo $this->lang->line('You did not provide any reply message'); ?>",function(){});
				return false;
			}

			$("#conversation_modal_body").html(loading);
			$("#reply_message").val('');
			$("#final_reply_button").addClass('disabled');
			$.ajax({
					url:base_url+'fb_msg_manager/reply_to_conversation',
					type:'POST',
					data:{thread_id:thread_id,page_table_id:page_table_id,reply_message:reply_message},
					success:function(response){
						$("#conversation_modal_body").html(response);
						$("#final_reply_button").removeClass('disabled');			
					}
					
				});
		});

    	// $("#unread_message_div").html(loading);
    	$("#page_list_div").html(loading2);

    	function ajax_call()
    	{    		
	    	$.ajax({
					url:base_url+'fb_msg_manager/get_unread_message',
					type:'POST',
					data:{},
					success:function(response){
						$("#unread_message_div").html(response);
				    	$.ajax({
								url:base_url+'fb_msg_manager/get_pages_conversation',
								type:'POST',
								data:{},
								success:function(response){
									$("#page_list_div").html(response);					
								}
								
							});				
					}
					
				});	    	
    	}
    	
    	$(document.body).on('click','#refresh_button',function(){  
    		// $("#unread_message_div").html(loading);
	    	$("#page_list_div").html(loading2);  		
	    	ajax_call();
    	});

    	ajax_call();
	});
</script>