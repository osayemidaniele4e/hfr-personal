@extends("layouts.master")


@section('content-title')
Exchange Logs

@endsection

@section("content")
<div class="box">
       
    @if($flash = session("alert-success"))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Success!</h4>
            {{session("alert-success")}}
        </div>
    @endif
    @if($flash = session("alert-danger"))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Errors!</h4>
            {{session("alert-danger")}}
        </div>
    @endif

  <div class="box-body">
    <div id='resending' hidden>
        <h4>Resending data to DHIS2. Please wait...</h4>
    </div>

    <div id='progress' class="progress" hidden>
       
        <div id="dynamic" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
            <span id="current-progress"></span>
        </div>
    </div>


    <table id="table1" class="table table-bordered table-striped" style="width:100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>UID</th>
          <th>Facility Name</th>
          <th>Facility </th>
          <th>Ownership</th>
          <th>Level of Care </th>
          <th>Level of Care Option</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        
        @foreach($logs as $log)
        <tr>
          <td>{{ $log->id }}</td>
          <td>
            @if($log->facility_status =='Failed' or $log->ownership_status == 'Failed' or $log->level_status == 'Failed' or $log->level_option_status == 'Failed' )
                <font color="red">  {{$log->facility_id}}</font>
            @else
                {{$log->facility_id}}
            @endif
          </td>
          <td>
            @if($log->facility_status =='Failed' or $log->ownership_status == 'Failed' or $log->level_status == 'Failed' or $log->level_option_status == 'Failed' )
                <font color="red">  {{$log->facility_name}}</font>
            @else
                {{$log->facility_name}}
            @endif
          </td>
          <td>
              @if ($log->facility_status == 'Failed')
              <font color="red">  {{$log->facility_status}}</font>
              @else
                {{$log->facility_status}}
              @endif
            
          </td>
          <td>
              @if ($log->ownership_status == 'Failed')
              <font color="red">  {{$log->ownership_status}}</font>
              @else
                {{$log->ownership_status}}
              @endif
            
          </td>
          <td>
              @if ($log->level_status == 'Failed')
              <font color="red">  {{$log->level_status}}</font>
              @else
                {{$log->level_status}}
              @endif
            
          </td>
          <td>
              @if ($log->level_option_status == 'Failed')
              <font color="red">  {{$log->level_option_status}}</font>
              @else
                {{$log->level_option_status}}
              @endif
            
          </td>
          
          <td>
                <form class="form-horizontal"  action="{{route('dhis.resend')}}" method="post">

                    <a href="#">
                        <button class="btn btn-success btn-sm"  type="button" data-toggle="modal" data-target="#view_details"
                            data-id="{{$log->id}}" data-facility_code="{{$log->facility_code}}"  data-start_date="{{$log->start_date}}" data-close_date="{{$log->close_date}}"
                            data-facility_name="{{$log->facility_name}}" data-alt_facility_name="{{$log->alt_facility_name}}" data-state="{{$log->state}}"
                            data-lga="{{$log->lga}}" data-ward="{{$log->ward}}" data-ownership="{{$log->ownership}}" 
                            data-facility_level="{{$log->facility_level}}" data-facility_level_option="{{$log->facility_level_option}}"
                            data-longitude="{{$log->longitude}}" data-latitude="{{$log->latitude}}"
                            data-postal_address="{{$log->postal_address}}" data-phone_number="{{$log->phone_number}}" data-email_address="{{$log->email_address}}"
                            data-website="{{$log->website}}"   data-by="{{ $log->firstname.' '.$log->lastname }}"  data-date="{{Carbon\Carbon::parse($log->created_at)->toFormattedDateString()}}"  >
                            More
                        </button>  
                    </a> 
                    @if($log->error_details != '')
                        <a href="#">
                            <button class="btn btn-danger btn-sm"  type="button" data-toggle="modal" data-target="#view_error"
                                data-error="{{ (string)$log->error_details }}" >
                                Error
                            </button>  
                        </a> 
                    @endif

                    @if($log->facility_status =='Failed' or $log->ownership_status == 'Failed' or $log->level_status == 'Failed' or $log->level_option_status == 'Failed' )
                            @csrf
                            <input type="hidden" name="facility_status" value={{$log->facility_status}}>
                            <input type="hidden" name="ownership_status" value={{$log->ownership_status}}>
                            <input type="hidden" name="level_status" value={{$log->level_status}}>
                            <input type="hidden" name="level_option_status" value={{$log->level_option_status}}>
                            <input type="hidden" name="facility_id" value={{$log->facility_id}}>
                            <input type="hidden" name="log_id" value={{$log->id}}>
                            <input type="hidden" name="request_type" value={{$log->request_type}}>
                            <input type="hidden" name="dhis_uid" value={{$log->dhis_uid}}>

                            
                            <button type="submit" class="btn btn-primary btn-sm resend_data">Resend</button>
                    @endif
                </form>

          </td>
        </tr>
        @endforeach
      </tbody>

      <tfoot>
    
      </tfoot>
    </table>
    
    
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->

