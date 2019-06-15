<?php include("application/views/multi_language/styles.php"); ?>
<style>.add_navbar > li:last-child a:before { font-family:"Font Awesome 5 Free";font-weight:900;content:"\f055"; }</style>


<div class="well well_border_left" id="home">
	<h4 class="text-center"> <i class="fa fa-dashboard"></i> <?php echo $this->lang->line("add new language"); ?></h4>
</div>

<!-- moving nav section -->
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-5 col-md-5 col-xs-12">
			<ul class="add_navbar" style="margin-left: -3px;">
				<li><a style="color: #0039e2;" href="<?php echo base_url("multi_language/index"); ?>"><i class="fa fa-home"></i> <?php echo $this->lang->line("language dashboard"); ?></a>
				</li>
				<li><a> <?php echo $this->lang->line("add language"); ?></a></li>
			</ul>
		</div>
	</div>
</div>

<style>

</style>

<div class="container-fluid">

	<!-- Language name saving section -->
	<section id="language_name_section">
		<br>
		<div class="row" id="LANGUAGENAME">
			<div class="col-lg-12 text-center language_name_field" style="padding: 0 16px 0 12px;">
				<div class="input-group" id="languagename_field">
					<input type="text" id="language_name" name="language_name" class="form-control text-center" style="font-size:14px;" placeholder="<?php echo $this->lang->line("language name"); ?>">
					<span class="input-group-btn">
						<button class="btn btn-primary" type="submit" id="save_language_name"><i class="fa fa-save"></i> <?php echo $this->lang->line("save"); ?></button>
					</span>
				</div>
			</div>
		</div>
		<br>
	</section>
	<br>

	<!-- Tab row -->
	<div class="row">
		<div class="col-lg-12">
			<ul class="nav nav-tabs">
				<li class="active" id="main_tab">
					<a data-toggle="tab" href="#fbinboxer_languages_tab"> <?php echo $this->config->item('product_name').' '.$this->lang->line('languages'); ?></a>
				</li>
				<li id="plugin_tab">
					<a data-toggle="tab" href="#plugins_languages_tab"> <?php echo $this->lang->line("plugins languages"); ?></a>
				</li>
				<li id="addon_tab">
					<a data-toggle="tab" href="#addons_languages_tab"> <?php echo $this->lang->line("add-ons languages"); ?></a>
				</li>
			</ul>
		</div>
	</div>
	
	<!-- Language file section -->
  	<div class="tab-content">
  		<!-- main app section -->
	    <div id="fbinboxer_languages_tab" class="tab-pane fade in active">
			<section id="main_app_section">
				<br>
				<div class="row" style="padding: 0px 0px 0 9px !important;">
					<?php  
						$i=0;
						foreach ($file_name as $value) :  
					?>
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 text-center language_file" file_type="main-application_<?php echo $i;?>" file_name="<?php echo $value; ?>" id="main_app">
						<div class="langFile col-lg-12 text-center">
							<h4><i class="fa fa-file-text"></i>
								<?php echo $value; ?> 
								<i id="<?php echo str_replace(".php",'',$value); ?>" style="color:#13d408;display: none;" class="fa fa-check-circle"></i>
							</h4>
						</div>
					</div>
					<?php 
						$i++; 
						endforeach; 
					?>
				</div>
			</section>
	    </div>
	
		<!-- plugin section -->
	    <div id="plugins_languages_tab" class="tab-pane fade">
			<section id="plugin_section">
				<br>
				<br>
				<div class="row">
					<div class="col-lg-6 col-lg-offset-3 col-xs-12 col-md-4 col-sm-12 language_file" file_type ="plugin_0" id="plugins">
						<div class="langFile text-center">
							<h4 id="lang_Name">
								<i class="fa fa-plug"></i> Plugin Languages 
								<i id="plugins1" style="color:#13d408;display: none;" class="fa fa-check-circle"></i>
							</h4>
						</div>
					</div>
				</div>
			</section>
	    </div>
	
		<!-- addon section -->
	    <div id="addons_languages_tab" class="tab-pane fade">
	      	<section id="addon_section">
	      		<br>
	      		<div class="row" style="padding: 0px 0px 0 9px !important;">
	      			<?php $i = 0;
	      			 foreach ($addons as $addon): ?>
	      			<div class="col-lg-3 col-xs-12 col-md-3 col-sm-12 text-center language_file" id="addons" file_type="add-on_<?php echo $i; ?>">
      					<div class="langFile col-lg-12 text-center">
      						<h4 style="font-weight: 400 !important;text-transform: capitalize;font-size: 11px;">
      							<i class="fa fa-tags"></i> 
      							<?php echo str_replace(array('.php','_','lang'), ' ', $addon); ?> 
      							<i id="<?php echo str_replace(array('.php','_','lang'), array('','',''), $addon); ?>" style="color:#13d408; display: none;" class="fa fa-check-circle"></i>
      						</h4>
      					</div>
	      			</div>
	      			<?php $i++; endforeach; ?>
	      		</div>
	      	</section>
	    </div><br><br>
  	</div>
