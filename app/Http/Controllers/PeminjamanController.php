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
        if($data != null){
            $no = 0;
            foreach ($data as $tr => $trItem) {
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
                 $arr[$no]['status'] = $trItem['status']; 
                 $no++;
            }
        }
        if($data != null){
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
        }else{
            return response()->json(
                    [
                        "status" => "failed"
                        , "success" =>false
                        , "message" => "peminjaman gagal id barang tidak ditemukan!"]
                    );
        }
        // $user = $this->detailUser($request->user_id);
        //  if($user == null){
        //    return response()->json(
        //             [
        //                 "status" => "failed"
        //                 , "success" =>false
        //                 , "message" => "peminjaman gagal id peminjam tidak ditemukan!"]
        //             );
        // }
        $user = $request->user_id;
        $unique = strtotime(date('Y-m-d H:i:s'));
        $start = strtotime($request->start_date); // or your date as well
        $end = strtotime($request->end_date);
        $datediff = $end - $start;

        $hari = floor($datediff / (60 * 60 * 24));
        $grandtotal = $hari * $barang['harga_barang'];
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
        if(!isset($data[$id]))
        {
            return null;
        }
        return $data[$id];
    }

    public function detailPinjam($id)
    {
        $data = $this->database->getReference('peminjaman')->getValue();
        if(!isset($data[$id]))
        {
            return null;
        }
        return $data[$id];
    }

    public function detailUser($id)
    {
        $data = $this->database->getReference('pengguna')->getValue();
        if(!isset($data[$id]))
        {
            return null;
        }
        return $data[$id];
    }
    

    public function updateKembali($id){
        $this->database
        ->getReference('peminjaman/' . $id)
        ->update([
            'status' => 'sudah',
        ]);

        $detailPinjam = $this->detailPinjam($id);
        if($detailPinjam != null){
            $barang = $this->detailBarang($detailPinjam['barang_id']);
                if($barang != null){
                     $this->database
                        ->getReference('barang/' . $barang['id'])
                        ->update([
                            'stock'=>$barang['stock'] + 1,
                    ]);
                }
            }
        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'peminjamans has been updated']
                );
    }

    public function update(Request $request){
        $barang = $this->detailBarang($request->barang_id);
        $fixStok = 0;
        if($barang == null){
            return response()->json(
                    [
                        "status" => "failed"
                        , "success" =>false
                        , "message" => "peminjaman gagal id barang tidak ditemukan!"]
                    );
        }
        $user = $this->detailUser($request->user_id);
         if($user == null){
           return response()->json(
                    [
                        "status" => "failed"
                        , "success" =>false
                        , "message" => "peminjaman gagal id peminjam tidak ditemukan!"]
                    );
        }


        $start = strtotime($request->start_date); // or your date as well
        $end = strtotime($request->end_date);
        $datediff = $end - $start;

        $hari = floor($datediff / (60 * 60 * 24));
        $grandtotal = $hari * $barang['harga_barang'];
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

public function delete($id){
     $barang = $this->detailBarang($id);
        if($barang != null){
             $this->database
            ->getReference('barang/' . $barang['id'])
            ->update([
                'stock'=>$barang['stock'] + 1,
        ]);
           
        }
        $this->database
        ->getReference('peminjaman/' . $id)
        ->remove();

        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'peminjamans has been deleted']
                );
    }

}
