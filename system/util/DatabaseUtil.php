<?php
/**
 * 数据库工具类
 * 只生成常用sql和基本的预编译sql
 *
 * @author vincent
 *        
 */
class DatabaseUtil {
	/**
	 * 创建数据库的sql
	 *
	 * @param string $databaseName
	 * @return string
	 */
	static function getCreateDatabaseQuery($databaseName) {
		$sql = 'CREATE DATABASE ' . $databaseName . ';';
		return $sql;
	}
	
	/**
	 * 创建数据表的sql
	 *
	 * @param string $modelName
	 * @return string
	 */
	static function getCreateTableQuery($modelName) {
		// 利用php反射机制 自动建数据表
		$rc = new ReflectionClass ( $modelName );
		$properties = $rc->getProperties (); // 获取所有属性
		$sql = 'CREATE TABLE ' . get_table_name ( $rc->getName () ) . ' ('; // 表名为全小写
		$propertyArray = array ();
		foreach ( $properties as $property ) {
			// 获取属性注释(字段的类型、长度、约束)
			preg_match ( '/#.*#/', $property->getDocComment (), $matches );
			if ($matches) { // 若没有注释 则忽略该字段
				$dbProperty = $property->name;
				$infos = explode ( '#', $matches [0] );
				for($j = 0, $len = count ( $infos ); $j < $len; $j ++) {
					if ($infos [$j] != '')
						$dbProperty .= ' ' . strtoupper ( $infos [$j] ); // 转换成大写
				}
				$propertyArray [] = $dbProperty;
			}
		}
		$sql .= implode ( $propertyArray, ',' ) . ')';
		return $sql;
	}
	/**
	 * 删除数据表sql
	 *
	 * @param string $modelName
	 */
	static function getDropTableQuery($modelName) {
		$sql = 'DROP TABLE ' . get_table_name ( $modelName );
		return $sql;
	}
	
	/**
	 * 插入数据的sql
	 *
	 * @param string $tableName
	 * @param array $keys
	 * @return string
	 */
	static function getInsertQuery($tableName, $keys) {
		$sql = 'INSERT INTO %s (%s) VALUES (%s)';
		return sprintf ( $sql, $tableName, implode ( $keys, ',' ), char_repeat ( count ( $keys ), '?', ',' ) );
	}
	/**
	 * 删除数据的sql
	 *
	 * @param string $tableName
	 * @param array $keys
	 * @return string
	 */
	static function getDeleteQuery($tableName, $keys) {
		$sql = 'DELETE FROM %s WHERE (%s)';
		return sprintf ( $sql, $tableName, self::_array_to_key_unkownvalue_pairs ( $keys, 'AND' ) );
	}
	
	/**
	 * 选择数据的sql
	 *
	 * @param string $tableName
	 * @param array $keys
	 * @return string
	 */
	static function getSelectQuery($tableName, $keys = array()) {
		if (! $keys) // 若不存在选择数据用的数组
			return 'SELECT * FROM ' . $tableName;
		$sql = 'SELECT * FROM %s WHERE (%s)';
		return sprintf ( $sql, $tableName, self::_array_to_key_unkownvalue_pairs ( $keys, 'AND' ) );
	}
	/**
	 * 更新数据的sql
	 *
	 * @param string $tableName
	 * @param array $keys1
	 * @param array $keys2
	 * @return string
	 */
	static function getUpdateQuery($tableName, $keys1, $keys2) {
		$sql = 'UPDATE %s SET %s WHERE (%s)';
		return sprintf ( $sql, $tableName, self::_array_to_key_unkownvalue_pairs ( $keys1, ',' ), self::_array_to_key_unkownvalue_pairs ( $keys2, 'AND' ) );
	}
	
	/**
	 * 数组转换成键=未知值对
	 *
	 * @param array $keys
	 * @param string $separator
	 */
	private static function _array_to_key_unkownvalue_pairs($keys, $separator) {
		$maxIndex = count ( $keys );
		$str = '';
		for($index = 0; $index < $maxIndex; $index ++) {
			$str .= $keys [$index] . ' = ? ';
			if ($index != $maxIndex - 1)
				$str .= ' ' . $separator . ' ';
		}
		return $str;
	}
	/**
	 * 数组转换成键=值对
	 *
	 * @param array $arr
	 * @param string $separator
	 */
	private static function _array_to_key_value_pairs($arr, $separator) {
		$index = 0;
		$maxIndex = count ( $arr ) - 1;
		$str = '';
		foreach ( $arr as $key => $value ) {
			$str .= $key . ' = ';
			if (gettype ( $value ) == 'string')
				$str .= "'" . $value . "'";
			else
				$str .= $value;
			if ($index != $maxIndex)
				$str .= ' ' . $separator . ' ';
			$index ++;
		}
		return $str;
	}
}
