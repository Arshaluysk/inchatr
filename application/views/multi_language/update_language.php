<?php include("application/views/multi_language/styles.php"); ?>

<div class="well well_border_left">
	<h4 class="text-center"> <i class="fa fa-dashboard"></i> <?php echo $this->lang->line("update language"); ?></h4>
</div>


<style>.add_navbar > li:last-child a:before { font-family:"Font Awesome 5 Free";font-weight:900;content:"\f044"; }</style>

<div class="container-fluid">
	<div class="row">
		<div class="col-lg-5 col-md-5 col-xs-12 col-sm-12">
			<ul class="add_navbar">
				<li><a style="color: #0039e2;" href="<?php echo base_url("multi_language/index"); ?>"><i class="fa fa-home"></i> <?php echo $this->lang->line("language dashboard"); ?></a></li>
				<li><a> <?php echo $this->lang->line("update language"); ?></a></li>
			</ul>
		</div>
	</div>
</div>


<div class="container-fluid">
	<div class="row">
		<!-- Main-application section -->
		<?php if($languageName == "main_app") : ?>
			<div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
				<div class="main_header">
					<ul class="headerlist">
						<li>
							<?php echo $this->config->item('product_name').' '.$this->lang->line('languages')." : ".$languagename." (".count($folderFiles)." files)"; ?>
						</li>
					</ul>
				</div>
			</div>

			<!-- language name update section -->
			<?php if($languagename != "english") : ?>
				<section id="language_name_sections">
					<div class="row">
						<div class="col-lg-12 text-center language_name_field">
							<div class="input-group" id="languagename_field">
								<input type="text" id="language_name" name="language_name" value="<?php echo $languagename; ?>" class="form-control text-center" style="font-size: 14px;">
								<span class="input-group-btn">
									<button class="btn btn-primary" type="submit" id="update_language_name"><i class="fa fa-save"></i> <?php echo $this->lang->line("update"); ?></button>
								</span>
							</div>
							<span id="exist" style="display:none;color:red;text-transform:capitalize;text-align:center"><?php echo $this->lang->line("this language already exist. you can not add this language again."); ?></span>
						</div>
					</div>
					<br>
				</section>
			<?php else : ?>
				<div class="row">
					<input type="hidden" name="language_name" value="<?php echo $languagename; ?>" class="hidden">
					<div class="col-lg-6 col-md-6 col-md-offset-3 col-lg-offset-3 col-xs-11 col-sm-11 not_english text-center">
						<p style="margin-bottom: 0;"><?php echo $this->lang->line("English Language Name can not be updated. You Can Update the file."); ?></p>
					</div>
				</div>
			<?php endif; ?>
			
			<!-- fbinboxer files sections -->
			<div class="row" style="padding: 0px 0px 0 26px !important;">
				<?php if(!empty($folderFiles)) : 
					$i = 0;
					foreach ($folderFiles as $value) : 
				?>
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 text-center allFiles" file_type="main-application_<?php echo $i;?>"  id="main_app">
						<div>
							<h4><i class="fa fa-file-text"></i> 
								<?php echo $value; ?>
								<i id="<?php echo str_replace(".php",'',$value); ?>" style="color:#13d408;display: none;" class="fa fa-check-circle"></i>
								
							</h4>
						</div>
					</div>
				<?php
					$i++; 
					endforeach; 
				else:
				?>
					<div class="text-center" style="font-weight: bold;background: #FFF;cursor: pointer; margin: 80px 278px;box-shadow: 3px 3px 3px #cccccc,-2px 0px 3px #cccccc;padding: 16px; color: #ff3d3d;box-shadow: 3px 3px 3px #cccccc, -2px 0px 3px #cccccc;">
						<p><i class="fa fa-close"></i> <?php echo $this->lang->line("This language folder is empty. No files to show."); ?></p>
					</div>
				<?php endif; ?>
			</div>
		
		<!-- Plugin Section -->
		<?php elseif ($languageName == "plugin") : ?>

			<div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
				<div class="main_header" id="plugs">
					<ul class="headerlist">
						<li>
							<?php echo $this->lang->line('plugin languages :')." ".$plugin_file; ?>
						</li>
					</ul>
				</div>
			</div>
			<input type="hidden" id="language_name" name="language_name" value="<?php echo $plugin_file; ?>" class="form-control text-center">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 text-center allFiles" file_type="plugin_0" id="plug" style="position: relative;left:376px;width: 27%;">
				<div>
					<h4><i class="fa fa-plug"></i> 
						<?php echo $plugin_file; ?>
						<i id="plugins1" class="fa fa-check-circle" style="color:#13d408;display: none;"></i>
					</h4>
				</div>
			</div>

		<!-- Addon Section -->
		<?php elseif ($languageName == "addon") : ?>
			
			<div class="col-lg-12 col-md-12">
				<div class="main_header">
					<ul class="headerlist">
						<li>
							<?php echo ucfirst(str_replace("_",' ',$languagename))." ".$this->lang->line('add-on languages')." (".count($module_language_folders)." languages)"; ?>
						</li>
					</ul>
				</div>
			</div>

			<input type="hidden" id="language_name" name="language_name" value="<?php echo $languagename; ?>" class="form-control text-center" style="font-size: 14px;">

			<div class="row" style="padding: 0px 20px 0 30px !important;">
				<?php
				if(!empty($module_language_folders)) :
					$i = 0;
					foreach ($module_language_folders as $value) : ?>
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 text-center allFiles" file_type="add-on_<?php echo $i; ?>" folderName="<?php echo $value; ?>" id="addons">
						<div>
							<h4><i class="fa fa-folder-open"></i> 
								<?php echo ucfirst($value); ?>
								<i id="<?php echo $value; ?>" class="fa fa-check-circle" style="color:#13d408;display: none;"></i>
							</h4>
						</div>
					</div>
					
				<?php 
					$i++;
					endforeach; 
					else : 
				?>
						<div id="no_files" class="text-center">
							<p><i class="fa fa-close"></i> 
								<?php echo $this->lang->line("This language folder is empty. No files to show."); ?>
							</p>
						</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>


