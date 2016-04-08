#用户行为习惯监测
## 1.需求分析

为方便平台管理者方便获取并汇总个CF站点的使用情况，为满足这一需求我们团队决定开发一个超级平台实现信息汇总。该平台主要包括以下功能：

1. 对各个用户的访问情况进行记录，需要记录的信息为访客姓名、访客人员类型（学生／老师）、访客IP、访客所在地、访客访问的站点、访客使用的何种浏览器、访客若使用IE浏览器则记录IE浏览器版本信息、访问时间、访问时长、退出方式（自主退出／自动退出）。
2. 对用户访问站点的操作情况进行记录汇总，需要记录的信息为用户访问了哪个模块、用户进行了哪种操作（如添加／修改／查询／导出／关注等）、用户在特定模块进行的操作类型、模块访问时间。

## 2.功能构想
1. 在各个站点添加**(base)**功能模块，用于其站点访问数据的收集，并向汇总平台发送收集数据
2. 建立汇总管理平台**(summary)**，收集各CF站点发来的信息并在系统界面以图表的方式进行显示。汇总管理平台包括以下功能：
      - 综合浏览量汇总API （所有页面点击访问总数）
      - 访客信息汇总API
      - 访问操作统计API
        - 忠诚度统计（统计在指定时间段内访问者访问的次数）
        - 访问深度 （统计在指定时间段内每次访问的模块数。）
      - 各模块中所作操作情况API
      - 各站点中模块访问情况 API

## 3.功能描述
![image](file:///Users/xutongkun/Desktop/示意图.png)


## 4.数据结构
###base_point--对于一个用户来说一次登录的信息
### base_point表的属性应包含
   - user `object`
   - sid `string`
   - address `string: match ip role`
   - browser `string: browser data`
   - dtstart `int(11): unix time`
   - dtend `int(11): unix time`
   - signout_way `int`
   
### base_action--每一次用户操作的信息(对哪个模块进行了那种操作)  
* 操作类型action主要包括：添加、修改、删除、查询、预约、导出、打印 
* 模块类型action_module比如：people role labs equipments ...

### base_action表的属性应包含
   - action `string`
   - module `string`
   - ctime `int(11) unix time`
   - point `object -> base_point`

### base_point表和base_action表是一对多的关系

-------------------------------------------
    
## 汇总管理平台 __summary__

### 数据结构
##### base表的属性应该包含
   - source `string`
   - source_name `string`	
   - uid `int`
   - bid `远程服务器的point id`
   - gapper_id `int`
   - umember `string`
   - address `string: match ip role`
   - city `string`
   - province `string`
   - os_type `string`
   - browser `string`
   - version `string`
   - dtstart `int(11): unix time`
   - keeptime `int(11): unix time`	
   - signout_way `int`
   
##### base/action表的属性应该包含	
   - action `string`
   - module `string`
   - ctime `int(11) unix time`
   - source `string`
   － uid `int`
   - bid `object -> base_point`

	
### API请求
namespace:  `Summary`

##### Summary.Base.creatPoint(array data)
* 接口说明: 创建新的访问信息记录
* 参数形式:

			{
				'source' : '站点唯一标示',
				'source_name' : '站点名称‘,
				'bid' : 'data 远程 id '	,
				'uid' : '用户id',
				'gapper_id' : '在用户组的id',
				'umember' : '用户角色',
				'address' : '用户登录的IP',
				'province' : '用户所在省份',
				'city' : '用户登录城市‘,
				'os' : '用户操作系统',
				'browser' : '用户使用浏览器',
				'version' : '浏览器版本',
				'dtstart' : '登录时间',
				'dtend' : '登出时间',
				'keeptime' : '会话保持时间',	
				'logout_way' : '登出方式',
					
			}


##### Summary.Base.creatAction(array data)
* 接口说明: 创建新的操作记录
* 参数形式:

			{
				'source' : '站点唯一表示',
				'uid' : '用户ID',
				'gapper_id' : 'gapper id',
				'bid' : 'data 远程 id',
				'action' : '操作种类',
				'module' : '操作所在模块‘,
				'ctime' : '操作开始时间',		
			}





   

  
  

   
