@extends('layouts.app')
@section('content')
<div class="container">
  <div class="row">
      <div class="col-md-12 text-right">
          <a href="{{route('router.list')}}">All Routers</a>
      </div>
  </div>
  <div class="row">
      <div class="col-md-12">
          <div id="alert-message"></div>
          <form action="{{route('router.update')}}" method="post"  id="updateRouterDataForm">
             @csrf
             <input type="hidden" name="txtHiddenRouterId" id="txtHiddenRouterId" value="{{rtrim(base64_encode($router->id),'==')}}">
             <div class="form-group">
                <label for="txtDnsRecord">Sap Id</label>
                <input maxlength="50" value="{{$router->sap_id}}" type="text" class="form-control" id="txtDnsRecord" name="txtDnsRecord" aria-describedby="txtDnsRecord" placeholder="No Of DNS Record">
                @if($errors->has('txtDnsRecord'))
                  <span class="text-danger">{{$errors->first('txtDnsRecord')}}</span>
                @endif
              </div>
              <div class="form-group">
                 <label for="txtInternetHostName">Internet Host Name</label>
                 <input maxlength="100" value="{{$router->internet_host_name}}" type="text" class="form-control" id="txtInternetHostName" name="txtInternetHostName" aria-describedby="EmployeeNameHelp" placeholder="Host Name">
                 @if($errors->has('txtInternetHostName'))
                   <span class="text-danger">{{$errors->first('txtInternetHostName')}}</span>
                 @endif
              </div>
              <div class="form-group">
                <label for="txtClientIpAddress">Client Ip Address</label>
                <input maxlength="100" value="{{$router->client_ip_address}}" type="text" class="form-control" id="txtClientIpAddress" name="txtClientIpAddress" placeholder="Ip Address">
                @if($errors->has('txtClientIpAddress'))
                  <span class="text-danger">{{$errors->first('txtClientIpAddress')}}</span>
                @endif
              </div>
              <div class="form-group">
                <label for="txtMacAddress">Mac Address</label>
                <input maxlength="100" value="{{$router->mac_address}}" type="text" class="form-control" id="txtMacAddress" name="txtMacAddress" placeholder="Mac Address">
                @if($errors->has('txtMacAddress'))
                  <span class="text-danger">{{$errors->first('txtMacAddress')}}</span>
                @endif
              </div>
              {{ csrf_field() }}
              <button type="button" class="btn btn-primary postRouterData">Submit</button>
          </form>
    </div>
  </div>
</div>
<script>
      $(document).ready(function(){
          //var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
          $(".postRouterData").click(function(){
              var form = $("#updateRouterDataForm");
              $.ajax({
                  /* the route pointing to the post function */
                  url: $("#updateRouterDataForm").attr('action'),
                  type: 'POST',
                  /* send the csrf-token and the input to the controller */
                  data: form.serialize(),
                  dataType: 'JSON',
                  /* remind that 'data' is the response of the AjaxController */
                  success: function (data) {
                      if(data.status=="success"){
                          $("#alert-message").html('<div class="alert alert-success">'+data.message+'</div>');
                          //form[0].reset();
                      }else if(data.status=="error"){
                          $("#alert-message").html('<div class="alert alert-danger">'+data.message+'</div>');
                      }else if(data.status=="errors"){
                          $.each(data.message, function( index, value ) {
                               $("#"+index).parent().append('<span class="text-danger">'+value+'</span>');
                          });
                      }
                  }
              });
              return false;
          });
          $(".form-control").focus(function(){
              $(this).parent().find('.text-danger').remove();
          });
     });
</script>
@stop
