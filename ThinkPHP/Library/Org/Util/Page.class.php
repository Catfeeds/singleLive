<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | 蜂蝶旋舞: 492663515 <492663515@qq.com>
// +----------------------------------------------------------------------
namespace Org\Util;
class Page {
 //dump($dataPacket);die;
    public $dataPacket; // 数据包
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $totalRows;// 总数据行数
    public $totalPages; // 分页总页面数
    public $parameter; // 分页跳转时要带的参数
    public $rollPage = 5;// 分页栏每页显示的页数
    public $lastSuffix = true; // 最后一页是否显示总页数

    private $nowPage = 1;// 当前的页数
    private $p       = 'p'; //分页参数名
    private $url     = ''; //当前链接URL

    // 分页显示定制
    private $config  = array(
        'header' => '共 %TOTAL_ROW% 条记录',
        'prev'   => '<<',
        'next'   => '>>',
        'last'   => '尾页',
        'first'  => '首页',
        'theme'  => '%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE% %JUMP%',
    );
    //分页标记附加操作
    private $class  = array(
        'header'     => 'class="rows"',//记录条数
        'first'      => 'class="first"',//首页
        'upRow'      => 'class="prev"',//上一页
        'linknum'    => 'class="num"',//1 2 3 4
        'nowpage'    => 'class="current"',//当前页
        'downRow'    => 'class="next"',//下一页
        'last'       => 'class="end"',//尾页
        'no_upRow'   => '',//当没有上一页的时候显示什么，默认空
        'no_downRow' => '',//当没有下一页的时候显示什么，默认空
    );
    /**
     * 构造函数
     * @param array   $dataPacket 二维数组数据包
     * @param integer $listRows   每页显示的行数
     * @param array   $parameter  分页跳转的参数
     */
    public function __construct($dataPacket,$listRows=20,$parameter = array(),$varPage=""){
        C('VAR_PAGE') && $this->p = C('VAR_PAGE'); //设置分页参数名称
        $this->p = $varPage ? $varPage : $this->p ;
        
        if (is_array($dataPacket)) {
            $this->dataPacket = $dataPacket;//设置数据包
            $this->totalRows  = count($dataPacket);//设置总数据行数
        }else if (!is_array($dataPacket)&&isset($dataPacket)) {
            $this->totalRows  = $dataPacket;//设置总数据行数
        }
        $this->listRows  = $listRows;
        $this->parameter = empty($parameter) ? $_GET : $parameter;
        $this->nowPage   = empty($_GET[$this->p]) ? 1 : intval($_GET[$this->p]);
        $this->nowPage   = $this->nowPage>0 ? $this->nowPage : 1;
        $this->firstRow  = $this->listRows * ($this->nowPage - 1);
    }
    public function data(){
        $data = array_slice($this->dataPacket,$this->firstRow,$this->listRows);
        return $data;
    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }
    public function setClass($name,$value) {
        if(isset($this->class[$name])) {
            $this->class[$name] = $value;
        }
    }
    /**
     * 生成页码，替换原始URL
     * @param  integer $page 页码
     * @return string
     */
    private function url($page){
        return str_replace(urlencode('[replace]'), $page, $this->url);
    }
    /**
     * 组装分页连接信息
     * @return [type] [description]
     */
    public function show() {

        if(0 == $this->totalRows) return '';

        /* 生成URL */
        $this->parameter[$this->p] = '[replace]';
        $this->url = U(ACTION_NAME, $this->parameter);
        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }

        /* 计算分页临时变量 */
        $now_cool_page      = $this->rollPage/2;
        $now_cool_page_ceil = ceil($now_cool_page);
        $this->lastSuffix /*&& $this->config['last']*/ = $this->totalPages;

        //上一页
        $up_row  = $this->nowPage - 1;
        $up_page = $up_row > 0 ? '<a href="'.$this->url($up_row).'" class="layui-laypage-prev">上一页</a>' : '';

        //下一页
        $down_row  = $this->nowPage + 1;
        $down_page = ($down_row <= $this->totalPages) ? '<a href="' . $this->url($down_row) . '" class="layui-laypage-next">下一页</a>' :'';

        //第一页
        $the_first = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1){
            $the_first = '<a href="' . $this->url(1) . '" class="laypage_first" title="首页">首页</a><span>…</span>';
        }

        //最后一页
        $the_end = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages){
            $the_end = '<span>…</span><a href="' . $this->url($this->totalPages) . '" class="layui-laypage-last" title="尾页">末页</a>';
        }

        //数字连接
        $link_page = "";
        for($i = 1; $i <= $this->rollPage; $i++){
            if(($this->nowPage - $now_cool_page) <= 0 ){
                $page = $i;
            }elseif(($this->nowPage + $now_cool_page - 1) >= $this->totalPages){
                $page = $this->totalPages - $this->rollPage + $i;
            }else{
                $page = $this->nowPage - $now_cool_page_ceil + $i;
            }
            if($page > 0 && $page != $this->nowPage){

                if($page <= $this->totalPages){
                    $link_page .= '<a '.$this->class['linknum'].' href="' . $this->url($page) . '">' . $page . '</a>';
                }else{
                    break;
                }
            }else{
                if($page > 0 && $this->totalPages != 1){
                    $link_page .= '<a '.$this->class['nowpage'].'>' . $page . '</a>';
                }
            }
        }
          /*跳转分页实验*/
        $jump_page = '';
        $jump_page .= '<span class="layui-laypage-total">到第<input type="number" min="1" onkeyup="this.value=this.value.replace(/\D/, \'\');" class="layui-laypage-skip" id="inp" ';
        $jump_page .= 'onkeydown="javascript:if(event.keyCode==13){var page=(this.value>'.$this->totalPages.')?'.$this->totalPages.':this.value;';
        $jump_page .= "location='".$this->url("'+page+'")."'";
        $jump_page .= '}" value="'.$this->nowPage.'" >页';
        $jump_page .= '<button class="layui-laypage-btn" id="go" type="button" onclick="';
        $jump_page .= "javascript:var val=document.getElementById('inp').value;var page =(val>".$this->totalPages.")?".$this->totalPages.":val;
                    location='".$this->url("'+page+'")."'";
        $jump_page .='">确定</button></span>';
        $header = '<span '.$this->class['header'].'>'.$this->config['header'].'</span>';





        //替换分页内容
        $page_str = str_replace(
            array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%','%JUMP%'),
            array($header, $this->nowPage, $up_page, $down_page, $the_first, $link_page, $the_end, $this->totalRows, $this->totalPages,$jump_page),
                $this->config['theme']);
        return '<div id="pageDemo"><div class="layui-box layui-laypage layui-laypage-molv">'.$page_str.'</div></div>';
    }



}