@extends('layouts.app')  
@section('1') 
@endsection
@section('2')
<table style="width: 1200px;" align="center">
	<tr>
		<td>
			<div class="col-xs-12" style="margin-top:10px;">  
				<div class="col-xs-8 col-xs-offset-2 text-center" style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); ">
					<div class="form-group text-left" style="margin-top: 10px !important; "> <a href="{{action('c@a')}}"><span class="glyphicon glyphicon-arrow-left" style="color: #8e8e8e;!important;"></span></a></div>
					<h6>RIwayat 	{{$c}}</h6> 
				     <table class="table table-active" id="btable">
					     <tr>
						     <th>Nomor struk</th>
						     <th>Waktu pembelian</th>
						     <th>Jumlah hari</th>
						     <th>Biaya yang dikeluarkan</th>
						     <th>Transaksi berhasil</th>
						     <th>Tombol tindakan</th>
					     </tr> 
					     <tr>
						     <td>-</td>
						     <td>-</td>
						     <td>-</td>
						     <td>-</td>
						     <td>-</td>
						     <td>- </td>
					     </tr>
				     </table>    		
				</div> 
			</div>
			<div class="container col-xs-12">
				<div class="row"></div>
			</div> 
		</td>
	</tr>
</table> 
@endsection 
@section('3') 
	<script >
        $(document).ready(function () {  
            b();
        });  
        function b() {
            var bdata = '{{$a}}';
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('k@c')}}',
                {
                    _token : $('meta[name="tn"]').attr('content'),
                    a: bdata
                },
                function (r){
                    console.log(r);
                    $.notifyClose();
                    $('#btable').html('');
                    var varb = '';
                    varb += '<tbody>';
                    varb += '<tr>';
                    varb += '<th>Nomor struk</th>';
                    varb += '<th>Waktu pembelian</th>';
                    varb += '<th>Jumlah hari</th>';
                    varb += '<th>Biaya yang dikeluarkan</th>';
                    varb += '<th>Transaksi berhasil</th>';
                    varb += '<th>Tombol tindakan</th>';
                    varb += '</tr>'; 
                    if (r['b']===null || parseInt(r['b'])===0 || r['b'].length===0) {
                        varb += '<tr>';
                        varb += '<td>-</td>';
                        varb += '<td>-</td>';
                        varb += '<td>-</td>';
                        varb += '<td>-</td>'; 
                        varb += '<td>-</td>'; 
                        varb += '<td>-</td>';  
                        varb += '</tr>';
                    }
                    else {
                        $.each(r['b'], function (fa, fb) {
                            varb += '<tr>';
                            varb += '<td>'+fb.c+'</td>';
                            varb += '<td>'+fb.created_at+'</td>';
                            varb += '<td>'+fb.e+' hari</td>';
                            varb += '<td>Rp. '+fb.f+',-</td>'; 
                            if (parseInt(fb.g)===1){
                                varb += '<td><span class="glyphicon glyphicon-ok-circle"></span></td>';
                            }
                            else if (parseInt(fb.g)===2){
                                varb += '<td><span class="glyphicon glyphicon-remove"></span></td>';
                            }

                            varb += '<td><a href="{{action('l@a')}}/'+fb.id+'"><span class="glyphicon glyphicon-search" style="color: #8e8e8e !important;"></span></a> </td>';
                            varb += '</tr>';
                        });
                    }
                    varb += '</tbody>';
                    $('#btable').append(varb);
                }
            );
        }
	</script>
@endsection
