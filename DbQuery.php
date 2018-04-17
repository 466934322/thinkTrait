<?php

namespace traits\model;

trait DbQuery
{
    /**
     * 根据条件获取指定数据
     * @param  string || array $param [条件]
     * @return object || null       [结果集]
     */
    public function getOneData($param='')
    {
        $where = $this->checkSelectCondition($param);
        $ret = $this->where($where)->find();

        return $ret;
    }
    /**
     * 获取列表
     * @param  array || string $where [查询条件 key=>value]
     * @param  string $order [order查询条件]
     * @param  string $page  [1,10 请查询thinnkphp5 page用法]
     * @return object || null        [返回结果集]
     */
    public function getList($where='', $order='', $page='')
    {
        $where = $this->checkSelectCondition($where);
        $this->where($where)->order($order)->page($page)->select();
    }
    /**
     * 新增方法
     * @param array $value [description]
     */
    public function add($param='')
    {
        if (!is_array($param)) {
            return false;
        }

        $data = $this->_filterParam($param);
        return $this->insert($data, false, true);
    }
    /**
     * 编辑方法
     * @param  array $param [条件]
     * @return [type]        [description]
     */
    public function edit($param='', $condition='')
    {
        if (!is_array($param)) {
            return false;
        }
        $data = $this->filterParam($param);
        if (is_null($condition)) {
            $ret = $this->isUpdate(true)->save($data);
        }else{
            $where = $this->_filterParam($condition);
            $ret = $this->where($where)->isUpdate(true)->save($data);
        }
        return $ret;
    }
    /**
     * 过滤数组-完善版 2018/04/12
     * @param  string || array $param [where条件]
     * @return [输出类型与输入类型一致]        [返回结果]
     */
    protected function _filterParam($param='')
    {
        //过滤数组类型
        if (is_array($param)) {
            if (count($param) != count($param, 1)) {
                //多维数组过滤
                foreach ($param as $key => $value) {
                    if (is_array($value)) {
                        $ret[$key] = $this->_filterParam($value);
                    }else{
                        $ret[$key] = trim($value);
                    }
                }
            }else{
                //一维数组过滤
                foreach ($param as $key => $value) {
                    $ret[$key] = trim($value);
                }
            }
        }elseif (is_string($param)) {
            //过滤字符串
            $ret = trim($param);
        }else{
            //其他类型暂不做处理
            $ret = $param;
        }

        return $ret;
    }
    /**
     * 检测where条件是否符合要求
     * @param  array || string $param [where条件]
     * @return [输出类型与输入类型一致]            [返回的结果]
     */
    protected function checkSelectCondition($param)
    {
        //数组
        if (is_array($param)) {
            $where = $this->_filterParam($param);
        }elseif (is_string($param)) {
            if (is_null($this->getPK())) {
                return false;
            }
            //主键条件
            $where[$this->getPK()] = $param;
        }else{
            return false;
        }

        return $where;
    }
}
