<!-- Modal delete record -->
<div class="modal fade" id="deleteUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
           
          <form action="{{route('deleteUser')}}" method="POST">
              @csrf
              @method("PUT")

              <div class="modal-body">
                  <p class="text-center" id = "message_del">
                    
                  </p>
                    <input type="hidden" id="user" name="user" value="">  

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No</button>
                <button type="submit" class="btn btn-warning btn-sm">Yes</button>
              </div>
            </form>
            
          </div>
        </div>
 </div> 
      