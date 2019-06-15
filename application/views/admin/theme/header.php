<?php
// $colorpos=strpos($loadthemebody,'light');
// if($colorpos!==FALSE) $colorclass='orange';
// else $colorclass='';

// $colorpos2=strpos($loadthemebody,'yellow-light');
// if($colorpos2!==FALSE) $colorclass='';
$colorclass='';
?>
<header class="main-header">
  <!-- Logo -->
  <a href="<?php echo base_url(); ?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b><i class="fa fa-envelope <?php echo $colorclass;?> fa-2x"></i></b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b><img  style="height:45px !important" src="<?php echo base_url();?>assets/images/logo.png" alt="<?php echo $this->config->item('product_name');?>" class="img-responsive">
    </b></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>
    <?php $this->load->view("admin/theme/notification"); ?>
  </nav>
</header>