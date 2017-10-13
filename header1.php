
<!DOCTYPE html>
<html lang="en">
   <head>
      <base href="<?php echo WEBROOT_URL; ?>" target="_self">
      <title>Welcome to Hospital Survey</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="assets/css/bootstrap.min.css">
      <link rel="stylesheet" href="assets/css/owl.carousel.css">
      <link rel="stylesheet" href="assets/css/owl.theme.css">
      <link href="assets/css/font-awesome.min.css" rel="stylesheet">
      <!-- bootstrap-daterangepicker -->
      <link href="assets/js/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
      <!-- bootstrap-datetimepicker -->
      <link href="assets/js/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">

      <link href='assets/css/css.css' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="assets/css/dataTables.bootstrap.min.css">
       <link rel="stylesheet" href="assets/css/responsive.bootstrap.min.css">
      <!-- toaster -->
      <link href="<?php echo WEBROOT_URL; ?>assets/js/toaster/jquery.toast.css" rel="stylesheet">
      <link rel="stylesheet" href="css/body.css">



      <script src="<?php echo WEBROOT_URL; ?>assets/js/jquery.min.js"></script>
      <script src="<?php echo WEBROOT_URL; ?>assets/js/bootstrap.min.js"></script>
      <script src="<?php echo WEBROOT_URL; ?>assets/js/owl.carousel.js"></script>
      <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo HOS_GOOGLE_MAP_APIS_KEY; ?>&callback=myMap"></script>
      <script src="<?php echo WEBROOT_URL; ?>js/jquery.bootstrap.newsbox.min.js" type="text/javascript"></script>
      <script src="<?php echo WEBROOT_URL; ?>js/news-ticker.js" type="text/javascript"></script>
      <script src="<?php echo WEBROOT_URL; ?>js/jssor.slider-25.2.0.min.js" type="text/javascript"></script>
      <script src="<?php echo WEBROOT_URL; ?>js/tab-slider.js" type="text/javascript"></script>
      <script src="<?php echo WEBROOT_URL; ?>js/my-owl-script.js" type="text/javascript"></script>
      <script src="<?php echo WEBROOT_URL; ?>js/pagination.js" type="text/javascript"></script>
      <script src="<?php echo WEBROOT_URL; ?>js/rating.js" type="text/javascript"></script>
      <script src="<?php echo WEBROOT_URL; ?>assets/js/parsleyjs/dist/parsley.js"></script>
      <!-- bootstrap-daterangepicker -->
       <script src="<?php echo WEBROOT_URL; ?>assets/js/moment/min/moment.min.js"></script>
       <script src="<?php echo WEBROOT_URL; ?>assets/js/bootstrap-daterangepicker/daterangepicker.js"></script>
       <!-- bootstrap-datetimepicker -->
      <script src="<?php echo WEBROOT_URL; ?>assets/js/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
      <script type="text/javascript">jssor_1_slider_init();</script>
      <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
      <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
      <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
      <script src="<?php echo WEBROOT_URL; ?>assets/js/jquery.form.js"></script>
      <!-- Toaster -->
      <script src="<?php echo WEBROOT_URL; ?>assets/js/toaster/jquery.toast.js"></script>
      <script src="js/initcomponents.js"></script>
      <script src="js/datatable_search_results.js"></script>
      <script src="js/html5shiv.js"></script>


      <?php
         if(isset($pagedef['additionaljs'])){
            foreach($pagedef['additionaljs'] as $js){
             echo "<script type='text/javascript' src='js/".$js."'></script>";
            }
         }
      ?>
      <script type="text/javascript" src="includes/validation.js"></script>

   </head>

   <body>
      <div class="top-gradient"></div>
      <section style="border-bottom:1px solid #000;">
         <div class="container">
            
         </div>
      </section>
