<style>
	#btnWrap{text-align:right;padding-bottom:30px;}
	.table th, .table td{text-align:center;}
	.status{color:blue;font-weight:bold;}
	.status.M{color:#ff6600;}
	.status.A{color:#660099;}
	.status.D{color:red;}
	tr.checked td{background-color:#fcf8e3!important;}
</style>
<div class="widget">
	<div class="widget-header">
		<h3 class="">提交变更</h3>
	</div>
	<div class="widget-content">
		<?php if($msg):?>
			<div class="alert alert-block">
				服务器svn信息有其他信息未识别出，但不影响您的使用，请将信息反馈给管理员，谢谢！<br>
				<?php echo $msg;?>
			</div>
		<?php endif;?>
		<?php if($error):?><div class="alert alert-danger"><?php echo $error;?></div><?php endif;?>
		<div class="alert alert-block" id="logWrap" style="display:none;"></div>

		<form id="list_form" name="list_form" method="POST">
			<div id="btnWrap">
				<?php if(User::hasRights($project['id'],'beta')||User::hasRights($project['id'],'prod')): ?>
				<a class="btn left" href="/submit/product/pId/<?php echo $project['id'];?>">部署代码</a>
				<?php endif; ?>
				<button class="btn btn-warning" type="button" id="doDiffProject">检查变更</button>
				<button class="btn btn-primary" type="button" onclick="check_list(); return false;" id="submit1">确认提交</button>
			</div>

			<div style="color:red;">
				代码注释(必选)
			</div>
			<textarea id="comment" name="comment" style="width:100%;box-sizing:border-box;"></textarea>

			<table class="table table-striped" style="text-align:center">
				<tr>
					<th><input type="checkbox" id="check_all" name="check_all" ></th>
					<th>文件名称</th>
					<th>更改类型</th>
					<th>最后修改人</th>
					<th>最后修改版本</th>
					<th>操作</th>
					<th>最后修改时间</th>
				</tr>
				<?php foreach($fList as $k=>$item): ?>
				<tr>
					<td>
						<input type="checkbox" name="file[<?php echo $k; ?>][file]" value="<?php echo $item['file'];?>"/>
						<input type="hidden" name="file[<?php echo $k; ?>][action]" value="<?php echo $item['action'];?>">
						<input type="hidden" name="file[<?php echo $k; ?>][author]" value="<?php echo $item['author'];?>">
						<input type="hidden" name="file[<?php echo $k; ?>][version]" value="<?php echo $item['version'];?>">
					</td>
					<td style='text-align:left;padding-left:3%'><?php echo $item['file'];?></td>
					<td class="status <?php echo $item['action'];?>"><?php echo $item['action'];?></td>
					<td><?php echo $item['author'];?></td>
					<td><?php echo $item['version'];?></td>
					<td>
						<?php if($item['action']=='M'):?>
						<a target="_blank" href="/diff/index/?pId=<?php echo $project['id'];?>&f=<?php echo $item['fileUEncode'];?>">Diff</a>
						<?php endif; ?>
					</td>
					<td><?php echo $item['date'];?></td>
				</tr>
				<?php endforeach; ?>
			</table>
			<input type="hidden" id="pId" name="pId" value="<?php echo $project['id'];?>">

			<br/>
			<div class="right">
				<?php if($user=='all'):?>
				<a class="btn" href="/submit/changed/pId/<?php echo $project["id"]?>">仅显示自己</a>
				<?php else:?>
				<a class="btn" href="/submit/changed/type/all/pId/<?php echo $project["id"]?>">显示全部</a>
				<?php endif;?>
				<button class="btn btn-primary" type="button" onclick="check_list(); return false;" id="submit2">确认提交</button>
			</div>
			<br/>
		</form>
	</div>
</div>
<script>
	$(function(){
		$("#check_all").click(function(){
			if($("#check_all").attr('checked')) $("[name^='file[']").attr("checked",'checked');
			else $("[name^='file[']").removeAttr('checked');
			$("input[type=checkbox]").each(function(){
				if(this.checked) $(this).parent('td').parent('tr').addClass('checked');
				else $(this).parent('td').parent('tr').removeClass('checked');
			});
		});
		$("input[type=checkbox]").each(function(){
			$(this).click(function(){
				$("input[type=checkbox]").each(function(){
					if(this.checked) $(this).parent('td').parent('tr').addClass('checked');
					else $(this).parent('td').parent('tr').removeClass('checked');
				})
			})
		});
	});
	function check_list()
	{
		var sFlag = false;
		var post_str = '';
		var files = new Array();
		$("[name^='file[']").each(function(){
			if(this.checked){
				sFlag = true;
				return false;//停止遍历
			}
		});

		if($('#comment').val() == ''){
			alert('请添加注释!');
		}else if(!sFlag){
			alert('请选择文件!');
		}else{
			$('#submit1').attr('onclick', '').html('正在提交...');
			$('#submit2').attr('onclick', '').html('正在提交...');
			$('#list_form').submit();
		}
	}
var doing = false;
$('#doDiffProject').click(function(){
	if(doing){alert('检查中，请稍后');return;}
	$.ajax({
		url:'/hook/doDiff/pId/<?php echo $project["id"]?>',
		dataType:'json',
		success:function(data){
			doing = false;
			if(typeof data != 'object'){alert('未知错误！'); return;}
			alert(data.msg);
			if(data.status==0) location.reload();
			else $('#logWrap').html('<pre>'+data.data.log+'</pre>').show();
		},error:function(){
			alert('错误的请求！');
			doing = false;
		}
	});
});
</script>