(function(global) {
	// body...
	global.in_array = function(arr, value) {
	            return arr.indexOf(value) !== -1;
	        };
	global.delCloneNodeId = function(cloneNode) {
		if(typeof cloneNode == "undefined") {
		    return ;
		}
		function getPropertyPart(value, preg) {
			var part = value.replace(preg, "");
			return part;
		}
		this.findMaxNumPart = function(tag, head, prop = "id") {
			var max = 0;
			$(document).find(tag+"["+prop+"^='"+head+"']").each(function(index, ele){
				var value = $(ele).attr(prop);
				var part = getPropertyPart(value, /[A-Z|a-z]+/);
				if(part > max) {
					max = part;
				}
			});
			return max;
		}
		this.propertyUniq = function(node, property) {
			var white_list = ["edit-toolbar", "page", "open"];
		    var value = $(cloneNode).attr(property);
		    if(typeof value != "undefined" && !in_array(value, white_list)) {
		        var charPart = getPropertyPart(value, /\d+/);
		        var numPart = getPropertyPart(value, /[A-Z|a-z]+/);
		        var tagName = node[0].tagName.toLowerCase();
		        var maxNum = this.findMaxNumPart(tagName, charPart, property);
		        var newValue = charPart + (parseInt(maxNum) + 1);
		        node.attr(property, newValue);
		    }
		}
		this.propertyUniq($(cloneNode), "id");
		this.propertyUniq($(cloneNode), "name");
		$(cloneNode).children().each(function(index, ele) {
		    delCloneNodeId(ele);
		});
	};
	global.uploadImg = function(inputFile, inputText, c, m) {
	    if (isImage(inputFile.val())) {
	        $.ajaxFileUpload({
	            url: 'index.php?c='+c+'&m='+m+'&file_id=' + inputFile.attr("id"), //用于文件上传的服务器端请求地址
	            secureuri: false, //一般设置为false
	            fileElementId: inputFile.attr("id"), //文件上传空间的id属性  <input type="file" id="file" name="file" />
	            dataType: 'json', //返回值类型 一般设置为json
	            success: function(data) //服务器成功响应处理函数
	                {
	                    alert("上传成功");
	                    inputText.attr("value", data.image_url);
	                },
	            error: function(data, status, e) //服务器响应失败处理函数
	                {
	                    alert("上传失败");
	                    alert(e);
	                }
	        });
	    } else {
	        $.messager.alert('信息提示', '文件类型不合法！');
	    }
	};
	global.jqChild = function(ele, index) {
		return $($(ele).children()[index]);
	};
	global.openEdit = function(node) {
		$('#page').html($(node).val());
		var input = $(node);
		$( '#edit-toolbar' ).dialog(
		    {  modal: true,
		       autoOpen: false,
		       width: "500px",
		       close:function() {      input.val($('#page').html());    }    
		    }); 
		$('#edit-toolbar').dialog('open');
	}
})(window)