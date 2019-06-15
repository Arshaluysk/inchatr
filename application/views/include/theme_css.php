<style type="text/css">

.content-wrapper{background: #fff;<?php echo $BOXSHADOW; ?> border-left:0 !important;}

<?php if($loadthemebody!="skin-black-light") { ?>
.navbar-static-top{<?php echo $BOXSHADOW; ?>}
<?php } ?>


.info-box-number,label:not(.css-label):not(.label-success):not(.label-info):not(.label-primary):not(.label-warning):not(.label-danger)
{
   color: <?php echo $THEMECOLORCODE; ?> !important; 
   font-weight: 400 !important;
}

.modal 
{
  background: linear-gradient(135deg,<?php echo $COLOR2; ?>,<?php echo hex2rgba($COLOR4,.4); ?>) !important;
}

.modal-title,.box-title,.box-title a,.box-header,.block_title,.well h4,.content-header h1,.ftitle{color: <?php echo $THEMECOLORCODE; ?> !important; font-weight: 300 !important;}
.box-title a:hover{background: none !important;}
h1,h2,h3,h4,.alert h4{font-weight: 300 !important;}
h5,h6,.datagrid-header .datagrid-cell span,th{font-weight: 400 !important;}
.info-box-text,.top-title{color: <?php echo $THEMECOLORCODE; ?> !important; font-weight: 300 !important;}

.block_title{font-size:16px;}

.well
{
	background: #f8f8f8;
	border: none;
	border-radius: 0;
}

.ajax-upload-dragdrop{border:.7px dashed <?php echo $THEMECOLORCODE; ?>;}

.ajax-file-upload-statusbar{border: .5px solid <?php echo $THEMECOLORCODE;?>;}
.morris-hover.morris-default-style{border: 1px solid <?php echo $THEMECOLORCODE;?>;}

.well_border_left{padding-top: 10px !important;padding-bottom: 10px !important;}

.dynamic_color,.select2-highlighted,.chzn-container .chzn-results .highlighted,.ajax-file-upload{background: <?php echo $THEMECOLORCODE;?> !important;}

.dynamic_font_color,.widget-user .widget-user-username a,.widget-user .widget-user-desc{color: <?php echo $THEMECOLORCODE;?> !important;}

.flexigrid.crud-form .mDiv{border-top: 3px solid <?php echo $THEMECOLORCODE;?>;}

/* .custom_box,.box{border-color: <?php echo $THEMECOLORCODE;?> !important;} */

.box-shadow{<?php echo $BOXSHADOW; ?>}

#dashboard-top .cmn,#dashboard-box .cmn,.cmn .icon{border-radius: 10px !important;}

.info-box,.modal-dialog{<?php echo $BOXSHADOW; ?>}
.datagrid-wrap .label,.flexigrid .label{line-height: 24px !important;}


#msg_dashboard .bg-a {background: <?php echo $COLOR1;?>;}   
#msg_dashboard .bg-b {background: <?php echo $COLOR2;?>;} 
#msg_dashboard .bg-c {background: <?php echo $COLOR3;?>;}
#msg_dashboard .bg-d {background: <?php echo $COLOR4;?>;}

#msg_dashboard  .third-circle      {background: <?php echo $COLOR1;?>;}   
#msg_dashboard  .third-circle.bg-b {background: <?php echo $COLOR2;?>;}   
#msg_dashboard  .third-circle.bg-c {background: <?php echo $COLOR3;?>;}   
#msg_dashboard  .third-circle.bg-d {background: <?php echo $COLOR4;?>;}

#msg_dashboard  .bg-a .more-info a {border: 1px solid <?php echo $COLOR1;?>;}
#msg_dashboard  .bg-b .more-info a {border: 1px solid <?php echo $COLOR2;?>;}
#msg_dashboard  .bg-c .more-info a {border: 1px solid <?php echo $COLOR3;?>;}
#msg_dashboard  .bg-d .more-info a {border: 1px solid <?php echo $COLOR4;?>;}

#msg_dashboard #dashboard-top .cmn.bg-a .short-info {color: <?php echo $COLOR1;?>;}
#msg_dashboard #dashboard-top .cmn.bg-b .short-info {color: <?php echo $COLOR2;?>;}   
#msg_dashboard #dashboard-top .cmn.bg-c .short-info {color: <?php echo $COLOR3;?>;}   
#msg_dashboard #dashboard-top .cmn.bg-d .short-info {color: <?php echo $COLOR4;?>;}

/* *:not(.fa){font-family: 'Open Sans' !important;font-size: 98% !important;font-weight: normal;} */


/* custom */
.grid_container td,.grid_container th,.flexigrid td,.flexigrid th{vertical-align: middle !important;text-align: center !important;}
/* custom */

</style>