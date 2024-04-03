<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/class/database.class");


class AMysql extends MySQL
{

	function __construct($_db_host='', $_db_user='', $_db_pass='', $_db_name='', $_db_port='3306') {
		parent::MySQL($_db_host, $_db_user, $_db_pass, $_db_name, $_db_port);
	}


	function row($sql) {
		$ret = $this->query($sql);
		$row = mysql_fetch_assoc($ret);
		mysql_free_result($ret);

		return $row;
	}


	function rows($sql) {
		$ret = $this->query($sql);
		$rows = array();
		while($row = mysql_fetch_assoc($ret)) {
			$rows[] = $row;
		}
		mysql_free_result($ret);

		return $rows;

	}


	function one($sql) {
		$ret = $this->query($sql);
		$one = mysql_fetch_row($ret);
		mysql_free_result($ret);

	}


	public function update($tb, $data, $where='') {

		if ( $where == '' ) {
			throw new Exception('no where');
		}

		$fields = array();
		
		foreach($data as $col=>$val) {
			$fields[] = sprintf("`%s`='%s'", $col, mysql_real_escape_string($val));	
		}

		if (is_array($where))
		{
			$_where = array();
			foreach($where as $col=>$val) {
				$_where[] = sprintf("`%s`='%s'", $col, mysql_real_escape_string($val));	
			}

			$_WHERE = implode(' AND ', $_where);
		} else {

			$_WHERE = $where;

		}

		$sql = sprintf("UPDATE %s SET %s WHERE %s", $tb, implode(',', $fields), $_WHERE);

		br($sql);

		//$this->query($sql);

	}

	public function insert($tb, $data) {

		$fields = array();
		$values = array();
		foreach($data as $col=>$val) {
			$fields[] = sprintf("`%s`", $col);
			$values[] = sprintf("'%s'", mysql_real_escape_string($val)); 
		}

		$sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $tb, implode(',', $fields), implode(',', $values));

		br($sql);

		//$this->query($sql);

		return mysql_insert_id();
	}



}
?>