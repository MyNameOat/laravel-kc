@extends('layouts-board.app')

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>ยอดขาย ประจำวันที่ {{ $now->format('d/m/Y') }}</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="sum_price">0</h1>
                    <div class="stat-percent font-bold text-success">100% <i class="fa fa-bolt"></i></div>
                    <small>Total income</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>บิลเงินสด ประจำวันที่ {{ $now->format('d/m/Y') }}</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="hs_price">0</h1>
                    <div class="stat-percent font-bold text-success">0% <i class="fa fa-bolt"></i></div>
                    <small>HS</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>บิลเงินเชื่อ ประจำวันที่ {{ $now->format('d/m/Y') }}</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="invoice_price">0</h1>
                    <div class="stat-percent font-bold text-success">0% <i class="fa fa-bolt"></i></div>
                    <small>Invoice</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>แสดงสัดส่วนยอดขายตามสาขา</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-9">
                            <div>
                                <div id="pie"></div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <ul class="stat-list">
                                <li>
                                    <h2 class="no-margins">2,346</h2>
                                    <small>Total orders in period</small>
                                    <div class="stat-percent">48% <i class="fa fa-level-up text-navy"></i></div>
                                    <div class="progress progress-mini">
                                        <div style="width: 48%;" class="progress-bar"></div>
                                    </div>
                                </li>
                                <li>
                                    <h2 class="no-margins ">4,422</h2>
                                    <small>Orders in last month</small>
                                    <div class="stat-percent">60% <i class="fa fa-level-down text-navy"></i></div>
                                    <div class="progress progress-mini">
                                        <div style="width: 60%;" class="progress-bar"></div>
                                    </div>
                                </li>
                                <li>
                                    <h2 class="no-margins ">9,180</h2>
                                    <small>Monthly income from orders</small>
                                    <div class="stat-percent">22% <i class="fa fa-bolt text-navy"></i></div>
                                    <div class="progress progress-mini">
                                        <div style="width: 22%;" class="progress-bar"></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-4">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Messages</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content ibox-heading">
                    <h3><i class="fa fa-envelope-o"></i> New messages</h3>
                    <small><i class="fa fa-tim"></i> You have 22 new messages and 16 waiting in draft folder.</small>
                </div>
                <div class="ibox-content">
                    <div class="feed-activity-list">

                        <div class="feed-element">
                            <div>
                                <small class="float-right text-navy">1m ago</small>
                                <strong>Monica Smith</strong>
                                <div>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem
                                    Ipsum
                                </div>
                                <small class="text-muted">Today 5:60 pm - 12.06.2014</small>
                            </div>
                        </div>

                        <div class="feed-element">
                            <div>
                                <small class="float-right">2m ago</small>
                                <strong>Jogn Angel</strong>
                                <div>There are many variations of passages of Lorem Ipsum available</div>
                                <small class="text-muted">Today 2:23 pm - 11.06.2014</small>
                            </div>
                        </div>

                        <div class="feed-element">
                            <div>
                                <small class="float-right">5m ago</small>
                                <strong>Jesica Ocean</strong>
                                <div>Contrary to popular belief, Lorem Ipsum</div>
                                <small class="text-muted">Today 1:00 pm - 08.06.2014</small>
                            </div>
                        </div>

                        <div class="feed-element">
                            <div>
                                <small class="float-right">5m ago</small>
                                <strong>Monica Jackson</strong>
                                <div>The generated Lorem Ipsum is therefore</div>
                                <small class="text-muted">Yesterday 8:48 pm - 10.06.2014</small>
                            </div>
                        </div>


                        <div class="feed-element">
                            <div>
                                <small class="float-right">5m ago</small>
                                <strong>Anna Legend</strong>
                                <div>All the Lorem Ipsum generators on the Internet tend to repeat</div>
                                <small class="text-muted">Yesterday 8:48 pm - 10.06.2014</small>
                            </div>
                        </div>
                        <div class="feed-element">
                            <div>
                                <small class="float-right">5m ago</small>
                                <strong>Damian Nowak</strong>
                                <div>The standard chunk of Lorem Ipsum used</div>
                                <small class="text-muted">Yesterday 8:48 pm - 10.06.2014</small>
                            </div>
                        </div>
                        <div class="feed-element">
                            <div>
                                <small class="float-right">5m ago</small>
                                <strong>Gary Smith</strong>
                                <div>200 Latin words, combined with a handful</div>
                                <small class="text-muted">Yesterday 8:48 pm - 10.06.2014</small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">

            <div class="row">
                <div class="col-lg-6">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>อันดับยอดขายแยกตามสาขา</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content table-responsive">
                            <table id="my-table" class="table table-hover no-margins isortope" data-isortope-autoresort='true'>
                                <thead>
                                <tr>
                                    <th>สาขา</th>
                                    <th>ยอดขายรวม</th>
                                    <th>เงินสด</th>
                                    <th>เงินเชื่อ</th>
                                </tr>
                                </thead>
                                <tbody id="mix-wrapper">
                                @foreach($warehouses as $warehouse)
                                    <tr id="tr_{{ $warehouse->id }}" class="mix-target" data-amount="0">
                                        <td data-sort-type='none'>{{ $warehouse->name }}</td>
                                        <td id="warehouse_sum_price_{{ $warehouse->id }}">0</td>
                                        <td data-sort-type='none' id="warehouse_hs_price_{{ $warehouse->id }}">0</td>
                                        <td data-sort-type='none' id="warehouse_invoice_price_{{ $warehouse->id }}">0</td>
                                        <input type="hidden" id="val_warehouse_sum_price_{{ $warehouse->id }}" value="0">
                                        <input type="hidden" id="val_warehouse_hs_price_{{ $warehouse->id }}" value="0">
                                        <input type="hidden" id="val_warehouse_invoice_price_{{ $warehouse->id }}" value="0">
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Small todo list</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <ul class="todo-list m-t small-list">
                                <li>
                                    <a href="#" class="check-link"><i class="fa fa-check-square"></i> </a>
                                    <span class="m-l-xs todo-completed">Buy a milk</span>

                                </li>
                                <li>
                                    <a href="#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                    <span class="m-l-xs">Go to shop and find some products.</span>

                                </li>
                                <li>
                                    <a href="#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                    <span class="m-l-xs">Send documents to Mike</span>
                                    <small class="label label-primary"><i class="fa fa-clock-o"></i> 1 mins</small>
                                </li>
                                <li>
                                    <a href="#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                    <span class="m-l-xs">Go to the doctor dr Smith</span>
                                </li>
                                <li>
                                    <a href="#" class="check-link"><i class="fa fa-check-square"></i> </a>
                                    <span class="m-l-xs todo-completed">Plan vacation</span>
                                </li>
                                <li>
                                    <a href="#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                    <span class="m-l-xs">Create new stuff</span>
                                </li>
                                <li>
                                    <a href="#" class="check-link"><i class="fa fa-square-o"></i> </a>
                                    <span class="m-l-xs">Call to Anna for dinner</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Transactions worldwide</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="row">
                                <div class="col-lg-6">
                                    <table class="table table-hover margin bottom">
                                        <thead>
                                        <tr>
                                            <th style="width: 1%" class="text-center">No.</th>
                                            <th>Transaction</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td> Security doors
                                            </td>
                                            <td class="text-center small">16 Jun 2014</td>
                                            <td class="text-center"><span class="label label-primary">$483.00</span>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td class="text-center">2</td>
                                            <td> Wardrobes
                                            </td>
                                            <td class="text-center small">10 Jun 2014</td>
                                            <td class="text-center"><span class="label label-primary">$327.00</span>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td class="text-center">3</td>
                                            <td> Set of tools
                                            </td>
                                            <td class="text-center small">12 Jun 2014</td>
                                            <td class="text-center"><span class="label label-warning">$125.00</span>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td class="text-center">4</td>
                                            <td> Panoramic pictures</td>
                                            <td class="text-center small">22 Jun 2013</td>
                                            <td class="text-center"><span class="label label-primary">$344.00</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">5</td>
                                            <td>Phones</td>
                                            <td class="text-center small">24 Jun 2013</td>
                                            <td class="text-center"><span class="label label-primary">$235.00</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">6</td>
                                            <td>Monitors</td>
                                            <td class="text-center small">26 Jun 2013</td>
                                            <td class="text-center"><span class="label label-primary">$100.00</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-6">
                                    <div id="world-map" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/plugins/number-count/dist/jquery.easy_number_animate.min.js"></script>
    <script src="/js/plugins/d3/d3.min.js"></script>
    <script src="/js/plugins/c3/c3.min.js"></script>
    <script>
        var chart;
        $(document).ready(function() {
            getSummarySaleInDay();

            chart = c3.generate({
                bindto: '#pie',
                data:{
                    columns: [
                        ['ขอนแก่น', 30],
                        ['data2', 120]
                    ],
                    colors:{
                        'ขอนแก่น': '#1ab394',
                        data2: '#BABABA'
                    },
                    type : 'pie'
                }
            });
        });

        function getSummarySaleInDay() {
            console.log('query');
            $.ajax({
                type: "POST",
                url: "{{ url('api/summary-sale-in-day') }}",
                success: function (response) {
                    let invoice_price = $('#invoice_price').text().replace(/,/g, '');
                    invoice_price = +invoice_price;
                    if (response.data.invoice_price != invoice_price) {
                        $('#invoice_price').easy_number_animate({
                            start_value: invoice_price,
                            end_value: response.data.invoice_price,
                            duration: 5000,
                            delimiter:','
                        });
                    }

                    let hs_price = $('#hs_price').text().replace(/,/g, '');
                    hs_price = +hs_price;
                    if (response.data.hs_price != hs_price) {
                        $('#hs_price').easy_number_animate({
                            start_value: hs_price,
                            end_value: response.data.hs_price,
                            duration: 5000,
                            delimiter:','
                        });
                    }

                    let sum_price = $('#sum_price').text().replace(/,/g, '');
                    sum_price = +sum_price;
                    let res_sum_price = +response.data.invoice_price + +response.data.hs_price;
                    if (res_sum_price != sum_price) {
                        $('#sum_price').easy_number_animate({
                            start_value: sum_price,
                            end_value: res_sum_price,
                            duration: 5000,
                            delimiter:','
                        });
                    }

                    $.each(response.data.warehouse_invoice_price, function (index, element) {
                        let warehouse_invoice_price = +$('#val_warehouse_invoice_price_'+element['warehouse_id']).val();
                        let res_warehouse_invoice_price = +element['total_amount'];
                        if (warehouse_invoice_price != res_warehouse_invoice_price) {
                            $('#val_warehouse_invoice_price_'+element['warehouse_id']).val(res_warehouse_invoice_price);
                            $('#warehouse_invoice_price_' + element['warehouse_id']).easy_number_animate({
                                start_value: warehouse_invoice_price,
                                end_value: res_warehouse_invoice_price,
                                duration: 5000,
                                delimiter: ',',
                            });
                            calWarehouseSumPrice(element['warehouse_id']);
                        }
                    });

                    $.each(response.data.warehouse_hs_price, function (index, element) {
                        let warehouse_hs_price = +$('#val_warehouse_hs_price_'+element['warehouse_id']).val();
                        let res_warehouse_hs_price = +element['total_amount'];
                        if (warehouse_hs_price != res_warehouse_hs_price) {
                            $('#val_warehouse_hs_price_'+element['warehouse_id']).val(res_warehouse_hs_price);
                            $('#warehouse_hs_price_'+element['warehouse_id']).easy_number_animate({
                                start_value: warehouse_hs_price,
                                end_value: res_warehouse_hs_price,
                                duration: 5000,
                                delimiter:',',
                            });
                            calWarehouseSumPrice(element['warehouse_id']);
                        }
                    });
                }
            });

            setTimeout(getSummarySaleInDay, 60000);
        }

        function calWarehouseSumPrice(warehouse_id) {
            let warehouse_hs_price = +$('#val_warehouse_hs_price_'+warehouse_id).val();
            let warehouse_invoice_price = +$('#val_warehouse_invoice_price_'+warehouse_id).val();
            let warehouse_sum_price = +$('#val_warehouse_sum_price_'+warehouse_id).val();
            if (warehouse_sum_price != (warehouse_hs_price + warehouse_invoice_price)) {
                $('#val_warehouse_sum_price_'+warehouse_id).val((warehouse_hs_price + warehouse_invoice_price));
                $('#warehouse_sum_price_'+warehouse_id).easy_number_animate({
                    start_value: warehouse_sum_price,
                    end_value: (warehouse_hs_price + warehouse_invoice_price),
                    duration: 5000,
                    delimiter:','
                });
                $("#tr_"+warehouse_id).attr("data-amount", (warehouse_hs_price + warehouse_invoice_price));
            }

            chart.load({
                columns: [
                    ['ขอนแก่น', 50],
                    ['data2', 400]
                ],
            });
        }
    </script>
@endsection
