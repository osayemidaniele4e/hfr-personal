@extends("layouts.master")


@section('content-title')


@endsection

@section("content")

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">Updated values for  <font color="MediumSeaGreen"><Strong>{{ $name }}</Strong></font> </h3>
    
    
    <!-- /.box-tools -->
  </div>
  <!-- /.box-header -->
  <form method="POST" action="{{route('publish.store')}}">
    @csrf
    
    <input type="hidden" id="id" name="id" value="{{ $hosp_id }}">
    <input type="hidden" name="requested_action" value="UPDATE FACILITY">

    <div class="box-body">
      <div class="form-group">
        <table class="table table-hover">
          
          <tbody>
            @foreach($audits as $attr=>$audit)
            <tr>  
                {{-- check if values are not empty --}}
                @if(!($audit['old']=="" and $audit['new']=="")) 

                   <td><label>{{array_search($attr,$lookup)}}</label></td>
                    <td>Updated from </td>
                    
                    @if($attr === 'state_id') 
                    <td>{{$old_values->state ==""? 'null': $old_values->state}}</td>
                    <td>to</td>
                    <td>{{$new_values->state}}</td>
                    @elseif($attr ==='lga_id')
                    <td>{{$old_values->lga ==""? 'null': $old_values->lga }}</td>
                    <td>to</td>
                    <td>{{$new_values->lga}}</td>
                    @elseif($attr === 'ward_id' )
                    <td>{{$old_values->ward ==""? 'null': $old_values->ward}}</td>
                    <td>to</td>
                    <td>{{$new_values->ward}}</td>
                    @elseif($attr === 'ownership_id')
                    <td>{{$old_values->ownership ==""? 'null': $old_values->ownership}}</td>
                    <td>to</td>
                    <td>{{$new_values->ownership}}</td>
                    @elseif($attr === 'ownership_type_id')
                    <td>{{$old_values->ownership_type == "" ? 'null' :$old_values->ownership_type}}</td>
                    <td>to</td>
                    <td>{{$new_values->ownership_type}}</td>
                    @elseif($attr === 'facility_level_id')
                    <td>{{$old_values->facility_level ==""? 'null': $old_values->facility_level}}</td>
                    <td>to</td>
                    <td>{{$new_values->facility_level}}</td>
                    @elseif($attr === 'facility_level_option_id')
                    <td>{{$old_values->facility_level_option  ==""? 'null': $old_values->facility_level_option}}</td>
                    <td>to</td>
                    <td>{{$new_values->facility_level_option}}</td>
                    @elseif($attr === 'operational_status_id')
                    <td>{{$old_values->operation_status  ==""? 'null': $old_values->operation_status}}</td>
                    <td>to</td>
                    <td>{{$new_values->operation_status}}</td>
                    @elseif($attr === 'registration_status_id')
                    <td>{{$old_values->registration_status ==""? 'null': $old_values->registration_status}}</td>
                    <td>to</td>
                    <td>{{$new_values->registration_status ==""? 'null': $new_values->registration_status}}</td>
                    @elseif($attr ==='license_status_id')
                    <td>{{$old_values->license_status ==""? 'null': $old_values->license_status}}</td>
                    <td>to</td>
                    <td>{{$new_values->license_status}}</td>
                    @else
                    <td>{{$audit['old'] == "" ? 'null' : $audit['old']}}</td>
                    <td>to</td>
                    <td>{{$audit['new']}}</td>
                    @endif
                 
                @endif
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      @if (!empty($new_services))
      <div class="row">
        <label class="col-md-2">Services Updated from</label>
        <div class="col-md-10">
            @if (!empty($old_services))
                @foreach ($old_services as $item)
                    <span class='label label-default'> {{ $item->name }}</span>
                @endforeach
            @else
                <span class='label label-default'> Null</span>                
            @endif
          
        </div>
      </div>

      <div class="row">
          <label class="col-md-2">To</label>
          <div class="col-md-10">
              @foreach ($new_services as $item)
                  <span class='label label-default'> {{ $item->name }}</span>
              @endforeach
          </div>
          
      </div>
    @endif
    
      
      <div class="row">
        <label class="col-md-2">Verification Note <font color="red">*</font></label>
        <div class="col-md-10">
          <textarea class="form-control" rows="3" name="notes" placeholder="Please enter verification note ..." required></textarea>
        </div>
      </div>
      
    </div>
    <div class="box-footer">
      <div class="pull-right">
        <button type="submit" class="btn btn-danger" name="action" value="reject">Reject</button>
        <button type="submit" class="btn btn-success" name="action" value="approve">Accept</button>
        <a href="{{ route('publish.pending') }}">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </a>
      </div>
    </div>
  </form>
</div>




@endsection 


@push('bk_script')
  @include('partials.notification')
@endpush