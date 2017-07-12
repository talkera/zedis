# zedis
代码部署系统，目前只支持svn，计划下一步支持git。

* 支持代码回滚、仿真环境维护、增量部署
* 提供在线diff功能，支持变更查看
* 自定义进程数多进程部署
* 系统内部自带文档

## 安装

只能在Linux运行，主要用到rsync export等命令

#### 1.环境

Mysql 版本无要求

svn 版本大于 **1.6**

php 版本大于 **5.4**
php5.4开始支持这样的数组[1,2,3] 尽管我已尽量避免使用，但是可能还是用到了。如果没有这个问题，在更低版本的php上运行应该也可以。
php要开放exec函数的使用

#### 2.服务器配置

**Apache**

只需要支持 AllowOverride 。根目录下有.htaccess规则

**Nginx**
```nginx
if ( !-e $request_filename ) {
	rewrite (.*) /index.php last;
}
location ~* ^/protected/.*$ {
	deny all;
}
```

**虚拟主机**

可用IP也可以用域名，但是必须要求是在根目录。如：

```
127.0.0.1/index.php
code.example.com/index.php
```

如果在子目录下，会有访问路径错误

#### 3.安装

导入代码根目录的sql后，直接运行即可看到系统使用的参数。
**务必确认svn、rsync的命令路径，目录的写权限**
如有问题，根据提示找到配置文件。改好后刷新页面，点击安装

#### 4.配置SVN Hook

开发环境的代码每次提交后，通过svn hook将本次变更信息发送给系统，并提醒系统检查变更。这样避免每次手动检查变更，同时有助于系统做出更好的差异判断。

配置dev代码库的post-commit文件。需注意修改**svnlook路径** 、**部署系统域名**

```shell
REPOS="$1"
REV="$2"

SVNLOOK=/usr/local/subversion/bin/svnlook
#注意svnlook路径
CHANGED=`$SVNLOOK changed -r $REV $REPOS`
AUTHOR=`$SVNLOOK author -r $REV $REPOS`
LOG=`$SVNLOOK log -r $REV $REPOS`

curl "http://zedis.example.com/sHook/postCommit/" -d "changed=$CHANGED&author=$AUTHOR&r=$REV&log=$LOG&repos=$REPOS"
```



## 使用

目前只支持svn，计划下一步支持git

#### 1.创建项目
每个项目要有两个代码库：开发环境代码库（dev）和生产环境代码库（prod）。dev由开发人员使用，prod由系统使用

开发代码仓库和生产代码仓库可以是两个，如:

```
开发：svn://example.com/dev/trunk/cms
生产：svn://example.com/product/trunk/cms
```

也可以是同一个仓库的两个目录，如：

```
开发：svn://example.com/main/trunk/cms
生产：svn://example.com/main/product/cms
```


#### 2.提交变更
每次开发人员将代码提交到dev后，在**提交变更**页面可以看到当前dev与prod代码的差异，可以选择将某些变更的文件提交到prod


#### 3.部署
提交到prod后进入代码部署页，可以选择将当前prod代码全量，或者增量部署到仿真机（beta） 
测试通过后，将beta环境的代码部署到生产环境.


#### 4.路径标识（markPath）
创建项目须填markPath。**每个项目的markPath必须唯一。**
markPath取自 **dev代码库**。	是从svn代码库跟目录到当前项目路径。
执行svn info 可以看到 Relative URL一项。如:

```
Relative URL: ^/trunk/blog
```

那么 trunk/blog 就是markPath(去掉前面的^和/)

系统根据路径标识判断出哪些项目有改动.
开发环境的代码每次提交svn后，svn hook请求系统api，提交变更文件列表。
系统将变更文件的路径与项目的 路径标识（MarkPath） 作匹配，判断哪些新项目有更新。
同时，系统为每个项目创建工作区，项目工作区以项目的markPath命名，如trunk/cms的目录为：trunk_cms

#### 其他

初始安装账号密码都是：admin
