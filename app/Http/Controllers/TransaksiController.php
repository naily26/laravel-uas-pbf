<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
class TransaksiController extends Controller
{

    private $database;

    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();
    }

    public function get(){
        $transaksi = $this->database->getReference('transaksi')->getValue();
       // $data = $this->database->getReference('gudang')->getValue();
        $arr = [];
        if($transaksi != null){
            foreach ($transaksi as $tr => $trItem) {
                $warehouse = $this->detailGudang($trItem['warehouse_id']);
                $material = $this->detailMaterial($trItem['material_id']);
                $user = $this->detailUser($trItem['user_id']);
                $trId = $trItem['id'];
                if($material != null || !is_null($material))
                {
                    $arr[$trId]['material_name'] = $material['name'];
                }else{
                    $arr[$trId]['material_name'] = 'Data was deleted!';
                }

                if($warehouse != null || !is_null($warehouse))
                {
                    $arr[$trId]['warehouse_name'] = $warehouse['name'];
                }else{
                    $arr[$trId]['warehouse_name'] = 'Data was deleted!';
                }

                if($user != null || !is_null($user))
                {
                    $arr[$trId]['user_name'] = $user['name'];
                }else{
                    $arr[$trId]['user_name'] = 'Data was deleted!';
                }

                 $arr[$trId]['date_transaction'] = $trItem['date_transaction']; 
            }
        }
       if($transaksi != null){
            return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "data" => $arr]
                );
        }else{
            return response()->json(
                [
                    "status" => "failed"
                    , "success" =>false
                    , "message" => "data not available"]
                );
        }
    }


    public function insert(Request $request)
    {
        $warehouse = $this->detailGudang($request->warehouse_id);
        $transaksi = $this->detailTransaksi();
        $maxStok = $request->qty;
        if($transaksi != null){
            foreach ($transaksi as $key => $value) {
                if($request->warehouse_id == $value['warehouse_id']){
                    $maxStok += $value['qty'];
                }
            }
        }


        if($maxStok > $warehouse['max_capacity']){
            return response()->json(
                [
                    "status" => "failed"
                    , "success" =>false
                    , "message" => "Warehouse quota full maximum"]
                );
        }

        $unique = strtotime(date('Y-m-d H:i:s'));
        $this->database
        ->getReference('transaksi/' . $unique)
        ->set([
            'id'=>$unique,
            'user_id' => $request->user_id,
            'material_id' => $request->material_id,
            'warehouse_id' => $request->warehouse_id,
            'qty' => $request->qty,
            'date_transaction'=>Carbon::now()->format('Y-m-d')
        ]);
        $material = $this->detailMaterial($request->material_id);
        $fixStok = $material['stock'] + $request->qty;
         $this->database
        ->getReference('material/' . $material['id'])
        ->update([
            'stock'=>$fixStok
        ]);
        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'transaction has been added']
                );
    }

    public function detailGudang($id)
    {
        $data = $this->database->getReference('gudang')->getValue();
        return $data[$id];
    }

    public function detailMaterial($id)
    {
        $data = $this->database->getReference('material')->getValue();
        return $data[$id];
    }

    public function detailUser($id)
    {
        $data = $this->database->getReference('user')->getValue();
        return $data[$id];
    }

    public function detailTransaksi()
    {
        $data = $this->database->getReference('transaksi')->getValue();
        return $data;
    }

}
