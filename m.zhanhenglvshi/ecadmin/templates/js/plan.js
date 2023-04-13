// JavaScript Document

//  跳转方法
function fun(url) {
	window.location.href = url; 
} 

// 添加对象
function add_goods(){
	var select_num = $('.client-div-select').length;
	var goods_num = $('.all_line').length;
	var html = '';
	var param = select_num+",'/index.php?s=Admin/Index/ajax_order/act/1'";
	//html += '<div class="all_line">';
	html += '<div class="client-div-select" onclick="select_show_hidde('+param+')">';
	html += '<span class="select-show">请选择产品</span>';
	html += '<input type="hidden" class="from-search" name="user_id[]" value="" />';
	html += '<i class="sanjiao-cat"></i>';
	html += '</div>';
	html += '<div class="div-select-ul">';
	html += '<div class="search-div"  style="display:none;">';
	html += '<input type="text" class="brand-name" name="brand_name" onkeyup="select_ul_search('+param+')" value="" />';
	html += '<i class="search-icon"></i>';
	html += '<ul class="search-ul" >';
	html += '</ul>';
	html += '</div>';
	html += '</div>';
	//html += '</div>';
	if(goods_num == 0){
		$('#add_goodd').append(html);
	}else{
		$($('.all_line')[goods_num-1]).append(html);
	}
}
// 跳到第n页、每页显示n条
function change_page(){
	var size = $('input[name="page_size"]').val();
	var page = $('input[name="page_now"]').val();
	var url = $('.page_reset').attr('href');
	location.href = url+'&size='+size+'&page='+page;
}

// 全选、取消全选  
function checkAll(obj){
	if(obj){
		var is_checked = obj.checked;
		if(is_checked == true){
			$('input[name="box[]"]').each(function(i,e){
				$(e)[0].checked = true;
			});
			sumCheckbox();
		}else{
			$('input[name="box[]"]').each(function(i,e){
				$(e)[0].checked = false;
			});
			sumCheckbox();
		}
	}
}
function close_div(name){
	$("#"+name).hide();
	$('.body_hei').hide();
}

// 统计已选中多选框个数
function sumCheckbox(){
	var num = $('input[name="box[]"]:checked').length;
	$('#order_num').text(num);
}

// 添加页面
function add_cus(){
	$('#myModal').modal({
		remote:'index.php?s=Admin/Index/add_message'
	}).on('hidden.bs.modal',function(){
		location.reload();
	});
}
function ajax_send(id){
	$.post('index.php?s=Admin/Index/check_message&id='+id,"",function(data){
		console.log(data);
		var html = "";
		if(data.error>0){
			html = "<div class='error'>"+data.message+"</div>";
		}else{
			html = "<div class='order_list_all'><div class='order_sn'>流水号:"+data.data.send_sn+"</div><div class='send_message'><div class='f_l'>发送者:"+data.data.send_name+"</div><div class='f_r'>"+data.data.send_time+"</div><div class='send_content'>发送内容:"+data.data.send_content+"</div><div class='f_l'>回复者:"+data.data.return_name+"</div><div class='f_r'>"+data.data.return_time+"</div><div class='return_tel'>回复者电话:"+data.data.return_tel+"</div><div class='send_content'>回复内容:"+data.data.return_content+"</div><div class='f_l'>事件类型:"+data.data.send_type+"</div><div class='f_r'>状态:"+data.data.send_status+"</div><div class='send_content'>事件备注:"+data.data.send_note+"</div><div class='clear'></div></div></div>";
		}
		$('.body_hei').show();
		$('.is_modal_content').html(html);
		$("#is_modal").show();
	},'json');
}
// 添加页面
function return_cus(id){
	$('#myModal').modal({
		remote:'index.php?s=Admin/Index/return_message&id='+id
	}).on('hidden.bs.modal',function(){
		location.reload();
	});
}
// 添加处理
function return_onclick(){
	$('#add').attr("disabled","disabled");
	var formdata=$('#add_from').serializeArray();
	$.post('index.php?s=Admin/Index/return_content',formdata,function(data){
		$('#myModal').find('.modal-header').html(data);
		$('#myModal').find('#add_from').remove();
		$('#myModal').find('.order_list_all').remove();
		$('#myModal').find('#add').hide();
		setInterval("fun('/index.php/Admin/Index/content')", 2000); 
	},'json');
}
// 添加处理
function add_onclick(){
	$('#add').attr("disabled","disabled");
	var formdata=$('#add_from').serializeArray();
	$.post('index.php?s=Admin/Index/doadd',formdata,function(data){
		$('#myModal').find('.modal-header').html(data);
		$('#myModal').find('#add_from').remove();
		$('#myModal').find('#add').hide();
		setInterval("fun('/index.php/Admin/Index/content')", 2000); 
	},'json');
}

