
@if($errors->any())   
   <div class="alert alert-danger alert-dismissible">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
       <h4><i class="icon fa fa-ban"></i> Alert!</h4>
       
       @foreach($errors->all() as $error)
            <div>
                {{$error}}
            </div>
       @endforeach
   </div>
@endif

@if($flash = session("alert-success"))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> Alert!</h4>
        {{session("alert-success")}}
    </div>
@endif

@if($flash = session("alert-danger"))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> Data Errors!</h4>
        {{session("alert-danger")}}
    </div>
@endif

