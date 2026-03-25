@extends("layouts.master")


@section('content-title')
Facility Services Rendered

@endsection

@section("content")
 
 
<div class="box">
    
        <div class="box-body">
                <form class="form-horizontal"  action="{{route('services.report')}}" method="GET">
                        @csrf
            
                        <div class="form-group">
                                <div class="col-sm-4">
                                        <select class="form-control select2" id="state_id" name ="state_id">
                                            <option value="1">All States</option>
                                            @foreach(getStates() as $st)
                                            <option value="{{$st->id}}"  {{ ($st->id == $data['state_id'] ? "selected":"") }}>{{$st->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-sm-4">
                                        <select class="form-control select2" id="lga_id" name="lga_id">
                                            <option value="1">--Select LGA--</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="form-control select2" id="ward_id" name="ward_id">
                                            <option value="1">--Select Ward--</option>
                                        </select>
                                    </div>

                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <select class="form-control select2" id="facility_level_id"  name="facility_level_id" style="width: 100%;">
                                    <option value="">--Select Facility Level--</option>
                                    @foreach(getLevelOfCare() as $st)
                                    <option value="{{ $st->id }}"  {{ ($st->id == $data['facility_level_id'] ? "selected":"") }}>{{ $st->name }}</option>
                                    @endforeach
                                </select>        
                            </div>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="ownership_id"  name="ownership_id" style="width: 100%;">
                                    <option value="">--Select Ownership--</option>
                                    @foreach(getOwnership() as $st)
                                    <option value="{{$st->id}}" {{ ($st->id == $data['ownership_id']  ? "selected":"") }}>{{$st->name}}</option>
                                    @endforeach
                                    
                                </select>
                            </div>   
                            <div class="col-sm-2">          </div>  
                            <div class="col-sm-2">
                                    <button type="submit" class="btn btn-success btn-block pull-right  btn-sm">Show</button>
                            </div> 
                        </div>
                    <hr>
                </form>

                @if ($facilities->count() == 0)
                    <div class="alert alert-success alert-dismissible">
                        No record found!
                    </div>
                @else

                <table id="table2" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>State</th>
                        <th>LGA</th>
                        <th>Ward</th>
                        <th>Facility Name</th>
                        <th>Service Rendered</th>                   
                    </tr>
                    </thead>
                    <tbody>
                
                        @foreach($facilities as $fac)
                            <tr>
                                <td>{{$fac->state}}</td>
                                <td>{{$fac->lga}}</td>
                                <td>{{$fac->ward}}</td>
                                <td>{{$fac->facility_name}}</td>
                                <td>{{$fac->services}}</td>                           
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            @endif
        
        
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
    
        {{-- download buttons  --}}
        <div class="btn-group pull-right">
            <form class="form-horizontal"  action="{{route('services.download')}}" method="post">
                @csrf
                
                <button type="submit" class="btn btn-primary btn-sm" name='format' value='xls'>Download</button>

            </form>
        </div>

      
    </div>
    
</div>
<!-- /.box -->

    
@endsection 
    
    
@push('bk_script')
    @include('partials.notification')
    @include('partials.dynamic_state_script')

    <script>
        
        $(document).ready( function () {
            $('#table2').DataTable( {
                "paging":   true,
                "ordering": true,
                "info":     true
            });

        });
    
    
     
    </script>
    
@endpush