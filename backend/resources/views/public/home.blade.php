@extends("layouts.pub.master_home")

@section('custom_css')
<link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
@endsection



@section("content")
<div class="latest-area section-padding bg-white">
    <div class="container">
        <div class="row">
               
            <div class="col-sm-8">
              
                <div id="map1" style="height: 500px; min-width: 500px; max-width: 800px; margin: 0 auto" >
                    
                </div>
                
            </div> 
            <div class="col-sm-4">
                    <div class="single-latest-item">      
                            <div id="levels" style="min-width: 310px; height: 250px; margin: 0 auto"></div>

                    </div>
                    <div class="single-latest-item">      
                            <div id="ownership" style="min-width: 310px; height: 250px; margin: 0 auto"></div>
                    </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                    <div class="single-latest-item">      
                            <div id="geo"></div>
                    </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                    <div id="backDiv" style="height: 30px">
                            <button id="backbutton" style="display: none" class="btn btn-success pull-right" type="button">Return Back</button>
                    </div>
            </div>
        </div>
    </div>        
    
</div>

{{-- Modal to show notification on training server  --}}
<div class="modal fade" id='trainingServerNote' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                            
                            <h1 class="modal-title" id="myModalLabel">Note!</h1>
                        </div>
                    <div class="modal-body">
                            <h2><font color="red">
                                This is HFR training server. Data contained in this site is for training purpose only, 
                                and may not reflect the truth.</font></h2>
                    </div>
                    <div class="modal-footer">   
                        <button type="button" class="btn btn-default" data-dismiss="modal">I understand, Proceed!</button>
                    </div>
            </div>
        </div>
</div>
    
{{-- end --}}



@endsection 

