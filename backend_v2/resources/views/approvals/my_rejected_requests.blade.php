@extends("layouts.master")

@section('content-title')
My Rejected Requests

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
          <th>Request Type</th>
          <th>Verification</th>
          <th>Validation</th>
          <th>Publication</th>
      </tr>
    </thead>
    <tbody>
        @foreach($myrequests as $r)
        <tr>
          <td> {{$r->facility_name}} </td>
          <td><span class="label label-default">{{$r->action}} </span></td>

          <td>
              @if ($r->verified_email != "" )
                @if (in_array($r->status_id,[2,9,16,4,11,18,5,12,19,7,14,21])) 
                  <span class="label label-success"> Accepted </span> <br>
                @endif
                @if (in_array($r->status_id,[3,10,17])) 
                  <span class="label label-danger"> Rejected </span> <br>
                @endif
                <Strong>Date: </Strong>{{ ($r->verified_at? date('d M Y', strtotime($r->verified_at)) : '') }} <br>   
                <Strong>By: </Strong>{{$r->verified_by}} <br>
                <Strong>E-mail: </Strong>{{$r->verified_email}} <br>
                <Strong>Mobile: </Strong>{{ $r->verified_mobile }} <br>
                <font color="MediumSeaGreen"><Strong>Remarks: </Strong></font> {{ $r->verify_note }}
              @else
                  Pending Verification
              @endif          
         </td>
         <td>
            @if ($r->validated_email != "" )
                @if (in_array($r->status_id,[4,11,18,7,14,21])) 
                  <span class="label label-success"> Accepted </span> <br>
                @endif
                @if (in_array($r->status_id,[5,12,19])) 
                  <span class="label label-danger"> Rejected </span> <br>
                @endif
                @if (in_array($r->status_id,[3,10,17]) and $r->validated_email != "") 
                  <span class="label label-danger"> Rejected </span> <br>
                @endif
                <Strong>Date: </Strong>{{ ($r->validated_at? date('d M Y', strtotime($r->validated_at)) : '') }} <br>
                <Strong>By: </Strong>{{$r->validated_by}} <br>
                <Strong>E-mail: </Strong>{{ $r->validated_email }} <br>
                <Strong>Mobile: </Strong>{{ $r->validated_mobile }} <br>
                <font color="MediumSeaGreen"><Strong>Remarks: </Strong></font>{{ $r->validate_note }}
            @else
                Pending Validation
            @endif               
        </td>
        <td>
            @if ($r->published_by !="")
                @if (in_array($r->status_id,[6,13,20])) 
                  <span class="label label-success"> Accepted </span> <br>
                @endif
                @if (in_array($r->status_id,[7,14,21])) 
                  <span class="label label-danger"> Rejected </span> <br>
                @endif
                @if (in_array($r->status_id,[3,10,17]) and $r->published_email != "") 
                    <span class="label label-danger"> Rejected </span> <br>
                @endif
                @if (in_array($r->status_id,[5,12,19]) and $r->published_email != "") 
                  <span class="label label-danger"> Rejected </span> <br>
                @endif
                <Strong>Date: </Strong>{{ ($r->published_at? date('d M Y', strtotime($r->validated_at)) : '') }} <br>
                <Strong>By: </Strong>{{ $r->published_by }} <br>
                <Strong>E-mail: </Strong>{{ $r->published_email }} <br>
                <Strong>Mobile: </Strong>{{ $r->published_mobile }} <br>
                <font color="MediumSeaGreen"><Strong>Remarks: </Strong></font> {{ $r->publish_note }}
            @else
                Pending Publication
            @endif              
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