// 编辑页面
function edit(id){
	$('#myModal').modal({
		remote:'index.php?s=Admin/Index/edit&id='+id
	}).on('hidden.bs.modal',function(){
		location.reload();
	});
}
// 修改处理
function edit_click(){
	$('#add').attr("disabled","disabled");
	var formdata=$('#add_from').serializeArray();
	$.post('index.php?s=Admin/Index/dosave',formdata,function(data){
		$('#myModal').find('.modal-header').html(data);
		$('#myModal').find('#add_from').remove();
		$('#myModal').find('#add').hide();
		setInterval("fun('/index.php/Admin/Index/content')", 2000); 
	},'json');
}
// 删除联系人
function del(id){
	a=confirm('警告：确认删除该产品?');
	if(a){
		$.get('index.php?s=Admin/Index/delete',{id:id},function(data){
			$('#myModal').find('.modal-body').html(data);
			setInterval("fun('/index.php/Admin/Index/content')", 2000); 
			$('#myModal').modal().on('hidden.bs.modal',function(){
				location.reload();
			});
		},'json');
	}
}

// 批量操作
function deletes(){
	var id = [];
	$('input[name="box[]"]:checked').each(function(){
		id.push($(this).val());
	});
	if(id.length > 0){
		if(confirm('警告：确定要删除选中的产品吗？')){
			$.post('index.php?s=Admin/Index/dealCus',{id:id},function(data){
				$('#myModal').find('.modal-body').html(data);
				setInterval("fun('/index.php/Admin/Index/content')", 2000); 
				$('#myModal').modal().on('hidden.bs.modal',function(){
					location.reload();
				});
			},'json');
		}
	}else{
		alert('请选择要操作的产品');
	}
}

// 显示/隐藏字段
function hide_show_field(obj){
	var type = $(obj).data('type');
	var num = $(obj).data('num');
	if(type == 'open'){
		$(obj).data('type','close');
		$('.colum_'+num).css({'display':'none'});
		$(obj).find('.glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-remove');
	}else{
		$(obj).data('type','open');
		$('.colum_'+num).css({'display':''});
		$(obj).find('.glyphicon').removeClass('glyphicon-remove').addClass('glyphicon-ok');
	}
}

function show_hidde(name){
	var obj = $('.'+name);
	obj.finish();
	obj.toggle();
}
function show_hidde2(name){
	var obj = $('.'+name);
	obj.finish();
	obj.show();
}
function hidde_div(name){
	var obj = $('.'+name);
	obj.finish();
	obj.hide();				
}
function select_show_hidde(n,str){
	if($(".search-div")[n].style.display=="none"){
			ajax_url("",str,n);
	}
	$($(".search-div")[n]).toggle();	
}
function select_ul_search(n,str){
	var search_val = $(".brand-name")[n].value;
	if(search_val != null || search_val != ""){
		ajax_url(search_val,str,n);
	}
}
function ajax_url(val,str,n){
	var xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			 searchSelectRespond(xmlhttp.responseText,n);
		}
	}
	xmlhttp.open("GET",str+"/val/"+val,true);
	xmlhttp.send();
}
function searchSelectRespond(result_str,n){
	result = JSON.parse(result_str);
	var html = "";
	for(var i=0; i<result.length; i++){
		html += "<li onclick='add_select_search(this,"+n+")' data_id="+result[i].cat_id+" >"+result[i].cat_name+"</li>";
	
	}
	if(result.length  == 0){
		$('.from-search')[n].value = 0;
	}
	$('.search-ul')[n].innerHTML=html;
}
function add_select_search(e,n){
	var date_id = e.getAttribute("data_id");
	var date_name = e.innerHTML;
	$(".from-search")[n].value = date_id;
	$(".select-show")[n].innerHTML = date_name;
	$(".brand-name")[n].value = date_name;
	$($(".search-div")[n]).toggle();
}

// ajax 获取城市
function changeReg(obj,type){
	var id = $(obj).val();
	if(id){
		$.post('index.php?s=Admin/Index/ajaxx_region',{id:id,type:type},function(data){
			var html = '';
			var html2 = '';
			if(type == 'pro'){
				for(var i=0;i<data.region.length;i++){
					html += '<option value="'+data.region[i].region_id+'">'+data.region[i].region_name+'</option>';
				}
				for(var i=0;i<data.region_sub.length;i++){
					html2 += '<option value="'+data.region_sub[i].region_id+'">'+data.region_sub[i].region_name+'</option>';
				}
				$('select[name="city"]').html(html);
				$('select[name="district"]').html(html2);
			}
			if(type == 'city'){
				for(var i=0;i<data.region.length;i++){
					html2 += '<option value="'+data.region[i].region_id+'">'+data.region[i].region_name+'</option>';
				}
				$('select[name="district"]').html(html2);
			}
		},'json');
	}
}