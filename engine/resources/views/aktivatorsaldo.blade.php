@extends('layouts.app')
@section('1')
@endsection
@section('2')
	<table style="width: 1200px;" align="center">
		<tr>
			<td>
				<div class="col-xs-12" style="margin-top:10px;">
					<div class="col-xs-12 text-center" style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); ">
						<div class="form-group text-center" style="margin-top: 10px !important; "> <a href="{{action('b@a')}}"><span class="glyphicon glyphicon-home" style="color: #8e8e8e;!important;"></span></a></div>
						<table class="table table-active" id="btable">
							<tbody>
							<tr>
								<th>Nomor pesanan/struk</th>
								<th>Nama Akun</th>
								<th>Permintaan saldo</th>
								<th>Saldo Sekarang</th>
								<th>Status</th> 
								<th>Action </th>
							</tr>
							<tr>
								<td>-</td>
								<td> </td>
								<td> </td>
								<td> </td>
								<td> </td>
								<td> </td> 
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table> 
@endsection
@section('3')
	<script> 
        $(document).ready(function () {
            f();
        });        
        function c(cid) {
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('j@c')}}', 
                {
                    _token : $('meta[name="tn"]').attr('content'),
                    a:cid
                },
                function (r){ 
                    if (parseInt(r['c'])===1){
                        location.href=r['d'];
                    }
                    else if (parseInt(r['c'])===0){
                        $.notifyClose();
                        notificationdata = r['e'];
                        notification();
                    }
                }
            );	        
        } 
        function d(did) {
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('j@d')}}', 
                {
                    _token : $('meta[name="tn"]').attr('content'),
                    a:did
                },
                function (r){
                    $.notifyClose();
                    notificationdata = r['a'];
                    notification();
                    if (parseInt(r['b'])===1){
                        location.reload();
                    }
                }
            );	        
        }
        function e(eida, eidb) {
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('j@e')}}', 
                {
                    _token : $('meta[name="tn"]').attr('content'),
                    a:eida,
                    b:eidb,
                },
                function (r){
                    $.notifyClose();
                    notificationdata = r['a'];
                    notification();
                    if (parseInt(r['b'])===1){
                        location.reload();
                    }
                }
            );	        
        }
        function f() { 
            var fdata = '{{$a}}';
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('j@f')}}',
                {
                    _token : $('meta[name="tn"]').attr('content'),
	                a: fdata
                },
                function (r){
                    $.notifyClose();
                    $('#btable').html('');
                    var varf = '';
                    varf += '<tbody>';
                    varf += '<tr>';
                    varf += '<th>Nomor pesanan/struk</th>';
                    varf += '<th>Date</th>';
                    varf += '<th>Nama Akun</th>';
                    varf += '<th>Permintaan saldo</th>'; 
                    varf += '<th>Biaya yang dikeluarkan</th>';
                    varf += '<th>Status</th>';
                    varf += '<th>Action </th>'; 
                    varf += '</tr>';


                    if (r['b']===null || parseInt(r['b'])===0 || r['b'].length===0){
                        varf += '<td>-</td>';
                        varf += '<td>-</td>';
                        varf += '<td>-</td>';
                        varf += '<td>-</td>';
                        varf += '<td>-</td>';
                        varf += '<td>-</td>';  
                        varf += '<td>-</td>';  
                    }
                    else {
                        $.each(r['b'], function (fa, fb) {
                            varf += '<tr>';
                            varf += '<td>'+fb.b+'</td>';
                            if (fb.g===null){
                                varf += '<td> - </td>';
                            }
                            else {
                                varf += '<td>'+fb.g+'</td>';
                            }
                           
                            varf += '<td>'+fb.a+'</td>';
                            varf += '<td>Rp. '+fb.d+',-</td>';
       
                            varf += '<td>Rp. '+fb.e+',-</td>'; 


                            if (parseInt(fb.c)===0){
                                varf += '<td> Menunggu dokumen </td>';
                            }
                            else if (parseInt(fb.c)===1){
                                varf += '<td><span class="glyphicon glyphicon-ok-circle"></span> </td>';
                            }
                            else if (parseInt(fb.c)===2){
                                varf += '<td><label class="label label-published" style="cursor: pointer;" onclick="c('+fb.bid+');">Periksa dokumen </label>'; 
                                varf += ' </td>';
                                
                            }
                            else if (parseInt(fb.c)===3){
                                varf += '<td><label class="label label-published"><span class="glyphicon glyphicon-remove-circle"></span> Tidak valid</label> </td>';
                            }
                            varf += '<td>';
                            varf += '<div class="btn-group">';
                            varf += '<a href="{{action('j@a')}}/riwayat/'+fb.usersid+'" onclick="b('+fb.bid+');" class="btn btn-xs  btn-like"><span class="glyphicon glyphicon-time"></span></a>'; 
                            varf += '<button onclick="d('+fb.bid+');" class="btn btn-xs  btn-like"><span class="glyphicon glyphicon-remove"></span></button>';
                            varf += '<button onclick="e('+fb.bid+','+fb.usersid+');" class="btn btn-xs  btn-like"><span class="glyphicon glyphicon-ok-circle"></span></button>';
                            varf += '</div>';
                            varf += '</td>'; 
                            varf += '</tr>';
                        });
                    }
                    varf += '</tbody>'; 
                    $('#btable').append(varf);
                }
            );
        }
	</script>
@endsection
