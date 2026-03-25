@extends("layouts.pub.master")

@section('custom_css')

  
@endsection

@section("content")  
<div class="latest-area section-padding bg-white">
    <div class="container">
            <div class="box-header">
                    <form class="form-horizontal"  action="{{route('population_filter')}}" method="POST">
                            @csrf
                            <div class="form-group">
                    
                           
                                    <div class="col-sm-11">
                                        <select class="form-control select2" id="state_id" name ="state_id">
                                            <option value="0">All States</option>
                                            @foreach($lst_states as $st)
                                                <option value="{{$st->id}}">{{$st->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            
                                    <div class="col-sm-1">
                                        <button type="submit" class="btn btn-success btn-sm pull-right">Filter</button>
                                    </div>
                            </div>
                    </form>
                
                </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="single-latest-item">      
                            <div id="pop_index" style="min-width: 310px; height: 500px; margin: 0 auto"></div>

                    </div>
                </div>

                
            </div>
    
    </div>
</div>
@endsection 

@push('custom_scripts')
<script src="{{ asset("hcharts/chart/highcharts.js")}}"></script>
<script src="{{ asset("hcharts/chart/exporting.js")}}"></script>
<script src="{{ asset("hcharts/chart/data.js")}}"></script>
<script src="{{ asset("hcharts/chart/offline-exporting.js")}}"></script>   


<script> 
    $("#state_id").val({{$state_id}}).change();

    var id = "{{ $state_id }}";
    var charttitle = "";
    var label,tooltipname = "";
   
    if(id == 0){
        charttitle = 'Population Index (Persons per Facility) in Nigeria';
        label = 'States';
        tooltipname = 'State'
    }
    else{
        charttitle = "Population Index (Persons per Facility) in " +  $("#state_id :selected").text() + " State";
        label = 'LGAs';
        tooltipname = 'LGA'
    }

    Highcharts.chart('pop_index', {
        chart: {
            type: 'column'
        },
        title: {
            text: charttitle
        },
        subtitle: {
            text: 'Source: Population Projection 2019'
        },
        xAxis: {
            categories: @json($pop_index_states),
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key} ' + tooltipname + ' </span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">Persons Per Facility: </td>' +
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
            name: label,
            
            data: @json($pop_index_ppf)
        }]
    });

    
</script>

@endpush