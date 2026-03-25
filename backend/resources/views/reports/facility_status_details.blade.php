@extends("layouts.master")


@section('content-title')
Facilities Status Details

@endsection

@section("content")
 
 
<div class="box">
        <div class="box-header with-border">
                <form class="form-horizontal"  action="{{route('status.detailsReport')}}" method="get">
                    @csrf
        
                        <div class="form-group">
                                <div class="col-sm-3">  
                                        @if (Auth::user()->state_id == 1 )
                                            <select class="form-control select2" id="state_id" name ="state_id"  style="width: 100%;">
                                                <option value="1">--Select State--</option>
                                                @foreach(getStates() as $st)
                                                    <option value="{{$st->id}}" {{ ($st->id == old('state_id') ? "selected":"") }}>{{$st->name}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control select2 dynamic" id="state_id" name ="state_id" disabled required  style="width: 100%;">                                
                                                @foreach(getStates() as $st)
                                                    <option value="{{ $st->id }}" {{ ($st->id == old('state_id') ? "selected":"") }}>{{ $st->name }}</option>
                                                @endforeach
                                                
                                            </select>
                                            <input type="hidden" name="state_id" value="{{ Auth::user()->state_id }}" />
                                        @endif
                                    </div>
                            
                                <div class="col-sm-3">
                                    <select class="form-control select2" id="lga_id" name="lga_id"  style="width: 100%;">
                                        <option value="1">--Select LGA--</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                        <select class="form-control select2" name="status_id"  style="width: 100%;">
                                            <option value="0" {{ (old('status_id')==0 ? "selected":"") }}>Facilities Never Updated</option>
                                            <option value="1" {{ (old('status_id')==1 ? "selected":"") }}>New Facility Requests (Pending Verification)</option>
                                            <option value="8" {{ (old('status_id')==8 ? "selected":"") }}>Update Requests (Pending Verification)</option>
                                            <option value="15" {{ (old('status_id')==15 ? "selected":"") }}>Deletion Requests (Pending Verification) </option>
                                            <option value="2" {{ (old('status_id')==2 ? "selected":"") }}>Verified Requests (Pending Validation)</option>
                                            <option value="4" {{ (old('status_id')==4 ? "selected":"") }}>Validated Requests (Pending Publication)</option>
                                            <option value="6" {{ (old('status_id')==6 ? "selected":"") }}>New Facility Published</option>
                                            <option value="20" {{ (old('status_id')==20 ? "selected":"") }}>Update Request Published</option>   
                                            <option value="13" {{ (old('status_id')==13 ? "selected":"") }}>Deletion Request Published</option>
                                            <option value="3" {{ (old('status_id')==3 ? "selected":"") }}>Rejected Verifications </option>
                                            <option value="5" {{ (old('status_id')==5 ? "selected":"") }}>Rejected Validations (Pending Verification)</option>
                                            <option value="7" {{ (old('status_id')==7 ? "selected":"") }}>Rejected Publications (Pending Validation)</option>                                        
                                        </select>
                                </div>
                                <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success btn-block btn-sm">Show</button>
                                </div>
                             
                  
                            </div>
                   
        
                </form>
        </div>
                
    
        <div class="box-body">
            <div class="callout callout-success">
                <p>{{$message}}</p>
            </div>

            <table id="table1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>State</th>
                        <th>LGA</th>
                        <th>Ward</th>
                        <th>UID</th>
                        <th>Facility Name</th>
                        <th>Facility Level</th>
                        <th>Ownership</th>
                        <th>Action Type</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach($facilities as $fac)
                    <tr>
                        <td>{{$fac->state }}</td>
                        <td>{{$fac->lga}}</td>
                        <td>{{$fac->ward}}</td>
                        <td>{{$fac->id }}</td>
                        <td>{{$fac->facility_name}}</td>
                        <td>{{$fac->facility_level}}</td>
                        <td>{{$fac->ownership}}</td>
                        <td>{{$fac->action}}</td>
                 
                </tr>
                @endforeach
                
            </tbody>
        </table>
        
        
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <div class="row">
            
            @php
            $perpage = $facilities->perpage();
            $currentpage = $facilities->currentpage();
            $from = ($currentpage-1)*$perpage+1;
            
            if ($facilities->currentpage() == $facilities->lastpage()) {
                $to = $facilities->total();
            } else {
                $to = $currentpage*$perpage;
            }
            @endphp
            
            <div class="col-md-4">
                Showing {{$from}} to {{$to}} of {{$facilities->total()}} entries
                
            </div>
            <div class="col-md-8">
                <div class="pull-right">
                    {{$facilities->links()}}                  
                </div>
            </div>
            
        </div>
            {{-- download buttons  --}}
            <div class="btn-group pull-right">
                <form class="form-horizontal"  action="{{route('status.detailsDownload')}}" method="post">
                    @csrf
                     <input type="hidden" name="state" value="{{ old('state_id') }}" />
                     <input type="hidden" name="lga" value="{{ old('lga_id') }}" />
                     <input type="hidden" name="status" value="{{ old('status_id') }}" />

                    
                    <button type="submit" class="btn btn-primary btn-sm" >Download</button>

                </form>
            </div>

      
    </div>
    
</div>
<!-- /.box -->




@endsection 
    
    
@push('bk_script')
    @include('partials.dynamic_state_script')
    @include('partials.notification')
    
    <script>
        
        $(document).ready( function () {
     

            //get lgas
            if( {{Auth::user()->state_id}} != 1){
                var stateID= {{Auth::user()->state_id}};
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{route('getLgaList')}}",
                    method:"POST",
                    data:{id:stateID, _token:_token},
                    success:function(result)
                    {
                        $('#lga_id').html(result);
                        $('#lga_id').val( "{{old('lga_id')}}" );
                    }         
                });
            }
           

         
        });
 
        

        
        
    </script>
    
@endpush