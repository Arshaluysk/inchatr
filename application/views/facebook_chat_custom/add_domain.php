<?php 
	if($this->session->userdata('trans_error') == 1) {
		echo "<div class='alert alert-danger text-center'><h4 style='margin:0;'><i class='fa fa-remove'></i> {$this->lang->line("your data has been failed to store into the database. please try again !")} </h4></div>";
		$this->session->unset_userdata('trans_error');
	}
?>
<style>
	#copy_button {
		background: white;
        color: black;
        padding-left: 5px;
        padding-right: 5px;
        margin-top: -15px;
        margin-right: -15px;
	}

	#copy_button:hover {
		cursor: pointer;
		background: orange;
		color: blue;
	}
</style>

<section class="content-header">
	<section class="content">
		<div class="box box-info custom_box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("create FB chat embed code"); ?></h3>
			</div><!-- /.box-header -->
			<!-- form start -->
			<form class="form-horizontal" action="<?php echo site_url().'fb_chat_plugin_custom/add_domain_action';?>" method="POST">
				<div class="box-body">

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line("domain name") ?> 
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input required name="domain_name" id="domain_name" value="<?php echo set_value('domain_name');?>"  class="form-control" type="text" required />		          
							<span class="red"><?php echo form_error('domain_name'); ?></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line("message header") ?> 
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input name="message_header" id="message_header" value="<?php echo set_value('message_header');?>"  class="form-control" type="text" />		          
							<span class="red"><?php echo form_error('message_header'); ?></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line("FB page url") ?>
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input required name="fb_page_url" id="fb_page_url" value="<?php echo set_value('fb_page_url');?>"  class="form-control" type="text" required placeholder="https://facebook.com/xeroneit" />		          
							<span class="red"><?php echo form_error('fb_page_url'); ?></span>
						</div>
					</div>
					

				</div> <!-- /.box-body --> 
				<div class="box-footer">
					<div class="form-group">
						<div class="col-sm-12 text-center">
							<input name="submit" id="submit" type="submit" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line("save");?>"/>  
							<input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line("cancel");?>" onclick='goBack("fb_chat_plugin_custom/fb_chat_domain_list")'/>  
						</div>
					</div>
				</div><!-- /.box-footer -->         
			</div><!-- /.box-info -->       
		</form>     
	</div>
</section>
</section>


<!-- Start modal for js code. -->
<div id="modal_add_domain" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 id="" class="modal-title"><i class="fa fa-copy"></i> <?php echo $this->lang->line("please copy this code");?></h4>
			</div>

			<div class="modal-body">
				<h3 class="text-center"><?php echo $this->lang->line("copy the code below and paste it to your web page (inside body tag)");?></h3>
				<div class="alert alert-success text-center clearfix">
					<div id="copy_button" class="pull-right" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line("click to copy");?>"><?php echo $this->lang->line("click to copy");?></div>
					<p id="add_domain_body"></p>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close");?></button>
			</div>
		</div>
	</div>
</div>
<!-- End modal for js code. -->


<div id="error_modal" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<!-- <div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
			</div> -->

			<div class="modal-body">
				<div class="alert alert-danger text-center">
					<p id="error_modal_body"></p>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close");?></button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#submit').click(function(e){
		e.preventDefault();
		var domain_name = $('#domain_name').val().trim();
		var fb_page_url = $('#fb_page_url').val().trim();
		var message_header = $('#message_header').val().trim();
		if(domain_name == '' || fb_page_url == '') {
			alert("<?php echo $this->lang->line('you have not enter any domain name or FB page url');?>");
		} else {
			var base_url="<?php echo site_url(); ?>";
			$.ajax({
				type:'POST' ,
				url: "<?php echo site_url(); ?>fb_chat_plugin_custom/add_domain_action",
				data:{domain_name:domain_name,fb_page_url:fb_page_url,message_header:message_header},
				success:function(response){
					$('#modal_add_domain').modal();
					$('#add_domain_body').text(response);
				}
			}); 
		}
	});

	$('#modal_add_domain').on('hidden.bs.modal', function () { 
		var link="<?php echo site_url('fb_chat_plugin_custom/fb_chat_domain_list'); ?>"; 
		window.location.assign(link); 
	})

	document.getElementById("copy_button").addEventListener("click", function() {
        copyToClipboard(document.getElementById("add_domain_body"));
    });

    function copyToClipboard(elem) {
          // create hidden text element, if it doesn't already exist
        var targetId = "_hiddenCopyText_";
        var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
        var origSelectionStart, origSelectionEnd;
        if (isInput) {
            // can just use the original source element for the selection and copy
            target = elem;
            origSelectionStart = elem.selectionStart;
            origSelectionEnd = elem.selectionEnd;
        } else {
            // must use a temporary form element for the selection and copy
            target = document.getElementById(targetId);
            if (!target) {
                var target = document.createElement("textarea");
                target.style.position = "absolute";
                target.style.left = "-9999px";
                target.style.top = "0";
                target.id = targetId;
                document.body.appendChild(target);
            }
            target.textContent = elem.textContent;
        }
        // select the content
        var currentFocus = document.activeElement;
        target.focus();
        target.setSelectionRange(0, target.value.length);
        
        // copy the selection
        var succeed;
        try {
              succeed = document.execCommand("copy");
        } catch(e) {
            succeed = false;
        }
        // restore original focus
        if (currentFocus && typeof currentFocus.focus === "function") {
            currentFocus.focus();
        }
        
        if (isInput) {
            // restore prior selection
            elem.setSelectionRange(origSelectionStart, origSelectionEnd);
        } else {
            // clear temporary content
            target.textContent = "";
        }
        return succeed;
    }

</script>



