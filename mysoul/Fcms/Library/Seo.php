<?php namespace Soulcms\Library;

/* *
 *
 * Copyright [2019]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 *
 * 本文件是框架系统文件，二次开发时不建议修改本文件
 *
 * */


/**
 * seo输出
 */

class Seo
{

    /**
     * 首页SEO信息
     *
     * @return	array
     */
    public function index() {

        $seo = [
            'meta_title' => \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_TITLE'),
            'meta_keywords' => \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_KEYWORDS'),
            'meta_description' => \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_DESCRIPTION')
        ];

        return $seo;
    }


    /**
     * 模块SEO信息
     *
     * @return	array
     */
    public function module($mod) {

        $seo = [];

        $seo['meta_title'] =  $mod['site'][SITE_ID]['module_title'] ? $mod['site'][SITE_ID]['module_title'] : $mod['name'].SITE_SEOJOIN.SITE_NAME;
        $seo['meta_keywords'] = $mod['site'][SITE_ID]['module_keywords'];

        $seo['meta_title'] = htmlspecialchars(mys_clearhtml($seo['meta_title']));
        $seo['meta_description'] = $mod['site'][SITE_ID]['module_description'];
        $seo['meta_description'] = htmlspecialchars(mys_clearhtml($seo['meta_description']));


        if (!$seo['meta_keywords']) {
            // 留空时使用主站seo
            $seo['meta_keywords'] = \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_KEYWORDS');
        }

        if (!$seo['meta_description']) {
            // 留空时使用主站seo
            $seo['meta_description'] = \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_DESCRIPTION');
        }

        return $seo;
    }


