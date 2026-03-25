@extends("layouts.master")

@section('content-title')
HFR-DHIS2 Exchange



@endsection

@section("content")
<div class="box">
  <div class="box-body">

   
    <div id='updating'>
        <h4>Creating facility in DHIS2. Please wait...</h4>
    </div>

    <div id='progress' class="progress">
      <div id="dynamic" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
        <span id="current-progress"></span>
      </div>
    </div>
    
    <div id='fac_error' class="alert alert-danger alert-dismissible" hidden>
        <h4><i class="icon fa fa-warning"></i> Error!</h4>
        Something went wrong while creating facility in DHIS2. Please, check logs for details!
    </div>
    <div id='fac_success' class="alert alert-success alert-dismissible" hidden>
        <h4><i class="icon fa fa-check"></i> Success!</h4>
        Facility was successfully created in DHIS2!
    </div>

  </div>
  <!-- /.box-body -->
  <div class="box-footer">
      <a href="{{route('dhis.logs')}}" id='btfooter' hidden>
          <button type="button" class="btn btn-primary">View Logs</button>
      </a>
  </div>
</div>
<!-- /.box -->
@endsection 



@push('bk_script')

@include('partials.notification')

<script>


$(document).ready(function() {
      $("#fac_error").hide();
      $("#fac_success").hide();


      var current_progress = 0;
      $(document).ajaxStart(function(){
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

      var id = '{{ $hosp->id }}';
      var state_id = '{{ $hosp->state_id }}';
      var ward_id = '{{ $hosp->ward_id }}';
      var facility_name ='{{ $hosp->facility_name}}';
      var alt_facility_name = '{{ (string)$hosp->alt_facility_name }}';
      var start_date = '{{ $hosp->start_date }}';
      var close_date = '{{ $hosp->close_date }}';
      var postal_address = '{{ $hosp->postal_address }}';
      var email_address = '{{ $hosp->email_address }}';
      var operational_status_id = '{{ $hosp->operational_status_id }}';
      var website = '{{ $hosp->website }}';
      var longitude = '{{ $hosp->longitude }}';
      var latitude = '{{ $hosp->latitude }}';
      var phone_number = '{{ $hosp->phone_number }}';
      var ownership_id = '{{ $hosp->ownership_id }}';
      var facility_level_id = '{{ $hosp->facility_level_id }}';
      var facility_level_option_id = '{{ $hosp->facility_level_option_id }}';
      var _token = $('input[name="_token"]').val();
      
      $.ajax({
          url:"{{route('dhis.store')}}",
          method:"POST",
          data:{state_id:state_id, ward_id:ward_id, facility_name:facility_name, start_date:start_date, close_date:close_date, postal_address:postal_address,
                email_address:email_address, website:website,longitude:longitude,  latitude:latitude, id:id, operational_status_id:operational_status_id,
                alt_facility_name:alt_facility_name,  phone_number:phone_number,ownership_id:ownership_id,
                facility_level_option_id:facility_level_option_id, facility_level_id:facility_level_id, _token:_token},
          success:function(result)
          {
              if (result =='Created'){
                  $("#fac_success").show();
                  $("#btfooter").show();
                  $("#progress").hide();
                  $('#updating').hide();
                  $("#fac_error").hide();
              }
              else{
                  $("#fac_error").show();
                  $("#btfooter").show();
                  $("#progress").hide();
                  $('#updating').hide();
                  $("#fac_success").hide();
              }
          }         
      })   

  
      $(document).ajaxStop(function(){
          current_progress = 95;
          $("#dynamic")
                .css("width", current_progress + "%")
                .attr("aria-valuenow", current_progress)
                .text(current_progress + "%");
          $("#progress").hide();
          $('#updating').hide();
            
      });
      

				
});



</script>

@endpush