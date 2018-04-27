# stools

#### STools 组件相比Tools 包含依赖db的sql操作，这部分功能依赖db组件 含如下方法： ####

1. static Obj( db )  选择数据库同配置文件 （ 默认default ） ,
唯一的静态方法，通常作为前行使用。
返回对象本身的实例化。
2. table( table ) 设置表名 ， 当table等于数组时 设置一组表名
返回值：同上。
3. columns ( columns ) 设置 field ， 当columns 等于数组时 设置一组field ，同table对应。 注： 如果table是一组数据 而此值是一个数据，那么此值将统一与每个table所对应。
返回值：同上。
4. where( where ) 设置where 同上 ， 不同的是 where本身为数组 如 [ id => 1]  ， 对应多个表时可以设置成 [[id=>1],[id=>2]]。
返回值：同上。
5. Page( page , pagesize ) 通过上述设置 直接进行分页查询 示例：

```

        STools::Obj()->table(  'test1' )->where( ['id[<]' => 10 ])->Page($page , 4);
         [
                    'count' => 0,
                    'data' => [],
                    'page' => 1,
                    'pagesize' => 10,
                    'maxpage' => 0,
          ];

```

6. multiPage () 参数与page相同 如下：

```
        STools::Obj()->table( [ 'test1' , 'test2' ])->where( ['id[<]' => 10 ])->multiPage($page , 4);

        [
            'unitC' => [], //各结果集总数 ， 索引数组 循序同 table
            'count' => 0, //结果总数
            'data' => [], 数据
            'page' => 1,
            'pagesize' => 10,
            'maxpage' => 0,
        ];

        注： 由于返回结果是由多个结果集拼接上的 ，在多个表field 不一致时 ， 可以通过 columns 将返回值指定成相同名称。

```


#### MDb 组件内调用 ， 对 db组件进行的二次封装。 ####

```
插入唯一数据
Base\MDb::e()->uniqueInsert('shechem_test1' , [
            'note1' => 'unique2'
            ] , '`note1`='.Base\MDb::e()->quote('unique2'));

  成功返回 id 否则 返回 0
  注： 插入的数据必须被 unique 含盖
```