{{-- modal facility details --}}
<div class="modal fade" id="view_details" tabindex="-1" role="dialog">
  <div class="modal-dialog " role="document">
      <div class="modal-content">
     
          <div class="modal-body">
              
              <div class="panel-body">
                  
                  <div class="panel-group" id="accordion">
                      {{-- panel one --}}
                      <div class="panel panel-default">
                          <div class="panel-heading">
                              <h4 class="panel-title">
                                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Facility Details</a>
                              </h4>
                          </div>
                          <div id="collapse1" class="panel-collapse collapse in">
                              <div class="panel-body">
                                  {{-- <div class="row">
                                      <label class="col-md-4">Facility Code:</label>
                                      <div class="col-md-8" id="facility_code"></div>
                                  </div> --}}
                               
                             
                                  <div class="row">
                                      <label class="col-md-4 text-md-right">Facility Name:</label>
                                      <div class="col-md-8" id="facility_name">    </div>
                                  </div>
                                  <div class="row">
                                      <label class="col-md-4 text-md-right">Alternate Name:</label>
                                      <div class="col-md-8" id="alt_facility_name">    </div>
                                  </div>
                                  <div class="row">
                                    <label class="col-md-4 text-md-right">Start Date:</label>
                                    <div class="col-md-8" id="start_date">    </div>
                                </div>
                                <div class="row">
                                    <label class="col-md-4 text-md-right">Close Date:</label>
                                    <div class="col-md-8" id="close_date">    </div>
                                </div>
                                  <div class="row">
                                    <label class="col-md-4">State:</label>
                                    <div class="col-md-8" id="state"></div>
                                </div>
                                <div class="row">
                                    <label class="col-md-4">LGA:</label>
                                    <div class="col-md-8" id="lga"></div>
                                </div>
                                <div class="row">
                                    <label class="col-md-4">Ward:</label>
                                    <div class="col-md-8" id="ward"></div>
                                </div>
                               
                                  <div class="row">
                                      <label class="col-md-4 text-md-right">Ownership:</label>
                                      <div class="col-md-8" id="ownership">    </div>
                                  </div>
                                  <div class="row">
                                    <label class="col-md-4 text-md-right"> Level of Care:</label>
                                    <div class="col-md-8" id="facility_level">    </div>
                                </div>
                                <div class="row">
                                    <label class="col-md-4 text-md-right">Level of Care Option:</label>
                                    <div class="col-md-8" id="facility_level_option">    </div>
                                </div>
                                  <div class="row">
                                    <label class="col-md-4">Postal Address:</label>
                                    <div class="col-md-8" id="postal_address"></div>
                                  </div>
                                  <div class="row">
                                      <label class="col-md-4">Longitude:</label>
                                      <div class="col-md-8" id="longitude"></div>
                                  </div>
                                  <div class="row">
                                      <label class="col-md-4">Latitude:</label>
                                      <div class="col-md-8" id="latitude"></div>
                                  </div>
                                  <div class="row">
                                    <label class="col-md-4">Phone Number:</label>
                                    <div class="col-md-8" id="phone_number"></div>
                                </div>
                            
                                <div class="row">
                                    <label class="col-md-4">Email Address:</label>
                                    <div class="col-md-8" id="email_address"></div>
                                </div>
                                <div class="row">
                                    <label class="col-md-4">Website:</label>
                                    <div class="col-md-8" id="website"></div>
                                </div>
                                <div class="row">
                                    <label class="col-md-4">Published By:</label>
                                    <div class="col-md-8" id="by"></div>
                                </div>
                                <div class="row">
                                    <label class="col-md-4">Published Date:</label>
                                    <div class="col-md-8" id="at"></div>
                                </div>
                                
                              </div>
                          </div>
                      </div>
             
              

                      
                  </div> 
                  
                  
              </div>
              
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
          </div><!-- /.modal-content -->
      </div><!--/.modal-dialog -->
  </div>
