@extends("layouts.master")


@section('content-title')
Facilities Update Reports

@endsection

@section("content")
 
 
<div class="box">
    
        <div class="box-body">
                <form class="form-horizontal"  action="{{route('updates.report')}}" method="GET">
                        @csrf
            
                        <div class="form-group">
                            
                            <div class="col-sm-5">
                                <select class="form-control select2" id="report" name="report" required>
                                    <option value="">--Select Report--</option>
                                    <option value="1" {{ (1 == $data['report'] ? "selected":"") }}>New Facilities </option>
                                    <option value="2" {{ (2 == $data['report'] ? "selected":"") }}>Updated Facilities</option>
                                    <option value="3" {{ (3 == $data['report'] ? "selected":"") }}>Deleted Facilities</option>
                                    
                                </select>
                            </div>
                            <label class="col-sm-1 control-label">From:</label>
                            <div class="col-sm-2">
                                <div class="input-group date" >
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="datepicker" name="from_date" value="{{ $data['from'] }}" autocomplete="off" required>
                                </div>
                            </div>
                            <label class="col-sm-1 control-label">To :</label>
                            <div class="col-sm-2">
                                <div class="input-group date" >
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="datepicker1" name="to_date" value="{{ $data['to'] }}" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-sm-1">
                                    <button type="submit" class="btn btn-success pull-right  btn-block btn-sm">Show</button>
                            </div> 
                        </div>
            
                </form>

                @if ($facilities != "none")

                <div class="alert alert-success alert-dismissible">
                   {{ $data['message'] }}
                </div>

                <table id="table2" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>State</th>
                        <th>LGA</th>
                        <th>Ward</th>
                        <th>Facility ID</th>
                        <th>Facility Name</th>
                        <th>Facility Level</th>
                        <th>Ownership</th>
                        @if ($data['report'] == 1)
                            <th>Date Created</th>  
                        @endif
                        @if ($data['report'] == 2)
                            <th>Date Updated</th>  
                        @endif
                        @if ($data['report'] == 3)
                            <th>Date Deleted</th>  
                        @endif

                    </tr>
                    </thead>
                    <tbody>
                
                        @foreach($facilities as $fac)
                            <tr>
                                <td>{{$fac->state}}</td>
                                <td>{{$fac->lga}}</td>
                                <td>{{$fac->ward}}</td>
                                <td>{{$fac->unique_id}}</td>
                                <td>{{$fac->facility_name}}</td>
                                <td>{{$fac->facility_level}}</td>
                                <td>{{$fac->ownership}}</td>
                                <td>
                                    @if ($data['report'] == 1)
                                        {{ date('d M Y', strtotime($fac->created_at)) }}
                                    @else
                                        {{ date('d M Y', strtotime($fac->updated_at)) }} 
                                    @endif
                                </td>
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
            <form class="form-horizontal"  action="{{route('updates.download')}}" method="post">
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
    
    <script>
        
        $(document).ready( function () {
            $('#table2').DataTable( {
                "paging":   true,
                "ordering": true,
                "info":     true
            });

        });
        $(function () {            
            //Date picker
            $('#datepicker1').datepicker({
                autoclose: true,
                endDate: new Date(),
            })       
                                                        
        })
    
     
    </script>
    
@endpush