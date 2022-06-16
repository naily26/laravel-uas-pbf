<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    private $database;

    public function __construct()
    {
        $this->database = \App\Services\FirebaseService::connect();
    }

    public function get(){
       $data = $this->database->getReference('pengguna')->getValue();
       $arr = [];
       $no = 0;
       if($data != null){
           foreach($data as $key => $item)
           {
                $arr[$no]['id'] = $item['id'];
                $arr[$no]['name'] = $item['name'];
                $arr[$no]['no_hp'] = $item['no_hp'];
                $arr[$no]['alamat_pengguna'] = $item['alamat_pengguna'];
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
        ->getReference('pengguna/' . $unique)
        ->set([
            'id'=>$unique,
            'name' => $request->name,
            'no_hp'=> $request->no_hp,
            'alamat_pengguna' => $request->alamat_pengguna,
        ]);
        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'penggunas has been added']
                );
    }

    public function update(Request $request){
        $this->database
        ->getReference('pengguna/' . $request->id)
        ->update([
             'name' => $request->name,
             'no_hp'=> $request->no_hp,
             'alamat_pengguna' => $request->alamat_pengguna,
        ]);

        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'penggunas has been updated']
                );
    }

    public function delete($id){
        $this->database
        ->getReference('pengguna/' . $id)
        ->remove();

        return response()->json(
                [
                     "status" => "success"
                    , "success" =>true
                    , "message" => 'penggunas has been deleted']
                );
    }
}
