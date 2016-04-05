<?php
/**
 * 提交按钮，暂时只用于搜索
 * Class SubmitForm
 */
class SubmitForm extends FormBase {

	public function _getSearchValue() {
		return [];
	}

	public function _getSearchHtml() {
		return '<button class="btn btn-default" type="submit">查询</button>';
	}
}