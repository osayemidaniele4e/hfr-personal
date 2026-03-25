@extends("layouts.master")

@section('bk_css')
  
@endsection

@section('content-title')
    Dashboard
@endsection

@section("content")
  <div class="">
        <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                            <div id="facility_status"></div>
                    </div>
                </div>
         
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                        <div id="completeness"></div>
                </div>
            </div>
        </div>

        <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                            <div id="visitors"></div>
                    </div>
                </div>
         
        </div>
      
        <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                            <div id="no_downloads"></div>
                    </div>
                </div>
        </div>

       <div class="row" hidden>
            <table  id="fac_status_table">
                    <thead>
                    <tr>
                        <th></th>
                        <th>New Facility Requests (Pending Verification)</th>
                        <th>Update Requests (Pending Verification)</th>
                        <th>Deletion Requests (Pending Verification)</th>
                        <th>Verified Requests (Pending Validation)</th>
                        <th>Validated Requests  (Pending Publication)</th>
                        <th>New Facility Published</th>
                        <th>Update Request Published</th>
                        <th>Deletion Request Published</th>
                        <th>Rejected Verifications </th>
                        <th>Rejected Validations (Pending Verification)</th>
                        <th>Rejected Publications (Pending Validation)</th>
                    </tr>
                
                    </thead>
                    <tbody>
                            
                        @foreach($facility_status as $status)
                            <tr>
                             <td>{{ $status->state }}</td>
                             <td>{{ $status->New_Facility_Requested }}</td>
                             <td>{{ $status->Update_Requested }}</td>
                             <td>{{ $status->Deletion_Requested }}</td>
                             <td>{{ $status->Request_Verified }}</td>
                             <td>{{ $status->Request_Validated }}</td>
                             <td>{{ $status->Facility_Created }}</td>
                             <td>{{ $status->Facility_Updated }}</td>
                             <td>{{ $status->Facility_Deleted }}</td>
                             <td>{{ $status->Verification_Rejected }}</td>
                             <td>{{ $status->Validation_Rejected }}</td>
                             <td>{{ $status->Publishing_Rejected }}</td>

                            </tr>
                        @endforeach
                    <tbody>
            </table>


            <table  id="fac_status_state">
                    <thead>
                    <tr>
                        <th></th>
                        <th>New Facility Requests</th>
                        <th>Update Requests</th>
                        <th>Deletion Requests </th>
                        <th>Verified Requests</th>
                        <th>Validated Requests</th>
                        <th>New Facility Published</th>
                        <th>Update Request Published</th>
                        <th>Deletion Request Published</th>
                        <th>Rejected Verifications </th>
                        <th>Rejected Validations</th>
                        <th>Rejected Publications</th>
                    </tr>
                
                    </thead>
                    <tbody>
           
                    <tbody>
            </table>
       </div>
     
  </div>


  
{{-- Modal chart drill down  --}}
<div class="modal fade" id="showstate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="state_summary"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">   
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- end --}}
 
@endsection 

@push('bk_script')
    <script src="{{ asset("hcharts/chart/highcharts.js")}}"></script>
    <script src="{{ asset("hcharts/chart/exporting.js")}}"></script>
    <script src="{{ asset("hcharts/chart/data.js")}}"></script>
   

