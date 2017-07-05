<style>
	img.example{height:100px;display:block;margin:10px auto;}
	h4{margin-top:10px;}
</style>
<div class="widget-content">
	<h2>安装</h2>
	<p>只能在Linux运行，主要用到rsync export等命令</p>
	<h4>1.环境</h4>
	<p>Mysql 版本无要求</p>
	<p>svn 版本大于 <strong>1.6</strong></p>
	<p>
		php 版本大于 <strong>5.4</strong><br>
		php5.4开始支持这样的数组[1,2,3] 尽管我已尽量避免使用，但是可能还是用到了。如果没有这个问题，在更低版本的php上运行应该也可以。<br>
		php要开放exec函数的使用
	</p>

	<h4>2.服务器配置</h4>
	<p><strong>Apache</strong>只需要支持 AllowOverride 根目录下有.htaccess规则</p>
	<p>
		<strong>Nginx</strong><br>
		<pre>
		if ( !-e $request_filename ) {
			rewrite (.*) /index.php last;
		}
		location ~* ^/protected/.*$ {
			deny all;
		}</pre>
	</p>
	<p>
		<strong>虚拟主机</strong><br/>
		可用IP也可以用域名，但是必须要求是在根目录。如：
		<pre>
		127.0.0.1/index.php
		code.example.com/index.php</pre>
		如果在子目录下，可能有路径错误
	</p>

	<h4>3.安装</h4>
	<p>导入代码根目录的sql后，直接运行即可看到系统使用的参数。<br>
		<strong>务必确认svn、rsync的命令路径，目录的写权限</strong><br/>
		如有问题，根据提示找到配置文件。改好后刷新页面，点击安装</p>
	<a href="/static/img/wiki/install.png" target="_blank" title="安装">
		点击查看示例图
	</a>

	<h4>4.配置SVN Hook</h4>
	<p>开发环境的代码每次提交后，通过svn hook将本次变更信息发送给系统，并提醒系统检查变更。这样避免每次手动检查变更，同时有助于系统做出更好的差异判断。</p>
	<p>
		配置dev代码库的post-commit文件。需注意修改<strong>svnlook路径</strong><strong>部署系统域名</strong>
	</p>
	<pre>
REPOS="$1"
REV="$2"

SVNLOOK=/usr/local/subversion/bin/svnlook
#注意svnlook路径
CHANGED=`$SVNLOOK changed -r $REV $REPOS`
AUTHOR=`$SVNLOOK author -r $REV $REPOS`
LOG=`$SVNLOOK log -r $REV $REPOS`

curl "http://zedis.example.com/sHook/postCommit/" -d "changed=$CHANGED&author=$AUTHOR&r=$REV&log=$LOG&repos=$REPOS"
	</pre>
</div>
