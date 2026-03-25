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
                            <div id="completeness_all"></div>
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

                });
                
                    </thead>
                    <tbody>
                            
                        @foreach($facility_status as $status)
                            <tr>
                             <td>{{ $status->lga }}</td>
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
       </div>
     
  </div>

 
@endsection 

@push('bk_script')
    <script src="{{ asset("hcharts/chart/highcharts.js")}}"></script>
    <script src="{{ asset("hcharts/chart/exporting.js")}}"></script>
    <script src="{{ asset("hcharts/chart/data.js")}}"></script>
   

<script>
    //facility status chart
    $('#fac_status_table').hide();
    var data = @json($facility_status);
    var state = data[0]['state'];

    Highcharts.chart('facility_status', {
        chart: {
            type: 'column'
            },
            title: {
                text: 'Health Facilities Status in ' + state
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
          
            },
            credits: {
                enabled: false
            },
  
    });

    //completenes for state by lgas
    Highcharts.chart('completeness', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Completeness of Signature Domain in ' + state
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
            data: @json($completenes)
        }]
    });

 //completenes all states
 Highcharts.chart('completeness_all', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Completeness of Signature Domain by State'
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
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'LGAs',
            data: @json($completenes_all)
        }]
    });


</script>
    


@endpush