<?php include("application/views/multi_language/styles.php"); ?>
<style>
	.info-box, .modal-dialog {-webkit-box-shadow: 0 0 0 transparent !important; -moz-box-shadow: 0 0 0 transparent !important;box-shadow: 0 0 0 transparent !important;}
	.modal-content {background: #ddd;}
    .modal-body {position: relative;padding:30px 40px;}
    @media screen and (max-width: 600px)
    {
		.modal-content {background: #ddd;}
	    .modal-body {position: relative;padding: 40px;}
    	.folder_head {background:#fff;padding:10px 0;margin:10px -34px -8px 17px;box-shadow:5px 3px 3px #CECECE,-5px -2px 3px #cecece;}
    	.folder_head:hover { background: <?php echo $THEMECOLORCODE; ?>;color:#fff;cursor:pointer;padding: 10px 0;margin:10px -34px -8px 17px;box-shadow: 5px 3px 3px #CECECE,-5px -2px 3px #cecece;}
    }
</style>



<div class="well well_border_left">
	<h4 class="text-center"> <i class="fa fa-dashboard"></i> <?php echo $this->lang->line("Language Lists"); ?></h4>
</div>

<div class="container-fluid" id="loading">
	<div class="row">

		<!-- Add or Delete button Section -->
		<div class="col-lg-6 col-xs-6 col-md-6 col-sm-6" style="padding-bottom: 20px;">
			<a class="btn btn-default add text-center" href="<?php echo base_url('multi_language/create_new_lang'); ?>">
				<i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('add a new language'); ?>
			</a>
		</div>
		<div class="col-lg-6 col-xs-6 col-md-6 col-sm-6" style="padding-bottom: 20px;">
			<a class="btn btn-danger delete text-center">
				<i class="fa fa-trash"></i> <?php echo $this->lang->line('delete a language'); ?>
			</a>
		</div>

		<!-- Tab section -->
		<div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
			<ul class="nav nav-tabs">
				<li class="active">
					<a data-toggle="tab" href="#fbinboxer_languages_tab"> <?php echo $this->config->item('product_name').' '."Languages (".count($lang).")"; ?>
					</a>
				</li>
				<li>
					<a data-toggle="tab" href="#plugins_languages_tab"> <?php echo $this->lang->line("plugins languages")." (".count($plugins_files).")"; ?>
					</a>
				</li>
				<li>
					<a id="addonTab" data-toggle="tab" href="#addons_languages_tab"> <?php echo $this->lang->line("add-ons languages")." (".count($addons).")"; ?>
					</a>
				</li>
			</ul>
		</div>
	</div>


	<!-- Performance Section -->
  	<div class="tab-content">
		
		<!-- fbinboxer language section -->
	    <div id="fbinboxer_languages_tab" class="tab-pane fade in active">
			<section id="main_application_section">
				<br><br>
				<div class="row">
					<?php 
					$i =0;
					foreach($lang as $lang_name) :  ?>
					<div class="col-xs-12 col-md-3 col-lg-3 text-center" style="margin-bottom: 20px;">
						<input type="hidden" name="folder_type" value="main-application_<?php echo $i;?>">
						<div class="language">
							<div class="row">
								<h4 class="language-name"><i class="fa fa-folder-open"></i> <?php echo $lang_name; ?></h4>
							</div>
							<div class="prog"></div>
							<div class="row">
								<div class="action_btn">
									<div class="col-xs-6 col-md-6 col-lg-6">
										<div>
											<a href="<?php echo base_url("multi_language/edit_language/".$lang_name."/main_app"); ?>" class="pull-left btn-sm btn btn-outline-primary edit_btn"><i class="fa fa-pencil-square-o"></i> <?php echo $this->lang->line("edit"); ?></a>
										</div>
									</div>
									<div class="col-xs-6 col-md-6 col-lg-6">
										<div>
											<a target="_blank" class="btn btn-sm btn-outline-info" title="<?php echo $this->lang->line("download this as backup") ?>" href="<?php echo base_url("multi_language/downloading_language_folder_zip/".$lang_name."/main_app"); ?>">
												<i class="fa fa-download"></i> <?php echo $this->lang->line("download"); ?></a>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php $i++; endforeach; ?>
				</div>
			</section>
	    </div>

		<!-- plugin sections -->
	    <div id="plugins_languages_tab" class="tab-pane fade">
	      	<section id="plugin_section">
	      		<br><br>
	      		<div class="row">
	      			<?php 
	      			$i=0;
	      			foreach($plugins_files as $file_name) :  ?>
	      			<div class="col-xs-12 col-md-3 col-lg-3 text-center" style="margin-bottom: 20px;">
	      				<div class="language">
							<div class="row">
		      					<h4 class="language-name"><i class="fa fa-folder-open"></i> <?php echo str_replace(".php","",$file_name); ?></h4>
							</div>
	      					<div class="prog"></div>
	      					<div class="row">
	      						<div class="action_btn" file_type="plugin_<?php echo $i;?>">
									<div class="col-xs-6 col-md-6 col-lg-6">
	      								<div>
	      									<?php $file_name = str_replace('.php','',$file_name); ?>
	      									<a title="<?php echo $this->lang->line("update this language") ?>" href="<?php echo base_url("multi_language/edit_language/".$file_name."/plugin"); ?>" class="pull-left btn-sm btn btn-outline-primary edit_btn"><i class="fa fa-pencil-square-o"></i> <?php echo $this->lang->line("edit"); ?></a>
	      								</div>
	      							</div>
	      							<div class="col-xs-6 col-md-6 col-lg-6">
	      								<div>
											<a target="_blank" class="btn btn-sm btn-outline-info" title="<?php echo $this->lang->line("download this as backup") ?>" href="<?php echo base_url("multi_language/downloading_language_folder_zip/".$file_name."/plugin"); ?>">
											<i class="fa fa-download"></i> <?php echo $this->lang->line("download"); ?>
										</a>
	      								</div>
	      							</div>
	      						</div>
	      					</div>
	      				</div>
	      			</div>
	      			<?php $i++; endforeach; ?>
	      		</div>
	      	</section>
	    </div>
		
		<!-- Add-on Sections -->
	    <div id="addons_languages_tab" class="tab-pane fade">
			<section id="addon_section">
				<br><br>
				<div class="row">
					<?php 
					$i = 0;
					foreach($addons as $addon_name) :  ?>
					<div class="col-xs-12 col-md-3 col-lg-3 text-center" style="margin-bottom: 20px;">
						<div class="language">
							<div class="row">
								<h4 class="language-name"><i class="fa fa-tags"></i> <?php echo str_replace("_"," ",$addon_name); ?></h4>
							</div>
							<div class="prog"></div>
							<div class="row">
								<div class="action_btn" file_type="add-on_<?php echo $i;?>">
									<div class="col-xs-6 col-md-6 col-lg-6">
										<div>
											<a href="<?php echo base_url("multi_language/edit_language/".$addon_name."/addon"); ?>" class="pull-left btn-sm btn btn-outline-primary edit_btn"><i class="fa fa-pencil-square-o"></i> Edit</a>
										</div>
									</div>								
									<div class="col-xs-6 col-md-6 col-lg-6">
										<div>
											<a id="addons" class="btn btn-sm btn-outline-info download_addon" addonname="<?php echo $addon_name; ?>" title="<?php echo $this->lang->line("download this as backup") ?>"><i class="fa fa-download"></i> <?php echo $this->lang->line("download"); ?>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php $i++; endforeach; ?>
				</div>
			</section>
	    </div>

  	</div>
</div>


<?php
	// Retrieve the selected language from session
	$selectedlanguage = $this->session->userdata("selected_language"); 
?>

<script>
	$(document).ready(function($) {

		// getting addon language folders to download
		$(document.body).on('click', '.download_addon', function(event) {
			event.preventDefault();

			var base_url    = "<?php echo base_url(); ?>";
			var addon 	    = $(this).attr("addonname");
			var clickedtype = $(this).attr("id");

			$.ajax({
				url: base_url +"Multi_language/get_addon_folders_to_download",
				type: 'POST',
				data: {addon: addon},
				success:function(response)
				{
					if(response)
					{
						$("#language_file_modal").modal();
						$('#languageDataBody').html(response);
						$("#addon_names").html(addon);
						$("#addon_type").html(clickedtype);
						$(".modal-title").html('<?php echo '<i class="fa fa-download"></i>'." ".$this->lang->line('click on the language to download') ?>');
					} else
					{
						$("#addon_names").html('');
						$("#addon_type").html('');
					}
				}
			})
		});

		// getting language folders to delete from all
		$(document.body).on('click', '.delete', function(event) {
			event.preventDefault();

			var base_url = "<?php echo base_url(); ?>";

			$.ajax({
				url: base_url+'multi_language/get_all_languages_to_delete',
				type: 'POST',
				data: {param1: 'value1'},
				success:function(response)
				{
					$("#language_file_modal").modal();
					$("#languageDataBody").html(response);
					$(".modal-title").html('<?php echo '<i class="fa fa-trash"></i>'." ".$this->lang->line('click on the language to delete') ?>');
					$("#addon_names").html('');
					$("#addon_type").html('');

				}
			})
		});


		// deleting the language from all, main,plugin,addons
		$(document.body).on('click', '.delete_language', function(event) 
		{
			event.preventDefault();
			var langname = $(this).html();
			var selectedLang = <?php echo '"'.$selectedlanguage.'"'; ?>;

			if(langname == 'english')
			{
				alertify.error('<?php echo $this->lang->line("sorry, english language can not be deleted.")?>');
				return;
			}

			if(langname == selectedLang)
			{
				alertify.error('<?php echo $this->lang->line("sorry,this is your selected language, it can not be deleted.")?>');
				return;
			}

			var that_parent = $(this).parent().parent();

			alertify.confirm('<?php echo $this->lang->line("Alert")?>','<?php echo $this->lang->line("are you sure that you want to delete this language? it will delete all files of this language."); ?>',function()
			{
				$.ajax({
					url: base_url+'multi_language/delete_language_from_all',
					type: 'POST',
					data: {langname: langname},
					success:function(response)
					{
						if(response =='1')
						{
							alertify.success('<?php echo $this->lang->line("your language file has been successfully deleted.")?>');
							$(that_parent).addClass('hidden');

						} else
						{
							alertify.error('<?php echo $this->lang->line("something went wrong, please try again.")?>');
							$(that_parent).removeClass('hidden');
						}
					}
				})
			},function(){});	
		});


		// if delete modal reload the location else no reload
		$(document.body).on('click','#modal_close', function(event) 
		{
			event.preventDefault();

			var download_modal = $("#addon_type").html();
			if(download_modal == "addons")
			{
				//no reload
				var tab = $("#addonTab").attr("href");

			} else {
				// if delete modal then do reload
				location.reload();
			}
		});

	});
</script>


<div class="modal fade" id="language_file_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="box-shadow: 0px 0px 16px -2px transparent !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id='modal_close' data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center" style="color: #000 !important;font-weight: 700 !important;"><i class="fa fa-language"></i> <?php echo $this->lang->line("select the language");?></h4>
            </div>
          	<div class="modal-body">
                <style>
                	.download_box{border:1px solid #ccc;margin: 0 auto;text-align: center;margin-top:3%;padding-bottom: 20px;background-color: #fffddd;color:#000;}
                </style>
				
				<div class="row">
					<div id="response_status"></div>
				</div>

	            <div class="row">
	                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
						<h3 class="hidden" id="addon_names"></h3>
						<h4 class="hidden" id="addon_type"></h4>
	                	<div id="languageDataBody">
							
	                	</div>
	                </div>
	            </div>
           
            </div>
        </div>
    </div>
</div>







