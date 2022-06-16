<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
class PeminjamanController extends Controller
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
            $no = 0;
            foreach ($transaksi as $tr => $trItem) {
                $barang = $this->detailBarang($trItem['barang_id']);
                $trId = $trItem['id'];
                $arr[$no]['id'] = $trId;
                if($barang != null || !is_null($barang))
                {
                    $arr[$no]['nama_barang'] = $barang['name'];
                }else{
                    $arr[$no]['nama_barang'] = 'Data was deleted!';
                }

                 $arr[$no]['start_date'] = $trItem['start_date']; 
                 $arr[$no]['end_date'] = $trItem['end_date']; 
                 $arr[$no]['grandtotal'] = $trItem['grandtotal']; 
                 $arr[$no]['hari'] = $trItem['hari']; 
                 $no++;
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
        $end = strtotime($request->start_date); // or your date as well
        $start = strtotime($request->end_date);
        $datediff = $end - $start;

        $hari = floor($datediff / (60 * 60 * 24));
        $grandtotal = $hari * $request->grandtotal;
        $this->database
        ->getReference('peminjaman/' . $unique)
        ->set([
            'id'=>$unique,
            'user_id' => $request->user_id,
            'barang_id'=>$request->barang_id,
            'start_date'=>$request->start_date,
            'end_date' => $request->end_date,
            'grandtotal'=> $grandtotal,
            'hari'=>$hari,
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

    public function update(Request $request){
        $end = strtotime($request->start_date); // or your date as well
        $start = strtotime($request->end_date);
        $datediff = $end - $start;

        $hari = floor($datediff / (60 * 60 * 24));
        $grandtotal = $hari * $request->grandtotal;
        $this->database
        ->getReference('peminjaman/' . $request->id)
        ->update([
            'user_id' => $request->user_id,
            'barang_id'=>$request->barang_id,
            'start_date'=>$request->start_date,
            'end_date' => $request->end_date,
            'grandtotal'=>$grandtotal,
            'hari'=>$hari,
        ]);

        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'peminjamans has been updated']
                );
    }



}
