<?php
/**
 * @author: axios
 *
 * @email: axioscros@aliyun.com
 * @blog:  http://hanxv.cn
 * @datetime: 2017/8/30 下午1:55
 */

namespace tpr\admin\common\model;

use think\Model;

class MenuModel extends Model
{
    public $name = 'menu';

    function __construct($data = [])
    {
        parent::__construct($data);
    }

    public static function model()
    {
        return new self();
    }

    public function menus($parent_id = 0)
    {
        $this->alias('menu');

        $this->field('menu.* ');

        $list = $this->where('parent_id', $parent_id)->order('sort asc')->select();

        return $list;
    }

    public function getMenu($only_parent = false)
    {
        $menus = $this->where('parent_id', 0)
            ->field('id,title ,title as name ,icon,parent_id,module , controller , func , sort')->order('sort')->select();
        if (!$only_parent) {
            foreach ($menus as &$m) {
                $m['children'] = $this->where('parent_id', $m['id'])
                    ->field('id,title ,title as name ,parent_id,icon,module , controller , func , sort')->order('sort')->select();
                $m['spread'] = true;
            }
        }

        return $menus;
    }

    public function getMenuTree($parent_id = 0, $role_id = 0)
    {
        return $this->menuTree($parent_id, $role_id);
    }

    private function menuTree($parent_id = 0, $role_id = 0)
    {
        $this->where('parent_id', $parent_id);

        if ($role_id) {
            $this->join("__ROLE_NODE__ rn", 'rn.menu_id=menu.id', 'left');
        }
        $this->field('id,title ,title as name ,icon , parent_id,module , controller , func , sort');

        $menu = $this->order('sort asc')->select();
        foreach ($menu as &$m) {
            $m['spread'] = true;
            $m['children'] = $this->menuTree($m['id'], $role_id);
        }
        return $menu;
    }

}