</div><!--/.modal -->

{{-- modal error details--}}
<div class="modal fade" id="view_error" tabindex="-1" role="dialog">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
       
            <div class="modal-body">
                
                <div class="panel-body">
                    
                    <div class="panel-group" id="accordion">
                        {{-- panel one --}}
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Error Details</a>
                                </h4>
                            </div>
                            <div id="collapse1" class="panel-collapse collapse in">
                                <div class="panel-body ;" >
                      
                                    <div class="row" >
                                        <div class="col-md-12" id="error">    </div>
                                    </div>
                          
                                  
                                </div>
                            </div>
                        </div>             
                    </div>              
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!--/.modal-dialog -->
    </div>
  </div><!--/.modal -->
  

@endsection 


@push("bk_script")
<script>
  $(document).ready( function () {

      $('#table1').DataTable( {
        "paging":   true,
        "ordering": true,
        "info":     true,
        responsive: true,
        "order": [[ 0, "desc" ]],
        "columnDefs": [
              {
                  "targets": [ 0 ],
                  "visible": false
              }
          ]

      } );

      $('#view_details').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var modal = $(this)
  
            modal.find('.modal-body #facility_code').text(button.data('facility_code'));
            modal.find('.modal-body #start_date').text(button.data('start_date'));
            modal.find('.modal-body #close_date').text(button.data('close_date'));
            modal.find('.modal-body #facility_name').text(button.data('facility_name'));
            modal.find('.modal-body #alt_facility_name').text(button.data('alt_facility_name'));
            modal.find('.modal-body #state').text(button.data('state'));
            modal.find('.modal-body #lga').text(button.data('lga'));
            modal.find('.modal-body #ward').text(button.data('ward'));
            modal.find('.modal-body #ownership').text(button.data('ownership'));
            modal.find('.modal-body #ownership_type').text(button.data('ownership_type'));
            modal.find('.modal-body #facility_level').text(button.data('facility_level'));
            modal.find('.modal-body #facility_level_option').text(button.data('facility_level_option'));
            modal.find('.modal-body #longitude').text(button.data('longitude'));
            modal.find('.modal-body #latitude').text(button.data('latitude'));
            modal.find('.modal-body #postal_address').text(button.data('postal_address'));
            modal.find('.modal-body #phone_number').text(button.data('phone_number'));
            modal.find('.modal-body #email_address').text(button.data('email_address'));
            modal.find('.modal-body #website').text(button.data('website'));
            modal.find('.modal-body #by').text(button.data('by'));
            modal.find('.modal-body #at').text(button.data('date'));

         
       
      });//end

        
      
      $('#view_error').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            var error = button.data('error');

            modal.find('.modal-body #error').text(JSON.stringify(error));
       
      });//end


     var current_progress = 0;

    $(".resend_data").click(function(){
        $("#resending").show();
        $("#progress").show();

        var interval = setInterval(function() {
              current_progress += 5;
              $("#dynamic")
              .css("width", current_progress + "%")
              .attr("aria-valuenow", current_progress)
              .text(current_progress + "%");
              if (current_progress >= 100)
                  clearInterval(interval);
          }, 2000);
    });

  } );
</script>
@endpush