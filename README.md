WIFI-Protocol
===============

WIFI-Protocol 是提供WIFI以Web Portal方式提供身份验证，授权，记帐和审计服务
 + 认证、授权、计费、审计（authentication、authorization、accounting、auditing）
 + 实现Portal1.0+2.0协议
 + 实现Radius1.0+2.0协议

> 的运行环境要求LAMP/LNMP（Linux>=6.4 Apache>=2.2 Nginx>=1.14 Mysql>=5.6 PHP>=5.4）
详细开发文档参考 [AAAA-WIFI完全开发手册](https://gitee.com/fyxtw/aaaa/wikis)

<hr>
## 目录结构

初始的目录结构如下：

~~~
wifiprotocol  目录名称（扩展类库）
├─portal           portal协议目录
│  ├─xxx.php            xxx文件
│  └─xxx.php            xxx文件
│
├─radius           radius协议目录
│  ├─xxx.php            xxx文件
│  └─xxx.php            xxx文件
│
├─library            工具类库目录
│  ├─xxx.php            xxx文件
│  └─xxx.php            xxx文件
│
├─extend                扩展类库目录
├─Portal.php            Portal协议文件
├─Radius.php            Radius协议文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件

~~~

## 命名规范

遵循PSR-2命名规范和PSR-4自动加载规范，并且注意如下规范：

### 目录和文件

*   目录不强制规范，驼峰和小写+下划线模式均支持；
*   类库、函数文件统一以`.php`为后缀；
*   类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致；
*   类名和类文件名保持一致，统一采用驼峰法命名（首字母大写）；

### 函数和类、属性命名

*   类的命名采用驼峰法，并且首字母大写，例如 `User`、`UserType`，默认不需要添加后缀，例如`UserController`应该直接命名为`User`；
*   函数的命名使用小写字母和下划线（小写字母开头）的方式，例如 `get_client_ip`；
*   方法的命名使用驼峰法，并且首字母小写，例如 `getUserName`；
*   属性的命名使用驼峰法，并且首字母小写，例如 `tableName`、`instance`；
*   以双下划线“__”打头的函数或方法作为魔法方法，例如 `__call` 和 `__autoload`；


## 参与开发

请参阅 [AAAA-WIFI 核心框架包](https://gitee.com/fyxtw/aaaa)。

## 版权信息

AAAA-WIFI遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2019-2020 by AAAA-WIFI (https://gitee.com/fyxtw/aaaa)

All rights reserved。

AAAA-WIFI® DTYPT(bober)。

更多细节参阅 [LICENSE.txt](LICENSE.txt)
