function autoCompleteGetJSON(id, url, defaultAction){
	$.getJSON(url,function(json){
		$(id).autocomplete({
			minLength: 0,
			delay: 0,
			autoFocus: true,
			source: json,
			select: function( event, ui ) {
				$(id).val( ui.item.value).parent().find(":hidden").val(ui.item.id);
				$(defaultAction).focus();
				return false;
			}
		}).focus() .data("autocomplete");
	});
}

function autoComplete_for_search(id, url, defaultAction, cnt,web_root){
	$.getJSON(url,function(json){
		$("#"+id).autocomplete({
			minLength: 0,
			delay: 0,
			autoFocus: true,
			source: json,
			open: function (event, ui ){
				//$(".parameter-num-input > .select-hide").hide();
				//$("select").hide();
			},
			close: function (event, ui ){
				//$(".parameter-num-input > .select-hide").show();
				//$("select").show();
			},
			select: function( event, ui ) {
				$("#"+id).val( ui.item.value);
				$('[name='+cnt+'-name]').val(ui.item.id);
				$(defaultAction).focus();

				//var all_child = "."+id+"-child";
				//var child = ".oneparameter"+cnt+"-child"; 

				//$(all_child).hide();
				//$(child).show();
				//$('.oneparameter'+cnt+'-child').trigger('click');

				//根据类别和属性显示最大值最小值
				var min_name = 'input[name='+cnt+'-min]';
				var max_name = 'input[name='+cnt+'-max]';
				var cat = $(':input[name='+cnt+'-ablity]').val();
				var par = $('input[name='+cnt+'-name]').val();
				$.get(web_root+"parameter/get_scope?parameter="+par+"&&category="+cat, function(data){
					data = eval("("+data+")");
					$(min_name).val(data.min);
					$(max_name).val(data.max);
				});

				return false;
			}
		}).focus() .data("autocomplete");
	});
}


function checkWindowHeight(){
	var windowHeight = $(window).height(); //浏览器当前窗口可视区域高度
	var documentHeight = $(document.body).outerHeight(true);//浏览器当前窗口文档body的总高度 包括border padding margin
	if(documentHeight < windowHeight){
		$('footer').css('position', 'fixed').css('bottom', 0);
	}else{
		$('footer').css('position', 'static');
	}
}

function loading(){
	if(arguments[0] != true){
		$(".loading").ajaxStart(function(){
			parentWidth = $(this).parent().width();
			selfWidth = $(this).width();
			marginLeft = (parentWidth - selfWidth) / 2;
			$(this).css('margin-left', marginLeft).show();
		});
	}
	
	$(".loading").ajaxStop(function(){
		$(this).hide();
	});
}

function showPage(page, obj){
	//$(obj).parentsUntil('form').find("[name=page]").val(page);
	$("[name=page]").val(page);
	var id = $(obj).parent().parent().parent().parent().attr("id");
	if(id == 'search-result-a')
		$("#sub-a").trigger('click');
	else if(id == 'search-result-b')
		$("#sub-b").trigger('click');
	else $("#sub").trigger('click');
	//清空page，防止查询新关键字时的Bug
	$("[name=page]").val('');
}



