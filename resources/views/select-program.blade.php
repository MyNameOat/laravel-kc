@extends('layouts-center.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="contact-box center-version">

                    <a href="{{ route('board.dashboard') }}">
                        <img alt="image" src="/img/a2.jpg">
                        <h3 class="m-b-xs"><strong>BOARD</strong></h3>
                        <div class="font-bold">สรุปภาพรวมระบบ</div>
                        <address class="m-t-md">
                            ดูสรุปภาพรวมทั้งหมด<br>
                            แจ้งเตือนส่วนกลางต่างๆ<br>
                            <br>
                        </address>
                    </a>
                    <div class="contact-box-footer">
                        <span class="text-danger">** ไม่ขึ้นกับสาขา **</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="contact-box center-version">

                    <a href="">
                        <img alt="image" src="/img/a2.jpg">
                        <h3 class="m-b-xs"><strong>WHS</strong></h3>
                        <div class="font-bold">ระบบจัดการคลังสินค้าสาขา</div>
                        <address class="m-t-md">
                            เปิดใบขอซื้อ<br>
                            รับเข้าสินค้า<br>
                            ตรวจสินค้าในคลัง<br>
                        </address>
                    </a>
                    <div class="contact-box-footer">
                        <span class="text-success">** ขึ้นกับสาขาที่เลือก **</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="contact-box center-version">

                    <a href="">
                        <img alt="image" src="/img/a2.jpg">
                        <h3 class="m-b-xs"><strong>WHS CEN*</strong></h3>
                        <div class="font-bold">ระบบจัดการคลังสินค้าส่วนกลาง</div>
                        <address class="m-t-md">
                            จัดการสินค้า, เปิดใบสั่งซื้อ<br>
                            จัดการระบบคอยล์<br>
                            บันทึก RR<br>
                        </address>
                    </a>
                    <div class="contact-box-footer">
                        <span class="text-danger">** ไม่ขึ้นกับสาขา **</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="contact-box center-version">

                    <a href="">
                        <img alt="image" src="img/a2.jpg">
                        <h3 class="m-b-xs"><strong>LOGISTIC</strong></h3>
                        <div class="font-bold">ระบบการจัดการการส่งสินค้า และ ทรัพยากร</div>
                        <address class="m-t-md">
                            จัดการระบบขนส่งสินค้าต่างๆ <br>
                            ที่เกิดจากความต้องการของลูกค้า<br>
                        </address>
                    </a>
                    <div class="contact-box-footer">
                        <span class="text-danger">** ไม่ขึ้นกับสาขา **</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="contact-box center-version">

                    <a href="{{ route('inv.index') }}">
                        <img alt="image" src="img/a2.jpg">
                        <h3 class="m-b-xs"><strong>INV</strong></h3>
                        <div class="font-bold">ระบบจัดการใบเบิกคลังสินค้า</div>
                        <address class="m-t-md">
                            สร้างใบเบิก <br>
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
