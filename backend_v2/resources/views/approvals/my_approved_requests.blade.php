@extends("layouts.master")

@section('content-title')
My Published Requests

@endsection

@section("content")




<div class="box">
 
<div class="box-body">
     {{-- search option --}}
     <form class="form-horizontal"  action="{{route('myrequest.search')}}" method="GET">
        @csrf
        
        <div class="form-group">
            
            <div class="col-md-10">
                <select class="form-control select2"  class="form-control" name="status"   data-width="100%">
                    <option value="1">My Pending Requests</option>    
                    <option value="2">My Rejected Requests</option>  
                    <option value="3">My Published Requests</option>                           
                </select>
            </div>
            
            <div class="col-sm-2">
                <button type="submit" class="btn btn-success pull-right  btn-block btn-sm">Show</button>
            </div> 
        </div>
        
    </form>
    <hr>
    {{-- --endsearch-- --}}
@if(!empty($myrequests))

  <table id="table1" class="table table-bordered table-striped" style="width:100%">
    <thead>
      <tr>
          <th>Facility Name</th>
          <th>Request Details</th>
          <th>Verified By</th>
          <th>Validated By</th>
          <th>Published By</th>
      </tr>
    </thead>
    <tbody>
        @foreach($myrequests as $r)
        <tr>
          <td>{{$r->facility_name}}</td>
          <td>
              <Strong>Request Type: </Strong>{{ $r->action }} <br>   
              <Strong>Request Date: </Strong>{{ ($r->requested_at? date('d M Y', strtotime($r->requested_at)) : '') }} <br>    
              <Strong>Request Note: </Strong>{{ $r->request_note }} <br>
          </td>
          <td>
                <Strong>Name: </Strong>{{$r->verified_by}} <br>
                <Strong>E-mail: </Strong>{{$r->verified_email}} <br>
                <Strong>Mobile: </Strong>{{ $r->verified_mobile }} <br>
                <Strong>Date: </Strong>{{ ($r->verified_at? date('d M Y', strtotime($r->verified_at)) : '') }} <br>     
                <font color="MediumSeaGreen"><Strong>Remarks: </Strong></font>{{ $r->verify_note }} <br>

          </td>
          <td>
                <Strong>Name: </Strong>{{$r->validated_by}} <br>
                <Strong>E-mail: </Strong>{{$r->validated_email}} <br>
                <Strong>Mobile: </Strong>{{ $r->validated_mobile }} <br>
                <Strong>Date: </Strong>{{ ($r->validated_at? date('d M Y', strtotime($r->validated_at)) : '')}} <br> 
                <font color="MediumSeaGreen"><Strong>Remarks: </Strong></font>{{ $r->validate_note }} <br>

          </td>
          <td>
                <Strong>Name: </Strong>{{$r->published_by}} <br>
                <Strong>E-mail: </Strong>{{$r->published_email}} <br>
                <Strong>Mobile: </Strong>{{ $r->published_mobile }} <br>
                <Strong>Date: </Strong>{{  ($r->published_at? date('d M Y', strtotime($r->published_at)) : '')}} <br>  
                <font color="MediumSeaGreen"><Strong>Remarks: </Strong></font>{{ $r->publish_note }} <br>

          </td>
      
    
    
        </tr>
        @endforeach
        
      </tbody>
    </table>
  @else
    <div class="callout callout-success">
        <p>No record found!</p>
    </div>
@endif
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->


 
  

@endsection 
  
@push("bk_script")

@include('partials.notification')


<script>
  $(document).ready(function(){
    $('#table1').DataTable( {
        "paging":   true,
        "ordering": true,
        "info":     true,
        responsive: true
    } );

  });
</script>

@endpush