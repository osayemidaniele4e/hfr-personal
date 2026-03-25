<!doctype html>
<html class="no-js" lang="en">
    
<head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        {{-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-130161904-1"></script>
       
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-130161904-1');
        </script> --}}

        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Nigeria Health Facility Registry</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
        
		<!-- favicon
		============================================ -->		
        <link rel="shortcut icon" type="image/x-icon" href="{{asset("favicon.ico")}}">
		
		<!-- Google Fonts
		============================================ -->		
        <link href='https://fonts.googleapis.com/css?family=Raleway:400,300,500,600,700,800' rel='stylesheet' type='text/css'>
	   
			
        <link rel="stylesheet" href="{{asset("design/css/bootstrap.min.css")}}">

        <link rel="stylesheet" href="{{asset("design/css/font-awesome.min.css")}}">
        <link rel="stylesheet" href="{{asset("design/css/meanmenu.min.css")}}">
        <link rel="stylesheet" href="{{asset("design/css/animate.css")}}">

        <link rel="stylesheet" href="{{asset("design/css/jquery-ui.css")}}">

        <link rel="stylesheet" href="{{asset("design/css/material-design-iconic-font.css")}}">
       
        <link rel="stylesheet" href="{{asset("design/style.css")}}">
        <link rel="stylesheet" href="{{asset("design/css/responsive.css")}}">
        <link rel="stylesheet" href="{{asset("design/css/color.css")}}">
    	
        <link rel="stylesheet" href="{{asset("/dist/css/select2.min.css")}}">
	
        @yield("custom_css")
     
    </head>
    <body>
     
        <!--Main Wrapper Start-->
        <div class="as-mainwrapper">
            <!--Bg White Start-->
            <div class="bg-white">
                <!--Header Area Start-->
                <header>
                    <div class="header-top bg-green effect-blue">
                        <div class="container ">
                                <div class="row">
                                        <div class="col-lg-7 col-md-6 col-sm-5 hidden-xs">
                                            {{-- <a href=""><img src="/img/logo.png"  width="50" height="50" alt="FMOH"></a> --}}
                                            <span><h4> NIGERIA Health Facility Registry (HFR)</h4></span>
                                        </div>
                                        <div class="col-lg-5 col-md-6 col-sm-7 col-xs-12">
                                            <div class="header-top-right">
                                                <div class="content"><a href="{{route('about')}}"> About</a></div>
                                                <div class="content"><a href="{{route('open_contact_form')}}"> Contact Us</a></div>
                                                <div class="content"><a href="/login"><i class="zmdi zmdi-account"></i> My Account</a></div>                                                                            
                                            </div>
                                        </div>
                                </div>
                        </div>
                    </div>
                    <div class="header-logo-menu ">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-3 col-sm-12">
                                        <div class="logo">
                                                <a href="{{route('home')}}"> 
                                                    <img src="{{asset('img/new_logo.png')}}"  alt="FMOH">
                                                </a>               
                                        </div>
                                        
                                </div>
                                <div class="col-md-9">
                                    <div class="mainmenu-area pull-right">
                                        <div class="mainmenu hidden-sm hidden-xs">
                                            <nav>
                                                <ul id="nav">
                                               
                                                     @include('layouts.pub.menu')
                                      
                                                </ul>
                                            </nav>
                                        </div>
                                
                                    </div> 
                                </div>

                            </div>
                        </div>
                    </div>  
                          <!-- Mobile Menu Area start -->
                          <div class="mobile-menu-area">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="mobile-menu">
                                            <nav id="dropdown">
                                                <ul>
                                                    @include('layouts.pub.menu')
                                                </ul>
                                            </nav>
                                        </div>					
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Mobile Menu Area end -->
                      
                </header>
                <!--End of Header Area-->
             <!--Breadcrumb Banner Area Start-->
                <div class="breadcrumb-banner-area">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                          
                            </div>
                        </div>
                    </div>
                </div>
                <!--End of Breadcrumb Banner Area-->
               
                   
                        @yield("content")
                    
                
                <!--End of Text Area-->
                           <!--Footer Widget Area Start-->
                           <div class="footer-widget-area">
                                <div class="container">
                                    <div class="row">
                                  
                                        <div class="col-md-4 col-sm-4">
                                            <div class="single-footer-widget">
                                                <h3>Nigeria HFR PARTNERS</h3>
                                                <div class="instagram-image">
                                                       
                                                        <div class="footer-img">
                                                            <img src="{{asset('img/nigeria_logo.jpg')}}" height="70" width="70" alt="FMOH">
                                                        </div>
                                                        <div class="footer-img">
                                                                <img src="{{asset('img/usaid.png')}}" height="70" width="70" alt="USAID">
                                                        </div>
                                                        <div class="footer-img">
                                                                <img src="{{asset('img/measure.jpg')}}" height="70" width="70" alt="MEASURE Evaluation">
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                                <div class="single-footer-widget">
                                                        <h3>Feedback</h3>
                                                        <ul class="footer-list">
                                                            <li><a rel="noopener noreferrer"href="{{route('open_contact_form')}}">Send us feedback</a></li>
                                                        </ul>
                                                    </div>
                                        </div>

                                        <div class="col-md-4 hidden-sm">
                                                <div class="single-footer-widget">
                                                    <h3>Useful Links</h3>
                                                    <ul class="footer-list">
                                                        <li><a target="_blank" rel="noopener noreferrer" href="http://health.gov.ng/">Federal Ministry of Health</a></li>
                                                        <li><a target="_blank" rel="noopener noreferrer" href="http://nphcda.gov.ng"> NPHCDA</a></li>
                                                        <li><a target="_blank" rel="noopener noreferrer" href="https://dhis2nigeria.org.ng">Nigeria DHIS2</a></li>
                                                        
                                                    </ul>
                                                </div>
                                        </div>

                                    </div>
                                </div>
                        </div>
              
                <!--Footer Area Start-->
                <footer class="footer-area">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 col-sm-7">
                                <span>Copyright &copy; 2018 <a href="http://health.gov.ng/">Federal Ministry of Health</a>. All Right Reserved </span>
                            </div>
                            <div class="col-md-4">
                                
                            </div>
                            <div class="col-md-2 col-sm-2">
                                    <span>Version 2.0 </span>
                            </div>
                            
                        </div>
                    </div>
                </footer>
                <!--End of Footer Area-->
            </div>   
            <!--End of Bg White--> 
        </div>    
        <!--End of Main Wrapper Area--> 


 <!-- Goolge Map Modal -->
        <div class="modal fade" id="googleMapModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  >
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Facilities in LGA</h4>
                        </div>
                        <div class="modal-body">
                            <div id="googleMap" style="height: 500px; min-width: 500px; max-width: 800px; margin: 0 auto"></div>
                            
                        </div>
                        <div class="modal-footer">
                          
                            <div class="row">
                                <div class="col-md-2">
                                    <img src="http://maps.google.com/mapfiles/ms/micons/green.png" alt="Public">Public
                                </div>
                                <div class="col-md-2">
                                        <img src="http://maps.google.com/mapfiles/ms/micons/blue.png" alt="Private">Private
                                </div>
                                <label class="col-md-2">P: Primary</label>
                                <label class="col-md-2">S: Secondary</label>
                                <label class="col-md-2">T: Tertiary</label>

                            </div>
                           
                            
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
        </div>

