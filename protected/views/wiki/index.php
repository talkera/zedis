<style>
	img.example{height:100px;display:block;margin:10px auto;}
	h4{margin-top:10px;}
</style>
<div class="widget-content">
	<h2>使用</h2>
	<p>目前只支持svn，计划下一步支持git</p>
	<h4>1.创建项目</h4>
	<p>每个项目要有两个代码库：开发环境代码库（dev）和生产环境代码库（prod）。dev由开发人员使用，prod由系统使用</p>
	<p>开发代码仓库和生产代码仓库可以是两个，如:<br>
		开发：svn://example.com/dev/trunk/cms<br>
		生产：svn://example.com/product/trunk/cms</p>
	<p>也可以是同一个仓库的两个目录，如：<br>
		开发：svn://example.com/main/trunk/cms<br>
		生产：svn://example.com/main/product/cms</p>
	<a href="/static/img/wiki/create.png" target="_blank" title="创建项目">
		点击查看示例图
	</a>

	<h4>2.提交变更</h4>
	<p>每次开发人员将代码提交到dev后，在“提交变更”页面可以看到当前dev与prod代码的差异，可以选择将某些变更的文件提交到prod</p>
	<a href="/static/img/wiki/submit.png" target="_blank" title="提交变更">
		点击查看示例图
	</a>

	<h4>3.部署</h4>
	<p>提交到prod后进入代码部署页，可以选择将当前prod代码全量，或者增量部署到仿真机（beta） <br>
		测试通过后，将beta环境的代码部署到生产环境.</p>
	<a href="/static/img/wiki/product.png" target="_blank" title="部署">
		点击查看示例图
	</a>

	<h4>4.路径标识（markPath）</h4>
	<p>
		创建项目须填markPath。<strong>每个项目的markPath必须唯一。<br>
		</strong>markPath取自 <strong>dev代码库</strong>。	是从svn代码库跟目录到当前项目路径。<br>
		执行svn info 可以看到 Relative URL一项。如:
		<pre>Relative URL: ^/trunk/blog</pre>那么 trunk/blog 就是markPath(去掉前面的^和/)
	</p>
	<p>系统根据路径标识判断出哪些项目有改动<br>
		开发环境的代码每次提交svn后，svn hook请求系统api，提交变更文件列表。<br>
		系统将变更文件的路径与项目的 路径标识（MarkPath） 作匹配，判断哪些新项目有更新。<br>
		同时，系统为每个项目创建工作区，项目工作区以项目的markPath命名，如/trunk/cms的目录为：trunk_cms</p>
</div>
