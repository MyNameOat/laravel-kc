<div class="modal fade" id="goodModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">ค้นหาสินค้า</h4>
            </div>
            <div class="pull-right">

            </div>
            <div class="modal-body" style="padding:20px;" id="dataGoods">
                <p>
                    @foreach($types as $type)
                    <button class="btn btn-success dim btn-outline bt-type-search" type="button" data-type_id="{{ $type->id }}">
                        <i class="fa fa-tag"></i> {{ $type->name }}
                    </button>
                    @endforeach
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered dataTables-example" id="tableGood">
                        <thead>
                            <tr>
                                <th>รหัสสินค้า</th>
                                <th>ชื่อสินค้า</th>
                                <th>จำนวนคงเหลือ</th>
                                <th>หน่วยนับ</th>
                                <th><input type="checkbox" id="check-sku-all"></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <div class="pull-right">
                        <button class="btn btn-primary " type="button" id="selectProduct">
                            <i class="fa fa-check"></i>&nbsp;เลือก
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
