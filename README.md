# zedis
���벿��ϵͳ��Ŀǰֻ֧��svn���ƻ���һ��֧��git��

* ֧�ִ���ع������滷��ά������������
* �ṩ����diff���ܣ�֧�ֱ���鿴
* �Զ������̲���

## ��װ

ֻ����Linux���У���Ҫ�õ�rsync export������

#### 1.����

Mysql �汾��Ҫ��

svn �汾���� **1.6**

php �汾���� **5.4**
php5.4��ʼ֧������������[1,2,3] �������Ѿ�������ʹ�ã����ǿ��ܻ����õ��ˡ����û��������⣬�ڸ��Ͱ汾��php������Ӧ��Ҳ���ԡ�
phpҪ����exec������ʹ��

#### 2.����������

**Apache**

ֻ��Ҫ֧�� AllowOverride ����Ŀ¼����.htaccess����

**Nginx**
```nginx
if ( !-e $request_filename ) {
	rewrite (.*) /index.php last;
}
location ~* ^/protected/.*$ {
	deny all;
}
```

**��������**

����IPҲ���������������Ǳ���Ҫ�����ڸ�Ŀ¼���磺

```
127.0.0.1/index.php
code.example.com/index.php
```

�������Ŀ¼�£����з���·������

#### 3.��װ

��������Ŀ¼��sql��ֱ�����м��ɿ���ϵͳʹ�õĲ�����
**���ȷ��svn��rsync������·����Ŀ¼��дȨ��**
�������⣬������ʾ�ҵ������ļ����ĺú�ˢ��ҳ�棬�����װ

#### 4.����SVN Hook

���������Ĵ���ÿ���ύ��ͨ��svn hook�����α����Ϣ���͸�ϵͳ��������ϵͳ���������������ÿ���ֶ��������ͬʱ������ϵͳ�������õĲ����жϡ�

����dev������post-commit�ļ�����ע���޸�**svnlook·��** ��**����ϵͳ����**

```shell
REPOS="$1"
REV="$2"

SVNLOOK=/usr/local/subversion/bin/svnlook
#ע��svnlook·��
CHANGED=`$SVNLOOK changed -r $REV $REPOS`
AUTHOR=`$SVNLOOK author -r $REV $REPOS`
LOG=`$SVNLOOK log -r $REV $REPOS`

curl "http://zedis.example.com/sHook/postCommit/" -d "changed=$CHANGED&author=$AUTHOR&r=$REV&log=$LOG&repos=$REPOS"
```



##ʹ��

Ŀǰֻ֧��svn���ƻ���һ��֧��git

####1.������Ŀ
ÿ����ĿҪ����������⣺������������⣨dev����������������⣨prod����dev�ɿ�����Աʹ�ã�prod��ϵͳʹ��

��������ֿ����������ֿ��������������:

```
������svn://example.com/dev/trunk/cms
������svn://example.com/product/trunk/cms
```

Ҳ������ͬһ���ֿ������Ŀ¼���磺

```
������svn://example.com/main/trunk/cms
������svn://example.com/main/product/cms
```


####2.�ύ���
ÿ�ο�����Ա�������ύ��dev����**�ύ���**ҳ����Կ�����ǰdev��prod����Ĳ��죬����ѡ��ĳЩ������ļ��ύ��prod


####3.����
�ύ��prod�������벿��ҳ������ѡ�񽫵�ǰprod����ȫ���������������𵽷������beta�� 
����ͨ���󣬽�beta�����Ĵ��벿����������.


####4.·����ʶ��markPath��
������Ŀ����markPath��**ÿ����Ŀ��markPath����Ψһ��**
markPathȡ�� **dev�����**��	�Ǵ�svn������Ŀ¼����ǰ��Ŀ·����
ִ��svn info ���Կ��� Relative URLһ���:

```
Relative URL: ^/trunk/blog
```

��ô trunk/blog ����markPath(ȥ��ǰ���^��/)

ϵͳ����·����ʶ�жϳ���Щ��Ŀ�иĶ�.
���������Ĵ���ÿ���ύsvn��svn hook����ϵͳapi���ύ����ļ��б�
ϵͳ������ļ���·������Ŀ�� ·����ʶ��MarkPath�� ��ƥ�䣬�ж���Щ����Ŀ�и��¡�
ͬʱ��ϵͳΪÿ����Ŀ��������������Ŀ����������Ŀ��markPath��������trunk/cms��Ŀ¼Ϊ��trunk_cms

#### ����

��ʼ��װ�˺����붼�ǣ�admin