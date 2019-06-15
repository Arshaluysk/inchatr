<style>

/*==================================== Language_list View =====================================*/

	.add { background: <?php echo $THEMECOLORCODE; ?>; color: #edeaea; font-size: 15px; width: 69%;padding: 10px;font-size: 18px;margin: 10px -3px;border: 1px solid #607d8b; }
	.add:hover { background: #ecf0f5; color: #000; font-size: 15px; width: 69%;padding: 10px;font-size: 18px;margin: 10px -3px;border: 1px solid #607d8b; }
	.delete {float: right; background: #8e2f2f; color: #edeaea; font-size: 15px; width: 69%;padding: 10px;font-size: 18px;margin: 10px 0;border: 1px solid #8e2f2f; }
	.delete:hover {float: right; background: none; color:#272424; font-size: 15px; width: 69%;padding: 10px;font-size: 18px;margin: 10px 0;}
	.folder {background: #FFF; cursor: pointer;box-shadow: 3px 3px 3px #cccccc, -2px 0px 3px #cccccc;padding: 5px; }
	.folder:hover {background: <?php echo $THEMECOLORCODE; ?>!important;color: #FFF;}

	.folder:hover a {color: #FFF;}
	.language{background:#fff;padding:10px;border-radius:0;box-shadow:0 0 16px -2px rgba(143,141,143,.61)}
	.language-name{color:#807e7e;padding-bottom:10px;font-weight:400!important;text-transform:uppercase;font-size:14px}
	.prog{height:2px;width:109%;margin:0 10px 0 -10px;overflow:hidden;background-color:#fbfbfb;border-radius:4px;-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);box-shadow:inset 0 1px 2px rgba(0,0,0,.1)}

	.delete_btn {padding: 4px 10px;}
	.action_btn {padding: 18px 0 38px 0px;}
	.folder-name{background:#868686;color:#fff;padding:5px;border-radius:40px;box-shadow:2px 3px 3px #98979744,-1px -1px 3px #98979744;}
	.folder-name h4 { font-weight: bold !important; }
	.nav-tabs>li{float:left;width:33.3%;margin-bottom:-1px;text-align:center}
	.nav-tabs { border-bottom: none !important; }
	.nav-tabs>li.active>a:before {font-family:"Font Awesome 5 Free";font-weight:900;content:"\f00c";}
	.nav-tabs>li.active>a,.nav-tabs>li.active>a:focus,.nav-tabs>li.active>a:hover{color:#fff;cursor:default;background-color:#3b4973;border:1px solid transparent;border-bottom-color:transparent;font-weight:700;font-size:16px;margin:0 -1px 0 -2px}
	.nav-tabs>li>a{padding:16px 0!important;margin-right:0;line-height:1.42857143;border:1px dashed;border-radius:0;background:#aaa;font-size:16px;color:#000}
	.language_file {background: #FFF;cursor: pointer; margin: 6px;box-shadow: 3px 3px 3px #cccccc,-2px 0px 3px #cccccc;width: 23.64%;padding: 24px; color: #444444; }
	.plugin_language_file { background: #FFF; cursor: pointer; margin: 10px 15px;box-shadow: 3px 3px 3px #cccccc, -2px 0px 3px #cccccc;width: 89%;padding: 10px; color: #444444; }
	.langFile h4,.plugin_language_file h4 { font-size: 12px; font-weight: 400 !important; }
	.modification span { font-size:10px; }
	.language_file:hover, .plugin_language_file:hover { background: <?php echo $THEMECOLORCODE; ?> !important; }
	.language_file:hover h4, .language_file:hover span, .plugin_language_file:hover h4, .plugin_language_file:hover span { color: #fff; }
	#plugins { position: relative;left: 365px; }
	.folder-name {background: #868686;color: #fff;padding: 5px;border-radius: 40px;box-shadow: 2px 3px 3px #98979744, -1px -1px 3px #98979744;}
	.folder-name h4 { font-weight: bold !important; }
	input:focus{border-width: 0px !important;outline:0 !important;-webkit-appearance:none !important;box-shadow: none !important;-moz-box-shadow: none !important;-webkit-box-shadow: none !important;}

/*============================ Update Language Views Section =======================================*/

	.allFiles {background: #FFF;cursor: pointer; margin: 6px;box-shadow: 3px 3px 3px #cccccc,-2px 0px 3px #cccccc;width: 23.34%;padding: 24px; color: #444444;box-shadow: 3px 3px 3px #cccccc, -2px 0px 3px #cccccc; }
	.allFiles:hover { background: <?php echo $THEMECOLORCODE; ?> !important; }
	.allFiles:hover h4 { color: #fff; }
	.allFiles h4 { font-weight: 400 !important;font-size: 12px; }
	#totalFiles span{border-top-right-radius:20px;border-bottom-right-radius:20px;background:#3a3a3c;padding:7px 33px;margin:19px;color:#fff;font-size:12px}
	.headerlist li { list-style: none; text-align: center; }
	.main_header { background: #3b4973;color: #fff;font-size:16px; padding:12px 0 4px 0;margin:0px -2px 24px 0px; }
	.language_name_field { padding: 0 30px; }

	.folder_head{background: #fff;padding: 10px 0;margin-bottom: -7px;box-shadow: 5px 3px 3px #CECECE,-5px -2px 3px #cecece}
	.folder_head:hover{background: <?php echo $THEMECOLORCODE; ?>;color:#fff;cursor:pointer;padding: 10px 0;margin-bottom: -7px;box-shadow: 5px 3px 3px #CECECE,-5px -2px 3px #cecece}
	.download_folder{background:#fff;padding:20px;box-shadow:5px 3px 3px #CECECE,-5px -2px 3px #cecece;margin:6px -10px;color:#717171}
	.download_folder:hover{background: <?php echo $THEMECOLORCODE; ?>; padding: 20px; box-shadow: 5px 3px 3px #CECECE, -5px -2px 3px #cecece;margin: 6px -10px; color: #fff;}	

	.delete_language{background: #fff;padding: 20px;box-shadow: 5px 3px 3px #CECECE, -5px -2px 3px #cecece;margin:6px -10px;color: #717171; }
	.delete_language:hover{background: <?php echo $THEMECOLORCODE; ?>;color:#fff;padding: 20px;box-shadow: 5px 3px 3px #CECECE, -5px -2px 3px #cecece;margin:6px -10px;}

	.delete_language:before{font-family:"Font Awesome 5 Free";font-weight:900;content:"\f2ed";margin-right: 5px; color: #821010;font-size:12px;}

	.add_navbar li { display: inline;list-style: none;margin:0px 0px 10px 8px; }
	.add_navbar > li:last-child a { color: #000; }
	.add_navbar > li:last-child {cursor: not-allowed; }
	.add_navbar li a:after { font-family:"Font Awesome 5 Free";font-weight:900;content:" / "; margin-left: 5px; }
	.add_navbar > li:last-child a:after { font-family:"Font Awesome 5 Free";font-weight:900;content:""; margin-left: 5px; }
	.add_navbar { padding: 10px 0px;margin-bottom: 20px;list-style: none;background-color: #cecece;}

	#no_files { font-weight: bold;background: #FFF;cursor: pointer; margin: 80px 278px;box-shadow: 3px 3px 3px #cccccc,-2px 0px 3px #cccccc;padding: 16px; color: #ff3d3d;box-shadow: 3px 3px 3px #cccccc, -2px 0px 3px #cccccc; }

	.not_english { background: #FFF;cursor: pointer;box-shadow: 3px 3px 3px #cccccc,-2px 0px 3px #cccccc;padding: 16px; color:#e44848;box-shadow: 3px 3px 3px #cccccc, -2px 0px 3px #cccccc;margin-bottom: 15px;margin-top:6px;font-weight: bold;font-size: 12px;  }



	@media screen and (max-width: 600px) 
	{
		.add{background:#607D8B;color:#edeaea;width:100%;padding:15px;font-size:13px;margin:10px -3px;border:1px solid #607d8b}
		.add:hover { background: #ecf0f5; color: #000; font-size: 13px; width: 100%;padding: 15px;margin: 10px -3px;border: 1px solid #607d8b; }
		.prog {width: 106%;}
		.delete{float:right;background:#8e2f2f;color:#edeaea;width:100%;padding:15px;font-size:13px;margin:10px 0;border:1px solid #8e2f2f}
		.delete:hover{float:right;background:0 0;color:#000;width:100%;padding:15px;font-size:13px;margin:10px 0;border:1px solid #8e2f2f}
		.language_file {background: #FFF;cursor: pointer; margin: 6px 6px;box-shadow: 3px 3px 3px #cccccc,-2px 0px 3px #cccccc;width: 94.8%;padding: 14px; color: #444444;}

		.nav-tabs>li>a{margin-right:0;line-height:1.42857143;border:1px dashed;border-radius:0;background:#aaa;color:#000;font-size: 14px;}

		.nav-tabs>li.active>a,.nav-tabs>li.active>a:focus,.nav-tabs>li.active>a:hover{color:#fff;cursor:default;background-color:#3b4973;border:1px solid transparent;border-bottom-color:transparent;font-weight:700;margin:0 -1px;font-size: 14px;}
			#plugins { position: relative;left: 0px; }

		.allFiles {background: #FFF;cursor: pointer; margin: 10px 3px;box-shadow: 3px 3px 3px #cccccc,-2px 0px 3px #cccccc;width:92.5%;padding: 16px; color: #444444;box-shadow: 3px 3px 3px #cccccc, -2px 0px 3px #cccccc; }
		#plugs { background: #3b4973; !important;color: #fff;font-size:18px; padding: 24px 0 10px 0;margin: 0 272px 25px 263px; }
		#plug { position: relative; left: 0 !important;width: 94% !important; }
		.language_name_field { padding: 0px 28px; }
		#language_name:focus { outline: none!important;border:none; }
		#language_name:active { outline: none!important; }
		#totalFiles span {border-top-right-radius: 20px;border-bottom-right-radius: 20px;background: #3a3a3c;padding: 7px 33px;margin: 28px;color: #fff;font-size: 12px;}
		.main_header { background: #3b4973;color: #fff;font-size:16px; padding:12px 23px 4px 0;margin:0px -2px 28px -3px;border-radius: 2px; }
		.add_navbar { text-align:center;margin: 0 -3px 20px -5px;list-style: none;background-color: #cecece;}

		.not_english { background: #FFF;cursor: pointer;box-shadow: 3px 3px 3px #cccccc,-2px 0px 3px #cccccc;padding: 16px; color:#e44848;box-shadow: 3px 3px 3px #cccccc, -2px 0px 3px #cccccc;margin-bottom: 15px;margin-top:6px;font-weight: bold;font-size: 12px;margin-left: 29px;width:88%;  }
	}


</style>