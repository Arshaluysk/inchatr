<style>

   .chat-box{

   position: fixed;

   bottom: 0px;

   right: 2px;

   /*width: 340px;*/

   z-index: 20000;

   overflow: auto;

   zoom: 1;



    }



    .header-area{

      background: #405D9B;

      min-height: 30px;

      border-top-left-radius: 7px;

      border-top-right-radius: 7px;

      -webkit-box-shadow: 0px 2px 16px 0px rgba(50, 50, 50, 0.75);

      -moz-box-shadow:    0px 2px 16px 0px rgba(50, 50, 50, 0.75);

      box-shadow:         0px 2px 16px 0px rgba(50, 50, 50, 0.75);

      cursor: pointer;

    }

    .header-area h4{

      color: white;

      margin:0px;

      padding-top: 5px;

    }

    #fontawesome-icon-rakib{

      padding-top: 5px;

      font-size: 20px;

    }

    #fontawesome-icon-clo{



      font-size: 25px;

    }

    #live-chat{



      -webkit-box-shadow: 0px 2px 16px 0px rgba(50, 50, 50, 0.75);

      -moz-box-shadow:    0px 2px 16px 0px rgba(50, 50, 50, 0.75);

      box-shadow:         0px 2px 16px 0px rgba(50, 50, 50, 0.75);

    }

  </style>

  <div class="chat-box">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">



    <div class="header-area" id="fb_chat_button_id">

      <div class="col-xs-1 col-md-1" id="fontawesome-icon-rakib">

        <i class="fa fa-facebook" style="color:white"></i>

      </div>

      <div class="col-xs-8 col-md-8">

        <h4><?php echo $header; ?></h4>

      </div>

      <div class="col-xs-2 col-md-2">



      </div>

      <div class="col-md-1" id ="fontawesome-icon-clo">

        <i id="up_down_icon" class="fa fa-angle-up" style="color:white;"></i>

      </div>

    </div>

    <div id="live-chat" style="display:none;" current-class="imhide" class="fb-page" data-href="<?php echo $page;?>" data-height="380" data-width="250" data-tabs="messages" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false" data-show-posts="false">



      <blockquote cite="<?php echo $page;?>" class="fb-xfbml-parse-ignore">

        <a href="https://www.facebook.com/facebook">Facebook</a>

      </blockquote>

    </div>

</div>