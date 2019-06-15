<?php $this->load->view("include/upload_js"); ?>
<div class="row padding-20">
	<div class="col-xs-12 col-md-6">
		<div class="box box-primary">
			<div class="box-header ui-sortable-handle  text-center" style="cursor: move;margin-bottom: 0px;">
				<i class="fa fa-envelope"></i>
				<h3 class="box-title"><?php echo $this->lang->line("send message button generator"); ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body" >
				<form action="#" enctype="multipart/form-data" class="padding-20">
					<div class="form-group text-center">
						<label style="width:100%">
							<?php echo $this->lang->line("choose pages"); ?> *
							 <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("choose pages") ?>" data-content="<?php echo $this->lang->line("You must have to choose a page for which you want the 'Send Message' button.") ?>"><i class='fa fa-info-circle'></i> </a>
						</label>
						<select class="form-control" id="page" name="page">	
						<option value=""><?php echo $this->lang->line("select page"); ?></option>
						<?php
							$i=0;
							$first_username;
							foreach($page_info as $key=>$val)
							{	
								$username=$val['username'];
								if($username=="")
								{
									$username=$val["page_id"];
								}
								if($i==0) $first_username = $username;
								$page_name=$val['page_name'];
								echo "<option value='{$username}'>{$page_name}</option>";								
							}
						 ?>						
						</select>						
					</div>
					<br/>
					<div class="form-group text-center">
						<label style="width:100%">
							<?php echo $this->lang->line("choose among default images or put image url"); ?> * <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("choose image") ?>" data-content="<?php echo $this->lang->line("You can choose among our default button images or can put an image url. Remember that image url has higher priority than default images.") ?>"><i class='fa fa-info-circle'></i> </a>
						</label>
							<br>
							<br>
							<?php
							for ($i=1; $i <=3 ; $i++) 
							{ 
								if($i==1) $checked='checked';
								else $checked='';
								echo "&nbsp;&nbsp; <input {$checked} type='radio' id='{$i}' value='{$i}' name='image'/>&nbsp;&nbsp;";
								echo "<label for='{$i}'><img style='cursor:pointer' src='".base_url('assets/images/messenger_button/'.$i).".png'></label><br/>";
							}
							?>
							<h3 style='width:100%' class='text-center'><?php echo $this->lang->line("or") ?></h3>
							<input type="text" class="form-control" name="url" id="url" placeholder="<?php echo $this->lang->line('Choose image from above or put a image url directly here');?>">
							

					</div>			
					
					<!-- <div class="box-footer clearfix">
						<div class="col-xs-12">
							<button style='width:100%;margin-bottom:10px;' class="btn btn-primary center-block btget embed code and see previewn-lg" id="get_button" name="get_button" type="button"><i class="fa fa-code"></i> Get Embed code</button>
						</div>
					</div> -->

				</form>
			</div>
			
		</div>
	</div>  <!-- end of col-6 left part -->

	<div class="col-xs-12 col-md-6">
		<div class="box box-primary">
			<div class="box-header ui-sortable-handle  text-center" style="cursor: move;margin-bottom: 0px;">
				<i class="fa fa-code"></i>
				<h3 class="box-title"><?php echo $this->lang->line("get embed code and see preview"); ?></h3>
				<!-- tools box -->
				<div class="pull-right box-tools"></div><!-- /. tools -->
			</div>
			<div class="box-body" >
				<div class="form-group text-center">
					<label style="width:100%">
						 <?php echo $this->lang->line("copy embed code and paste it in your site"); ?>
						 <a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Copy embed code") ?>" data-content="<?php echo $this->lang->line("Copy this code and paste it in your web pages to show 'Send Message' button. Clicking this button will take your visitors to messenger for sending message in your page. You can see preview below to ensure how it will look.") ?>"><i class='fa fa-info-circle'></i> </a>
					</label>
					<textarea name="copy_code" id="copy_code" class='form-control' style='height:200px;text-align: left !important;'></textarea>				
				</div>
				<div class="form-group text-center">
					<div id="preview">						
						<a href=""><img src=""><a>
					</div>	
				</div>
			</div>
			
		</div>
	</div>  <!-- end of col-6 left part -->

</div>


<style type="text/css" media="screen">
	#preview img{max-width:100% !important;}
</style>


<?php $this->load->view("facebook_ex/campaign/style");?>
		

<?php 
	
	$PleaseapagetogenerateSendMessagebutton = $this->lang->line("Please a page to generate 'Send Message' button.");
	$Pleasechooseamongdefaultimagesorputlinkbutton = $this->lang->line("Please choose among default images or put link button.");

 ?>

<script>
 
	$j("document").ready(function(){

		var base_url="<?php echo base_url();?>";

		 $("#page").select2(); 


		$('[data-toggle="popover"]').popover(); 
		$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});


        function get_button()
        {
        	var page = $("#page").val();
        	var image = $("input[name=image]:checked").val();
        	var link = $("#url").val();
        	var PleaseapagetogenerateSendMessagebutton = "<?php echo $PleaseapagetogenerateSendMessagebutton; ?>";
        	var Pleasechooseamongdefaultimagesorputlinkbutton = "<?php echo $Pleasechooseamongdefaultimagesorputlinkbutton; ?>";

        	if(page=="")
        	{
        		alertify.alert('<?php echo $this->lang->line("Alert");?>',PleaseapagetogenerateSendMessagebutton,function(){});
        		return;
        	}

        	if(image=="" && link=="")
        	{
        		alertify.alert('<?php echo $this->lang->line("Alert");?>',Pleasechooseamongdefaultimagesorputlinkbutton,function(){});
        		return;
        	}

        	if(link=="")
        	{
        		link = base_url+"assets/images/messenger_button/"+image+".png";
        	}

        	var embed_code = '<a href="http://m.me/'+page+'"><img src="'+link+'"></a>';

        	$("#copy_code").text(embed_code);
        	$("#preview").html(embed_code);

        }

        // $(document.body).on('click','#get_button',get_button);
        $(document.body).on('change','#page',get_button);
        $(document.body).on('blur','#url',get_button);
        $(document.body).on('click','input[name=image]',get_button);


    });



</script>
