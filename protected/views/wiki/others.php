<style>
	img.example{height:100px;display:block;margin:10px auto;}
	h4{margin-top:10px;}
</style>
<div class="widget-content">
	<h2>其他</h2>

	<h4>工作目录</h4>
	<p>工作目录中，每个项目下有三个目录：dev prod beta<br>
		dev目录check自开发环境代码仓库<br>
		prod目录check自生产环境代码仓库<br>
		beta目录是仿真机运行的代码，也用于部署到生产环境<br>
		dev目录是更新源，prod目录用于提交更新到生产环境，beta目录用于rsync到产品机<br>
	</p>
	<p>
		开发环境的代码每次提交后，通过svn hook将本次变更信息发送给系统<br>
		系统收到变更信息后，将变更文件的路径与项目的MarkPath做匹配，判断出哪些项目有更新<br>
		对于有更新的项目，系统先update该项目的dev目录，然后将dev目录的文件通过rsync同步到prod目录。<br>
		如果变更信息中包含新增文件或者删除文件，则在rsync后，对prod目录进行svn add和svn delete操作。但是，并不提交！<br>
		然后在prod目录下执行svn status命令，找出变更文件的列表，获取这些列表中的文件在dev目录中上次更新的状态（svn status）：时间、操作人。<br>
		然后将获取到的状态信息保存成变更文件，即“提交变更”页面中看到的文件列表<br>
		在提交变更页面，用户勾选需要提交的文件，系统会在prod目录中将勾选的文件commit到生产环境仓库。<br>
		然后系统会更新beta目录，并通过rsync同步到仿真机（如果有的话）<br>
		仿真机测试通过后，可以进入部署代码的页面，选择将代码全量或增量推送到线上。
	</p>
</div>