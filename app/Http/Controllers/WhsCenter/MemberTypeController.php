<?php

namespace App\Http\Controllers\WhsCenter;

use App\Http\Controllers\Controller;
use App\Member_type;
use Illuminate\Http\Request;
use App\GoodPrice;

class MemberTypeController extends Controller
{
    public function index (){
        return view('whs-center.members.set-member-types.index');
    }

    public function getMemberType(){
        return datatables()->of(
            Member_type::all()
        )->toJson();
    }

    public function storeMemberType(Request $req){

        $this->validate($req,['name' => 'required']);
        $type_name = $req->name;
        $checkTypeName = Member_type::where('name',$type_name)->first();
        if($checkTypeName){
            $data = [
                'title' => 'เกิดข้อผิดพลาด',
                'msg' => 'ชื่อประเภทสมาชิกซ้ำ กรุณาลองใหม่อีกครั้ง',
                'status' => 'error',                
            ];
            return  $data;
        }
            $memberType = new Member_type;
            $memberType->name = $type_name;
            if($memberType->save()){
                $data = [
                    'title' => 'บันทึกสำเร็จ',
                    'msg' => 'บันทึกประเภทสมาชิกใหม่สำเร็จ',
                    'status' => 'success',                
                ]; 
            }else{
                $data = [
                    'title' => 'เกิดข้อผิดพลาด',
                    'msg' => 'บันทึกไม่สำเร็จ',
                    'status' => 'error',                
                ];
            }      
      
        return $data;
    }

    public function editMemberType(Request $req){

        $this->validate($req,['id' => 'required','name' => 'required']);
        $id = $req->id;
        $name = $req->name;

        $checkTypeName = Member_type::where('name',$name)->first();
        if($checkTypeName){
            $data = [
                'title' => 'เกิดข้อผิดพลาด',
                'msg' => 'ชื่อประเภทสมาชิกซ้ำ กรุณาลองใหม่อีกครั้ง',
                'status' => 'error',                
            ];
            return  $data;
        }

        $memberType = Member_type::find($id);
        $memberType->name = $name;

        if($memberType->save()){
                $data = [
                    'title' => 'บันทึกสำเร็จ',
                    'msg' => 'บันทึกประเภทสมาชิกใหม่สำเร็จ',
                    'status' => 'success',                
                ]; 
        }else{
                $data = [
                    'title' => 'เกิดข้อผิดพลาด',
                    'msg' => 'บันทึกไม่สำเร็จ',
                    'status' => 'error',                
                ];
        }      
        return $data;
    }

    public function deleteMemberType(Request $req){

        $this->validate($req,['id' => 'required']);

        $id = $req->id;
        $memberType = Member_type::find($id);

        $goodPrice = GoodPrice::where('member_type_id',$id);
        $goodPrice->delete();
  
        if($memberType->delete()){
            $data = [
                'title' => 'ลบสำเร็จ',
                'msg' => 'ลบประเภทกลุ่มสมาชิกสำเร็จ',
                'status' => 'success',                
            ]; 
        }else{
            $data = [
                'title' => 'เกิดข้อผิดพลาด',
                'msg' => 'ลบประเภทกลุ่มสมาชิกไม่สำเร็จ',
                'status' => 'error',                
            ];
        } 
        return $data;     
    }
  


}
