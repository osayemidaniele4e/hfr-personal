<link rel="stylesheet" href="{{asset("/dist/notify/animate.min.css")}}">
<script src="{{ asset("dist/notify/bootstrap-notify.min.js")}}"></script>

<script>
    
    @if($flash = session("alert-success"))
        $.notify({
            message: '{{session("alert-success")}}'
        },{
            type:'success',
            delay: 5000,
            // animate: {
            //         enter: 'animated fadeInRight',
		    //         exit: 'animated fadeOutRight'
            //  },
             offset:{
                    y:60,
                    x:20
            },
        });
    
    @endif
    
    
    @if($flash = session("alert-danger"))
        $.notify({
            message: '{{session("alert-danger")}}'
        },{
            type:'danger',
            delay: 5000,
            // animate: {
            //         enter: 'animated fadeInRight',
		    //         exit: 'animated fadeOutRight'
            //  },
             offset:{
                    y:60,
                    x:20
            },
        });
    @endif
    
    
    @if($errors->any())  
    var delay = 5000; 
        @foreach($errors->all() as $error)   
            $.notify({
                message: '{{$error}}'
            },{
                type: 'danger',
                allow_dismiss: true,
	            newest_on_top: false,
                spacing: 5,
                delay: delay,
	            timer: 1000,
                // animate: {
                //     enter: 'animated fadeInRight',
		        //     exit: 'animated fadeOutRight'
                // },
                offset:{
                    y:60,
                    x:20
                },
            });

            delay=delay+1000;
        @endforeach
    @endif
    
</script>