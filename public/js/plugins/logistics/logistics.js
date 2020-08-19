$( document ).ready(function() {

    $( "#addCarData" ).click(function() {
        callPagecarAdd();
    });

    $( "#addTransporttypeCar" ).click(function() {
        callPageTransporttypeAdd();
    });

    $('#transferDocument').click(function(){
        callPagetransferDocument();
    })

});

//โหลดหน้า ข้อมูลรถขนส่ง
function callPagecarAdd(){
    OpenLoading();
    $.ajax({
        type: "GET",
        url: 'callpageDataList',
        success: function (Result) {
            $('#ContantLogistics').html(Result);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                onOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
                Toast.fire({
                icon: 'success',
                title: 'ข้อมูลรถขนส่ง'
            })
            CloseLoading();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}

//โหลดหน้า เพิ่มข้อมูลประเภทขนส่ง
function callPageTransporttypeAdd(){
    OpenLoading();
    $.ajax({
        type: "GET",
        url: 'callPageTransporttype',
        success: function (Result) {
            $('#ContantLogistics').html(Result);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                onOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

                Toast.fire({
                icon: 'success',
                title: 'เพิ่มข้อมูลประเภทขนส่ง'
            })
            CloseLoading();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}

//โหลดหน้าบันทึกข้อมูลรถขนส่ง
function CallpageEvenAdd(){
    OpenLoading();
    $.ajax({
        type: "GET",
        url: 'callpageEvenAdd',
        success: function (Result) {
            $('#ContantLogistics').html(Result);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                onOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

                Toast.fire({
                icon: 'success',
                title: 'เพิ่มข้อมูลรถขนส่ง'
            })
            CloseLoading();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}

//โหลดหน้า เอกสาร
function callPagetransferDocument(){
    OpenLoading();
    $.ajax({
        type: "GET",
        url: 'callPagetransferDocument',
        success: function (Result) {
            $('#ContantLogistics').html(Result);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                onOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
                Toast.fire({
                icon: 'success',
                title: 'เอกสารขนส่ง'
            })
            CloseLoading();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}

function callPagetransferDocumentAdd(){
    OpenLoading();
    $.ajax({
        type: "GET",
        url: 'callPagetransferDocumentadd',
        success: function (Result) {
            $('#ContantLogistics').html(Result);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                onOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
                Toast.fire({
                icon: 'success',
                title: 'เพิ่มข้อมูลเอกสารขนส่ง'
            })
            CloseLoading();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}








  