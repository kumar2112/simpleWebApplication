@extends('layouts.app')
@section('content')
<div class="container">
  <div id="alert-message"></div>
  <div class="row">
      <div class="col-md-12 text-right">
          <a href="{{route('router.create')}}">Add Router</a>
      </div>
  </div>
  <div class="row">
      <div class="col-md-12">
        <table class="table table-striped" id="routerTable">
            <thead>
              <tr>
                  <th>#ID</th>
                  <th>Sap Id</th>
                  <th>Host Name</th>
                  <th>Client Ip Address</th>
                  <th>Mac Address</th>
                  <th>Action</th>
              </tr>
            </thead>
         </table>
      </div>
  </div>
</div>
<script>

     $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'none';
        var routerTable=$('#routerTable').DataTable( {
                                  "serverSide": true,
                                  "ordering": false,
                                  "info" :true,
                                  "pageLength": 2,
                                  "bFilter":   true,
                                  "ajax": {
                                      "url": "<?php echo url('/routers') ?>",
                                      'headers': {
                                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                      },
                                      "type": "POST"
                                  },
                                  "columns": [
                                                { "data": "id" },
                                                { "data": "sap_id" },
                                                { "data": "internet_host_name" },
                                                { "data": "client_ip_address" },
                                                { "data": "mac_address"},
                                                { "data": "action" }
                                              ],
                            });
        $('#routerTable thead th').each( function () {
            var title = $('#routerTable thead th').eq( $(this).index() ).text();
            //console.log(title);
            if(title!='#ID' && title!='Action'){
                $(this).append( '<input type="text" placeholder="Search '+title+'" />' );
            }
        });
        routerTable.columns().every( function () {
           var that = this;

           $( 'input', this.header() ).on( 'keyup change', function () {

               if ( that.search() !== this.value ) {
                  console.log('keypu')
                   that
                       .column()
                       .search( this.value )
                       .draw();
               }
           });
       });
    });

    $(document).ready(function(){
        //var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        //$(".deleteRouter").on("click", function(){
        $('body').on('click', '.deleteRouter', function() {
            var elm=$(this);
            $.ajax({
                /* the route pointing to the post function */
                url: $(this).attr('href'),
                type: 'GET',
                /* send the csrf-token and the input to the controller */
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function (data) {
                    if(data.status=="success"){
                         elm.text(data.rstatus);
                        $("#alert-message").html('<div class="alert alert-success">'+data.message+'</div>');
                        //form.reset();
                    }else if(data.status=="error"){
                        $("#alert-message").html('<div class="alert alert-danger">'+data.message+'</div>');
                    }
                }
            });
            return false;
        });
   });
</script>
@stop
