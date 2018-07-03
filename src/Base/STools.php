<?php
/**
 * Created by PhpStorm.
 * User: mhx
 * Date: 2017/9/29
 * Time: 10:48
 */

namespace BBear\STools\Base;
use BBear\Tools\Base\Tools;
use MedMy\DbMy as MDB;

/**
 * Class STools
 * @package ATSoft\Tools\Base
 *
 * 这个类依赖 db组件  和 基础类的方法
 * 提供和数据库相关的服务
 *
 */
class STools
{

    //静态变量保存全局实例
    private static $_instance = null;
    private $showError = true;

    public $dbPre = [];
    public $dbTable = [];
    public $dbJoin = [];
    public $dbColumns = [];
    public $dbWhere = [];

    //私有构造函数，防止外界实例化对象
    private function __construct() {
        $this->initV();
    }

    private function initV(){
        $this->dbPre = [0];
        $this->dbTable = [''];
        $this->dbColumns = ['*'];
        $this->dbWhere = [[]];
        $this->dbJoin = [];
    }

    //私有克隆函数，防止外办克隆对象
    private function __clone() {
    }

    //静态方法，单例统一访问入口
    static private function getInstance() {
        if ( !self::$_instance instanceof self) {
            self::$_instance = new self ();
        }
        return self::$_instance;
    }


    /**
     * mhx
     * 说明：设置数据库
     * @param null $dbPre
     * @return STools|null
     * @throws \Exception
     */
    static  function Obj($dbPre = null){
        $dbPre = $dbPre ?? 0;
        $obj = self::getInstance();
        if(is_array($dbPre)){
            if(!Tools::is_indexArray( $dbPre )) throw new \Exception( 'STools where throw : $dbPre must is indexed array!');
            foreach( $obj->dbPre as $k=>$v ){
                $obj->dbPre[$k] = $v;
            }
        }else{
            $obj->dbPre[0] = $dbPre;
        }

        return $obj;
    }

    function table( $table ){

        if(is_array( $table )){
            if(!Tools::is_indexArray( $table )) throw new \Exception( 'STools where throw : $table must is indexed array!');
//            foreach( $table as $k=>$v ){
//                $this->dbTable[$k] = $v;
//            }
            $this->dbTable = $table;
        }else{
            $this->dbTable[0] = $table;
        }

        return $this;
    }

    function join( $join ){
        if( Tools::is_indexArray( $join ) ){
            $this->dbJoin = $join;
        }else{
            $this->dbJoin[0] = $join;
        }
        return $this;
    }

    function columns( $columns = '*' ){

        if( Tools::is_indexArray( $columns ) && Tools::is_indexArray( $columns[0] ) ){
            $this->dbColumns = $columns;
        }else{
            $this->dbColumns[0] = $columns;
        }

        return $this;
    }

    function where( $where = null ){
        if( Tools::is_indexArray( $where ) ){
            foreach( $where as $k=>$v ){
                $this->dbWhere[$k] = $v;
            }
        }else{
            $this->dbWhere[0] = $where;
        }
        return $this;
    }

    public function Page( $page = 1 , $pagesize= 10 , $debug = false){

        $count = MDb::e( $this->dbPre[0] )->count($this->dbTable[0] , $this->dbWhere[0]);

        if($debug){
            echo MDb::e( $this->dbPre[0] )->log() . PHP_EOL;
        }

        $page = Tools::page( $page , $count ,$pagesize);
        $this->dbWhere[0]['LIMIT'] = $page['limit'];

        if( empty( $this->dbJoin ) ){
            $data = MDb::e( $this->dbPre[0] )->select( $this->dbTable[0] ,$this->dbColumns[0] , $this->dbWhere[0] );
        }else{
            $data = MDb::e( $this->dbPre[0] )->select( $this->dbTable[0] , $this->dbJoin[0] ,$this->dbColumns[0] ,$this->dbWhere[0] );
        }


        if($debug){
            echo MDb::e( $this->dbPre[0] )->log() . PHP_EOL;
        }

        return [
            'count' => $count,
            'list' => $data,
            'page' => $page['page'],
            'pagesize' => $page['pagesize'],
            'maxpage' => $page['maxpage'],
        ];
    }

    public function multiPage( $page = 1 , $pagesize= 10 ){
        $count = [];
        foreach( $this->dbTable as $k => $v ){
            $count[$k] = MDb::e( $this->_IfNullFirstVal('dbPre' , $k) )->count($this->dbTable[$k] , $this->_IfNullFirstVal('dbWhere' , $k));
        }
        $multiPage = Tools::multiPage( $page , $count , $pagesize);
        $rData = [];
        foreach( $this->dbTable as $k => $v ){
            if($multiPage['limit'][$k]){
                $where = $this->_IfNullFirstVal('dbWhere' , $k);
                $where['LIMIT'] = $multiPage['limit'][$k];
                $data =  MDb::e( $this->_IfNullFirstVal('dbPre' , $k) )->select( $this->dbTable[$k]  , $this->_IfNullFirstVal('dbColumns' , $k)  , $where );
                $rData = array_merge( $rData , $data);
            }
        }
        return [
            'unitC' => $count,
            'count' => $multiPage['count'],
            'list' => $rData,
            'page' => $multiPage['page'],
            'pagesize' => $multiPage['pagesize'],
            'maxpage' => $multiPage['maxpage'],
        ];
    }

    private function _IfNullFirstVal( $property , $key ){
        return empty($this->$property[$key]) ? $this->$property[0] : $this->$property[$key];
    }

}