<script>
//facility status chart
$('#fac_status_table').hide();
        
    Highcharts.chart('facility_status', {
        chart: {
            type: 'column'
            },
            title: {
                text: 'Health Facilities Status by State'
            },
            data: {
                table: 'fac_status_table'
            },
            colors: ['#2f7ed8','#C0C0C0', '#DDDF00', '#0d233a', '#BA55D3', '#a6c961','#64E572','#50B432', '#f28f43', '#ED561B','#910000' ],
            yAxis: {
                min: 0,
                title: {
                text: ''
                },
                stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
                }
            },
            legend: {
                verticalAlign: 'bottom',
                floating: false,
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            tooltip: {
                
                pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                    },

                },
                series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            $('#myModalLabel').text('Health Facilities Status');
                            $('#fac_status_state tbody').empty();
                            
                            $.ajax({
                                url:"{{route('state.facilitystatus')}}",
                                method:"POST",
                                data:{state:this.name, _token: "{{ csrf_token() }}"},
                                success:function(result)
                                {   
                                    $.each(result, function (index, value) {
                                       var row = "<tr> \
                                            <td>" + value['lga'] + "</td> \
                                            <td>" + value['New_Facility_Requested'] + "</td> \
                                            <td>" + value['Update_Requested'] + "</td> \
                                            <td>" + value['Deletion_Requested'] + "</td> \
                                            <td>" + value['Request_Verified'] + "</td> \
                                            <td>" + value['Request_Validated'] + "</td> \
                                            <td>" + value['Facility_Created'] + "</td> \
                                            <td>" + value['Facility_Updated'] + "</td> \
                                            <td>" + value['Facility_Deleted'] + "</td> \
                                            <td>" + value['Verification_Rejected'] + "</td> \
                                            <td>" + value['Validation_Rejected'] + "</td> \
                                            <td>" + value['Publishing_Rejected'] + "</td> \
                                        </tr>";

                                        $('#fac_status_state > tbody:last-child').append(row);
                                    });
                                    
                                    Highcharts.chart('state_summary', {
                                        chart: {
                                            type: 'column'
                                            },
                                            title: {
                                                text: 'Health Facilities Status'
                                            },
                                            data: {
                                                table: 'fac_status_state'
                                            },
                                            colors: ['#2f7ed8','#C0C0C0', '#DDDF00', '#0d233a', '#BA55D3', '#a6c961','#64E572','#50B432', '#f28f43', '#ED561B','#910000' ],
                                            yAxis: {
                                                min: 0,
                                                title: {
                                                text: ''
                                                },
                                                stackLabels: {
                                                enabled: true,
                                                style: {
                                                    fontWeight: 'bold',
                                                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                                                }
                                                }
                                            },
                                            legend: {
                                                verticalAlign: 'bottom',
                                                floating: false,
                                                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                                                borderColor: '#CCC',
                                                borderWidth: 1,
                                                shadow: false
                                            },
                                            tooltip: {
                                                
                                                pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                                            },
                                            plotOptions: {
                                                column: {
                                                    stacking: 'normal',
                                                    dataLabels: {
                                                        enabled: false,
                                                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                                                    },

                                                },
                                        
                                        
                                            },
                                            credits: {
                                                enabled: false
                                            },
                                
                                    });
                               
                                    $('#showstate').modal('show');
                                
                                }//success ends         
                            });
                        }
                    }
                }
            }
          
            },
            credits: {
                enabled: false
            },
  
    });

//visitors
Highcharts.chart('visitors', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Number of Visitors in the Last 30 days'
    },
    subtitle: {
        text: 'Source: Google Analytics'
    },
    xAxis: {
        categories: @json($dates),
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Visitors'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: 'Visits',
        data: @json($visitors)
    }],
    credits: {
        enabled: false
    },
});

    //downloads
    Highcharts.chart('no_downloads', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Monthly Download Requests'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Downloads Requests'
            }
        },
        series: [{
            name: 'Downloads',
            colorByPoint: true,
            data: @json($num_downloads)
            }],

        credits: {
                enabled: false
        },
    });

    //completenes
    Highcharts.chart('completeness', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Completeness of Signature Domain'
        },
        subtitle: {
            text: ''
        },
        colors: ['#2f7ed8' ],
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
            },
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            $("#state_summary").empty();
                            $('#myModalLabel').text('Completeness of Signature Domain in '+this.name);
                          
                            $.ajax({
                                url:"{{route('state.completeness')}}",
                                method:"POST",
                                data:{state:this.name, _token: "{{ csrf_token() }}"},
                                success:function(result)
                                {
                                    Highcharts.chart('state_summary', {
                                        chart: {
                                            type: 'column'
                                        },
                                        title: {
                                            text: ''
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
                                            name: 'LGAs',
                                            data: result
                                        }]
                                    });

                                
                                    $('#showstate').modal('show');
                                
                                }//success ends         
                            });
                        }
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'States',
            colorByPoint: true,
            data: @json($completenes)
        }]
    });



</script>
    


@endpush