@push('custom_scripts')
    <script src="{{ asset("hcharts/chart/highcharts.js")}}"></script>
    <script src="{{ asset("hcharts/map/exporting.js")}}"></script>
    <script src="{{ asset("hcharts/map/offline-exporting.js")}}"></script>

    <script src="{{ asset("hcharts/map/map.js")}}"></script>    
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC5oMDI7RfiReQVCk3brQPcWrJJR7UYgjw" async defer></script>
<script>
  
    window.onload = function() {
    
        if ("{{App::environment('Training')}}"){ //show a warning note on training server 
            $('#trainingServerNote').modal('show');
        }

        $('#backbutton').hide(0);
        
        showAllStatesMap();
        facilitybyLevel();
        facilitybyOwnership();
        facilitywithGeoCodes();
    };
    
    $("#backbutton").click(function(){
        $('#backbutton').hide(0);
        showAllStatesMap();
        facilitybyLevel();
        facilitybyOwnership();
        facilitywithGeoCodes();
    });
    
    
    function showAllStatesMap(){
        
        var data= @json($total_facilities_state);
        
        $.getJSON('geo/All_States.geojson', function (geojson) {
            
            // Initiate the chart
            Highcharts.mapChart('map1', {
                chart: {
                    map: geojson
                },
                
                title: {
                    text: 'Distribution of Hospitals and Clinics in Nigeria'
                },
                
                mapNavigation: {
                    enabled: true,
                    buttonOptions: {
                        verticalAlign: 'top'
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    verticalAlign: 'middle'
                },
                colorAxis: {
                    min: 0,
                    minColor: '#E6E7E8',
                    maxColor: '#005645'
                },
                credits: {
                    enabled: false
                },
                
                
                series: [{
                    data: data,
                    keys: ['statecode', 'value'],
                    joinBy: 'statecode',
                    name: 'Hospitals and Clinics',
                    states: {
                        hover: {
                            color: '#BADA56'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        format: '{point.properties.statename}'
                    },
                    
                    point:{
                        events:{
                            click: function(){
                                showSelectedState(this.statecode);
                            },
                   
                        }
                    }
                }]
                
            });//highmpa end
            
        });//all states map ends
        
    }
    
    //load state map
    function showSelectedState(statecode){
       
        var path = "geo/";
        file = statecode.concat(".geojson");
        full_path = path.concat(file);
        
        //get number of facilities of selected state by lga
        $.ajax({
            url:"{{route('getFacilitesByLGA')}}",
            method:"POST",
            data:{state_code:statecode, _token: "{{ csrf_token() }}"},
            success:function(result)
            {
                var statename= result.state;

                //if success get and display the state map
                $.getJSON(full_path, function (geojson) {
                    
                    // Initiate the chart
                    Highcharts.mapChart('map1', {
                        chart: {
                            map: geojson
                        },
                        
                        title: {
                            text: 'Distribution of Hospitals and Clinics'
                        },
                        subtitle: {
                            text: statename.concat(" State")
                        },
                        
                        legend: {
                            layout: 'vertical',
                            align: 'left',
                            verticalAlign: 'middle'
                        },
                        colorAxis: {
                            min: 1,
                            minColor: '#E6E7E8',
                            maxColor: '#005645'
                        },
                        
                        mapNavigation: {
                            enabled: true,
                            buttonOptions: {
                                verticalAlign: 'top',
                                align: 'left'
                            }
                        },
                        
                        credits: {
                            enabled: false
                        },
                        
                        series: [{
                            data: result.facilities,
                            keys: ['LGA_UID','value'],
                            joinBy: ['LGA_UID'],
                            name: 'Health Facilities',
                            states: {
                                hover: {
                                    color: '#BADA56'
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                format: '{point.properties.lganame}'
                            },
                            point:{
                                events:{
                                    click: function(){
                                        // Get the modal
                                        $('#googleMapModal').modal('show');
                                                                             
                                        showGoogleMap(statecode,this.LGA_UID)
                                    }
                                }
                            }
                        }]
                        
                    });//chart ends
                    
                });
                
                $('#backbutton').show();
               

                showFacilityByLevelSelectedState(result.by_level,statename);
                showFacilityByOwnershipSelectedState(result.by_ownership,statename);
                showFacilitywithGeoCodesState(result.geo_codes,statename);
            }//success ends         
        });//ajax ends
        
    }//end show state
    

    //on click of lga map, this function will load state map and get 
    //facilities with coordinates and display on google map
    function showGoogleMap(statecode,lgacode){
        //get path for geojson file for state
        var path = "geo/";
        file = statecode.concat(".geojson");
        full_path = path.concat(file);
        
        
        //get facilities with geo coorindates
        $.ajax({
            url:"{{route('getFacilitesGMap')}}",
            method:"POST",
            data:{lga_code:lgacode, _token: "{{ csrf_token() }}"},
            success:function(result)
            {
                var locations = result.facilities_list;
                //set the title of modal form
                var modalTitle = "Hospitals and Clinics in ".concat(result.lga_name);
                modalTitle = modalTitle.concat(" LGA")
                $('#myModalLabel').text(modalTitle);

                //google map functions starts here

                //get the first coordinate to center the map
                var lati,longi;
                $.each(locations, function(i, item) {   
                    lati = item.latitude;
                    longi = item.longitude;
                    return false;
                });

                var map = new google.maps.Map(document.getElementById('googleMap'), {
                    zoom: 9,
                    center: new google.maps.LatLng(lati, longi),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                
                map.data.loadGeoJson(full_path);
                
                var infowindow = new google.maps.InfoWindow();
                
                var marker;
                $.each(locations, function(i, item) {
                    //add labels for different fac types
                    if (item.facility_level=="Primary"){
                        var labels="P";
                    }
                    if (item.facility_level=="Secondary"){
                        var labels="S";
                    }
                    if (item.facility_level=="Tertiary"){
                        var labels="T";
                    }
                    //icon ownership
                    if (item.ownership=="Public"){
                        var icon = {
                            url: "http://maps.google.com/mapfiles/ms/micons/green.png", 
                            scaledSize: new google.maps.Size(40, 40), 
                        };
                    }
                    if (item.ownership=="Private"){
                        var icon = {
                            url: "http://maps.google.com/mapfiles/ms/micons/blue.png", 
                            scaledSize: new google.maps.Size(40, 40), 
                        };
                    }

                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(item.latitude, item.longitude),
                        label: labels,
                        icon: icon,
                        map: map
                    });
                    
                    google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
                        return function() {
                            infowindow.setContent(item.facility_name);
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                    
                    //add facility details
                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                            var days = item.operational_days;
                            var operational_days = days.replace(/\,/g, ", ");

                            $('#phone_number1').text(item.phone_number== null ? "" :item.phone_number);
                            $('#email_address1').text(item.email_address== null ? "" :item.email_address);
                            $('#website1').text(item.website== null ? "" :item.website);
                            $('#operational_days1').text(item.operational_days== null ? "" :operational_days);
                            $('#operational_hours1').text(item.operational_hours== null ? "" :item.operational_hours);
                            $('#operation_status1').text(item.operation_status== null ? "" :item.operation_status);
                            $('#regulatory_status1').text(item.regulatory_status== null ? "" :item.regulatory_status);
                            $('#license_status1').text(item.license_status== null ? "" :item.license_status);
                            $('#doctors1').text(item.doctors== null ? "" :item.doctors);
                            $('#dentist1').text(item.dentist == null ? "" :item.dentist);
                            $('#nurses1').text(item.nurses== null ? "" :item.nurses);
                            $('#midwifes1').text(item.midwifes== null ? "" :item.midwifes);
                            $('#nurse_midwife1').text(item.nurse_midwife == null ? "" :item.nurse_midwife );

                            $('#details_Title').text(item.facility_name);
                            
                            $('#details_onGmap').modal('show')
                        }
                    })(marker, i));
                });
                
                
            }//success ends         
        });//ajax ends
        
    }//end function
    
    function showFacilityByLevelSelectedState(byLevel,statename){
        Highcharts.chart('levels', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: "Hospitals and Clinics by Level of Care"
            },
            subtitle: {
                text: statename.concat(" State")
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
                }
            },
            series: [{
                name: 'Facilities',
                colorByPoint: true,
                data: byLevel
            }],
            credits: {
                        enabled: false
                    },
        });
    }

    function showFacilityByOwnershipSelectedState(byOwnership,statename){
        Highcharts.chart('ownership', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: "Hospitals and Clinics by Ownership"
        },
        subtitle: {
            text: statename.concat(" State")
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                style: {
                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
            }
        },
        series: [{
            name: 'Facilities',
            colorByPoint: true,
            data: byOwnership
        }],
        credits: {
                    enabled: false
                },
        });
    }
    
    function showFacilitywithGeoCodesState(geo_percent,statename){
        Highcharts.chart('geo', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Percentage of Hospitals and Clinics with Geo Coordinates'
        },
        subtitle: {
            text: statename.concat(" State")
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">Percent: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: false
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'LGAs',
            data: geo_percent
        }]
    });
    } 


</script>


<script> 
//-- show facility by leveles chart--
function facilitybyLevel(){
    Highcharts.chart('levels', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: "Hospitals and Clinics by Level of Care"
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
                }
            },
            series: [{
                name: 'Facilities',
                colorByPoint: true,
                data: @json($facilities_level_state)
            }],
            credits: {
                        enabled: false
                    },
    });
}  

//show facilities by ownership sumary 
function facilitybyOwnership(){
    Highcharts.chart('ownership', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: "Hospitals and Clinics by Ownership"
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                style: {
                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
            }
        },
        series: [{
            name: 'Facilities',
            colorByPoint: true,
            data: @json($facilities_ownership_state)
        }],
        credits: {
                    enabled: false
                },
        });
}

//-- geocoordinates summary --
function facilitywithGeoCodes(){
    Highcharts.chart('geo', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Percentage of Hospitals and Clinics with Geo Coordinates'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">Percent: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: false
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'States',
            data: @json($geo_percent)
        }]
    });
}

</script>



@endpush