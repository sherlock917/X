<?php
/**
 * 模型类
 * 所有模型都要继承该类
 *
 * @author vincent
 *        
 */
abstract class Model {
	protected $db; // 数据库访问对象
	protected $tableName; // model对应的表名
	/**
	 * 构造函数
	 */
	function __construct() {
		$this->db = DB::getInstance ();
		$this->tableName = get_table_name ( get_class ( $this ) ); // 获取当前引用的类型对应的数据表名
	}
	/**
	 * 析构函数
	 */
	function __destruct() {
	}
	
	/**
	 * 通过id查找数据
	 *
	 * @param string $id
	 * @param string $idName 索引的名字
	 * @return multitype:
	 */
	function findById($id, $idName = 'id') {
		$rs = $this->db->select ( $this->tableName, array (
				$idName => $id 
		) );
		if ($rs)
			return $rs [0]; // 因为一个id只对应一条记录
		return $rs;
	}
	/**
	 * 通过id删除数据
	 *
	 * @param string $id
	 * @return Ambigous <PDOStatement, number>
	 */
	function deleteById($id) {
		return $this->db->delete ( $this->tableName, array (
				'id' => $id 
		) );
	}
	/**
	 * 删除数据
	 *
	 * @param array $conditions 删除条件
	 */
	function delete($conditions) {
		return $this->db->delete ( $this->tableName, $conditions );
	}
	
	/**
	 * 查找记录
	 *
	 * @param array $conditions 查找条件
	 * @param array $order 要排序的字段
	 * @param string $desc 是否逆序
	 * @return multitype:
	 */
	function find($conditions, $order = array(), $desc = FALSE) {
		$sql = DatabaseUtil::getSelectQuery ( $this->tableName, array_keys ( $conditions ) );
		if ($order) {
			$sql .= ' ORDER BY ' . implode ( $order, ',' );
			if ($desc)
				$sql .= ' DESC';
		}
		$stat = $this->db->getPdo ()->prepare ( $sql );
		$stat->execute ( array_values ( $conditions ) );
		return $stat->fetchAll ();
	}
	
	/**
	 * 获取所有数据
	 *
	 * @param array $order 要排序的字段
	 * @param boolean $desc 是否逆序
	 * @return multitype:
	 */
	function findAll($order = array(), $desc = FALSE) {
		return $this->find ( array (), $order, $desc );
	}
	/**
	 * 更新数据
	 *
	 * @param array $newData 要更新的数据
	 * @param array $conditions 选择条件
	 * @return boolean
	 */
	function update($newData, $conditions) {
		return $this->db->update ( $this->tableName, $newData, $conditions );
	}
	/**
	 * 保存数据
	 *
	 * @param array $arr
	 */
	function save($arr) {
		return $this->db->insert ( $this->tableName, $arr );
	}
	
	/**
	 * 执行sql
	 *
	 * @param string $sql
	 * @return PDOStatement
	 */
	function query($sql) {
		return $this->db->query ( $sql );
	}
	/**
	 * 执行sql
	 *
	 * @param string $sql
	 * @return Ambigous <PDOStatement, number>
	 */
	function exec($sql) {
		return $this->db->exec ( $sql );
	}
	/**
	 * 分页获取数据
	 *
	 * @param array $conditions 选择条件
	 * @param int $page 页号
	 * @param int $pageSize 每页的大小
	 * @param array $order 排序
	 * @param boolean $desc 是否逆序
	 * @return multitype:
	 */
	function getPage($conditions, $page, $pageSize, $order = array(), $desc = FALSE) {
		$sql = DatabaseUtil::getSelectQuery ( $this->tableName, array_keys ( $conditions ) );
		if ($order) {
			$sql .= ' ORDER BY ' . implode ( $order, ',' );
			if ($desc)
				$sql .= ' DESC';
		}
		$sql .= ' LIMIT %d , %d';
		$index = ($page - 1) * $pageSize;
		$pdo = $this->db->getPdo ();
		$stat = $pdo->prepare ( sprintf ( $sql, $index, $pageSize ) );
		$stat->execute ( array_values ( $conditions ) );
		return $stat->fetchAll ();
	}
	
	/**
	 * 统计表中记录的数量
	 *
	 * @return integer
	 */
	function count() {
		$rs = $this->db->query ( 'SELECT count(*) AS count FROM ' . $this->tableName );
		$row = $rs->fetch ();
		return $row ['count'];
	}
}