</div>


<?php $giveAname = $this->lang->line("you haven't given any name of your language. please first save your language name."); ?>

<script>

	$(document).ready(function(){

		// save language name for all
		$(document.body).on('click', '#save_language_name', function(event) {
			event.preventDefault();
			var languageName = $('#language_name').val();

			// if the language name filed is empty
			if(languageName == '') {
				var giveAname = "<?php echo $giveAname; ?>";
				alertify.alert('<?php echo $this->lang->line("Alert")?>',giveAname,function(){});
				return false;
			}

			$.ajax({
				url: base_url+'multi_language/save_language_name',
				type: 'POST',
				data: {languageName: languageName},
				success:function(response)
				{
					if(response == "1") 
					{
						alertify.success('<?php echo $this->lang->line("your given information has been successfully added") ?>');

					} else if(response == '3') 
					{
						alertify.error('<?php echo $this->lang->line("only characters and underscores are allowed.") ?>');
					}
					else 
					{
						alertify.error('<?php echo $this->lang->line("sorry, this language already exists, you can not add this again.") ?>');
					}

				}
			});
	
		});


		// showing language files data from directory
		$(document.body).on('click', '.language_file', function(event) {
			event.preventDefault();

			var languageFieldSelect = $(this).attr('id');
			var languageName = $.trim($('#language_name').val());
			var fileType = $(this).attr('file_type');
			var base_url = "<?php echo base_url(); ?>";

			// if the language name filed is empty
			if(languageName == '') 
			{
				var giveAname = "<?php echo $giveAname; ?>";
				alertify.alert('<?php echo $this->lang->line("Alert")?>',giveAname,function(){});
				return false;
			} 

			// loading processing img
			var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
			$('#response_status').html(loading);

		    $.ajax({
		    	type:'POST',
		    	url: base_url+"multi_language/ajax_get_language_details",
		    	data: {fileType:fileType,languageName:languageName},
		    	dataType: 'JSON',
		    	success:function(response){
			    	if(response.result == "1") 
			    	{
	    		  		$('#language_file_modal').modal();
	    				$('#languageDataBody').html(response.langForm);
	    		  		$("#language_type_modal").html(fileType);
	    		  		$('#response_status').html('');
	    		  		$("#new_lang_val").html(languageName);

			    	} else
			    	{
			    		alertify.alert('<?php echo $this->lang->line("Alert")?>','<?php echo $this->lang->line("you have not given or saved any language, please save the language name before adding language files."); ?>',function(){});
			    	}
		    	}
		    });
		});
		

		// saving language file with language folder name
		$(document.body).on('click', '.save_language_button', function(event) {
			event.preventDefault();

			var languageFieldSelect = $(this).attr('id');
			var languageName 		= $('#language_name').val();

			// if the language name filed is empty
			if(languageName == '') {
				var giveAname = "<?php echo $giveAname; ?>";
				alertify.alert('<?php echo $this->lang->line("Alert")?>',giveAname,function(){});
				return false;
			}

			// Generate the language folder name from input
			var folder_name = $("#language_folder_name").val(languageName);
			// detect the file type clicked
			var clickedFile = $("#language_file_id").val();
			var ftype 		= $("#language_type_modal").html();
			var base_url 	= "<?php echo base_url(); ?>";
			var loading 	= '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
			$('#saving_response').html(loading);

			var alldatas = new FormData($("#language_creating_form")[0]);

		    $.ajax({
		    	type:'POST',
		    	url: base_url+"multi_language/ajax_language_file_saving",
		    	data: alldatas,
		    	dataType : 'JSON',
		    	cache: false,
		    	contentType: false,
		    	processData: false,
		    	success:function(response){
		    		if(response.status=="1")
			        {
			         	$("#saving_response").html(response.message);
			         	$("#"+response.file_name).css("display","inline");
			        }
			        else
			        {
			         	$("#saving_response").html(response.message);
			        }
		    	}
		    });
		});

	});
</script>



<div class="modal fade" id="language_file_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id='modal_close' data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("please add your language");?></h4>
            </div>
          	<div class="modal-body">
                <style>
                	.download_box{border:1px solid #ccc;margin: 0 auto;text-align: center;margin-top:3%;padding-bottom: 20px;background-color: #fffddd;color:#000;}
                </style>
				<h3 class="hidden" id="language_type_modal"></h3>
				<h4 class="hidden" id="new_lang_val"></h4>
				<div class="row">
					<div id="response_status"></div>
				</div>
				
	            <div class="row">
	                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	                	<div id="languageDataBody">
						
	                	</div>
	                </div>
	            </div>
           
            </div>
        </div>
    </div>
</div>
