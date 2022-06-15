<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
class peminjamanController extends Controller
{

    private $database;

    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();
    }

    public function get(){
       $data = $this->database->getReference('peminjaman')->getValue();
       $arr = [];
        if($transaksi != null){
            foreach ($transaksi as $tr => $trItem) {
                $barang = $this->detailBarang($trItem['barang_id']);
                $trId = $trItem['id'];
                if($barang != null || !is_null($barang))
                {
                    $arr[$trId]['nama_barang'] = $barang['name'];
                }else{
                    $arr[$trId]['nama_barang'] = 'Data was deleted!';
                }

                 $arr[$trId]['start_date'] = $trItem['start_date']; 
                 $arr[$trId]['end_date'] = $trItem['end_date']; 
            }
        }
        if($data != null){
            return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "data" => $data]
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
        $barang = $this->detailBarang($request->barang_id);
        $fixStok = 0;
        if($barang != null){
            $fixStok = $barang['stock'] - 1;
            if($fixStok < 0){
                return response()->json(
                    [
                        "status" => "failed"
                        , "success" =>false
                        , "message" => "peminjaman gagal stock barang tidak cukup"]
                    );
            }
        }

        $unique = strtotime(date('Y-m-d H:i:s'));
        
        $this->database
        ->getReference('peminjaman/' . $unique)
        ->set([
            'id'=>$unique,
            'email' => $request->email,
            'barang_id'=>$request->barang_id,
            'start_date'=>$request->start_date,
            'end_date' => $request->end_date,
            'grandtotal'=>$request->grandtotal,
            'status'=>'belum',
        ]);

         $this->database
            ->getReference('barang/' . $barang['id'])
            ->update([
                'stock'=>$fixStok
        ]);
        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'peminjamans has been added']
                );
    }

    public function detailBarang($id)
    {
        $data = $this->database->getReference('barang')->getValue();
        return $data[$id];
    }
    

    public function updateKembali($id){
        $this->database
        ->getReference('peminjaman/' . $id)
        ->update([
            'status' => 'sudah',
        ]);

        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'peminjamans has been updated']
                );
    }



}
