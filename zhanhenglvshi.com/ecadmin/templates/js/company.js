// JavaScript Document

//  跳转方法
function fun(url) {
	window.location.href = url; 
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
			var num = $('input[name="box[]"]:checked').length;
			$('#order_num').text(num);
		}else{
			$('input[name="box[]"]').each(function(i,e){
				$(e)[0].checked = false;
			});
			var num = $('input[name="box[]"]:checked').length;
			$('#order_num').text(num);
		}
	}
}

// 统计已选中多选框个数
function sumCheckbox(){
	var num = $('input[name="box[]"]:checked').length;
	$('#order_num').text(num);
}

// 添加页面
function add_cus(){
	$('#myModal').modal({
		remote:'index.php?s=Admin/Company/add'
	}).on('hidden.bs.modal',function(){
		location.reload();
	});
}

// 提交添加表单
function add_onclick(){
	var formdata=$('#add_from').serializeArray();
	$.post('index.php?s=Admin/Company/doadd',formdata,function(data){
		$('#myModal').find('.modal-header').html(data);
		$('#myModal').find('#add_from').remove();
		$('#myModal').find('#add').hide();
		setInterval("fun('/index.php?s=Admin/Company/lists')", 2000); 
	},'json');
}

//编辑页面
function edit(id){
	$('#myModal').modal({
		remote:'index.php?s=Admin/Company/edit&id='+id
	}).on('hidden.bs.modal',function(){
		location.reload();
	});
}

// 提交编辑表单
function edit_click(){
	var formdata=$('#add_from').serializeArray();
	$.post('index.php?s=Admin/Company/dosave',formdata,function(data){
		$('#myModal').find('.modal-header').html(data);
		$('#myModal').find('#add_from').remove();
		$('#myModal').find('#add').hide();
		setInterval("fun('/index.php?s=Admin/Company/lists')", 2000); 
	},'json');
}

//删除公司
function del(id){
	a=confirm('警告：确认删除该公司吗?');
	if(a){
		$.get('index.php?s=Admin/Company/delete',{id:id},function(data){
			$('#myModal').find('.modal-body').html(data);
			$('#myModal').find('#add').hide();
			setInterval("fun('/index.php?s=Admin/Company/lists')", 2000); 
			$('#myModal').modal().on('hidden.bs.modal',function(){
				location.reload();
			});
		},'json');
	}
}

// 批量删除公司
function deletes(type){
	var id = [];
	$('input[name="box[]"]:checked').each(function(){
		id.push($(this).val());
	});
	if(id.length > 0){
		if(confirm('警告：确定要删除把选中公司吗？')){
			$.post('index.php?s=Admin/Company/dealCus',{id:id},function(data){
				$('#myModal').find('.modal-body').html(data);
				setInterval("fun('/index.php?s=Admin/Company/lists')", 2000); 
				$('#myModal').modal().on('hidden.bs.modal',function(){
					location.reload();
				});
			},'json');
		}
	}else{
		alert('请选择要操作的公司');
	}
}

// 添加分类页面
function add_cat(){
	$('#myModal').modal({
		remote:'index.php?s=Admin/Company/add_cat'
	}).on('hidden.bs.modal',function(){
		location.reload();
	});
}

// 提交分类添加表单
function cat_click(){
	var formdata=$('#add_from').serializeArray();
	$.post('index.php?s=Admin/Company/do_add_cat',formdata,function(data){
		$('#myModal').find('.modal-header').html(data);
		$('#myModal').find('#add_from').remove();
		$('#myModal').find('#add').hide();
		setInterval("fun('/index.php?s=Admin/Company/lists')", 2000); 
	},'json');
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
	xmlhttp.open("GET","/index.php?s=Admin/Company/ajax_staff"+str+"/val/"+val,true);
	xmlhttp.send();
}
function searchSelectRespond(result_str,n){
	result = JSON.parse(result_str);
	var html = "";
	for(var i=0; i<result.length; i++){
		html += "<li onclick='add_select_search(this,"+n+")' data_id="+result[i].cat_id+" >"+result[i].cat_name+"</li>";
	
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
		$.post('index.php?s=Admin/Company/ajaxx_region',{id:id,type:type},function(data){
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