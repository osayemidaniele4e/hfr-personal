<script src="{{ asset("dist/notify/bootstrap-notify.min.js")}}"></script>
<link rel="stylesheet" href="{{asset("/dist/notify/animate.min.css")}}">

<script>
    
    @if($flash = session("alert-success"))
        $.notify({
            message: '{{session("alert-success")}}'
        },{
            type:'success',
            delay: 1000,
             offset:{
                    y:60,
                    x:20
            },
        });
    
    @endif
    

    
</script>