<?php 
$giveAname = $this->lang->line("you have not given any name of your language. please first give your language name or update."); 
$editable_language = $this->uri->segment(3);
?>

<script>
	$(document).ready(function(){

		// updating language name
		$(document.body).on('click', '#update_language_name', function(event) {
			event.preventDefault();

			var base_url 	 = '<?php echo base_url(); ?>';
			var languagename = $("#language_name").val();
			var pre_value 	 = '<?php echo $editable_language; ?>';


			if(languagename == '')
			{
				var giveAname = "<?php echo $giveAname; ?>";
				alertify.alert('<?php echo $this->lang->line("Alert")?>',giveAname,function(){});
				return false;
			}

			if(languagename === pre_value)
			{
				alertify.error('<?php echo $this->lang->line("this language already exist, no need to update.") ?>');

			} else 
			{
				$.ajax({
					url: base_url+'multi_language/updating_language_name',
					type: 'POST',
					dataType:'JSON',
					data: {languagename: languagename,pre_value:pre_value},
					success:function(response)
					{
						if(response.status =="1")
						{
							var name = response.new_name;
							var currentUrl = base_url+"multi_language/edit_language/"+name+"/main_app";
							location.assign(currentUrl);
							
						} 
						else if(response.status =='3') 
						{
							alertify.error('<?php echo $this->lang->line("only characters and underscores are allowed.") ?>');
						} else
						{
							alertify.error('<?php echo $this->lang->line("this language is already exist, please try with different one."); ?>');
						}
					}
				});
			}
		});


		// showing language files data from directory
		$(document.body).on('click', '.allFiles', function(event) {
			event.preventDefault();

			// getting which file is clicked
			var fileType 			= $(this).attr('file_type');
			var languageFieldSelect = $(this).attr('id');
			var languageName 		= '<?php echo $editable_language; ?>';
			var langname_existance  = $("#language_name").val();
			var addonLangName		= $(this).attr("folderName");
			var base_url 			= "<?php echo base_url(); ?>";

			// if the language name filed is empty
			if(languageFieldSelect == "main_app") 
			{
				if(langname_existance == '') 
				{
					var giveAname = "<?php echo $giveAname; ?>";
					alertify.alert('<?php echo $this->lang->line("Alert")?>',giveAname,function(){});
					return false;
				}
			}

			// loading processing img
			var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
			$('#response_status').html(loading);

		    $.ajax({
		    	type:'POST',
		    	url: base_url+"multi_language/ajax_get_lang_file_data_update",
		    	dataType:'JSON',
		    	data: {fileType:fileType,languageName:languageName,langname_existance:langname_existance},
		    	success:function(response)
		    	{
			    	if(response.status == "1") 
			    	{
			    		$('#language_file_modal').modal();
			  			$('#languageDataBody').html(response.langForm);
			    		$('#response_status').html('');
			    		$("#languName").html(languageName);
			    		$("#addon_languName").html(addonLangName);

			    	} else if(response.status == "3")
			    	{
			    		alertify.alert('<?php echo $this->lang->line("Alert")?>','<?php echo $this->lang->line("your given name has not updated, please update the name first."); ?>',function(){});
			    	} else
			    	{
			    		$('#response_status').html(loading);
			    	}
		    	}

		    });

		});
		

		// saving language file with language folder name
		$(document.body).on('click', '.update_language_button', function(event) {
			event.preventDefault();

			var languageName = $('#language_name').val();

			// if the language name filed is empty
			if(languageName == '') 
			{
				var giveAname = "<?php echo $giveAname; ?>";
				alertify.alert('<?php echo $this->lang->line("Alert")?>',giveAname,function(){});
				return false;
			}

			// Generate the language folder name from input
			var folder_name = $("#language_folder_name").val(languageName);
			// detect the file type clicked
			var clickedFile = $("#language_file_id").val();
			var base_url 	= "<?php echo base_url(); ?>";

			var processingloading 	= '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
			$('#saving_response').html(processingloading);

			var alldatas = new FormData($("#language_creating_form")[0]);

		    $.ajax({
		    	type:'POST',
		    	url: base_url+"multi_language/ajax_updating_lang_file_data",
		    	data: alldatas,
		    	dataType : 'JSON',
		    	cache: false,
		    	contentType: false,
		    	processData: false,
		    	success:function(response){
		    		if(response.status=="1")
			        {
			         	$("#saving_response").html(response.message);
			         	$("#"+response.fileName).css("display","inline");
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


<!-- Modal Section -->

<div class="modal fade" id="htm" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" id="languag_data">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user-check"></i> <?php echo $this->lang->line("lead list");?></h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12 table-responsive" id="response_div" style="padding: 20px;"></div>
                </div>               
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="language_file_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id='modal_close' data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-edit"></i> <?php echo $this->lang->line("update your language");?></h4>
            </div>
          	<div class="modal-body">
                <style>
                	.download_box{border:1px solid #ccc;margin: 0 auto;text-align: center;margin-top:3%;padding-bottom: 20px;background-color: #fffddd;color:#000;}
                </style>
				
				<div class="row">
					<div id="response_status"></div>
				</div>

	            <div class="row">
	                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	                	<h3 class="hidden" id="languName"></h3>
	                	<h3 class="hidden" id="addon_languName"></h3>
	                	<div id="languageDataBody">
						
	                	</div>
	                </div>
	            </div>
           
            </div>
        </div>
    </div>
</div>





