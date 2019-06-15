<style type="text/css">
	::placeholder {
	  color: #ddd !important;
	}
</style>
<div class="modal-dialog" style="width: 100%;margin:20px 10px 30px 10px;">

	<div class="modal-content">
		
		<div class="modal-header" style="padding: 30px;">
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-5">
					<?php echo form_dropdown('page_id', $page_dropdown, $enabled_page, 'id="page_id" class="form-control"'); ?>
				</div>
				<div class="col-md-2">
					<button class="btn <?php echo ($is_any_page_enabled == true) ? "btn-danger" : "btn-info"; ?>" id="enable_disable_webhook"><?php if ($is_any_page_enabled) echo $this->lang->line('Disable Webhook'); else echo $this->lang->line('Enable Webhook'); ?></button>
				</div>

				<input type="hidden" id="is_any_page_enabled" value="<?php echo ($is_any_page_enabled == true) ? "enabled" : "disabled"; ?>">
				<input type="hidden" id="currently_enabled_page" value="<?php echo $enabled_page; ?>">
			</div>

		</div>
		<div class="modal-body" style="padding: 15px 30px 15px 30px;">
			
			<h4><i class="fa fa-reply"></i> <?php echo $this->lang->line('Set reply for  page messaging'); ?></h4><br><br>
			
			<div class="row">

				<div class="col-md-4 text-center"><?php echo $this->lang->line('Keyword'); ?></div>
				<div class="col-md-8 text-center"><?php echo $this->lang->line('Reply'); ?></div>
			</div><br>
			<div class="row">

				<div class="col-md-4"><input id="keyword_1" type="text" class="form-control" name="keyword[]" value="<?php echo isset($page_messaging_info[0]['keywords']) ? $page_messaging_info[0]['keywords'] : '' ?>" placeholder="Hi"></div>
				<div class="col-md-8"><input id="reply_1" type="text" class="form-control" name="reply[]" value="<?php echo isset($page_messaging_info[0]['reply_message']) ? $page_messaging_info[0]['reply_message'] : '' ?>" placeholder="Hi, How can we help you?"></div>
			</div><br>
			<div class="row">

				<div class="col-md-4"><input id="keyword_2" type="text" class="form-control" name="keyword[]" value="<?php echo isset($page_messaging_info[1]['keywords']) ? $page_messaging_info[1]['keywords'] : '' ?>" placeholder="Help"></div>
				<div class="col-md-8"><input id="reply_2" type="text" class="form-control" name="reply[]" value="<?php echo isset($page_messaging_info[1]['reply_message']) ? $page_messaging_info[1]['reply_message'] : '' ?>" placeholder="Please put your question here, one of my team member will help you."></div>
			</div><br>
			<div class="row">
	
				<div class="col-md-4"><input id="keyword_3" type="text" class="form-control" name="keyword[]" value="<?php echo isset($page_messaging_info[2]['keywords']) ? $page_messaging_info[2]['keywords'] : '' ?>" placeholder="Info"></div>
				<div class="col-md-8"><input id="reply_3" type="text" class="form-control" name="reply[]" value="<?php echo isset($page_messaging_info[2]['reply_message']) ? $page_messaging_info[2]['reply_message'] : '' ?>" placeholder="We are a software firm. We develop web application."></div>
			</div><br><br>
			<button class="btn btn-primary btn-lg" id="save_reply"><i class="fa fa-save"></i> <?php echo $this->lang->line('Save Reply'); ?></button>

		</div><br><br>

	</div>

</div>


<script>
	
	$(document).ready(function() {

		$("#page_id").select2(); 
		
		$(document.body).on('click', '#enable_disable_webhook', function(event) {
			event.preventDefault();
			var has_messenger_bot = "<?php echo $has_messenger_bot; ?>";
			if(has_messenger_bot == 'yes')
			{
				alertify.alert('<?php echo $this->lang->line('Warning!!!'); ?>', 'Seems you have Bot Inboxer add-on, Please set reply from Bot Settings menu of Bot Inboxer add-on.');
				return false;

			}
			var selected_page = $("#page_id").val();
			var enable_or_disable = $("#is_any_page_enabled").val();
			var enabled_page = $("#currently_enabled_page").val();

			if (enable_or_disable == 'enabled' && selected_page != enabled_page) 
				alertify.alert('<?php echo $this->lang->line('Warning!!!'); ?>', '<?php echo $this->lang->line('Please, disable the enabled page first.'); ?>');
			else {

				if (selected_page == -1)
					alertify.alert('<?php echo $this->lang->line('Warning!!!'); ?>', '<?php echo $this->lang->line('Please, select atleast one page.'); ?>');
				else {

					$.ajax({
						url: '<?php echo base_url('facebook_rx_account_import/enableDisableWebHook'); ?>',
						type: 'POST',
						dataType: 'JSON',
						data: {page_id: selected_page, enable_or_disable: enable_or_disable},
						success: function (response) {
							if(response.error != '')
								alertify.alert('<?php echo $this->lang->line('Warning!!!'); ?>', response.error);
							else
							{								
								if (enable_or_disable == "disabled") {

									$("#enable_disable_webhook").html('<?php echo $this->lang->line('Disable Webhook'); ?>');
									$("#enable_disable_webhook").removeClass('btn-info');
									$("#enable_disable_webhook").addClass('btn-danger');

									$("#currently_enabled_page").val(selected_page);
									$("#is_any_page_enabled").val("enabled");
								}
								else if (enable_or_disable == "enabled") {

									$("#enable_disable_webhook").html('<?php echo $this->lang->line('Enable Webhook'); ?>');
									$("#enable_disable_webhook").removeClass('btn-danger');
									$("#enable_disable_webhook").addClass('btn-info');

									$("#currently_enabled_page").val("");
									$("#is_any_page_enabled").val("disabled");
								}
							}

						}
					});
					
				}
			}


		});


		$(document.body).on('click', '#save_reply', function(event) {
			event.preventDefault();
			
			var enable_or_disable = $("#is_any_page_enabled").val();
			var enabled_page = $("#currently_enabled_page").val();

			var keyword_1 = $("#keyword_1").val();
			var keyword_2 = $("#keyword_2").val();
			var keyword_3 = $("#keyword_3").val();

			var reply_1 = $("#reply_1").val();
			var reply_2 = $("#reply_2").val();
			var reply_3 = $("#reply_3").val();
			

			if ( enable_or_disable == "disabled" || enabled_page == "" || keyword_1 == "" || keyword_2 == "" || keyword_3 == "" || reply_1 == "" || reply_2 == "" || reply_3 == "")
				alertify.alert('<?php echo $this->lang->line('Warning!!!'); ?>', '<?php echo $this->lang->line('Please, enable webhook for a page and set all the kewword and reply.'); ?>');
			else {

				$.ajax({
					url: '<?php echo base_url('facebook_rx_account_import/submitPagesMessageInfo'); ?>',
					type: 'POST',
					data: {enabled_page: enabled_page, keyword_1: keyword_1, keyword_2: keyword_2, keyword_3: keyword_3, reply_1: reply_1, reply_2: reply_2, reply_3: reply_3},
					success: function (response) {
						alertify.alert('<?php echo $this->lang->line('Success!!!'); ?>', '<?php echo $this->lang->line('Data has been stored successfully.'); ?>');
					}
				});
							
			}
		});

	});

</script>
