<?php
namespace Common\Model;
use Think\Model;
class ConfigModel extends Model {
	public function set_config($data = array())
	{
		$map['key'] = $data['name'];
		$id = $this->where($map)->getField('id');
		if ($id) {
			$update['value'] = $data['value'];
			$flag = $this->where('id = '.$id)->save($update);
		}else{
			$add = [
				'key' => $data['name'],
				'value' => $data['value']
			];
			$flag = $this->data($add)->add();
		}
		return $flag?true:false;
	}
	public function get_config($name = false)
	{
		if (!is_bool($name) && !stristr($name,',')) {
			$map['key'] = $name;
		}else if (!is_bool($name) && stristr($name,',')) {
			$map['key'] = array('in',$name);
		}
		$data = $this->field('key,value')->where($map)->select(['index'=>'key']);
		$config = array_map(function($data)
		{
			return $data['value'];
		}, $data);
		return $config;
	}
}