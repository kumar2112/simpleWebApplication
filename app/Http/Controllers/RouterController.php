<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

Use App\Routers;
class RouterController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
   */
    public function __construct()
    {
        //$this->middleware('auth');
    }
    /*
    * @return list router view
    *
    */
    public function listEmployee(Request $request){
        $routers=new Routers;
        if($request->ajax()){
             try{
                   $columns = array(
                                   array( 'db' => 'sap_id', 'dt' => 'sap_id' ),
                                   array( 'db' => 'internet_host_name',  'dt' => 'internet_host_name' ),
                                   array( 'db' => 'client_ip_address',   'dt' => 'client_ip_address' ),
                                   array( 'db' => 'mac_address',     'dt' => 'mac_address' ),
                                   array( 'db' => 'action',     'dt' => 'action' )
                              );
                   $routers=$routers;
                   $recordsTotal=$routers->count();
                   if(isset($request->start) && $request->length != -1 ) {
                       $routers=$routers->offset(intval($request->start));
                       $routers=$routers->limit(intval($request->length));
                   }

                   $recordsFiltered=$recordsTotal;
                   if ( isset($request->search) && $request->search['value'] != '' ) {
                       $globalSearch = array();
                       $columnSearch = array();

                       $str = $request->search['value'];

                       $dtColumns = $this->pluck( $columns, 'dt' );

                       for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
                         $requestColumn = $request['columns'][$i];
                         $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                         $column = $columns[ $columnIdx ];
                         if ($requestColumn['searchable'] == 'true' ) {
                             if($column['db']!='action'){
                               //$whereData[]=[$column['db'],'LIKE',"%{$str}%"];
                               $routers=$routers->orWhere($column['db'],'LIKE',"%{$str}%");
                               //$routers->orwhere($column['db'],'LIKE',"%{$str}%");
                             }

                         }
                       }
                       if($routers){
                         $recordsFiltered=$routers->count('id');
                       }
                   }
                   $routersArray=array();

                   $data=$routers->get();


                   foreach($data as $d){
                         $action="";

                         $action.='<a href="'.route("router.edit",array('id'=>rtrim(base64_encode($d->id),'=='))).'"><i class="fa fa-edit">Edit</i></a>';
                         $action.='<b> | </b>';
                         if($d->is_deleted==1){
                             $action.='<a class="deleteRouter" href="'.route("router.delete",array('id'=>rtrim(base64_encode($d->id),'=='))).'" ><i class="fa fa-trash"></i>Deactivate</a>';
                         }else if($d->is_deleted==0){
                             $action.='<a class="deleteRouter" href="'.route("router.delete",array('id'=>rtrim(base64_encode($d->id),'=='))).'" ><i class="fa fa-trash">Activate</i></a>';
                         }


                       $routersArray[]= [  "id" => "<strong>#".$d->id."</strong>",
                                           "sap_id" => $d->sap_id,
                                           "internet_host_name"=>$d->internet_host_name,
                                           "client_ip_address"=> $d->client_ip_address,
                                           "mac_address"=> $d->mac_address,
                                           "action" =>$action
                                         ];
                   }

                   $response["draw"]= isset ( $request['draw'] ) ?intval( $request['draw'] ) :0;
                   $response["recordsTotal"]=intval($recordsTotal);
                   $response['recordsFiltered']=intval( $recordsFiltered);
                   $response["data"]=$routersArray;

                   //$log=['data'=>$request->all(),'user'=>Session::get('useremail'),'status'=>"success"];
                   //$this->logger->addRecord(400, json_encode($request->all()),$log);
                   return $response;
             }catch(Exception $ex){
                //echo $ex->toString();
             }
       }else{
         return view('routers.list');
       }
    }
    /**
       *
       * @private function
       * @param array && prop
       * @return array
    */
    private function pluck ( $a, $prop ){
        $out = array();
        for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
          $out[] = $a[$i][$prop];
        }
        return $out;
    }
     /*
     * @return create new router view
     *
     */
     public function createRouter(){
        //$Companies=Companies::all();
        return view('routers.create');
     }
     /*
     * @param Request $request
     * @return void
     *
     */
     public function storeRouter(Request $request){
          $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
          $mac_address_regex='/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/';

          $validator = Validator::make($request->all(),[
                           'txtDnsRecord' => 'required|min:18|alpha_num',
                           'txtInternetHostName' => 'required|min:14|regex:'.$regex,
                           'txtClientIpAddress' => 'required|ip',
                           'txtMacAddress' => 'required|min:18|regex:'.$mac_address_regex,
                        ]);
          if ($validator->fails()) {
            return response()->json(['status'=>'errors', 'message'=>$validator->errors()]);
            // return redirect()->route('employee.create')
            //             ->withErrors($validator)
            //             ->withInput();
          }else{
              $routerCount=Routers::where('client_ip_address','=',trim($request->txtClientIpAddress))->count();
              if($routerCount>0){
                 return response()->json(['status'=>'error', 'message'=>'Ip address should be unique.']);
              }
              $Routers=new Routers();
              $Routers->sap_id=$request->txtDnsRecord;
              $Routers->internet_host_name=$request->txtInternetHostName;
              $Routers->client_ip_address=$request->txtClientIpAddress;
              $Routers->mac_address=$request->txtClientIpAddress;
              $Routers->save();
              return response()->json(['status'=>'success', 'message'=>'Router added successfully.']);

         }

     }
     /*
     * @param $id
     * @return edit router view
     *
     */
     public function editRouter($id){

        $id=$id."==";
        $routerId=base64_decode(trim($id));
        $router=Routers::where('id','=',$routerId)->get()->first();

        if(empty($router)){
           abort(404);
        }

        return view('routers.edit',compact('router'));
     }

     /*
     * @param Request $request
     * @return void
     *
     */
     public function updateRouter(Request $request){
       $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
       $validator = Validator::make($request->all(),[
                        'txtDnsRecord' => 'required|alpha_num',
                        'txtInternetHostName' => 'required|regex:'.$regex,
                        'txtClientIpAddress' => 'required|ip',
                        'txtMacAddress' => 'required',
                     ]);
         if ($validator->fails()) {
           return response()->json(['status'=>'errors', 'message'=>$validator->errors()]);
           // return redirect()->route('employee.create')
           //             ->withErrors($validator)
           //             ->withInput();
         }else{
            try{
                $id=trim($request->txtHiddenRouterId);
                $id=$id."==";
                $routerId=base64_decode(trim($id));
                $routerCount=Routers::where('client_ip_address','=',trim($request->txtClientIpAddress))->where('id','<>',$routerId)->count();
                //$routerCount=Routers::where('client_ip_address','=',trim($request->txtClientIpAddress))->count();
                if($routerCount>0){
                   return response()->json(['status'=>'error', 'message'=>'Ip address should be unique.']);
                }
                $router=Routers::where('id','=',$routerId)->where('is_deleted','=','0')->get()->first();
                if(empty($router)){
                   return response()->json(['status'=>'error', 'message'=>'something went wrong.']);
                }
                $router->sap_id=$request->txtDnsRecord;
                $router->internet_host_name=$request->txtInternetHostName;
                $router->client_ip_address=$request->txtClientIpAddress;
                $router->mac_address=$request->txtMacAddress;
                $router->save();
                return response()->json(['status'=>'success', 'message'=>'Router updated successfully.']);
            }catch(\Illuminate\Database\QueryException $ex){

                 abort(404);
            }
         }

     }

     /*
     * @param $id
     * @return void
     *
     */
     public function deleteRouter($id){
         try{
             $id=trim($id);
             $id=$id."==";
             $routerId=base64_decode(trim($id));
             $routers=Routers::where('id','=',$routerId)->get()->first();
             if(empty($routers)){
                return response()->json(['status'=>'error', 'message'=>'Something went wrong.']);
             }
             $rstatus="";
             if($routers->is_deleted==1){
                $routers->is_deleted='0';
                $msg="Router deactivated successfully.";
                $rstatus="Activate";
             }else if($routers->is_deleted==0){
                $routers->is_deleted='1';
                $msg="Router activated successfully.";
                $rstatus="Deactivate";
             }
             $routers->save();
             return response()->json(['status'=>'success','message'=>$msg,'rstatus'=>$rstatus]);
        }catch(\Illuminate\Database\QueryException $ex){
             abort(404);
        }
     }
}
