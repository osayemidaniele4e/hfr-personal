@extends("layouts.master")

@section('content-title')
HFR-DHIS2 Exchange



@endsection

@section("content")
<div class="box">
  <div class="box-body">
    {{-- <div class="alert alert-success alert-dismissible">
        <h4><i class="icon fa fa-check"></i>   {{ $message }}</h4>
    </div> --}}
   
    <div id='updating'>
        <h4>Sending Updates to DHIS2. Please wait...</h4>
    </div>

    <div id='progress' class="progress">
      <div id="dynamic" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
        <span id="current-progress"></span>
      </div>
    </div>
    <div id='fac_error' class="alert alert-danger alert-dismissible" hidden>
        <h4><i class="icon fa fa-warning"></i> Error!</h4>
        Something went wrong while updating DHIS2. Please, check logs for details!
    </div>
    <div id='fac_success' class="alert alert-success alert-dismissible" hidden>
        <h4><i class="icon fa fa-check"></i> Success!</h4>
        Facility was successfully updated in DHIS2!
    </div>

  </div>
  <!-- /.box-body -->
  <div class="box-footer" id='btfooter'hidden>
      <a href="{{route('dhis.logs')}}">
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
    //   $("#fac_error").hide();
    //   $("#fac_success").hide();


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

      var data =  @json($data);
      var id = '{{ $id }}';
      var _token = $('input[name="_token"]').val();

      $.ajax({
          url:"{{route('dhis.update')}}",
          method:"POST",
          data:{data:data, id:id, _token:_token},
          success:function(result)
          {
              if (result =='Updated'){
                  $("#fac_success").show();
                  $("#btfooter").show();
                  $("#progress").hide();
                  $('#updating').hide();
                  $("#fac_error").hide();
              }else{
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