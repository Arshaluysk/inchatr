

  <!--   <link href="<?php echo base_url();?>plugins/assets/css/lib/bootstrap.min.css" rel="stylesheet"> -->
    <link href="<?php echo base_url();?>plugins/assets/css/custom.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
   

    <div class="container">
      <div class="row clearfix">
	  
	  
	          <!-- Components -->
        <div class="col-md-6">
          <h2>Drag & Drop components</h2>
          <hr>
          <div class="tabbable">
            <ul class="nav nav-tabs" id="formtabs">
              <!-- Tab nav -->
            </ul>
            <form class="form-horizontal" id="components" role="form">
              <fieldset>
                <div class="tab-content">
                  <!-- Tabs of snippets go here -->
                </div>
              </fieldset>
            </form>
          </div>
        </div>
        <!-- / Components -->
	  
	  
        <!-- Building Form. -->
        <div class="col-md-6">
          <div class="clearfix">
            <h2>Your Form</h2>
            <hr>
            <div id="build">
              <form id="target" class="form-horizontal">
              </form>
            </div>
          </div>
        </div>
        <!-- / Building Form. -->

      </div>

    </div> <!-- /container -->


   <!-- <script src="<?php echo base_url();?>plugins/assets/js/main.js"></script>
   <script src="<?php echo base_url();?>plugins/assets/js/main-built.js"></script> -->
   <script data-main="<?php echo base_url();?>plugins/assets/js/main-built.js" src="<?php echo base_url();?>plugins/assets/js/lib/require.js"></script>
