<?php 
	if($this->session->userdata('success_message') == 'success')
	{
		echo "<h4 style='margin:0'><div class='alert alert-success text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line('your account has been imported successfully.')."</div></h4>";
		$this->session->unset_userdata('success_message');
	}

	if($this->session->userdata('limit_cross') != '')
	{
		echo "<h4 style='margin:0'><div class='alert alert-danger text-center'><i class='fa fa-remove'></i> ".$this->session->userdata('limit_cross')."</div></h4>";
		$this->session->unset_userdata('limit_cross');
	}
	if($this->config->item('has_manage_page_approval') == '') $has_manage_page_approval='1'; 
	else $has_manage_page_approval=$this->config->item('has_manage_page_approval');
	$is_demo=$this->is_demo;
?>
<style>
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
	  background-color: <?php echo $THEMECOLORCODE; ?>;
	  -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);
	          box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);
	  -webkit-transition: width .6s ease;
	       -o-transition: width .6s ease;
	          transition: width .6s ease;
	}
	.existing_account {
		margin: 10px 0 10px 0
		font-size: 16px;
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
	.horizontal_break{
		padding: 2px;
		margin: 0px;
	}

	.info-box-icon {
	    border-top-left-radius: 2px;
	    border-top-right-radius: 0;
	    border-bottom-right-radius: 0;
	    border-bottom-left-radius: 2px;
	    display: block;
	    float: left;
	    height: 55px;
	    width: 90px;
	    text-align: center;
	    font-size: 40px;
	    line-height: 55px;
	    background: rgba(0,0,0,0.2);
	}

	.info-box {
	    display: block;
	    min-height: 50px;
	    background: #fff;
	    width: 100%;
	    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
	    border-radius: 2px;
	    margin-bottom: 15px;
	}
	/* .wrapper,.content-wrapper{background: #fafafa !important;} */
	.well{background: #fff;}

</style>

<?php if($existing_accounts != '0' && $show_import_account_box == 1) { ?>
	<div class="well well_border_left">
		<h4 class="text-center"> <i class="fa fa-facebook-official"></i> <?php echo $this->lang->line("your existing accounts") ?></h4>
	</div>
<?php } else echo "<br><br>";?>



<div class="clearfix">
	<?php  if($show_import_account_box==0) : ?>
		<div style="padding: 0 15px;">			
			<div class='alert alert-danger text-center'><i class='fa fa-times-circle'></i> <?php echo $this->lang->line('due to system configuration change you have to delete one or more imported FB accounts and import again. Please check the following accounts and delete the account that has warning to delete.'); ?></div>
		</div>
	<?php endif; ?>

	<?php 	
	if($has_manage_page_approval == '1') : 
	?>
		<div class="row" style="padding:0 15px;">
			<div class="col-xs-12">				
				<?php 
				if($is_demo && $this->session->userdata('user_type') == 'Admin')
				echo '<div class="alert alert-warning text-center">Account import has been disabled in admin account because you will not be able to unlink the Facebook account you import as admin. If you want to test with your own accout then <a href="'.base_url('home/sign_up').'" target="_BLANK">sign up</a> to create your own demo account then import your Facebook account there.</div>';
				else {?>	
				<h4>
					<div class="text-center">
						<p data-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line("you must be logged in your facebook account for which you want to refresh your access token. for synch your new page, simply refresh your token. if any access token is restricted for any action, refresh your access token.");?>"><?php echo $fb_login_button; ?></p>
					</div>
				</h4>
				<?php } ?>
			</div>
		</div>
	<?php else : ?>
		<div class="row" role="dialog">
			<div class="col-xs-12">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title text-center"><i class='fa fa-plus-circle'></i> <?php echo $this->lang->line("add facebook account") ?></h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-xs-8">
									<input type="text" class="form-control" placeholder="<?php echo $this->lang->line("enter your facebook numeric id")?>" id="fb_numeric_id" />
									<span class="label label-light"><a href="http://findmyfbid.com/" target="_blank" style="color: ornage;"><i class="fa fa-question-circle"></i> <?php echo $this->lang->line("how to get FB numeric ID?") ?></a></span>
								</div>
								<div class="col-xs-4">
									<button class="btn btn-outline-primary" id="submit"><i class='fa fa-send'></i> <?php echo $this->lang->line("send app request") ?></button>
								</div>

								<div class="col-xs-12" id="response">
									
								</div>

							</div>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div>
		</div><!-- /.modal -->
	<?php endif; ?>


	<?php if($existing_accounts != '0') : ?>
		<div>
			<?php  if($show_import_account_box == 1 && $has_manage_page_approval != '1') { ?>
			<div class="col-xs-12">				
				<h4>
					<div class="text-center">						
						<p data-toggle="tooltip" title="<?php echo $this->lang->line("you must be logged in your facebook account for which you want to refresh your access token. for synch your new page, simply refresh your token. if any access token is restricted for any action, refresh your access token.");?>"><?php echo $fb_login_button; ?></p>
					</div>
				</h4>
			</div>

			<?php } ?>

			<br>

			<div class="row" style="padding:0 15px;">
			<?php $i=0; foreach($existing_accounts as $value) : ?>
				<div class="col-xs-12 col-sm-12 col-md-6">
					<div class="modal-dialog" style="width: 100%;margin:0 0 30px 0;">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title"><i class="fa fa-facebook"></i> <?php echo $value['name']; ?> </h4>
							</div>
							<div class="modal-body">
								<div class="row">
									<div class="col-xs-12">
										<?php
											if($value['need_to_delete'] == 1)
											{
												echo "<div class='alert alert-danger text-center'><i class='fa fa-close'></i> ".$this->lang->line('you have to delete this account.')."</div>";
											} 
										?>
										<?php 
											if($value['validity'] == 'no')
											{
												echo "<div class='alert alert-danger text-center'><i class='fa fa-close'></i> ".$this->lang->line('your login validity has been expired.')."</div>";
											}
										?>
										<div class="row">
											<?php $profile_picture="https://graph.facebook.com/me/picture?access_token={$value['user_access_token']}&width=150&height=150"; ?>
											<div class="text-xs-center text-sm-center col-xs-12 col-sm-12 col-md-6">
												<img src="<?php echo $profile_picture;?>" alt="" class='img-circle'>
											</div>
											<div class="col-xs-12 col-sm-12 col-md-6">
												<br/>
												<div class="info-box">
													<span class="info-box-icon bg-aqua" style="background:#fff !important;border-right:1px solid #eee;color: <?php echo $THEMECOLORCODE;?> !important;"><i class="fa fa-newspaper-o"></i></span>
													<div class="info-box-content">
														<span class="info-box-text"><?php echo $this->lang->line("total pages");?></span>
														<span class="info-box-number"><?php echo number_format($value['total_pages']); ?></span>
													</div><!-- /.info-box-content -->
												</div><!-- /.info-box -->
												<button class="delete_account pull-right btn btn-outline-danger" table_id="<?php echo $value['userinfo_table_id']; ?>" data-toggle="tooltip" data-placement="left" title="<?php echo $this->lang->line("do you want to remove this account from our database? you can import again.");?>"><i class="fa fa-unlink"></i> <?php echo $this->lang->line("Unlink this account") ?></button>
											</div>									
										</div><!-- /.row -->

										<br/>
										<p class="existing_account"><?php echo $this->lang->line("page list") ?></p>
										<div class="custom_progress"><div class="custom_progress_bar" style="width: 70%"></div></div>

										<div style="height: 300px;overflow-y:auto;" class="yscroll">
											<ul class="products-list product-list-in-box">
											<?php foreach($value['page_list'] as $page_info) : ?>												
												<li class="item">
								                  <div class="product-img">
								                    <img src="<?php echo $page_info['page_profile']; ?>">
								                  </div>
								                  <div class="product-info">
								                    <a target="_BLANK" href="https://facebook.com/<?php echo $page_info['page_id'];?>" class="product-title"><?php echo $page_info['page_name']; ?>							                      	

								                      	<?php
														if($page_info['bot_enabled']=='0')
														{ ?>
															<a style="margin-top:10px;margin-right:10px;"  restart='0' bot-enable="<?php echo $page_info['id'];?>" id="bot-<?php echo $page_info['id'];?>" class="btn btn-sm btn-outline-success pull-right enable_bot"><i class="fa fa-check"></i> <?php echo $this->lang->line("Enable Bot") ?></a>
														<?php
														}
														else if ($page_info['bot_enabled']=='1')
														{ ?>
															<a style="margin-top:10px;margin-right:10px;"  restart='0' bot-enable="<?php echo $page_info['id'];?>" id="bot-<?php echo $page_info['id'];?>" class="btn btn-sm btn-outline-danger pull-right disable_bot"><i class="fa fa-remove"></i> <?php echo $this->lang->line("Disable Bot") ?></a>
														<?php
														} 
														else
														{ ?>
															<a style="margin-top:10px;margin-right:10px;"  restart='1' bot-enable="<?php echo $page_info['id'];?>" id="bot-<?php echo $page_info['id'];?>" class="btn btn-sm btn-outline-warning pull-right enable_bot"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Re-start Bot") ?></a>
														<?php
														} 
														?>
								                    </a>

								                    <a style="margin-top:10px;margin-right:10px;" class="btn-sm btn btn-outline-warning pull-right page_delete" table_id="<?php echo $page_info['id']; ?>" title="<?php echo $this->lang->line("do you want to remove this page from our database?");?>" data-placement="left" data-toggle="tooltip">
								                      		<i class="fa fa-remove"></i> <?php echo $this->lang->line("Remove"); ?>
								                      	</a>
								                      	
							                    	<span class="product-description">
							                          <?php echo $this->lang->line('email');?> : </b> <?php echo $page_info['page_email']; ?>
							                        </span>
								                  </div>
								                </li>
											<?php endforeach; ?>
											</ul>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			<?php
				$i++;
				if($i%2 == 0)
					echo "</div><div class='row' style='padding:0 15px;'>";
				endforeach;				
			?>
			</div> 
		</div>
	<?php endif; ?>
</div>


<div class="modal fade" id="delete_confirmation" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-flag"></i> <?php echo $this->lang->line("Deletion Report") ?></h4>
            </div>
            <div class="modal-body" id="delete_confirmation_body">                

            </div>
        </div>
    </div>
</div>

<?php 
    
    $doyouwanttodelete = $this->lang->line("do you want to delete this group from database?");
    $ifyoudeletethispage = $this->lang->line("if you delete this page, all the campaigns corresponding to this page will also be deleted. Do you want to delete this page from database?");
    $ifyoudeletethisaccount = $this->lang->line("if you delete this account, all the pages, groups and all the campaigns corresponding to this account will also be deleted form database. do you want to delete this account from database?");
    $facebooknumericidfirst = $this->lang->line("please enter your facebook numeric id first");
    $enablebot = $this->lang->line("Enable Bot");
    $disablebot = $this->lang->line("Disable Bot");
    $restart_bot = $this->lang->line("Re-start Bot");
    $somethignwentwrong = $this->lang->line("something went wrong, please try again.");
    $please_wait = $this->lang->line("please wait");
    $areyousure = $this->lang->line("are you sure");

?>


<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();
	});
	
	$j("document").ready(function() {

		var base_url = "<?php echo base_url();?>";

		var enablebot = "<?php echo $enablebot;?>";
		var disablebot = "<?php echo $disablebot;?>";
		var restart_bot = "<?php echo $restart_bot;?>";
		var somethignwentwrong = "<?php echo $somethignwentwrong;?>";
		var please_wait = "<?php echo $please_wait;?>...";
		var areyousure = "<?php echo $areyousure;?>";

		$j(document.body).on('click','.enable_bot',function(){
			var page_id = $(this).attr('bot-enable');
			var restart = $(this).attr('restart');
			$(this).addClass('disabled');

			alertify.confirm('<?php echo $this->lang->line("Confirm");?>',areyousure, 
		    function(){ 
		       $.ajax
				({
				   type:'POST',
				   // async:false,
				   url:base_url+'messenger_bot/enable_disable_bot',
				   data:{page_id:page_id,enable_disable:'enable',restart:restart},
				   dataType:'JSON',
				   success:function(response)
				    {
				    	if(response.success)
				    	{
				    		var disable_html="<i class='fa fa-remove'></i> "+disablebot;
				    		$('#bot-'+page_id).html(disable_html).removeClass('btn-outline-success').removeClass('enable_bot').addClass('btn-outline-danger').addClass('disable_bot').removeClass('disabled').attr('restart','0');
				    	}
				    	else 
				    	{
				    		alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.error,function(){});
				    		$('#bot-'+page_id).removeClass('disabled');
				    	}
				    }
				       
				});
		    },
		    function(){     
		    });
		});

		$j(document.body).on('click','.disable_bot',function(){
			var page_id = $(this).attr('bot-enable');
			var restart = $(this).attr('restart');
			$(this).addClass('disabled');

			alertify.confirm('<?php echo $this->lang->line("Confirm");?>',areyousure, 
		    function(){


		    	$.ajax
		    	({
		    	   type:'POST',
		    	   url:base_url+'messenger_bot/check_page_response',
		    	   data:{page_id:page_id},
		    	   dataType:'JSON',
		    	   success:function(response)
		    	    {
		    	      if(response.has_pageresponse == '1')
		    	      {              
		    	        alertify.confirm('Alert !', '<?php echo $this->lang->line("There is PageResponse set for this page by you or other admin of this page. By disabling Messenger Bot will also disable the PageResponse. You can restart PageResponse by going to Page Response -> Account Import menu.");?>', 
		    	          function()
		    	          { 
		    	            $.ajax
		    	            ({
		    	               type:'POST',
		    	               url:base_url+'messenger_bot/enable_disable_bot',
		    	               data:{page_id:page_id,enable_disable:'disable',restart:restart},
		    	               dataType:'JSON',
		    	               success:function(response)
		    	                {
		    	                	if(response.success)
		    	                	{
		    	                		var enable_html="<i class='fa fa-refresh'></i> "+restart_bot;
		    	                		$('#bot-'+page_id).html(enable_html).removeClass('btn-outline-danger').removeClass('disable_bot').addClass('btn-outline-warning').addClass('enable_bot').removeClass('disabled').attr('restart','1');
		    	                	}
		    	                	else 
		    	                	{
		    	                		alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.error,function(){});
		    	                		$('#bot-'+page_id).removeClass('disabled');
		    	                	}
		    	                }		    	                   
		    	            });

		    	          }, 
		    	          function()
		    	          { 
		    	            location.reload();
		    	          }
		    	        );

		    	      }
		    	      else 
		    	      {

		    	        $.ajax
		    	        ({
		    	           type:'POST',
		    	           url:base_url+'messenger_bot/enable_disable_bot',
		    	           data:{page_id:page_id,enable_disable:'disable',restart:restart},
		    	           dataType:'JSON',
		    	           success:function(response)
		    	            {
		    	            	if(response.success)
		    	            	{
		    	            		var enable_html="<i class='fa fa-refresh'></i> "+restart_bot;
		    	            		$('#bot-'+page_id).html(enable_html).removeClass('btn-outline-danger').removeClass('disable_bot').addClass('btn-outline-warning').addClass('enable_bot').removeClass('disabled').attr('restart','1');
		    	            	}
		    	            	else 
		    	            	{
		    	            		alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.error,function(){});
		    	            		$('#bot-'+page_id).removeClass('disabled');
		    	            	}
		    	            }
		    	               
		    	        });

		    	      }
		    	    }
		    	});

		    },
		    function(){     
		    });
		});


		$(".page_delete").click(function(){
			var ifyoudeletethispage = "<?php echo $ifyoudeletethispage; ?>";
  			var page_table_id = $(this).attr('table_id');

		    alertify.confirm('<?php echo $this->lang->line("are you sure");?>',ifyoudeletethispage, 
		    function(){ 
		        $("#delete_confirmation_body").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
				$("#delete_confirmation").modal();

				$.ajax
				({
				   type:'POST',
				   // async:false,
				   url:base_url+'messenger_bot/ajax_delete_page_action',
				   data:{page_table_id:page_table_id},
				   success:function(response)
				    {
				        $("#delete_confirmation_body").html(response);
				    }
				       
				});
		    },
		    function(){     
		    });
		});


		$(".delete_account").click(function(){
			var ifyoudeletethisaccount = "<?php echo $ifyoudeletethisaccount; ?>";
			var user_table_id = $(this).attr('table_id');
			alertify.confirm('<?php echo $this->lang->line("are you sure");?>',ifyoudeletethisaccount, 
		    function(){
		    	$("#delete_confirmation_body").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
				$("#delete_confirmation").modal();
				$.ajax
				({
				   type:'POST',
				   // async:false,
				   url:base_url+'messenger_bot/ajax_delete_account_action',
				   data:{user_table_id:user_table_id},
				   success:function(response)
				    {
				    	if(response == 'success')
				    	{
				    		var link="<?php echo site_url('home/logout'); ?>"; 
							window.location.assign(link);
				    	}
				    	else
					        $("#delete_confirmation_body").html(response);
				    }
				       
				});

		    },
		    function(){     
		    });
		});


		$('#delete_confirmation').on('hidden.bs.modal', function () { 
			location.reload(); 
		});


		$("#submit").click(function(){
			var facebooknumericidfirst = "<?php echo $facebooknumericidfirst; ?>";
			var fb_numeric_id = $("#fb_numeric_id").val().trim();
			if(fb_numeric_id == '')
			{
				alertify.alert('<?php echo $this->lang->line("Alert"); ?>',facebooknumericidfirst,function(){});
				return false;
			}

			var loading = '<br/><br/><img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block"><br/>';
        	$("#response").html(loading);

			$.ajax
			({
			   type:'POST',
			   // async:false,
			   url:base_url+'messenger_bot/send_user_roll_access',
			   data:{fb_numeric_id:fb_numeric_id},
			   success:function(response)
			    {
			        $("#response").html(response);
			    }
			       
			});
		});

		
		$(document.body).on('click','#fb_confirm',function(){
			var loading = '<br/><br/><img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block"><br/>';
        	$("#response").html(loading);
			$.ajax
			({
			   type:'POST',
			   // async:false,
			   url:base_url+'messenger_bot/ajax_get_login_button',
			   data:{},
			   success:function(response)
			    {
			        $("#response").html(response);
			    }
			       
			});
		});


	});
</script>