    /**
     * 模块搜索SEO信息
     */
    function search($mod, $catid, $param, $page = 1) {

        $seo = [];
        $seo['meta_keywords'] = '';

        $data['page'] = $page > 1 ? $page : '';
        $data['join'] = SITE_SEOJOIN ? SITE_SEOJOIN : '_';
        $data['modulename'] = $data['modname'] = $mod['name'];
        $data['param'] = '';
        $data['keyword'] = '';

        if ($param['keyword']) {
            $data['keyword'] = $param['keyword'];
            $seo['meta_keywords'].= $data['keyword'].',';
            unset($param['keyword']);
        }

        $param_value = [];
        if ($catid) {
            $t = mys_get_cat_pname($mod, $catid, PHP_EOL);
            $t && $t = explode(PHP_EOL, $t);
            $t && $param_value = $t;
            unset($param['catid']);
            unset($param['catdir']);
        }

        if ($param) {
            $myfield = $mod['field'];
            if ($catid) {
                $cat_field = $mod['category'][$catid]['field'];
                $cat_field && $myfield = mys_array22array($myfield, $cat_field);
            }
            foreach ($param as $name => $value) {
                if (!isset($myfield[$name])) {
                    unset($param[$name]);
                    continue;
                }
                switch ($myfield[$name]['fieldtype']) {

                    case 'Radio':
                    case 'Select':
                        $opt = mys_format_option_array($myfield[$name]['setting']['option']['options']);
                        isset($opt[$value]) && $opt[$value] && $param_value[] = $opt[$value];
                        break;

                    case 'Linkages':
                    case 'Linkage':
                        $param_value[] = mys_linkagepos($myfield[$name]['setting']['option']['linkage'], $value, $data['join']);
                        break;

                    default:
                        $value && $param_value[] = $value;
                        break;

                }
            }
        }

        if ($param_value) {
            $data['param'] = implode($data['join'], $param_value);
            $seo['meta_keywords'].= implode(',', $param_value).',';
        }

        $meta_title = $mod['site'][SITE_ID]['search_title'] ? $mod['site'][SITE_ID]['search_title'] : '['.mys_lang('第%s页', '{page}').'{join}][{keyword}{join}][{param}{join}]{modulename}{join}{SITE_NAME}';

        if (preg_match_all('/\[.*\{(.+)\}.*\]/U', $meta_title, $m)) {
            $new = '';
            $replace = '';
            foreach ($m[1] as $i => $field) {
                $replace.= $m[0][$i];
                if (isset($data[$field]) && strlen($data[$field])) {
                    $new.= str_replace(array('[', ']'), '', $m[0][$i]);
                }
            }
            $meta_title = str_replace($replace, $new, $meta_title);
        }

        $rep = new \php5replace($data);
        $seo['meta_title'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $meta_title);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $seo['meta_title']);
        $seo['meta_title'] = trim(str_replace($data['join'].$data['join'], $data['join'], $seo['meta_title']), $data['join']);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $seo['meta_title']);
        unset($rep);

        $seo['meta_title'] = htmlspecialchars(mys_clearhtml($seo['meta_title']));
        $seo['meta_keywords'].= $mod['site'][SITE_ID]['search_keywords'];

        $seo['meta_keywords'] = trim($seo['meta_keywords'], ',');
        $seo['meta_description'] = $mod['site'][SITE_ID]['search_description'];
        $seo['meta_description'] = htmlspecialchars(mys_clearhtml($seo['meta_description']));
        $seo['meta_description'] = str_replace('"', '', $seo['meta_description']);


        if (!$seo['meta_keywords']) {
            // 留空时使用主站seo
            $seo['meta_keywords'] = \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_KEYWORDS');
        }

        if (!$seo['meta_description']) {
            // 留空时使用主站seo
            $seo['meta_description'] = \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_DESCRIPTION');
        }

        return $seo;
    }

    /**
     * 模块栏目SEO信息
     *
     * @param	array	$mod
     * @param	array	$cat
     * @param	intval	$page
     * @return	array
     */
    function category($mod, $catid, $page = 1) {

        $seo = [];

        $cat = $mod['category'][$catid];
        $cat['page'] = intval($page);
        $cat['join'] = SITE_SEOJOIN;
        $cat['name'] = $cat['catname'] = $cat['name'];
        $cat['catpname'] = mys_get_cat_pname($mod, $catid, $cat['join']);
        $cat['modulename'] = $cat['modname'] = $mod['title'];

        $rep = new \php5replace($cat);

        $meta_title = $cat['setting']['seo']['list_title'] ? $cat['setting']['seo']['list_title'] : '['.mys_lang('第%s页', '{page}').'{join}]{modulename}{join}{SITE_NAME}';
        $meta_title = $page > 1 ? str_replace(array('[', ']'), '', $meta_title) : preg_replace('/\[.+\]/U', '', $meta_title);
        $seo['meta_title'] = htmlspecialchars(mys_clearhtml($seo['meta_title']));

        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $meta_title);
        $seo['meta_title'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_title']);
        $seo['meta_title'] = str_replace($cat['join'].$cat['join'], $cat['join'], $seo['meta_title']);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $seo['meta_title']);

        $seo['meta_keywords'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $cat['setting']['seo']['list_keywords']);
        $seo['meta_keywords'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_keywords']);
        $seo['meta_keywords'] = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $seo['meta_keywords']);

        $seo['meta_description'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $cat['setting']['seo']['list_description']);
        $seo['meta_description'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_description']);
        $seo['meta_description'] = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $seo['meta_description']);

        $seo['meta_description'] = htmlspecialchars(mys_clearhtml($seo['meta_description']));
        $seo['meta_description'] = str_replace('"', '', $seo['meta_description']);

        if (!$seo['meta_keywords']) {
            // 留空时使用主站seo
            $seo['meta_keywords'] = \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_KEYWORDS');
        }

        if (!$seo['meta_description']) {
            // 留空时使用主站seo
            $seo['meta_description'] = \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_DESCRIPTION');
        }

        return $seo;
    }

    /**
     * 模块内容SEO信息
     *
     * @param	array	$mod
     * @param	array	$cat
     * @param	intval	$page
     * @return	array
     */
    function show($mod, $data, $page = 1) {

        $seo = [];

        $cat = $mod['category'][$data['catid']];
        $data['page'] = $page;
        $data['join'] = SITE_SEOJOIN;
        $data['name'] = $data['catname'] = $cat['name'];
        $data['title'] = mys_clearhtml($data['title']);
        $data['catname'] = $cat['name'];
        $data['catpname'] = mys_get_cat_pname($mod, $data['catid'], $data['join']);
        $data['modulename'] = $data['modname'] = $mod['name'];
        $data['description'] = htmlspecialchars(mys_clearhtml($data['description']));

        $meta_title = $mod['site'][SITE_ID]['show_title'] ? $mod['site'][SITE_ID]['show_title'] : '['.mys_lang('第%s页', '{page}').'{join}]{title}{join}{catpname}{join}{modulename}{join}{SITE_NAME}';
        $meta_title = $page > 1 ? str_replace(array('[', ']'), '', $meta_title) : preg_replace('/\[.+\]/U', '', $meta_title);

        $rep = new \php5replace($data);
        $seo['meta_title'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $meta_title);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $seo['meta_title']);
        $seo['meta_title'] = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $seo['meta_title']);
        $seo['meta_title'] = str_replace($data['join'].$data['join'], $data['join'], $seo['meta_title']);
        $seo['meta_title'] = htmlspecialchars(mys_clearhtml($seo['meta_title']));

        if ($mod['site'][SITE_ID]['show_keywords']) {
            $seo['meta_keywords'] = $mod['site'][SITE_ID]['show_keywords'];
            $seo['meta_keywords'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_keywords']);
            $seo['meta_keywords'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $seo['meta_keywords']);
            $seo['meta_keywords'] = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $seo['meta_keywords']);
        } else {
            $seo['meta_keywords'] = $data['keywords'];
        }

        if ($mod['site'][SITE_ID]['show_description']) {
            $seo['meta_description'] = $mod['site'][SITE_ID]['show_description'];
            $seo['meta_description'] = preg_replace_callback('#{([A-Z_]+)}#U', array($rep, 'php55_replace_var'), $seo['meta_description']);
            $seo['meta_description'] = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $seo['meta_description']);
            $seo['meta_description'] = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $seo['meta_description']);
        } else {
            $seo['meta_description'] = $data['description'];
        }

        $seo['meta_description'] = htmlspecialchars(mys_clearhtml($seo['meta_description']));
        $seo['meta_description'] = str_replace('"', '', $seo['meta_description']);
        $seo['meta_keywords'] = str_replace('"', '', $seo['meta_keywords']);
        $seo['meta_title'] = str_replace('"', '', $seo['meta_title']);

        return $seo;
    }


    // 评论的
    function comment($mod, $data) {

        $seo = [
            'meta_title' => mys_lang('评论: %s', $data['title']).SITE_SEOJOIN.$mod['name'],
            'meta_keywords' => $data['keywords'],
            'meta_description' => $data['description'],
        ];

        return $seo;
    }

    // 模块表单
    function mform_list($form, $index, $page = 1) {

        $seo = [
            'meta_title' => $index['title'].SITE_SEOJOIN.$form['name'],
            'meta_keywords' => $index['keywords'],
            'meta_description' => $index['description'],
        ];

        return $seo;
    }

    // 模块表单
    function mform_post($form, $index) {

        $seo = [
            'meta_title' => $index['title'].SITE_SEOJOIN.$form['name'],
            'meta_keywords' => $index['keywords'],
            'meta_description' => $index['description'],
        ];

        return $seo;
    }

    // 模块表单
    function mform_show($form, $index, $data) {

        $seo = [
            'meta_title' => $index['title'].SITE_SEOJOIN.$form['name'],
            'meta_keywords' => $index['keywords'],
            'meta_description' => $index['description'],
        ];

        return $seo;
    }

    // 网站表单
    function form_list($form, $page = 1) {

        $seo = [
            'meta_title' => $form['name'],
            'meta_keywords' => '',
            'meta_description' => '',
        ];

        return $seo;
    }

    // 网站表单
    function form_post($form) {

        $seo = [
            'meta_title' => $form['name'],
            'meta_keywords' => '',
            'meta_description' => '',
        ];

        return $seo;
    }

    // 网站表单
    function form_show($form, $data) {

        $seo = [
            'meta_title' => $data['title'].SITE_SEOJOIN.$form['name'],
            'meta_keywords' => '',
            'meta_description' => '',
        ];

        return $seo;
    }

    // 用户中心seo
    function member($menu) {


        $seo = [
            'menu' => $menu['url'],
            'page_bar' => '<div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <span>'.mys_lang('用户中心').'</span>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    {value}
                </ul>
            </div>',
        ];


        // 自定义菜单显示
        if (function_exists('mys_my_member_menu')) {
            $seo['menu'] = mys_my_member_menu( $seo['menu']);
        }

        list($uri1, $uri2) = \Soulcms\Service::L('router')->member_uri();
        $uri = isset($menu['uri'][$uri1]) ? $uri1 : (isset($menu['uri'][$uri2]) ? $uri2 : '');

        if (!$uri && APP_DIR && APP_DIR != 'member') {
            // 来自内容模块的菜单全部归结于内容下
            $uri = APP_DIR.'/home/index';
        }

        $seo['mymenu'] = []; // 当前菜单id和pid

        if ($menu['uri'][$uri]) {
            $seo['page_bar'] = str_replace('{value}', '
                    <li>
                        <i class="'.mys_icon($menu['uri'][$uri]['picon']).'"></i>
                        <span>'.mys_lang($menu['uri'][$uri]['pname']).'</span>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <i class="'.mys_icon($menu['uri'][$uri]['icon']).'"></i>
                        <span>'.mys_lang($menu['uri'][$uri]['name']).'</span>
                    </li>
                    ', $seo['page_bar']);
            $seo['mymenu'] = [$menu['uri'][$uri]['id'], $menu['uri'][$uri]['pid']]; // 当前菜单id和pid
            $seo['meta_name'] = $menu['uri'][$uri]['name'];
            $seo['meta_title'] = $menu['uri'][$uri]['name'].SITE_SEOJOIN.$menu['uri'][$uri]['pname'].SITE_SEOJOIN.mys_lang('用户中心');
        } else {
            $seo['meta_title'] = $seo['meta_name'] = mys_lang('用户中心');
        }

        $seo['page_bar'] = str_replace('{value}', '', $seo['page_bar']);
        $seo['meta_keywords'] = \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_KEYWORDS');
        $seo['meta_description'] = \Soulcms\Service::C()->get_cache('site', SITE_ID, 'seo', 'SITE_DESCRIPTION');

        return $seo;
    }

}