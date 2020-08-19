@extends('layouts-center.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="contact-box center-version">
                    <a href="{{ route('inv.index') }}">
                        <img alt="image" src="img/a2.jpg">
                        <h3 class="m-b-xs"><strong>INV</strong></h3>
                        <div class="font-bold">ระบบจัดการใบเบิกคลังสินค้า</div>
                        <address class="m-t-md">
                            เบิกสินค้า <br>
                            อนุมัติใบเบิก<br>
                            รายงานใบเบิก
                        </address>
                    </a>
                    <div class="contact-box-footer">
                        <span class="text-danger">** ไม่ขึ้นกับสาขา **</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
