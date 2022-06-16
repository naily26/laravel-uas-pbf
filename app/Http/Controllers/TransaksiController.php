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
        $transaksi = $this->database->getReference('peminjaman')->getValue();
       // $data = $this->database->getReference('gudang')->getValue();
        $arr = [];
        if($transaksi != null){
            $no = 0;
            foreach ($transaksi as $tr => $trItem) {
                $barang = $this->detailMaterial($trItem['barang_id']);
                $user = $this->detailUser($trItem['user_id']);
                $trId = $trItem['id'];
                $arr[$no]['id'] = $trId;
                if($barang != null || !is_null($barang))
                {
                    $arr[$trId]['nama_barang'] = $barang['name'];
                }else{
                    $arr[$trId]['nama_barang'] = 'Data was deleted!';
                }


                if($user != null || !is_null($user))
                {
                    $arr[$trId]['nama_pengguna'] = $user['name'];
                }else{
                    $arr[$trId]['nama_pengguna'] = 'Data was deleted!';
                }

                 $arr[$trId]['date_transaction'] = $trItem['date_transaction']; 
                 $arr[$no]['start_date'] = $trItem['start_date']; 
                 $arr[$no]['end_date'] = $trItem['end_date']; 
                 $arr[$no]['grandtotal'] = $trItem['grandtotal']; 
                 $arr[$no]['hari'] = $trItem['hari']; 
                 $no++;
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



    public function detailMaterial($id)
    {
        $data = $this->database->getReference('barang')->getValue();
        return $data[$id];
    }

    public function detailUser($id)
    {
        $data = $this->database->getReference('user')->getValue();
        return $data[$id];
    }


}
