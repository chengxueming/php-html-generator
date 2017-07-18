(function(global) {
	// body...
	window.delCloneNodeId = function(cloneNode) {
		if(typeof cloneNode == "undefined") {
		    return ;
		}
		this.propertyAutoAdd = function(property) {
		    var value = $(cloneNode).attr(property);
		    if(typeof value != "undefined") {
		        var charPart = value.replace(/\d+/, "");
		        var numPart = value.replace(/[A-Z|a-z]+/,"");
		        console.log(charPart + (parseInt(numPart) + 1));
		        $(cloneNode).attr(property, charPart + (parseInt(numPart) + 1));
		    }
		}
		this.propertyAutoAdd("id");
		this.propertyAutoAdd("name");
		$(cloneNode).children().each(function(index, ele) {
		    delCloneNodeId(ele);
		});
	};
	window.uploadImg = function(inputFile, inputText, c, m) {
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
	window.jqFirstChild = function(ele) {
		return $($(ele).children()[0]);
	}
})(window)