<!-- Modal Facility Details on google map-->
<div class="modal fade" id="details_onGmap" tabindex="-1" role="dialog" aria-labelledby="details_Title">
        <div class="modal-dialog " role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="details_Title"></h4>
              <div class='notifications top-right'></div>
            </div>
            <div class="modal-body">
              
                <div class="panel-body">
                   
                        <div class="panel-group" id="accordion1">
                             {{-- panel one --}}
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#collapse1">Status</a>
                                  </h4>
                                </div>
                                <div id="collapse1" class="panel-collapse collapse in">
                                  <div class="panel-body">
                                      <div class="row">
                                          <label class="col-md-4">Operational Status:</label>
                                          <div class="col-md-8" id="operation_status1"></div>
                                      </div>
                                      <div class="row">
                                          <label class="col-md-4">Regulatory Status:</label>
                                          <div class="col-md-8" id="regulatory_status1"></div>
                                      </div>
                                      <div class="row">
                                          <label class="col-md-4">License Status:</label>
                                          <div class="col-md-8" id="license_status1"></div>
                                      </div>
                                  </div>
                                </div>
                            </div>
                                  {{-- pane two --}}
                                  <div class="panel panel-default">
                                        <div class="panel-heading">
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion1" href="#collapse2">Services</a>
                                          </h4>
                                        </div>
                                        <div id="collapse2" class="panel-collapse collapse">
                                          <div class="panel-body">
                                                <div class="row">
                                                        <label class="col-md-6">Medical Services:</label>
                                                        <div class="col-md-6" id="medical"></div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="col-md-6">Surgical Services:</label>
                                                        <div class="col-md-6" id="surgical"></div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="col-md-6">Obsterics Services:</label>
                                                        <div class="col-md-6" id="gyn"></div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="col-md-6">Pediatrics Services:</label>
                                                        <div class="col-md-6" id="pediatrics"></div>   
                                                    </div>
                                                    <div class="row">
                                                        <label class="col-md-6">Dental Services:</label>
                                                        <div class="col-md-6" id="dental"></div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="col-md-6">Specific Clinical Services:</label>
                                                        <div class="col-md-6" id="specialservice"></div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="col-md-6">Capacity for Accidents and Emergency Services (Beds):</label>
                                                        <div class="col-md-6" id="beds_accidents_emerg"></div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="col-md-6">Capacity for Admission Services (Beds):</label>
                                                        <div class="col-md-6" id="beds_adminission"></div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="col-md-6">Capacity for ICU Services (Beds):</label>
                                                        <div class="col-md-6" id="beds_icu"></div>
                                                    </div>
                                           
                                          </div>
                                        </div>
                                  </div>

                          {{-- panel three --}}
                          <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion1" href="#collapse3">Days of Operation</a>
                              </h4>
                            </div>
                            <div id="collapse3" class="panel-collapse collapse">
                              <div class="panel-body">
                                  <div class="row">
                                      <label class="col-md-4 text-md-right">Days of Operation:</label>
                                      <div class="col-md-8" id="operational_days1">   </div>
                                  </div>
                                  <div class="row">
                                      <label class="col-md-4 text-md-right">Hours of Operation:</label>
                                      <div class="col-md-8" id="operational_hours1">   </div>
                                  </div>
                            </div>
                            </div>
                          </div>
                        
                          {{-- panel four --}}
                          <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion1" href="#collapse4">Contacts</a>
                              </h4>
                            </div>
                            <div id="collapse4" class="panel-collapse collapse">
                              <div class="panel-body">
                                  <div class="row">
                                      <label class="col-md-4">Phone Number:</label>
                                      <div class="col-md-8" id="phone_number1"></div>
                                  </div>
                                  <div class="row">
                                      <label class="col-md-4">Email Address:</label>
                                      <div class="col-md-8" id="email_address1"></div>
                                  </div>
                                  <div class="row">
                                      <label class="col-md-4">Website:</label>
                                      <div class="col-md-8" id="website1"></div>
                                  </div>
                              </div>
                            </div>
                          </div>
                         
                            {{-- panel five --}}
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#collapse5">Personnel</a>
                                  </h4>
                                </div>
                                <div id="collapse5" class="panel-collapse collapse">
                                  <div class="panel-body">
                                      <div class="row">
                                          <label class="col-md-6">Number of Doctors:</label>
                                          <div class="col-md-6" id="doctors1"></div>
                                      </div>
                                    
                                      <div class="row">
                                          <label class="col-md-6">Number of Dentists:</label>
                                          <div class="col-md-6" id="dentist1"></div>
                                      </div>
                                    
                                      <div class="row">
                                          <label class="col-md-6">Number of Nurses:</label>
                                          <div class="col-md-6" id="nurses1"></div>
                                      </div>
                                      <div class="row">
                                          <label class="col-md-6">Number of Midwifes:</label>
                                          <div class="col-md-6" id="midwifes1"></div>
                                      </div>
                                      <div class="row">
                                          <label class="col-md-6">Number of Nurses/Midwifes:</label>
                                          <div class="col-md-6" id="nurse_midwife1"></div>
                                      </div>
                                    
                                  </div>
                                </div>
                              </div>
                        
                        </div> 
                     

                </div>
               
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           </div>
          </div><!-- /.modal-content -->
    </div><!--/.modal-dialog -->
</div><!-- end modal -->






        
	
        <script src="{{asset("design/js/vendor/jquery-1.12.4.min.js")}}"></script>
        <script src="{{asset("design/js/bootstrap.min.js")}}"></script>
        <script src="{{asset("design/js/jquery.meanmenu.js")}}"></script>        	
        <script src="{{asset("design/js/wow.min.js")}}"></script>
        {{-- <script src="{{asset("design/js/jquery.scrollUp.min.js")}}"></script>
        <script src="{{asset("design/js/waypoints.min.js")}}"></script>
        <script src="{{asset("design/js/jquery.counterup.min.js")}}"></script> --}}
        
        <script src="{{asset("dist/js/select2.full.min.js")}}"></script>
        <script src="{{asset("design/js/main.js")}}"></script>

       
        <script>
    
            $('.select2').select2() 

        </script>

        @stack("custom_scripts")
</body>

</html>