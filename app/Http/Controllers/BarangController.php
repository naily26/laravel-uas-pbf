<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarangController extends Controller
{
    private $database;

    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();
    }

    public function get(){
       $data = $this->database->getReference('barang')->getValue();
       $arr = [];
       if($data != null){
       $no = 0;
           foreach($data as $key => $item)
           {
                $arr[$no]['id'] = $item['id'];
                $arr[$no]['name'] = $item['name'];
                $arr[$no]['stock'] = $item['stock'];
                $arr[$no]['harga_barang'] = $item['harga_barang'];
                $arr[$no]['gambar'] = $item['gambar'];
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


    public function insert(Request $request){
        $unique = strtotime(date('Y-m-d H:i:s'));
        $this->database
        ->getReference('barang/' . $unique)
        ->set([
            'id'=>$unique,
            'name' => $request->name,
            'stock'=> $request->stock,
            'harga_barang' => $request->harga_barang,
            'gambar' => $request->gambar,
        ]);
        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'barangs has been added']
                );
    }

    public function update(Request $request){
        $this->database
        ->getReference('barang/' . $request->id)
        ->update([
            'name' => $request->name,
            'harga_barang' => $request->harga_barang,
            'harga_barang' => $request->harga_barang,
        ]);

        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'barangs has been updated']
                );
    }

    public function delete($id){
        $this->database
        ->getReference('barang/' . $id)
        ->remove();

        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'barangs has been deleted']
                );
    }
}
