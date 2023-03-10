<?php namespace Soulcms\Controllers;

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



// 共享栏目生成静态
class Html extends \Soulcms\Home\Module
{

	// 生成栏目
	public function category() {
		parent::_Category_Html();
	}

	// 生成内容
	public function show() {
		parent::_Show_Html();
	}

	// 生成栏目单页
	public function categoryfile() {
		parent::_Category_Html_File();
	}

	// 生成内容单页
	public function showfile() {
		parent::_Show_Html_File();
	}

	
}
