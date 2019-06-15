<style type="text/css">
	
		.fc-button-group button:focus{
			outline: none;
			
		}

</style>


<!-- fullCalendar -->
<link rel="stylesheet" href="<?php echo base_url();?>/plugins/fullcalendar/fullcalendar.min.css">
<!-- full calender -->
<div class="well well_border_left">
	<h4 class="text-center"><i class='fa fa-calendar'></i> <?php echo $this->lang->line("activity calendar"); ?></h4>
</div>


<div class="container-fluid">
	<div class="box box-info">
	 <div id="su"></div>
	</div>
</div>



<script src="<?php echo base_url();?>plugins/fullcalendar/fullcalendar.min.js"></script>
 <script>
   
   $(function () {



     /* initialize the calendar
      -----------------------------------------------------------------*/
     //Date for the calendar events (dummy data)
     
     var events = <?php echo json_encode($data) ?>;

     var date = new Date()
     var d    = date.getDate(),
         m    = date.getMonth(),
         y    = date.getFullYear()


     $('#su').fullCalendar({
       header    : {
         left  : 'prev,next today',
         center: 'title',
         right : 'month,agendaWeek,agendaDay'
       },
       buttonText: {
         today: 'today',
         month: 'month',
         week : 'week',
         day  : 'day'
       },
       eventRender: function(eventObj, $el) {
         $el.popover({
           title: eventObj.title,
           content: eventObj.description,
           trigger: 'hover',
           placement: 'top',
           container: 'body',
           html:true
         });
       },
       //Random default events
       events    : events,
       editable  : true,
       droppable : true, // this allows things to be dropped onto the calendar !!!

     })


   })
 </script>