@extends("layouts.master")


@section('content-title')
Facilities Status Summary

@endsection

@section("content")
 
 
<div class="box">
    
        <div class="box-body">
                <form class="form-horizontal"  action="{{route('status.report')}}" method="GET">
                        @csrf
            
                        <div class="form-group">
                                <div class="col-sm-10">
                                        <select class="form-control select2" id="state_id" name ="state_id">
                                            <option value="1">All States</option>
                                            @foreach(getStates() as $st)
                                            <option value="{{$st->id}}"  {{ ($st->id == $data['state_id'] ? "selected":"") }}>{{$st->name}}</option>
                                            @endforeach
                                        </select>
                                </div>
                                    
                            
                                <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success btn-block pull-right  btn-sm">Show</button>
                                </div> 
                        </div>    
                        <hr>

                </form>

                @if ($facility_status->count() == 0)
                    <div class="alert alert-success alert-dismissible">
                        No record found!
                    </div>
                @else

                <table id="table2" class="table table-bordered table-striped">
                        <thead>
                                <tr>
                                    @if ($data['state_id']==1)
                                        <th>State</th>
                                    @else
                                        <th>LGA</th>                                        
                                    @endif
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
                                    @foreach($facility_status as $status)
                                        <tr>
                                            @if ($data['state_id']==1)
                                                <td>{{ $status->state }}</td>
                                            @else
                                                <td>{{ $status->lga }}</td>
                                            @endif
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
            @endif
        
        
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
    
        {{-- download buttons  --}}
        <div class="btn-group pull-right">
            <form class="form-horizontal"  action="{{route('status.download')}}" method="post">
                @csrf
                
                <input type="hidden" id="state_id" name="state_id" value="{{ $data['state_id'] }}">  

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
                "info":     true,
                responsive: true
            });

        });
    
    
     
    </script>
    
@endpush