<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{

    protected $connection = 'HRM';

    public function parentDepartments()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'DISTRICT_ID');
    }

    public function amphur()
    {
        return $this->belongsTo(Amphur::class, 'amphur_id', 'AMPHUR_ID');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'PROVINCE_ID');
    }

    public function departmentDivisions()
    {
        return $this->hasMany(DepartmentDivision::class);
    }

    public function transformStatus()
    {
        if ($this->department_type_id == 1) {
            return 'สำนักงานใหญ่';
        }

        if ($this->department_type_id == 2) {
            return 'เขต';
        }

        return 'สาขา';
    }

    public function address()
    {
        $address = '';
        $address += ($this->address_name != ''        ? ' ที่อยู่ '.$this->address_name : '');
        $address += ($this->address_num != ''         ? ' เลขที่ '.$this->address_num : '');
        $address += ($this->address_moo != ''         ? ' หมู่ '.$this->address_moo : '');
        $address += ($this->address_build != ''       ? ' ชื่ออาคาร '.$this->address_build : '');
        $address += ($this->address_build_num != ''   ? ' เลข '.$this->address_build_num : '');
        $address += ($this->address_build_class != '' ? ' ชั้น '.$this->address_build_class : '');
        $address += ($this->address_village != ''     ? ' หมู่บ้าน '.$this->address_village : '');
        $address += ($this->alley != ''               ? ' ตรอก/ซอย '.$this->alley : '');
        $address += ($this->road != ''                ? ' ถนน '.$this->road : '');
        $address += ($this->district_id != ''         ? ' ตำบล '.$this->district->DISTRICT_NAME : '');
        $address += ($this->amphur_id != ''           ? ' อำเภอ '.$this->amphur->AMPHUR_NAME : '');
        $address += ($this->province_id != ''         ? ' จังหวัด '.$this->province->PROVINCE_NAME : '');
        $address += ($this->postcode != ''            ? ' รหัสไปรษณีย์ '.$this->postcode : '');
        return $address;
    }

    public function departmentApproves()
    {

        return $this->hasMany(DepartmentApprove::class);
    }

    public function menuAccesses()
    {
        return $this->hasMany(MenuAccess::class);
    }
}
