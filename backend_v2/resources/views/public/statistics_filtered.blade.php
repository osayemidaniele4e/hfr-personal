@extends("layouts.pub.master")

@section('custom_css')

@endsection


@section("content")  


<div class="latest-area section-padding bg-white">
    <div class="container">
            <div class="box-header">
            <form class="form-horizontal"  action="{{route("filterStatistics")}}" method="GET">
                            @csrf
                        <div class="form-group">
                                <div class="col-sm-6">
                                    <select class="form-control select2" id="facility_type_id" name="facility_type_id">
                                        @foreach($lst_facility_types as $ty)
                                            <option value="{{$ty->id}}">{{$ty->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                    
                                <div class="col-sm-5">
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
        {{-- filter message --}}
        <div role="alert" class="alert alert-success"> 
            <p id="filtermessage"></p>
        </div>
        <div class="row">
            {{-- Hospitals --}}
                <div class="col-sm-3">     
                        <div class="single-latest-item">   
                            <div class="single-latest-text">
                                    Number of Hospitals and Clinics  
                                   <strong> {{ $total_num_fac[0] }}</strong>
                            </div>
                        </div>
                </div>
                   {{-- pharmacy --}}
                <div class="col-sm-3">     
                        <div class="single-latest-item">   
                            <div class="single-latest-text">
                                    Number of Pharmaceuticals Premises  
                                    <strong>{{ $total_num_fac[2] }}</strong>
                                    
                            </div>
                        </div>
                </div>
                    {{-- lab --}}
                <div class="col-sm-3">     
                        <div class="single-latest-item">   
                            <div class="single-latest-text">
                                    Number of Laboratories Premises  
                                   <strong> {{ $total_num_fac[1] }}</strong>
                            </div>
                        </div>
                </div>
                {{-- radiology--}}
                <div class="col-sm-3">     
                        <div class="single-latest-item">   
                            <div class="single-latest-text">
                                    Number of Radiologies and Imaging   
                                    <strong>{{ $total_num_fac[3] }}</strong>
                                   
                            </div>
                        </div>
                </div>

        </div>

        <!-- *******************summary 1 ends**************** -->

        {{-- summary 2 --}}
        <div class="row">
            <div class="col-sm-6">
                    <div class="single-latest-item">
                    
                    
                        <div class="single-latest-text">
                                <h4 id="h_ownership"></h4> <br>
                                <div class="display" style="width:100%">
                                    <table class="table no-margin" id="table1">
                                        <thead>
                                        <tr>
                                            @if($state_id==0)
                                                <th>State</th>
                                            @else
                                                <th>LGA</th>
                                            @endif
                                            <th>Public</th>
                                            <th>Private</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                                @foreach($ownerships_by_lga as $own)
                                                <tr>
                                                    @if($state_id==0)
                                                        <td>{{$own->state}}</td>
                                                    @else
                                                        <td>{{$own->lga}}</td>
                                                    @endif
                                                    
                                                    <td>{{$own->Public}}</td>
                                                    <td>{{$own->Private}}</td>
                                                    <td>{{$own->Public + $own->Private}}</td>
                                                </tr>
                                                @endforeach
                                        <tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
            </div>

            <div class="col-sm-6">
                    <div class="single-latest-item">
                           
                            <div class="single-latest-text">
                                    <h4 id="h_level"> </h4> <br>
                                    <div class="display">
                                            <table class="table no-margin" id="table2">
                                                <thead>
                                                <tr>
                                                    @if($state_id==0)
                                                        <th>State</th>
                                                    @else
                                                        <th>LGA</th>
                                                    @endif
                                                    <th>Primary</th>
                                                    <th>Secondary</th>
                                                    <th>Tertiary</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($levels_by_lga as $lev)
                                                        <tr>
                                                            @if($state_id==0)
                                                                <td>{{$lev->state}}</td>
                                                            @else
                                                                <td>{{$lev->lga}}</td>
                                                            @endif
                                                            <td>{{$lev->Primary}}</td>
                                                            <td>{{$lev->Secondary}}</td>
                                                            <td>{{$lev->Tertiary}}</td>
                                                            <td>{{$lev->Primary + $lev->Secondary + $lev->Tertiary}}</td>
                                                        </tr>
                                                    @endforeach
                                                <tbody>
                                            </table>
                                        </div>
                            </div>
                        </div>
            </div>
        
            
        </div>
        {{-- summary 2 ends --}}
        {{-- summary table 3 --}}
        @if($facility_type_id ==1)

            <div class="row">
                <div class="col-sm-12">
                        <div class="single-latest-item">
                        
                            <div class="single-latest-text">
                                    <h4>Hospitals and Clinics by Ownership and Level of Care</h4> <br>
                                    <div class="display" style="width:100%">
                                        <table class="table no-margin" id="table3">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">LGA</th> 
                                                    <th colspan="4">Public</th> 
                                                    <th colspan="4">Private</th> 
                                                    <th rowspan="2">Total (A+B)</th>
                                                </tr>                                          
                                                    <th>Primary</th> 
                                                    <th>Secondary</th> 
                                                    <th>Tertiary</th>
                                                    <th>Sub-Total (A)</th> 
                                                    <th>Primary</th> 
                                                    <th>Secondary</th> 
                                                    <th>Tertiary</th>  
                                                    <th>Sub-Total (B)</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                    @foreach($levels_ownership_by_lga as $lev)
                                                    <tr>
                                                        @if($state_id==0)
                                                            <td>{{$lev->state}}</td>
                                                        @else
                                                            <td>{{$lev->lga}}</td>
                                                        @endif
                                                        <td>{{$lev->Pub_Primary}}</td>
                                                        <td>{{$lev->Pub_Secondary}}</td>
                                                        <td>{{$lev->Pub_Tertiary}}</td>
                                                        <td>{{$lev->Pub_Primary + $lev->Pub_Secondary + $lev->Pub_Tertiary}}</td>
                                                        <td>{{$lev->Priv_Primary}}</td>
                                                        <td>{{$lev->Priv_Secondary}}</td>
                                                        <td>{{$lev->Priv_Tertiary}}</td>
                                                        <td>{{$lev->Priv_Primary + $lev->Priv_Secondary + $lev->Priv_Tertiary}}</td>
                                                        <td>{{$lev->Pub_Primary + $lev->Pub_Secondary + $lev->Pub_Tertiary+$lev->Priv_Primary + $lev->Priv_Secondary + $lev->Priv_Tertiary}}</td>
                                                    </tr>
                                                @endforeach
                                            <tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>
                </div>

            </div>
            
        @endif
    </div> 
</div> {{--  --}}


@endsection 

@push('custom_scripts')

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>

@include('partials.dynamic_lgas_only')

    
<script>

$(document).ready( function () {
    
    //table 1
    $('#table1').DataTable( {
        "paging":   true,
        "ordering": false,
        "info":     true,
        "lengthChange": true,
        "searching"   : true,
        "autoWidth"   : false,
        "pageLength": 8,
    } );
     //table 2
     $('#table2').DataTable( {
        "paging":   true,
        "ordering": false,
        "info":     true,
        "lengthChange": true,
        "searching"   : true,
        "autoWidth"   : false,
        "pageLength": 8,
    } );

       //table 3
       $('#table3').DataTable( {
        "paging":   true,
        "ordering": false,
        "info":     true,
        "lengthChange": true,
        "searching"   : true,
        "autoWidth"   : false,
        "pageLength": 8,
    } );


    $("#state_id").val({{$state_id}}).change();
    $("#facility_type_id").val({{$facility_type_id}}).change();
    $("#h_ownership").text($("#facility_type_id :selected").text() + " by Ownership");
    $("#h_level").text($("#facility_type_id :selected").text() + " by Level of Care");
    //set message after filter
    $("#filtermessage").text("Summary of "+ $("#facility_type_id :selected").text() + " in "+  $("#state_id :selected").text() + " State");
    
});


</script>

@endpush